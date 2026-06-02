<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .footer {
            opacity: 0;
            transition: opacity 1s ease-in;
            background-color: #2c3e50;
            color: #fafafa;
            padding: 2rem 1rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .footer.visible { opacity: 1; }


        .footer-section {
            flex: 1;
            margin: 1rem;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #e74c3c; /* Accent color */
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section li {
            margin: 0.5rem 0;
        }

        .footer-section a {
            color: #fafafa; /* Link color */
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #e74c3c; /* Hover effect */
        }

        .footer-bottom {
            text-align: center;
            width: 100%;
            margin-top: 2rem;
            border-top: 1px solid #4ecdc4; /* Top border color */
            padding-top: 1rem;
        }

        .footer-bottom p {
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .bottom-links {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .bottom-links li a {
            color: #fafafa; /* Link color */
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-icons a img {
            width: 32px;
            height: 32px;
            transition: transform 0.3s;
        }

        .social-icons a:hover img {
            transform: scale(1.1); /* Zoom on hover */
        }
    </style>
</head>
<body>
    <footer id="footer" class="footer"> 
        
        <div class="footer-section job-portal-only">
            <h3>Job Portal</h3>
            <ul>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="about_us.php">Contact Us</a></li>
                <li><a href="about_us.php">Login & Support</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Job Seekers</h3>
            <ul>
                <li><a href="job_listings.php">Search Jobs</a></li>
                <li><a href="job_listings.php">Submit Resume</a></li>
                <li><a href="Career_advice.php">Career Advice</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Employers</h3>
            <ul>
                <li><a href="#">Post a Job</a></li>
                <li><a href="#">View Candidates</a></li>
                <li><a href="#">Employer Dashboard</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Legal</h3>
            <ul>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Cookie Policy</a></li>
            </ul>
        </div>

        <div class="footer-bottom">
            <p>Copyright © All rights reserved - 2024 Designed by Work Wise</p>
            <ul class="bottom-links">
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
            <div class="social-icons">
                <a href="https://web.facebook.com/?_rdc=1&_rdr"><img src="images/facebook-icon.png" alt="Facebook"></a>
                <a href="#"><img src="images/twitter-icon.png" alt="Twitter"></a>
                <a href="#"><img src="images/instagram-icon.png" alt="Instagram"></a>
            </div>
        </div>
    </footer>

    <script src="js/footer.js"></script>
</body>
</html>
