<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="background-image:url('/assets/images/hero-bg.png');" id="home">
  
    <div class="hero-ctas d-flex flex-wrap gap-3">
      <!-- <a href="/contact.php" class="btn btn-brass">Start a Project</a> -->
      <!-- <a href="/client/login.php" class="btn btn-outline-light-custom">Client Portal</a> -->
    </div>
  </div>
    <a href="/contact.php" class="hero-bottom-cta">Get Started</a>
</section>



<!-- ============================================================
     OCKTOVA — About section with animated growth-graph card
     Drop this in place of the existing #about section in about.php.
     Requires: growth-card.css  and  growth-card.js
     ============================================================ -->

<!-- growth-card styles now live in your existing style.css — no separate stylesheet needed -->

<section class="section" id="about">
  <div class="container-custom">

    <div class="growth-card" id="octGrowthCard">

      <!-- ---------- Left: content (own column, never overlaps the chart) ---------- -->
      <div class="growth-card__content">
        <span class="eyebrow">About Us</span>
        <h2 class="mt-3 mb-3">Building technology that moves businesses forward.</h2>
        <p class="text-secondary">
          We’re a team of engineers and technology builders creating intelligent software for modern businesses.
          From automating everyday workflows to solving complex operational challenges, we combine thoughtful design, solid engineering, and practical AI to build solutions that create lasting value.
        </p>
        <a href="/about.php" class="btn btn-outline-dark mt-2">More About Us</a>

        <div class="stat-stack">
          <div class="stat-row">
            <div class="stat-num" data-count-to="10" data-suffix="+">0</div>
            <div class="stat-desc">Projects shipped</div>
          </div>
          <div class="stat-row">
            <div class="stat-num" data-count-to="1" data-pad="2">00</div>
            <div class="stat-desc">Years operating</div>
          </div>
          <div class="stat-row">
            <div class="stat-num" data-count-to="99.9" data-suffix="%" data-decimals="1">0%</div>
            <div class="stat-desc">Uptime SLA</div>
          </div>
        </div>
      </div>

      <!-- ---------- Right: animated growth chart, own column ---------- -->
      <div class="growth-card__chart" id="octChart">
        <svg viewBox="0 0 700 420" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
          <defs>
            <linearGradient id="octAfterGradient" x1="0%" y1="100%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#8AA9FF"/>
              <stop offset="55%" stop-color="#3B6BFF"/>
              <stop offset="100%" stop-color="#1734C4"/>
            </linearGradient>
            <filter id="octLineShadow" x="-40%" y="-40%" width="180%" height="180%">
              <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#3B6BFF" flood-opacity="0.28"/>
            </filter>
          </defs>

          <!-- smooth curves are generated at runtime by growth-card.js -->
          <path class="oct-line oct-line--before" id="octLineBefore" pathLength="1" />
          <path class="oct-line oct-line--after"  id="octLineAfter"  pathLength="1" filter="url(#octLineShadow)" />

          <!-- comet dot that travels the "after" line once it finishes drawing -->
          <circle class="oct-comet" id="octComet" r="6" />
        </svg>

        <!-- divider + logo marker sit in HTML so the linked logo is real, clickable markup -->
        <div class="oct-divider" id="octDivider"></div>

        <div class="oct-marker" id="octMarker">
          <a href="assets/images/logo.jpg" class="oct-logo-link" aria-label="Ocktova">
            <img src="assets/images/logo.jpg" alt="Ocktova logo" class="oct-logo-mark">
          </a>
          <span class="oct-marker-label">after Ocktova</span>
        </div>
      </div>

    </div>

  </div>
</section>

<script src="/assets/js/growth-card.js"></script>



