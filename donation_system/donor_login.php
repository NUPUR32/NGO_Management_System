<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (empty($password)) {
        $error = "Please enter a password!";
    } else {
        // Check if email exists in donors table
        $stmt = $conn->prepare("SELECT * FROM donors WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Email and password match, set session and redirect
            $donor = $result->fetch_assoc();
            $_SESSION['donor'] = $donor['donor_name'];
            header("Location: donor_dashboard.php");
            exit();
        } else {
            // Check if email exists but password is incorrect
            $stmt = $conn->prepare("SELECT * FROM donors WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $error = "Incorrect password!";
            } else {
                // Email doesn't exist, create new donor
                $donor_name = substr($email, 0, strpos($email, '@')) . '_' . time(); // Create unique donor name
                $stmt = $conn->prepare("INSERT INTO donors (donor_name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $donor_name, $email, $password);
                if ($stmt->execute()) {
                    $_SESSION['donor'] = $donor_name;
                    $success = "Welcome! Your donor account has been created.";
                    header("Location: donor_dashboard.php");
                    exit();
                } else {
                    $error = "Failed to create donor account. Please try again.";
                }
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Login - Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #FFF6E7; /* Light peach background from Space ECE */
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1); /* Light overlay for contrast */
            z-index: 1;
        }
        .login-container {
            width: 400px;
            padding: 2.5rem 2rem;
            background: rgba(255, 170, 0, 0.9); /* Orange from Space ECE */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .login-container h2 {
            color: #000000;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .login-container h5 {
            color: #000000;
            font-family: Arial, sans-serif;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 25px;
            border: 1px solid #ddd;
            background-color: #ecf0f1; /* Light gray from Space ECE */
            color: #000000;
            padding: 10px;
        }
        .form-control:focus {
            border-color: #27ae60; /* Green border on focus */
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
        }
        .btn-login {
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 1.1rem;
            background-color: #27ae60; /* Green from Space ECE */
            border: none;
            color: #FFFFFF;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #219653; /* Darker green */
        }
        .alert-danger {
            background-color: #FFB347; /* Orange from dashboard for pending */
            color: #000000;
            border-radius: 15px;
            border: none;
        }
        .alert-success {
            background-color: #27ae60; /* Green from Space ECE */
            color: #FFFFFF;
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Donor Login</h2>
    <h5>Donate and Track Your Contributions</h5>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Enter your password" required>
        <button type="submit" name="login" class="btn btn-login w-100">Login or Register</button>
    </form>
</div>

</body>
</html>