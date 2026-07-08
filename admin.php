<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

$welcome = "SpacECE India Foundation";

// Fetch all volunteers
try {
    $stmt = $pdo->query("SELECT id, name, email, username, skills, location, availability, experience, points, profile_pic FROM volunteers");
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching volunteers: " . $e->getMessage();
    $volunteers = [];
}

// Fetch all tasks
try {
    $stmt = $pdo->query("SELECT t.*, v.name as volunteer_name FROM tasks t LEFT JOIN volunteers v ON t.volunteer_id = v.id");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching tasks: " . $e->getMessage();
    $tasks = [];
}

// Fetch all stories
try {
    $stmt = $pdo->query("SELECT s.*, v.name as volunteer_name FROM stories s JOIN volunteers v ON s.volunteer_id = v.id");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching stories: " . $e->getMessage();
    $stories = [];
}

// Fetch all survey submissions
try {
    $stmt = $pdo->query("SELECT * FROM survey_submissions");
    $survey_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching survey submissions: " . $e->getMessage();
    $survey_submissions = [];
}

// Fetch all contact submissions
try {
    $stmt = $pdo->query("SELECT * FROM contact_submissions");
    $contact_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching contact submissions: " . $e->getMessage();
    $contact_submissions = [];
}

// Handle task assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_task'])) {
    $task_title = $_POST['task_title'];
    $description = $_POST['description'] ?? 'No description';
    $required_skills = $_POST['required_skills'] ?? '';
    $type = $_POST['task_type'];

    try {
        // Set volunteer_id to NULL to make task available for claiming
        $stmt = $pdo->prepare("INSERT INTO tasks (volunteer_id, title, description, required_skills, type, status) VALUES (NULL, ?, ?, ?, ?, 'open')");
        $stmt->execute([$task_title, $description, $required_skills, $type]);
        $success = "Task assigned successfully and is available for volunteers to claim!";
        $stmt = $pdo->query("SELECT t.*, v.name as volunteer_name FROM tasks t LEFT JOIN volunteers v ON t.volunteer_id = v.id");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error assigning task: " . $e->getMessage();
    }
}

// Handle task deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'] ?? null;
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        $success = "Task deleted successfully!";
        $stmt = $pdo->query("SELECT t.*, v.name as volunteer_name FROM tasks t LEFT JOIN volunteers v ON t.volunteer_id = v.id");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error deleting task: " . $e->getMessage();
    }
}

// Handle task completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task'])) {
    $task_id = $_POST['task_id'] ?? null;
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ? AND status IN ('open', 'assigned')");
        if ($stmt->execute([$task_id]) && $stmt->rowCount() > 0) {
            $success = "Task marked as completed!";
            $stmt = $pdo->prepare("SELECT volunteer_id FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($task['volunteer_id']) {
                $stmt = $pdo->prepare("UPDATE volunteers SET points = points + 10 WHERE id = ?");
                $stmt->execute([$task['volunteer_id']]);
            }
        } else {
            $error = "Task could not be marked as completed or is already completed.";
        }
        $stmt = $pdo->query("SELECT t.*, v.name as volunteer_name FROM tasks t LEFT JOIN volunteers v ON t.volunteer_id = v.id");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error completing task: " . $e->getMessage();
    }
}

// Handle deletion of survey submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_survey'])) {
    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM survey_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Survey submission deleted successfully!";
        $stmt = $pdo->query("SELECT * FROM survey_submissions");
        $survey_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error deleting survey submission: " . $e->getMessage();
    }
}

// Handle deletion of contact submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact'])) {
    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Contact submission deleted successfully!";
        $stmt = $pdo->query("SELECT * FROM contact_submissions");
        $contact_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error deleting contact submission: " . $e->getMessage();
    }
}

// Handle deletion of volunteer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_volunteer'])) {
    $volunteer_id = $_POST['volunteer_id'];
    try {
        $stmt = $pdo->prepare("SELECT profile_pic FROM volunteers WHERE id = ?");
        $stmt->execute([$volunteer_id]);
        $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($volunteer && $volunteer['profile_pic']) {
            $file_to_delete = 'Uploads/profiles/' . $volunteer['profile_pic'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }
        $stmt = $pdo->prepare("DELETE FROM volunteers WHERE id = ?");
        $stmt->execute([$volunteer_id]);
        $success = "Volunteer deleted successfully!";
        $stmt = $pdo->query("SELECT id, name, email, username, skills, location, availability, experience, points, profile_pic FROM volunteers");
        $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error deleting volunteer: " . $e->getMessage();
    }
}

