<?php
// Start the session safely before content rendering begins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php');

// Initialize success and error messages
$success = '';
$error = '';

// Fetch companies from the database for the dropdown
$companyQuery = "SELECT company_id, company_name FROM companies";
$companyResult = $con->query($companyQuery);

// Handle job addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $job_title = filter_input(INPUT_POST, 'job_title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING); 
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
    $company_id = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_NUMBER_INT);

    // Ensure all fields are filled
    if ($job_title && $description && $location && $salary && $company_id) {
        // Insert new job into the database
        $query = "INSERT INTO jobs (job_title, description, location, salary, company_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        
        // FIXED: Properly handles salary decimals via 'sssdi' parameter definitions
        $stmt->bind_param("sssdi", $job_title, $description, $location, $salary, $company_id); 

        if ($stmt->execute()) {
            $success = "Job added successfully.";
        } else {
            $error = "Failed to add job. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* Theme Alignment Baseline styling */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        /* Master Flex wrapper configuration layer */
        .page-wrapper {
            display: flex;
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

        .add-job-container {
            background-color: #ffffff;
            padding: 35px;
            max-width: 550px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        .add-job-container:hover {
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

        .form-group label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.95em;
            display: block;
            margin-bottom: 8px;
        }

        .form-group label i {
            margin-right: 8px;
            color: #e74c3c; /* Theme Red Accent Color */
            width: 16px;
            text-align: center;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 15px;
            background-color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        /* REMOVED BLUE FOCUS STYLES - Replaced with Core Red Theme Accent Glow */
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #2c3e50, #0056b3); /* Branding Blue/Navy Gradient */
            color: white;
            padding: 14px;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s, transform 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-submit:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

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
            min-height: 100px;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                flex-direction: column;
            }
            .add-job-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include("sidebar.php"); ?>

        <main>
            <div class="add-job-container">
                <h2><i class="fas fa-briefcase" style="color: #e74c3c; margin-right: 10px;"></i>Add New Job</h2>

                <?php if ($success): ?>
                    <div class="status-msg success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="status-msg error-message"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form action="add_jobs.php" method="POST" onsubmit="return validateForm()">
                    
                    <div class="form-group">
                        <label for="job_title"><i class="fas fa-heading"></i>Job Title:</label>
                        <input type="text" id="job_title" name="job_title" placeholder="e.g. Senior Backend Developer" required>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i>Job Description:</label>
                        <textarea id="description" name="description" placeholder="Detail standard operations scope and candidate expectations..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="location"><i class="fas fa-map-marker-alt"></i>Location:</label>
                        <input type="text" id="location" name="location" placeholder="e.g. Colombo, LK or Remote" required>
                    </div>

                    <div class="form-group">
                        <label for="salary"><i class="fas fa-money-bill-wave"></i>Annual Salary ($):</label>
                        <input type="text" id="salary" name="salary" placeholder="e.g. 85000.00" required oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                    </div>

                    <div class="form-group">
                        <label for="company"><i class="fas fa-building"></i>Hiring Corporation:</label>
                        <select id="company" name="company" required>
                            <option value="" disabled selected>Choose target organization context</option>
                            <?php while($row = $companyResult->fetch_assoc()): ?>
                                <option value="<?php echo $row['company_id']; ?>"><?php echo htmlspecialchars($row['company_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">Publish Job Opportunity</button>
                </form>
            </div>
        </main>
        
    </div>

    <script src="scripts/validateForm.js"></script>
</body>
</html>