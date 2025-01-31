<?php
// process-attendance.php

include('config/config.php'); // Database connection

$qr_code = $_POST['qr_code']; // The scanned QR code
$attendance_action = $_POST['attendance_action']; // The selected attendance action
$user_id = getUserIdByQrCode($qr_code); // Function to get user ID from QR code

// Check if the user already has an attendance record for today
$stmt = $pdo->prepare("SELECT * FROM tbl_attendance WHERE user_id = ? AND DATE(time_in) = CURDATE()");
$stmt->execute([$user_id]);
$attendance = $stmt->fetch();

if ($attendance) {
    // Update based on the selected action
    if ($attendance_action == 'break_out' && is_null($attendance['break_out'])) {
        $stmt = $pdo->prepare("UPDATE tbl_attendance SET break_out = NOW() WHERE tbl_attendance_id = ?");
        $stmt->execute([$attendance['tbl_attendance_id']]);
    } elseif ($attendance_action == 'break_in' && is_null($attendance['break_in'])) {
        $stmt = $pdo->prepare("UPDATE tbl_attendance SET break_in = NOW() WHERE tbl_attendance_id = ?");
        $stmt->execute([$attendance['tbl_attendance_id']]);
    } elseif ($attendance_action == 'time_out' && is_null($attendance['time_out'])) {
        $stmt = $pdo->prepare("UPDATE tbl_attendance SET time_out = NOW() WHERE tbl_attendance_id = ?");
        $stmt->execute([$attendance['tbl_attendance_id']]);
    }
} else {
    // No attendance record for today, create a new record (Time In)
    if ($attendance_action == 'time_in') {
        $stmt = $pdo->prepare("INSERT INTO tbl_attendance (user_id, time_in) VALUES (?, NOW())");
        $stmt->execute([$user_id]);
    }
}

// Return a response after processing
echo "Attendance updated successfully.";
?>
