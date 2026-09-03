<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// If already authenticated, skip straight to the dashboard.
if (!empty($_SESSION['client_id']) && !empty($_SESSION['authenticated'])) {
    redirectTo('/client/dashboard.php');
}

$pageTitle = 'Client Login';
$errorMessage = '';

// Friendly explanation when redirected here by the auth middleware.
if (isset($_GET['reason'])) {
    if ($_GET['reason'] === 'timeout') {
        $errorMessage = 'Your session expired due to inactivity. Please log in again.';
    } elseif ($_GET['reason'] === 'auth') {
        $errorMessage = 'Please log in to access the client portal.';
    }
}

// Very small in-memory rate limiter keyed on session, to slow down
// brute-force attempts without needing an extra database table.
if (empty($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_locked_until'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Your session expired. Please refresh the page and try again.';
    } elseif (time() < ($_SESSION['login_locked_until'] ?? 0)) {
        $errorMessage = 'Too many failed attempts. Please try again in a minute.';
    } else {
        $clientId = cleanInput($_POST['client_id'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($clientId === '' || $password === '') {
            $errorMessage = 'Please enter both your Client ID and password.';
        } else {
            try {
                $pdo = getDbConnection();
                // Prepared statement — prevents SQL injection.
                $stmt = $pdo->prepare(
                    'SELECT id, client_id, company_name, password FROM clients WHERE client_id = :client_id LIMIT 1'
                );
                $stmt->execute([':client_id' => $clientId]);
                $client = $stmt->fetch();

                // Verify the hashed password. password_verify() handles the
                // comparison in constant time internally.
                if ($client && password_verify($password, $client['password'])) {
                    // Prevent session fixation by issuing a fresh session ID.
                    session_regenerate_id(true);

                    $_SESSION['authenticated']  = true;
                    $_SESSION['client_id']      = $client['client_id'];
                    $_SESSION['client_db_id']   = $client['id'];
                    $_SESSION['company_name']   = $client['company_name'];
                    $_SESSION['last_activity']  = time();
                    $_SESSION['login_attempts'] = 0;

                    redirectTo('/client/dashboard.php');
                } else {
                    $_SESSION['login_attempts']++;
                    if ($_SESSION['login_attempts'] >= 5) {
                        $_SESSION['login_locked_until'] = time() + 60;
                        $_SESSION['login_attempts'] = 0;
                    }
                    $errorMessage = 'Invalid Client ID or password.';
                }
            } catch (PDOException $e) {
                logError('Login query failed: ' . $e->getMessage());
                $errorMessage = 'A server error occurred. Please try again later.';
            }
        }
    }
}
?>
<?php
/* Shared site chrome — renders the MAIN WEBSITE NAVBAR (default design).
 * header.php already loads the fonts, Bootstrap and style.css used here. */
$pageTitle     = $pageTitle ?? 'Client Login';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">';
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-shell">
  <div class="auth-container">

    <!-- Left panel: illustration + pitch -->
    <div class="auth-visual">
      <div class="auth-illustration" aria-hidden="true">
        <div class="glow"></div>
        <svg viewBox="0 0 340 260" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="cubeFace1" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#2563EB" stop-opacity="0.55"/>
              <stop offset="100%" stop-color="#38BDF8" stop-opacity="0.35"/>
            </linearGradient>
            <linearGradient id="cubeFace2" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#1E293B" stop-opacity="0.9"/>
              <stop offset="100%" stop-color="#334155" stop-opacity="0.9"/>
            </linearGradient>
          </defs>
          <!-- scattered geometric cubes, evokes the reference "AI cluster" artwork -->
          <g stroke="rgba(56,189,248,0.35)" stroke-width="1">
            <polygon points="170,40 210,60 170,80 130,60" fill="url(#cubeFace1)"/>
            <polygon points="130,60 170,80 170,120 130,100" fill="url(#cubeFace2)"/>
            <polygon points="210,60 170,80 170,120 210,100" fill="url(#cubeFace1)"/>

            <polygon points="120,90 155,108 120,126 85,108" fill="url(#cubeFace2)"/>
            <polygon points="85,108 120,126 120,160 85,142" fill="url(#cubeFace1)"/>
            <polygon points="155,108 120,126 120,160 155,142" fill="url(#cubeFace2)"/>

            <polygon points="215,95 250,113 215,131 180,113" fill="url(#cubeFace1)"/>
            <polygon points="180,113 215,131 215,165 180,147" fill="url(#cubeFace2)"/>
            <polygon points="250,113 215,131 215,165 250,147" fill="url(#cubeFace1)"/>

            <polygon points="165,135 200,153 165,171 130,153" fill="url(#cubeFace2)"/>
            <polygon points="130,153 165,171 165,205 130,187" fill="url(#cubeFace1)"/>
            <polygon points="200,153 165,171 165,205 200,187" fill="url(#cubeFace2)"/>
          </g>
        </svg>
      </div>

      <h2>Systems built to earn your trust.</h2>
      <p>Access your dashboards, reports, and project status in one secure workspace — built for organizations that need things to simply work.</p>

      <ul class="auth-feature-list">
        <li><span class="check"><i class="fa-solid fa-check"></i></span> End-to-end encrypted sessions</li>
        <li><span class="check"><i class="fa-solid fa-check"></i></span> Role-based access control</li>
        <li><span class="check"><i class="fa-solid fa-check"></i></span> Audit-logged activity</li>
      </ul>
    </div>

    <!-- Right panel: login card (all original PHP-driven logic preserved below) -->
    <div class="auth-card">
      <a href="/index.php" class="brand-mark-link" aria-label="Back to homepage">
        <span class="brand-mark">MS</span>
        <span class="brand-name-inline">Meridian Systems</span>
      </a>

      <span class="auth-badge"><span class="dot"></span> Secure Client Portal</span>

      <h1>Welcome back</h1>
      <p class="sub">Sign in with your Client ID and password to access your dashboard.</p>

      <?php if ($errorMessage): ?>
        <div class="auth-error" role="alert"><?= e($errorMessage) ?></div>
      <?php endif; ?>

      <form action="/client/login.php" method="POST" novalidate>
        <?= csrfField() ?>

        <label for="client_id">Client ID</label>
        <input type="text" id="client_id" name="client_id" autocomplete="username" required
               placeholder="e.g. V102289"
               value="<?= e($_POST['client_id'] ?? '') ?>">

        <label for="password">Password</label>
        <div class="password-field">
          <input type="password" id="password" name="password" autocomplete="current-password" required
                 placeholder="••••••••••">
          <button type="button" class="password-toggle" aria-label="Show password" data-target="password">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>

        <button type="submit" class="btn-auth-submit">
          Sign In <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div class="auth-links-row">
        <a href="/client/forgot-password.php">Forgot Password?</a>
        <a href="/contact.php">Contact Support</a>
      </div>

      <div class="auth-security-note">
        <i class="fa-solid fa-lock"></i> Protected by industry-standard encryption
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Password visibility toggle — presentation only, does not touch form logic/values.
  document.querySelectorAll('.password-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.target);
      var icon = btn.querySelector('i');
      var isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      icon.classList.toggle('fa-eye', !isHidden);
      icon.classList.toggle('fa-eye-slash', isHidden);
      btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
  });
</script>
</body>
</html>

