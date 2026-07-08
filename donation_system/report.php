<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Existing queries
$total = $conn->query("SELECT SUM(amount) as total FROM donations")->fetch_assoc()['total'] ?? 0;
$count = $conn->query("SELECT COUNT(*) as total FROM donations")->fetch_assoc()['total'] ?? 0;

// Donation analysis queries
$status_breakdown = $conn->query("SELECT status, SUM(amount) as total, COUNT(*) as count FROM donations GROUP BY status");
$avg_amount = $conn->query("SELECT AVG(amount) as avg_amount FROM donations")->fetch_assoc()['avg_amount'] ?? 0;
// Use LOWER(donor_name) for case-insensitive grouping
$top_donor = $conn->query("SELECT donor_name, SUM(amount) as total FROM donations GROUP BY LOWER(donor_name) ORDER BY total DESC LIMIT 1")->fetch_assoc();

// Additional queries for collapsible details
$status_details = [];
$statuses = ['approved', 'pending', 'rejected'];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT donor_name, amount, created_at FROM donations WHERE status = ? ORDER BY amount DESC LIMIT 3");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $status_details[$status] = [];
    while ($row = $result->fetch_assoc()) {
        $status_details[$status][] = $row;
    }
    $stmt->close();
}
$min_max = $conn->query("SELECT MIN(amount) as min_amount, MAX(amount) as max_amount FROM donations")->fetch_assoc();
$top_donor_details = [];
if ($top_donor && !empty($top_donor['donor_name'])) {
    $stmt = $conn->prepare("SELECT amount, status, created_at FROM donations WHERE donor_name = ? ORDER BY created_at DESC LIMIT 3");
    $stmt->bind_param("s", $top_donor['donor_name']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $top_donor_details[] = $row;
    }
    $stmt->close();
}

