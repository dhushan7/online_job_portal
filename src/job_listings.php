<?php
// Start the session safely before content rendering begins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php';

$keyword = $_GET['keyword'] ?? '';
$location = $_GET['location'] ?? '';

$stmt = $con->prepare("
    SELECT j.job_id, j.job_title, j.description, j.location, c.company_name
    FROM jobs j
    INNER JOIN companies c ON j.company_id = c.company_id
    WHERE j.job_title LIKE ? AND j.location LIKE ?
");

$kw = "%$keyword%";
$loc = "%$location%";

$stmt->bind_param("ss", $kw, $loc);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Reset and Base Rules */
        html, body { 
            margin: 0; 
            padding: 0; 
            height: 100%;
            font-family: 'Open Sans', sans-serif; 
            background: #FAFAFA; 
            color: #343a40;
        }

        /* 100vh Layout Wrapper */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* Expands vertically inside wrapper frames */
        main {
            flex: 1 0 auto; 
            padding: 40px 20px;
        }

        .container { 
            width: 100%; 
            max-width: 1200px;
            margin: auto; 
            box-sizing: border-box;
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50; /* Core system navy blue */
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 25px;
        }

        /* Grid Layout for Job Cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        /* Styled Card Profile */
        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 1.3em;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .card p {
            line-height: 1.6;
            color: #555;
            margin: 8px 0;
        }

        .company-name {
            font-family: 'Lato', sans-serif;
            color: #0056b3;
            font-weight: bold;
            font-size: 1.05em;
        }

        .job-location {
            color: #7f8c8d !important; 
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .job-location i {
            color: #e74c3c; /* Accent theme red icon markup */
            margin-right: 6px;
            width: 14px;
        }

        .job-snippet {
            font-size: 0.95em;
            margin-top: 12px !important;
        }

        /* Core Theme CTA Button */
        .btn {
            display: block;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #2c3e50, #0056b3); /* Navy Gradient */
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s, transform 0.2s;
        }

        .btn:hover {
            opacity: 0.95;
            transform: scale(1.01);
            color: white;
        }

        .no-records {
            text-align: center;
            color: #7f8c8d;
            padding: 40px 0;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include 'navbar.php'; ?>

        <main>
            <div class="container">
                <h2>Available Jobs</h2>

                <?php if ($result->num_rows > 0): ?>
                    <div class="grid">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="card">
                                <div>
                                    <h3><?= htmlspecialchars($row['job_title']) ?></h3>
                                    <p class="company-name"><?= htmlspecialchars($row['company_name']) ?></p>
                                    <p class="job-location">
                                        <i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($row['location']) ?>
                                    </p>
                                    <p class="job-snippet">
                                        <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>...
                                    </p>
                                </div>
                                <a class="btn" href="job_details.php?id=<?= (int)$row['job_id'] ?>">
                                    View Details
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                
                <?php else: ?>
                    <div class="no-records">
                        <i class="fas fa-search fa-2x mb-3" style="color: #bdc3c7;"></i>
                        <p>No jobs found matching your criteria.</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </main>

    </div> <?php include 'footer.php'; ?>

</body>
</html>