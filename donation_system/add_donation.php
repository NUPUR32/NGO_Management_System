<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$errors = [];
$success = '';

if (isset($_POST['submit'])) {
    $donor_name = trim($_POST['donor_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'Cash');

    // Server-side validation
    if (empty($donor_name)) {
        $errors['donor_name'] = "Donor name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]{1,100}$/', $donor_name)) {
        $errors['donor_name'] = "Donor name must be letters and spaces, up to 100 characters.";
    }
    if (empty($amount)) {
        $errors['amount'] = "Amount is required.";
    } elseif (!is_numeric($amount) || $amount <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
        $errors['amount'] = "Amount must be a positive number with up to 2 decimal places.";
    }
    if (!in_array($status, ['approved', 'pending', 'rejected'])) {
        $errors['status'] = "Invalid status selected.";
    }

    if (empty($errors)) {
        // Mock payment method storage (no DB change)
        $_SESSION['last_donation'] = [
            'donor_name' => $donor_name,
            'amount' => $amount,
            'status' => $status,
            'payment_method' => $payment_method,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $stmt = $conn->prepare("INSERT INTO donations (donor_name, amount, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $donor_name, $amount, $status);
        if ($stmt->execute()) {
            $success = "Donation added successfully via " . htmlspecialchars($payment_method) . " at " . date('h:i A T') . "!";
        } else {
            $errors['general'] = "Failed to add donation: " . $conn->error;
            error_log("Donation insertion failed: " . $conn->error);
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
    <title>Add Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 20px;
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
            padding: 20px;
            background: rgba(255, 170, 0, 0.9);
            position: relative;
            z-index: 2;
            border-radius: 10px;
            margin: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .main h3 {
            color: #000000;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 24px;
            text-align: center;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #ecf0f1;
            color: #000000;
            padding: 10px;
            margin-bottom: 0.5rem;
        }
        .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
        }
        .btn-primary {
            border-radius: 5px;
            padding: 10px 20px;
            background-color: #27ae60;
            border: none;
            color: #FFFFFF;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #219653;
        }
        .alert-success {
            background-color: #27ae60;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
            margin-top: 1rem;
            text-align: center;
        }
        .alert-danger {
            background-color: #FF0000;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
            margin-top: 1rem;
            text-align: center;
        }
        .invalid-feedback {
            color: #FF0000;
            font-size: 0.9rem;
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
    <a href="proposal_generator.php"><span class="icon">📝</span> Proposal Generator</a>
    <a href="logout.php" class="logout"><span class="icon">🚪</span> Logout</a>
</div>

<div class="main">
    <h3>Add New Donation</h3>
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-2">
            <input name="donor_name" class="form-control" placeholder="Donor Name" value="<?= isset($_POST['donor_name']) ? htmlspecialchars($_POST['donor_name']) : '' ?>" required>
            <?php if (!empty($errors['donor_name'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['donor_name']) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <input name="amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="Amount" value="<?= isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '' ?>" required>
            <?php if (!empty($errors['amount'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['amount']) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <select name="status" class="form-control">
                <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= (isset($_POST['status']) && $_POST['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= (isset($_POST['status']) && $_POST['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
            <?php if (!empty($errors['status'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['status']) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <select name="payment_method" class="form-control">
                <option value="Cash" <?= (!isset($_POST['payment_method']) || $_POST['payment_method'] == 'Cash') ? 'selected' : '' ?>>Cash</option>
                <option value="PayPal" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal') ? 'selected' : '' ?>>PayPal</option>
                <option value="Stripe" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Stripe') ? 'selected' : '' ?>>Stripe</option>
            </select>
        </div>
        <button name="submit" class="btn btn-primary">Add Donation</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>