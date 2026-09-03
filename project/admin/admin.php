<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// // ---- Auth guard --------------------------------------------------------
// // TODO: this check is a placeholder. Update it to match whatever
// // login.php actually sets in $_SESSION on a successful login
// // (e.g. $_SESSION['admin_logged_in'] = true;)
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
// if (empty($_SESSION['admin_logged_in'])) {
//     header('Location: /client/login.php');
//     exit;
// }
// // -------------------------------------------------------------------------

$pageTitle = 'Admin Panel';

/**
 * Small helper: safely count rows in a table without breaking the whole
 * page if a table doesn't exist yet or a query fails.
 */
function safeCount(PDO $pdo, string $table): ?int
{
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        logError("Admin dashboard count failed for {$table}: " . $e->getMessage());
        return null;
    }
}

$counts = [
    'feedback' => null,
    'career'   => null,
    'blog'     => null,
    'contact'  => null,
];

try {
    $pdo = getDbConnection();
    // TODO: adjust these table names to match your actual schema.
    $counts['feedback'] = safeCount($pdo, 'feedback_messages');
    $counts['career']   = safeCount($pdo, 'career_applications');
    $counts['blog']     = safeCount($pdo, 'blog_posts');
    $counts['contact']  = safeCount($pdo, 'contact_messages');
} catch (PDOException $e) {
    logError('Admin dashboard DB connection failed: ' . $e->getMessage());
}

require __DIR__ . '/../includes/admin-header.php';
?>

<!-- Admin-only stylesheet — only needed on admin pages, so it's kept separate
     from the main site CSS instead of being loaded on every page. -->
<link rel="stylesheet" href="/assets/css/admin.css"> 

<section class="section" style="padding-top:4rem;">
  <div class="container-custom">

    <div class="admin-hero">
      <div>
        <span class="eyebrow">Admin</span>
        <h1 class="mt-3 mb-0">Admin Panel</h1>
      </div>
      <span class="admin-hero-date"><?= e(date('d M Y')) ?></span>
    </div>

    <div class="admin-grid">

      <a href="/admin/feedback.php" class="admin-card" style="--admin-accent:#2f6fed;">
        <div class="admin-card-icon"><i class="fa-solid fa-comment-dots" aria-hidden="true"></i></div>
        <div class="admin-card-title">Feedback Entries</div>
        <p class="admin-card-desc">Review feedback submitted by clients and site visitors.</p>
        <div class="admin-card-footer">
          <div>
            <span class="admin-card-count"><?= $counts['feedback'] !== null ? e((string) $counts['feedback']) : '—' ?></span>
            <span class="admin-card-count-label">Total</span>
          </div>
          <span class="admin-card-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </div>
      </a>

      <a href="/admin/candidate_information.php" class="admin-card" style="--admin-accent:#d97706;">
        <div class="admin-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></div>
        <div class="admin-card-title">Career Queries</div>
        <p class="admin-card-desc">View job applications and career-related inquiries.</p>
        <div class="admin-card-footer">
          <div>
            <span class="admin-card-count"><?= $counts['career'] !== null ? e((string) $counts['career']) : '—' ?></span>
            <span class="admin-card-count-label">Total</span>
          </div>
          <span class="admin-card-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </div>
      </a>

      <a href="/admin/blogs_entry.php" class="admin-card" style="--admin-accent:#16a34a;">
        <div class="admin-card-icon"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i></div>
        <div class="admin-card-title">Blog Entries</div>
        <p class="admin-card-desc">Create, edit, and manage published blog posts.</p>
        <div class="admin-card-footer">
          <div>
            <span class="admin-card-count"><?= $counts['blog'] !== null ? e((string) $counts['blog']) : '—' ?></span>
            <span class="admin-card-count-label">Total</span>
          </div>
          <span class="admin-card-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </div>
      </a>

      <a href="/admin/leads.php" class="admin-card" style="--admin-accent:#9333ea;">
        <div class="admin-card-icon"><i class="fa-solid fa-address-book" aria-hidden="true"></i></div>
        <div class="admin-card-title">Contact Details</div>
        <p class="admin-card-desc">Browse contact form submissions from the website.</p>
        <div class="admin-card-footer">
          <div>
            <span class="admin-card-count"><?= $counts['contact'] !== null ? e((string) $counts['contact']) : '—' ?></span>
            <span class="admin-card-count-label">Total</span>
          </div>
          <span class="admin-card-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        </div>
      </a>

    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>