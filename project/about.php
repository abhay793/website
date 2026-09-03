<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
require __DIR__ . '/includes/header.php';

/**
 * Team roster. To add a new member, simply append an associative array
 * to this list — the markup below renders it dynamically, so no HTML
 * changes are required elsewhere.
 */
$teamMembers = [
    [
        'name'        => 'Aditya Pal',
        'position'    => 'Founder & CEO',
        'description' => '',
        'image'       => '/assets/images/team-1.jpg',
        'linkedin'    => 'https://www.linkedin.com/in/aditya--pal',
    ],
    [
        'name'        => 'Saurabh Gupta',
        // 'position'    => 'Co-Founder ',
        'description' => '',
        'image'       => '/assets/images/team-2.jpg',
        'linkedin'    => 'https://www.linkedin.com/in/itssaurabh7512',
    ],
    [
        'name'        => 'Lucky Singh',
        // 'position'    => 'Co-Founder ',
        'description' => '',
        'image'       => '/assets/images/team-3.jpg',
        'linkedin'    => 'https://www.linkedin.com/in/lucky-singh-2b6bb5330/',
    ],
    [
        'name'        => 'Abhay Yadav',
        // 'position'    => 'Co-Founder ',
        'description' => '',
        'image'       => '/assets/images/team-4.jpg',
        'linkedin'    => 'https://www.linkedin.com/in/itsabhay793',
    ],
];
?>




<!-- ================= PAGE INTRO ================= -->
<section class="section" style="padding-top:4rem;">
  <div class="container-custom">
    <span class="eyebrow">About Us</span>
    <h1 class="mt-3 mb-4" style="max-width:720px;">Building intelligent systems for businesses that move the world forward.</h1>
    <p class="text-secondary" style="max-width:720px; font-size:1.05rem;">
      Founded by a team of builders with backgrounds in AI, data science, and technology,
       we create software that simplifies  operations, automates workflows, and helps organizations scale with confidence. We combine technical excellence with a deep focus on solving real business problems—delivering products that are modern, reliable, and built for long-term growth.
    </p>
  </div>
</section>






<!-- ================= TEAM PHOTOS ================= -->
<section class="section section-tight">
  <div class="container-custom">
    <span class="eyebrow">Our Team</span>
    <h2 class="mt-3 mb-3" style="max-width:640px;">The people behind the systems.</h2>
    <p class="text-secondary" style="max-width:640px;">
      Founded by a group of college friends with backgrounds in AI, data science, and technology,
       we combine innovation, creativity, and technical expertise to build solutions that empower businesses to thrive
    </p>
    <div class="row g-4 mt-2">
      <?php foreach ($teamMembers as $member): ?>
      <div class="col-md-6 col-lg-3">
        <div class="team-card h-100">
          <div class="team-photo-wrap">
            <img src="<?= e($member['image']) ?>" alt="Portrait of <?= e($member['name']) ?>" loading="lazy">
            <div class="team-overlay">
              <a class="linkedin-link" href="<?= e($member['linkedin']) ?>" target="_blank" rel="noopener noreferrer"
                 aria-label="View <?= e($member['name']) ?>'s LinkedIn profile">
                <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
              </a>
            </div>
          </div>
          <div class="team-info">
            <div class="name"><?= e($member['name']) ?></div>
            <div class="position"><?= e($member['position']) ?></div>
            <p class="desc mb-0"><?= e($member['description']) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>






<section class="section section-tight" style="background:var(--ink-900); color:#fff;">
  <div class="container-custom">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="eyebrow">Why Choose Us</span>
        <h2 class="mt-3 mb-3" style="color:#fff;">Software partners who stay accountable after launch.</h2>
        <p style="color:rgba(255,255,255,0.7);">
          We don't disappear after go-live. Every engagement includes documentation,
          a clean codebase, and a team that understands the system well enough to
          extend it — not just patch it.
        </p>
      </div>
      <div class="col-lg-6">
        <ul class="list-unstyled d-flex flex-column gap-3">
          <li class="d-flex gap-3"><i class="fa-solid fa-check text-warning mt-1" aria-hidden="true"></i>
            <span>Powered by innovation and a passion for solving real-world business challenges.</span></li>
          <li class="d-flex gap-3"><i class="fa-solid fa-check text-warning mt-1" aria-hidden="true"></i>
            <span>Designed with scalability, security, and long-term growth in mind.</span></li>
          <li class="d-flex gap-3"><i class="fa-solid fa-check text-warning mt-1" aria-hidden="true"></i>
            <span>A hands-on team dedicated to building products that make an impact.</span></li>
          <li class="d-flex gap-3"><i class="fa-solid fa-check text-warning mt-1" aria-hidden="true"></i>
            <span>Transparent, fixed-scope delivery — no surprise change orders.</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>


<!-- ---------------------------------- -->



<!-- ================= REMAINING ABOUT CONTENT =================
<section class="section">
  <div class="container-custom">
    <span class="eyebrow">Core Values</span>
    <h2 class="mt-3 mb-4">What guides how we build</h2>
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-lock" aria-hidden="true"></i></div>
          <h6>Trust & Transparency</h6>
          <p class="text-secondary small mb-0">We believe in honest communication and lasting relationships.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-broom" aria-hidden="true"></i></div>
          <h6>Client Commitment</h6>
          <p class="text-secondary small mb-0">Your goals become our goals, from idea to execution.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i></div>
          <h6>Quality & Reliability</h6>
          <p class="text-secondary small mb-0">We build solutions that are secure, scalable, and dependable.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="value-card">
          <div class="icon-badge"><i class="fa-solid fa-handshake" aria-hidden="true"></i></div>
          <h6>Innovation with Purpose</h6>
          <p class="text-secondary small mb-0">We use technology to create meaningful value for businesses.</p>
        </div>
      </div>
    </div>
  </div>
</section> -->


<?php require __DIR__ . '/includes/footer.php'; ?>