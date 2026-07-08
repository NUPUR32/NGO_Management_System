<?php
$host = "localhost";
$user = "dsyjiawz_donation_system";
$password = "NGO_MANAGEMENT";
$dbname = "dsyjiawz_donation_system";


$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>