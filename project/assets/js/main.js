/**
 * main.js
 * Shared site behavior: sticky header scroll state, smooth scroll for
 * in-page anchors, and closing the mobile menu after a link is tapped.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var header = document.getElementById('siteHeader');
    var navCollapseEl = document.getElementById('mainNav');

    // Toggle a subtle shadow once the user scrolls past the hero fold.
    function updateHeaderState() {
      if (!header) return;
      if (window.scrollY > 12) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    }
    updateHeaderState();
    window.addEventListener('scroll', updateHeaderState, { passive: true });

    // Smooth scroll for same-page hash links (e.g. index.php#about).
    document.querySelectorAll('a[href*="#"]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var url = new URL(link.href, window.location.href);
        var samePage = url.pathname === window.location.pathname;
        var hash = url.hash;
        if (samePage && hash) {
          var target = document.querySelector(hash);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    });

    // Collapse the mobile nav after a link is selected.
    if (navCollapseEl) {
      navCollapseEl.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
          if (navCollapseEl.classList.contains('show') && window.bootstrap) {
            var collapseInstance = window.bootstrap.Collapse.getInstance(navCollapseEl) ||
              new window.bootstrap.Collapse(navCollapseEl, { toggle: false });
            collapseInstance.hide();
          }
        });
      });
    }

    // Expandable "About" panels — hovering a collapsed panel expands it
    // and collapses the others. Click is also wired up as a fallback for
    // touch devices, which don't fire hover events. Leaving the whole
    // strip resets it back to the first panel.
    var expandTabsWrap = document.getElementById('aboutExpandTabs');
    var expandTabs = document.querySelectorAll('.expand-tabs .expand-tab');
    if (expandTabs.length) {
      function activateTab(tab) {
        if (tab.classList.contains('active')) return;
        expandTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
      }

      // Only real mouse/trackpad devices get hover-to-expand + auto-reset
      // on mouse-leave. Phones/tablets report as not-hover-capable, so
      // they never get these listeners and rely on click alone — this
      // avoids phones' fake mouseenter/mouseleave events on tap causing
      // the panel to flicker open then immediately snap shut.
      var supportsHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

      expandTabs.forEach(function (tab) {
        tab.addEventListener('click', function () { activateTab(tab); });
        if (supportsHover) {
          tab.addEventListener('mouseenter', function () { activateTab(tab); });
        }
      });

      if (expandTabsWrap && supportsHover) {
        expandTabsWrap.addEventListener('mouseleave', function () {
          activateTab(expandTabs[0]);
        });
      }
    }

    // Blog accordion — click the + button next to a post title to expand
    // or collapse it. Multiple posts can be open at once.
    document.querySelectorAll('.blog-acc-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        btn.closest('.blog-acc-item').classList.toggle('open');
      });
    });
  });
})();




/* ==========================================================================
   OCTOVA — main.js
   Lusion-inspired interactions: preloader, particles, cursor, reveals,
   counters, interactive services, mobile menu, form helpers.
   ========================================================================== */
