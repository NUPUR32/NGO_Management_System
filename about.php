<?php
session_start();
$welcome = "SpacECE India foundation";

include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
            overflow-x: hidden;
        }
        .sidebar {
            width: 60px;
            background: #FFFFFF;
            color: #000000;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            transition: width 0.3s ease;
            overflow: hidden;
        }
        .sidebar:hover {
            width: 250px;
        }
        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar .logo img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .sidebar:hover .logo img {
            opacity: 1;
        }
        .sidebar h2 {
            font-size: 14px;
            text-align: center;
            margin: 0 0 20px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: #000000;
            padding: 15px 20px;
            text-decoration: none;
            margin-bottom: 10px;
            background: #FFFFFF;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar a:hover {
            background: #F0F0F0;
        }
        .sidebar a .icon {
            margin-right: 10px;
            opacity: 1;
            flex-shrink: 0;
        }
        .sidebar a .text {
            margin-left: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar:hover a .text {
            opacity: 1;
        }
        .content {
            margin-left: 60px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover ~ .content {
            margin-left: 250px;
        }
        .header {
            background: rgba(255, 170, 0, 0.9);
            color: #000000;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 800;
        }
        .about-box {
            background: rgba(255, 170, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto 20px;
        }
        .about-box h2 {
            color: #000000;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .about-box h3 {
            color: #000000;
            margin: 20px 0 10px 0;
            font-size: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        .about-box p {
            color: #000000;
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.5;
        }
        .about-image {
            max-width: 100%;
            width: 800px;
            height: 400px;
            border-radius: 5px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            object-fit: cover;
        }
        footer {
            background: rgba(255, 170, 0, 0.9);
            color: #000000;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/spaceece_logo.jpg" alt="Space ECE Logo" style="max-height: 50px;">
        </div>
        <h2><?php echo $welcome; ?></h2>
        <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="admin.php"><span class="icon">🔐</span><span class="text">Admin</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>About Us - Space ECE</h1>
        </div>
        <div class="about-box">
            <h2>About Us</h2>
            <img src="images/image1.jpg" alt="General overview of Space ECE initiatives" class="about-image">
            <p>Welcome to Space ECE, an initiative dedicated to spreading awareness about Early Childhood Education (ECE). Founded by educationists Aparna and Sachin, we aim to transform the early learning landscape by supporting parents, teachers, and communities across India. With over a decade of experience in child development, child rights, and educational research, we bring expertise to create nurturing environments for children aged 0–8 years. Our work focuses on the social, physical, aesthetic, cognitive, and emotional development of young minds, believing these foundational years shape their future. Through innovative programs and resources, we strive to make learning accessible and engaging.</p>
            <p>Our platform offers regular sessions, video galleries, and training programs to equip parents and educators with the skills to foster child growth. We organize events like the Get-Together with ECE experts from Maharashtra, featuring leaders such as Ms. Madhuri Sahasrabudhe and Dr. Suneeta Kulkarni, to set the tone for serious efforts in the ECE domain. Our YouTube channel, SpacTube, provides educational content, while initiatives like Home-as-a-Learning-SPACE encourage experiential learning at home. Join us in our journey to build a future where every child thrives through quality early education.</p>
        </div>
        <div class="about-box">
            <h2>Our Mission and Vision</h2>
            <h3>Our Mission</h3>
            <img src="images/image2.jpg" alt="Education as a human right for young children" class="about-image">
            <p>At Space ECE, our mission is to spread awareness about Early Childhood Education and empower parents and teachers to nurture young children effectively. We aim to provide a platform where caregivers can learn essential parenting skills, such as managing emotions, utilizing children’s energy creatively, and encouraging their development through tailored sessions. With initiatives like regular workshops, we address key areas including diet plans, physical activity, and emotional growth, ensuring holistic development for children aged 0–8. Our goal is to help every child become a good human being by fostering a supportive learning environment.</p>
            <p>We are committed to offering practical solutions, such as language learning sessions for multilingual households, which have proven valuable for parents like those raising children like Aneesh. Our volunteer-driven approach includes collaboration with ECE experts to deliver high-quality training, enhancing the skills of educators and parents alike. Through our Home-as-a-Learning-SPACE program, we provide resources and activity guides to create stimulating home environments, promoting self-directed learning. We believe that by equipping adults with knowledge, we can break barriers to early education and build resilient, curious young minds.</p>
            <p>Our mission extends to building a community of informed caregivers through online and offline modes, including our video gallery for research purposes. We invite parents to contribute content, fostering a collaborative ecosystem. With leaders like Aparna, with 16 years in child development, and Sachin, with 14 years in educational research, we drive programs that align with child rights and protection. Every effort is designed to ensure children learn healthy discipline and emotional management from an early age, setting them on a path to success without coercive control.</p>
            <h3>Our Vision</h3>
            <img src="images/image3.jpg" alt="Community education initiative for early childhood" class="about-image">
            <p>Our vision at Space ECE is to create a world where every child receives quality Early Childhood Education, shaping a foundation for lifelong learning and success. We envision a future where parents and teachers are empowered with the knowledge and resources to foster social, physical, aesthetic, cognitive, and emotional development in children aged 0–8 years. Inspired by the idea that a vibrant learning environment reduces the need for adult intervention, we aim to enrich surroundings to let children explore and grow according to their inclinations. By 2030, we aspire to impact millions of young lives across India.</p>
            <p>We see a society where homes become dynamic learning spaces through our Home-as-a-Learning-SPACE initiative, supported by educational kits and activity guides. Our vision includes a robust network of ECE experts and parents working together, as demonstrated by our Get-Together events with over 50 specialists from Maharashtra. We aim to expand our SpacTube video library, categorized by developmental domains, to serve as a global resource for researchers and educators. This vision drives us to innovate, using technology and partnerships to reach remote communities and ensure inclusive education.</p>
            <p>We dream of a future where children’s literature in Indian languages meets high standards of quantity and quality, supported by discussions and accessibility initiatives. Our SPACE Academy will continue to train parents and para-teachers, creating a ripple effect of awareness and skill-building. With a focus on sustainability and child autonomy, we strive to be a leader in ECE, leveraging the expertise of founders like Aparna and Sachin to guide this transformation. Our ultimate goal is a generation of confident, resilient individuals ready to thrive in a rapidly changing world.</p>
        </div>
        <div class="about-box">
            <h2>Our Impact</h2>
            <img src="images/image4.jpg" alt="Impact of Space ECE programs on children in India" class="about-image">
            <p>The impact of Space ECE is evident in the lives of countless children and families we’ve touched through our Early Childhood Education initiatives. Over the years, we’ve conducted numerous sessions attended by parents like those learning to manage their child’s emotions and energy, with feedback highlighting improved parenting skills and child engagement. Our Home-as-a-Learning-SPACE program has distributed learning kits to hundreds of households, creating stimulating environments that foster curiosity and exploration. The Get-Together event with over 50 ECE experts from Maharashtra has sparked serious efforts, influencing policy and practice in the region.</p>
            <p>Our SpacTube channel, featuring over 1,000 videos categorized by developmental domains, serves as a rich resource for parents and researchers, with plans for a secured web portal to expand access. These videos, labeled with descriptions, have helped parents like those raising multilingual children, such as Aneesh’s family, understand language development better. Our training programs, led by experts like Aparna and Sachin, have equipped hundreds of educators and parents with practical tools, enhancing child development outcomes. The initiative to invite parent-uploaded content has begun building a collaborative research ecosystem.</p>
            <p>We’ve addressed gaps in children’s literature by initiating discussions on its importance and accessibility, aiming to improve quality in Indian languages. Our focus on healthy discipline and emotional management has empowered children to develop resilience early on, reducing the need for coercive control later. Community engagement has grown, with events and workshops fostering a network of informed caregivers. As we scale up, our impact continues to deepen, driven by a commitment to innovation and inclusion, ensuring every child benefits from a strong educational foundation.</p>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>