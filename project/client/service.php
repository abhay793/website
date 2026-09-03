<?php
declare(strict_types=1);
/**
 * ============================================================================
 *  OCTOVA — service.php (THE SINGLE PHP FILE FOR THE ENTIRE WEBSITE)
 *  ---------------------------------------------------------------------------
 *  Everything lives in this one file — no index.php, no config.php:
 *
 *    PART A — CONFIGURATION : company info, services, why-us, process, stats
 *    PART B — FORM HANDLING : contact form POST (validate, save to
 *                             /submissions, attempt email via mail())
 *    PART C — PAGE MARKUP   : the full one-page HTML template
 *
 *  Companion files in the same folder:
 *    - style.css  : design system (Lusion-inspired)
 *    - main.js    : interactions (preloader, particles, reveals…)
 * ============================================================================
 */

/* =====================================================================
 *  PART A — CONFIGURATION
 *  Edit these values to update the whole site.
 * ===================================================================== */

define('SITE_NAME', 'Octova');
define('SITE_TAGLINE', 'Technology Company');
define('SITE_VERSION', '1.0.0');

define('SITE_EMAIL', 'Hello@ocktova.com');
define('SITE_PHONE', '+91 730403260');
define('SITE_LOCATION', 'Available worldwide · Remote friendly');
define('SITE_DOMAIN', 'ocktova.com');

/* WhatsApp — digits only, international format (E.164: +91 730403260), no '+' or spaces. */
define('WHATSAPP_NUMBER', '91730403260');
define('WHATSAPP_DISPLAY', '+91 730403260');

/* --- Services --- */
$services = [
    [
        'no'       => '01',
        'title'    => 'Website Enhancement',
        'tagline'  => 'Revive & elevate',
        'desc'     => 'We audit, redesign and fine-tune existing websites into modern, fast, conversion-ready experiences — without rebuilding what already works.',
        'features' => [
            'UI/UX redesign & refreshing',
            'Performance & speed optimisation',
            'SEO & Core Web Vitals tuning',
            'Mobile & accessibility fixes',
            'Analytics & conversion setup',
        ],
    ],
    [
        'no'       => '02',
        'title'    => 'Database',
        'tagline'  => 'Batteries included',
        'desc'     => 'Reliable data is the backbone of every product. We design, migrate and manage databases that stay fast, secure and available around the clock.',
        'features' => [
            'Schema design & data modelling',
            'Migration & data cleanup',
            'Query & index optimisation',
            'Backups & disaster recovery',
            'Monitoring & reporting',
        ],
    ],
    [
        'no'       => '03',
        'title'    => 'Hosting',
        'tagline'  => 'Always online',
        'desc'     => 'From setup to scaling, we deploy your site on fast, secure infrastructure and keep it running smoothly — so you never worry about downtime.',
        'features' => [
            'Server setup & configuration',
            'SSL, DNS & security hardening',
            'One-click deployment pipelines',
            '24/7 uptime monitoring',
            'Performance & scale tuning',
        ],
    ],
    [
        'no'       => '04',
        'title'    => 'Website From Scratch',
        'tagline'  => 'Born to perform',
        'desc'     => 'A custom website designed and engineered for your brand — built from the ground up with clean code, thoughtful UX and room to grow.',
        'features' => [
            'Custom design & branding',
            'Modern front-end engineering',
            'CMS / admin panel included',
            'E-commerce & payments',
            'Launch, SEO & team training',
        ],
    ],
];

/* --- Why choose Octova --- */
$reasons = [
    [
        'title' => 'End-to-end delivery',
        'text'  => 'Strategy, design, hosting and support under one roof — one team, one contract, zero finger-pointing.',
        'icon'  => 'layers',
    ],
    [
        'title' => 'Fast & secure by default',
        'text'  => 'Performance and security are engineered in from day one — not bolted on after launch.',
        'icon'  => 'shield',
    ],
    [
        'title' => 'Transparent pricing',
        'text'  => 'Clear scopes and honest timelines. No hidden fees, no surprises on the invoice.',
        'icon'  => 'tag',
    ],
    [
        'title' => 'Support that answers',
        'text'  => 'Real humans, 24/7 coverage and responses measured in minutes — not tickets.',
        'icon'  => 'chat',
    ],
];

