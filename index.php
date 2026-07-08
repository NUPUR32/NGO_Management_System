<?php
session_start();
$welcome = "Welcome, Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
            color: #000000;
            overflow-x: hidden;
        }
        .sidebar {
            width: 60px;
            background: #FFFFFF;
            color: #000000;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            transition: width 0.3s ease;
            overflow: hidden;
        }
        .sidebar:hover {
            width: 250px;
        }
        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar .logo img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .sidebar:hover .logo img {
            opacity: 1;
        }
        .sidebar h2 {
            font-size: 14px;
            text-align: center;
            margin: 0 0 20px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: #000000;
            padding: 15px 20px;
            text-decoration: none;
            margin-bottom: 10px;
            background: #FFFFFF;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar a:hover {
            background: #F0F0F0;
        }
        .sidebar a .icon {
            margin-right: 10px;
            opacity: 1;
            flex-shrink: 0;
        }
        .sidebar a .text {
            margin-left: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar:hover a .text {
            opacity: 1;
        }
        .content {
            margin-left: 60px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover ~ .content {
            margin-left: 250px;
        }
        .header {
            color: #000000;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
        }
        .header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 60px;
            font-weight: 800;
        }
        .box {
            background: rgba(255, 170, 0, 0.9);
            color: #000000;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .ngo-title {
            font-size: 36px;
            font-weight: bold;
            color: #000000;
            text-align: center;
            margin: 20px 0;
        }
        .combined-box {
            width: 100%;
            margin: 20px 0;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .combined-box .blue-section {
            background: #FFAA00;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #000000;
            font-size: 24px;
            font-weight: bold;
        }
        .combined-box .white-section {
            background: white;
            min-height: 550px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px;
            flex-wrap: wrap;
        }
        .location-box {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
            width: 30%;
            min-width: 250px;
        }
        .location-box h3 {
            margin: 0 0 10px 0;
            color: #000000;
            font-size: 16px;
        }
        .location-box p {
            margin: 5px 0;
            color: #000000;
            font-size: 14px;
        }
        .location-box .map {
            width: 100%;
            height: 250px;
            border: 0;
            border-radius: 5px;
            margin-top: 10px;
        }
        .contact-link {
            text-align: center;
            margin: 20px 0;
        }
        .contact-link a {
            color: #000000;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .contact-link a:hover {
            text-decoration: underline;
        }
        .contact-link a svg {
            margin-right: 10px;
        }
        .connect-section {
            background: #FFAA00;
            color: #FFFFFF;
            padding: 10px 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
        }
        .connect-section h3 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #FFFFFF;
        }
        .connect-section .line {
            position: absolute;
            top: 50%;
            left: 10px;
            width: 20px;
            height: 2px;
            background: red;
            transform: translateY(-50%);
        }
        .connect-section .social-icons a {
            color: #FFFFFF;
            margin: 0 10px;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }
        .connect-section .social-icons a:hover {
            opacity: 0.7;
        }
        footer {
            background: rgba(255, 170, 0, 0.9);
            color: #000000;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/spaceece_logo.jpg" alt="Space ECE Logo" style="max-height: 50px;">
        </div>
        <h2><?php echo "SpacECE India foundation"; ?></h2>
        <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="login.php"><span class="icon">🔐</span><span class="text">Admin</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>SpaceECE India Foundation</h1>
            <p>Supporting Family Growth & Child Development</p>
        </div>
        <div class="box">
            <p>Space ECE India Foundation is a non-profit organization focused on transforming early childhood education by providing innovative learning opportunities, particularly for underserved communities in India. Founded in 2018, it emphasizes the critical early years (0-8 years) as the foundation for lifelong learning. The organization believes in empowering children, parents, and educators through accessible and engaging educational programs.Space ECE is dedicate We aim to empower families with resources and opportunities to thrive.</p>
        </div>
        <div class="ngo-title">NGO Management System</div>
        <div class="combined-box">
            <div class="blue-section">Locate us</div>
            <div class="white-section">
                <div class="location-box">
                    <h3>Office</h3>
                    <p>C1-602, Chandralok Nagari,</p>
                    <p>Ganesh Nagar, Dhayari, Pune.</p>
                    <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15175.969723618753!2d73.8080869!3d18.452169!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc29511ea741a71%3A0xbc6b4d3ee77b22ac!2sSitaee+Nagar%2C+Dhayari%2C+Pune%2C+Maharashtra+411041%2C+India!5e0!3m2!1sen!2sus!4v1624356789012!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="location-box">
                    <h3>Field: Urban Slum</h3>
                    <p>Gosavi Wasti,</p>
                    <p>Karve Nagar, personally identifiable information redacted</p>
                    <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15176.850169897467!2d73.814408!3d18.494244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1624356789012!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="location-box">
                    <h3>Field: Rural</h3>
                    <p>ShriramNagar,</p>
                    <p>Khed Shivapur, Pune</p>
                    <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3785.9873616732086!2d73.8418403!3d18.3439225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2ed2a990f025f%3A0xecbc10b5dd47e2da!2sKhed%2C+Maharashtra+412205!5e0!3m2!1sen!2sus!4v1624356789012!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <div class="contact-link">
            <a href="contact.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 22.621l-3.521-6.795c-.008.004-1.974.97-2.064 1.011-2.24 1.086-6.799-7.82-4.609-8.994l2.083-1.026-3.493-6.817-2.106 1.039c-7.202 3.755 4.233 25.982 11.6 22.615.121-.055 2.102-1.029 2.11-1.033z"/></svg>
                Contact Us
            </a>
        </div>
        <div class="connect-section">
            <div class="line"></div>
            <h3>Connect With Us</h3>
            <div class="social-icons">
                <a href="https://www.facebook.com/SpacECE/" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                </a>
                <a href="https://x.com/ece_spac" aria-label="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-.139 9.237c.209 4.617-3.234 9.765-9.33 9.765-1.854 0-3.579-.543-5.032-1.475 1.742.205 3.48-.278 4.86-1.359-1.437-.027-2.649-.976-3.066-2.28.515.098 1.021.069 1.482-.056-1.579-.317-2.668-1.739-2.633-3.26.442.246.949.394 1.486.411-1.461-.977-1.875-2.907-1.016-4.383 1.619 1.986 4.038 3.293 6.766 3.43-.479-2.053 1.08-4.03 3.199-4.03.943 0 1.797.398 2.395 1.037.748-.147 1.451-.42 2.086-.796-.246.767-.766 1.41-1.443 1.816.664-.08 1.297-.256 1.885-.517-.439.656-.996 1.234-1.639 1.697z"/></svg>
                </a>
                <a href="https://www.instagram.com/spac.ece/" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M14.829 6.302c-.738-.034-.96-.04-2.829-.04s-2.09.007-2.828.04c-1.899.087-2.783.986-2.87 2.87-.033.738-.041.959-.041 2.828s.008 2.09.041 2.829c.087 1.879.967 2.783 2.87 2.87.737.033.959.041 2.828.041 1.87 0 2.091-.007 2.829-.041 1.899-.086 2.782-.988 2.87-2.87.033-.738.04-.96.04-2.829s-.007-2.09-.04-2.828c-.088-1.883-.973-2.783-2.87-2.87zm-2.829 9.293c-1.985 0-3.595-1.609-3.595-3.595 0-1.985 1.61-3.594 3.595-3.594s3.595 1.609 3.595 3.594c0 1.985-1.61 3.595-3.595 3.595zm3.737-6.491c-.464 0-.84-.376-.84-.84 0-.464.376-.84.84-.84.464 0 .84.376.84.84 0 .463-.376.84-.84.84zm-1.404 2.896c0 1.289-1.045 2.333-2.333 2.333s-2.333-1.044-2.333-2.333c0-1.289 1.045-2.333 2.333-2.333s2.333 1.044 2.333 2.333zm-2.333-12c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6.958 14.886c-.115 2.545-1.532 3.955-4.071 4.072-.747.034-.986.042-2.887.042s-2.139-.008-2.886-.042c-2.544-.117-3.955-1.529-4.072-4.072-.034-.746-.042-.985-.042-2.886 0-1.901.008-2.139.042-2.886.117-2.544 1.529-3.955 4.072-4.071.747-.035.985-.043 2.886-.043s2.14.008 2.887.043c2.545.117 3.957 1.532 4.071 4.071.034.747.042.985.042 2.886 0 1.901-.008 2.14-.042 2.886z"/></svg>
                </a>
                <a href="https://www.youtube.com/channel/UC8ZUYAoz4vsgHPWIiTCL_dA" aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.441 16.892c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-6.441-7.234l4.917 2.338-4.917 2.346v-4.684z"/></svg>
                </a>
                <a href="https://wa.me/919096305648" aria-label="WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 1.856.001 3.598.723 4.907 2.034 1.31 1.311 2.031 3.054 2.030 4.908-.001 3.825-3.113 6.938-6.937 6.938z"/></svg>
                </a>
            </div>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>