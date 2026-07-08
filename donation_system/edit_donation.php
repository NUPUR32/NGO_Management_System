<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';

// Initialize variables
$donation_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$donor_name = '';
$amount = '';
$status = '';
$created_at = '';
$error = '';
$success = '';

if ($donation_id) {
    $stmt = $conn->prepare("SELECT donor_name, amount, status, created_at FROM donations WHERE id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $donation = $result->fetch_assoc();
        $donor_name = $donation['donor_name'];
        $amount = $donation['amount'];
        $status = $donation['status'];
        $created_at = $donation['created_at'];
    } else {
        $error = "Donation not found.";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_name = trim($_POST['donor_name']);
    $amount = trim($_POST['amount']);
    $status = trim($_POST['status']);
    $created_at = trim($_POST['created_at']);

    // Validation
    $errors = [];
    if (!preg_match("/^[a-zA-Z\s]{1,100}$/", $donor_name)) {
        $errors[] = "Donor name must be 1-100 characters, letters and spaces only.";
    }
    if (!is_numeric($amount) || $amount <= 0 || !preg_match("/^\d+(\.\d{1,2})?$/", $amount)) {
        $errors[] = "Amount must be a positive number with up to 2 decimal places.";
    }
    if (!in_array($status, ['approved', 'pending', 'rejected'])) {
        $errors[] = "Invalid status.";
    }
    try {
        $date = new DateTime($created_at, new DateTimeZone('Asia/Kolkata'));
    } catch (Exception $e) {
        $errors[] = "Invalid date format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE donations SET donor_name = ?, amount = ?, status = ?, created_at = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $donor_name, $amount, $status, $created_at, $donation_id);
        if ($stmt->execute()) {
            header("Location: index.php?message=Donation updated successfully&type=success");
            exit;
        } else {
            $error = "Failed to update donation.";
        }
        $stmt->close();
    } else {
        $error = "Errors: " . implode(" ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background: #FFF6E7;
            position: relative;
            font-family: Arial, sans-serif;
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
            padding: 30px 20px;
            position: relative;
            z-index: 2;
            transition: width 0.3s ease;
        }
        .sidebar h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
            color: #000000;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: #000000;
            padding: 15px;
            text-decoration: none;
            margin-bottom: 10px;
            background: #FFFFFF;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background: #F0F0F0;
        }
        .sidebar a.logout {
            color: #FF0000;
        }
        .sidebar a.logout:hover {
            background: #FF0000;
            color: #FFFFFF;
        }
        .main {
            flex: 1;
            padding: 30px;
            background: rgba(255, 170, 0, 0.9);
            position: relative;
            z-index: 2;
            border-radius: 10px;
            margin: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .main h2 {
            color: #000000;
            margin-bottom: 25px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            background: #ecf0f1;
            color: #000000;
        }
        .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
        }
        .btn-primary {
            background: #27ae60;
            border: none;
            color: #FFFFFF;
        }
        .btn-primary:hover {
            background: #219653;
        }
        .alert-danger {
            background: #FF0000;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="index.php"><span class="icon">🏠</span> Dashboard</a>
        <a href="add_donation.php"><span class="icon">💸</span> Add Donation</a>
        <a href="manage_donors.php"><span class="icon">👥</span> Manage Donors</a>
        <a href="report.php"><span class="icon">📊</span> Reports</a>
        <a href="terms_and_conditions.php"><span class="icon">📜</span> Terms and Conditions</a>
        <a href="logout.php" class="logout"><span class="icon">🚪</span> Logout</a>
    </div>

    <div class="main">
        <h2>Edit Donation</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($donation_id && empty($error)): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Donor Name</label>
                    <input type="text" name="donor_name" class="form-control" value="<?= htmlspecialchars($donor_name, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="datetime-local" name="created_at" class="form-control" value="<?= (new DateTime($created_at, new DateTimeZone('Asia/Kolkata')))->format('Y-m-d\TH:i') ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Donation</button>
            </form>
        <?php else: ?>
            <p>No donation selected or donation not found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>