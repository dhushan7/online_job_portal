<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Handle registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Initialize error array
    $errors = [];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed to check email existence
    if (empty($errors)) {
        $checkQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Email already exists.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for better security
            $query = "INSERT INTO users (name, email, phone_number, password, type) VALUES (?, ?, ?, ?, 0)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $phone_number, $hashed_password);

            if ($stmt->execute()) {
                // Registration successful
                $_SESSION['name'] = $name; // Store name in session
                
                // Auto redirect
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Registration failed. Please try again.";
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
    <title>Job Portal - Register</title>
    <style>
        /* Base Reset & Height Handlers */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
        }

        body {
            background-image: url('images/bg.jpg'); /* Match login background path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #2c3e50, #0056b3);
            padding: 20px;
            color: white;
            flex-shrink: 0;
        }

        header .logo {
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
        }

        .logo1 {
            height: 45px;
            width: fit-content;
            justify-content: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid transparent;
        }

        nav a:hover {
            color: #e74c3c;
            border: 2px solid #e74c3c;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form {
            margin: 10px auto;
        }

        .register-title {
            text-align: center;
            color: #34495e; 
            margin-bottom: 20px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .register-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Prevents fields from stretching weirdly */
            transition: border-color 0.3s;
        }

        .register-input:focus {
            border-color: #e74c3c;
            outline: none;
        }

        .register-button {
            background-color: #2c3e50; 
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
        }

        .register-button:hover {
            background-color: #34495e; 
            transform: scale(1.05);
        }

        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #2c3e50; 
            text-decoration: none;
            font-weight: bold;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <header>
            <div class="logo"><img class="logo1" src="images/full_logo.png" alt="Logo"></div>
            <nav>
                <ul>
                    <li><a href="register.php" class="active">Sign Up</a></li>
                    <li><a href="login.php">Sign In</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="container">
                <form class="form" method="POST" action="">
                    <h1 class="register-title">Register</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="error-messages">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" class="register-input" placeholder="Full Name" required>
                    </div>

                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="register-input" placeholder="Email Address" required>
                    </div>

                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="register-input" placeholder="Password" required>
                    </div>

                    <div class="input-wrapper">
                        <input type="password" id="confirm-password" name="confirm_password" class="register-input" placeholder="Confirm Password" required>
                    </div>

                    <div class="input-wrapper">
                        <input type="tel" id="phone" name="phone_number" class="register-input" placeholder="Phone Number" required>
                    </div>

                    <input type="submit" value="Register Now" class="register-button">

                    <div class="forgot-password">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </main>

    </div> <?php include 'footer.php'; ?>

</body>
</html>