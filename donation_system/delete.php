<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

if (!$conn) {
    header('Location: index.php?message=Database connection failed&type=danger');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && isset($_POST['confirm']) && $_POST['confirm'] == '1') {
    $id = intval($_POST['delete_id']);

    $stmt = $conn->prepare("DELETE FROM donations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header('Location: index.php?message=Donation deleted successfully&type=success');
        } else {
            header('Location: index.php?message=No donation found with the specified ID&type=danger');
        }
    } else {
        header('Location: index.php?message=Deletion failed: ' . urlencode($conn->error) . '&type=danger');
    }

    $stmt->close();
    exit;
}

header('Location: index.php?message=Invalid request&type=danger');
exit;
?>