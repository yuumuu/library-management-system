<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Try to delete the student
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['msg'] = "✅ Student deleted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        if ($conn->errno == 1451) {
            $_SESSION['msg'] = "❌ Cannot delete student: They have borrowing history.";
        } else {
            $_SESSION['msg'] = "❌ Error: " . $stmt->error;
        }
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();
