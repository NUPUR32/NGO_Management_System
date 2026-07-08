<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = '';
$proposal = '';
$imagePath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_proposal'])) {
    $heading = trim($_POST['heading'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $image = $_FILES['proposal_image'] ?? null;

    if (empty($heading) || empty($details) || empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $message = "All fields are required, and amount must be a positive number.";
    } else {
        $proposal_id = uniqid();
        $imagePath = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/proposals/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imagePath = $uploadDir . uniqid() . '_' . basename($image['name']);
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        $stmt = $conn->prepare("INSERT INTO proposals (proposal_id, heading, details, amount, image_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("sssds", $proposal_id, $heading, $details, $amount, $imagePath);
            if ($stmt->execute()) {
                $message = "Proposal generated successfully!";
                $proposal = ['proposal_id' => $proposal_id, 'heading' => $heading, 'details' => $details, 'amount' => $amount, 'image' => $imagePath];
            } else {
                $message = "Failed to generate proposal. Error: " . $conn->error;
            }
            $stmt->close();
        } else {
            $message = "Database preparation failed. Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Generator</title>
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
        .proposal-preview {
            margin-top: 20px;
            padding: 15px;
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .proposal-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
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
    <a href="proposal_generator.php"><span class="icon">📝</span> Proposal Generator</a>
    <a href="logout.php" class="logout"><span class="icon">🚪</span> Logout</a>
</div>

<div class="main">
    <h3>Generate Proposal</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?= strpos($message, 'successfully') !== false ? 'success' : 'danger' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-2">
            <input name="heading" class="form-control" placeholder="Proposal Heading" required>
        </div>
        <div class="mb-2">
            <textarea name="details" class="form-control" placeholder="Proposal Details" rows="4" required></textarea>
        </div>
        <div class="mb-2">
            <input name="amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="Amount Required" required>
        </div>
        <div class="mb-2">
            <input type="file" name="proposal_image" class="form-control" accept="image/*">
        </div>
        <button name="generate_proposal" class="btn btn-primary">Generate Proposal</button>
    </form>
    <?php if ($proposal): ?>
        <div class="proposal-preview">
            <h4><?= htmlspecialchars($proposal['heading']) ?></h4>
            <p><?= nl2br(htmlspecialchars($proposal['details'])) ?></p>
            <p><strong>Amount Required:</strong> $<?= number_format($proposal['amount'], 2) ?></p>
            <?php if ($proposal['image']): ?>
                <img src="<?= htmlspecialchars($proposal['image']) ?>" alt="Proposal Image" class="proposal-image">
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>