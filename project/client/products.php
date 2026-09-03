<?php
declare(strict_types=1);
/**
 * ============================================================================
 *  OCTOVA — products.php (PRODUCT LINEUP PAGE)
 *  ---------------------------------------------------------------------------
 *  NVIDIA GeForce-inspired product page for the Octova brand.
 *  Standalone file — uses its own design system in style-products.css.
 *
 *  Sections:
 *    1. NAV      — fixed product header with links
 *    2. HERO     — headline + metrics, dot-grid backdrop
 *    3. TICKER   — infinite product strip
 *    4. LINEUP   — product cards (CRM, Sites, Boost, Data, Host)
 *    5. FEATURED — Octova CRM spotlight banner with app mock UI
 *    6. FEATURES — capability feature grid
 *    7. COMPARE  — product comparison table
 *    8. CTA BAND — big call-to-action + contact details
 *    9. FOOTER   — site links + floating WhatsApp button
 * ============================================================================
 */

/* ---- Config (Octova company + contact) ---- */
define('SITE_NAME', 'Octova');
define('SITE_TAGLINE', 'Technology Company');
define('SITE_VERSION', '2.0.1');
define('SITE_EMAIL', 'Hello@ocktova.com');
define('SITE_PHONE', '+91 730403260');
define('SITE_LOCATION', 'Available worldwide · Remote friendly');
define('SITE_DOMAIN', 'ocktova.com');
define('WHATSAPP_NUMBER', '91730403260');
define('WHATSAPP_DISPLAY', '+91 730403260');

/* ---- Products (CRM + products matching Octova services) ---- */
$products = [
    [
        'tag'    => 'Business',
        'hot'    => true,
        'name'   => 'Octova CRM',
        'blurb'  => 'Your entire business in one place.',
        'desc'   => 'A complete customer relationship platform — leads, pipelines, invoices and support, customised for growing teams.',
        'feats'  => ['Lead & contact management', 'Sales pipeline & follow-ups', 'Invoices, quotes & payments'],
    ],
    [
        'tag'    => 'Websites',
        'hot'    => false,
        'name'   => 'Octova Sites',
        'blurb'  => 'Design, launch, grow.',
        'desc'   => 'Build fast, secure, conversion-ready websites from scratch — no code required, fully customisable.',
        'feats'  => ['Drag-and-drop website builder', 'E-commerce & payments', 'SEO, blogging & analytics'],
    ],
    [
        'tag'    => 'Optimisation',
        'hot'    => false,
        'name'   => 'Octova Boost',
        'blurb'  => 'Faster. Better. Converting.',
        'desc'   => 'An all-in-one performance and user-experience suite that finds and fixes what is slowing your site down.',
        'feats'  => ['Speed & Core Web Vitals tuning', 'SEO audit & technical fixes', 'UX & conversion improvements'],
    ],
    [
        'tag'    => 'Data',
        'hot'    => false,
        'name'   => 'Octova Data',
        'blurb'  => 'Your data, always on.',
        'desc'   => 'Managed databases designed, migrated and monitored so your products stay fast, secure and available.',
        'feats'  => ['Schema design & migration', 'Automated backups & recovery', 'Monitoring & query optimisation'],
    ],
    [
        'tag'    => 'Infrastructure',
        'hot'    => false,
        'name'   => 'Octova Host',
        'blurb'  => 'Uptime you can rely on.',
        'desc'   => 'Blazing-fast, secure hosting with automatic scaling, SSL and 24/7 support around the clock.',
        'feats'  => ['Fast SSD hosting & CDN', 'SSL, DNS & security hardening', 'One-click deploys & monitoring'],
    ],
];

/* ---- Comparison table (capability × product) ---- */
$compare_cols = ['Octova CRM', 'Octova Sites', 'Octova Boost', 'Octova Data', 'Octova Host'];
$compare_rows = [
    ['label' => 'Customer & lead management', 'marks' => ['check', 'cross', 'cross', 'cross', 'cross']],
    ['label' => 'Website builder',            'marks' => ['cross', 'check', 'cross', 'cross', 'cross']],
    ['label' => 'Performance optimisation',   'marks' => ['lone',  'lone',  'check', 'cross', 'lone']],
    ['label' => 'Managed database & backups', 'marks' => ['cross', 'cross', 'cross', 'check', 'lone']],
    ['label' => 'Hosting, CDN & uptime SLAs', 'marks' => ['cross', 'check', 'cross', 'cross', 'check']],
    ['label' => '24/7 human support',         'marks' => ['check', 'check', 'check', 'check', 'check']],
];

