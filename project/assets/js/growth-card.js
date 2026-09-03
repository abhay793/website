/* ============================================================
   OCKTOVA — growth-card.js
   Builds a smooth, natural chart curve at runtime (no sharp
   zig-zags), then plays a scroll-triggered draw-in + a comet
   dot that travels the "after" line, plus stat count-up.
   Save to: /assets/js/growth-card.js
   ============================================================ */

(function () {
  var card = document.getElementById('octGrowthCard');
  if (!card || card.dataset.octBound) return;
  card.dataset.octBound = "1";

  /* ---------- 1. build natural, smooth curves ---------- */

  // Catmull-Rom -> cubic Bezier, so the line flows through every
  // point instead of hitting it with a hard corner.
  function smoothPath(points) {
    if (points.length < 3) {
      return 'M' + points.map(function (p) { return p.join(','); }).join('L');
    }
    var d = 'M' + points[0][0] + ',' + points[0][1];
    for (var i = 0; i < points.length - 1; i++) {
      var p0 = points[i - 1] || points[i];
      var p1 = points[i];
      var p2 = points[i + 1];
      var p3 = points[i + 2] || p2;

      var cp1x = p1[0] + (p2[0] - p0[0]) / 6;
      var cp1y = p1[1] + (p2[1] - p0[1]) / 6;
      var cp2x = p2[0] - (p3[0] - p1[0]) / 6;
      var cp2y = p2[1] - (p3[1] - p1[1]) / 6;

      d += ' C' + cp1x + ',' + cp1y + ' ' + cp2x + ',' + cp2y + ' ' + p2[0] + ',' + p2[1];
    }
    return d;
  }

  // gentle, mostly-flat drift before Ocktova
  var beforePoints = [
    [0, 340], [50, 332], [100, 338], [150, 320], [200, 326], [250, 305], [300, 312], [330, 295]
  ];
  // natural, confident climb after Ocktova
  var afterPoints = [
    [330, 295], [380, 255], [410, 268], [450, 220], [480, 232],
    [520, 180], [550, 192], [590, 140], [620, 150], [660, 95], [700, 60]
  ];

  var beforeEl = document.getElementById('octLineBefore');
  var afterEl = document.getElementById('octLineAfter');
  beforeEl.setAttribute('d', smoothPath(beforePoints));
  afterEl.setAttribute('d', smoothPath(afterPoints));

  /* ---------- 2. stat count-up ---------- */

  function countUp(el) {
    var to = parseFloat(el.dataset.countTo);
    var decimals = parseInt(el.dataset.decimals || "0", 10);
    var suffix = el.dataset.suffix || "";
    var pad = parseInt(el.dataset.pad || "0", 10);
    var duration = 1400;
    var start = null;

    function frame(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var value = to * eased;
      var text = decimals ? value.toFixed(decimals) : Math.round(value).toString();
      if (pad) text = text.padStart(pad, "0");
      el.textContent = text + suffix;
      if (progress < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  /* ---------- 3. comet dot that travels the "after" line ---------- */

  function runComet() {
    var comet = document.getElementById('octComet');
    var pathLength = afterEl.getTotalLength();
    var duration = 900;
    var start = null;

    comet.classList.add('is-running');

    function frame(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = progress < 1 ? 1 - Math.pow(1 - progress, 2) : 1;
      var point = afterEl.getPointAtLength(pathLength * eased);
      comet.setAttribute('cx', point.x);
      comet.setAttribute('cy', point.y);
      if (progress < 1) {
        requestAnimationFrame(frame);
      } else {
        // settle at the tip, then fade — the pulsing logo carries
        // the "still growing" feeling from here on
        comet.style.transition = 'opacity .6s ease';
        comet.style.opacity = '0';
      }
    }
    requestAnimationFrame(frame);
  }

  /* ---------- 4. scroll trigger ---------- */

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        card.classList.add('is-visible');
        card.querySelectorAll('.stat-num[data-count-to]').forEach(countUp);
        // let the after-line's draw-in transition (1.9s + .6s delay) finish first
        setTimeout(runComet, 2550);
        observer.unobserve(card);
      }
    });
  }, { threshold: 0.35 });

  observer.observe(card);
})();