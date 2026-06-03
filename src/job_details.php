<?php
// Include database connection
include 'db.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = (int) $_GET['id'];

$stmt = $con->prepare("
    SELECT j.job_title, j.description, j.location, j.salary, c.company_name
    FROM jobs j
    INNER JOIN companies c ON j.company_id = c.company_id
    WHERE j.job_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Job not found");
}

$job = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['job_title']) ?> - Job Details</title>

    <style>
        /* Base Reset & Height Handlers */
        html, body { 
            margin: 0; 
            padding: 0; 
            height: 100%;
            font-family: Arial, sans-serif; 
            background: #f4f6f9; 
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
            padding: 40px 0;
            display: flex;
            align-items: center; /* Vertically centers the profile card on the screen */
        }

        .container {
            width: 70%;
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        h1 {
            margin-top: 0;
            color: #2c3e50;
        }

        hr {
            border: 0;
            height: 1px;
            background: #eee;
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #E76F51;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include 'navbar.php'; ?>

        <main>
            <div class="container">
                <h1><?= htmlspecialchars($job['job_title']) ?></h1>

                <p><b>Company:</b> <?= htmlspecialchars($job['company_name']) ?></p>
                <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
                <p><b>Salary:</b> <?= htmlspecialchars($job['salary']) ?></p>

                <hr>

                <p style="line-height: 1.6; color: #444;"><?= nl2br(htmlspecialchars($job['description'])) ?></p>

                <a class="btn" href="job_listings.php">← Back to Jobs</a>
            </div>
        </main>

    </div> <?php include 'footer.php'; ?>

</body>
</html>