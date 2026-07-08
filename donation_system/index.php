<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include 'db.php';

// Handle message display from query parameters
$message = '';
$messageType = '';
if (isset($_GET['message']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');
    $messageType = in_array($_GET['type'], ['success', 'danger']) ? $_GET['type'] : 'danger';
}

// Fetch database donations
$databaseDonations = [];
$result = $conn->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 10");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $databaseDonations[] = $row;
    }
}

// Calculate summary totals from database
$approved = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='approved'")->fetch_assoc()['total'] ?? 0;
$pending = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$rejected = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='rejected'")->fetch_assoc()['total'] ?? 0;

// Add proposal donations to totals
if (isset($_SESSION['proposals']) && is_array($_SESSION['proposals'])) {
    foreach ($_SESSION['proposals'] as $prop) {
        $approved += ($prop['status'] ?? 'approved') === 'approved' ? $prop['donated'] : 0;
        $pending += ($prop['status'] ?? 'pending') === 'pending' ? $prop['donated'] : 0;
        $rejected += ($prop['status'] ?? 'rejected') === 'rejected' ? $prop['donated'] : 0;
    }
}

// Set all donations to database donations
$allDonations = $databaseDonations;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Dashboard</title>
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
            font-size: 36px;
        }
        .main h4 {
            color: #000000;
            margin-top: 30px;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .card {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #FFFFFF;
        }
        .bg-approved {
            background-color: #27ae60 !important;
        }
        .bg-pending {
            background-color: #FFB347 !important;
        }
        .bg-rejected {
            background-color: #FF0000 !important;
        }
        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 10px;
        }
        .table {
            background-color: #FFFFFF;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            table-layout: fixed;
        }
        .table thead {
            background-color: #27ae60;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .table th, .table td {
            padding: 10px;
            vertical-align: middle;
            border: 1px solid #ddd;
            color: #000000;
        }
        .table tbody tr:hover {
            background-color: #ecf0f1;
        }
        .table th:nth-child(1), .table td:nth-child(1) { width: 25%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 20%; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 20%; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 25%; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 30%; text-align: center; }
        .btn-edit, .btn-delete {
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.9rem;
            margin-right: 5px;
            text-decoration: none;
            border: none;
            color: #FFFFFF;
        }
        .btn-edit {
            background-color: #27ae60;
        }
        .btn-edit:hover {
            background-color: #219653;
        }
        .btn-delete {
            background-color: #FF0000;
        }
        .btn-delete:hover {
            background-color: #CC0000;
        }
        .alert-success {
            background-color: #27ae60;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
        }
        .alert-danger {
            background-color: #FF0000;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
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
        <h2>Donation Overview</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4"><div class="card text-white bg-approved p-3"><h4>$<?= number_format($approved, 2) ?> Approved</h4></div></div>
            <div class="col-md-4"><div class="card text-white bg-pending p-3"><h4>$<?= number_format($pending, 2) ?> Pending</h4></div></div>
            <div class="col-md-4"><div class="card text-white bg-rejected p-3"><h4>$<?= number_format($rejected, 2) ?> Rejected</h4></div></div>
        </div>

        <h4>Latest Donations</h4>
        <div class="table-container">
            <table class="table table-striped mt-2">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allDonations)): ?>
                        <tr><td colspan="5" class="text-center">No donations found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($allDonations as $donation): ?>
                            <?php
                            try {
                                $date = new DateTime($donation['created_at'], new DateTimeZone('Asia/Kolkata'));
                                $formattedDate = $date->format('d/m/Y H:i');
                            } catch (Exception $e) {
                                $formattedDate = 'Invalid Date';
                            }
                            $donorName = htmlspecialchars($donation['donor_name'], ENT_QUOTES, 'UTF-8');
                            $amount = number_format(floatval($donation['amount']), 2);
                            $status = htmlspecialchars($donation['status'], ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr>
                                <td><?= $donorName ?></td>
                                <td>$<?= $amount ?></td>
                                <td><?= $status ?></td>
                                <td><?= $formattedDate ?></td>
                                <td>
                                    <a href="edit_donation.php?id=<?= urlencode($donation['id']) ?>" class="btn-edit">Edit</a>
                                    <form action="delete.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete <?= addslashes($donorName) ?>\'s donation of $<?= $amount ?>?');">
                                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($donation['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="confirm" value="1">
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>