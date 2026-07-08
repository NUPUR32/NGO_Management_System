<?php
session_start();
$welcome = "SpacECE India Foundation";
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // Plain text password
    $skills = $_POST['skills'] ?? '';
    $location = $_POST['location'] ?? '';
    $availability = $_POST['availability'] ?? '';
    $experience = $_POST['experience'] ?? '';

    try {
        // Check for existing username
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM volunteers WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username '$username' is already taken. Please choose a different one.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO volunteers (name, email, username, password, skills, location, availability, experience, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([$name, $email, $username, $password, $skills, $location, $availability, $experience]);
            $success = "Application submitted successfully! Use your username and password to sign in.";
        }
    } catch (PDOException $e) {
        $error = "Error submitting application: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer - Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
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
            position: relative;
            padding-bottom: 60px;
        }
        .sidebar:hover ~ .content {
            margin-left: 250px;
        }
        .header {
            background: rgba(255, 170, 0, 0.9);
            color: #000000;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 800;
        }
        .section {
            background: rgba(255, 170, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .section h2 {
            color: #000000;
            margin: 0 0 15px 0;
            font-size: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .section .subheader {
            background: #FFAA00;
            color: #000000;
            padding: 10px;
            margin: -20px -20px 20px -20px;
            border-radius: 10px 10px 0 0;
            font-size: 18px;
            text-align: center;
        }
        .section p {
            color: #000000;
            margin: 5px 0;
            font-size: 14px;
        }
        .section ul {
            color: #000000;
            margin: 5px 0 5px 20px;
            font-size: 14px;
        }
        .apply-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border: 2px solid #219653;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .apply-button:hover {
            background: #219653;
            transform: scale(1.05);
        }
        .apply-button::before {
            content: "👆 Apply Now";
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        .modal .close:hover {
            color: #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            color: #333;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #ecf0f1;
            box-sizing: border-box;
        }
        .form-group button {
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background: #219653;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .signin-link-box {
            text-align: center;
            margin: 20px auto;
            background: #2980b9;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: block;
            width: fit-content;
        }
        .signin-link {
            color: white;
            font-size: 16px;
            text-decoration: none;
            font-weight: bold;
        }
        .signin-link:hover {
            color: #ecf0f1;
            text-decoration: underline;
        }
        .separator {
            border: 0;
            height: 1px;
            background: #000000;
            margin: 20px 0;
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const applyButton = document.querySelector('.apply-button');
            const modal = document.querySelector('.modal');
            const closeButton = document.querySelector('.close');

            applyButton.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'flex';
            });

            closeButton.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Hide modal if success message is present (after submission)
            if (document.querySelector('.message.success')) {
                modal.style.display = 'none';
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/spaceece_logo.jpg" alt="Space ECE Logo" style="max-height: 50px;">
        </div>
        <h2><?php echo $welcome; ?></h2>
        <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="admin.php"><span class="icon">🔐</span><span class="text">Admin</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>SPACƎECE Volunteering</h1>
            <p>Join our mission to make a difference</p>
        </div>
        <a href="#" class="apply-button"></a>
        <div class="modal">
            <div class="modal-content">
                <span class="close">×</span>
                <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
                <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>

                <div class="subheader">Volunteer Application</div>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="skills">Skills (e.g., coding, design):</label>
                        <input type="text" id="skills" name="skills" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability (e.g., weekends, 10 hours/week):</label>
                        <input type="text" id="availability" name="availability" required>
                    </div>
                    <div class="form-group">
                        <label for="experience">Experience (brief description):</label>
                        <textarea id="experience" name="experience" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit_application">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="section">
            <div class="subheader">Benefits of Volunteering</div>
            <p>Some of the benefits of volunteering in early childhood education at SpaceECE India Foundation include:</p>
            <ul>
                <li>Developing skills and experience: Volunteering in early childhood education can help you to develop a range of skills and gain valuable experience that can be beneficial for your personal and professional growth.</li>
                <li>Making a difference in the lives of young children: Volunteering in early childhood education can be a fulfilling experience as you work to support the development and education of young children.</li>
                <li>Building connections with the community: Volunteering at SpaceECE India Foundation can help you to build connections with the community and meet new people who share your interests and values.</li>
                <li>Contributing to a meaningful cause: By volunteering at SpaceECE India Foundation, you can contribute to a meaningful cause and make a positive impact in the world.</li>
            </ul>
            <p>Overall, volunteering at SpaceECE India Foundation in the field of early childhood education can be a valuable and rewarding experience that can help you to develop skills, make a difference in the lives of young children, build connections, and contribute to a meaningful cause.</p>
        </div>
        <div class="section">
            <div class="subheader">About Volunteering at SpaceECE</div>
            <p>Volunteering at SPACEƎCE India Foundation in the field of early childhood education can be a rewarding experience. As a volunteer, you will have the opportunity to make a positive impact in the lives of young children and contribute to their development and education.</p>
            <p>Working in early childhood education at SPACEƎCE India Foundation may involve a range of activities, including helping to plan and run educational programs and activities, assisting with the development of educational resources and materials, and providing support to all organizational functions.</p>
            <p>Volunteering at SPACEƎCE India Foundation in the field of early childhood education can be a valuable and rewarding experience that can help you to develop skills, make a difference in the lives of young children, build connections, and contribute to a meaningful cause.</p>
        </div>
        <div class="section">
            <div class="subheader">Responsibilities of Regular Volunteers</div>
            <ol>
                <li>Conducting regular activities with children or youth: Volunteers may be expected to conduct regular educational or recreational activities with children or youth, such as tutoring, mentoring, or coaching.</li>
                <li>Assisting with administrative tasks: Volunteers may be asked to help with administrative tasks, such as data entry, filing, or answering phone calls.</li>
                <li>Supporting program staff: Volunteers may be required to support program staff in various ways, such as providing assistance with program design, implementation, or evaluation.</li>
                <li>Engaging with the community: Volunteers may be asked to engage with the community to promote the organization's mission and goals.</li>
                <li>Fundraising: Volunteers may be asked to help raise funds for the organization by organizing events, writing grant proposals, or soliciting donations.</li>
                <li>Assisting with marketing and communication: Volunteers may be required to assist with marketing and communication tasks, such as creating social media posts, designing flyers, or drafting press releases.</li>
                <li>Providing support to other volunteers: Volunteers may be asked to provide support to other volunteers, such as helping them with training or mentoring.</li>
            </ol>
        </div>
        <div class="section">
            <div class="subheader">Expectations from the Volunteers</div>
            <ol>
                <li>Participating in regular training sessions: Volunteers may be required to attend regular training sessions to enhance their skills and knowledge.</li>
                <li>Participating in meetings: Volunteers may be expected to participate in meetings with program staff, board members, or other volunteers.</li>
                <li>Completing reports: Volunteers may be asked to complete reports documenting their activities, achievements, and challenges.</li>
            </ol>
        </div>
        <hr class="separator">
        <div class="signin-link-box">
            <a href="volunteer_signin.php" class="signin-link">Already a volunteer? Sign in</a>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>