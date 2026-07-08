<?php
session_start();

if (!isset($_SESSION['donor'])) {
    header("Location: donor_login.php");
    exit();
}

include 'db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $feedback = trim($_POST['feedback']);
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $donor_name = $_SESSION['donor'];

    // Validate inputs
    $errors = [];
    if (empty($feedback)) {
        $errors[] = "Feedback cannot be empty.";
    } elseif (strlen($feedback) > 1000) {
        $errors[] = "Feedback cannot exceed 1000 characters.";
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }

    if (empty($errors)) {
        // Insert feedback into database
        $stmt = $conn->prepare("INSERT INTO feedback (donor_name, feedback, rating, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ssi", $donor_name, $feedback, $rating);
            if ($stmt->execute()) {
                $message = "Feedback submitted successfully! Rating: $rating stars";
                $messageType = 'success';
            } else {
                $message = "Failed to submit feedback. Please try again. Error: " . $conn->error;
                $messageType = 'danger';
            }
            $stmt->close();
        } else {
            $message = "Database preparation failed. Check table structure. Error: " . $conn->error;
            $messageType = 'danger';
        }
    } else {
        $message = "Errors: " . implode(" ", $errors);
        $messageType = 'danger';
    }
}

// Fetch feedback history
$donor_name = $_SESSION['donor'];
$stmt = $conn->prepare("SELECT feedback, rating, created_at FROM feedback WHERE donor_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $donor_name);
$stmt->execute();
$feedbacks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set active section
if (!isset($_SESSION['active_section'])) {
    $_SESSION['active_section'] = 'feedback';
}
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'profile.php') {
    $_SESSION['active_section'] = 'profile';
} elseif ($current_page === 'donor_dashboard.php') {
    $_SESSION['active_section'] = 'donor_dashboard';
} elseif ($current_page === 'goals.php') {
    $_SESSION['active_section'] = 'goals';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Feedback</title>
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

        .funds-box {
            background: rgba(255, 170, 0, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 100px;
        }

        .funds-box h4 {
            color: #000000;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .funds-box .card {
            background: #FFFFFF;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .funds-box .card:hover {
            transform: scale(1.05);
        }

        .funds-box .card-header {
            background: #27ae60;
            color: #FFFFFF;
            padding: 10px;
            cursor: pointer;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
        }

        .funds-box .card-body {
            padding: 15px;
            color: #000000;
        }

        .modal-content {
            border-radius: 10px;
            background: #FFFFFF;
        }

        .modal-header {
            background: #27ae60;
            color: #FFFFFF;
            border-bottom: none;
        }

        .modal-body,
        .modal-footer {
            color: #000000;
        }

        .modal-footer {
            border-top: none;
        }

        .btn-close-white {
            filter: invert(1);
        }

        .btn-primary {
            background: #27ae60;
            border: none;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #219653;
        }

        .btn-secondary {
            background: #FF0000;
            border: none;
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background: #CC0000;
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

        .feedback-table {
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .feedback-table th,
        .feedback-table td {
            color: #000000;
            padding: 12px;
        }

        .feedback-table th {
            background: #27ae60; /* Changed to green */
            color: #FFFFFF;
        }
    </style>
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
            <h3>Submit Feedback</h3>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Your Feedback</label>
                    <textarea class="form-control" name="feedback" rows="4" required maxlength="1000"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating (1-5 Stars)</label>
                    <select class="form-control" name="rating" required>
                        <option value="" selected disabled>Select Rating</option>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                </div>
                <button type="submit" name="submit_feedback" class="btn btn-donate">Submit Feedback</button>
            </form>
            <?php if (empty($feedbacks)): ?>
                <p class="mt-3">No feedback submitted yet.</p>
            <?php else: ?>
                <h4 class="mt-4">Feedback History</h4>
                <table class="table feedback-table">
                    <thead>
                        <tr>
                            <th>Feedback</th>
                            <th>Rating</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['feedback'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($feedback['rating'], ENT_QUOTES, 'UTF-8'); ?> Stars</td>
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($feedback['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="funds-box">
            <h4>Fund Allocation</h4>
            <div class="card">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#educationCollapse" aria-expanded="false" aria-controls="educationCollapse">Education</div>
                <div id="educationCollapse" class="collapse" data-bs-parent=".funds-box">
                    <div class="card-body">
                        <p>50% of funds go to education initiatives, including scholarships, school supplies, and digital learning tools for underprivileged students.</p>
                        <p><strong>Options:</strong> Stationery, Tuition, Laptops, Textbooks, Uniforms, Scholarships, Digital Tools</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#disasterCollapse" aria-expanded="false" aria-controls="disasterCollapse">Disaster Relief</div>
                <div id="disasterCollapse" class="collapse" data-bs-parent=".funds-box">
                    <div class="card-body">
                        <p>20% of funds support disaster relief efforts, providing immediate aid like food, water, and shelter during natural calamities.</p>
                        <p><strong>Options:</strong> Food, Shelter, Water, Clothing, Medical Kits, Blankets, Tents</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#healthCollapse" aria-expanded="false" aria-controls="healthCollapse">Health</div>
                <div id="healthCollapse" class="collapse" data-bs-parent=".funds-box">
                    <div class="card-body">
                        <p>30% of funds are allocated to healthcare programs, funding medical camps, essential medicines, and rural clinic infrastructure.</p>
                        <p><strong>Options:</strong> Medical Supplies, Health Camps, Vaccinations, Medicines, Equipment, Sanitation Kits, Ambulance Services</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Payment processing is mocked for this demo. In a real application, integrate with a payment gateway like PayPal or Stripe.</p>
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