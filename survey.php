<?php
session_start();
$welcome = "SpacECE India Foundation";

include 'db_connect.php';

// Handle survey form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_survey'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');

    if (!empty($name) && !empty($email) && !empty($feedback)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (empty($latitude) || empty($longitude)) {
            $error = "Please capture your GPS location before submitting.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO survey_submissions (name, email, feedback, latitude, longitude, submission_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $feedback, $latitude, $longitude]);
                $displaySuccess = true;
                // Debug: Confirm insertion
                echo "<!-- Survey submitted: Name=$name, Email=$email, Feedback=$feedback, Latitude=$latitude, Longitude=$longitude -->";
            } catch (PDOException $e) {
                $error = "Error submitting feedback: " . $e->getMessage();
                // Debug: Show error
                echo "<!-- Error: $error -->";
            }
        }
    } else {
        $error = "Please fill in all fields and capture GPS location.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey - Space ECE</title>
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
        .survey-box {
            background: rgba(255, 170, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .survey-box h2 {
            color: #000000;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .survey-box p {
            color: #000000;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .survey-box form {
            display: flex;
            flex-direction: column;
        }
        .survey-box label {
            margin-bottom: 5px;
            color: #000000;
            font-size: 14px;
        }
        .survey-box input,
        .survey-box textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #ecf0f1;
        }
        .survey-box button {
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .survey-box button:hover {
            background: #219653;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
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
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById("latitude").value = position.coords.latitude.toFixed(8);
                        document.getElementById("longitude").value = position.coords.longitude.toFixed(8);
                        document.getElementById("gps-status").innerText = "GPS location captured successfully!";
                        document.getElementById("gps-error").innerText = "";
                    },
                    (error) => {
                        document.getElementById("gps-error").innerText = "Error capturing GPS: " + error.message;
                        document.getElementById("gps-status").innerText = "";
                    }
                );
            } else {
                document.getElementById("gps-error").innerText = "Geolocation is not supported by this browser.";
            }
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/spaceece_logo.jpg" alt="Space ECE Logo" style="max-height: 50px;">
        </div>
        <h2><?php echo htmlspecialchars($welcome); ?></h2>
        <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="admin.php"><span class="icon">🔐</span><span class="text">Admin</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>Survey - Space ECE</h1>
        </div>
        <div class="survey-box">
            <h2>Share Your Feedback</h2>
            <p>Help us improve by sharing your thoughts and suggestions. Please allow location access to tag your feedback with GPS coordinates.</p>
            <?php if (isset($displaySuccess) && $displaySuccess): ?>
                <div class="message success">Thank you for your feedback!</div>
            <?php elseif (isset($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="submit_survey" value="1">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <label for="feedback">Feedback:</label>
                <textarea id="feedback" name="feedback" rows="5" required><?php echo htmlspecialchars($feedback ?? ''); ?></textarea>
                <label for="latitude">Latitude:</label>
                <input type="text" id="latitude" name="latitude" readonly>
                <label for="longitude">Longitude:</label>
                <input type="text" id="longitude" name="longitude" readonly>
                <button type="button" onclick="getLocation()">Capture GPS Location</button>
                <div id="gps-status" class="message success" style="display: none;"></div>
                <div id="gps-error" class="message error" style="display: none;"></div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>