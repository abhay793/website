/**
 * ============================================================================
 * products-bg.js — Full-page background animation for the Octova Products page
 * ============================================================================
 * Renders a fixed, full-viewport <canvas id="prodBg"> behind all page content:
 *
 *   1. Aurora glow orbs  — large drifting radial gradients (blue/sky palette)
 *   2. Perspective grid  — a "digital terrain" wireframe flowing to a horizon
 *                          vanishing point (speeds up as you scroll)
 *   3. Particles         — floating, twinkling dots with mouse parallax
 *
 * Behaviour / performance:
 *   - devicePixelRatio capped at 1.5; fewer particles on small screens
 *   - animation pauses while the tab is hidden (rAF keeps running, draw skipped)
 *   - respects prefers-reduced-motion (draws a single static frame)
 *   - mouse + scroll positions are eased for smooth parallax
 *
 * Related files: products.php (canvas element), style-products.css (#prodBg)
 * ============================================================================
 */
(function () {
    'use strict';

    var canvas = document.getElementById('prodBg');
    if (!canvas || !canvas.getContext) { return; }
    var ctx = canvas.getContext('2d');

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var W = 0, H = 0, DPR = 1;
    var mouse   = { x: 0.5, y: 0.5 };   // target (raw)
    var smooth  = { x: 0.5, y: 0.5 };   // eased
    var scrollS = window.pageYOffset || 0; // eased scroll position

    /* ---------- Aurora orbs ---------- */
    var ORBS = [
        { x: 0.78, y: 0.16, r: 0.44, c: [37, 99, 235],  a: 0.30, sp: 0.000090, ph: 0.0 },
        { x: 0.12, y: 0.72, r: 0.40, c: [96, 165, 250], a: 0.18, sp: 0.000120, ph: 2.1 },
        { x: 0.45, y: 0.40, r: 0.32, c: [29, 78, 216],  a: 0.15, sp: 0.000105, ph: 4.2 },
        { x: 0.90, y: 0.82, r: 0.28, c: [96, 165, 250], a: 0.11, sp: 0.000135, ph: 5.4 }
    ];

    /* ---------- Particles ---------- */
    var particles = [];
    function seedParticles() {
        particles.length = 0;
        var count = W < 700 ? 50 : 90;
        for (var i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * W,
                y: Math.random() * H,
                r: 0.8 + Math.random() * 1.8,
                vy: 8 + Math.random() * 22,
                sway: Math.random() * Math.PI * 2,
                tw: Math.random() * Math.PI * 2,
                depth: 0.35 + Math.random() * 0.65,
                white: Math.random() < 0.25
            });
        }
    }

    function resize() {
        DPR = Math.min(window.devicePixelRatio || 1, 1.5);
        W = window.innerWidth;
        H = window.innerHeight;
        canvas.width = Math.round(W * DPR);
        canvas.height = Math.round(H * DPR);
        ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
        seedParticles();
    }

    /* ---------- Layer 1: aurora orbs ---------- */
    function drawOrbs(now) {
        ctx.globalCompositeOperation = 'lighter';
        for (var i = 0; i < ORBS.length; i++) {
            var o = ORBS[i];
            var ox = (o.x + Math.sin(now * o.sp + o.ph) * 0.05) * W + (smooth.x - 0.5) * 40 * (i + 1) / 2;
            var oy = (o.y + Math.cos(now * o.sp * 1.3 + o.ph) * 0.06) * H + (smooth.y - 0.5) * 26;
            var r = o.r * Math.max(W, H);
            var g = ctx.createRadialGradient(ox, oy, 0, ox, oy, r);
            g.addColorStop(0, 'rgba(' + o.c[0] + ',' + o.c[1] + ',' + o.c[2] + ',' + o.a + ')');
            g.addColorStop(0.55, 'rgba(' + o.c[0] + ',' + o.c[1] + ',' + o.c[2] + ',' + (o.a * 0.35) + ')');
            g.addColorStop(1, 'rgba(' + o.c[0] + ',' + o.c[1] + ',' + o.c[2] + ',0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(ox, oy, r, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.globalCompositeOperation = 'source-over';
    }

    /* ---------- Layer 2: perspective grid ---------- */
    function drawGrid(now) {
        var horizon = H * 0.60 + (smooth.y - 0.5) * H * 0.04;
        var vpx = W * 0.5 + (smooth.x - 0.5) * W * 0.07;
        var flow = now * 0.000045 + scrollS * 0.00016;  /* grid speeds up as you scroll */
        var N = 15;
        var depth = H - horizon;
        var i, p, y, a;

        /* horizon glow */
        var hg = ctx.createRadialGradient(vpx, horizon, 0, vpx, horizon, W * 0.34);
        hg.addColorStop(0, 'rgba(96, 165, 250, 0.16)');
        hg.addColorStop(1, 'rgba(96, 165, 250, 0)');
        ctx.fillStyle = hg;
        ctx.fillRect(0, horizon - W * 0.34, W, W * 0.68);

        ctx.lineWidth = 1;

        /* horizontal lines flowing toward the viewer */
        for (i = 0; i < N; i++) {
            p = ((i + flow) % N) / N;   /* 0 (horizon) → 1 (viewer) */
            y = horizon + Math.pow(p, 2.7) * depth;
            a = 0.03 + Math.pow(p, 1.8) * 0.20;
            ctx.strokeStyle = 'rgba(96, 165, 250, ' + a.toFixed(3) + ')';
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(W, y);
            ctx.stroke();
        }

        /* vertical lines converging on the vanishing point */
        for (i = -9; i <= 9; i++) {
            var spread = W * 0.115;
            var xb = vpx + i * spread;
            var xt = vpx + i * spread * 0.045;
            a = Math.max(0.10 - Math.abs(i) * 0.007, 0.02);
            ctx.strokeStyle = 'rgba(37, 99, 235, ' + a.toFixed(3) + ')';
            ctx.beginPath();
            ctx.moveTo(xt, horizon);
            ctx.lineTo(xb, H);
            ctx.stroke();
        }
    }

    /* ---------- Layer 3: particles ---------- */
    function drawParticles(now, dt) {
        var px = (smooth.x - 0.5), py = (smooth.y - 0.5);
        for (var i = 0; i < particles.length; i++) {
            var pt = particles[i];
            pt.y -= pt.vy * dt;
            if (pt.y < -6) { pt.y = H + 6; pt.x = Math.random() * W; }
            var x = pt.x + Math.sin(now * 0.0004 + pt.sway) * 14 + px * 34 * pt.depth;
            var y = pt.y + py * 20 * pt.depth;
            var tw = 0.55 + 0.45 * Math.sin(now * 0.0016 + pt.tw);
            var a = (0.14 + 0.40 * tw) * pt.depth;
            ctx.fillStyle = pt.white
                ? 'rgba(255, 255, 255, ' + a.toFixed(3) + ')'
                : 'rgba(96, 165, 250, ' + a.toFixed(3) + ')';
            ctx.beginPath();
            ctx.arc(x, y, pt.r * (0.8 + 0.4 * tw), 0, Math.PI * 2);
            ctx.fill();
        }
    }

    /* ---------- Main loop ---------- */
    var last = 0;
    function frame(now) {
        requestAnimationFrame(frame);
        if (document.hidden) { last = now; return; }
        var dt = Math.min((now - last) / 1000, 0.05) || 0.016;
        last = now;

        smooth.x += (mouse.x - smooth.x) * 0.045;
        smooth.y += (mouse.y - smooth.y) * 0.045;
        scrollS += ((window.pageYOffset || 0) - scrollS) * 0.09;

        ctx.clearRect(0, 0, W, H);
        drawOrbs(now);
        drawGrid(now);
        drawParticles(now, dt);
    }

    /* ---------- Events ---------- */
    var resizeT;
    window.addEventListener('resize', function () {
        clearTimeout(resizeT);
        resizeT = setTimeout(function () {
            resize();
            if (reduceMotion) { staticFrame(); }
        }, 120);
    }, { passive: true });
    window.addEventListener('mousemove', function (e) {
        mouse.x = e.clientX / W;
        mouse.y = e.clientY / H;
    }, { passive: true });
    window.addEventListener('scroll', function () {
        /* scrollS is eased live in frame() — nothing to do here */
    }, { passive: true });

    function staticFrame() {
        ctx.clearRect(0, 0, W, H);
        drawOrbs(4000);
        drawGrid(4000);
        drawParticles(4000, 0.016);
    }

    /* ---------- Boot ---------- */
    resize();
    if (reduceMotion) {
        staticFrame();
    } else {
        last = performance.now();
        requestAnimationFrame(frame);
    }
})();
