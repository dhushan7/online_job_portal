<?php
include 'navbar.php';
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
<html>
<head>
<title>Job Details</title>

<style>
body { font-family:Arial; background:#f4f6f9; }

.page-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;           /* Exactly full screen height */
    box-sizing: border-box;
}

.container {
    width:70%;
    margin:auto;
    background:white;
    padding:30px;
    margin-top:50px;
    border-radius:10px;
}

.btn {
    display:inline-block;
    margin-top:20px;
    padding:10px 15px;
    background:#E76F51;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
</style>
</head>

<body>

<div class="container">

    <h1><?= htmlspecialchars($job['job_title']) ?></h1>

    <p><b>Company:</b> <?= htmlspecialchars($job['company_name']) ?></p>
    <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
    <p><b>Salary:</b> <?= htmlspecialchars($job['salary']) ?></p>

    <hr>

    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>

    <a class="btn" href="job_listings.php">← Back to Jobs</a>

</div>

</body>
</html>

<?php include 'footer.php'; ?>