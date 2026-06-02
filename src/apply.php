<?php 
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php'); 

// Check if the connection is successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}

$email = $_SESSION['email'];

// Fetch user information
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($con->error));
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Initialize feedback messages
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $user['id']; 
    $company_id = intval($_POST['company_id']);
    $job_id = intval($_POST['job_id']);
    $cover_letter = $_POST['cover_letter'];
    $status = 'pending'; 

    // Handle file upload
    $target_dir = "uploads/"; 
    
    // Ensure the folder space physically exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . uniqid() . '_' . basename($_FILES["resume_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size (500KB cap check)
    if ($_FILES["resume_file"]["size"] > 500000) {
        $message = "Sorry, your file size exceeds the 500KB limit.";
        $message_type = "danger";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        $message = "Sorry, only PDF, DOC & DOCX files are allowed.";
        $message_type = "danger";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
            // SECURE FIX: Using prepared statements instead of directly mapping raw inputs to raw strings
            $query = "INSERT INTO applications (user_id, job_id, company_id, cover_letter, resume_file, status) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $con->prepare($query);
            
            if ($insertStmt) {
                $insertStmt->bind_param("iiiiss", $user_id, $job_id, $company_id, $cover_letter, $target_file, $status);
                if ($insertStmt->execute()) {
                    $message = "Application submitted successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error submitting application: " . htmlspecialchars($insertStmt->error);
                    $message_type = "danger";
                }
                $insertStmt->close();
            } else {
                $message = "Database setting failure: " . htmlspecialchars($con->error);
                $message_type = "danger";
            }
        } else {
            $message = "Sorry, there was an error uploading your resume file to the server.";
            $message_type = "danger";
        }
    }
}

// Fetch company names
$companies = mysqli_query($con, "SELECT company_id, company_name FROM companies");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Base Reset & Height Handlers */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        /* 1. LAYOUT CONTROLLER WRAPPER */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; 
            box-sizing: border-box;
        }

        /* 2. FORCES CONTENT FOOTER TO REMAIN PINNED AT BOTTOM */
        main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 35px;
            max-width: 600px;
            width: 100%;
            margin: auto;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .container:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.95em;
            display: block;
            margin-bottom: 8px;
        }

        label i {
            margin-right: 8px;
            color: #e74c3c; /* Core system theme red accent */
            width: 16px;
            text-align: center;
        }

        select, textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
            font-size: 15px;
            background-color: #fff;
            font-family: 'Open Sans', sans-serif;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        select:focus, textarea:focus, input[type="file"]:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
            outline: none;
        }

        input[type="file"] {
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            cursor: pointer;
        }

        small {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
        }

        .submit-btn {
            background: linear-gradient(135deg, #2c3e50, #0056b3); /* Navy Gradient */
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: bold;
            transition: opacity 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .submit-btn:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

        /* Banner Status Rules */
        .status-msg {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            font-size: 0.95em;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        textarea {
            resize: vertical;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('company').addEventListener('change', function () {
                var company_id = this.value;
                if (company_id) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'fetch_jobs.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (this.status === 200) {
                            document.getElementById('job').innerHTML = this.responseText;
                        }
                    };
                    xhr.send('company_id=' + company_id);
                } else {
                    document.getElementById('job').innerHTML = '<option value="">Select a job</option>';
                }
            });
        });
    </script>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include('navbar.php'); ?>

        <main>
            <div class="container">
                <h2><i class="fas fa-paper-plane" style="color: #e74c3c; margin-right: 10px;"></i>Job Application Form</h2>
                
                <?php if ($message && $message_type === 'success'): ?>
                    <div class="status-msg success-message"><i class="fas fa-check-circle me-2"></i><?php echo $message; ?></div>
                <?php elseif ($message && $message_type === 'danger'): ?>
                    <div class="status-msg error-message"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="company"><i class="fas fa-building"></i>Select Company:</label>
                        <select id="company" name="company_id" required>
                            <option value="">Select a company</option>
                            <?php while ($row = mysqli_fetch_assoc($companies)) {
                                echo "<option value='{$row['company_id']}'>{$row['company_name']}</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="job"><i class="fas fa-briefcase"></i>Select Job:</label>
                        <select id="job" name="job_id" required>
                            <option value="">Select a job</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cover_letter"><i class="fas fa-file-alt"></i>Cover Letter:</label>
                        <textarea id="cover_letter" name="cover_letter" rows="5" placeholder="Explain why you are an ideal fit for this opening..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="resume"><i class="fas fa-file-upload"></i>Upload Resume:</label>
                        <input type="file" id="resume" name="resume_file" required>
                        <small>Allowed formats: PDF, DOC, DOCX (max size: 500KB)</small>
                    </div>

                    <button class="submit-btn" type="submit">Submit Application</button>
                </form>
            </div>
        </main>
        
        <?php include('footer.php'); ?>

    </div>

</body>
</html>
<?php 
// Close connection
if (isset($con)) {
    mysqli_close($con); 
}
?>