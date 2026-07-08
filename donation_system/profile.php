<?php
session_start();
include 'db.php';

// Check if donor is logged in
if (!isset($_SESSION['donor'])) {
    header("Location: donor_login.php");
    exit();
}

$donor_name = $_SESSION['donor'];
$error = '';
$success = '';

// Fetch donor details
$stmt = $conn->prepare("SELECT donor_name, email, password FROM donors WHERE donor_name = ?");
$stmt->bind_param("s", $donor_name);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $donor = $result->fetch_assoc();
    $current_email = $donor['email'];
    $current_password = $donor['password'];
} else {
    $error = "Unable to fetch profile details.";
}
$stmt->close();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $new_donor_name = trim($_POST['donor_name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    // Validate inputs
    if (empty($new_donor_name) || empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid name and email.";
    } else {
        // Check if email is already used by another donor
        $stmt = $conn->prepare("SELECT donor_name FROM donors WHERE email = ? AND donor_name != ?");
        $stmt->bind_param("ss", $new_email, $donor_name);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email is already in use by another donor.";
        } else {
            // Update donor details
            $stmt = $conn->prepare("UPDATE donors SET donor_name = ?, email = ?, password = ? WHERE donor_name = ?");
            $stmt->bind_param("ssss", $new_donor_name, $new_email, $new_password, $donor_name);
            if ($stmt->execute()) {
                $_SESSION['donor'] = $new_donor_name; // Update session
                $success = "Profile updated successfully!";
                $donor_name = $new_donor_name;
                $current_email = $new_email;
                $current_password = $new_password;
            } else {
                $error = "Failed to update profile. Please try again.";
            }
            $stmt->close();
        }
    }
}

// Set active section
if (!isset($_SESSION['active_section'])) {
    $_SESSION['active_section'] = 'donor_dashboard';
}
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'profile.php') {
    $_SESSION['active_section'] = 'profile';
} elseif ($current_page === 'goals.php') {
    $_SESSION['active_section'] = 'goals';
} elseif ($current_page === 'feedback.php') {
    $_SESSION['active_section'] = 'feedback';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile - Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #FFF6E7;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .sidebar {
            width: 250px;
            background: #FFFFFF;
            color: #000000;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding: 20px 0;
            z-index: 3;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            display: block;
            max-width: 80%;
            margin: 0 auto 20px;
        }

        .sidebar h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: #000000;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li:hover,
        .sidebar ul li.active {
            background: #F0F0F0;
        }

        .sidebar ul li span.icon {
            margin-right: 10px;
        }

        .sidebar ul li a {
            color: #000000;
            text-decoration: none;
            display: block;
        }

        .sidebar ul li a:hover {
            background: #FF0000;
            color: #FFFFFF;
        }

        .sidebar ul li a.logout {
            color: #FF0000;
        }

        .sidebar ul li a.logout:hover {
            background: #FF0000;
            color: #FFFFFF;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);

            padding: 30px;
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }

        .content-box {
            background: rgba(255, 170, 0, 0.9);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in;
            margin-bottom: 30px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .content-box h3 {
            color: #000000;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            background: #ecf0f1;
            color: #000000;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
        }

        .btn-update {
            border-radius: 5px;
            background-color: #27ae60;
            border: none;
            color: #FFFFFF;
            transition: background-color 0.3s ease;
        }

        .btn-update:hover {
            background-color: #219653;
        }

        .alert-danger {
            background: #FF0000;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
        }

        .alert-success {
            background: #27ae60;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
        }

        .form-label {
            color: #000000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="sidebar">
            <img src="uploads/image.jpg" alt="SpaceECE Logo" class="logo">
            <h3>Donor Panel</h3>
            <ul>
                <li class="<?php echo $_SESSION['active_section'] === 'donor_dashboard' ? 'active' : ''; ?>">
                    <a href="donor_dashboard.php">
                        <span class="icon">🏠</span> Dashboard
                    </a>
                </li>
                <li class="<?php echo $_SESSION['active_section'] === 'goals' ? 'active' : ''; ?>">
                    <a href="goals.php">
                        <span class="icon">📈</span> Goals
                    </a>
                </li>
                <li class="<?php echo $_SESSION['active_section'] === 'feedback' ? 'active' : ''; ?>">
                    <a href="feedback.php">
                        <span class="icon">💬</span> Feedback
                    </a>
                </li>
                <li class="<?php echo $_SESSION['active_section'] === 'profile' ? 'active' : ''; ?>">
                    <a href="profile.php">
                        <span class="icon">👤</span> Profile
                    </a>
                </li>
                 <li>
                <a href="proposals_available.php">
                    <span class="icon">📝</span> Proposals Available
                </a>
            </li>
                <li>
                    <a href="logout.php" class="logout">
                        <span class="icon">🚪</span> Logout
                    </a>
                </li>
            </ul>
        </div>
        <div class="main-content">
            <div class="content-box">
                <h3>Donor Profile</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="donor_name" class="form-label">Donor Name</label>
                        <input type="text" name="donor_name" id="donor_name" class="form-control" value="<?php echo htmlspecialchars($donor_name, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($current_email, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" value="<?php echo htmlspecialchars($current_password, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-update w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>