(function () {
    'use strict';

    /* ----------------------------------------------------------
     * 1. PRELOADER
     * ---------------------------------------------------------- */
    const preloader = document.getElementById('preloader');
    const preloaderCount = document.getElementById('preloaderCount');
    const preloaderBar = document.getElementById('preloaderBar');

    function runPreloader(callback) {
        if (!preloader) { callback(); return; }
        let finished = false;
        let progress = 0;
        const MIN_DURATION = 1400; // keep the intro cinematic
        const start = performance.now();

        function complete() {
            if (finished) return;
            finished = true;
            if (preloaderCount) preloaderCount.textContent = '100';
            if (preloaderBar) preloaderBar.style.width = '100%';
            setTimeout(function () {
                preloader.classList.add('done');
                document.body.classList.remove('locked');
                if (callback) callback();
            }, 220);
        }

        /* Hard fallback: never let the preloader block the page,
           even if requestAnimationFrame stalls. */
        setTimeout(complete, 3200);

        function tick(now) {
            if (finished) return;
            const elapsed = now - start;
            const t = Math.min(elapsed / MIN_DURATION, 1);
            /* ease-out progress */
            progress = Math.round((1 - Math.pow(1 - t, 3)) * 100);

            if (preloaderCount) preloaderCount.textContent = String(progress).padStart(2, '0');
            if (preloaderBar) preloaderBar.style.width = progress + '%';

            if (t < 1) requestAnimationFrame(tick);
            else complete();
        }
        requestAnimationFrame(tick);
    }

    /* ----------------------------------------------------------
     * 2. CUSTOM CURSOR (fine pointers only)
     * ---------------------------------------------------------- */
    const dot = document.getElementById('cursorDot');
    const ring = document.getElementById('cursorRing');
    const finePointer = window.matchMedia('(pointer: fine)').matches;

    if (finePointer && dot && ring) {
        document.body.classList.add('has-cursor');
        let mx = -100, my = -100, rx = -100, ry = -100;

        document.addEventListener('mousemove', function (e) {
            mx = e.clientX;
            my = e.clientY;
            dot.style.transform = 'translate(' + (mx - 3.5) + 'px, ' + (my - 3.5) + 'px)';
        });

        (function loop() {
            rx += (mx - rx) * 0.16;
            ry += (my - ry) * 0.16;
            ring.style.transform = 'translate(' + (rx - 19) + 'px, ' + (ry - 19) + 'px)';
            requestAnimationFrame(loop);
        })();

        const hoverables = 'a, button, input, select, textarea, label, .service-row, .why-card';
        document.addEventListener('mouseover', function (e) {
            if (e.target.closest(hoverables)) ring.classList.add('is-hover');
        });
        document.addEventListener('mouseout', function (e) {
            if (e.target.closest(hoverables)) ring.classList.remove('is-hover');
        });
    }

    /* ----------------------------------------------------------
     * 3. HERO PARTICLE FIELD (canvas 2D network)
     * ---------------------------------------------------------- */
    const canvas = document.getElementById('heroCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        let W = 0, H = 0, dpr = 1;
        const mouse = { x: null, y: null };

        function resize() {
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            W = canvas.offsetWidth;
            H = canvas.offsetHeight;
            canvas.width = W * dpr;
            canvas.height = H * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }

        function build() {
            const count = Math.min(Math.floor((W * H) / 14000), 110);
            particles = [];
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: Math.random() * W,
                    y: Math.random() * H,
                    vx: (Math.random() - 0.5) * 0.35,
                    vy: (Math.random() - 0.5) * 0.35,
                    r: Math.random() * 1.8 + 0.6
                });
            }
        }

        function draw() {
            ctx.clearRect(0, 0, W, H);
            const LINK_DIST = 130;

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > W) p.vx *= -1;
                if (p.y < 0 || p.y > H) p.vy *= -1;

                if (mouse.x !== null) {
                    const dx = mouse.x - p.x;
                    const dy = mouse.y - p.y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < 160 && dist > 0.001) {
                        p.x += (dx / dist) * 0.25;
                        p.y += (dy / dist) * 0.25;
                    }
                }

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(96,165,250,0.55)';
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const q = particles[j];
                    const dx = p.x - q.x;
                    const dy = p.y - q.y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < LINK_DIST) {
                        const alpha = (1 - dist / LINK_DIST) * 0.22;
                        ctx.strokeStyle = 'rgba(96,165,250,' + alpha + ')';
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(q.x, q.y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', function () { resize(); build(); });

        canvas.addEventListener('mousemove', function (e) {
            const rect = canvas.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        });
        canvas.addEventListener('mouseleave', function () {
            mouse.x = null;
            mouse.y = null;
        });

        resize();
        build();
        draw();
    }

    /* ----------------------------------------------------------
     * 4. NAV — scroll state + mobile menu
     * ---------------------------------------------------------- */
    const nav = document.getElementById('siteNav');
    const burger = document.getElementById('navBurger');
    const navLinks = document.getElementById('navLinks');

    function onScroll() {
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 40);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (burger && navLinks) {
        burger.addEventListener('click', function () {
            const open = navLinks.classList.toggle('is-open');
            burger.classList.toggle('is-open', open);
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.classList.toggle('locked', open);
        });
        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.classList.remove('is-open');
                burger.classList.remove('is-open');
                burger.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('locked');
            });
        });
    }

    /* ----------------------------------------------------------
     * 5. SCROLL REVEAL (observes .reveal and .hero-line)
     * ---------------------------------------------------------- */
    const revealEls = document.querySelectorAll('.reveal, .hero-line');

    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        revealEls.forEach(function (el) {
            /* elements in the hero cascade in AFTER the preloader,
               handled in the bootstrap step below */
            if (el.closest('.hero')) return;

            if (el.classList.contains('hero-line')) {
                /* cascade each hero line slightly later */
                const index = Array.prototype.indexOf.call(el.parentElement.children, el);
                el.style.transitionDelay = (0.12 * index + 0.1) + 's';
            }
            revealObserver.observe(el);
        });
    } else {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }

    /* ----------------------------------------------------------
     * 6. ANIMATED COUNTERS
     * ---------------------------------------------------------- */
    const counters = document.querySelectorAll('.count');

    function animateCounter(el) {
        const target = parseFloat(el.getAttribute('data-target')) || 0;
        const duration = 1600;
        const start = performance.now();

        function step(now) {
            const t = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - t, 4);
            el.textContent = Math.round(eased * target);
            if (t < 1) requestAnimationFrame(step);
            else el.textContent = target;
        }
        requestAnimationFrame(step);
    }

    if ('IntersectionObserver' in window && counters.length) {
        const counterObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.6 });
        counters.forEach(function (c) { counterObserver.observe(c); });
    } else {
        counters.forEach(function (c) { c.textContent = c.getAttribute('data-target'); });
    }

    /* ----------------------------------------------------------
     * 7. SERVICES — accordion rows + sticky preview sync
     * ---------------------------------------------------------- */
    const serviceRows = document.querySelectorAll('.service-row');
    const preview = document.getElementById('servicesPreview');
    const list = document.getElementById('servicesList');

    function syncPreview(row) {
        if (!preview) return;

        const no = row.querySelector('.service-no').textContent;
        const title = row.querySelector('.service-title').textContent;
        const desc = row.querySelector('.service-desc').textContent;
        const features = row.querySelectorAll('.service-features li');

        const previewNo = document.getElementById('previewNo');
        const previewTitle = document.getElementById('previewTitle');
        const previewDesc = document.getElementById('previewDesc');
        const previewFeatures = document.getElementById('previewFeatures');

        [previewDesc, previewFeatures].forEach(function (el) {
            if (el) el.classList.add('swapping');
        });

        setTimeout(function () {
            if (previewNo) previewNo.textContent = no;
            if (previewTitle) previewTitle.textContent = title;
            if (previewDesc) previewDesc.textContent = desc;
            if (previewFeatures) {
                previewFeatures.innerHTML = '';
                features.forEach(function (li) {
                    const copy = document.createElement('li');
                    copy.textContent = li.textContent;
                    previewFeatures.appendChild(copy);
                });
            }
            [previewDesc, previewFeatures].forEach(function (el) {
                if (el) el.classList.remove('swapping');
            });
        }, 200);
    }

    serviceRows.forEach(function (row) {
        const head = row.querySelector('.service-row-head');
        head.addEventListener('click', function () {
            const wasActive = row.classList.contains('active');
            serviceRows.forEach(function (r) {
                r.classList.remove('active');
                const b = r.querySelector('.service-row-head');
                if (b) b.setAttribute('aria-expanded', 'false');
            });
            if (!wasActive) {
                row.classList.add('active');
                head.setAttribute('aria-expanded', 'true');
            }
            if (window.innerWidth > 1024) {
                syncPreview(row.classList.contains('active') ? row : serviceRows[0]);
            }
        });
    });

    /* initialise the preview with the active (first) row */
    if (serviceRows.length) syncPreview(serviceRows[0]);

    /* ----------------------------------------------------------
     * 8. FORM — light client-side UX helpers
     * (server validation remains the source of truth)
     * ---------------------------------------------------------- */
    const form = document.querySelector('.cta-form');
    if (form) {
        if (form.hasAttribute('novalidate')) {
            form.addEventListener('submit', function () {
                const name = document.getElementById('name');
                const email = document.getElementById('email');
                const message = document.getElementById('message');

                [name, email, message].forEach(function (el) {
                    if (el) {
                        el.style.borderColor = '';
                        el.style.boxShadow = '';
                    }
                });

                let firstBad = null;
                const flag = function (el) {
                    el.style.borderColor = '#DC2626';
                    el.style.boxShadow = '0 0 0 4px rgba(220,38,38,0.12)';
                    if (!firstBad) firstBad = el;
                };

                if (!name.value.trim()) flag(name);
                if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) flag(email);
                if (!message.value.trim()) flag(message);

                if (firstBad) {
                    firstBad.focus({ preventScroll: false });
                    firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (event) event.preventDefault();
                }
            });
        }

        /* clear the red highlight as the user types */
        form.addEventListener('input', function (e) {
            if (e.target && e.target.style && e.target.style.borderColor === '#DC2626') {
                e.target.style.borderColor = '';
                e.target.style.boxShadow = '';
            }
        });
    }

    /* ----------------------------------------------------------
     * 9. BOOTSTRAP
     * ---------------------------------------------------------- */
    /* only lock scrolling when a preloader exists — otherwise the
       page would stay overflow:hidden since nothing removes the class */
    if (preloader) document.body.classList.add('locked');
    runPreloader(function () {
        /* gently fade in hero content after preloader unmasks */
        document.querySelectorAll('.hero .reveal, .hero .hero-line').forEach(function (el, i) {
            if (!el.classList.contains('is-visible')) {
                el.style.transitionDelay = (0.08 * i + 0.1) + 's';
                el.classList.add('is-visible');
            }
        });
    });
})();