// Feedback query
$feedback_result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
$total_feedback = $feedback_result->num_rows;
$starred_count = 0;
if ($total_feedback > 0) {
    $starred_count = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE starred = 1")->fetch_assoc()['count'] ?? 0;
}
$starred_percentage = $total_feedback > 0 ? ($starred_count / $total_feedback * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <!-- Fixed Bootstrap CSS link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        .main h3, .main h4 {
            color: #000000;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            text-align: center;
        }
        .alert-info {
            background-color: #27ae60;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
            margin-top: 1rem;
            text-align: center;
        }
        .alert-info a {
            color: #FFFFFF;
            text-decoration: underline;
        }
        .alert-info a:hover {
            color: #F0F0F0;
        }
        .card {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            color: #FFFFFF;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-header {
            border-radius: 10px 10px 0 0;
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #FFFFFF;
        }
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .card-body {
            padding: 15px;
            color: #000000;
            background: #FFFFFF;
            border-radius: 0 0 10px 10px;
        }
        .card-body p, .card-body ul {
            margin: 0;
            line-height: 1.6;
        }
        .card-body ul {
            padding-left: 20px;
        }
        .card-body li {
            margin-bottom: 10px;
        }
        .bg-approved { background-color: #27ae60 !important; }
        .bg-pending { background-color: #FFB347 !important; }
        .bg-rejected { background-color: #FF0000 !important; }
        .bg-average { background-color: #219653 !important; }
        .bg-top-donor { background-color: #2ecc71 !important; }
        .bg-feedback { background-color: #27ae60 !important; }
        .bi-chevron-right {
            transition: transform 0.3s ease;
        }
        .bi-chevron-right.rotate-90 {
            transform: rotate(90deg);
        }
        .feedback-item.starred {
            border-left: 5px solid #FFD700;
        }
        .feedback-item .star-icon {
            color: #FFD700;
            margin-right: 5px;
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
    <h3>Donation Reports</h3>
    <div class="alert alert-info">
        <strong>Total Donations:</strong> $<?= number_format($total, 2) ?><br>
        <strong>Number of Donations:</strong> <?= htmlspecialchars($count, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <h4 class="mt-4">Donation Analysis</h4>
    <?php if ($count > 0): ?>
        <div class="row" id="donationAnalysisAccordion">
            <?php while ($row = $status_breakdown->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card bg-<?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-header" id="heading<?= ucfirst($row['status']) ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?= ucfirst($row['status']) ?>" aria-expanded="false" aria-controls="collapse<?= ucfirst($row['status']) ?>">
                            <h5>Total Amount of <?= ucfirst(htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8')) ?> Donations <?= $row['status'] === 'pending' ? 'Awaiting Review' : ($row['status'] === 'rejected' ? 'Declined' : 'Received') ?></h5>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <div id="collapse<?= ucfirst($row['status']) ?>" class="collapse" aria-labelledby="heading<?= ucfirst($row['status']) ?>" data-bs-parent="#donationAnalysisAccordion">
                            <div class="card-body">
                                <p>$<?= number_format($row['total'], 2) ?> (<?= htmlspecialchars($row['count'], ENT_QUOTES, 'UTF-8') ?> donations)</p>
                                <?php if (!empty($status_details[$row['status']])): ?>
                                    <p>Top <?= min(3, count($status_details[$row['status']])) ?> Donations:</p>
                                    <ul>
                                        <?php foreach ($status_details[$row['status']] as $detail): ?>
                                            <li><?= htmlspecialchars($detail['donor_name'], ENT_QUOTES, 'UTF-8') ?>: $<?= number_format($detail['amount'], 2) ?> on <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No individual donations available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="col-md-4">
                <div class="card bg-average">
                    <div class="card-header" id="headingAverage" data-bs-toggle="collapse" data-bs-target="#collapseAverage" aria-expanded="false" aria-controls="collapseAverage">
                        <h5>Average Donation Amount Across All Contributions</h5>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div id="collapseAverage" class="collapse" aria-labelledby="headingAverage" data-bs-parent="#donationAnalysisAccordion">
                        <div class="card-body">
                            <p>$<?= number_format($avg_amount, 2) ?></p>
                            <p>Additional Details:</p>
                            <ul>
                                <li>Minimum Donation: $<?= number_format($min_max['min_amount'] ?? 0, 2) ?></li>
                                <li>Maximum Donation: $<?= number_format($min_max['max_amount'] ?? 0, 2) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($top_donor && !empty($top_donor['donor_name'])): ?>
                <div class="col-md-4">
                    <div class="card bg-top-donor">
                        <div class="card-header" id="headingTopDonor" data-bs-toggle="collapse" data-bs-target="#collapseTopDonor" aria-expanded="false" aria-controls="collapseTopDonor">
                            <h5>Top Donor by Total Contribution Amount</h5>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <div id="collapseTopDonor" class="collapse" aria-labelledby="headingTopDonor" data-bs-parent="#donationAnalysisAccordion">
                            <div class="card-body">
                                <p><?= htmlspecialchars($top_donor['donor_name'], ENT_QUOTES, 'UTF-8') ?> ($<?= number_format($top_donor['total'], 2) ?>)</p>
                                <?php if (!empty($top_donor_details)): ?>
                                    <p>Last <?= min(3, count($top_donor_details)) ?> Donations:</p>
                                    <ul>
                                        <?php foreach ($top_donor_details as $detail): ?>
                                            <li>$<?= number_format($detail['amount'], 2) ?> (<?= ucfirst(htmlspecialchars($detail['status'], ENT_QUOTES, 'UTF-8')) ?>) on <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No recent donations available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No donations available for analysis. <a href="add_donation.php">Add a donation</a> to get started.
        </div>
    <?php endif; ?>

    <!-- Feedback History Section -->
    <h4 class="mt-4">Feedback History</h4>
    <?php if ($total_feedback > 0): ?>
        <div class="row" id="feedbackHistoryAccordion">
            <div class="col-12">
                <div class="card bg-feedback">
                    <div class="card-header" id="headingFeedback" data-bs-toggle="collapse" data-bs-target="#collapseFeedback" aria-expanded="false" aria-controls="collapseFeedback">
                        <h5>Feedback Overview</h5>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div id="collapseFeedback" class="collapse" aria-labelledby="headingFeedback" data-bs-parent="#feedbackHistoryAccordion">
                        <div class="card-body">
                            <p><strong>Total Feedback:</strong> <?= htmlspecialchars($total_feedback, ENT_QUOTES, 'UTF-8') ?> <span class="badge bg-secondary">Entries</span></p>
                            <p><strong>Starred Feedback:</strong> <?= htmlspecialchars($starred_count, ENT_QUOTES, 'UTF-8') ?> (<?= number_format($starred_percentage, 1) ?>%) <span class="badge bg-warning">Important</span></p>
                            <h6>Recent Feedback:</h6>
                            <?php while ($feedback = $feedback_result->fetch_assoc()): ?>
                                <div class="feedback-item <?= $feedback['starred'] ? 'starred' : '' ?>">
                                    <?php if ($feedback['starred']): ?>
                                        <span class="star-icon">★</span>
                                    <?php endif; ?>
                                    <p><strong>Donor:</strong> <?= htmlspecialchars($feedback['donor_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Feedback:</strong> <?= htmlspecialchars($feedback['feedback'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Rating:</strong> <?= htmlspecialchars($feedback['rating'], ENT_QUOTES, 'UTF-8') ?>/5</p>
                                    <p><strong>Time:</strong> <?= date('d/m/Y H:i', strtotime($feedback['created_at'])) ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No feedback submitted yet. Encourage donors to provide feedback.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Rotate arrow on collapse toggle
    document.querySelectorAll('.card-header').forEach(header => {
        header.addEventListener('click', () => {
            const arrow = header.querySelector('.bi-chevron-right');
            const collapse = document.querySelector(header.dataset.bsTarget);
            if (collapse.classList.contains('show')) {
                arrow.classList.remove('rotate-90');
            } else {
                arrow.classList.add('rotate-90');
            }
        });
    });
</script>