/* ---- Capabilities (features) data ---- */
$eng_features = [
    ['icon' => 'rocket',  'title' => 'Performance first',   'text' => 'Sub-second loads, optimised queries and lean builds — speed is engineered in from day one, not patched on later.'],
    ['icon' => 'shield',  'title' => 'Secure by default',   'text' => 'Encryption, hardened configs and regular security audits protect every product and every byte of your data.'],
    ['icon' => 'cloud',   'title' => 'Cloud native',        'text' => 'Built on scalable cloud infrastructure with CDN delivery — your product grows as your traffic grows.'],
    ['icon' => 'refresh', 'title' => 'Always up to date',   'text' => 'Continuous updates, automated backups and zero-downtime deployments keep everything current and safe.'],
    ['icon' => 'support', 'title' => '24/7 human support',  'text' => 'Real engineers on call around the clock — direct answers fast, not ticket queues and chatbots.'],
    ['icon' => 'growth',  'title' => 'Built to scale',      'text' => 'Modular architecture means you can start small and add features, users and products without rebuilds.'],
];
/* ---- Product video (paste your link here) ----
   Supported: YouTube  (youtube.com/watch?v=…, youtu.be/…, /shorts/…)
              Vimeo    (vimeo.com/123456789)
              Direct   (.mp4 / .webm file URL — self-hosted video)
   Leave empty ('') to hide the whole video section. */
$product_video_url = 'https://youtu.be/Z5Kthk4n9No?si=am0OupuAlNcMrR23';

/* ---- Video embed helper: converts any supported link into an inline embed ---- */
function octova_video_embed(string $url): array
{
    $url = trim($url);
    if ($url === '') {
        return ['type' => 'none', 'src' => ''];
    }

    /* YouTube: watch?v=ID | youtu.be/ID | /shorts/ID | /embed/ID | /live/ID */
    if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{6,20})~', $url, $m)) {
        return ['type' => 'iframe', 'src' => 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0&modestbranding=1&playsinline=1'];
    }

    /* Vimeo: vimeo.com/ID (optionally /video/ID) */
    if (preg_match('~vimeo\.com/(?:video/)?(\d{6,12})~', $url, $m)) {
        return ['type' => 'iframe', 'src' => 'https://player.vimeo.com/video/' . $m[1] . '?dnt=1&title=0&byline=0'];
    }

    /* Direct video file (mp4 / webm / ogg) */
    if (preg_match('~\.(mp4|webm|ogg)(\?.*)?$~i', $url)) {
        return ['type' => 'video', 'src' => $url];
    }

    return ['type' => 'none', 'src' => ''];
}

/* ---- Icon helper ---- */

/* ---- Icon helper ---- */
function octova_product_icon(string $name): string
{
    $icons = [
        'rocket'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2c.7-.8.7-2 0-2.8-.8-.7-2.2-.7-3 .8Z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.9A12.9 12.9 0 0 1 22 2c0 2.7-.8 7.5-6 11a22.4 22.4 0 0 1-4 2Z"/><path d="M9 12H4s.6-3.3 2-4c1.6-.9 3 0 3 0"/><path d="M12 15v5s3.3-.6 4-2c.9-1.6 0-3 0-3"/></svg>',
        'shield'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>',
        'cloud'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 0 0 0-9 6 6 0 0 0-11.6 1.5A4 4 0 0 0 7 19h10.5Z"/></svg>',
        'refresh' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>',
        'support' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><path d="M12 17h.01"/></svg>',
        'growth'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>',
    ];
    return $icons[$name] ?? $icons['rocket'];
}

/* ---- Compare-cell helper ---- */
function octova_product_check(string $kind): string
{
    if ($kind === 'check') {
        return '<span class="check">✔</span>';
    }
    if ($kind === 'cross') {
        return '<span class="cross">✕</span>';
    }
    return '<span class="lone">•</span>';
}
?>
<?php
/* Shared site chrome - renders the MAIN WEBSITE NAVBAR (header.php).
 * config.php is required for APP_NAME used by the navbar. */
require_once __DIR__ . '/../includes/config.php';

$v    = SITE_VERSION;
$site = SITE_NAME;
$pageTitle     = $site . ' Products - CRM, Sites, Boost, Data & Host';
$pageHeadExtra = <<<HTML
    <meta name="description" content="Explore {$site} products: Octova CRM, Octova Sites, Octova Boost, Octova Data and Octova Host - engineered for speed, security and growth.">
    <meta name="theme-color" content="#0A1128">

    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='11' fill='%232563EB'/%3E%3Ccircle cx='20' cy='20' r='13' fill='%232563EB'/%3E%3Ccircle cx='20' cy='20' r='8' fill='%232563EB'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style-products.css?v={$v}">
