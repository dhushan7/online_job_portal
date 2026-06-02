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
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Explicitly assign type mapping for navbar.php to prevent errors inside the navbar include file
if ($user) {
    $_SESSION['type'] = $user['type']; 
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy(); 
    header("Location: login.php"); 
    exit();
}

// Handle update logic
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $phone_number = trim($_POST['phone_number']);
    $skills = trim($_POST['skills']);
    $experience = trim($_POST['experience']);
    $education = trim($_POST['education']);
    $new_password = trim($_POST['new_password']);

    // Dynamically build the update statement safely using prepared values
    $update_sql = "UPDATE users SET name=?, phone_number=?, skills=?, experience=?, education=?" . 
                  ($new_password ? ", password=?" : "") . 
                  " WHERE email=?";
    
    $stmt = $con->prepare($update_sql);
    
    if ($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssssss", $name, $phone_number, $skills, $experience, $education, $hashed_password, $email);
    } else {
        $stmt->bind_param("ssssss", $name, $phone_number, $skills, $experience, $education, $email);
    }
    
    if ($stmt->execute()) {
        $message = "Profile updated successfully.";
        $message_type = "success";
        
        // Refresh local array data for instant render updates
        $user['name'] = $name;
        $user['phone_number'] = $phone_number;
        $user['skills'] = $skills;
        $user['experience'] = $experience;
        $user['education'] = $education;
    } else {
        $message = "Error updating profile: " . $stmt->error;
        $message_type = "danger";
    }
}

