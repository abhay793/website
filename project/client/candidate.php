<?php

// =====================================================
// SHARED CONFIGURATION
// =====================================================

require_once __DIR__ . '/../includes/config.php';


// =====================================================
// DATABASE CONNECTION
// =====================================================

$conn = getDbConnection();


// =====================================================
// FORM SUBMISSION
// =====================================================

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values
    $name = trim($_POST["name"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // User enters the complete LinkedIn and GitHub URLs
    $linkedin = trim($_POST["linkedin"] ?? "");
    $github = trim($_POST["github"] ?? "");

    $portfolio = trim($_POST["portfolio"] ?? "");
    $cover_letter = trim($_POST["cover_letter"] ?? "");
    $role = trim($_POST["role"] ?? "");


    // =================================================
    // REQUIRED FIELD VALIDATION
    // =================================================

    if (
        empty($name) ||
        empty($address) ||
        empty($phone) ||
        empty($email) ||
        empty($linkedin) ||
        empty($github) ||
        empty($role)
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (
        !isset($_FILES["resume"]) ||
        $_FILES["resume"]["error"] !== UPLOAD_ERR_OK
    ) {

        $message = "Please upload your resume.";
        $message_type = "error";

    } else {

        // =============================================
        // RESUME VALIDATION
        // =============================================

        $resume_name = $_FILES["resume"]["name"];
        $resume_tmp = $_FILES["resume"]["tmp_name"];
        $resume_size = $_FILES["resume"]["size"];

        $extension = strtolower(
            pathinfo($resume_name, PATHINFO_EXTENSION)
        );


        // Only PDF
        if ($extension !== "pdf") {

            $message = "Only PDF files are allowed.";
            $message_type = "error";

        // Maximum 5 MB
        } elseif ($resume_size > 5 * 1024 * 1024) {

            $message = "Resume must be smaller than 5 MB.";
            $message_type = "error";

        } else {

            // =========================================
            // CREATE UPLOAD DIRECTORY
            // =========================================

            $upload_directory =
                __DIR__ . "/uploads/resumes/";

            if (!is_dir($upload_directory)) {

                mkdir(
                    $upload_directory,
                    0755,
                    true
                );
            }


            // =========================================
            // CREATE UNIQUE FILE NAME
            // =========================================

            $new_resume_name =
                uniqid("resume_", true) . ".pdf";

            $resume_destination =
                $upload_directory . $new_resume_name;


            // =========================================
            // MOVE RESUME
            // =========================================

            if (
                move_uploaded_file(
                    $resume_tmp,
                    $resume_destination
                )
            ) {

                $resume_path =
                    "uploads/resumes/" . $new_resume_name;


                // =====================================
                // INSERT DATA USING PDO
                // =====================================

                $sql = "
                    INSERT INTO candidates
                    (
                        name,
                        address,
                        phone,
                        email,
                        linkedin,
                        github,
                        portfolio,
                        cover_letter,
                        resume_path,
                        role
                    )
                    VALUES
                    (
                        :name,
                        :address,
                        :phone,
                        :email,
                        :linkedin,
                        :github,
                        :portfolio,
                        :cover_letter,
                        :resume_path,
                        :role
                    )
                ";


                try {

                    $stmt = $conn->prepare($sql);

                    $stmt->execute([
                        ":name" => $name,
                        ":address" => $address,
                        ":phone" => $phone,
                        ":email" => $email,
                        ":linkedin" => $linkedin,
                        ":github" => $github,
                        ":portfolio" => $portfolio,
                        ":cover_letter" => $cover_letter,
                        ":resume_path" => $resume_path,
                        ":role" => $role
                    ]);


                    $message =
                        "Application submitted successfully!";

                    $message_type = "success";

                } catch (PDOException $e) {

                    $message =
                        "Database error. Please try again.";

                    $message_type = "error";
                }

            } else {

                $message =
                    "Failed to upload resume.";

                $message_type = "error";
            }
        }
    }
}


// =====================================================
// SHARED HEADER
// =====================================================

require_once __DIR__ . '/../includes/header.php';

?>


<!-- =====================================================
     CANDIDATE INFORMATION
     ===================================================== -->

<section class="section section-tight">

    <div class="container-custom">

        <div class="mb-4">

            <span class="eyebrow">
                CANDIDATE
            </span>

            <h1>
                Candidate Information
            </h1>

            <p>
                Please provide your information below.
            </p>

        </div>


        <!-- =================================================
             FORM CARD
             ================================================= -->

        <div class="card-custom">


            <!-- =================================================
                 STATUS MESSAGE
                 ================================================= -->

            <?php if (!empty($message)): ?>

                <div class="form-status <?= htmlspecialchars($message_type) ?>">

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORM
                 ================================================= -->

            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- =================================================
                     NAME + PHONE
                     ================================================= -->

                <div class="candidate-entry-row">


                    <!-- NAME -->

                    <div class="form-group">

                        <label
                            for="name"
                            class="form-label-custom"
                        >
                            Name *
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control-custom"
                            required
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="form-group">

                        <label
                            for="phone"
                            class="form-label-custom"
                        >
                            Phone Number *
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control-custom"
                            required
                        >

                    </div>


                </div>


                <!-- =================================================
                     ADDRESS + EMAIL
                     ================================================= -->

                <div class="candidate-entry-row">


                    <!-- ADDRESS -->

                    <div class="form-group">

                        <label
                            for="address"
                            class="form-label-custom"
                        >
                            Address *
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            class="form-control-custom"
                            rows="2"
                            required
                        ></textarea>

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label-custom"
                        >
                            Email ID *
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control-custom"
                            required
                        >

                    </div>


                </div>


                <!-- =================================================
                     LINKEDIN + GITHUB
                     ================================================= -->

                <div class="candidate-entry-row">


                    <!-- LINKEDIN -->

                    <div class="form-group">

                        <label
                            for="linkedin"
                            class="form-label-custom"
                        >
                            LinkedIn *
                        </label>

                        <input
                            type="url"
                            id="linkedin"
                            name="linkedin"
                            class="form-control-custom"
                            required
                        >

                    </div>


                    <!-- GITHUB -->

                    <div class="form-group">

                        <label
                            for="github"
                            class="form-label-custom"
                        >
                            GitHub *
                        </label>

                        <input
                            type="url"
                            id="github"
                            name="github"
                            class="form-control-custom"
                            required
                        >

                    </div>


                </div>


                <!-- =================================================
                     PORTFOLIO
                     ================================================= -->

                <div class="form-group">

                    <label
                        for="portfolio"
                        class="form-label-custom"
                    >
                        Portfolio
                    </label>

                    <small class="form-hint">
                        Optional
                    </small>

                    <input
                        type="url"
                        id="portfolio"
                        name="portfolio"
                        class="form-control-custom"
                        
                    >

                </div>


                <!-- =================================================
                     COVER LETTER
                     ================================================= -->

                <div class="form-group">

                    <label
                        for="cover_letter"
                        class="form-label-custom"
                    >
                        Cover Letter
                    </label>

                    <small class="form-hint">
                        Optional
                    </small>

                    <textarea
                        id="cover_letter"
                        name="cover_letter"
                        class="form-control-custom"
                        rows="8"
                        placeholder="Write your cover letter here..."
                    ></textarea>

                </div>


                <!-- =================================================
                     ROLE
                     ================================================= -->

                <div class="form-group">

                    <label
                        for="role"
                        class="form-label-custom"
                    >
                        Role Applying For *
                    </label>

                    <input
                        type="text"
                        id="role"
                        name="role"
                        class="form-control-custom"
                        placeholder="Example: Data Analyst"
                        required
                    >

                </div>


                <!-- =================================================
                     RESUME
                     ================================================= -->

                <div class="form-group">

                    <label
                        for="resume"
                        class="form-label-custom"
                    >
                        Resume *
                    </label>

                    <input
                        type="file"
                        id="resume"
                        name="resume"
                        class="form-control-custom"
                        accept=".pdf,application/pdf"
                        required
                    >

                    <small class="form-hint">
                        PDF only. Maximum size: 5 MB.
                    </small>

                </div>


                <!-- =================================================
                     SUBMIT
                     ================================================= -->

                <div class="candidate-submit">

                    <button
                        type="submit"
                        class="btn btn-brass"
                    >
                        Submit
                    </button>

                </div>


            </form>

        </div>

    </div>

</section>


<?php

// =====================================================
// SHARED FOOTER
// =====================================================

require_once __DIR__ . '/../includes/footer.php';

?>