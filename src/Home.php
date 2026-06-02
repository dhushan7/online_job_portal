<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <style>
        /* Reset and base styles */
        html, body {
            margin: 0;
            padding: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        /* 1. THE CRITICAL FIX: The wrapper forces Navbar + Main Content to be exactly 100vh */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;           /* Exactly full screen height */
            box-sizing: border-box;
        }

        /* 2. Allows your content inside the 100vh wrapper to expand and fill any empty space */
        main {
            flex: 1; 
            display: flex;
            flex-direction: column;
        }

        /* Your existing hero & search styling */
        .hero-section {
            background: #182a3b;
            color: white;
            text-align: center;
            padding: 80px 20px;
            flex: 1; /* Optional: Makes the hero stretch to fill empty space if desired */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero-title { font-size: 40px; margin: 0; }
        .hero-subtitle { margin-top: 10px; opacity: 0.8; }
        
        .search-section { padding: 40px 0; text-align: center; background: #f4f6f9;}
        .search-input { padding: 10px; margin: 5px; width: 250px; }
        .btn { padding: 10px 15px; background:#E76F51; color:white; text-decoration:none; border-radius:5px; display: inline-block; margin-top: 15px;}
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <?php include 'navbar.php'; ?>

        <main>
            <div class="hero-section">
                <h1 class="hero-title">Find Your Dream Job</h1>
                <p class="hero-subtitle">Best opportunities are waiting for you</p>
                <div>
                    <a class="btn" href="job_listings.php">Browse Jobs</a>
                </div>
            </div>

            <div class="search-section">
                <h2>Search Jobs</h2>
                <form action="job_listings.php" method="GET">
                    <input class="search-input" type="text" name="keyword" placeholder="Job title">
                    <input class="search-input" type="text" name="location" placeholder="Location">
                    <button class="btn" type="submit">Search</button>
                </form>
            </div>
        </main>

    </div> <?php include 'footer.php'; ?>

</body>
</html>