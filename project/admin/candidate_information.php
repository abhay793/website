<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Candidate Information';

$range = $_GET['range'] ?? 'all';
$where = '';

if ($range === 'week') {
    $where = 'WHERE created_at >= (NOW() - INTERVAL 7 DAY)';
} elseif ($range === 'month') {
    $where = 'WHERE created_at >= (NOW() - INTERVAL 30 DAY)';
} else {
    $range = 'all';
}

$candidates = [];
$fetchError = null;

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "SELECT id, name, address, phone, email, linkedin, github,
                portfolio, cover_letter, resume_path, role, created_at
         FROM candidates
         $where
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logError('Candidate information query failed: ' . $e->getMessage());
    $fetchError = 'Could not load candidate information right now. Please try again shortly.';
}

require __DIR__ . '/../includes/admin-header.php';
?>



<link rel="stylesheet" href="style.css">

<section class="section candidate-page">
  <div class="container-custom">
    <div class="candidate-header">
      <div>
        <span class="eyebrow">Admin</span>
        <h1 class="mt-3 mb-0">Candidate Information</h1>
      </div>

      <div class="filter-bar" aria-label="Filter candidates by submission date">
        <a href="/admin/candidate_information.php?range=all" class="filter-btn <?= $range === 'all' ? 'active' : '' ?>">All</a>
        <a href="/admin/candidate_information.php?range=week" class="filter-btn <?= $range === 'week' ? 'active' : '' ?>">Weekly</a>
        <a href="/admin/candidate_information.php?range=month" class="filter-btn <?= $range === 'month' ? 'active' : '' ?>">Monthly</a>
      </div>
    </div>

    <div class="card-custom candidate-table-card">
      <?php if ($fetchError): ?>
        <div class="form-status error m-3" role="alert"><?= e($fetchError) ?></div>
      <?php elseif (empty($candidates)): ?>
        <p class="text-secondary text-center m-0 p-5">No candidates found for this range.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="data-table candidate-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Resume</th>
                <th>Submitted</th>
                <th class="data-table-view-col"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($candidates as $candidate):
                  $submittedFormatted = date('d M Y', strtotime($candidate['created_at']));
                  $resumePath = ltrim((string) $candidate['resume_path'], '/');
                  $resumeName = basename($resumePath);
              ?>
                <tr>
                  <td class="data-table-truncate"><?= e($candidate['name']) ?></td>
                  <td class="data-table-truncate"><?= e($candidate['role']) ?></td>
                  <td class="data-table-truncate"><a href="mailto:<?= e($candidate['email']) ?>"><?= e($candidate['email']) ?></a></td>
                  <td class="data-table-truncate"><?= e($candidate['phone']) ?></td>
                  <td><a href="/<?= e($resumePath) ?>" download="<?= e($resumeName) ?>">Download</a></td>
                  <td class="text-nowrap"><?= e($submittedFormatted) ?></td>
                  <td>
                    <button
                      type="button"
                      class="view-btn"
                      data-name="<?= e($candidate['name']) ?>"
                      data-role="<?= e($candidate['role']) ?>"
                      data-address="<?= e($candidate['address']) ?>"
                      data-email="<?= e($candidate['email']) ?>"
                      data-phone="<?= e($candidate['phone']) ?>"
                      data-linkedin="<?= e($candidate['linkedin']) ?>"
                      data-github="<?= e($candidate['github']) ?>"
                      data-portfolio="<?= e($candidate['portfolio'] ?: '—') ?>"
                      data-cover-letter="<?= e($candidate['cover_letter'] ?: '—') ?>"
                      data-resume="<?= e($resumePath) ?>"
                      data-resume-name="<?= e($resumeName) ?>"
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

    <p class="candidate-count small mt-3 mb-0"><?= count($candidates) ?> candidate<?= count($candidates) === 1 ? '' : 's' ?></p>
  </div>
</section>

<div class="msg-modal-overlay" id="candidateModalOverlay">
  <div class="msg-modal" role="dialog" aria-modal="true" aria-labelledby="candidateModalName">
    <div class="msg-modal-header">
      <div>
        <h3 id="candidateModalName"></h3>
        <span class="msg-modal-meta" id="candidateModalMeta"></span>
      </div>
      <button type="button" class="msg-modal-close" id="candidateModalClose" aria-label="Close">&times;</button>
    </div>
    <div class="candidate-modal-body">
      <div class="candidate-modal-field"><label>Role</label><p id="candidateModalRole"></p></div>
      <div class="candidate-modal-field"><label>Email</label><p id="candidateModalEmail"></p></div>
      <div class="candidate-modal-field"><label>Phone</label><p id="candidateModalPhone"></p></div>
      <div class="candidate-modal-field"><label>Address</label><p id="candidateModalAddress"></p></div>
      <div class="candidate-modal-field"><label>LinkedIn</label><p><a id="candidateModalLinkedin" target="_blank" rel="noopener noreferrer"></a></p></div>
      <div class="candidate-modal-field"><label>GitHub</label><p><a id="candidateModalGithub" target="_blank" rel="noopener noreferrer"></a></p></div>
      <div class="candidate-modal-field"><label>Portfolio</label><p><a id="candidateModalPortfolio" target="_blank" rel="noopener noreferrer"></a></p></div>
      <div class="candidate-modal-field"><label>Resume</label><p><a id="candidateModalResume"></a></p></div>
      <div class="candidate-modal-field candidate-modal-field--wide"><label>Cover Letter</label><p id="candidateModalCoverLetter"></p></div>
    </div>
  </div>
</div>

<script>
(function () {
  const overlay = document.getElementById('candidateModalOverlay');
  const closeButton = document.getElementById('candidateModalClose');
  if (!overlay) return;

  const setText = (id, value) => {
    document.getElementById(id).textContent = value || '—';
  };
  const setLink = (id, value) => {
    const link = document.getElementById(id);
    let isSafeUrl = false;
    try {
      const url = new URL(value);
      isSafeUrl = url.protocol === 'http:' || url.protocol === 'https:';
    } catch (error) {
      isSafeUrl = false;
    }
    link.textContent = isSafeUrl ? value : '—';
    link.href = isSafeUrl ? value : '#';
  };

  document.querySelectorAll('.view-btn').forEach((button) => {
    button.addEventListener('click', () => {
      setText('candidateModalName', button.dataset.name);
      setText('candidateModalMeta', button.dataset.submitted);
      setText('candidateModalRole', button.dataset.role);
      setText('candidateModalAddress', button.dataset.address);
      setText('candidateModalEmail', button.dataset.email);
      setText('candidateModalPhone', button.dataset.phone);
      setLink('candidateModalLinkedin', button.dataset.linkedin);
      setLink('candidateModalGithub', button.dataset.github);
      setLink('candidateModalPortfolio', button.dataset.portfolio);
      setText('candidateModalCoverLetter', button.dataset.coverLetter);

      const resume = document.getElementById('candidateModalResume');
      resume.textContent = button.dataset.resumeName || 'Download resume';
      resume.href = '/' + (button.dataset.resume || '');
      resume.download = button.dataset.resumeName || '';

      overlay.classList.add('show');
    });
  });

  const hide = () => overlay.classList.remove('show');
  closeButton.addEventListener('click', hide);
  overlay.addEventListener('click', (event) => {
    if (event.target === overlay) hide();
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') hide();
  });
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>