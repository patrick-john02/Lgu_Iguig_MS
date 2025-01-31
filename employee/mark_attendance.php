<?php
session_start();
include('../config/config.php'); // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form was submitted to mark attendance
if (isset($_POST['mark_attendance'])) {
    $session_id = $_POST['session_id'];

    // Check if the attendance has already been marked
    $attendance_check_sql = "SELECT * FROM session_attendance WHERE session_id = :session_id AND user_id = :user_id";
    $attendance_stmt = $pdo->prepare($attendance_check_sql);
    $attendance_stmt->bindParam(':session_id', $session_id);
    $attendance_stmt->bindParam(':user_id', $user_id);
    $attendance_stmt->execute();
    $attendance = $attendance_stmt->fetch(PDO::FETCH_ASSOC);

    if ($attendance) {
        // If attendance is already marked, redirect back
        header("Location: employee_dashboard.php?error=already_marked");
        exit();
    }

    // Insert attendance record into the session_attendance table
    $status = 'Present'; // You can change this based on some logic or input
    $insert_sql = "INSERT INTO session_attendance (session_id, user_id, status) VALUES (:session_id, :user_id, :status)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->bindParam(':session_id', $session_id);
    $insert_stmt->bindParam(':user_id', $user_id);
    $insert_stmt->bindParam(':status', $status);
    
    if ($insert_stmt->execute()) {
        // Attendance marked successfully
        header("Location: atttend_session.php?success=attendance_marked");
    } else {
        // Error occurred while marking attendance
        header("Location: atttend_session.php?error=attendance_failed");
    }
}
?>