/* --- Process steps --- */
$process = [
    [
        'no'    => '01',
        'title' => 'Discover',
        'text'  => 'We dig into your goals, audience and existing setup to map exactly what “done” looks like.',
    ],
    [
        'no'    => '02',
        'title' => 'Design',
        'text'  => 'Wireframes and pixel-perfect design that matches your brand and converts your visitors.',
    ],
    [
        'no'    => '03',
        'title' => 'Build',
        'text'  => 'Clean, tested, production-ready code — with weekly demos so you always see progress.',
    ],
    [
        'no'    => '04',
        'title' => 'Launch & grow',
        'text'  => 'Deployment, monitoring and ongoing care. We stay long after launch day passes.',
    ],
];

/* --- Hero statistics --- */
$stats = [
    ['value' => 120, 'suffix' => '+', 'label' => 'Projects delivered'],
    ['value' => 98,  'suffix' => '%', 'label' => 'Client satisfaction'],
    ['value' => 40,  'suffix' => '+', 'label' => 'Active clients'],
    ['value' => 24,  'suffix' => '/7', 'label' => 'Support coverage'],
];
/* =====================================================================
 *  PART B — CONTACT FORM HANDLING (server-side)
 *  Validates POST, saves enquiries as JSON into /submissions,
 *  and attempts to email each enquiry to SITE_EMAIL via mail().
 * ===================================================================== */
