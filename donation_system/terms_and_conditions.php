<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background: #FFF6E7; /* Light peach background from Space ECE */
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
            background: rgba(0, 0, 0, 0.1); /* Light overlay for contrast */
            z-index: 1;
        }
        .sidebar {
            width: 250px;
            background: #FFFFFF; /* White sidebar from Space ECE */
            color: #000000;
            padding: 30px 20px;
            position: relative;
            z-index: 2;
            transition: width 0.3s ease;
        }
        .sidebar h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            margin-bottom: 30px;
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
            padding: 10px;
            text-decoration: none;
            margin: 15px 0;
            background: #FFFFFF;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background: #F0F0F0;
        }
        .sidebar a.logout {
            color: #FF0000; /* Red for logout */
        }
        .sidebar a.logout:hover {
            background: #FF0000;
            color: #FFFFFF;
        }
        .main {
            flex: 1;
            padding: 30px;
            background: rgba(255, 170, 0, 0.9); /* Orange from Space ECE */
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
            text-align: center;
        }
        .card {
            border: none;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
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
            font-size: 1.2rem;
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
        .card-body a {
            color: #27ae60; /* Green from Space ECE */
            text-decoration: underline;
        }
        .card-body a:hover {
            color: #219653; /* Darker green */
        }
        .bg-introduction { background-color: #219653 !important; } /* Darker green */
        .bg-donation { background-color: #27ae60 !important; } /* Green from Space ECE */
        .bg-user { background-color: #FFB347 !important; } /* Orange from dashboard */
        .bg-privacy { background-color: #FF0000 !important; } /* Red from Space ECE */
        .bg-liability { background-color: #2ecc71 !important; } /* Lighter green */
        .bg-modifications { background-color: #3498db !important; } /* Blue for contrast */
        .bg-contact { background-color: #7f8c8d !important; } /* Gray for contrast */
        .bi-chevron-right {
            transition: transform 0.3s ease;
        }
        .bi-chevron-right.rotate-90 {
            transform: rotate(90deg);
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
        <h2>Terms and Conditions</h2>
        <div class="accordion" id="termsAccordion">
            <!-- Introduction -->
            <div class="card bg-introduction">
                <div class="card-header" id="headingIntroduction" data-bs-toggle="collapse" data-bs-target="#collapseIntroduction" aria-expanded="false" aria-controls="collapseIntroduction">
                    <h5>1. Introduction</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseIntroduction" class="collapse" aria-labelledby="headingIntroduction" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <p>Welcome to the Donation System. These Terms and Conditions govern the use of our platform for managing donations. By accessing or using this system, you agree to comply with these terms.</p>
                    </div>
                </div>
            </div>
            <!-- Donation Policies -->
            <div class="card bg-donation">
                <div class="card-header" id="headingDonation" data-bs-toggle="collapse" data-bs-target="#collapseDonation" aria-expanded="false" aria-controls="collapseDonation">
                    <h5>2. Donation Policies</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseDonation" class="collapse" aria-labelledby="headingDonation" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <ul>
                            <li><strong>Eligibility</strong>: Donations can be made by individuals, organizations, or entities authorized to make financial contributions.</li>
                            <li><strong>Non-Refundable</strong>: All donations are final and non-refundable unless otherwise specified by applicable law or our refund policy.</li>
                            <li><strong>Usage</strong>: Donations will be used solely for the purposes outlined by the organization, as displayed on our platform.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- User Responsibilities -->
            <div class="card bg-user">
                <div class="card-header" id="headingUser" data-bs-toggle="collapse" data-bs-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser">
                    <h5>3. User Responsibilities</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseUser" class="collapse" aria-labelledby="headingUser" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <ul>
                            <li><strong>Accurate Information</strong>: Users must provide accurate and truthful information when making donations or interacting with the system.</li>
                            <li><strong>Compliance</strong>: Users must comply with all applicable local, state, national, and international laws regarding donations.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Privacy -->
            <div class="card bg-privacy">
                <div class="card-header" id="headingPrivacy" data-bs-toggle="collapse" data-bs-target="#collapsePrivacy" aria-expanded="false" aria-controls="collapsePrivacy">
                    <h5>4. Privacy</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapsePrivacy" class="collapse" aria-labelledby="headingPrivacy" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <p>Your personal information is handled in accordance with our Privacy Policy. We collect donor names, donation amounts, and timestamps to manage contributions effectively.</p>
                    </div>
                </div>
            </div>
            <!-- Limitation of Liability -->
            <div class="card bg-liability">
                <div class="card-header" id="headingLiability" data-bs-toggle="collapse" data-bs-target="#collapseLiability" aria-expanded="false" aria-controls="collapseLiability">
                    <h5>5. Limitation of Liability</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseLiability" class="collapse" aria-labelledby="headingLiability" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <p>The Donation System and its administrators are not liable for any direct, indirect, incidental, or consequential damages arising from the use of this platform.</p>
                    </div>
                </div>
            </div>
            <!-- Modifications -->
            <div class="card bg-modifications">
                <div class="card-header" id="headingModifications" data-bs-toggle="collapse" data-bs-target="#collapseModifications" aria-expanded="false" aria-controls="collapseModifications">
                    <h5>6. Modifications</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseModifications" class="collapse" aria-labelledby="headingModifications" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective upon posting to this page.</p>
                    </div>
                </div>
            </div>
            <!-- Contact Us -->
            <div class="card bg-contact">
                <div class="card-header" id="headingContact" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="false" aria-controls="collapseContact">
                    <h5>7. Contact Us</h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div id="collapseContact" class="collapse" aria-labelledby="headingContact" data-bs-parent="#termsAccordion">
                    <div class="card-body">
                        <p>If you have any questions about these Terms and Conditions, please visit <a href="https://www.spacece.in/">https://www.spacece.in/</a>.</p>
                    </div>
                </div>
            </div>
        </div>
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
</body>
</html>