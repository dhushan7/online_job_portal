<?php
// Start the session safely before content rendering begins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php');

// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch jobs with their associated company names, filtered by search query
$query = "SELECT j.job_id, j.job_title, j.description, j.location, j.salary, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.company_id 
          WHERE j.job_title LIKE ? OR c.company_name LIKE ? OR j.location LIKE ?";
$stmt = $con->prepare($query);
$search_term = "%" . $search_query . "%";
$stmt->bind_param("sss", $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Jobs - Admin Panel</title>
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

        .page-wrapper {
            display: flex;
            min-height: 100vh;
            box-sizing: border-box;
            margin-left: 250px;
        }

        main {
            flex: 1;
            padding: 40px 20px;
        }

        .job-container { 
            width: 100%; 
            max-width: 1200px;
            margin: auto; 
            box-sizing: border-box;
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .search-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 35px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .input-group {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            transition: color 0.3s;
        }

        .search-input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 15px;
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-input:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            outline: none;
        }

        .search-input:focus + i {
            color: #e74c3c;
        }

        .search-button {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            color: white;
            border: none;
            padding: 0 30px;
            height: 49px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s, transform 0.2s;
        }

        .search-button:hover {
            opacity: 0.95;
            transform: scale(1.01);
        }

        .job-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .job-card {
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

        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .job-card h3 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 1.3em;
            margin-top: 0;
            margin-bottom: 12px;
        }

        .job-card p {
            line-height: 1.6;
            color: #555;
            margin: 6px 0;
            font-size: 0.95em;
        }

        .job-card p strong {
            color: #2c3e50;
        }

        /* Button Groups inside Management Cards */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            flex: 1;
            padding: 11px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: opacity 0.3s, transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .button:hover {
            opacity: 0.9;
            transform: scale(1.01);
        }

        .edit-button {
            background: #2c3e50;
            color: white;
        }

        .delete-button {
            background: #e74c3c;
            color: white;
        }

        .no-records {
            text-align: center;
            color: #7f8c8d;
            padding: 40px 0;
            font-size: 1.1em;
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include("sidebar.php"); ?>

        <main>
            <div class="job-container">
                <h2>Available Jobs</h2>

                <div class="search-wrapper">
                    <form class="search-form" action="" method="GET">
                        <div class="input-group">
                            <input class="search-input" type="text" name="search" placeholder="Search by job title, company, or location..." value="<?php echo htmlspecialchars($search_query); ?>">
                            <i class="fas fa-search"></i>
                        </div>
                        <button type="submit" class="search-button">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </form>
                </div>

                <div class="job-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="job-card">
                                <div>
                                    <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                                    <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p><strong>Salary:</strong> $<?php echo htmlspecialchars(number_format($row['salary'], 2)); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                </div>
                                <div class="button-group">
                                    <a href="edit_job.php?id=<?php echo $row['job_id']; ?>" class="button edit-button"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_job.php?id=<?php echo $row['job_id']; ?>" class="button delete-button" onclick="return confirm('Are you sure you want to completely drop this job opening profile?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-records">
                            <i class="fas fa-search fa-2x mb-3" style="color: #bdc3c7;"></i>
                            <p>No managed job records available matching filters.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
    </div>

</body>
</html>