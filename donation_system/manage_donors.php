<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$donor_count = $conn->query("SELECT COUNT(DISTINCT donor_name) as count FROM donations")->fetch_assoc()['count'] ?? 0;
$donors = $conn->query("SELECT donor_name, SUM(amount) as total FROM donations GROUP BY donor_name");

if (isset($_GET['get_donor_details']) && !empty($_GET['donor_name'])) {
    $donor_name = trim($_GET['donor_name']);
    $stmt = $conn->prepare("SELECT amount, status, category, item, payment_method, created_at FROM donations WHERE donor_name = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $donor_name);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($donations);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    $stmt->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donors</title>
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
        .sidebar h4 {
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
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }
        .main h3 {
            color: #000000;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 24px;
            text-align: center;
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
        .alert-info {
            background-color: #27ae60;
            color: #FFFFFF;
            border-radius: 5px;
            border: none;
            text-align: center;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #ecf0f1;
            color: #000000;
            padding: 10px;
        }
        .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
        }
        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
            width: 100%;
            max-width: 800px;
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
        .table th:nth-child(1), .table td:nth-child(1) { width: 40%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 30%; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 30%; }
        .btn-primary {
            border-radius: 5px;
            background-color: #27ae60;
            border: none;
            color: #FFFFFF;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #219653;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }
        .modal-content {
            border-radius: 10px;
            background-color: #FFFFFF;
        }
        .modal-header {
            background-color: #27ae60;
            color: #FFFFFF;
            border-bottom: none;
        }
        .modal-body {
            color: #000000;
        }
        .modal-footer {
            border-top: none;
        }
        .btn-secondary {
            background-color: #FF0000;
            border: none;
            color: #FFFFFF;
        }
        .btn-secondary:hover {
            background-color: #CC0000;
        }
        .btn-close-white {
            filter: invert(1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <a href="index.php"><span class="icon">🏠</span> Dashboard</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'add_donation.php' ? 'active' : ''; ?>">
                <a href="add_donation.php"><span class="icon">💸</span> Add Donation</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_donors.php' ? 'active' : ''; ?>">
                <a href="manage_donors.php"><span class="icon">👥</span> Manage Donors</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'report.php' ? 'active' : ''; ?>">
                <a href="report.php"><span class="icon">📊</span> Reports</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'terms_and_conditions.php' ? 'active' : ''; ?>">
                <a href="terms_and_conditions.php"><span class="icon">📜</span> Terms and Conditions</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'proposal_generator.php' ? 'active' : ''; ?>">
                <a href="proposal_generator.php"><span class="icon">📝</span> Proposal Generator</a>
            <li>
                <a href="logout.php" class="logout"><span class="icon">🚪</span> Logout</a>
            </li>
        </ul>
    </div>

    <div class="main">
        <div class="content-box">
            <h3>All Donors</h3>
            <div class="alert alert-info">
                <strong>Total Donors:</strong> <?= htmlspecialchars($donor_count, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="mb-3 d-flex">
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by donor name">
                <button id="export-btn" class="btn btn-primary ms-2">Export to CSV</button>
            </div>
            <div class="table-container">
                <table class="table table-bordered" id="donorsTable">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Total Donated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = $donors->fetch_assoc()): ?>
                            <tr>
                                <td class="donor-name"><?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="total">$<?= number_format($d['total'], 2) ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-details" data-donor="<?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?>">View Details</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="donorDetailsModal" tabindex="-1" aria-labelledby="donorDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="donorDetailsModalLabel">Donation History</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="donorDetailsBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const viewButtons = document.querySelectorAll('.view-details');
        const modalEl = document.getElementById('donorDetailsModal');
        const modalTitle = document.getElementById('donorDetailsModalLabel');
        const modalBody = document.getElementById('donorDetailsBody');

        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                const name = button.dataset.donor;
                modalTitle.textContent = `Donation History for ${name}`;
                modalBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

                fetch(`manage_donors.php?get_donor_details=1&donor_name=${encodeURIComponent(name)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            modalBody.innerHTML = '<tr><td colspan="6" class="text-center">No donations found.</td></tr>';
                        } else {
                            modalBody.innerHTML = data.map(d => `
                                <tr>
                                    <td>$${parseFloat(d.amount).toFixed(2)}</td>
                                    <td>${d.category || 'N/A'}</td>
                                    <td>${d.item || 'N/A'}</td>
                                    <td>${d.payment_method || 'N/A'}</td>
                                    <td>${d.status.charAt(0).toUpperCase() + d.status.slice(1)}</td>
                                    <td>${new Date(d.created_at).toLocaleString()}</td>
                                </tr>
                            `).join('');
                        }
                    })
                    .catch(err => {
                        modalBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">${err.message}</td></tr>`;
                    });

                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', () => {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#donorsTable tbody tr');
            rows.forEach(row => {
                const donorName = row.querySelector('.donor-name').textContent.toLowerCase();
                row.style.display = donorName.includes(searchTerm) ? '' : 'none';
            });
        });

        // Export to CSV
        document.getElementById('export-btn').addEventListener('click', () => {
            const table = document.getElementById('donorsTable');
            const rows = table.querySelectorAll('tbody tr');
            const data = [];
            rows.forEach(row => {
                const donorName = row.querySelector('.donor-name').textContent;
                const total = row.querySelector('.total').textContent.replace('$', '');
                data.push(`"${donorName.replace(/"/g, '""')}",${total}`);
            });
            const csv = ['Donor Name,Total Donated', ...data].join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'donors.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });
    </script>
</body>
</html>