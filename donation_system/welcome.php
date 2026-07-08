<?php
// No contact form handling needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Donation Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
      background: #FFF6E7;
      color: #000000;
      line-height: 1.6;
    }
    .hero {
      height: 100vh;
      text-align: center;
      padding-top: 150px;
      position: relative;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.1);
      z-index: 1;
    }
    .logo {
      position: fixed;
      top: 20px;
      left: 30px;
      z-index: 20;
      transition: transform 0.3s ease;
    }
    .logo img {
      height: 70px;
      filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
    }
    .logo:hover {
      transform: scale(1.1);
    }
    .title-box {
      background: #FFFFFF;
      color: #000000;
      padding: 15px 40px;
      display: inline-block;
      border-radius: 10px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      font-size: 2rem;
      margin-bottom: 30px;
      text-transform: uppercase;
      letter-spacing: 2px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .welcome-box {
      max-width: 900px;
      margin: 0 auto;
      background: rgba(255, 170, 0, 0.9);
      border-radius: 10px;
      padding: 60px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 2;
    }
    .welcome-text h1 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      font-size: 5rem;
      letter-spacing: 3px;
      color: #000000;
      text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
      animation: slideUp 1s ease;
    }
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(50px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .welcome-text h4 {
      font-size: 1.5rem;
      color: #000000;
      margin-bottom: 20px;
    }
    .welcome-text p {
      color: #000000;
    }
    .btn-start {
      background: #27ae60;
      color: #FFFFFF;
      border-radius: 5px;
      padding: 15px 35px;
      font-weight: 600;
      border: none;
      margin-top: 30px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    .btn-start:hover {
      background: #219653;
      transform: translateY(-5px);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }
    .terms {
      margin-top: 25px;
      font-size: 1rem;
      color: #000000;
    }
    .terms a {
      color: #27ae60;
      text-decoration: underline;
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
    }
    .footer-text {
      margin: 0;
    }
    .footer-links {
      display: flex;
      gap: 15px;
    }
    .footer-links a {
      color: #000000;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .footer-links a:hover {
      color: #333333;
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
      .welcome-text h1 {
        font-size: 3rem;
      }
      .welcome-box {
        padding: 30px;
      }
      footer {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

<!-- Logo -->
<div class="logo">
  <img src="Uploads/WhatsApp Image 2025-06-28 at 21.00.20_cb760bc2.jpg" alt="Logo">
</div>

<!-- Home Section -->
<section id="home" class="hero">
  <div class="title-box">DONATION MANAGEMENT SYSTEM</div>
  <div class="welcome-box">
    <div class="welcome-text">
      <h1>WELCOME</h1>
      <h4>to our Donation Management Community</h4>
      <p class="mt-3">A place to manage donations, donors, and community impact efficiently with cutting-edge technology.</p>
      <form method="get" action="login.php" onsubmit="return checkTerms()">
        <div class="terms">
          <input type="checkbox" id="agree" required>
          <label for="agree">
            I agree to the <a href="#" onclick="alert('Terms and Conditions: By using this platform, you agree to responsibly manage donor data, respect privacy, and comply with all applicable laws. Misuse will result in revoked access.');">Terms and Conditions</a>
          </label>
        </div>
        <button type="submit" class="btn btn-start">Join Us Now</button>
      </form>
    </div>
  </div>
</section>

<!-- Footer -->
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

<!-- Scripts -->
<script>
function checkTerms() {
  const checkbox = document.getElementById('agree');
  if (!checkbox.checked) {
    alert("You must agree to the terms and conditions.");
    return false;
  }
  return true;
}
</script>
</body>
</html>
