<?php
// Include database connection
include('db.php');

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and get the email
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_email = $_SESSION['email'];

// Fetch user ID based on the email
$query = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $user_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $user_id = $user['id']; // Get the user ID from the fetched result
} else {
    header("Location: login.php"); // Redirect if user not found
    exit();
}

// Handle deletion if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the application ID from the POST request
    $application_id = intval($_POST['application_id']);

    // Prepare and execute the delete query
    $query = "DELETE FROM applications WHERE application_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $application_id, $user_id);
    mysqli_stmt_execute($stmt);

    // Check if the application was deleted
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: applications.php?msg=Application deleted successfully");
        exit();
    } else {
        header("Location: applications.php?msg=Error deleting application");
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Fetch applications for the logged-in user
$query = "SELECT a.application_id, a.cover_letter, a.applied_at, a.status, 
                  u.name AS user_name, j.job_title, c.company_name, a.resume_file 
          FROM applications a 
          JOIN users u ON a.user_id = u.id 
          JOIN jobs j ON a.job_id = j.job_id 
          JOIN companies c ON a.company_id = c.company_id
          WHERE a.user_id = ?
          ORDER BY a.applied_at DESC"; // Ordered by latest submissions
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    
    <style>
        /* Base System Defaults */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        /* 1. STRUCTURAL MASTER PAGE LAYOUT CONTROL */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; 
            box-sizing: border-box;
        }

        /* 2. FLEX AREA STRETCH FOR FORCED BOTTOM STICKY FOOTER */
        main {
            flex: 1;
            padding: 40px 0;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-weight: 700;
        }

        /* Styled Portal Dashboard Application Cards */
        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%; /* Keeps grid items matching row dimensions cleanly */
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 1.25em;
            font-weight: bold;
        }

        .card-subtitle {
            font-family: 'Lato', sans-serif;
            font-size: 1em;
        }

        /* Custom Branding Status Flag Badges */
        .status-accepted { color: #28a745; font-weight: bold; background: #e2f0d9; padding: 2px 8px; border-radius: 4px; }
        .status-rejected { color: #dc3545; font-weight: bold; background: #f8d7da; padding: 2px 8px; border-radius: 4px; }
        .status-pending { color: #e67e22; font-weight: bold; background: #fff3cd; padding: 2px 8px; border-radius: 4px; }
        
        .cover-letter {
            display: block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            background-color: #f8f9fa;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #0056b3;
            font-size: 0.95em;
        }

        .apply-button-wrapper {
            margin-bottom: 35px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-warning {
            background-color: #f39c12;
            border-color: #f39c12;
            color: #fff;
        }
        
        .btn-warning:hover {
            background-color: #d35400;
            border-color: #d35400;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <?php include('navBar.php'); ?>

        <main>
            <div class="container">
                <h1 class="text-center mb-4">My Job Applications</h1>

                <?php if (isset($_GET['msg'])) : ?>
                    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($_GET['msg']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="text-center apply-button-wrapper">
                    <a href="apply.php" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> Apply for a New Job
                    </a>
                </div>

                <div class="row g-4"> <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['job_title']); ?></h5>
                                        <h6 class="card-subtitle mb-3 text-muted"><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($row['company_name']); ?></h6>
                                        
                                        <div class="mb-3">
                                            <p class="card-text mb-2"><strong>Applicant:</strong> <?php echo htmlspecialchars($row['user_name']); ?></p>
                                            <p class="card-text mb-2"><strong>Submission Record:</strong></p>
                                            <span class="cover-letter mb-2" title="<?php echo htmlspecialchars($row['cover_letter']); ?>">
                                                <?php echo htmlspecialchars($row['cover_letter']); ?>
                                            </span>
                                            <p class="card-text mt-2 mb-2"><small class="text-muted"><i class="far fa-calendar-alt me-1"></i><?php echo htmlspecialchars($row['applied_at']); ?></small></p>
                                        </div>

                                        <div class="mt-auto">
                                            <p class="card-text mb-3">
                                                <strong>Status:</strong> 
                                                <?php 
                                                    $status = htmlspecialchars($row['status']);
                                                    if (strcasecmp($status, 'accepted') === 0) {
                                                        echo "<span class='status-accepted'>$status</span>";
                                                    } elseif (strcasecmp($status, 'rejected') === 0) {
                                                        echo "<span class='status-rejected'>$status</span>";
                                                    } else {
                                                        echo "<span class='status-pending'>$status</span>";
                                                    }
                                                ?>
                                            </p>
                                            
                                            <p class="card-text mb-3">
                                                <strong>Attachment:</strong>
                                                <?php if (!empty($row['resume_file'])) : ?>
                                                    <a href="<?php echo htmlspecialchars($row['resume_file']); ?>" class="btn btn-primary btn-sm ms-1" download>
                                                        <i class="fas fa-file-download me-1"></i> Resume
                                                    </a>
                                                <?php else : ?>
                                                    <span class="text-muted italic small">None Uploaded</span>
                                                <?php endif; ?>
                                            </p>

                                            <div class="d-flex justify-content-between pt-2 border-top">
                                                <form action="" method="POST" onsubmit="return confirm('Are you sure you want to retract and delete this application record?');" style="margin:0;">
                                                    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($row['application_id']); ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash-alt"></i> Withdraw
                                                    </button>
                                                </form>
                                                
                                                <?php if (strcasecmp($row['status'], 'accepted') !== 0) : ?>
                                                    <a href="edit_application.php?application_id=<?php echo htmlspecialchars($row['application_id']); ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Modify
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted my-5">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                            <p class="fs-5">You haven't submitted any job applications yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <?php include('footer.php'); ?>

    </div> 

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
mysqli_close($con);
?>