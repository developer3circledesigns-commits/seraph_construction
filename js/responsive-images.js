/* =====================================================
   SERAPH — Responsive image helpers
   Picks appropriately sized URLs for full-bleed backgrounds
   cloned by animations.js (avoids loading 2230w for 768px bg).
   ===================================================== */
(function (global) {
  'use strict';

  function parseSrcset(srcset) {
    return srcset.split(',').map(function (part) {
      part = part.trim();
      if (!part) { return null; }
      var bits = part.split(/\s+/);
      var url = bits[0];
      var width = parseInt(bits[1], 10) || 0;
      return url ? { url: url, width: width } : null;
    }).filter(Boolean).sort(function (a, b) {
      return a.width - b.width;
    });
  }

  function pickSrcsetUrl(srcset, targetWidth) {
    var candidates = parseSrcset(srcset);
    if (!candidates.length) { return ''; }
    var chosen = candidates[candidates.length - 1];
    for (var i = 0; i < candidates.length; i++) {
      if (candidates[i].width >= targetWidth) {
        chosen = candidates[i];
        break;
      }
    }
    return chosen.url;
  }

  /** URL for decorative full-viewport backgrounds (dark overlay — cap at 1024px). */
  function bgUrlFromImg(img, maxWidth) {
    if (!img) { return ''; }

    var explicit = img.getAttribute('data-bg-src');
    if (explicit) { return explicit; }

    var cap = maxWidth || Math.min(global.innerWidth || 1024, 1024);
    var srcset = img.getAttribute('srcset');
    if (srcset) {
      return pickSrcsetUrl(srcset, cap);
    }

    return img.currentSrc || img.getAttribute('src') || img.src || '';
  }

  global.SeraphImages = {
    pickSrcsetUrl: pickSrcsetUrl,
    bgUrlFromImg: bgUrlFromImg,
  };
})(window);
