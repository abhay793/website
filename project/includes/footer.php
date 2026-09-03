<footer class="site-footer">
  <div class="container-custom">
    <div class="row g-4">

      <!-- Brand -->
      <div class="col-lg-4">
        <div class="brand mb-3">
          <img src="/assets/images/logo.jpg" alt="<?= e(APP_NAME) ?> logo" class="brand-mark">
          <span class="brand-name">
            <?= e(APP_NAME) ?>
          </span>
        </div>

        <p class="mb-0" style="max-width:320px;">
          Enterprise software, secure client portals, and applied AI—built for reliability.
        </p>

        <div class="footer-social">
          <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        </div>
      </div>

      <!-- Company -->
      <div class="col-6 col-lg-2 footer-col">
        <button class="footer-toggle" data-bs-toggle="collapse" data-bs-target="#footerCompany" aria-expanded="false">
          Company
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </button>
        <ul class="list-unstyled gap-2 collapse footer-collapse" id="footerCompany">
          <li><a href="/index.php">Home</a></li>
          <li><a href="/about.php">About Us</a></li>
          <li><a href="/blogs.php">Blogs</a></li>
          <li><a href="/contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Client + Employee -->
      <div class="col-6 col-lg-2 footer-col">
        <!-- Client -->
        <button class="footer-toggle" data-bs-toggle="collapse" data-bs-target="#footerClient" aria-expanded="false">
          Client
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </button>
        <ul class="list-unstyled gap-2 mb-4 collapse footer-collapse" id="footerClient">
          <li><a href="/client/login.php">Client Portal</a></li>
          <li><a href="/client/login.php">Dashboard Login</a></li>
        </ul>

        <!-- Employee -->
        <button class="footer-toggle" data-bs-toggle="collapse" data-bs-target="#footerEmployee" aria-expanded="false">
          Employee
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </button>
        <ul class="list-unstyled gap-2 collapse footer-collapse" id="footerEmployee">
          <li><a href="/admin/admin.php">Employee Portal</a></li>
        </ul>
      </div>

    </div><!-- /.row -->
  </div>
</footer>

<!-- Footer Bottom: separate strip below the card, not inside it -->
<div class="footer-bottom-wrap">
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>

    <div class="footer-legal-links">
      <a href="/privacy-policy.php">Privacy Policy</a>
      <a href="/terms-of-service.php">Terms of Service</a>
    </div>

    <span>Built with care for reliability.</span>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>

</body>
</html>