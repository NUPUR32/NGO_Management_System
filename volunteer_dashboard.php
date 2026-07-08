<?php
ob_start(); // Start output buffering
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if volunteer is logged in
if (!isset($_SESSION['volunteer_id'])) {
    header("Location: volunteer_signin.php");
    ob_end_flush();
    exit;
}

try {
    include 'db_connect.php';

    // Test database connection
    $pdo->query("SELECT 1");

    $welcome = "Welcome, " . htmlspecialchars($_SESSION['volunteer_name']);
    $volunteer_id = $_SESSION['volunteer_id'];

    // Fetch volunteer data including role
    $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
    $stmt->execute([$volunteer_id]);
    $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$volunteer) {
        throw new Exception("Volunteer not found.");
    }
    $is_admin = isset($volunteer['role']) && $volunteer['role'] === 'admin';

    // Ensure upload directories exist
    $upload_dir = 'Uploads/';
    $story_dir = 'Uploads/stories/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_dir($story_dir)) {
        mkdir($story_dir, 0755, true);
    }

    // Handle profile picture upload
    $show_upload = !$volunteer['profile_pic'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic']) && $show_upload) {
        $file_name = $volunteer_id . '_' . basename($_FILES['profile_pic']['name']);
        $target_file = $upload_dir . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_file_type, $allowed_types) && $_FILES['profile_pic']['size'] < 5000000) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("UPDATE volunteers SET profile_pic = ? WHERE id = ?");
                $stmt->execute([$file_name, $volunteer_id]);
                $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
                $stmt->execute([$volunteer_id]);
                $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
                $success = "Profile picture uploaded successfully!";
                $show_upload = false;
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type or size too large (max 5MB).";
        }
    }

    // Handle profile picture change request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_pic'])) {
        $show_upload = true;
        $file_to_delete = $upload_dir . $volunteer['profile_pic'];
        if ($volunteer['profile_pic'] && file_exists($file_to_delete)) {
            unlink($file_to_delete);
            $stmt = $pdo->prepare("UPDATE volunteers SET profile_pic = NULL WHERE id = ?");
            $stmt->execute([$volunteer_id]);
            $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
            $stmt->execute([$volunteer_id]);
            $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Handle profile picture removal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_pic'])) {
        $file_to_delete = $upload_dir . $volunteer['profile_pic'];
        if ($volunteer['profile_pic'] && file_exists($file_to_delete)) {
            if (unlink($file_to_delete)) {
                $stmt = $pdo->prepare("UPDATE volunteers SET profile_pic = NULL WHERE id = ?");
                $stmt->execute([$volunteer_id]);
                $volunteer['profile_pic'] = null;
                $success = "Profile picture removed successfully!";
                $show_upload = true;
            } else {
                $error = "Failed to remove the file.";
            }
        } else {
            $error = "No profile picture to remove or file not found.";
        }
    }

    // Handle personal info update
    $edit_mode = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_info'])) {
        $edit_mode = true;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_info'])) {
        $name = $_POST['name'] ?? $volunteer['name'];
        $email = $_POST['email'] ?? $volunteer['email'];
        $username = $_POST['view_details'] ?? $volunteer['username'];
        $skills = $_POST['skills'] ?? $volunteer['skills'];
        $location = $_POST['location'] ?? $volunteer['location'];
        $availability = $_POST['availability'] ?? $volunteer['availability'];
        $experience = $_POST['experience'] ?? $volunteer['experience'];

        $stmt = $pdo->prepare("UPDATE volunteers SET name = ?, email = ?, username = ?, skills = ?, location = ?, availability = ?, experience = ? WHERE id = ?");
        $stmt->execute([$name, $email, $username, $skills, $location, $availability, $experience, $volunteer_id]);
        $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
        $stmt->execute([$volunteer_id]);
        $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['volunteer_name'] = $volunteer['name'];
        $welcome = "Welcome, " . htmlspecialchars($_SESSION['volunteer_name']);
        $success = "Personal info updated successfully!";
        $edit_mode = false;
    }

    // Handle task claiming
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_task'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("UPDATE tasks SET volunteer_id = ?, status = 'assigned' WHERE id = ? AND status = 'open' AND volunteer_id IS NULL");
        if ($stmt->execute([$volunteer_id, $task_id])) {
            if ($stmt->rowCount() > 0) {
                $success = "Task claimed successfully! It is now under Assigned Tasks.";
            } else {
                $error = "Task could not be claimed. It may have been taken or is no longer available.";
            }
        } else {
            $error = "Task could not be claimed. Please try again.";
        }
    }

    // Handle task rejection
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_task'])) {
        $success = "Task rejected. It remains available for other volunteers.";
    }

    // Handle task completion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task'])) {
        $task_id = $_POST['task_id'] ?? null;
        $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ? AND volunteer_id = ? AND status = 'assigned'");
        if ($stmt->execute([$task_id, $volunteer_id]) && $stmt->rowCount() > 0) {
            $success = "Task marked as completed! You earned 10 points.";
            $stmt = $pdo->prepare("UPDATE volunteers SET points = points + 10 WHERE id = ?");
            $stmt->execute([$volunteer_id]);
            $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
            $stmt->execute([$volunteer_id]);
            $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Task could not be marked as completed or is already completed.";
        }
    }

    // Handle video progress
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_watched'])) {
        $video_id = $_POST['video_id'];
        $stmt = $pdo->prepare("INSERT INTO video_progress (volunteer_id, video_id, watched, watched_at) VALUES (?, ?, TRUE, NOW()) ON DUPLICATE KEY UPDATE watched = TRUE, watched_at = NOW()");
        $stmt->execute([$volunteer_id, $video_id]);
        $success = "Video marked as watched!";
    }

    // Fetch all available tasks
    $stmt = $pdo->query("SELECT * FROM tasks WHERE status = 'open' AND volunteer_id IS NULL");
    $available_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch assigned tasks for the volunteer
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE volunteer_id = ? AND status IN ('assigned', 'open')");
    $stmt->execute([$volunteer_id]);
    $assigned_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch stories
    $stmt = $pdo->prepare("SELECT * FROM stories WHERE volunteer_id = ?");
    $stmt->execute([$volunteer_id]);
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch learning resources
    $stmt = $pdo->prepare("SELECT lr.*, vp.watched FROM learning_resources lr LEFT JOIN video_progress vp ON lr.id = vp.video_id AND vp.volunteer_id = ? ORDER BY lr.category, lr.title");
    $stmt->execute([$volunteer_id]);
    $learning_resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group learning resources by category
    $categories = [];
    foreach ($learning_resources as $resource) {
        $categories[$resource['category']][] = $resource;
    }

    // Fetch video progress summary
    $stmt = $pdo->prepare("SELECT COUNT(*) as watched_count FROM video_progress WHERE volunteer_id = ? AND watched = TRUE");
    $stmt->execute([$volunteer_id]);
    $watched_count = $stmt->fetch(PDO::FETCH_ASSOC)['watched_count'];
    $total_videos = count($learning_resources);

    // Handle story submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_title'])) {
        $title = $_POST['story_title'];
        $content = $_POST['story_content'];
        $media_file = null;

        if (isset($_FILES['story_media']) && $_FILES['story_media']['size'] > 0) {
            $file_name = $volunteer_id . '_' . time() . '_' . basename($_FILES['story_media']['name']);
            $target_file = $story_dir . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];

            if (in_array($file_type, $allowed_types) && $_FILES['story_media']['size'] < 10000000) {
                if (move_uploaded_file($_FILES['story_media']['tmp_name'], $target_file)) {
                    $media_file = $file_name;
                } else {
                    $error = "Error uploading media file.";
                }
            } else {
                $error = "Invalid file type or size too large (max 10MB).";
            }
        }

        if (!isset($error)) {
            $stmt = $pdo->prepare("INSERT INTO stories (volunteer_id, title, content, media_file) VALUES (?, ?, ?, ?)");
            $stmt->execute([$volunteer_id, $title, $content, $media_file]);
            $success = "Story submitted successfully!";
            $stmt = $pdo->prepare("SELECT * FROM stories WHERE volunteer_id = ?");
            $stmt->execute([$volunteer_id]);
            $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    $error = "Error loading dashboard: Database connection failed.";
    error_log("Dashboard error: " . $e->getMessage(), 3, 'errors.log');
} catch (Exception $e) {
    $error = "Error loading dashboard: " . $e->getMessage();
    error_log("Dashboard error: " . $e->getMessage(), 3, 'errors.log');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard - Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
            overflow-x: hidden;
        }
        .sidebar {
            width: 60px;
            background: linear-gradient(145deg, #FFFFFF, #F5F5F5);
            color: #000000;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            transition: width 0.3s ease;
            overflow: hidden;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar:hover {
            width: 250px;
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar h2 img {
            max-height: 50px;
            margin-right: 5px;
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
            transform: translateX(5px);
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
            background: linear-gradient(90deg, #FFAA00, #FFB733);
            color: #000000;
            padding: 15px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            font-size: 36px;
            font-weight: 800;
            margin: 0;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
        }
        .dashboard-box {
            background: linear-gradient(135deg, #FFAA00, #FFD700);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto 20px;
        }
        .dashboard-box h2 {
            color: #000000;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            justify-content: center;
        }
        .tab-button {
            padding: 10px 25px;
            background: #FFFFFF;
            border: none;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 800;
            color: #000000;
            margin-right: 5px;
            transition: background 0.3s ease;
        }
        .tab-button:hover, .tab-button.active {
            background: #FFAA00;
            color: #FFFFFF;
        }
        .tab-content {
            display: none;
            padding: 20px;
            background: #FFFFFF;
            border-radius: 0 10px 10px 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .tab-content.active {
            display: block;
        }
        .dashboard-box h3 {
            color: #000000;
            margin: 20px 0 10px;
            font-size: 20px;
            font-weight: 800;
        }
        .dashboard-box h4 {
            color: #333333;
            margin: 15px 0 10px;
            font-size: 18px;
        }
        .dashboard-box p {
            color: #000000;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .dashboard-box ul {
            list-style: none;
            padding: 0;
        }
        .dashboard-box ul li {
            background: #FFF6E7;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            position: relative;
        }
        .dashboard-box form {
            display: flex;
            flex-direction: column;
        }
        .dashboard-box input, .dashboard-box textarea, .dashboard-box select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #DDDDDD;
            border-radius: 5px;
            background: #F9F9F9;
            font-size: 14px;
        }
        .dashboard-box button {
            padding: 10px;
            background: #27AE60;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        .dashboard-box button:hover {
            background: #219653;
        }
        .task-actions, .story-actions {
            margin-top: 10px;
            text-align: right;
        }
        .task-actions button, .story-actions button {
            margin-left: 5px;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background: #D4EDDA;
            color: #155724;
        }
        .message.error {
            background: #F8D7DA;
            color: #721C24;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
        }
        .modal-content {
            background: #FFFFFF;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #000000;
        }
        .close:hover {
            color: #FFAA00;
        }
        .profile-pic {
            max-width: 100px;
            max-height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .media-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .media-preview video {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.dataset.tab).classList.add('active');
                });
            });

            if (tabs.length > 0) {
                tabs[0].classList.add('active');
                contents[0].classList.add('active');
            }

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        this.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.close').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2><img src="images/spaceece_logo.jpg" alt="Space ECE Logo"><?php echo htmlspecialchars($welcome); ?></h2>
        
        <?php if ($is_admin) { ?>
            <a href="admin.php"><span class="icon"><i class="fas fa-user-shield"></i></span><span class="text">Admin Portal</span></a>
        <?php } ?>
         <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="logout.php"><span class="icon"><i class="fas fa-sign-out-alt"></i></span><span class="text">Logout</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>Volunteer Dashboard</h1>
            <p><?php echo htmlspecialchars($welcome); ?></p>
        </div>
        <?php if (isset($success)) { ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>
        <?php if (isset($error)) { ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <div class="dashboard-box">
            <h2>Volunteer Dashboard</h2>
            <div class="tabs">
                <button class="tab-button" data-tab="tab-profile">Profile</button>
                <button class="tab-button" data-tab="tab-tasks">Tasks</button>
                <button class="tab-button" data-tab="tab-learning">Learning Resources</button>
                <button class="tab-button" data-tab="tab-stories">Stories</button>
            </div>
            <div id="tab-profile" class="tab-content">
                <h3>Your Profile</h3>
                <?php if ($show_upload) { ?>
                    <form method="post" enctype="multipart/form-data">
                        <h4>Upload Profile Picture</h4>
                        <input type="file" name="profile_pic" accept="image/*" required>
                        <button type="submit">Upload</button>
                    </form>
                <?php } else { ?>
                    <img src="Uploads/<?php echo htmlspecialchars($volunteer['profile_pic']); ?>?t=<?php echo time(); ?>" alt="Profile Picture" class="profile-pic">
                    <form method="post" style="display: inline;">
                        <button type="submit" name="change_pic">Change Picture</button>
                    </form>
                    <form method="post" style="display: inline;">
                        <button type="submit" name="remove_pic">Remove Picture</button>
                    </form>
                <?php } ?>
                <h4>Personal Information</h4>
                <?php if ($edit_mode) { ?>
                    <form method="post">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($volunteer['name']); ?>" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($volunteer['email']); ?>" required>
                        <input type="text" name="view_details" value="<?php echo htmlspecialchars($volunteer['username']); ?>" required>
                        <input type="text" name="skills" value="<?php echo htmlspecialchars($volunteer['skills']); ?>">
                        <input type="text" name="location" value="<?php echo htmlspecialchars($volunteer['location']); ?>">
                        <input type="text" name="availability" value="<?php echo htmlspecialchars($volunteer['availability']); ?>">
                        <textarea name="experience"><?php echo htmlspecialchars($volunteer['experience']); ?></textarea>
                        <button type="submit" name="save_info">Save</button>
                    </form>
                <?php } else { ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($volunteer['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($volunteer['email']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($volunteer['username']); ?></p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($volunteer['skills'] ?: 'Not specified'); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($volunteer['location'] ?: 'Not specified'); ?></p>
                    <p><strong>Availability:</strong> <?php echo htmlspecialchars($volunteer['availability'] ?: 'Not specified'); ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($volunteer['experience'] ?: 'Not specified'); ?></p>
                    <p><strong>Points:</strong> <?php echo $volunteer['points'] ?: '0'; ?></p>
                    <form method="post">
                        <button type="submit" name="edit_info">Edit Info</button>
                    </form>
                <?php } ?>
            </div>
            <div id="tab-tasks" class="tab-content">
                <h3>Available Tasks</h3>
                <ul>
                    <?php foreach ($available_tasks as $task) { ?>
                        <li>
                            <?php echo htmlspecialchars($task['title']); ?> - <?php echo htmlspecialchars($task['description']); ?> (Type: <?php echo htmlspecialchars($task['type']); ?>, Skills: <?php echo htmlspecialchars($task['required_skills'] ?: 'None'); ?>)
                            <div class="task-actions">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" name="claim_task">Claim</button>
                                    <button type="submit" name="reject_task">Reject</button>
                                </form>
                            </div>
                        </li>
                    <?php } ?>
                    <?php if (empty($available_tasks)) { ?>
                        <li>No available tasks at the moment.</li>
                    <?php } ?>
                </ul>
                <h3>Assigned Tasks</h3>
                <ul>
                    <?php foreach ($assigned_tasks as $task) { ?>
                        <li>
                            <?php echo htmlspecialchars($task['title']); ?> - <?php echo htmlspecialchars($task['description']); ?> (Type: <?php echo htmlspecialchars($task['type']); ?>, Status: <?php echo htmlspecialchars($task['status']); ?>)
                            <?php if ($task['status'] !== 'completed') { ?>
                                <div class="task-actions">
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" name="complete_task">Mark as Completed</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if (empty($assigned_tasks)) { ?>
                        <li>No assigned tasks at the moment.</li>
                    <?php } ?>
                </ul>
            </div>
            <div id="tab-learning" class="tab-content">
                <h3>Learning Resources</h3>
                <p>Watched <?php echo $watched_count; ?> out of <?php echo $total_videos; ?> videos.</p>
                <?php foreach ($categories as $category => $resources) { ?>
                    <h4><?php echo htmlspecialchars($category); ?></h4>
                    <ul>
                        <?php foreach ($resources as $resource) { ?>
                            <li>
                                <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank"><?php echo htmlspecialchars($resource['title']); ?></a>
                                (<?php echo $resource['watched'] ? 'Watched' : 'Not Watched'; ?>)
                                <?php if (!$resource['watched']) { ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="video_id" value="<?php echo $resource['id']; ?>">
                                        <button type="submit" name="mark_watched">Mark as Watched</button>
                                    </form>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <?php if (empty($categories)) { ?>
                    <p>No learning resources available.</p>
                <?php } ?>
            </div>
            <div id="tab-stories" class="tab-content">
                <h3>Your Stories</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="text" name="story_title" placeholder="Story Title" required>
                    <textarea name="story_content" placeholder="Share your story..." required></textarea>
                    <input type="file" name="story_media" accept="image/*,video/*">
                    <button type="submit">Submit Story</button>
                </form>
                <ul>
                    <?php foreach ($stories as $story) { ?>
                        <li>
                            <?php echo htmlspecialchars($story['title']); ?> - <?php echo htmlspecialchars($story['content']); ?> (Posted: <?php echo $story['created_at']; ?>)
                            <?php if ($story['media_file']) { ?>
                                <div class="media-preview">
                                    <?php
                                    $file_type = strtolower(pathinfo($story['media_file'], PATHINFO_EXTENSION));
                                    if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                                        <img src="Uploads/stories/<?php echo htmlspecialchars($story['media_file']); ?>?t=<?php echo time(); ?>" alt="Story Media">
                                    <?php } elseif (in_array($file_type, ['mp4', 'mov', 'avi'])) { ?>
                                        <video controls>
                                            <source src="Uploads/stories/<?php echo htmlspecialchars($story['media_file']); ?>?t=<?php echo time(); ?>" type="video/<?php echo $file_type; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if (empty($stories)) { ?>
                        <li>No stories posted yet.</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>
<?php ob_end_flush(); ?>