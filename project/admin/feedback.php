<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Data Entry';

$status = null; // 'success' | 'error'
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $client_id = cleanInput($_POST['client_id'] ?? '');
  $feedback = cleanInput($_POST['feedback'] ?? '');
  $sentiment = $_POST['sentiment'] ?? 'neutral';

  $allowed = ['positive', 'negative', 'neutral'];

  if ($client_id === '' || mb_strlen($client_id) > 64) {
    $status = 'error';
    $message = 'Please provide a valid Client ID (max 64 chars).';

  } elseif ($feedback === '' || mb_strlen($feedback) > 4000) {
    $status = 'error';
    $message = 'Please enter feedback (up to 4000 characters).';

  } elseif (!in_array($sentiment, $allowed, true)) {
    $status = 'error';
    $message = 'Invalid sentiment selected.';

  } else {
    try {
      $pdo = getDbConnection();

      // Ensure the provided client_id exists
      $check = $pdo->prepare(
        'SELECT 1 FROM clients WHERE client_id = :client_id LIMIT 1'
      );

      $check->execute([
        ':client_id' => $client_id
      ]);

      if (!$check->fetchColumn()) {
        $status = 'error';
        $message = 'Client ID not found. Please create the client first or use a valid Client ID.';
      } else {

        $stmt = $pdo->prepare(
          'INSERT INTO feedback 
          (client_id, feedback, sentiment, created_at) 
          VALUES (:client_id, :feedback, :sentiment, NOW())'
        );

        $stmt->execute([
          ':client_id' => $client_id,
          ':feedback'  => $feedback,
          ':sentiment' => $sentiment,
        ]);

        // Post/Redirect/Get
        $_SESSION['flash'] = [
          'status' => 'success',
          'message' => 'Feedback saved successfully.'
        ];

        redirectTo('/admin/feedback.php');
        exit;
      }

    } catch (PDOException $e) {
      logError('Data entry insert failed: ' . $e->getMessage());

      $status = 'error';
      $message = 'Server error saving feedback. Please try again later.';
    }
  }
}

// Handle flash messages from redirects
if (!empty($_SESSION['flash']) && is_array($_SESSION['flash'])) {
  $flash = $_SESSION['flash'];

  $status = $flash['status'] ?? $status;
  $message = $flash['message'] ?? $message;

  unset($_SESSION['flash']);
}
require __DIR__ . '/../includes/admin-header.php';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="dash-shell">

  <!-- Main Content Only -->
  <main class="dash-main w-100">

    <div class="dash-header">
      <div>
        <span class="eyebrow">Data Entry</span>

        <h2 class="mt-2 mb-0">
          Manual Feedback Entry
        </h2>
      </div>
    </div>

    <section class="section" style="padding-top:1rem;">

      <div class="container-custom">

        <div class="card-custom">

          <?php if ($status === 'success'): ?>

            <div class="form-status success" role="status">
              <?= e($message) ?>
            </div>

          <?php elseif ($status === 'error'): ?>

            <div class="form-status error" role="alert">
              <?= e($message) ?>
            </div>

          <?php endif; ?>


          <form action="/admin/feedback.php" method="POST">

            <div class="row g-3">

              <!-- Client ID -->
              <div class="col-md-6">

                <label
                  class="form-label-custom"
                  for="client_id"
                >
                  Client ID *
                </label>

                <input
                  class="form-control form-control-custom"
                  type="text"
                  id="client_id"
                  name="client_id"
                  maxlength="64"
                  required
                  value="<?= e($_POST['client_id'] ?? '') ?>"
                >

              </div>


              <!-- Feedback -->
              <div class="col-12">

                <label
                  class="form-label-custom"
                  for="feedback"
                >
                  Feedback *
                </label>

                <textarea
                  class="form-control form-control-custom"
                  id="feedback"
                  name="feedback"
                  rows="6"
                  maxlength="4000"
                  required
                ><?= e($_POST['feedback'] ?? '') ?></textarea>

              </div>


              <!-- Sentiment -->
              <div class="col-md-4">

                <label
                  class="form-label-custom"
                  for="sentiment"
                >
                  Sentiment
                </label>

                <select
                  class="form-control form-control-custom"
                  id="sentiment"
                  name="sentiment"
                >

                  <option
                    value="neutral"
                    <?= (($_POST['sentiment'] ?? '') === 'neutral') ? 'selected' : '' ?>
                  >
                    Neutral
                  </option>

                  <option
                    value="positive"
                    <?= (($_POST['sentiment'] ?? '') === 'positive') ? 'selected' : '' ?>
                  >
                    Positive
                  </option>

                  <option
                    value="negative"
                    <?= (($_POST['sentiment'] ?? '') === 'negative') ? 'selected' : '' ?>
                  >
                    Negative
                  </option>

                </select>

              </div>


              <!-- Save Button -->
              <div class="col-12">

                <button
                  type="submit"
                  class="btn btn-brass px-4"
                >
                  Save Feedback
                </button>

              </div>

            </div>

          </form>

        </div>

      </div>

    </section>

  </main>


  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  ></script>

</body>
</html>
<?php require __DIR__ . '/../includes/footer.php'; ?>