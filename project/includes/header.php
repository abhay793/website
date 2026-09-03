<?php


/**
 * header.php
 * Shared sticky navbar. Include after opening <body>.
 * Expects $pageTitle to optionally be set by the calling page.
 */
require_once __DIR__ . '/functions.php';
$current = currentPage();
/** Helper to print "active" class when on a given page. */
function navActive(string $page, string $current): string
{
    return $page === $current ? ' active' : '';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= e(APP_NAME) ?></title>
<meta name="description" content="OCKTOVA — enterprise software consulting and AI-driven client solutions.">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<!-- Site styles -->
<link rel="stylesheet" href="/assets/css/style.css?v=1">
<?= $pageHeadExtra ?? '' ?>
</head>
<body class="<?= $current === 'index.php' ? 'has-floating-hero' : 'has-floating-header' ?>">

<header class="site-header" id="siteHeader">
  <nav class="navbar navbar-expand-lg container-custom" aria-label="Primary navigation">
    <a class="brand navbar-brand" href="/index.php">
  <img src="/assets/images/logo.jpg" alt="<?= e(APP_NAME) ?> logo" class="brand-mark">
  <span class="brand-name"><?= e(APP_NAME) ?></span>
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation menu">
      <i class="fa-solid fa-bars" aria-hidden="true"></i>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="mainNav">
      <ul class="navbar-nav align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('index.php', $current) ?>" href="/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('about.php', $current) ?>" href="/about.php">About Us</a></li>

        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('service.php', $current) ?>" href="/client/service.php">Services</a></li>
        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('products.php', $current) ?>" href="/client/products.php">Products</a></li>

        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('blogs.php', $current) ?>" href="/blogs.php">Blogs</a></li>
         <a class="nav-link nav-link-custom<?= navActive('candidate.php', $current) ?>" href="/client/candidate.php">Join Our Team</a></li> 
         
        <li class="nav-item"><a class="nav-link nav-link-custom<?= navActive('contact.php', $current) ?>" href="/contact.php">Contact Us</a></li>

      </ul>
      <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
        <!-- <a href="/client/login.php" class="btn-portal nav-cta">Client Portal</a> -->
      </div>
    </div>
  </nav>
</header>