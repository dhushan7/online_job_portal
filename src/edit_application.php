<?php
// Start output buffering
ob_start();

// Include database connection
include('db.php');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Check if application_id is provided
if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    // Fetch the current application details
    $query = "SELECT cover_letter, resume_file FROM applications WHERE application_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $application_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $application = mysqli_fetch_assoc($result);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cover_letter = $_POST['cover_letter'];
        $resume_file = $_FILES['resume_file'];

        // Prepare file upload
        $upload_dir = 'uploads/'; // Ensure this directory exists and is writable
        $uploaded_file = $application['resume_file']; // Default to current file
        if ($resume_file['size'] > 0) { // Check if a new file is uploaded
            $uploaded_file = $upload_dir . basename($resume_file['name']);
            move_uploaded_file($resume_file['tmp_name'], $uploaded_file);
        }

        // Update the application in the database
        $update_query = "UPDATE applications SET cover_letter = ?, resume_file = ? WHERE application_id = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, 'ssi', $cover_letter, $uploaded_file, $application_id);
        $execute_status = mysqli_stmt_execute($update_stmt);

        // Redirect after successful update
        header("Location: applications.php?msg=Application updated successfully");
        exit();
    }
} else {
    // Redirect if application_id is not set
    header("Location: applications.php?msg=Application not found");
    exit();
}

// REMOVED THE NAVBAR INCLUDE FROM THE TOP BLOCK - IT'S BEEN MOVED INSIDE THE <body> TAG BELOW
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Base Reset & Height Handlers */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        /* 1. LAYOUT BOX FORCES A MINIMUM OF 100% VH BASELINE */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* 2. MAIN AREA STRETCHES TO FILL ANY EMPTY SCREEN SPACE */
        main {
            flex: 1 0 auto;
            padding: 50px 20px;
            display: flex;
            align-items: center; /* Vertically centers the edit form if screen height permits */
            justify-content: center;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 700px;
            margin: auto;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #004085;
            border-color: #004085;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <?php include('navbar.php'); ?>

        <main>
            <div class="container">
                <h1 class="text-center mb-4">Edit Your Application</h1>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="cover_letter" class="form-label">Cover Letter</label>
                        <textarea id="cover_letter" name="cover_letter" class="form-control" rows="6" required><?php echo htmlspecialchars($application['cover_letter']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="resume_file" class="form-label">Upload New Resume (if any)</label>
                        <input type="file" id="resume_file" name="resume_file" class="form-control" accept=".pdf,.doc,.docx">
                        <div class="form-text text-muted mt-2">
                            Current file: <strong class="text-dark"><?php echo htmlspecialchars(basename($application['resume_file'])); ?></strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-2">
                        <button type="submit" class="btn btn-primary px-4">Update Application</button>
                        <a href="applications.php" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </main>

    </div> <?php include('footer.php'); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close database connection
mysqli_close($con);

// End output buffering and flush headers safely
ob_end_flush();
?>