<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us';

$formStatus = null; // 'success' | 'error'
$formMessage = '';
$errors = [];

$old = [
    'name' => '',
    'email' => '',
    'company' => '',
    'phone' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- CSRF check ----
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {

        $formStatus = 'error';
        $formMessage = 'Your session expired. Please refresh the page and try again.';

    } else {

        // ---- Sanitize input ----
        $old['name'] = cleanInput($_POST['name'] ?? '');
        $old['email'] = cleanInput($_POST['email'] ?? '');
        $old['company'] = cleanInput($_POST['company'] ?? '');
        $old['phone'] = cleanInput($_POST['phone'] ?? '');
        $old['message'] = cleanInput($_POST['message'] ?? '');

        // ---- Server-side validation ----
        if ($old['name'] === '' || mb_strlen($old['name']) > 120) {
            $errors[] = 'Please enter a valid name.';
        }

        if ($old['email'] === '' || !isValidEmail($old['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (mb_strlen($old['company']) > 150) {
            $errors[] = 'Company name is too long.';
        }

        if (
            $old['phone'] !== '' &&
            !preg_match('/^[0-9+\-\s()]{6,20}$/', $old['phone'])
        ) {
            $errors[] = 'Please enter a valid phone number.';
        }

        if ($old['message'] === '' || mb_strlen($old['message']) > 4000) {
            $errors[] = 'Please enter a message (up to 4000 characters).';
        }

        // ---- Insert into database ----
        if (empty($errors)) {

            try {

                $pdo = getDbConnection();

                $stmt = $pdo->prepare(
                    'INSERT INTO contact_messages
                    (name, email, phone, company, message, created_at)
                    VALUES (:name, :email, :phone, :company, :message, NOW())'
                );

                $stmt->execute([
                    ':name' => $old['name'],
                    ':email' => $old['email'],
                    ':phone' => $old['phone'],
                    ':company' => $old['company'],
                    ':message' => $old['message'],
                ]);

                $formStatus = 'success';

                $formMessage =
                    'Thank you — your message has been received. We will be in touch shortly.';

                $old = [
                    'name' => '',
                    'email' => '',
                    'company' => '',
                    'phone' => '',
                    'message' => ''
                ];

                // Regenerate CSRF token after successful submission.
                unset($_SESSION['csrf_token']);

            } catch (PDOException $e) {

                logError(
                    'Contact form insert failed: ' . $e->getMessage()
                );

                $formStatus = 'error';

                $formMessage =
                    'Something went wrong while sending your message. Please try again later.';
            }

        } else {

            $formStatus = 'error';
            $formMessage = implode(' ', $errors);
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:4rem;">
    <div class="container-custom">

        <span class="eyebrow">Contact Us</span>

        <h1 class="mt-3 mb-3" style="max-width:640px;">
            Tell us about your project.
        </h1>

        <p class="text-secondary" style="max-width:640px;">
            Reach out directly or use the form below — every inquiry is reviewed by a
            member of our engineering team, not a sales queue.
        </p>

    </div>
</section>


<section class="section section-tight">

    <div class="container-custom">

        <div class="row g-5">

            <!-- Contact Information -->
            <div class="col-lg-4">

                <div class="contact-info-item" style="padding-top:0;">

                    <div class="icon-badge">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>Address</strong>

                        <p class="text-secondary mb-0">
                            Mumbai, Maharashtra, India
                        </p>
                    </div>

                </div>


                <div class="contact-info-item">

                    <div class="icon-badge">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>Phone</strong>

                        <p class="text-secondary mb-0">
                            +91 73030 36260
                        </p>
                    </div>

                </div>


                <div class="contact-info-item" style="border-bottom:none;">

                    <div class="icon-badge">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>Email</strong>

                        <p class="text-secondary mb-0">
                            hello@ocktova.com
                        </p>
                    </div>

                </div>

            </div>


            <!-- Contact Form -->
            <div class="col-lg-8">

                <div class="card-custom">

                    <?php if ($formStatus === 'success'): ?>

                        <div class="form-status success" role="status">
                            <?= e($formMessage) ?>
                        </div>

                    <?php elseif ($formStatus === 'error'): ?>

                        <div class="form-status error" role="alert">
                            <?= e($formMessage) ?>
                        </div>

                    <?php endif; ?>


                    <form
                        action="/contact.php"
                        method="POST"
                        novalidate
                        id="contactForm"
                    >

                        <?= csrfField() ?>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    class="form-label-custom"
                                    for="name"
                                >
                                    Name *
                                </label>

                                <input
                                    class="form-control form-control-custom"
                                    type="text"
                                    id="name"
                                    name="name"
                                    maxlength="120"
                                    required
                                    value="<?= e($old['name']) ?>"
                                >

                            </div>


                            <div class="col-md-6">

                                <label
                                    class="form-label-custom"
                                    for="email"
                                >
                                    Email *
                                </label>

                                <input
                                    class="form-control form-control-custom"
                                    type="email"
                                    id="email"
                                    name="email"
                                    maxlength="150"
                                    required
                                    value="<?= e($old['email']) ?>"
                                >

                            </div>


                            <div class="col-md-6">

                                <label
                                    class="form-label-custom"
                                    for="company"
                                >
                                    Company
                                </label>

                                <input
                                    class="form-control form-control-custom"
                                    type="text"
                                    id="company"
                                    name="company"
                                    maxlength="150"
                                    value="<?= e($old['company']) ?>"
                                >

                            </div>


                            <div class="col-md-6">

                                <label
                                    class="form-label-custom"
                                    for="phone"
                                >
                                    Phone
                                </label>

                                <input
                                    class="form-control form-control-custom"
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    maxlength="20"
                                    value="<?= e($old['phone']) ?>"
                                >

                            </div>


                            <div class="col-12">

                                <label
                                    class="form-label-custom"
                                    for="message"
                                >
                                    Message *
                                </label>

                                <textarea
                                    class="form-control form-control-custom"
                                    id="message"
                                    name="message"
                                    rows="5"
                                    maxlength="4000"
                                    required
                                ><?= e($old['message']) ?></textarea>

                            </div>


                            <div class="col-12">

                                <button
                                    type="submit"
                                    class="btn btn-brass px-4"
                                >
                                    Send Message
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


<script src="/assets/js/contact-form.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>


<!-- WhatsApp Toast -->
<div
    class="whatsapp-toast"
    id="whatsappToast"
    role="status"
    aria-live="polite"
>

    <button
        class="toast-close"
        id="toastClose"
        aria-label="Close notification"
        type="button"
    >
        &times;
    </button>


    <div class="toast-icon">

        <i
            class="fa-brands fa-whatsapp"
            aria-hidden="true"
        ></i>

    </div>


    <div class="toast-body">

        <strong>Need a fast reply?</strong>

        <p>
            Chat with us on WhatsApp for quick answers.
        </p>

        <a
            href="https://wa.me/917304036260"
            target="_blank"
            rel="noopener noreferrer"
            class="toast-link"
        >
            Start Chat

            <i
                class="fa-solid fa-arrow-right"
                aria-hidden="true"
            ></i>

        </a>

    </div>

</div>


<script>
(function () {

    const toast = document.getElementById('whatsappToast');
    const closeBtn = document.getElementById('toastClose');

    if (!toast) return;

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    const hide = () => {
        toast.classList.remove('show');
    };

    const timer = setTimeout(hide, 5000);

    closeBtn.addEventListener('click', () => {

        clearTimeout(timer);
        hide();

    });

})();
</script>