HTML;

require __DIR__ . '/../includes/header.php';
?>

<!-- Custom cursor (desktop only) -->
<div class="cursor-dot" id="cursorDot" aria-hidden="true"></div>
<div class="cursor-ring" id="cursorRing" aria-hidden="true"></div>

<!-- Full-page background animation -->
<canvas id="prodBg" aria-hidden="true"></canvas>

<!-- ============ 1. HERO ============ -->
<section class="prod-hero" id="top">
    <div class="container">
        <p class="hero-kicker preveal"><span class="kicker-index">01</span>Software for service businesses</p>

        <h1 class="preveal">
            <span class="line-solid">OCTOVA</span>
            <span class="line-outline">PRODUCTS</span>
        </h1>

        <div class="hero-copy preveal">
            <p class="hero-lead">
                One connected suite for running a service business — manage customers with
                <span class="hl">Octova CRM</span>, launch sites, boost performance, organise data and
                host it all. <span class="hl-line">Engineered around the four services we deliver.</span>
            </p>
            <div class="hero-cta-row">
                <a class="btn btn-primary" href="#lineup">Explore the lineup <span aria-hidden="true">→</span></a>
                <div class="hero-minor-links">
                    <a class="text-link" href="#crm">See Octova CRM <span aria-hidden="true">↗</span></a>
                    <span class="link-sep" aria-hidden="true"></span>
                    <a class="text-link" href="service.php">Our services <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        </div>

        <a class="prod-hero-scroll preveal" href="#metrics" aria-label="Scroll down to metrics">
            <span class="scroll-label">Scroll</span>
            <span class="scroll-track" aria-hidden="true"><span class="scroll-thumb"></span></span>
        </a>
    </div>
</section>

<!-- ============ 1b. METRICS STRIP ============ -->
<section class="prod-metrics" id="metrics">
    <div class="container">
<div class="prod-metrics-grid">
            <div class="metric preveal">
                <div class="metric-top">
                    <span class="metric-index">01</span>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    </span>
                </div>
                <div class="metric-num"><span class="ot-count" data-target="5">0</span></div>
                <div class="metric-name">Products</div>
                <div class="metric-label">One connected ecosystem</div>
            </div>
            <div class="metric preveal">
                <div class="metric-top">
                    <span class="metric-index">02</span>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 5.4L20 8l-4 4.1.9 5.9L12 15.2 7.1 18l.9-5.9L4 8l5.6-.6z"/></svg>
                    </span>
                </div>
                <div class="metric-num"><span class="ot-count" data-target="120">0</span><small>+</small></div>
                <div class="metric-name">Projects</div>
                <div class="metric-label">Delivered across industries</div>
            </div>
            <div class="metric preveal">
                <div class="metric-top">
                    <span class="metric-index">03</span>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg>
                    </span>
                </div>
                <div class="metric-num"><span class="ot-count" data-target="99">0</span><small>%</small></div>
                <div class="metric-name">Uptime</div>
                <div class="metric-label">SLA-backed, in writing</div>
            </div>
            <div class="metric preveal">
                <div class="metric-top">
                    <span class="metric-index">04</span>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13a8 8 0 0 1 16 0"/><rect x="2.5" y="13" width="4" height="6" rx="1.5"/><rect x="17.5" y="13" width="4" height="6" rx="1.5"/><path d="M19.5 19a3 3 0 0 1-3 3H13"/></svg>
                    </span>
                </div>
                <div class="metric-num"><span class="ot-count" data-target="24">0</span><small>/7</small></div>
                <div class="metric-name">Support</div>
                <div class="metric-label">Real humans, never bots</div>
            </div>
        </div>
    </div>
</section>

<!-- ============ 3. TICKER ============ -->
<div class="prod-strip" aria-hidden="true">
    <div class="prod-strip-track">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="prod-strip-group">
                <span>Octova CRM</span><i>✦</i>
                <span>Octova Sites</span><i>✦</i>
                <span>Octova Boost</span><i>✦</i>
                <span>Octova Data</span><i>✦</i>
                <span>Octova Host</span><i>✦</i>
            </div>
        <?php endfor; ?>
    </div>
</div>

