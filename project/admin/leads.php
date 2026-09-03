<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// // ---- Auth guard --------------------------------------------------------
// // TODO: this check is a placeholder. Update it to match whatever
// // login.php actually sets in $_SESSION on a successful login
// // (e.g. $_SESSION['client_logged_in'] = true; or $_SESSION['user_id'] = ...).
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
// if (empty($_SESSION['client_logged_in']) && empty($_SESSION['user_id'])) {
//     header('Location: /client/login.php');
//     exit;
// }
// // -------------------------------------------------------------------------

$pageTitle = 'Contact Submissions';

// ---- Filter handling ----
$range = $_GET['range'] ?? 'all';
$startInput = $_GET['start'] ?? '';
$endInput   = $_GET['end'] ?? '';

$where = '';
$params = [];

if ($range === 'week') {
    $where = 'WHERE created_at >= (NOW() - INTERVAL 7 DAY)';
} elseif ($range === 'month') {
    $where = 'WHERE created_at >= (NOW() - INTERVAL 30 DAY)';
} elseif ($range === 'custom' && $startInput !== '' && $endInput !== '') {
    // Basic format guard — falls back to "all" if either date is malformed.
    $startDate = DateTime::createFromFormat('Y-m-d', $startInput);
    $endDate   = DateTime::createFromFormat('Y-m-d', $endInput);
    if ($startDate && $endDate) {
        $where = 'WHERE created_at BETWEEN :start AND :end';
        $params[':start'] = $startInput . ' 00:00:00';
        $params[':end']   = $endInput . ' 23:59:59';
    } else {
        $range = 'all';
    }
}

// ---- Fetch data (prepared statement — no user input concatenated into SQL) ----
$leads = [];
$fetchError = null;
try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "SELECT name, email, phone, company, message, created_at
         FROM contact_messages
         $where
         ORDER BY created_at DESC"
    );
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logError('Leads page query failed: ' . $e->getMessage());
    $fetchError = 'Could not load submissions right now. Please try again shortly.';
}

require __DIR__ . '/../includes/admin-header.php';
?>

<section class="section" style="padding-top:4rem;">
  <div class="container-custom">
    <div class="dash-header">
      <div>
        <span class="eyebrow">Client Portal</span>
        <h1 class="mt-3 mb-0">Contact Submissions</h1>
      </div>

      <form action="/admin/leads.php" method="GET" class="d-flex flex-wrap align-items-center gap-2" id="leadsFilterForm">
        <div class="filter-bar">
          <a href="/admin/leads.php?range=all" class="filter-btn <?= $range === 'all' ? 'active' : '' ?>">All</a>
          <a href="/admin/leads.php?range=week" class="filter-btn <?= $range === 'week' ? 'active' : '' ?>">Weekly</a>
          <a href="/admin/leads.php?range=month" class="filter-btn <?= $range === 'month' ? 'active' : '' ?>">Monthly</a>
        </div>
        <input type="hidden" name="range" value="custom">
        <input type="date" name="start" class="form-control form-control-custom" style="max-width:150px;"
               value="<?= e($startInput) ?>">
        <span class="text-secondary">to</span>
        <input type="date" name="end" class="form-control form-control-custom" style="max-width:150px;"
               value="<?= e($endInput) ?>">
        <button type="submit" class="btn btn-outline-dark">Apply</button>
      </form>
    </div>

    <div class="card-custom" style="padding:0; overflow:hidden;">
      <?php if ($fetchError): ?>
        <div class="form-status error m-3" role="alert"><?= e($fetchError) ?></div>
      <?php elseif (empty($leads)): ?>
        <p class="text-secondary text-center m-0 p-5">No submissions found for this range.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Message</th>
                <th>Submitted</th>
                <th class="data-table-view-col"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($leads as $lead):
                $submittedFormatted = date('d M Y', strtotime($lead['created_at']));
              ?>
                <tr>
                  <td class="data-table-truncate"><?= e($lead['name']) ?></td>
                  <td class="data-table-truncate"><a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a></td>
                  <td class="data-table-truncate"><?= e($lead['phone'] ?: '—') ?></td>
                  <td class="data-table-truncate"><?= e($lead['company'] ?: '—') ?></td>
                  <td class="data-table-truncate"><?= e($lead['message']) ?></td>
                  <td class="text-nowrap"><?= e($submittedFormatted) ?></td>
                  <td>
                    <button
                      type="button"
                      class="view-btn"
                      data-name="<?= e($lead['name']) ?>"
                      data-email="<?= e($lead['email']) ?>"
                      data-phone="<?= e($lead['phone'] ?: '—') ?>"
                      data-company="<?= e($lead['company'] ?: '—') ?>"
                      data-message="<?= e($lead['message']) ?>"
                      data-submitted="<?= e($submittedFormatted) ?>"
                    >View</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <p class="text-secondary small mt-3 mb-0"><?= count($leads) ?> result<?= count($leads) === 1 ? '' : 's' ?></p>
  </div>
</section>

<!-- ================= MESSAGE DETAIL MODAL ================= -->
<div class="msg-modal-overlay" id="msgModalOverlay">
  <div class="msg-modal">
    <div class="msg-modal-header">
      <div>
        <h3 id="msgModalName"></h3>
        <span class="msg-modal-meta" id="msgModalMeta"></span>
      </div>
      <button type="button" class="msg-modal-close" id="msgModalClose" aria-label="Close">&times;</button>
    </div>
    <div class="msg-modal-body">
      <div class="msg-modal-row">
        <label>Email</label>
        <p id="msgModalEmail"></p>
      </div>
      <div class="msg-modal-row">
        <label>Phone</label>
        <p id="msgModalPhone"></p>
      </div>
      <div class="msg-modal-row">
        <label>Company</label>
        <p id="msgModalCompany"></p>
      </div>
      <div class="msg-modal-row">
        <label>Message</label>
        <p id="msgModalMessage"></p>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const overlay = document.getElementById('msgModalOverlay');
  const closeBtn = document.getElementById('msgModalClose');
  const nameEl = document.getElementById('msgModalName');
  const metaEl = document.getElementById('msgModalMeta');
  const emailEl = document.getElementById('msgModalEmail');
  const phoneEl = document.getElementById('msgModalPhone');
  const companyEl = document.getElementById('msgModalCompany');
  const messageEl = document.getElementById('msgModalMessage');
  if (!overlay) return;

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      nameEl.textContent = btn.dataset.name || '';
      metaEl.textContent = btn.dataset.submitted || '';
      emailEl.textContent = btn.dataset.email || '';
      phoneEl.textContent = btn.dataset.phone || '';
      companyEl.textContent = btn.dataset.company || '';
      messageEl.textContent = btn.dataset.message || '';
      overlay.classList.add('show');
    });
  });

  const hide = () => overlay.classList.remove('show');
  closeBtn.addEventListener('click', hide);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) hide();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') hide();
  });
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>