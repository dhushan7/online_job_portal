<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Advice - Online Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #2c3e50, #0056b3);
            color: white;
        }

        header h1 {
            font-size: 2.5em;
            margin: 0;
            animation: slideIn 1s ease-in-out;
        }

        header p {
            font-size: 1.2em;
            margin: 10px 0 0;
        }

        .search-bar {
            margin: 20px 0;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 1px solid #0056b3;
            border-radius: 5px;
            font-size: 1em;
        }

        section {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        section h2 {
            font-size: 2em;
            color: #2c3e50;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .faq-item {
            margin-bottom: 15px;
        }

        .faq-question {
            font-weight: bold;
            color: #0056b3;
            cursor: pointer;
            padding: 10px;
            border: 1px solid #0056b3;
            border-radius: 5px;
            background-color: #e9ecef;
            transition: background-color 0.3s;
        }

        .faq-question:hover {
            background-color: #dbe7f4;
        }

        .faq-answer {
            display: none;
            padding: 10px 20px;
            margin: 0;
            background-color: #f8f9fa;
            border-left: 4px solid #0056b3;
            border-radius: 0 0 5px 5px;
        }

        .back-to-top {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .back-to-top:hover {
            background-color: #004494;
        }

        .contact-info {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .contact-info a {
            color: #0056b3;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
    <script>
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }
        }

        function filterFAQs() {
            const searchInput = document.getElementById('faqSearch').value.toLowerCase();
            const faqItems = document.getElementsByClassName('faq-item');

            Array.from(faqItems).forEach(item => {
                const question = item.getElementsByClassName('faq-question')[0].innerText.toLowerCase();
                if (question.includes(searchInput)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        window.onscroll = function() {
            const backToTopBtn = document.getElementById('backToTop');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopBtn.style.display = "block";
            } else {
                backToTopBtn.style.display = "none";
            }
        };

        function scrollToTop() {
            document.body.scrollTop = 0; 
            document.documentElement.scrollTop = 0; 
        }
    </script>
</head>
<body>

    <div class="container">
        <header>
            <h1>Career Advice 📚</h1>
            <p>Your Questions, Answered!</p>
        </header>
        
        <div class="search-bar">
            <input type="text" id="faqSearch" placeholder="Search FAQ..." onkeyup="filterFAQs()">
        </div>

        <section>
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">1. How do I create a profile on the job portal?</div>
                <p class="faq-answer">To create a profile, simply click on the 'Register' button on the homepage, fill in the required information, and submit your details. You'll receive a confirmation email shortly after.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">2. What types of jobs are listed on the portal?</div>
                <p class="faq-answer">We offer a wide range of job listings across various industries, including technology, healthcare, finance, and more. You can use the search feature to find jobs that match your skills and interests.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">3. How can I improve my chances of getting hired?</div>
                <p class="faq-answer">To improve your chances, make sure to create a detailed profile, upload a professional resume, and actively apply for jobs that match your qualifications. Additionally, consider enhancing your skills through online courses.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">4. What should I include in my resume?</div>
                <p class="faq-answer">Your resume should include your contact information, a summary statement, work experience, education, skills, and any relevant certifications. Tailor your resume to each job application to highlight the most relevant information.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">5. Can I receive job alerts?</div>
                <p class="faq-answer">Yes, you can set up job alerts by visiting your profile settings. You'll receive notifications for new job postings that match your criteria.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">6. How do I prepare for a job interview?</div>
                <p class="faq-answer">Research the company, practice common interview questions, dress professionally, and prepare questions to ask the interviewer to demonstrate your interest.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">7. What should I do if I'm not getting responses to my applications?</div>
                <p class="faq-answer">Consider reviewing your resume for clarity and relevance, applying for a wider range of positions, and following up with employers to express continued interest.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">8. Is it better to apply online or through networking?</div>
                <p class="faq-answer">Networking can often lead to better opportunities, but applying online allows you to reach a larger number of companies. A combination of both is usually the most effective approach.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">9. How do I ask for a reference?</div>
                <p class="faq-answer">Reach out to previous employers, colleagues, or professors whom you trust. Explain your situation and ask if they would be willing to provide a reference for your job applications.</p>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">10. What are some good questions to ask in an interview?</div>
                <p class="faq-answer">You can ask about company culture, growth opportunities, team dynamics, and expectations for the role. This shows your interest and helps you assess if the company is the right fit for you.</p>
            </div>
            <!-- Add more questions here as needed -->
        </section>

        <div class="contact-info">
            <h2>Need More Help?</h2>
            <p>If you have additional questions or need further assistance, feel free to <a href="contact_us.php">contact us</a>.</p>
        </div>

        <button id="backToTop" class="back-to-top" onclick="scrollToTop()">Back to Top</button>
    </div>
    
    <script>
        // Optional: Smooth scroll for back to top button
        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

<?php include 'footer.php'; ?>

</body>
</html>