<!-- ================= VISION & MISSION (moved to top, right after intro) ================= -->
<section class="section section-tight" style="background:var(--paper-200);">
  <div class="container-custom">
    <span class="eyebrow">What Drives Us</span>
    <h2 class="mt-3 mb-4">Mission, vision, and how we work</h2>
    <div class="expand-tabs" id="aboutExpandTabs">
      <div class="expand-tab active" style="background-image:url('/assets/images/about-mission.jpg');" data-tab="mission">
        <span class="expand-tab-label">Our Mission</span>
        <div class="expand-tab-content">
          <span class="eyebroww">Our Mission</span>
          <h3 class="mt-3 mb-3">Empowering businesses through intelligent technology</h3>
          <p>
            We build AI-powered B2B SaaS solutions that automate tasks, simplify workflows, 
            and boost productivity. Our secure, scalable platforms help businesses manage customers, 
            employees, and operations efficiently—supporting sustainable growth from startups to enterprises.
             We build AI-powered B2B SaaS solutions that automate tasks, simplify workflows, 
            and boost productivity. Our secure, scalable platforms help businesses manage customers, 
            employees, and operations efficiently—supporting sustainable growth from startups to enterprises.
          </p>
        </div>
      </div>
      <div class="expand-tab" style="background-image:url('/assets/images/about-vision.jpg');" data-tab="vision">
        <span class="expand-tab-label">Our Vision</span>
        <div class="expand-tab-content">
          <span class="eyebroww">Our Vision</span>
          <h3 class="mt-3 mb-3">Building the future of intelligent business automation</h3>
          <p>
            We envision an AI-driven future where intelligent systems automate everyday
            operations, allowing people to focus on creativity, innovation, strategy, and
            meaningful work. Our goal is simple: automate everything that can be
            automated, so businesses can focus on what humans do best — creating,
            innovating, and growing.
          </p>
        </div>
      </div>
      <div class="expand-tab" style="background-image:url('/assets/images/Trust.jpg');" data-tab="panel-3">
        <!-- TODO: swap the background-image path above for your own image -->
        <span class="expand-tab-label">Security & Trust</span>
        <div class="expand-tab-content">
          <span class="eyebroww">Security & Trust</span>
          <h3 class="mt-3 mb-3">Protecting what matters most</h3>
          <p>Trust is earned through responsibility and consistency. We take security and privacy seriously, applying thoughtful practices to protect sensitive information and maintain dependable services.
             From data handling to platform design, we work to create an environment where businesses can operate with confidence and peace of mind.</p>
        </div>
      </div>
      <div class="expand-tab" style="background-image:url('/assets/images/about-panel-4.jpg');" data-tab="panel-4">
        <!-- TODO: swap the background-image path above for your own image -->
        <span class="expand-tab-label">Our Approach</span>
        <div class="expand-tab-content">
          <span class="eyebroww">Our Approach</span>
          <h3 class="mt-3 mb-3">Technology designed around real business needs</h3>
          <p>We believe technology should solve real problems, not create unnecessary complexity. We take a practical, user-focused approach to every solution, combining thoughtful design with reliable technology.
             By understanding how businesses actually work, we create experiences that are simple, useful, and built to deliver lasting value.</p>
        </div>
      </div>
    </div>
  </div>
</section>  






<section class="section section-tight" style="background: var(--paper-200);">
  <div class="container-custom">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></div>
          <h5>Security First</h5>
          <p class="text-secondary mb-0">OWASP-aligned engineering, prepared statements, and hardened
          authentication on every client-facing surface.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-diagram-project" aria-hidden="true"></i></div>
          <h5>Built to Scale</h5>
          <p class="text-secondary mb-0">Modular architecture that grows from a single client
          dashboard to a multi-tenant platform without a rewrite.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-robot" aria-hidden="true"></i></div>
          <h5>Applied AI</h5>
          <p class="text-secondary mb-0">Conversational tooling and sentiment analytics wired
          directly into the systems your team already uses.</p>
        </div>
      </div>
    </div>
  </div>
</section>




<section class="section" id="contact">
  <div class="container-custom">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="eyebrow">Let's Talk</span>
        <h2 class="mt-3 mb-3">Have a project in mind?</h2>
        <p class="text-secondary mb-4">
          Tell us what you're building and we'll get back to you within one
          business day with next steps.
        </p>
        <a href="/contact.php" class="btn btn-brass">Go to Contact Page</a>
      </div>
      <div class="col-lg-6">
        <div class="card-custom">
          <div class="contact-info-item" style="border-bottom:none; padding-top:0;">
            <div class="icon-badge"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></div>
            <div>
              <strong>Office</strong>
              <p class="text-secondary mb-0">Mumbai, Maharashtra, India</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="icon-badge"><i class="fa-solid fa-phone" aria-hidden="true"></i></div>
            <div>
              <strong>Phone</strong>
              <p class="text-secondary mb-0">+91 730403260</p>
            </div>
          </div>
          <div class="contact-info-item" style="border-bottom:none;">
            <div class="icon-badge"><i class="fa-solid fa-envelope" aria-hidden="true"></i></div>
            <div>
              <strong>Email</strong>
              <p class="text-secondary mb-0">hello@ocktovs.com</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>