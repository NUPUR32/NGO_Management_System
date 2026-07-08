<?php
session_start();
include 'db.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donation Management System - Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #FFF6E7;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      position: relative;
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
    .funds-explanation-box {
      position: fixed;
      top: 50%;
      left: 20px;
      transform: translateY(-50%);
      width: 300px;
      background: rgba(255, 170, 0, 0.9);
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 2;
      text-align: center;
      color: #000000;
      font-size: 0.95rem;
      display: none;
      max-height: 80vh;
      overflow-y: auto;
    }
    .toggle-button {
      margin-top: 20px;
      background-color: #27ae60;
      color: #FFFFFF;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .toggle-button:hover {
      background-color: #219653;
    }
    .login-container {
      width: 400px;
      padding: 2.5rem 2rem;
      background: rgba(255, 170, 0, 0.9);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      text-align: center;
      position: relative;
      z-index: 2;
      margin-top: 60px;
    }
    .login-container h2 {
      color: #000000;
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      margin-bottom: 1rem;
    }
    .login-container h5 {
      color: #000000;
      margin-bottom: 1.5rem;
    }
    .form-control {
      border-radius: 5px;
      border: 1px solid #ddd;
      background-color: #ecf0f1;
      color: #000000;
    }
    .form-control:focus {
      border-color: #27ae60;
      box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.25);
    }
    .btn-login {
      border-radius: 5px;
      padding: 12px 30px;
      font-size: 1.1rem;
      background-color: #27ae60;
      border: none;
      color: #FFFFFF;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #219653;
    }
    .btn-donor {
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 1rem;
      background-color: #27ae60;
      border: none;
      color: #FFFFFF;
      text-decoration: none;
      display: inline-block;
      margin-top: 1rem;
      transition: background-color 0.3s ease;
    }
    .btn-donor:hover {
      background-color: #219653;
    }
    .alert-danger {
      background-color: #FF0000;
      color: #FFFFFF;
      border-radius: 5px;
      border: none;
    }
    .donation-counter {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 300px;
      background: rgba(255, 170, 0, 0.9);
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 3;
      padding: 20px;
      text-align: center;
      color: #000000;
      transition: transform 0.3s ease;
    }
    .donation-counter:hover {
      transform: scale(1.05);
    }
    .donation-counter h4 {
      color: #000000;
      margin-bottom: 15px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      text-transform: uppercase;
    }
    .donation-counter p {
      color: #000000;
      font-size: 1.3rem;
      margin: 5px 0;
      font-weight: 600;
    }
    .funds-allocation {
      margin-top: 15px;
      font-size: 0.9rem;
    }
    .funds-allocation p {
      margin: 5px 0;
      color: #000000;
    }
    .progress-bar-container {
      margin-top: 15px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 5px;
      height: 20px;
      overflow: hidden;
    }
    .progress-bar {
      height: 100%;
      background: #27ae60;
      width: 0%;
      transition: width 0.5s ease;
      border-radius: 5px;
      text-align: right;
      padding-right: 10px;
      color: #FFFFFF;
      font-size: 0.9rem;
      font-weight: bold;
    }
    footer {
      padding: 20px;
      background: #FFFFFF;
      color: #000000;
      text-align: center;
      font-size: 0.9rem;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      gap: 20px;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 2;
    }
    .footer-text {
      margin: 0;
    }
    .footer-links {
      display: flex;
      gap: 15px;
    }
    .footer-links a {
      color: #000000; /* Black for Contact Us link */
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .footer-links a:hover {
      color: #333333; /* Darker gray on hover */
    }
    .social-icons {
      display: flex;
      gap: 15px;
    }
    .social-icons a {
      color: #000000;
      font-size: 1.5rem;
      transition: all 0.3s ease;
    }
    .social-icons a:hover {
      color: #27ae60;
      transform: translateY(-3px);
    }
    @media (max-width: 768px) {
      .login-container {
        width: 90%;
        padding: 1.5rem;
      }
      .funds-explanation-box {
        width: 90%;
        left: 5%;
        transform: translateY(-50%);
      }
      .donation-counter {
        width: 90%;
        right: 5%;
        bottom: 80px;
      }
      footer {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

<div class="funds-explanation-box" id="fundsExplanationBox">
  <p><strong>Your Donations at Work</strong></p>
  <p>Your generous contributions are making a significant impact across multiple sectors. We allocate 50% of funds to education initiatives, supporting underprivileged students with scholarships, school supplies, and digital learning tools. This has enabled over 1,000 students to continue their education in the past year alone.</p>
  <p>Another 30% goes to healthcare programs, funding medical camps, providing essential medicines, and improving rural clinic infrastructure. Last month, we assisted 500 families with free health check-ups and distributed critical supplies to remote areas.</p>
  <p>The remaining 20% is dedicated to disaster relief efforts, offering immediate aid such as food, water, and shelter during natural calamities. Recently, our team provided relief to 200 households affected by floods, ensuring their safety and recovery.</p>
  <p><strong>Our Impact</strong></p>
  <p>Every dollar you donate helps us expand these efforts. We partner with local organizations to ensure funds reach those in need efficiently. Our transparent process allows donors to see the real change their support creates, from building schools to saving lives.</p>
  <p><strong>Call to Action</strong></p>
  <p>Join us in making a difference! Log in as a donor to contribute or spread the word through our social media channels. Your support can transform communities and provide hope where it’s needed most. Together, we can achieve more!</p>
</div>

<div class="login-container">
    <h2>Donation Management System</h2>
    <h5>Admin Login</h5>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button type="submit" name="login" class="btn btn-login w-100">Login</button>
    </form>

    <a href="donor_login.php" class="btn-donor">Donor Login</a>
    <button class="toggle-button" onclick="toggleFundsBox()">Show Funds Info</button>
</div>

<div class="donation-counter" id="donationCounter">
  <h4>Total Donations</h4>
  <p id="donationTotal">$0.00</p>
  <p id="lastUpdated">Last updated: Loading...</p>
  <div class="funds-allocation">
    <p><strong>Where Funds Go:</strong></p>
    <p>50% Education</p>
    <p>30% Healthcare</p>
    <p>20% Disaster Relief</p>
  </div>
  <div class="progress-bar-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>
</div>

<footer>
  <div class="footer-text">Donation Management System | SpacECE | 2025</div>
  <div class="footer-links">
    <a href="https://www.spacece.in/contact-us" target="_blank">Contact Us</a>
  </div>
  <div class="social-icons">
    <a href="https://www.facebook.com/SpacECE/" target="_blank" title="SpacECE India Foundation on Facebook">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://www.instagram.com/spac.ece/" target="_blank" title="SpacECE India Foundation on Instagram">
      <i class="fab fa-instagram"></i>
    </a>
    <a href="https://api.whatsapp.com/send/?phone=%2B919096305648&text=You+are+chatting+with+%27SPACE+for+ECE%27.+Please+text+your+query+here.&app_absent=0" target="_blank" title="SpacECE India Foundation on WhatsApp">
      <i class="fab fa-whatsapp"></i>
    </a>
    <a href="https://www.youtube.com/@SpacECE" target="_blank" title="SpacECE India Foundation on YouTube">
      <i class="fab fa-youtube"></i>
    </a>
    <a href="https://www.linkedin.com/company/spacece-co/" target="_blank" title="SpacECE India Foundation on LinkedIn">
      <i class="fab fa-linkedin-in"></i>
    </a>
  </div>
</footer>

<script>
  function updateDonationCounter() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        const data = JSON.parse(this.responseText);
        const total = parseFloat(data.total);
        document.getElementById('donationTotal').textContent = `$${total.toFixed(2)}`;
        document.getElementById('lastUpdated').textContent = `Last updated: ${data.timestamp}`;
        
        const goal = 10000;
        let percentage = (total / goal) * 100;
        percentage = Math.min(100, Math.max(0, percentage));
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = `${percentage}%`;
        progressBar.textContent = `${percentage.toFixed(1)}%`;
      }
    };
    xhttp.open("GET", "get_total_donations.php", true);
    xhttp.send();
  }

  function toggleFundsBox() {
    const box = document.getElementById('fundsExplanationBox');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
  }

  updateDonationCounter();
  setInterval(updateDonationCounter, 10000);
</script>

</body>
</html>