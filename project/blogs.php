<?php
/**
 * blogs.php
 * Resources / Blog listing page. Uses the shared site header.php + footer.php.
 *
 * HOW TO CONNECT REAL DATA LATER:
 * Replace the $posts array below with a query against your database or CMS.
 * Keep the same keys (title, excerpt, category, tags, date, read_time, slug)
 * and the filters/cards/styling all keep working as-is.
 */

// ---------------------------------------------------------------------
// 1. DUMMY DATA — swap this block for a DB query when you're ready
// ---------------------------------------------------------------------
$posts = [
    [
        "title"     => "5 Ways AI Chatbots Cut Customer Support Costs",
        "excerpt"   => "How growing teams are using conversational AI to resolve first-contact tickets without adding headcount.",
        "category"  => "AI",
        "tags"      => ["AI", "Customer Support", "Case Study"],
        "date"      => "2026-06-24",
        "read_time" => 6,
        "slug"      => "ai-chatbots-cut-support-costs",
    ],
    [
        "title"     => "Building Your First Automation Flow: A Step-by-Step Guide",
        "excerpt"   => "From trigger to go-live — a practical walkthrough for setting up your first automated workflow.",
        "category"  => "Automation",
        "tags"      => ["Automation", "Guide", "Getting Started"],
        "date"      => "2026-06-22",
        "read_time" => 8,
        "slug"      => "first-automation-flow-guide",
    ],
    [
        "title"     => "7 Cybersecurity Threats Small Businesses Face in 2026",
        "excerpt"   => "Phishing, credential stuffing, and supply-chain attacks are up. Here's what actually matters for lean teams.",
        "category"  => "Cybersecurity",
        "tags"      => ["Cybersecurity", "Risk", "Best Practices"],
        "date"      => "2026-06-19",
        "read_time" => 7,
        "slug"      => "cybersecurity-threats-small-business-2026",
    ],
    [
        "title"     => "Understanding Large Language Models: A Beginner's Guide",
        "excerpt"   => "No jargon, no hype — just a clear mental model for how LLMs actually generate a response.",
        "category"  => "AI",
        "tags"      => ["AI", "Guide", "Fundamentals"],
        "date"      => "2026-06-17",
        "read_time" => 9,
        "slug"      => "understanding-llms-beginners-guide",
    ],
    [
        "title"     => "Automating Customer Onboarding End-to-End",
        "excerpt"   => "Turn a five-step manual checklist into a self-running flow that emails, tags, and provisions accounts automatically.",
        "category"  => "Automation",
        "tags"      => ["Automation", "Onboarding", "Workflow"],
        "date"      => "2026-06-15",
        "read_time" => 5,
        "slug"      => "automating-customer-onboarding",
    ],
    [
        "title"     => "Multi-Factor Authentication: A Rollout Checklist",
        "excerpt"   => "A phased plan for getting MFA across your whole org without a support-ticket avalanche.",
        "category"  => "Cybersecurity",
        "tags"      => ["Cybersecurity", "Checklist", "IT"],
        "date"      => "2026-06-12",
        "read_time" => 4,
        "slug"      => "mfa-rollout-checklist",
    ],
    [
        "title"     => "AI-Powered Content Generation: Best Practices for 2026",
        "excerpt"   => "What to automate, what to keep human, and how to build a review step that actually catches mistakes.",
        "category"  => "AI",
        "tags"      => ["AI", "Content", "Best Practices"],
        "date"      => "2026-06-09",
        "read_time" => 6,
        "slug"      => "ai-content-generation-best-practices",
    ],
    [
        "title"     => "Ransomware Protection: A Checklist for Growing Teams",
        "excerpt"   => "Backups, segmentation, and the three settings most teams forget to turn on until it's too late.",
        "category"  => "Cybersecurity",
        "tags"      => ["Cybersecurity", "Checklist", "Ransomware"],
        "date"      => "2026-06-06",
        "read_time" => 5,
        "slug"      => "ransomware-protection-checklist",
    ],
    [
        "title"     => "The Future of Workflow Automation in SaaS Products",
        "excerpt"   => "Why 'automation' is quietly becoming a core product feature instead of an add-on integration.",
        "category"  => "Automation",
        "tags"      => ["Automation", "SaaS", "Trends"],
        "date"      => "2026-06-03",
        "read_time" => 7,
        "slug"      => "future-of-workflow-automation-saas",
    ],
];

