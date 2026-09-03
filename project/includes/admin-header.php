<?php
/**
 * admin/header.php
 * Shared sticky navbar for Admin pages.
 *
 * Expects $pageTitle to optionally be set by the calling page.
 */

require_once __DIR__ . '/functions.php';

$current = currentPage();

/**
 * Helper to print "active" class when on a given page.
 */
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

    <title>
        <?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>
        <?= e(APP_NAME) ?>
    </title>

    <meta name="description"
          content="Meridian Systems — Admin Portal.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
          rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Site styles -->
    <link rel="stylesheet"
          href="/assets/css/style.css?v=1">
</head>

<body>

<header class="site-header" id="siteHeader">

    <nav class="navbar navbar-expand-lg container-custom"
         aria-label="Admin navigation">

        <!-- Brand -->
        <a class="brand navbar-brand" href="/index.php">

            <span class="brand-mark" aria-hidden="true">O</span>

            <span class="brand-name">
                <?= e(APP_NAME) ?>
            </span>

        </a>


        <!-- Mobile Menu Button -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-expanded="false"
                aria-label="Toggle navigation menu">

            <i class="fa-solid fa-bars" aria-hidden="true"></i>

        </button>


        <!-- Navigation -->
        <div class="collapse navbar-collapse justify-content-end"
             id="mainNav">

            <ul class="navbar-nav align-items-lg-center gap-lg-1">


                <!-- BUTTON 1 -->
                <li class="nav-item">

                    <a class="nav-link nav-link-custom<?= navActive('admin.php', $current) ?>"
                       href="/admin/admin.php">

                        Admin Pannel

                    </a>

                </li>


                <!-- BUTTON 2 -->
                <li class="nav-item">

                    <a class="nav-link nav-link-custom<?= navActive('feedback.php', $current) ?>"
                       href="/admin/feedback.php">

                        Feedback

                    </a>

                </li>


                <!-- BUTTON 3 -->
                <li class="nav-item">

                    <a class="nav-link nav-link-custom<?= navActive('candidate_information.php', $current) ?>"
                       href="/admin/candidate_information.php">

                        Career

                    </a>

                </li>


                <!-- BUTTON 4 -->
                <li class="nav-item">

                    <a class="nav-link nav-link-custom<?= navActive('blogs_entry.php', $current) ?>"
                       href="/admin/blogs_entry.php">

                        Blogs
                    </a>

                </li>




                 <!-- BUTTON 4 -->
                <li class="nav-item">

                    <a class="nav-link nav-link-custom<?= navActive('leads.php', $current) ?>"
                       href="/admin/leads.php">

                        Leads
                    </a>

                </li>


            </ul>

        </div>

    </nav>

</header>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>