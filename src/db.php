<?php
$con = mysqli_connect("localhost", "root", "97271", "online_job_portal");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
?>
