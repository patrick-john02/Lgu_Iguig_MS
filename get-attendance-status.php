<?php
// get-attendance-status.php

include('config/config.php'); // Database connection

$qr_code = $_POST['qr_code']; // The scanned QR code
$user_id = getUserIdByQrCode($qr_code); // Function to get user ID from QR code

// Check if the user already has an attendance record for today
$stmt = $pdo->prepare("SELECT * FROM tbl_attendance WHERE user_id = ? AND DATE(time_in) = CURDATE()");
$stmt->execute([$user_id]);
$attendance = $stmt->fetch();

// Determine what action to return
if ($attendance) {
    if (is_null($attendance['time_in'])) {
        // No Time In yet, so it's Time In
        $attendance_action = 'time_in';
    } elseif (is_null($attendance['break_out'])) {
        // Time In is recorded, but Break Out is missing
        $attendance_action = 'break_out';
    } elseif (is_null($attendance['break_in'])) {
        // Break Out is recorded, but Break In is missing
        $attendance_action = 'break_in';
    } elseif (is_null($attendance['time_out'])) {
        // Break In is recorded, but Time Out is missing
        $attendance_action = 'time_out';
    }
} else {
    // No attendance record for today, so it's Time In
    $attendance_action = 'time_in';
}

// Return the action to the frontend
echo json_encode(['attendance_action' => $attendance_action]);
?>
