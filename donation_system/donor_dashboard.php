```php
<?php
session_start();

if (!isset($_SESSION['donor'])) {
    header("Location: donor_login.php");
    exit();
}

include 'db.php';

// Handle donation submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate'])) {
    $amount = trim($_POST['amount']);
    $category = trim($_POST['category']);
    $item = trim($_POST['item']);
    $payment_method = trim($_POST['payment_method']);
    $donor_name = $_SESSION['donor'];

    // Validate inputs
    $errors = [];
    if (!is_numeric($amount) || $amount <= 0) {
        $errors[] = "Amount must be a positive number.";
    }
    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    if (empty($item)) {
        $errors[] = "Item is required.";
    }
    if (empty($payment_method)) {
        $errors[] = "Payment method is required.";
    }

    if (empty($errors)) {
        // Insert donation into database with updated schema
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, amount, category, item, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        if ($stmt) {
            $stmt->bind_param("sdsss", $donor_name, $amount, $category, $item, $payment_method);
            if ($stmt->execute()) {
                $message = "Donation of $$amount submitted successfully!";
                $messageType = 'success';
                // Clear form after successful submission
                $amount = '';
                $category = '';
                $item = '';
                $payment_method = '';
            } else {
                $message = "Failed to submit donation. Please try again. Error: " . $conn->error;
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

// Retain form values from POST data
$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$item = isset($_POST['item']) ? trim($_POST['item']) : '';
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

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
    <title>Donor Dashboard</title>
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
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category');
            const itemSelect = document.getElementById('item');
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

            const items = {
                'Education': ['Stationery', 'Tuition', 'Laptops', 'Textbooks', 'Uniforms', 'Scholarships', 'Digital Tools'],
                'Disaster Relief': ['Food', 'Shelter', 'Water', 'Clothing', 'Medical Kits', 'Blankets', 'Tents'],
                'Health': ['Medical Supplies', 'Health Camps', 'Vaccinations', 'Medicines', 'Equipment', 'Sanitation Kits', 'Ambulance Services']
            };

            function updateItems() {
                const category = categorySelect.value;
                console.log('Category selected:', category); // Debug log
                itemSelect.innerHTML = '<option value="">Select Item</option>';

                if (category && items[category]) {
                    items[category].forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        if (item === '<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>') {
                            option.selected = true;
                        }
                        itemSelect.appendChild(option);
                    });
                    console.log('Items populated:', items[category]); // Debug log
                } else {
                    console.log('No items found for category:', category); // Debug log
                }
            }

            function showPaymentModal() {
                if (paymentMethodSelect.value !== '') {
                    paymentModal.show();
                }
            }

            // Initial call to set items based on pre-selected category
            updateItems();

            // Add event listeners
            categorySelect.addEventListener('change', updateItems);
            paymentMethodSelect.addEventListener('change', showPaymentModal);
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
            <h3>Make a Donation</h3>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <form method="POST" id="donationForm">
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" value="<?php echo htmlspecialchars($amount, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category" id="category" required>
                        <option value="" <?php echo empty($category) ? 'selected' : ''; ?>>Select Category</option>
                        <option value="Education" <?php echo $category === 'Education' ? 'selected' : ''; ?>>Education</option>
                        <option value="Disaster Relief" <?php echo $category === 'Disaster Relief' ? 'selected' : ''; ?>>Disaster Relief</option>
                        <option value="Health" <?php echo $category === 'Health' ? 'selected' : ''; ?>>Health</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item</label>
                    <select class="form-control" name="item" id="item" required>
                        <option value="" <?php echo empty($item) ? 'selected' : ''; ?>>Select Item</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" name="payment_method" id="payment_method" required>
                        <option value="" <?php echo empty($payment_method) ? 'selected' : ''; ?>>Select Payment Method</option>
                        <option value="Credit Card" <?php echo $payment_method === 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="PayPal" <?php echo $payment_method === 'PayPal' ? 'selected' : ''; ?>>PayPal</option>
                        <option value="UPI" <?php echo $payment_method === 'UPI' ? 'selected' : ''; ?>>UPI</option>
                        <option value="Bank Transfer" <?php echo $payment_method === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                    <input type="hidden" id="qrCodeData" name="qrCodeData" value="">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="recurring" name="recurring" <?php echo isset($_POST['recurring']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="recurring">Make this a recurring donation</label>
                </div>
                <button type="submit" name="donate" class="btn btn-donate">Donate Now</button>
            </form>
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
                <div class="modal-body" id="modalBody">
                    <p>Scan the QR code below to complete your payment:</p>
                    <img src="uploads/image1.jpg" alt="Payment QR Code" style="max-width: 100%; height: auto;">
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
