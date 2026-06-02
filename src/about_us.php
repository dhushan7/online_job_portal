<?php 
// Include the navigation bar
include('navbar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Online Job Portal</title>
    <!-- Combined all font families into a single call matching your header theme precisely -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Base Reset synced with your master portal layout definitions */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA; /* Light background matching dashboard */
            color: #343a40;
        }

        /* 1. MASTER LAYOUT CONTROLLER LAYER */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* 2. FLEX AREA STYLING FOR STICKY FOOTER ACCORDANCE */
        main {
            flex: 1;
            padding: 40px 20px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            box-sizing: border-box;
        }

        /* Portal Brand Matching Header Banner */
        .about-header {
            text-align: center;
            padding: 35px 20px;
            background: linear-gradient(135deg, #2c3e50, #0056b3); /* Your core navy blue gradient */
            color: white;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .about-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.6em;
            margin: 0;
        }

        .about-header p {
            font-family: 'Lato', sans-serif;
            font-size: 1.2em;
            margin: 10px 0 0;
            opacity: 0.9;
        }

        /* Unified Card Theme Layouts */
        section {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd; /* Matches your feedback card layout borders */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        section:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        section h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.6em;
            color: #2c3e50; /* Primary Portal Navy Color */
            margin-top: 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        section p, section ul {
            line-height: 1.7;
            color: #555;
            font-size: 1.05em;
        }

        section ul {
            list-style-type: none; /* Strip stock bullets to apply custom design */
            padding-left: 0;
        }

        section ul li {
            position: relative;
            padding-left: 25px;
            margin-bottom: 10px;
        }

        /* Custom item icons using your profile highlight red color (#e74c3c) */
        section ul li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #e74c3c; 
            font-weight: bold;
        }

        /* Contact Details Custom Box Styling */
        .contact-info {
            background-color: #f8f9fa;
            border-left: 5px solid #e74c3c; /* Red border focus bar */
        }

        .contact-info a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .icon {
            margin-right: 12px;
            color: #e74c3c; /* Accent focus red color icons */
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <main>
            <div class="container">
                
                <header class="about-header">
                    <h1>About Us</h1>
                    <p>Connecting Talent with Opportunity</p>
                </header>
                
                <section>
                    <h2><i class="fas fa-rocket icon"></i>Our Mission</h2>
                    <p>
                        At <strong>Online Job Portal</strong>, our mission is to empower job seekers and employers alike by creating a seamless and efficient hiring process. We believe that every individual deserves a chance to showcase their skills, and every employer deserves access to the best talent. 
                    </p>
                    <p>
                        Our platform is designed to simplify the job search experience and facilitate meaningful connections in a fast-paced job market.
                    </p>
                </section>

                <section>
                    <h2><i class="fas fa-briefcase icon"></i>What We Offer</h2>
                    <ul>
                        <li>Comprehensive job listings across various industries.</li>
                        <li>User-friendly interface for both job seekers and employers.</li>
                        <li>Advanced search filters to find the perfect match.</li>
                        <li>Profile creation and resume upload for job seekers.</li>
                        <li>Company profiles to help candidates learn about potential employers.</li>
                        <li>Regular updates and notifications on new job postings.</li>
                    </ul>
                </section>

                <section>
                    <h2><i class="fas fa-eye icon"></i>Our Vision</h2>
                    <p>
                        We envision a world where job seekers can easily connect with employers, leading to a more fulfilling work life and a thriving economy. We strive to be the leading online job portal that not only matches candidates with job opportunities but also supports their career growth and development.
                    </p>
                </section>

                <section>
                    <h2><i class="fas fa-users icon"></i>Our Team</h2>
                    <p>
                        Our dedicated team comprises industry professionals, tech enthusiasts, and HR experts who are passionate about bridging the gap between talent and opportunity. With their combined experience and insights, we continuously strive to enhance our platform and user experience.
                    </p>
                </section>

                <section class="contact-info">
                    <h2><i class="fas fa-phone-alt icon"></i>Get in Touch</h2>
                    <p>
                        We would love to hear from you! Whether you’re a job seeker or an employer, feel free to reach out to us for any inquiries or feedback. Your input is invaluable in helping us improve our services.
                    </p>
                    <p>
                        <strong>Email:</strong> <a href="mailto:support@onlinejobportal.com">support@onlinejobportal.com</a><br>
                        <strong>Phone:</strong> <a href="tel:+12345678901">+1 234 567 8901</a>
                    </p>
                </section>
                
            </div>
        </main>
        
        <!-- Extracted from body tags onto the primary wrapper context layer safely -->
        <?php include('footer.php'); ?>
        
    </div>
   
</body>
</html>