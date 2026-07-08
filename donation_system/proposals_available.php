```php
<?php
session_start();

if (!isset($_SESSION['donor'])) {
    header("Location: donor_login.php");
    exit();
}

include 'db.php';

$message = '';
$proposals = [];

$stmt = $conn->prepare("SELECT proposal_id, heading, details, amount, image_path, created_at FROM proposals");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $proposal_id = $row['proposal_id'];
        // Calculate total donated amount for this proposal
        $donated_stmt = $conn->prepare("SELECT SUM(amount) as total_donated FROM proposal_donations WHERE proposal_id = ?");
        if ($donated_stmt) {
            $donated_stmt->bind_param("s", $proposal_id);
            $donated_stmt->execute();
            $donated_result = $donated_stmt->get_result();
            $donated_row = $donated_result->fetch_assoc();
            $row['donated'] = $donated_row['total_donated'] ?: 0;
            $donated_stmt->close();
        }
        $proposals[$proposal_id] = $row;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate_to_proposal'])) {
    $proposal_id = $_POST['proposal_id'];
    $amount = floatval($_POST['amount']);

    if (isset($proposals[$proposal_id]) && $amount > 0) {
        $remaining = $proposals[$proposal_id]['amount'] - ($proposals[$proposal_id]['donated'] ?: 0);
        if ($amount <= $remaining) {
            // Insert into donations table (assuming id is auto-increment)
            $donor_name = $_SESSION['donor'];
            $category = 'Proposal'; // You can adjust this category
            $item = $proposals[$proposal_id]['heading'];
            $payment_method = 'N/A'; // Adjust based on your form
            $donation_stmt = $conn->prepare("INSERT INTO donations (donor_name, amount, category, item, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            if ($donation_stmt) {
                $donation_stmt->bind_param("sdsss", $donor_name, $amount, $category, $item, $payment_method);
                if ($donation_stmt->execute()) {
                    $donation_id = $conn->insert_id;
                    // Link to proposal_donations
                    $link_stmt = $conn->prepare("INSERT INTO proposal_donations (proposal_id, donation_id, amount, donated_at) VALUES (?, ?, ?, NOW())");
                    if ($link_stmt) {
                        $link_stmt->bind_param("sid", $proposal_id, $donation_id, $amount);
                        if ($link_stmt->execute()) {
                            $message = "Donation of $$amount to '{$proposals[$proposal_id]['heading']}' successful!";
                            // Set a hidden input to trigger modal via JavaScript
                            echo '<input type="hidden" id="donationSuccess" value="true">';
                        } else {
                            $message = "Failed to link donation. Error: " . $conn->error;
                        }
                        $link_stmt->close();
                    }
                } else {
                    $message = "Failed to submit donation. Error: " . $conn->error;
                }
                $donation_stmt->close();
            }
        } else {
            $message = "Donation amount exceeds remaining funds.";
        }
    } else {
        $message = "Invalid proposal or donation amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposals Available</title>
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
        .card {
            background: #FFFFFF;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-header {
            background: #27ae60;
            color: #FFFFFF;
            padding: 10px;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
        }
        .card-body {
            padding: 15px;
            color: #000000;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            background: #ecf0f1;
            color: #000000;
            padding: 10px;
        }
        .btn-donate {
            border-radius: 5px;
            background-color: #27ae60;
            border: none;
            color: #FFFFFF;
            transition: background-color 0.3s ease;
        }
        .btn-donate:hover {
            background-color: #219653;
        }
        .btn-qr {
            border-radius: 5px;
            background-color: #3498db;
            border: none;
            color: #FFFFFF;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-qr:hover {
            background-color: #2980b9;
        }
        .alert-success {
            background: #27ae60;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
        }
        .alert-danger {
            background: #FF0000;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
        }
        .proposal-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 5px;
        }
        .missing-image {
            color: red;
            font-style: italic;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            const forms = document.querySelectorAll('form[method="POST"]');
            const qrImage = document.querySelector('#paymentModal img');

            // Debug: Check if QR image is loading
            if (qrImage && qrImage.complete) {
                console.log('QR image loaded successfully:', qrImage.src);
            } else if (qrImage) {
                console.log('QR image failed to load:', qrImage.src);
                qrImage.onerror = function() {
                    console.error('Image load error for:', qrImage.src);
                    qrImage.parentElement.innerHTML = '<p class="missing-image">QR code image not found. Please check the path: ' + qrImage.src + '</p>';
                };
            } else {
                console.error('QR image element not found in modal');
            }

            forms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(form);

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.documentElement.innerHTML = html;
                        const successElement = document.getElementById('donationSuccess');
                        if (successElement && successElement.value === 'true') {
                            paymentModal.show();
                            successElement.remove();
                            console.log('Modal shown after successful donation');
                        } else {
                            console.log('No donation success detected');
                        }
                    })
                    .catch(error => console.error('Fetch error:', error));
                });
            });

            // Add event listener for QR Code buttons
            document.querySelectorAll('.btn-qr').forEach(button => {
                button.addEventListener('click', function () {
                    paymentModal.show();
                    console.log('QR Code modal shown for button click');
                });
            });
        });
    </script>
</head>
<body>
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
            <li class="<?php echo $current_page === 'proposals_available.php' ? 'active' : ''; ?>">
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
            <h3>Proposals Available</h3>
            <?php if ($message): ?>
                <div class="alert alert-<?= strpos($message, 'successful') !== false ? 'success' : 'danger' ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if (empty($proposals)): ?>
                <p>No proposals available at the moment.</p>
            <?php else: ?>
                <?php foreach ($proposals as $id => $proposal): ?>
                    <div class="card">
                        <div class="card-header"><?= htmlspecialchars($proposal['heading']) ?></div>
                        <div class="card-body">
                            <p><?= nl2br(htmlspecialchars($proposal['details'])) ?></p>
                            <p><strong>Amount Required:</strong> $<?= number_format($proposal['amount'], 2) ?></p>
                            <p><strong>Donated So Far:</strong> $<?= number_format($proposal['donated'], 2) ?></p>
                            <p><strong>Remaining:</strong> $<?= number_format($proposal['amount'] - $proposal['donated'], 2) ?></p>
                            <?php if ($proposal['image_path']): ?>
                                <img src="<?= htmlspecialchars($proposal['image_path']) ?>" alt="Proposal Image" class="proposal-image">
                            <?php endif; ?>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="proposal_id" value="<?= $id ?>">
                                <div class="mb-2">
                                    <input type="number" step="0.01" min="0.01" max="<?= $proposal['amount'] - $proposal['donated'] ?>" class="form-control" name="amount" placeholder="Enter donation amount" required>
                                </div>
                                <button type="submit" name="donate_to_proposal" class="btn btn-donate">Donate</button>
                                <button type="button" class="btn-qr">QR Code</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <p>Scan the QR code below to complete your payment:</p>
                    <?php
                    $qrPath = 'uploads/image1.jpg';
                    if (file_exists($qrPath)) {
                        echo '<img src="' . htmlspecialchars($qrPath) . '" alt="Payment QR Code" style="max-width: 100%; height: auto;">';
                    } else {
                        echo '<p class="missing-image">QR code image not found at: ' . htmlspecialchars($qrPath) . '</p>';
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
