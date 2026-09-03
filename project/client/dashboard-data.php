<?php
/**
 * dashboard-data.php
 * JSON endpoint consumed by assets/js/dashboard.js. Requires an
 * authenticated client session. Returns feedback totals, sentiment
 * breakdown, and a 14-day trend series scoped to the logged-in client.
 */
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

// Reuse the same guard as the page itself, but respond with JSON on failure
// instead of redirecting, since this is an AJAX endpoint.
if (empty($_SESSION['client_id']) || empty($_SESSION['authenticated'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}
if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT_SECONDS) {
    http_response_code(401);
    echo json_encode(['error' => 'Session expired.']);
    exit;
}
$_SESSION['last_activity'] = time();

$response = [
    'total'    => 0,
    'positive' => 0,
    'negative' => 0,
    'trend'    => ['labels' => [], 'values' => []],
];

try {
    $pdo = getDbConnection();
    $clientId = $_SESSION['client_id'];

    // Sentiment totals.
    $stmt = $pdo->prepare('SELECT sentiment, COUNT(*) AS cnt FROM feedback WHERE client_id = :client_id GROUP BY sentiment');
    $stmt->execute([':client_id' => $clientId]);
    foreach ($stmt->fetchAll() as $row) {
        $response['total'] += (int) $row['cnt'];
        if ($row['sentiment'] === 'positive') {
            $response['positive'] = (int) $row['cnt'];
        } elseif ($row['sentiment'] === 'negative') {
            $response['negative'] = (int) $row['cnt'];
        }
    }

    // 14-day trend, filling in any missing days with zero.
    $trendStmt = $pdo->prepare(
        'SELECT DATE(created_at) AS day, COUNT(*) AS cnt
         FROM feedback
         WHERE client_id = :client_id AND created_at >= (CURDATE() - INTERVAL 13 DAY)
         GROUP BY DATE(created_at)
         ORDER BY day ASC'
    );
    $trendStmt->execute([':client_id' => $clientId]);
    $byDay = [];
    foreach ($trendStmt->fetchAll() as $row) {
        $byDay[$row['day']] = (int) $row['cnt'];
    }

    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $response['trend']['labels'][] = date('M j', strtotime($date));
        $response['trend']['values'][] = $byDay[$date] ?? 0;
    }

    echo json_encode($response);
} catch (PDOException $e) {
    logError('Dashboard data query failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load dashboard data.']);
}
