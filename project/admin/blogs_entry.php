<?php
// =============================================================================
// blog_entry.php
// Admin page for creating a new blog post.
// Backend logic preserved.
// =============================================================================

require_once __DIR__ . '/../includes/config.php';

$pdo = getDbConnection();

// Department dropdown options
$departments = [
    'Full Stack',
    'Data Science',
    'Sales',
    'Cyber Security'
];

$errors  = [];
$success = false;

// =============================================================================
// FORM PROCESSING
// =============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title      = trim($_POST['title'] ?? '');
    $blog_date  = trim($_POST['blog_date'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $content    = trim($_POST['content'] ?? '');

    // Validation
    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($blog_date === '') {
        $errors[] = 'Date is required.';
    }

    if (!in_array($department, $departments, true)) {
        $errors[] = 'Please choose a valid department.';
    }

    if ($content === '') {
        $errors[] = 'Content is required.';
    }

    // Save blog
    if (empty($errors)) {

        $stmt = $pdo->prepare(
            'INSERT INTO blogs (title, blog_date, department, content)
             VALUES (:title, :blog_date, :department, :content)'
        );

        $stmt->execute([
            ':title'      => $title,
            ':blog_date'  => $blog_date,
            ':department' => $department,
            ':content'    => $content,
        ]);

        $success = true;

        // Clear form after successful submission
        $title      = '';
        $blog_date  = '';
        $department = '';
        $content    = '';
    }
}

// =============================================================================
// PAGE
// =============================================================================

$pageTitle = 'Blog Admin';

require __DIR__ . '/../includes/admin-header.php';
?>

<div class="blog-admin-page">

    <div class="blog-admin-inner">

        <!-- Page heading -->
        <div class="eyebrow">Admin</div>

        <h2>Blogs Entry</h2>


        <!-- Blog form card -->
        <div class="card-custom">

            <?php if ($success): ?>

                <div class="form-status success">
                    Blog post saved successfully.
                </div>

            <?php endif; ?>


            <?php if (!empty($errors)): ?>

                <div class="form-status error">

                    <?php foreach ($errors as $err): ?>

                        <?= htmlspecialchars($err) ?><br>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <form method="post" action="blog_entry.php">


                <!-- Title -->
                <div class="form-group">

                    <label
                        class="form-label-custom"
                        for="title">
                        Title
                    </label>

                    <input
                        class="form-control-custom"
                        type="text"
                        id="title"
                        name="title"
                        value="<?= htmlspecialchars($title ?? '') ?>"
                        required>

                </div>


                <!-- Date + Department -->
                <div class="blog-entry-row">


                    <!-- Date -->
                    <div class="form-group">

                        <label
                            class="form-label-custom"
                            for="blog_date">
                            Date
                        </label>

                        <input
                            class="form-control-custom"
                            type="date"
                            id="blog_date"
                            name="blog_date"
                            value="<?= htmlspecialchars($blog_date ?? '') ?>"
                            required>

                    </div>


                    <!-- Department -->
                    <div class="form-group">

                        <label
                            class="form-label-custom"
                            for="department">
                            Dept
                        </label>

                        <select
                            class="form-control-custom"
                            id="department"
                            name="department"
                            required>

                            <option
                                value=""
                                disabled
                                <?= empty($department) ? 'selected' : '' ?>>
                                Select department
                            </option>

                            <?php foreach ($departments as $dept): ?>

                                <option
                                    value="<?= htmlspecialchars($dept) ?>"
                                    <?= (($department ?? '') === $dept) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($dept) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>


                <!-- Content -->
                <div class="form-group">

                    <label
                        class="form-label-custom"
                        for="content">
                        Content
                    </label>

                    <textarea
                        class="form-control-custom"
                        id="content"
                        name="content"
                        required><?= htmlspecialchars($content ?? '') ?></textarea>

                </div>


                <!-- Submit -->
                <button
                    type="submit"
                    class="btn btn-brass">
                    Submit
                </button>


            </form>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>