// Handle deletion of story
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_story'])) {
    $story_id = $_POST['story_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM stories WHERE id = ?");
        $stmt->execute([$story_id]);
        $success = "Story deleted successfully!";
        $stmt = $pdo->query("SELECT s.*, v.name as volunteer_name FROM stories s JOIN volunteers v ON s.volunteer_id = v.id");
        $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error deleting story: " . $e->getMessage();
    }
}

// Handle editing of volunteer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_volunteer'])) {
    $volunteer_id = $_POST['volunteer_id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $availability = trim($_POST['availability'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $points = filter_var($_POST['points'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'default' => 0]]);

    if (empty($name) || empty($email) || empty($username) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid name, email, or username. Please ensure all required fields are filled and email is valid.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE volunteers SET name = ?, email = ?, username = ?, skills = ?, location = ?, availability = ?, experience = ?, points = ? WHERE id = ?");
            $success = $stmt->execute([$name, $email, $username, $skills, $location, $availability, $experience, $points, $volunteer_id]);
            if ($success) {
                $success = "Volunteer updated successfully!";
                $stmt = $pdo->query("SELECT id, name, email, username, skills, location, availability, experience, points, profile_pic FROM volunteers");
                $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = "Failed to update volunteer. No rows affected or database error.";
            }
        } catch (PDOException $e) {
            $error = "Error updating volunteer: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Space ECE</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background: #FFF6E7;
            color: #000;
            overflow-x: hidden;
        }
        .sidebar {
            width: 60px;
            background: #fff;
            color: #000;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            transition: width 0.3s ease;
            overflow: hidden;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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
            opacity: 0.7;
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
            border-bottom: 1px solid #FFAA00;
            padding-bottom: 10px;
            font-weight: 800;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: #000;
            padding: 15px 20px;
            text-decoration: none;
            margin-bottom: 10px;
            background: #fff;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar a:hover {
            background: #FFAA00;
            color: #fff;
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
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar:hover {
                width: 100%;
            }
            .content {
                margin-left: 0;
            }
            .sidebar:hover ~ .content {
                margin-left: 0;
            }
            .dashboard-box {
                max-width: 100%;
            }
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
            background: #FFAA00;
            color: #000;
            padding: 15px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto 20px;
        }
        .dashboard-box h2 {
            color: #000;
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
            background: #fff;
            border: none;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 800;
            color: #000;
            margin-right: 5px;
            transition: background 0.3s ease;
        }
        .tab-button:hover, .tab-button.active {
            background: #FFAA00;
            color: #fff;
        }
        .tab-content {
            display: none;
            padding: 20px;
            background: #fff;
            border-radius: 0 10px 10px 10px;
        }
        .tab-content.active {
            display: block;
        }
        .dashboard-box h3 {
            color: #000;
            margin: 20px 0 10px;
            font-size: 20px;
            font-weight: 800;
        }
        .dashboard-box h4 {
            color: #333;
            margin: 15px 0 10px;
            font-size: 18px;
        }
        .dashboard-box p {
            color: #000;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .volunteer-box {
            background: #FFAA00;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .volunteer-info, .volunteer-tasks {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
        }
        .volunteer-info h4, .volunteer-tasks h4 {
            margin: 0 0 10px 0;
            color: #000;
            font-size: 18px;
        }
        .volunteer-info p, .volunteer-tasks ul li {
            margin: 5px 0;
            color: #000;
            font-size: 14px;
        }
        .volunteer-tasks ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .volunteer-actions {
            margin-top: 10px;
            text-align: right;
        }
        .submission-actions {
            margin-top: 10px;
            text-align: right;
        }
        .task-actions {
            margin-top: 10px;
            text-align: right;
        }
        .volunteer-actions button,
        .submission-actions button,
        .task-actions button {
            padding: 8px 15px;
            margin-left: 5px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        .volunteer-actions button:hover,
        .submission-actions button:hover,
        .task-actions button:hover {
            background: #219653;
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
        .dashboard-box input,
        .dashboard-box textarea,
        .dashboard-box select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            font-size: 14px;
        }
        .dashboard-box button {
            padding: 10px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        .dashboard-box button:hover {
            background: #219653;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: #d9534f;
            text-decoration: none;
            font-weight: 800;
            font-size: 16px;
        }
        .logout a:hover {
            text-decoration: underline;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        footer {
            background: #FFAA00;
            color: #000;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow: auto;
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            cursor: move;
        }
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #000;
        }
        .close:hover {
            color: #FFAA00;
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

            document.querySelectorAll('.delete-survey, .delete-contact, .delete-task, .delete-volunteer, .delete-story').forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.edit-volunteer, .view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.dataset.modal;
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.style.display = 'block';
                        makeDraggable(modal.querySelector('.modal-content'));
                    } else {
                        console.error('Modal not found:', modalId);
                    }
                });
            });

            document.querySelectorAll('.close').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });

            window.onclick = function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            };

            function makeDraggable(element) {
                let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
                element.onmousedown = dragMouseDown;

                function dragMouseDown(e) {
                    e.preventDefault();
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    document.onmouseup = closeDragElement;
                    document.onmousemove = elementDrag;
                }

                function elementDrag(e) {
                    e.preventDefault();
                    pos1 = pos3 - e.clientX;
                    pos2 = pos4 - e.clientY;
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    element.style.top = (element.offsetTop - pos2) + "px";
                    element.style.left = (element.offsetLeft - pos1) + "px";
                }

                function closeDragElement() {
                    document.onmouseup = null;
                    document.onmousemove = null;
                }
            }
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/spaceece_logo.jpg" alt="Space ECE Logo" style="max-height: 50px;">
        </div>
        <h2><?php echo htmlspecialchars($welcome); ?></h2>
        <a href="index.php"><span class="icon">🏠</span><span class="text">Home</span></a>
        <a href="survey.php"><span class="icon">📋</span><span class="text">Survey</span></a>
        <a href="donation_system/welcome.php"><span class="icon">💖</span><span class="text">Donate Us</span></a>
        <a href="volunteer.php"><span class="icon">🙋‍♂️</span><span class="text">Volunteer</span></a>
        <a href="about.php"><span class="icon">ℹ️</span><span class="text">About Us</span></a>
        <a href="admin.php"><span class="icon">🔐</span><span class="text">Admin</span></a>
    </div>
    <div class="content">
        <div class="header">
            <h1>Admin Portal - Space ECE</h1>
            <p>Manage volunteers, tasks, and submissions </p>
        </div>
        <div class="dashboard-box">
            <h2>Admin Portal</h2>
            <?php if (isset($success)) { ?>
                <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <?php } ?>
            <?php if (isset($error)) { ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <div class="tabs">
                <button class="tab-button" data-tab="tab-volunteers">Manage Volunteers</button>
                <button class="tab-button" data-tab="tab-survey-contact">Manage Surveys & Contact Us</button>
            </div>

            <!-- Volunteers Tab -->
            <div id="tab-volunteers" class="tab-content">
                <h3>Manage Volunteers</h3>
                <?php if (!empty($volunteers)) { ?>
                    <?php foreach ($volunteers as $volunteer) { ?>
                        <div class="volunteer-box">
                            <div class="volunteer-info">
                                <h4><?php echo htmlspecialchars($volunteer['name'] ?? 'N/A'); ?></h4>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($volunteer['email'] ?? 'N/A'); ?></p>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($volunteer['username'] ?? 'N/A'); ?></p>
                                <p><strong>Skills:</strong> <?php echo htmlspecialchars($volunteer['skills'] ?? 'N/A'); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($volunteer['location'] ?? 'N/A'); ?></p>
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($volunteer['availability'] ?? 'N/A'); ?></p>
                                <p><strong>Experience:</strong> <?php echo htmlspecialchars($volunteer['experience'] ?? 'N/A'); ?></p>
                                <p><strong>Points:</strong> <?php echo $volunteer['points'] ?? '0'; ?></p>
                                <?php if ($volunteer['profile_pic']) { ?>
                                    <p><strong>Profile Pic:</strong> <img src="Uploads/profiles/<?php echo htmlspecialchars($volunteer['profile_pic']); ?>?t=<?php echo time(); ?>" alt="Profile" style="max-width: 50px; max-height: 50px; border-radius: 5px;"></p>
                                <?php } else { ?>
                                    <p><strong>Profile Pic:</strong> Not uploaded</p>
                                <?php } ?>
                            </div>
                            <div class="volunteer-tasks">
                                <h4>Tasks</h4>
                                <ul>
                                    <?php
                                    $volunteer_tasks = array_filter($tasks, fn($t) => $t['volunteer_id'] == $volunteer['id']);
                                    if (!empty($volunteer_tasks)) {
                                        foreach ($volunteer_tasks as $task) { ?>
                                            <li>
                                                <?php echo htmlspecialchars($task['title'] ?? 'N/A'); ?> (Type: <?php echo htmlspecialchars($task['type'] ?? 'N/A'); ?>, Status: <?php echo htmlspecialchars($task['status'] ?? 'N/A'); ?>)
                                                <div class="task-actions">
                                                    <?php if ($task['status'] !== 'completed') { ?>
                                                        <form method="post" action="" style="display:inline;">
                                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                            <button type="submit" name="complete_task">Mark as Complete</button>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li>No tasks assigned.</li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="volunteer-actions">
                                <form method="post" action="">
                                    <input type="hidden" name="volunteer_id" value="<?php echo $volunteer['id']; ?>">
                                    <select name="task_type" required>
                                        <option value="Administrative Support">Administrative Support</option>
                                        <option value="Fundraising and Outreach">Fundraising and Outreach</option>
                                        <option value="Education and Training">Education and Training</option>
                                        <option value="Community and Social Services">Community and Social Services</option>
                                        <option value="Environmental Conservation">Environmental Conservation</option>
                                    </select>
                                    <select name="task_title" required>
                                        <optgroup label="Administrative Support - Office Management">
                                            <option value="Answer and route incoming calls">Answer and route incoming calls</option>
                                            <option value="Organize and file documents">Organize and file documents</option>
                                            <option value="Update contact lists in the database">Update contact lists in the database</option>
                                            <option value="Prepare meeting agendas">Prepare meeting agendas</option>
                                            <option value="Take minutes during meetings">Take minutes during meetings</option>
                                            <option value="Schedule appointments for staff">Schedule appointments for staff</option>
                                            <option value="Manage email correspondence">Manage email correspondence</option>
                                            <option value="Process incoming mail">Process incoming mail</option>
                                            <option value="Maintain inventory records">Maintain inventory records</option>
                                            <option value="Assist with data entry">Assist with data entry</option>
                                        </optgroup>
                                        <optgroup label="Fundraising and Outreach - Event Planning">
                                            <option value="Design flyers for fundraising events">Design flyers for fundraising events</option>
                                            <option value="Contact potential donors via phone">Contact potential donors via phone</option>
                                            <option value="Write grant proposal drafts">Write grant proposal drafts</option>
                                            <option value="Organize charity auctions">Organize charity auctions</option>
                                            <option value="Set up online crowdfunding campaigns">Set up online crowdfunding campaigns</option>
                                        </optgroup>
                                        <optgroup label="Fundraising and Outreach - Promotion">
                                            <option value="Distribute promotional materials">Distribute promotional materials</option>
                                            <option value="Host virtual fundraising webinars">Host virtual fundraising webinars</option>
                                            <option value="Coordinate sponsor thank-you letters">Coordinate sponsor thank-you letters</option>
                                            <option value="Plan community awareness walks">Plan community awareness walks</option>
                                            <option value="Collect donations at events">Collect donations at events</option>
                                        </optgroup>
                                        <optgroup label="Education and Training - Tutoring">
                                            <option value="Tutor children in basic math">Tutor children in basic math</option>
                                            <option value="Teach English to non-native speakers">Teach English to non-native speakers</option>
                                            <option value="Assist with homework clubs">Assist with homework clubs</option>
                                            <option value="Lead storytelling sessions">Lead storytelling sessions</option>
                                            <option value="Organize educational workshops">Organize educational workshops</option>
                                        </optgroup>
                                        <optgroup label="Education and Training - Material Preparation">
                                            <option value="Prepare teaching materials">Prepare teaching materials</option>
                                            <option value="Mentor young volunteers">Mentor young volunteers</option>
                                            <option value="Conduct skill-building classes">Conduct skill-building classes</option>
                                            <option value="Facilitate parent education sessions">Facilitate parent education sessions</option>
                                            <option value="Support online learning platforms">Support online learning platforms</option>
                                        </optgroup>
                                        <optgroup label="Community and Social Services - Support Services">
                                            <option value="Deliver meals to homebound individuals">Deliver meals to homebound individuals</option>
                                            <option value="Organize clothing drives">Organize clothing drives</option>
                                            <option value="Assist at food banks">Assist at food banks</option>
                                            <option value="Provide companionship to seniors">Provide companionship to seniors</option>
                                            <option value="Help with disaster relief efforts">Help with disaster relief efforts</option>
                                        </optgroup>
                                        <optgroup label="Community and Social Services - Community Engagement">
                                            <option value="Support refugee integration programs">Support refugee integration programs</option>
                                            <option value="Organize community clean-up days">Organize community clean-up days</option>
                                            <option value="Assist with child care services">Assist with child care services</option>
                                            <option value="Facilitate support group meetings">Facilitate support group meetings</option>
                                            <option value="Coordinate toy distribution events">Coordinate toy distribution events</option>
                                        </optgroup>
                                        <optgroup label="Environmental Conservation - Conservation Activities">
                                            <option value="Plant trees in local parks">Plant trees in local parks</option>
                                            <option value="Clean up beaches or rivers">Clean up beaches or rivers</option>
                                            <option value="Monitor wildlife habitats">Monitor wildlife habitats</option>
                                            <option value="Maintain garden beds">Maintain garden beds</option>
                                            <option value="Organize recycling drives">Organize recycling drives</option>
                                        </optgroup>
                                        <optgroup label="Environmental Conservation - Awareness">
                                            <option value="Educate on sustainable practices">Educate on sustainable practices</option>
                                            <option value="Assist with reforestation projects">Assist with reforestation projects</option>
                                            <option value="Track environmental data">Track environmental data</option>
                                            <option value="Build birdhouses or feeders">Build birdhouses or feeders</option>
                                            <option value="Promote energy conservation awareness">Promote energy conservation awareness</option>
                                        </optgroup>
                                    </select>
                                    <input type="text" name="description" placeholder="Description (optional)">
                                    <input type="text" name="required_skills" placeholder="Required Skills (optional)">
                                    <button type="submit" name="assign_task">Assign Task</button>
                                </form>
                                <button class="view-details" data-modal="view-volunteer-<?php echo $volunteer['id']; ?>">View Details</button>
                                <button class="edit-volunteer" data-modal="edit-volunteer-<?php echo $volunteer['id']; ?>">Edit</button>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="volunteer_id" value="<?php echo $volunteer['id']; ?>">
                                    <button type="submit" name="delete_volunteer" class="delete-volunteer">Delete</button>
                                </form>
                            </div>
                        </div>
                        <!-- Edit Volunteer Modal -->
                        <div id="edit-volunteer-<?php echo $volunteer['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close">×</span>
                                <h3>Edit Volunteer: <?php echo htmlspecialchars($volunteer['name'] ?? 'N/A'); ?></h3>
                                <form method="post" action="">
                                    <input type="hidden" name="volunteer_id" value="<?php echo $volunteer['id']; ?>">
                                    <label for="name-<?php echo $volunteer['id']; ?>">Name:</label>
                                    <input type="text" id="name-<?php echo $volunteer['id']; ?>" name="name" value="<?php echo htmlspecialchars($volunteer['name'] ?? ''); ?>" required>
                                    <label for="email-<?php echo $volunteer['id']; ?>">Email:</label>
                                    <input type="email" id="email-<?php echo $volunteer['id']; ?>" name="email" value="<?php echo htmlspecialchars($volunteer['email'] ?? ''); ?>" required>
                                    <label for="username-<?php echo $volunteer['id']; ?>">Username:</label>
                                    <input type="text" id="username-<?php echo $volunteer['id']; ?>" name="username" value="<?php echo htmlspecialchars($volunteer['username'] ?? ''); ?>" required>
                                    <label for="skills-<?php echo $volunteer['id']; ?>">Skills:</label>
                                    <input type="text" id="skills-<?php echo $volunteer['id']; ?>" name="skills" value="<?php echo htmlspecialchars($volunteer['skills'] ?? ''); ?>">
                                    <label for="location-<?php echo $volunteer['id']; ?>">Location:</label>
                                    <input type="text" id="location-<?php echo $volunteer['id']; ?>" name="location" value="<?php echo htmlspecialchars($volunteer['location'] ?? ''); ?>">
                                    <label for="availability-<?php echo $volunteer['id']; ?>">Availability:</label>
                                    <input type="text" id="availability-<?php echo $volunteer['id']; ?>" name="availability" value="<?php echo htmlspecialchars($volunteer['availability'] ?? ''); ?>">
                                    <label for="experience-<?php echo $volunteer['id']; ?>">Experience:</label>
                                    <textarea id="experience-<?php echo $volunteer['id']; ?>" name="experience"><?php echo htmlspecialchars($volunteer['experience'] ?? ''); ?></textarea>
                                    <label for="points-<?php echo $volunteer['id']; ?>">Points:</label>
                                    <input type="number" id="points-<?php echo $volunteer['id']; ?>" name="points" value="<?php echo $volunteer['points'] ?? 0; ?>" min="0" required>
                                    <button type="submit" name="edit_volunteer">Save Changes</button>
                                </form>
                            </div>
                        </div>
                        <!-- View Details Modal -->
                        <div id="view-volunteer-<?php echo $volunteer['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close">×</span>
                                <h3>Volunteer Details: <?php echo htmlspecialchars($volunteer['name'] ?? 'N/A'); ?></h3>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($volunteer['email'] ?? 'N/A'); ?></p>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($volunteer['username'] ?? 'N/A'); ?></p>
                                <p><strong>Skills:</strong> <?php echo htmlspecialchars($volunteer['skills'] ?? 'N/A'); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($volunteer['location'] ?? 'N/A'); ?></p>
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($volunteer['availability'] ?? 'N/A'); ?></p>
                                <p><strong>Experience:</strong> <?php echo htmlspecialchars($volunteer['experience'] ?? 'N/A'); ?></p>
                                <p><strong>Points:</strong> <?php echo $volunteer['points'] ?? '0'; ?></p>
                                <?php if ($volunteer['profile_pic']) { ?>
                                    <p><strong>Profile Pic:</strong> <img src="Uploads/profiles/<?php echo htmlspecialchars($volunteer['profile_pic']); ?>?t=<?php echo time(); ?>" alt="Profile" style="max-width: 50px; max-height: 50px; border-radius: 5px;"></p>
                                <?php } else { ?>
                                    <p><strong>Profile Pic:</strong> Not uploaded</p>
                                <?php } ?>
                                <h4>Tasks</h4>
                                <ul>
                                    <?php
                                    $volunteer_tasks = array_filter($tasks, fn($t) => $t['volunteer_id'] == $volunteer['id']);
                                    if (!empty($volunteer_tasks)) {
                                        foreach ($volunteer_tasks as $task) { ?>
                                            <li><?php echo htmlspecialchars($task['title'] ?? 'N/A'); ?> (Type: <?php echo htmlspecialchars($task['type'] ?? 'N/A'); ?>, Status: <?php echo htmlspecialchars($task['status'] ?? 'N/A'); ?>)</li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li>No tasks assigned.</li>
                                    <?php } ?>
                                </ul>
                                <h4>Stories</h4>
                                <ul>
                                    <?php
                                    $volunteer_stories = array_filter($stories, fn($s) => $s['volunteer_id'] == $volunteer['id']);
                                    if (!empty($volunteer_stories)) {
                                        foreach ($volunteer_stories as $story) { ?>
                                            <li><?php echo htmlspecialchars($story['title'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($story['content'] ?? 'N/A'); ?> (Posted: <?php echo $story['created_at'] ?? 'N/A'; ?>)</li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li>No stories posted.</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No volunteers found.</p>
                <?php } ?>

                <h3>Manage Tasks</h3>
                <?php if (!empty($tasks)) { ?>
                    <ul>
                        <?php foreach ($tasks as $task) { ?>
                            <li>
                                <span>
                                    <?php echo htmlspecialchars($task['title'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($task['description'] ?? 'N/A'); ?> 
                                    (Assigned to: <?php echo htmlspecialchars($task['volunteer_name'] ?? 'Unassigned'); ?>, 
                                    Type: <?php echo htmlspecialchars($task['type'] ?? 'N/A'); ?>, 
                                    Status: <?php echo htmlspecialchars($task['status'] ?? 'N/A'); ?>)
                                </span>
                                <div class="task-actions">
                                    <form method="post" action="" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" name="delete_task" class="delete-task">Delete</button>
                                    </form>
                                    <?php if ($task['status'] !== 'completed') { ?>
                                        <form method="post" action="" style="display:inline;">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" name="complete_task">Mark as Complete</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>No tasks available.</p>
                <?php } ?>

                <h3>Volunteer Stories</h3>
                <?php if (!empty($stories)) { ?>
                    <ul>
                        <?php foreach ($stories as $story) { ?>
                            <li>
                                <?php echo htmlspecialchars($story['title'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($story['content'] ?? 'N/A'); ?> 
                                (By: <?php echo htmlspecialchars($story['volunteer_name'] ?? 'N/A'); ?>, Posted: <?php echo $story['created_at'] ?? 'N/A'; ?>)
                                <div class="submission-actions">
                                    <form method="post" action="" style="display:inline;">
                                        <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
                                        <button type="button" onclick="alert('Title: <?php echo addslashes(htmlspecialchars($story['title'] ?? 'N/A')); ?>\nContent: <?php echo addslashes(htmlspecialchars($story['content'] ?? 'N/A')); ?>\nBy: <?php echo addslashes(htmlspecialchars($story['volunteer_name'] ?? 'N/A')); ?>\nPosted: <?php echo addslashes(htmlspecialchars($story['created_at'] ?? 'N/A')); ?>')">Read</button>
                                        <button type="submit" name="delete_story" class="delete-story">Delete</button>
                                    </form>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>No stories posted yet.</p>
                <?php } ?>
            </div>

         <!-- Surveys & Contact Us Tab -->
<div id="tab-survey-contact" class="tab-content">
    <h3>Manage Survey Submissions</h3>
    <?php if (!empty($survey_submissions)) { ?>
        <ul>
            <?php foreach ($survey_submissions as $survey) { ?>
                <li>
                    Name: <?php echo htmlspecialchars($survey['name'] ?? 'N/A'); ?> - 
                    Email: <?php echo htmlspecialchars($survey['email'] ?? 'N/A'); ?> - 
                    Feedback: <?php echo htmlspecialchars($survey['feedback'] ?? 'N/A'); ?> - 
                    Location: <?php echo ($survey['latitude'] && $survey['longitude']) ? htmlspecialchars($survey['latitude'] . ', ' . $survey['longitude']) : 'Not provided'; ?> 
                    (Submitted: <?php echo $survey['submission_date'] ?? 'N/A'; ?>)
                    <div class="submission-actions">
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $survey['id']; ?>">
                            <button type="button" onclick="alert('Name: <?php echo addslashes(htmlspecialchars($survey['name'] ?? 'N/A')); ?>\nEmail: <?php echo addslashes(htmlspecialchars($survey['email'] ?? 'N/A')); ?>\nFeedback: <?php echo addslashes(htmlspecialchars($survey['feedback'] ?? 'N/A')); ?>\nLocation: <?php echo ($survey['latitude'] && $survey['longitude']) ? addslashes(htmlspecialchars($survey['latitude'] . ', ' . $survey['longitude'])) : 'Not provided'; ?>\nSubmitted: <?php echo addslashes(htmlspecialchars($survey['submission_date'] ?? 'N/A')); ?>')">Read</button>
                            <button type="submit" name="delete_survey" class="delete-survey">Delete</button>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No survey submissions found.</p>
    <?php } ?>

    <h3>Manage Contact Submissions</h3>
    <?php if (!empty($contact_submissions)) { ?>
        <ul>
            <?php foreach ($contact_submissions as $contact) { ?>
                <li>
                    Name: <?php echo htmlspecialchars($contact['name'] ?? 'N/A'); ?> - 
                    Email: <?php echo htmlspecialchars($contact['email'] ?? 'N/A'); ?> - 
                    Message: <?php echo htmlspecialchars($contact['message'] ?? 'N/A'); ?> 
                    (Submitted: <?php echo $contact['submission_date'] ?? 'N/A'; ?>)
                    <div class="submission-actions">
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                            <button type="button" onclick="alert('Name: <?php echo addslashes(htmlspecialchars($contact['name'] ?? 'N/A')); ?>\nEmail: <?php echo addslashes(htmlspecialchars($contact['email'] ?? 'N/A')); ?>\nMessage: <?php echo addslashes(htmlspecialchars($contact['message'] ?? 'N/A')); ?>\nSubmitted: <?php echo addslashes(htmlspecialchars($contact['submission_date'] ?? 'N/A')); ?>')">Read</button>
                            <button type="submit" name="delete_contact" class="delete-contact">Delete</button>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No contact submissions found.</p>
    <?php } ?>
</div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <footer>
        © 2025 Space ECE. All rights reserved.
    </footer>
</body>
</html>