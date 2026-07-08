<?php
include 'db.php';

$total = $conn->query("SELECT SUM(amount) as total FROM donations")->fetch_assoc()['total'] ?? 0;
echo json_encode([
  'total' => $total,
  'timestamp' => date('Y-m-d H:i:s')
]);
?>