$feedback    = ['type' => '', 'message' => ''];
$form_errors = [];
$form_values = ['name' => '', 'email' => '', 'service' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $form_values = [
        'name'    => trim((string)($_POST['name'] ?? '')),
        'email'   => trim((string)($_POST['email'] ?? '')),
        'service' => trim((string)($_POST['service'] ?? '')),
        'message' => trim((string)($_POST['message'] ?? '')),
    ];
    $honeypot = trim((string)($_POST['company'] ?? ''));

    if ($form_values['name'] === '') {
        $form_errors['name'] = 'Please let us know your name.';
    }
    if ($form_values['email'] === '' || !filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
        $form_errors['email'] = 'Please enter a valid email address.';
    }
    if ($form_values['message'] === '') {
        $form_errors['message'] = 'Please tell us a little about your project.';
    }

    /* Honeypot filled = spam bot. Silently pretend it worked. */
    if ($honeypot !== '') {
        $feedback = ['type' => 'success', 'message' => 'Thanks — your message has been received. We’ll get back to you within one business day.'];
    } elseif (empty($form_errors)) {
        $entry = [
            'received_at' => date('Y-m-d H:i:s T'),
            'name'        => $form_values['name'],
            'email'       => $form_values['email'],
            'service'     => $form_values['service'],
            'message'     => $form_values['message'],
        ];

        $saved = false;
        $dir   = __DIR__ . '/submissions';
        if (is_dir($dir) && is_writable($dir)) {
            $file = $dir . '/enquiry-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.json';
            $saved = file_put_contents($file, json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
        }

        $mailed = false;
        if (function_exists('mail')) {
            $subject = 'New enquiry from ' . $entry['name'];
            $body  = "Service : {$entry['service']}\n"
                   . "Name    : {$entry['name']}\n"
                   . "Email   : {$entry['email']}\n\n"
                   . "Message:\n{$entry['message']}\n";
            $headers  = "From: " . SITE_NAME . " <no-reply@" . SITE_DOMAIN . ">\r\n"
                      . "Reply-To: {$entry['email']}\r\n"
                      . "MIME-Version: 1.0\r\n"
                      . "Content-Type: text/plain; charset=UTF-8\r\n";
            $mailed = @mail(SITE_EMAIL, $subject, $body, $headers);
        }

        if ($saved || $mailed) {
            $feedback = [
                'type'    => 'success',
                'message' => 'Message received! Thanks, ' . htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8')
                           . '. We’ll reply within one business day.',
            ];
            $form_values = ['name' => '', 'email' => '', 'service' => '', 'message' => ''];
        } else {
            $feedback = [
                'type'    => 'error',
                'message' => 'We couldn’t submit your message just now. Please email us directly at ' . SITE_EMAIL . '.',
            ];
        }
    }
}

/* =====================================================================
 *  PART C — PAGE MARKUP (below)
 * ===================================================================== */
?>
<?php
/* =====================================================================
 *  Shared site chrome - renders the MAIN WEBSITE NAVBAR (header.php).
 *  config.php is required for APP_NAME used by the navbar.
 * ===================================================================== */
require_once __DIR__ . '/../includes/config.php';

$v    = SITE_VERSION;
$site = SITE_NAME;
$pageTitle     = $site . ' - Website Enhancement, Database, Hosting & Websites From Scratch';
$pageHeadExtra = <<<HTML
    <meta name="description" content="{$site} is a technology company delivering website enhancement, database management, hosting and custom websites built from scratch.">
    <meta name="theme-color" content="#0A1128">
    <meta property="og:title" content="{$site} - Digital services, engineered">
    <meta property="og:description" content="Website enhancement, database, hosting &amp; websites from scratch.">
    <meta property="og:type" content="website">

    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='11' fill='%232563EB'/%3E%3Ccircle cx='20' cy='20' r='13' fill='%232563EB'/%3E%3Ccircle cx='20' cy='20' r='8' fill='%232563EB'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/service.css?v={$v}">
    <script defer src="../assets/js/main.js?v={$v}"></script>
HTML;

require __DIR__ . '/../includes/header.php';
?>

<!-- ============ 2. CUSTOM CURSOR (desktop only) ============ -->
<div class="cursor-dot" id="cursorDot" aria-hidden="true"></div>
<div class="cursor-ring" id="cursorRing" aria-hidden="true"></div>

<!-- ============ 3. HERO ============ -->
<section class="hero" id="top">
    <canvas class="hero-canvas" id="heroCanvas" aria-hidden="true"></canvas>
    <div class="hero-glow hero-glow-a"></div>
    <div class="hero-glow hero-glow-b"></div>

    <div class="container hero-inner">
        <p class="hero-eyebrow reveal"><?= SITE_NAME ?> — <?= SITE_TAGLINE ?></p>

        <h1 class="hero-title">
            <span class="hero-line"><span class="hero-line-inner">We build the web</span></span>
            <span class="hero-line"><span class="hero-line-inner">that builds your</span></span>
            <span class="hero-line"><span class="hero-line-inner hero-outline">business.</span></span>
        </h1>

        <p class="hero-sub reveal">Website enhancement, database, hosting and websites from scratch — engineered end-to-end for speed, security and growth.</p>

        <div class="hero-actions reveal">
            <a class="btn btn-primary" href="#contact">Start a project</a>
            <a class="btn btn-ghost" href="#services">Explore services</a>
            <a class="btn btn-ghost" href="products.php">Our products <span aria-hidden="true">→</span></a>
        </div>

        <div class="hero-stats">
            <?php foreach ($stats as $stat): ?>
                <div class="hero-stat reveal">
                    <span class="hero-stat-num">
                        <span class="count" data-target="<?= $stat['value'] ?>">0</span><?= $stat['suffix'] ?>
                    </span>
                    <span class="hero-stat-label"><?= $stat['label'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <a class="hero-scroll" href="#services" aria-label="Scroll to services"><span></span></a>
</section>

<!-- ============ 4. MARQUEE STRIP ============ -->
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="marquee-group">
                <span>Website Enhancement</span><i>✦</i>
                <span>Database</span><i>✦</i>
                <span>Hosting</span><i>✦</i>
                <span>Websites From Scratch</span><i>✦</i>
            </div>
        <?php endfor; ?>
    </div>
</div>

<!-- ============ 5. SERVICES ============ -->
<section class="section services" id="services">
    <div class="container">
        <div class="sec-head reveal">
            <span class="sec-eyebrow">What we do</span>
            <h2 class="sec-title">Services engineered<br><em>for the modern web</em></h2>
            <p class="sec-sub">Four core services, one accountable partner. Select a service to explore what’s included.</p>
        </div>

        <div class="services-grid">
            <div class="services-list" id="servicesList">
                <?php foreach ($services as $i => $svc): ?>
                    <article class="service-row<?= $i === 0 ? ' active' : '' ?>" id="service-<?= $i ?>">
                        <button class="service-row-head" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                            <span class="service-no"><?= $svc['no'] ?></span>
                            <span class="service-title"><?= $svc['title'] ?></span>
                            <span class="service-tagline"><?= $svc['tagline'] ?></span>
                            <span class="service-arrow" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M9 7h8v8"/></svg>
                            </span>
                        </button>
                        <div class="service-details">
                            <p class="service-desc"><?= $svc['desc'] ?></p>
                            <ul class="service-features">
                                <?php foreach ($svc['features'] as $feature): ?>
                                    <li><?= $feature ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a class="service-link" href="#contact">Start this service <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="services-preview glass" id="servicesPreview" aria-live="polite">
                <span class="preview-no" id="previewNo">01</span>
                <h3 class="preview-title" id="previewTitle">Website Enhancement</h3>
                <p class="preview-desc" id="previewDesc"></p>
                <ul class="preview-features" id="previewFeatures"></ul>
                <a class="btn btn-primary btn-sm preview-cta" href="#contact">Get a free quote</a>
            </aside>
        </div>
    </div>
</section>

<?php
/* Small inline-icon helper for the "Why Octova" cards. */
if (!function_exists('octova_icon')) {
    function octova_icon(string $name): string
    {
        $icons = [
            'layers' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/></svg>',
            'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>',
            'tag'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.3 9.3a1.5 1.5 0 0 0 2.1 0l8.9-8.9a1.5 1.5 0 0 0 0-2.1L12 2Z"/><circle cx="7" cy="7" r="1.5"/></svg>',
            'chat'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.9 8.9 0 0 1-3.3-.7L3 21l1.6-5.4A8.4 8.4 0 1 1 21 11.5Z"/><path d="M8 10h8M8 14h5"/></svg>',
        ];
        return $icons[$name] ?? $icons['layers'];
    }
}
?>

<!-- ============ 6. WHY OCTOVA ============ -->
<section class="section why" id="why">
    <div class="container">
        <div class="sec-head reveal">
            <span class="sec-eyebrow">Why Octova</span>
            <h2 class="sec-title">A partner,<br><em>not just a vendor</em></h2>
        </div>

        <div class="why-grid">
            <?php foreach ($reasons as $reason): ?>
                <article class="why-card glass reveal">
                    <span class="why-icon"><?= octova_icon($reason['icon']) ?></span>
                    <h3><?= $reason['title'] ?></h3>
                    <p><?= $reason['text'] ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<!-- ============ 7. PROCESS ============ -->
<section class="section process" id="process">
    <div class="container">
        <div class="sec-head reveal">
            <span class="sec-eyebrow">How we work</span>
            <h2 class="sec-title">From idea to<br><em>launch, in four steps</em></h2>
        </div>

        <ol class="process-list">
            <?php foreach ($process as $step): ?>
                <li class="process-step reveal">
                    <span class="process-no"><?= $step['no'] ?></span>
                    <div class="process-body">
                        <h3 class="process-title"><?= $step['title'] ?></h3>
                        <p class="process-text"><?= $step['text'] ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ============ 8. CONTACT / CTA ============ -->
<section class="section cta" id="contact">
    <div class="container">
        <div class="cta-grid">
            <div class="cta-info reveal">
                <span class="sec-eyebrow">Contact</span>
                <h2 class="sec-title">Let’s build something<br><em>remarkable together</em></h2>
                <p class="cta-lead">Tell us where you are and where you want to go. We’ll reply within one business day — usually much faster.</p>

                <ul class="cta-contacts">
                    <li>
                        <span class="cta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        </span>
                        <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                    </li>
                    <li>
                        <span class="cta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/></svg>
                        </span>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', SITE_PHONE) ?>"><?= SITE_PHONE ?></a>
                    </li>
                    <li>
                        <span class="cta-icon cta-icon-wa" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2a9.9 9.9 0 0 0-8.52 14.94L2 22l5.2-1.46A9.9 9.9 0 1 0 12.04 2Zm5.8 14.06c-.24.68-1.4 1.3-1.94 1.34-.5.05-1.12.24-3.77-.78-3.18-1.24-5.2-4.44-5.36-4.65-.16-.21-1.28-1.7-1.28-3.24 0-1.54.81-2.3 1.1-2.61.28-.32.62-.4.83-.4h.6c.19 0 .45-.07.7.53.26.62.87 2.14.95 2.29.08.16.13.35.02.56-.1.21-.15.34-.3.53-.16.18-.33.41-.47.55-.16.16-.32.33-.14.65.19.32.83 1.37 1.78 2.22 1.22 1.1 2.25 1.44 2.57 1.6.32.16.5.13.69-.08.18-.21.79-.93 1-1.25.21-.32.42-.27.7-.16.29.1 1.83.86 2.15 1.02.31.16.52.24.6.37.07.13.07.75-.17 1.43Z"/></svg>
                        </span>
                        <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20Octova%2C%20I%27d%20like%20to%20talk%20about%20a%20project." target="_blank" rel="noopener"><?= WHATSAPP_DISPLAY ?></a>
                    </li>
                    <li>
                        <span class="cta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
                        </span>
                        <span class="cta-plain"><?= SITE_LOCATION ?></span>
                    </li>
                </ul>

                <div class="cta-badge">
                    <span class="cta-dot" aria-hidden="true"></span>
                    Average first response: <strong>under 2 hours</strong>
                </div>
            </div>
<form class="cta-form glass reveal" method="post" action="#contact" novalidate>
                <?php if ($feedback['type'] === 'success'): ?>
                    <div class="form-alert form-alert--success" role="status"><?= $feedback['message'] ?></div>
                <?php elseif ($feedback['type'] === 'error'): ?>
                    <div class="form-alert form-alert--error" role="alert"><?= $feedback['message'] ?></div>
                <?php endif; ?>

                <!-- Honeypot (hidden from humans, catches bots) -->
                <div class="hp-field" aria-hidden="true">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" tabindex="-1" autocomplete="off">
                </div>

                <div class="field">
                    <label for="name">Your name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($form_values['name'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Jane Doe" required>
                    <?php if (isset($form_errors['name'])): ?><span class="field-error"><?= $form_errors['name'] ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($form_values['email'], ENT_QUOTES, 'UTF-8') ?>" placeholder="jane@company.com" required>
                    <?php if (isset($form_errors['email'])): ?><span class="field-error"><?= $form_errors['email'] ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label for="service">Service you need</label>
                    <select id="service" name="service">
                        <option value="">Choose a service…</option>
                        <?php foreach ($services as $svc): ?>
                            <option value="<?= htmlspecialchars($svc['title'], ENT_QUOTES, 'UTF-8') ?>" <?= $form_values['service'] === $svc['title'] ? 'selected' : '' ?>><?= $svc['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="message">Project details</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tell us about your website, its goals and any deadlines…"><?= htmlspecialchars($form_values['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($form_errors['message'])): ?><span class="field-error"><?= $form_errors['message'] ?></span><?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block" name="contact_submit" value="1">Send message<span aria-hidden="true"> →</span></button>
                <p class="form-note">We reply within one business day. No spam, ever.</p>
            </form>
        </div>
    </div>
</section>

<!-- ============ 9. FLOATING WHATSAPP BUTTON + CLOSING TAGS ============ -->
<a class="wa-float" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20Octova%2C%20I%27d%20like%20to%20talk%20about%20a%20project." target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.52 14.94L2 22l5.2-1.46A9.9 9.9 0 1 0 12.04 2Zm5.8 14.06c-.24.68-1.4 1.3-1.94 1.34-.5.05-1.12.24-3.77-.78-3.18-1.24-5.2-4.44-5.36-4.65-.16-.21-1.28-1.7-1.28-3.24 0-1.54.81-2.3 1.1-2.61.28-.32.62-.4.83-.4h.6c.19 0 .45-.07.7.53.26.62.87 2.14.95 2.29.08.16.13.35.02.56-.1.21-.15.34-.3.53-.16.18-.33.41-.47.55-.16.16-.32.33-.14.65.19.32.83 1.37 1.78 2.22 1.22 1.1 2.25 1.44 2.57 1.6.32.16.5.13.69-.08.18-.21.79-.93 1-1.25.21-.32.42-.27.7-.16.29.1 1.83.86 2.15 1.02.31.16.52.24.6.37.07.13.07.75-.17 1.43Z"/></svg>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</section>