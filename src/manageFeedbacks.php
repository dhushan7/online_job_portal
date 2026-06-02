<?php
// Include database connection
include('db.php');
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navigation bar
include('navbar.php');

// Initialize variables
$message = '';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "You need to be logged in to manage feedback.";
    exit; // Stop the script if not logged in
}

// Get the logged-in user's email
$user_email = $_SESSION['email'];

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $feedback_id = intval($_GET['id']);
    $deleteSql = "DELETE FROM feedbacks WHERE feedback_id = $feedback_id";
    
    if (mysqli_query($con, $deleteSql)) {
        $message = "Feedback deleted successfully.";
    } else {
        $message = "Error deleting feedback: " . mysqli_error($con);
    }
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $rating = intval($_POST['rating']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);
    
    $updateSql = "UPDATE feedbacks SET rating = $rating, comments = '$comments' WHERE feedback_id = $feedback_id";

    if (mysqli_query($con, $updateSql)) {
        $message = "Feedback updated successfully.";
    } else {
        $message = "Error updating feedback: " . mysqli_error($con);
    }
}

// SQL query to retrieve feedback data along with the related user, company, and job names
$sql = "SELECT f.feedback_id, u.name AS username, c.company_name, j.job_title, f.rating, f.comments, f.created_at
        FROM feedbacks f
        JOIN users u ON f.user_id = u.id
        JOIN companies c ON f.company_id = c.company_id
        JOIN jobs j ON f.job_id = j.job_id
        WHERE u.email = '$user_email'";  // Filter by logged-in user's email

$result = mysqli_query($con, $sql);

// Check for query execution errors
if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedbacks - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            box-sizing: border-box;
        }
        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .card h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 1.3em;
            margin-top: 0;
            margin-bottom: 10px;
            padding-right: 140px; /* Space out title from side actions */
        }
        .rating {
            font-size: 1.2em;
            color: gold;
            margin-bottom: 12px;
        }
        .card p {
            line-height: 1.6;
            color: #555;
            margin-bottom: 10px;
        }
        .actions {
            position: absolute;
            right: 25px;
            top: 25px;
            display: flex;
            gap: 10px;
        }
        .actions button, .actions a {
            padding: 8px 14px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            font-size: 0.85em;
            transition: opacity 0.3s, background-color 0.3s;
        }
        .actions a {
            background-color: #dc3545; 
        }
        .actions button:hover, .actions a:hover {
            opacity: 0.9;
            color: white;
        }
        .feedback-btn {
            display: inline-block;
            margin-bottom: 10px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            text-decoration: none;
            font-size: 0.95em;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s;
        }
        .feedback-btn:hover {
            opacity: 0.95;
            color: white;
        }

        /* Status Message Banner Styles */
        .status-msg {
            padding: 12px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
            font-size: 0.95em;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 8% auto;
            padding: 30px;
            border: 1px solid #ddd;
            width: 90%;
            max-width: 550px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-content h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        .close:hover {
            color: #333;
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
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 15px;
            background-color: #fff;
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
            outline: none;
        }
        .modal-submit-btn {
            background: linear-gradient(135deg, #2c3e50, #0056b3);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s;
        }
        .modal-submit-btn:hover {
            opacity: 0.95;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <main>
        <div class="container">
            <h1>Manage Feedbacks</h1>
            <a href="feedback_create.php" class="feedback-btn">Give Feedback</a>
            
            <?php if ($message): ?>
                <div class="status-msg success-message"><i class="fas fa-check-circle me-2"></i><?php echo $message; ?></div>
            <?php endif; ?>

            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='card'>";
                    echo "<h2>" . htmlspecialchars($row['username']) . " - " . htmlspecialchars($row['company_name']) . " - " . htmlspecialchars($row['job_title']) . "</h2>";
                    echo "<div class='rating'>" . str_repeat('<i class="fas fa-star"></i>', $row['rating']) . str_repeat('<i class="far fa-star"></i>', 5 - $row['rating']) . "</div>";
                    echo "<p>" . htmlspecialchars($row['comments']) . "</p>";
                    echo "<p><small class='text-muted'><i class='far fa-calendar-alt me-1'></i>Posted on: " . $row['created_at'] . "</small></p>";
                    echo "<div class='actions'>
                            <button class='editBtn' data-id='" . $row['feedback_id'] . "' data-username='" . htmlspecialchars($row['username']) . "' data-company='" . htmlspecialchars($row['company_name']) . "' data-job='" . htmlspecialchars($row['job_title']) . "' data-rating='" . $row['rating'] . "' data-comment='" . htmlspecialchars($row['comments']) . "'>Edit</button>
                            <a href='?action=delete&id=" . $row['feedback_id'] . "' onclick=\"return confirm('Are you sure you want to delete this feedback?');\"><i class='fas fa-trash-alt me-1'></i>Delete</a>
                          </div>";
                    echo "</div>";
                }
            } else {
                echo "<p style='margin-top: 20px; color: #7f8c8d;'>No feedbacks found.</p>";
            }
            ?>
        </div>
    </main>

    <?php include('footer.php'); ?>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Edit Feedback</h2>
        <form id="editForm" method="POST">
            <input type="hidden" id="feedback_id" name="feedback_id">
            <div class="form-group">
                <label for="username">User Name:</label>
                <input type="text" id="username" name="username" required readonly style="background-color: #f8f9fa; color: #6c757d;">
            </div>
            <div class="form-group">
                <label for="company">Company Name:</label>
                <input type="text" id="company" name="company" required readonly style="background-color: #f8f9fa; color: #6c757d;">
            </div>
            <div class="form-group">
                <label for="job">Job Title:</label>
                <input type="text" id="job" name="job" required readonly style="background-color: #f8f9fa; color: #6c757d;">
            </div>
            <div class="form-group">
                <label for="rating">Rating:</label>
                <div id="starRating" class="rating" style="cursor: pointer;">
                    <i class="far fa-star" data-value="1"></i>
                    <i class="far fa-star" data-value="2"></i>
                    <i class="far fa-star" data-value="3"></i>
                    <i class="far fa-star" data-value="4"></i>
                    <i class="far fa-star" data-value="5"></i>
                </div>
                <input type="hidden" id="rating" name="rating" value="1" required>
            </div>
            <div class="form-group">
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4" required></textarea>
            </div>
            <button type="submit" class="modal-submit-btn">Update Feedback</button>
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById("myModal");
    var closeModal = document.getElementById("closeModal");

    document.querySelectorAll('.editBtn').forEach(function(button) {
        button.onclick = function() {
            document.getElementById('feedback_id').value = this.getAttribute('data-id');
            document.getElementById('username').value = this.getAttribute('data-username');
            document.getElementById('company').value = this.getAttribute('data-company');
            document.getElementById('job').value = this.getAttribute('data-job');
            document.getElementById('rating').value = this.getAttribute('data-rating');
            updateStars(this.getAttribute('data-rating'));
            document.getElementById('comments').value = this.getAttribute('data-comment');
            
            modal.style.display = "block";
        };
    });

    closeModal.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    var stars = document.querySelectorAll('#starRating i');
    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            var ratingValue = this.getAttribute('data-value');
            document.getElementById('rating').value = ratingValue;
            updateStars(ratingValue);
        });
    });

    function updateStars(rating) {
        stars.forEach(function(star) {
            if (star.getAttribute('data-value') <= rating) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }

    function initializeStars() {
        var rating = document.getElementById('rating').value;
        updateStars(rating);
    }

    document.addEventListener('DOMContentLoaded', initializeStars);
</script>
</body>
</html>