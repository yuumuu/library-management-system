<?php
$servername = "localhost";
$username = "root";
$password = ""; // or "" if no password in MAMP
$dbname = "library_system"; // Make sure this DB exists in phpMyAdmin

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
