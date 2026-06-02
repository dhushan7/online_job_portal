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

// Explicitly assign type mapping for navbar.php to prevent errors inside the navbar include file
if ($user) {
    $_SESSION['type'] = $user['type']; 
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($user['id']);
    $company_id = intval($_POST['company_id']);
    $job_id = intval($_POST['job_id']);
    $rating = intval($_POST['rating']);
    $comments = $_POST['comments']; 

    // Safe Prepared Statements instead of injecting raw variables into strings
    $insertSql = "INSERT INTO feedbacks (user_id, company_id, job_id, rating, comments, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
                  
    $insertStmt = $con->prepare($insertSql);
    if ($insertStmt) {
        $insertStmt->bind_param("iiiis", $user_id, $company_id, $job_id, $rating, $comments);
        if ($insertStmt->execute()) {
            $message = "Feedback created successfully.";
            $message_type = "success";
        } else {
            $message = "Error creating feedback: " . htmlspecialchars($insertStmt->error);
            $message_type = "danger";
        }
        $insertStmt->close();
    } else {
        $message = "Database error setting up statement: " . htmlspecialchars($con->error);
        $message_type = "danger";
    }
}

// Fetch all companies from the companies table
$companySql = "SELECT company_id, company_name FROM companies";
$companyResult = mysqli_query($con, $companySql);

if (!$companyResult) {
    echo "Error: " . mysqli_error($con);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Feedback - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Base Reset */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        
        main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            align-items: center; 
            justify-content: center;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            width: 100%;
            max-width: 600px;
            margin: auto;
            box-sizing: border-box;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .form-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        form div {
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
            color: #e74c3c; 
            width: 16px;
            text-align: center;
        }

        select, input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 15px;
            background-color: #fff;
            font-family: 'Open Sans', sans-serif;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        select:focus, input:focus, textarea:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
            outline: none;
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

        /* Status Banner Styles */
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
</head>
<body>

    <div class="page-wrapper">

        <?php include 'navbar.php'; ?>

        <main>
            <div class="form-container">
                <h1><i class="fas fa-pencil-alt" style="color: #e74c3c; margin-right: 10px;"></i>Create Feedback</h1>

                <?php if ($message && $message_type === 'success'): ?>
                    <div class="status-msg success-message"><i class="fas fa-check-circle me-2"></i><?php echo $message; ?></div>
                <?php elseif ($message && $message_type === 'danger'): ?>
                    <div class="status-msg error-message"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" id="feedbackForm">
                    <div>
                        <label for="company_id"><i class="fas fa-building"></i>Company Name:</label>
                        <select id="company_id" name="company_id" required>
                            <option value="" disabled selected>Select Company</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($companyResult)) {
                                echo "<option value='" . $row['company_id'] . "'>" . htmlspecialchars($row['company_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label for="job_id"><i class="fas fa-briefcase"></i>Job Title:</label>
                        <select id="job_id" name="job_id" required>
                            <option value="" disabled selected>Select a company first</option>
                        </select>
                    </div>

                    <div>
                        <label for="rating"><i class="fas fa-star"></i>Rating:</label>
                        <select id="rating" name="rating" required>
                            <option value="" disabled selected>Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>

                    <div>
                        <label for="comments"><i class="fas fa-comment-dots"></i>Comments:</label>
                        <textarea id="comments" name="comments" rows="4" placeholder="Share your experience..." required></textarea>
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">

                    <button class="submit-btn" type="submit">Submit Feedback</button>
                </form>
            </div>
        </main>
        
        <?php include('footer.php'); ?>
    </div>
     
    <script>
        document.getElementById('company_id').addEventListener('change', function () {
            var companyId = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_jobs.php?company_id=' + companyId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('job_id').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    </script>
   
</body>
</html>