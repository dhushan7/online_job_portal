<?php
ob_start(); // Start output buffering safely before layout execution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php');

// Handle status update if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = intval($_POST['application_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $query = "UPDATE applications SET status = ? WHERE application_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'si', $status, $application_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: admin_applications.php?msg=Application status updated successfully");
        exit();
    } else {
        header("Location: admin_applications.php?msg=Error updating application status");
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Handle search query
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Fetch all applications from the database
$query = "SELECT a.application_id, a.cover_letter, a.applied_at, a.status, 
                 u.name AS user_name, j.job_title, c.company_name, a.resume_file 
          FROM applications a 
          JOIN users u ON a.user_id = u.id 
          JOIN jobs j ON a.job_id = j.job_id 
          JOIN companies c ON a.company_id = c.company_id
          WHERE j.job_title LIKE ?";
$stmt = mysqli_prepare($con, $query);
$search_param = '%' . $search_term . '%';

// FIXED: Corrected function from mysqli_prepare_bind_param to mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Job Applications - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    
    <style>
        /* Theme Alignment Baseline styling */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        .page-wrapper {
            display: flex;
            min-height: 100vh;
            box-sizing: border-box;
            margin-left: 250px;
        }

        main {
            flex: 1;
            padding: 40px 20px;
        }

        .container-fluid {
            width: 100%;
            max-width: 1400px;
            margin: auto;
            box-sizing: border-box;
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .status-msg {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            font-size: 0.95em;
        }

        .search-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 35px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .input-group {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            transition: color 0.3s;
            z-index: 5;
        }

        .search-input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 15px;
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        /* Red accent focus loop matching design architecture */
        .search-input:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            outline: none;
        }

        .search-input:focus + i {
            color: #e74c3c;
        }

        .search-button {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            color: white;
            border: none;
            padding: 0 30px;
            height: 49px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s, transform 0.2s;
        }

        .search-button:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

        .table-responsive-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table {
            background-color: #fff;
            margin-bottom: 0;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        th {
            background-color: #2c3e50 !important;
            color: white !important;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            text-align: center;
            padding: 14px !important;
            border-bottom: none;
        }

        td {
            text-align: center;
            padding: 12px !important;
            color: #495057;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .cover-letter {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }

        .btn-download {
            background: #e74c3c;
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            border: none;
            padding: 8px 14px;
            font-size: 0.85rem;
            transition: opacity 0.3s, transform 0.2s;
        }

        .btn-download:hover {
            background-color: #c82333;
            color: white;
            transform: scale(1.02);
        }

        .form-select-custom {
            font-size: 0.9rem;
            padding: 6px 30px 6px 12px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-select-custom:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            outline: none;
        }

        @media (max-width: 992px) {
            .page-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include("sidebar.php"); ?>

        <main>
            <div class="container-fluid">
                
                <h2>Job Application Ledger</h2>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-info status-msg mb-4"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                <?php endif; ?>

                <div class="search-wrapper">
                    <form method="GET" action="" class="search-form">
                        <div class="input-group">
                            <input class="search-input" type="text" name="search" placeholder="Filter by target job listing title..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <button type="submit" class="search-button">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </form>
                </div>

                <div class="table-responsive-wrapper">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant Name</th>
                                    <th>Job Title</th>
                                    <th>Target Corporation</th>
                                    <th>Cover Letter</th>
                                    <th>Submission Date</th>
                                    <th>Processing Status</th>
                                    <th>Documentation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($row['application_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['company_name']); ?></span></td>
                                        <td class="cover-letter" title="<?php echo htmlspecialchars($row['cover_letter']); ?>">
                                            <?php echo htmlspecialchars($row['cover_letter']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['applied_at']))); ?></td>
                                        <td>
                                            <form action="" method="POST" style="margin: 0;">
                                                <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($row['application_id']); ?>">
                                                <select name="status" class="form-select form-select-sm form-select-custom text-capitalize" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="reviewed" <?php echo ($row['status'] == 'reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                                                    <option value="accepted" <?php echo ($row['status'] == 'accepted') ? 'selected' : ''; ?>>Accepted</option>
                                                    <option value="rejected" <?php echo ($row['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['resume_file'])) : ?>
                                                <a href="<?php echo htmlspecialchars($row['resume_file']); ?>" class="btn btn-download btn-sm rounded" download>
                                                    <i class="fas fa-file-download"></i> Download
                                                </a>
                                            <?php else : ?>
                                                <small class="text-muted">No Attachment</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block text-black-50"></i>
                                            No evaluation pipeline applications match current filters.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
        
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
ob_end_flush();
?>