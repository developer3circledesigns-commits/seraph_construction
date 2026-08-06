/* =====================================================================
   SERAPH CONSTRUCTION — Client portal JS
   ===================================================================== */
(function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  /* ---------- User dropdown ---------- */
  const wrap = $('#userMenuWrap');
  if (wrap) {
    const btn = $('#userMenuBtn');
    const menu = $('#userMenuDropdown');
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      menu.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
      if (!wrap.contains(e.target)) menu.classList.remove('open');
    });
  }

  /* ---------- Live indicator + SSE ---------- */
  const liveDot = $('#liveIndicator');
  let flashTimer = null;
  let liveConn = null;

  // EventSource reconnects automatically (with backoff) and resumes from the
  // Last-Event-ID it sends on each retry. Only flag offline after a sustained
  // outage so the dot does not flicker on brief drops.
  const SSE_GRACE_MS = 8000;

  function setLiveOffline(offline) {
    if (!liveDot) return;
    liveDot.textContent = offline ? 'Reconnecting…' : 'Live';
    liveDot.classList.toggle('offline', offline);
  }

  function connectSSE() {
    const es = new EventSource('/client/api/sse');
    liveConn = es;
    let offlineTimer = null;

    es.addEventListener('status_update', (ev) => onEvent('status_update', ev));
    es.addEventListener('milestone', (ev) => onEvent('milestone', ev));
    es.addEventListener('project', (ev) => onEvent('project', ev));

    es.onopen = () => {
      clearTimeout(offlineTimer);
      offlineTimer = null;
      setLiveOffline(false);
    };
    es.onerror = () => {
      if (offlineTimer) return;
      offlineTimer = setTimeout(() => {
        setLiveOffline(true);
        offlineTimer = null;
      }, SSE_GRACE_MS);
    };
  }

  function onEvent(type, ev) {
    let data;
    try { data = JSON.parse(ev.data); } catch (e) { return; }

    if (!data.project_id) return;

    // If viewing this project's timeline, refresh to show the new update
    const params = new URLSearchParams(location.search);
    if (location.pathname === '/client/projects/view' && params.get('id') === String(data.project_id)) {
      flashNotice(type === 'milestone' ? 'Milestone reached!' : 'New update posted — refreshing…');
      setTimeout(() => location.reload(), 1200);
    } else if (location.pathname === '/client/' || location.pathname === '/client/index') {
      flashNotice('A project was updated — refreshing…');
      setTimeout(() => location.reload(), 1200);
    }
  }

  function flashNotice(msg) {
    const el = document.createElement('div');
    el.style.cssText =
      'position:fixed;top:16px;right:16px;z-index:999;background:#16a34a;color:#fff;' +
      'padding:12px 18px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.4);font-size:.88rem;';
    el.textContent = msg;
    document.body.appendChild(el);
    clearTimeout(flashTimer);
    flashTimer = setTimeout(() => el.remove(), 4000);
  }

  /* ---------- Lightbox ---------- */
  const lightbox = $('#lightbox');
  const lightboxImg = $('#lightboxImg');
  if (lightbox) {
    document.addEventListener('click', (e) => {
      const img = e.target.closest('img[data-lightbox]');
      if (!img) return;
      lightboxImg.src = img.src;
      lightbox.classList.add('open');
    });
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox || e.target.closest('.lightbox__close')) lightbox.classList.remove('open');
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') lightbox.classList.remove('open');
    });
  }

  if (liveDot) {
    connectSSE();

    // Pause the stream when the tab is hidden; resume when visible again.
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        if (liveConn) { try { liveConn.close(); } catch (e) {} liveConn = null; }
      } else if (!liveConn) {
        connectSSE();
      }
    });
  }
})();
