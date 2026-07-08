<?php
session_start();

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $schedule = $_POST['schedule'] ?? '';
    $location = $_POST['location'] ?? '';
    $motivation = $_POST['motivation'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO volunteer_applications (name, email, phone, skills, schedule, location, motivation) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $skills, $schedule, $location, $motivation]);
        $success = "Application stored successfully!";
    } catch (PDOException $e) {
        $error = "Error storing application: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Volunteer Application - Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
            overflow-x: hidden;
        }
        .content {
            margin-left: 60px;
            padding: 20px;
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
        .form-box {
            background: rgba(255, 170, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .form-box h2 {
            color: #000000;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            text-align: center;
        }
        .form-box form {
            display: flex;
            flex-direction: column;
        }
        .form-box label {
            margin-bottom: 5px;
            color: #000000;
            font-size: 14px;
        }
        .form-box input, .form-box textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #ecf0f1;
        }
        .form-box button {
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-box button:hover {
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
    <div class="content">
        <div class="header">
            <h1>Store Volunteer Application</h1>
            <p>Manually enter volunteer application data</p>
        </div>
        <div class="form-box">
            <h2>Volunteer Application Form</h2>
            <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
            <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
            <form method="post" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone">
                <label for="skills">Skills:</label>
                <input type="text" id="skills" name="skills" required>
                <label for="schedule">Schedule Availability:</label>
                <input type="text" id="schedule" name="schedule">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location">
                <label for="motivation">Motivation:</label>
                <textarea id="motivation" name="motivation" rows="5"></textarea>
                <button type="submit">Store Application</button>
            </form>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>