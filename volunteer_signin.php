<?php
ob_start(); // Start output buffering
session_start();
$welcome = "SpacECE India Foundation";

include 'db_connect.php';

// Default credentials
$default_username = "Akashjindal";
$default_password = "Akash@12345";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        // Test database connection
        $pdo->query("SELECT 1");

        // Check against default credentials
        if ($username === $default_username && $password === $default_password) {
            $_SESSION['volunteer_id'] = 1;
            $_SESSION['volunteer_name'] = "Akash Jindal";
            header("Location: volunteer_dashboard.php");
            ob_end_flush();
            exit;
        } else {
            // Check against database
            $stmt = $pdo->prepare("SELECT id, name, password FROM volunteers WHERE username = ?");
            $stmt->execute([$username]);
            $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($volunteer && $password === $volunteer['password']) {
                $_SESSION['volunteer_id'] = $volunteer['id'];
                $_SESSION['volunteer_name'] = $volunteer['name'];
                header("Location: volunteer_dashboard.php");
                ob_end_flush();
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
    } catch (PDOException $e) {
        $error = "Error during login: Database connection failed.";
        error_log("Login error: " . $e->getMessage(), 3, 'errors.log');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Sign In - Space ECE</title>
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
        .signin-box {
            background: rgba(255, 170, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        .signin-box h2 {
            color: #000000;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            text-align: center;
        }
        .signin-box p {
            color: #000000;
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #000000;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #ecf0f1;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .form-group button:hover {
            background: #219653;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
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
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
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
            <h1>Volunteer Sign In - Space ECE</h1>
            <p>Access your volunteer account</p>
        </div>
        <div class="signin-box">
            <h2>Sign In</h2>
            <?php if (isset($error)) echo "<div class='message error'>" . htmlspecialchars($error) . "</div>"; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="password-toggle" onclick="togglePassword()">👁️</span>
                </div>
                <div class="form-group">
                    <button type="submit">Sign In</button>
                </div>
            </form>
            <a href="volunteer.php" class="back-link">Back to Volunteer Page</a>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
<?php ob_end_flush(); ?>