// Handle account deletion
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM users WHERE email=?";
    $stmt = $con->prepare($delete_sql);
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        session_destroy(); 
        header("Location: login.php?msg=Account permanently deleted"); 
        exit();
    } else {
        $message = "Error deleting account: " . $stmt->error;
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Online Job Portal</title>
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

        /* Structural Page Flex Wrapper Layout */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        main {
            flex: 1;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            box-sizing: border-box;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 2em;
        }

        .profile-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .profile-card h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }

        .profile-card h2 i {
            margin-right: 10px;
            color: #e74c3c; /* Theme Red Accent */
        }

        .profile-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eaeaea;
        }

        .profile-detail:last-of-type {
            border-bottom: none;
            margin-bottom: 20px;
            padding-bottom: 0;
        }

        .profile-detail label {
            font-weight: bold;
            color: #7f8c8d;
            font-size: 0.95em;
        }

        .profile-detail span {
            color: #333;
            font-weight: 500;
            text-align: right;
            max-width: 70%;
        }

        /* Action Buttons */
        .btn-theme {
            background: linear-gradient(135deg, #2c3e50, #0056b3);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            font-size: 0.9em;
            cursor: pointer;
            transition: opacity 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-right: 10px;
        }

        .btn-danger-outline {
            background: transparent;
            color: #e74c3c;
            border: 2px solid #e74c3c;
            padding: 8px 20px;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-danger-outline:hover {
            background-color: #e74c3c;
            color: white;
        }

        .btn-theme:hover {
            opacity: 0.95;
            transform: scale(1.01);
            color: white;
        }

        /* Status Banners */
        .status-msg {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 25px;
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

        /* Popups / Modals Architecture */
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            padding: 35px;
            border-radius: 8px;
            width: 90%;
            max-width: 550px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.3s ease-out;
            box-sizing: border-box;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.95em;
            display: block;
            margin-bottom: 8px;
        }

        .popup-content input, .popup-content textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .popup-content input:focus, .popup-content textarea:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
            outline: none;
        }

        .popup-content button[type="submit"] {
            background: linear-gradient(135deg, #2c3e50, #0056b3);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
            line-height: 1;
        }

        .close:hover {
            color: #333;
        }

        textarea {
            resize: vertical;
        }

        @media (max-width: 600px) {
            .profile-detail {
                flex-direction: column;
                align-items: flex-start;
            }
            .profile-detail span {
                text-align: left;
                max-width: 100%;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php include 'navbar.php'; ?>
    <main>
        <div class="container">
            <h1>User Profile</h1>
            
            <?php if ($message && $message_type === 'success'): ?>
                <div class="status-msg success-message"><i class="fas fa-check-circle me-2"></i><?= $message; ?></div>
            <?php elseif ($message && $message_type === 'danger'): ?>
                <div class="status-msg error-message"><i class="fas fa-exclamation-triangle me-2"></i><?= $message; ?></div>
            <?php endif; ?>

            <div class="profile-card">
                <h2><i class="fas fa-user-circle"></i>Profile Information</h2>
                <div class="profile-detail">
                    <label>Name:</label>
                    <span><?= htmlspecialchars($user['name']); ?></span>
                </div>
                <div class="profile-detail">
                    <label>Email:</label>
                    <span><?= htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="profile-detail">
                    <label>Phone Number:</label>
                    <span><?= htmlspecialchars($user['phone_number'] ?? 'Not Specified'); ?></span>
                </div>
                <div class="profile-detail">
                    <label>Account Created:</label>
                    <span><?= htmlspecialchars($user['created_at']); ?></span>
                </div>
                <div class="pt-2">
                    <button id="edit-button" class="btn-theme" aria-label="Edit Profile">Edit Profile</button>
                    <button type="button" class="btn-danger-outline" onclick="document.getElementById('delete-confirmation').style.display='flex'" aria-label="Delete Account">Delete Account</button>
                </div>
            </div>

            <div class="profile-card">
                <h2><i class="fas fa-tools"></i>Professional Skills</h2>
                <div class="profile-detail" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                    <span><?= nl2br(htmlspecialchars($user['skills'] ?? 'No skills added yet. Update your profile to display listings.')); ?></span>
                </div>
            </div>

            <div class="profile-card">
                <h2><i class="fas fa-history"></i>Work Experience</h2>
                <div class="profile-detail" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                    <span><?= nl2br(htmlspecialchars($user['experience'] ?? 'No work experience history specified.')); ?></span>
                </div>
            </div>

            <div class="profile-card">
                <h2><i class="fas fa-graduation-cap"></i>Education History</h2>
                <div class="profile-detail" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                    <span><?= nl2br(htmlspecialchars($user['education'] ?? 'No educational criteria tracked yet.')); ?></span>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

</div>

<div id="delete-confirmation" class="popup">
    <div class="popup-content" style="text-align: center;">
        <span class="close" onclick="document.getElementById('delete-confirmation').style.display='none'">&times;</span>
        <h3 style="font-family: 'Montserrat', sans-serif; color: #2c3e50; margin-bottom: 15px;">Delete Account?</h3>
        <p style="color: #666; margin-bottom: 25px;">Are you sure? This action is permanent and will completely retract your active application profiles.</p>
        <form method="POST" style="margin: 0;">
            <button type="submit" name="delete" class="btn-theme" style="background: #e74c3c; margin-bottom: 10px; width: 100%;">Yes, Delete Account permanently</button>
            <button type="button" class="btn-danger-outline" style="width: 100%; border-color: #ced4da; color: #666;" onclick="document.getElementById('delete-confirmation').style.display='none'">Cancel</button>
        </form>
    </div>
</div>

<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="document.getElementById('popup').style.display='none'">&times;</span>
        <h2 style="text-align: center; margin-bottom: 25px; color: #2c3e50; font-family: 'Montserrat', sans-serif;">Edit Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name-edit">Full Name</label>
                <input type="text" id="name-edit" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number-edit">Phone Number</label>
                <input type="text" id="phone_number-edit" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="skills-edit">Professional Skills</label>
                <textarea id="skills-edit" name="skills" rows="3" placeholder="e.g. PHP, JavaScript, CodeIgniter, Agile Execution..."><?= htmlspecialchars($user['skills'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="experience-edit">Work Experience</label>
                <textarea id="experience-edit" name="experience" rows="3" placeholder="Detail past organizational operational profiles..."><?= htmlspecialchars($user['experience'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="education-edit">Education History</label>
                <textarea id="education-edit" name="education" rows="3" placeholder="Degree titles, institutions, graduation dates..."><?= htmlspecialchars($user['education'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Leave blank to maintain current credentials">
            </div>
            <button type="submit" name="update" aria-label="Update Profile">Save Profile Changes</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('edit-button').onclick = function() {
        document.getElementById('popup').style.display = 'flex';
    }
    window.onclick = function(event) {
        if (event.target.classList.contains('popup')) {
            event.target.style.display = 'none';
        }
    }
</script>
</body>
</html>