<?php
require_once __DIR__ . '/../includes/auth.php';
requireClientLogin();

$pageTitle = 'Dashboard';

// Pull quick stat counts server-side for the initial page render (fast,
// no flash of empty state). The charts below re-fetch live data via AJAX.
$totalFeedback = 0;
$positiveFeedback = 0;
$negativeFeedback = 0;

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT sentiment, COUNT(*) AS cnt FROM feedback WHERE client_id = :client_id GROUP BY sentiment');
    $stmt->execute([':client_id' => $_SESSION['client_id']]);
    foreach ($stmt->fetchAll() as $row) {
        $totalFeedback += (int) $row['cnt'];
        if ($row['sentiment'] === 'positive') {
            $positiveFeedback = (int) $row['cnt'];
        } elseif ($row['sentiment'] === 'negative') {
            $negativeFeedback = (int) $row['cnt'];
        }
    }
} catch (PDOException $e) {
    logError('Dashboard stat query failed: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dash-shell">
<div class="row g-0">
  <aside class="col-lg-2 dash-sidebar">
    <div class="brand">
      <span class="brand-mark" aria-hidden="true">MS</span>
      <span class="brand-name" style="color:#fff;"><?= e(APP_NAME) ?></span>
    </div>
    <nav class="dash-nav" aria-label="Dashboard navigation">
      <a href="/client/dashboard.php" class="active"><i class="fa-solid fa-gauge" aria-hidden="true"></i> Dashboard</a>
      <a href="/client/ai-solution.php"><i class="fa-solid fa-robot" aria-hidden="true"></i> AI Solution</a>
      <a href="/client/logout.php"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Logout</a>
    </nav>
  </aside>

  <main class="col-lg-10 dash-main">
    <div class="dash-header">
      <div>
        <span class="eyebrow">Client Dashboard</span>
        <h2 class="mt-2 mb-0">Welcome back, <?= e($_SESSION['company_name'] ?? $_SESSION['client_id']) ?></h2>
      </div>
      <span class="text-secondary small">Client ID: <?= e($_SESSION['client_id']) ?></span>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="metric-card accent-brass">
          <span class="metric-bar" aria-hidden="true"></span>
          <div class="metric-label">Total Feedback</div>
          <div class="metric-value" id="statTotal"><?= (int) $totalFeedback ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="metric-card accent-green">
          <span class="metric-bar" aria-hidden="true"></span>
          <div class="metric-label">Positive Feedback</div>
          <div class="metric-value" id="statPositive"><?= (int) $positiveFeedback ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="metric-card accent-red">
          <span class="metric-bar" aria-hidden="true"></span>
          <div class="metric-label">Negative Feedback</div>
          <div class="metric-value" id="statNegative"><?= (int) $negativeFeedback ?></div>
        </div>
      </div>
    </div>

    <div class="chart-panel mb-4">
      <h6>Feedback Trend (Last 14 Days)</h6>
      <canvas id="trendChart" height="90" role="img" aria-label="Line chart of feedback volume over the last 14 days"></canvas>
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="chart-panel text-center">
          <h6>Total Feedback</h6>
          <canvas id="doughnutTotal" role="img" aria-label="Doughnut chart of total feedback"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="chart-panel text-center">
          <h6>Positive Feedback</h6>
          <canvas id="doughnutPositive" role="img" aria-label="Doughnut chart of positive feedback"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="chart-panel text-center">
          <h6>Negative Feedback</h6>
          <canvas id="doughnutNegative" role="img" aria-label="Doughnut chart of negative feedback"></canvas>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="/assets/js/dashboard.js"></script>
</body>
</html>