<!-- ============ 4. LINEUP ============ -->
<section class="section" id="lineup">
    <div class="container">
        <span class="sec-label preveal">The Lineup</span>
        <h2 class="sec-title preveal">One ecosystem,<br><em>five products.</em></h2>
        <p class="sec-sub preveal">Each product maps to an Octova service — and they all work together natively.</p>

        <div class="lineup-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card preveal">
                    <span class="tag">
                        <?= $product['hot'] ? '<i class="tag-dot" aria-hidden="true"></i>' : '' ?><?= htmlspecialchars($product['tag'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <h3>
                        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                        <small><?= htmlspecialchars($product['blurb'], ENT_QUOTES, 'UTF-8') ?></small>
                    </h3>
                    <p class="desc"><?= htmlspecialchars($product['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                    <ul class="feats">
                        <?php foreach ($product['feats'] as $feat): ?>
                            <li><?= htmlspecialchars($feat, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-cta">
                        <a href="#contact">Learn more <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 5. FEATURED CRM ============ -->
<section class="section featured" id="crm">
    <div class="container">
        <div class="featured-grid">
            <div class="preveal">
                <span class="sec-label">Flagship Product</span>
                <h2>Octova <em>CRM</em></h2>
                <p>Every customer, every deal, every follow-up — in one clean dashboard. Built for agencies, studios and services teams that live inside their inbox.</p>

                <ul class="feature-check-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Capture leads from your website, email and WhatsApp automatically.
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Drag deals through a visual pipeline with smart follow-up reminders.
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Send quotes and invoices, track payments and know exactly what is owed.
                    </li>
                </ul>

                <a class="btn btn-primary" href="#contact">Request a demo <span aria-hidden="true">→</span></a>
            </div>

            <div class="featured-visual preveal" aria-hidden="true">
                <div class="fv-head">
                    <span class="fv-dots"><i></i><i></i><i></i></span>
                    <span class="fv-tag">Octova CRM · Dashboard</span>
                </div>
                <div class="fv-body">
                    <div class="fv-row">
                        <span class="fv-chip">Deals won</span>
                        <div class="fv-name">₹ 4.8L</div>
                        <div class="fv-sub">This month</div>
                        <div class="fv-bar"><span style="width: 78%;"></span></div>
                    </div>
                    <div class="fv-row">
                        <span class="fv-chip green">New leads</span>
                        <div class="fv-name">86</div>
                        <div class="fv-sub">Last 30 days</div>
                        <div class="fv-bar"><span style="width: 64%;"></span></div>
                    </div>
                    <div class="fv-row">
                        <span class="fv-chip">Pipeline value</span>
                        <div class="fv-name">₹ 12.2L</div>
                        <div class="fv-sub">12 open deals</div>
                        <div class="fv-bar"><span style="width: 92%;"></span></div>
                    </div>
                    <div class="fv-row">
                        <span class="fv-chip green">Follow-ups due</span>
                        <div class="fv-name">7</div>
                        <div class="fv-sub">Across 6 clients</div>
                        <div class="fv-bar"><span style="width: 48%;"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ 5b. PRODUCT VIDEO ============ -->
<?php $octova_video = octova_video_embed($product_video_url); ?>
<?php if ($octova_video['type'] !== 'none'): ?>
<section class="section video-section" id="video">
    <div class="container">
        <span class="sec-label preveal">Watch</span>
        <h2 class="sec-title preveal">See it <em>in action.</em></h2>
        <p class="sec-sub preveal">A quick look at what Octova products can do for your business.</p>

        <div class="video-frame preveal">
            <?php if ($octova_video['type'] === 'iframe'): ?>
                <iframe
                    src="<?= htmlspecialchars($octova_video['src'], ENT_QUOTES, 'UTF-8') ?>"
                    title="Octova product video"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
            <?php else: ?>
                <video controls preload="metadata" playsinline poster="">
                    <source src="<?= htmlspecialchars($octova_video['src'], ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                    Your browser does not support embedded video.
                </video>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ 6. FEATURES ============ -->
<section class="section" id="features">
    <div class="container">
        <span class="sec-label preveal">Capabilities</span>
        <h2 class="sec-title preveal">Engineered like every<br><em>Octova service.</em></h2>
        <p class="sec-sub preveal">The same values that power our services are built directly into every product.</p>

        <div class="feature-grid">
            <?php foreach ($eng_features as $feat): ?>
                <article class="feat-card preveal">
                    <span class="feat-icon"><?= octova_product_icon($feat['icon']) ?></span>
                    <h3><?= htmlspecialchars($feat['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($feat['text'], ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 7. COMPARE ============ -->
<section class="section" id="compare">
    <div class="container">
        <span class="sec-label preveal">Compare</span>
        <h2 class="sec-title preveal">Which product<br><em>fits your workflow?</em></h2>
        <p class="sec-sub preveal">Pick and mix — every product works standalone or together.</p>

        <div class="compare-wrap preveal">
            <table class="compare">
                <thead>
                    <tr>
                        <th>Capability</th>
                        <?php foreach ($compare_cols as $col): ?>
                            <th class="product-head"><?= htmlspecialchars($col, ENT_QUOTES, 'UTF-8') ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compare_rows as $row): ?>
                        <tr>
                            <td class="row-label"><?= htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8') ?></td>
                            <?php foreach ($row['marks'] as $mark): ?>
                                <td><?= octova_product_check($mark) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ============ 8. CTA BAND ============ -->
<section class="section cta-band" id="contact">
    <div class="container">
        <h2 class="preveal">Ready to build with <em>Octova?</em></h2>
        <p class="preveal">Tell us which product fits your business — we’ll set up a free walkthrough and a migration plan. No pressure, no jargon.</p>

        <div class="prod-hero-actions preveal" style="justify-content:center;">
            <a class="btn btn-primary" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20Octova%2C%20I%27d%20like%20a%20product%20demo." target="_blank" rel="noopener">Book a free demo</a>
            <a class="btn btn-ghost" href="mailto:<?= SITE_EMAIL ?>">Email <?= SITE_EMAIL ?></a>
            <a class="btn btn-ghost" href="tel:<?= preg_replace('/[^0-9+]/', '', SITE_PHONE) ?>"><?= SITE_PHONE ?></a>
        </div>
    </div>
</section>

<!-- Floating WhatsApp -->
<a class="wa-float-2" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20Octova%2C%20I%20have%20a%20question%20about%20your%20products." target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.52 14.94L2 22l5.2-1.46A9.9 9.9 0 1 0 12.04 2Zm5.8 14.06c-.24.68-1.4 1.3-1.94 1.34-.5.05-1.12.24-3.77-.78-3.18-1.24-5.2-4.44-5.36-4.65-.16-.21-1.28-1.7-1.28-3.24 0-1.54.81-2.3 1.1-2.61.28-.32.62-.4.83-.4h.6c.19 0 .45-.07.7.53.26.62.87 2.14.95 2.29.08.16.13.35.02.56-.1.21-.15.34-.3.53-.16.18-.33.41-.47.55-.16.16-.32.33-.14.65.19.32.83 1.37 1.78 2.22 1.22 1.1 2.25 1.44 2.57 1.6.32.16.5.13.69-.08.18-.21.79-.93 1-1.25.21-.32.42-.27.7-.16.29.1 1.83.86 2.15 1.02.31.16.52.24.6.37.07.13.07.75-.17 1.43Z"/></svg>
</a>

<script src="../assets/js/products-bg.js?v=<?= SITE_VERSION ?>"></script>
<script>
    /* Custom cursor (fine pointers only — same behaviour as service page) */
    (function () {
        var dot = document.getElementById('cursorDot');
        var ring = document.getElementById('cursorRing');
        if (!window.matchMedia('(pointer: fine)').matches || !dot || !ring) return;

        document.body.classList.add('has-cursor');
        var mx = -100, my = -100, rx = -100, ry = -100;

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

        var hoverables = 'a, button, input, select, textarea, label, .product-card, .metric, .feat-card, .fv-row';
        document.addEventListener('mouseover', function (e) {
            if (e.target.closest(hoverables)) ring.classList.add('is-hover');
        });
        document.addEventListener('mouseout', function (e) {
            if (e.target.closest(hoverables)) ring.classList.remove('is-hover');
        });
    })();

    /* Scroll reveal */
    (function () {
        var els = document.querySelectorAll('.preveal');
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            els.forEach(function (el) { io.observe(el); });
        } else {
            els.forEach(function (el) { el.classList.add('is-visible'); });
        }
    })();

    /* Animated counters */
    (function () {
        var counters = document.querySelectorAll('.ot-count');
        function animate(el) {
            var target = parseFloat(el.getAttribute('data-target')) || 0;
            var duration = 1500;
            var start = performance.now();
            function step(now) {
                var t = Math.min((now - start) / duration, 1);
                var eased = 1 - Math.pow(1 - t, 4);
                el.textContent = Math.round(eased * target);
                if (t < 1) requestAnimationFrame(step);
                else el.textContent = target;
            }
            requestAnimationFrame(step);
        }
        if ('IntersectionObserver' in window && counters.length) {
            var cio = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        animate(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.6 });
            counters.forEach(function (el) { cio.observe(el); });
        } else {
            counters.forEach(function (el) { el.textContent = el.getAttribute('data-target'); });
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>