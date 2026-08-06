/* =====================================================================
   SERAPH CONSTRUCTION — Admin panel JS
   ===================================================================== */
(function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  /* ---------- Sidebar (mobile) ---------- */
  const sidebar = $('#sidebar');
  const backdrop = $('#sidebarBackdrop');
  const hamburger = $('#hamburger');

  if (sidebar && hamburger) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.add('open');
      backdrop.classList.add('show');
    });
    backdrop.addEventListener('click', () => {
      sidebar.classList.remove('open');
      backdrop.classList.remove('show');
    });
  }

  /* ---------- User dropdown ---------- */
  function initUserMenu(wrapId, btnId, menuId) {
    const wrap = $(wrapId);
    if (!wrap) return;
    const btn = $(btnId);
    const menu = $(menuId);
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      menu.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
      if (!wrap.contains(e.target)) menu.classList.remove('open');
    });
  }
  initUserMenu('#userMenuWrap', '#userMenuBtn', '#userMenuDropdown');

  /* ---------- Notifications ---------- */
  const notifWrap = $('#notifWrap');
  let loadNotifs = null;
  if (notifWrap) {
    const bell = $('#notifBell');
    const dropdown = $('#notifDropdown');
    const list = $('#notifList');

    loadNotifs = function loadNotifs() {
      fetch('/admin/api/notifications?action=list')
        .then((r) => r.json())
        .then((res) => {
          if (!res.success) return;
          const unread = res.data.unread;
          const count = $('#notifCount');
          if (unread > 0) {
            if (!count) {
              const span = document.createElement('span');
              span.className = 'notif__count';
              span.id = 'notifCount';
              span.textContent = unread;
              bell.appendChild(span);
            } else {
              count.textContent = unread;
            }
          } else if (count) {
            count.remove();
          }

          if (!res.data.items.length) {
            list.innerHTML = '<div class="notif__item muted small">No notifications.</div>';
            return;
          }
          list.innerHTML = res.data.items.map((n) =>
            `<div class="notif__item ${n.is_read ? '' : 'unread'}" data-id="${n.id}">
               <div class="notif__item-title">${esc(n.title)}</div>
               ${n.message ? `<div class="small muted">${esc(n.message)}</div>` : ''}
               <div class="notif__item-time">${esc(n.type)} &middot; ${esc(n.created_at)}</div>
             </div>`
          ).join('') +
          '<div class="notif__item"><button class="btn btn--ghost btn--sm btn--block" id="markAllRead">Mark all as read</button></div>';
        });
    }

    function markRead(id) {
      const form = new FormData();
      form.append('id', id);
      fetch('/admin/api/notifications?action=read', { method: 'POST', body: form })
        .then((r) => r.json())
        .then(() => loadNotifs());
    }

    bell.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('open');
      if (dropdown.classList.contains('open')) loadNotifs();
    });
    document.addEventListener('click', (e) => {
      if (!notifWrap.contains(e.target)) dropdown.classList.remove('open');
    });
    document.addEventListener('click', (e) => {
      if (e.target.closest('.notif__item[data-id]')) {
        const item = e.target.closest('.notif__item[data-id]');
        markRead(item.dataset.id);
        item.classList.remove('unread');
      }
    });
    document.addEventListener('click', (e) => {
      if (e.target.id === 'markAllRead') {
        fetch('/admin/api/notifications?action=read_all', { method: 'POST' })
          .then((r) => r.json())
          .then(() => loadNotifs());
      }
    });
  }

  /* ---------- SSE live updates ---------- */
  const liveDot = $('#liveIndicator');
  let liveConn = null;

  // EventSource reconnects automatically (with backoff) and resumes from the
  // Last-Event-ID it sends on each retry.
  // We do NOT manually close + re-create (that spawned duplicate connections),
  // and we only flag the dot as offline after a real, sustained outage so it
  // doesn't flicker during brief drops (page loads, navigation, reloads).
  const SSE_GRACE_MS = 8000;

  function setLiveOffline(offline) {
    if (!liveDot) return;
    liveDot.textContent = offline ? 'Reconnecting…' : 'Live';
    liveDot.classList.toggle('offline', offline);
  }

  function connectSSE() {
    const es = new EventSource('/admin/api/sse');
    liveConn = es;
    let offlineTimer = null;
    let pendingReload = false;
    es.addEventListener('status_update', (ev) => onEvent('status_update', ev));
    es.addEventListener('milestone', (ev) => onEvent('milestone', ev));
    es.addEventListener('project', (ev) => onEvent('project', ev));

    es.onopen = () => {
      clearTimeout(offlineTimer);
      offlineTimer = null;
      pendingReload = false;
      setLiveOffline(false);
    };
    es.onerror = () => {
      // Wait through the grace period; only flag offline if still down.
      if (offlineTimer) return;
      offlineTimer = setTimeout(() => {
        setLiveOffline(true);
        offlineTimer = null;
      }, SSE_GRACE_MS);
    };
  }

  // Debounced full-page refresh: avoids reloading mid-event and dropping the
  // stream repeatedly (the old code reloaded on EVERY event).
  let reloadScheduled = false;
  function scheduleReload() {
    if (reloadScheduled) return;
    reloadScheduled = true;
    setTimeout(() => {
      reloadScheduled = false;
      if (document.visibilityState === 'visible') location.reload();
    }, 800);
  }

  function onEvent(type, ev) {
    let data;
    try { data = JSON.parse(ev.data); } catch (e) { return; }

    // Refresh notification count on new activity
    if (type === 'status_update' || type === 'milestone') {
      if (loadNotifs) loadNotifs();
      flashLive(`${data.title || 'New update'} — ${data.progress || 0}%`);
    }

    // Auto-refresh current page data (dashboard / project view)
    if (type === 'status_update' && data.project_id && location.pathname.includes('/admin/')) {
      const onProject = location.pathname === '/admin/projects/view'
        && new URLSearchParams(location.search).get('id') === String(data.project_id);
      const onDashboard = location.pathname === '/admin/' || location.pathname === '/admin/index';
      if (onProject || onDashboard) scheduleReload();
    }
  }

  /* ---------- Live flash toast ---------- */
  let toast = null;
  function flashLive(msg) {
    if (!toast) {
      toast = document.createElement('div');
      toast.style.cssText =
        'position:fixed;bottom:24px;right:24px;z-index:999;background:#16a34a;color:#fff;' +
        'padding:12px 18px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.4);' +
        'font-size:.88rem;transition:opacity .3s,transform .3s;';
      document.body.appendChild(toast);
    }
    toast.textContent = '🔔 ' + msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-8px)';
    }, 4000);
  }

  /* ---------- Helpers ---------- */
  function esc(str) {
    const d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  /* ---------- Dropzone image preview ---------- */
  const dropzone = $('#dropzone');
  const imageInput = $('#imageInput');
  const preview = $('#preview');

  if (dropzone && imageInput) {
    ['dragenter', 'dragover'].forEach((ev) =>
      dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('dragover'); })
    );
    ['dragleave', 'drop'].forEach((ev) =>
      dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('dragover'); })
    );
    dropzone.addEventListener('drop', (e) => {
      imageInput.files = e.dataTransfer.files;
      renderPreview();
    });
    dropzone.addEventListener('click', () => imageInput.click());
    imageInput.addEventListener('change', renderPreview);

    function renderPreview() {
      if (!preview) return;
      preview.innerHTML = '';
      Array.from(imageInput.files).forEach((f) => {
        const url = URL.createObjectURL(f);
        const el = document.createElement('div');
        el.style.cssText = 'position:relative;width:110px;height:82px;border-radius:8px;overflow:hidden;border:1px solid #e3e8f0;';
        el.innerHTML = `<img src="${url}" style="width:100%;height:100%;object-fit:cover">`;
        preview.appendChild(el);
      });
    }
  }

  /* ---------- Remove existing image on edit ---------- */
  const removeBtn = document.querySelector('.remove-img-btn');
  if (removeBtn) {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.remove-img-btn');
      if (!btn) return;
      e.preventDefault();
      const id = btn.dataset.id;
      const hidden = $('#removeImages');
      if (hidden) {
        const existing = hidden.value ? hidden.value.split(',') : [];
        existing.push(id);
        hidden.value = existing.join(',');
      }
      btn.closest('.gallery__item').remove();
    });
  }

  /* ---------- Lightbox ---------- */
  const lightbox = $('#lightbox');
  const lightboxImg = $('#lightboxImg');
  if (lightbox) {
    document.addEventListener('click', (e) => {
      const item = e.target.closest('.gallery__item img');
      if (!item) return;
      lightboxImg.src = item.src;
      lightbox.classList.add('open');
    });
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox || e.target.closest('.lightbox__close')) lightbox.classList.remove('open');
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') lightbox.classList.remove('open');
    });
  }

  /* ---------- Init ---------- */
  if (liveDot) {
    connectSSE();

    // Pause the stream when the tab is hidden so a background tab does not
    // hold the (single-worker in dev, or shared) server connection open; resume
    // immediately when the tab becomes visible again.
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        if (liveConn) { try { liveConn.close(); } catch (e) {} liveConn = null; }
      } else if (!liveConn) {
        connectSSE();
      }
    });
  }
})();
