<?php
// Start the session safely before content rendering begins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection file
require('db.php');

// Handle deletion of a company
if (isset($_POST['delete_company_id'])) {
    $company_id = intval($_POST['delete_company_id']); 

    // Prepare the SQL DELETE statement
    $query = "DELETE FROM companies WHERE company_id = ?";
    
    // Prepare and execute the statement
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $company_id); 
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Company deleted successfully.";
        } else {
            $_SESSION['message'] = "Error: Company could not be deleted.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error: Could not prepare the SQL statement.";
    }
}

// Handle editing of a company
if (isset($_POST['edit_company_id'])) {
    $company_id = intval($_POST['edit_company_id']);
    $name = $_POST['company_name'];
    $email = $_POST['company_email'];
    $address = $_POST['company_address'];
    $contact_number = $_POST['company_contact_number'];

    // Prepare the SQL UPDATE statement
    $query = "UPDATE companies SET company_name = ?, email = ?, address = ?, contact_number = ? WHERE company_id = ?";
    
    // Prepare and execute the statement
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssssi", $name, $email, $address, $contact_number, $company_id);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Company updated successfully.";
        } else {
            $_SESSION['message'] = "Error: Company could not be updated.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error: Could not prepare the SQL statement.";
    }
}

// Handle search query
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Fetch all companies from the database
$query = "SELECT * FROM companies WHERE company_name LIKE ?";
$stmt = $con->prepare($query);
$search_param = '%' . $search_term . '%';
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies List - Admin Panel</title>
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

        .container {
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

        .status-msg {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            font-size: 0.95em;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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

        /* FIXES THE BLUE OUTLINE - Changes focus glow to System Accent Red */
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


        .flex-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 2px solid #ddd;
        }

        .card h3 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            font-size: 1.4em;
            margin-top: 0;
            margin-bottom: 12px;
        }

        .card p {
            line-height: 1.5;
            color: #555;
            margin: 6px 0;
            font-size: 0.9em;
        }

        .card p strong {
            color: #2c3e50;
        }

        /* Operational actions alignment inside layout */
        .card-actions {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
        }

        .card button, .card form button {
            width: 100%;
            padding: 11px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: opacity 0.3s, transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card button.edit-btn {
            background-color: #2c3e50;
            color: white;
        }

        .card button.delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .card button:hover, .card form button:hover {
            opacity: 0.9;
            transform: scale(1.01);
        }

        .no-records {
            text-align: center;
            color: #7f8c8d;
            padding: 40px 0;
            font-size: 1.1em;
            grid-column: 1 / -1;
        }


        #editModal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        #editModal .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #ddd;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.3s;
            box-sizing: border-box;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        #editModal .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }

        #editModal .close:hover {
            color: #e74c3c;
        }

        #editModal h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        #editModal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #editModal form label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.9em;
            margin-bottom: -5px;
        }

        #editModal form input {
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 15px;
            background-color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
        }

        /* MODAL FOCUS OVERRIDE: Replaces the old blue look with your theme red accent */
        #editModal form input:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        #editModal form button {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            margin-top: 10px;
            transition: opacity 0.3s;
        }

        #editModal form button:hover {
            opacity: 0.95;
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
            <div class="container">
                
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="status-msg">' . htmlspecialchars($_SESSION['message']) . '</div>';
                    unset($_SESSION['message']); 
                }
                ?>

                <h2 style="text-align: center; margin-bottom: 30px;">Registered Corporations</h2>

                <div class="search-wrapper">
                    <form method="GET" action="" class="search-form">
                        <div class="input-group">
                            <input class="search-input" type="text" name="search" placeholder="Search by registered company title..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <i class="fas fa-building"></i>
                        </div>
                        <button type="submit" class="search-button">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </form>
                </div>

                <div class="flex-container">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="card">
                                <div>
                                    <?php if (!empty($row['logo'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['logo']); ?>" alt="Company Logo">
                                    <?php else: ?>
                                        <img src="default-logo.png" alt="Default Logo">
                                    <?php endif; ?>
                                    <h3><?php echo htmlspecialchars($row['company_name']); ?></h3>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
                                </div>
                                
                                <div class="card-actions">
                                    <button class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars($row['company_id']); ?>, '<?php echo htmlspecialchars(addslashes($row['company_name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['address'])); ?>', '<?php echo htmlspecialchars(addslashes($row['contact_number'])); ?>')">
                                        <i class="fas fa-edit"></i> Edit Profile
                                    </button>
                                    <form method="POST" action="" style="width: 100%; margin: 0;">
                                        <input type="hidden" name="delete_company_id" value="<?php echo $row['company_id']; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to drop this corporate organization registration card profile?');">
                                            <i class="fas fa-trash-alt"></i> Drop Profile
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-records">
                            <i class="fas fa-folder-open fa-2x mb-3" style="color: #bdc3c7;"></i>
                            <p>No corporate records available matching filters.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
    </div>

    <div id="editModal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Company</h2>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="edit_company_id" id="edit_company_id" value="">
                
                <label for="company_name">Company Name:</label>
                <input type="text" name="company_name" id="company_name" required>
                
                <label for="company_email">Email:</label>
                <input type="email" name="company_email" id="company_email" required>
                
                <label for="company_address">Address:</label>
                <input type="text" name="company_address" id="company_address" required>
                
                <label for="company_contact_number">Contact Number:</label>
                <input type="text" name="company_contact_number" id="company_contact_number" required>
                
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(companyId, name, email, address, contactNumber) {
            document.getElementById('edit_company_id').value = companyId;
            document.getElementById('company_name').value = name;
            document.getElementById('company_email').value = email;
            document.getElementById('company_address').value = address;
            document.getElementById('company_contact_number').value = contactNumber;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>