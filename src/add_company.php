<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php'); 

// Initialize variables for success and error messages
$success = '';
$error = '';

// Handle company addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    // Sanitize and validate inputs
    $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $logo_path = '';

    // Handle file upload for logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = 'uploads/';
            
            // Ensure folder exists
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $logo_path = $uploadFileDir . uniqid() . '.' . $fileExtension;
            
            if (!move_uploaded_file($fileTmpPath, $logo_path)) {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, or PNG allowed.";
        }
    }

    if (empty($error)) {
        // Check if email already exists
        $checkQuery = "SELECT * FROM companies WHERE email = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            // Insert new company into the database
            $query = "INSERT INTO companies (company_name, email, address, contact_number, logo, description) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssssss", $company_name, $email, $address, $contact_number, $logo_path, $description);

            if ($stmt->execute()) {
                $success = "Company added successfully.";
            } else {
                $error = "Failed to add company. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* Base Structural Rules */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA;
            color: #343a40;
        }

        /* 1. FLEX LAYOUT ARCHITECTURE (Accommodates sidebars cleanly) */
        .page-wrapper {
            display: flex;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* Accounts for admin sidebar container dimensions gracefully */
        .sidebar-container {
            width: 260px; /* Adjust based on your actual sidebar width */
            flex-shrink: 0;
        }

        main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-company-container {
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

        .add-company-container:hover {
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
            color: #e74c3c; /* Core branding accent color code */
            width: 16px;
            text-align: center;
        }

        .form-group input,
        .form-group textarea {
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

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        }

        /* File Upload Input Custom Adjustments */
        .form-group input[type="file"] {
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, #2c3e50, #0056b3); /* Branding Blue Gradient */
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

        /* Inline field dynamic feedback spans */
        .field-error {
            color: #e74c3c;
            font-size: 13px;
            display: block;
            margin-top: 5px;
            font-weight: bold;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                flex-direction: column;
            }
            .add-company-container {
                padding: 20px;
            }
        }
    </style>

    <script>
        function validateForm() {
            const companyName = document.getElementById('company_name').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const contactNumber = document.getElementById('contact_number').value;
            const logo = document.getElementById('logo').value;

            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.field-error').forEach((elem) => {
                elem.innerText = '';
            });

            if (companyName.trim() === '') {
                document.getElementById('company_name-error').innerText = 'Company name is required.';
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').innerText = 'Invalid email format.';
                isValid = false;
            }

            if (address.trim() === '') {
                document.getElementById('address-error').innerText = 'Address is required.';
                isValid = false;
            }

            // Universal length checker fallback (handles multiple formats up to 15 digits safely)
            if (contactNumber.length < 9 || contactNumber.length > 15) {
                document.getElementById('contact_number-error').innerText = 'Please enter a valid phone length.';
                isValid = false;
            }

            if (logo && !logo.match(/\.(jpg|jpeg|png)$/i)) {
                document.getElementById('logo-error').innerText = 'Only JPG, JPEG, or PNG files are allowed.';
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include("sidebar.php"); ?>

        <main>
            <div class="add-company-container">
                <h2><i class="fas fa-building" style="color: #e74c3c; margin-right: 10px;"></i>Add New Company</h2>

                <?php if ($success): ?>
                    <div class="status-msg success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="status-msg error-message"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST" class="add-company-form" enctype="multipart/form-data" onsubmit="return validateForm()">
                    
                    <div class="form-group">
                        <label for="company_name"><i class="fas fa-signature"></i>Company Name:</label>
                        <input type="text" id="company_name" name="company_name" placeholder="e.g. Tech Corp Ltd"安全 required>
                        <span class="field-error" id="company_name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i>Corporate Email:</label>
                        <input type="email" id="email" name="email" placeholder="e.g. contact@techcorp.com" required>
                        <span class="field-error" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="address"><i class="fas fa-map-marked-alt"></i>Headquarters Address:</label>
                        <textarea id="address" name="address" placeholder="Enter physical company office address" required></textarea>
                        <span class="field-error" id="address-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="contact_number"><i class="fas fa-phone"></i>Contact Hotline:</label>
                        <input type="tel" id="contact_number" name="contact_number" placeholder="Enter phone number" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span class="field-error" id="contact_number-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="logo"><i class="fas fa-image"></i>Company Brand Logo:</label>
                        <input type="file" id="logo" name="logo">
                        <span class="field-error" id="logo-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-file-alt"></i>About Corporate Description:</label>
                        <textarea id="description" name="description" placeholder="Briefly introduce industry space or company background..." required></textarea>
                        <span class="field-error" id="description-error"></span>
                    </div>

                    <button type="submit" class="btn-submit">Register Company Profile</button>
                </form>
            </div>
        </main>
        
    </div>

</body>
</html>