// Sort newest first (this is what powers the "Recent" filter)
usort($posts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

// ---------------------------------------------------------------------
// 2. FILTER SETUP
// ---------------------------------------------------------------------
$filters = ["Recent", "AI", "Automation", "Cybersecurity"];

$category_glyph = [
    "AI"            => "AI",
    "Automation"    => "AT",
    "Cybersecurity" => "CS",
    "Recent"        => "OC",
];

function friendly_date(string $date): string {
    return date("d M", strtotime($date));
}

// ---------------------------------------------------------------------
// 3. SHARED SITE HEADER (your own includes)
// ---------------------------------------------------------------------
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Blogs';
require __DIR__ . '/includes/header.php';
?>

<style>
  /* Scoped styles for the blog listing — safe to move into style.css later */
  .blog-page{ background:#F5F6F8; }
  .blog-hero{ padding:64px 0 40px; text-align:center; }
  .blog-hero-eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:12px; font-weight:500;
    letter-spacing:0.14em; text-transform:uppercase;
    color:#00A87E;
    display:flex; align-items:center; justify-content:center; gap:8px;
    margin-bottom:16px;
  }
  .blog-hero-eyebrow::before, .blog-hero-eyebrow::after{
    content:""; width:22px; height:1px; background:#E4E7EC;
  }
  .blog-hero h1{
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size:clamp(28px, 4vw, 42px);
    line-height:1.15;
    letter-spacing:-0.02em;
    margin:0 auto 14px;
    max-width:720px;
    color:#10131A;
  }
  .blog-hero p{
    color:#5B6272;
    font-size:16px;
    max-width:520px;
    margin:0 auto;
  }

  .blog-filters{
    display:flex; justify-content:center; gap:10px;
    flex-wrap:wrap;
    padding:8px 0 48px;
  }
  .blog-filter-btn{
    font-family:'IBM Plex Mono', monospace;
    font-size:12.5px; font-weight:500;
    letter-spacing:0.04em; text-transform:uppercase;
    padding:10px 18px;
    border-radius:999px;
    border:1px solid #E4E7EC;
    background:#FFFFFF;
    color:#5B6272;
    cursor:pointer;
    transition:all .18s ease;
  }
  .blog-filter-btn:hover{ border-color:#10131A; color:#10131A; }
  .blog-filter-btn.active{ background:#10131A; border-color:#10131A; color:#fff; }

  .blog-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:28px;
    padding-bottom:80px;
  }
  @media (max-width:900px){ .blog-grid{grid-template-columns:repeat(2,1fr);} }
  @media (max-width:640px){ .blog-grid{grid-template-columns:1fr;} }

  .blog-card{
    background:#FFFFFF;
    border:1px solid #E4E7EC;
    border-radius:16px;
    overflow:hidden;
    display:flex; flex-direction:column;
    text-decoration:none; color:inherit;
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .blog-card:hover{
    transform:translateY(-4px);
    box-shadow:0 16px 32px -18px rgba(16,19,26,0.28);
    color:inherit;
  }
  .blog-card.hidden{ display:none; }

  .blog-thumb{
    position:relative;
    height:150px;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
  }
  .blog-thumb.cat-AI{ background:linear-gradient(135deg, #5B5FEF, #7C6CF2); }
  .blog-thumb.cat-Automation{ background:linear-gradient(135deg, #0EA57D, #35D0B3); }
  .blog-thumb.cat-Cybersecurity{ background:linear-gradient(135deg, #D64545, #F2793E); }

  .blog-hexgrid{
    position:absolute; inset:0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.18) 1px, transparent 1.6px);
    background-size:16px 16px;
    opacity:.55;
  }
  .blog-thumb-glyph{
    position:relative;
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size:15px;
    letter-spacing:0.08em;
    color:#fff;
    background:rgba(255,255,255,0.16);
    border:1px solid rgba(255,255,255,0.35);
    padding:6px 14px;
    border-radius:999px;
  }

  .blog-card-body{ padding:20px 20px 22px; display:flex; flex-direction:column; gap:10px; flex:1; }
  .blog-meta{
    font-family:'IBM Plex Mono', monospace;
    font-size:11.5px;
    color:#5B6272;
    letter-spacing:0.03em;
    display:flex; gap:8px; align-items:center;
  }
  .blog-meta .dot{ width:3px; height:3px; border-radius:50%; background:#5B6272; }

  .blog-card h3{
    font-family:'Sora', sans-serif;
    font-size:17px; line-height:1.35;
    margin:0; font-weight:600; letter-spacing:-0.01em;
    color:#10131A;
  }
  .blog-card p.excerpt{ font-size:13.5px; color:#5B6272; line-height:1.55; margin:0; }

  .blog-tag-row{ display:flex; flex-wrap:wrap; gap:6px; margin-top:auto; padding-top:6px; }
  .blog-tag{
    font-family:'IBM Plex Mono', monospace;
    font-size:10.5px; font-weight:500;
    letter-spacing:0.03em; text-transform:uppercase;
    padding:4px 9px;
    border-radius:6px;
    background:#F5F6F8;
    color:#5B6272;
    border:1px solid #E4E7EC;
  }
  .blog-tag.primary{ background:#E4F7F1; color:#00A87E; border-color:transparent; }

  .blog-empty-state{
    text-align:center;
    padding:70px 20px;
    color:#5B6272;
    font-family:'IBM Plex Mono', monospace;
    font-size:13px;
    display:none;
  }
</style>

<main class="blog-page">
  <section class="blog-hero container-custom">
    <div class="blog-hero-eyebrow">Ocktova Resources</div>
    <h1>Actionable insights to supercharge your automation, AI &amp; security workflows</h1>
    <p>Guides, playbooks, and updates from the team building Ocktova.</p>
  </section>

  <div class="container-custom">
    <div class="blog-filters" id="filters">
      <?php foreach ($filters as $i => $f): ?>
        <button class="blog-filter-btn <?= $i === 0 ? 'active' : '' ?>" data-filter="<?= htmlspecialchars($f) ?>">
          <?= htmlspecialchars($f) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <div class="blog-grid" id="postGrid">
      <?php foreach ($posts as $post):
          $cat = $post['category'];
      ?>
        <a href="/blog-single.php?slug=<?= urlencode($post['slug']) ?>"
           class="blog-card"
           data-category="<?= htmlspecialchars($cat) ?>">
          <div class="blog-thumb cat-<?= htmlspecialchars($cat) ?>">
            <div class="blog-hexgrid"></div>
            <span class="blog-thumb-glyph"><?= htmlspecialchars($category_glyph[$cat]) ?></span>
          </div>
          <div class="blog-card-body">
            <div class="blog-meta">
              <span><?= friendly_date($post['date']) ?></span>
              <span class="dot"></span>
              <span><?= (int)$post['read_time'] ?> min read</span>
            </div>
            <h3><?= htmlspecialchars($post['title']) ?></h3>
            <p class="excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
            <div class="blog-tag-row">
              <?php foreach ($post['tags'] as $ti => $tag): ?>
                <span class="blog-tag <?= $ti === 0 ? 'primary' : '' ?>"><?= htmlspecialchars($tag) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="blog-empty-state" id="emptyState">No posts in this category yet — check back soon.</div>
  </div>
</main>

<script>
  const blogFilterButtons = document.querySelectorAll('.blog-filter-btn');
  const blogCards = document.querySelectorAll('.blog-card');
  const blogEmptyState = document.getElementById('emptyState');

  blogFilterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      blogFilterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filter = btn.dataset.filter;
      let visibleCount = 0;

      blogCards.forEach(card => {
        const match = (filter === 'Recent') || (card.dataset.category === filter);
        card.classList.toggle('hidden', !match);
        if (match) visibleCount++;
      });

      blogEmptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    });
  });
</script>

<?php
// ---------------------------------------------------------------------
// 4. SHARED SITE FOOTER — uses footer.php if you have one, else closes
//    the tags header.php opened.
// ---------------------------------------------------------------------
if (file_exists(__DIR__ . '/includes/footer.php')) {
    require_once __DIR__ . '/includes/footer.php';
} else {
    echo "\n<!-- No includes/footer.php found — closing tags opened by header.php -->\n</body>\n</html>";
}