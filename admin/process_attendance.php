<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php'); // Ensure this initializes $pdo correctly

// Read the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

$action = $data['action'] ?? null;
$employee_id = $data['user_id'] ?? null;

if (!$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Employee ID is required.']);
    exit();
}

$timestamp = date('Y-m-d H:i:s'); // Get the current timestamp

try {
    switch ($action) {
        case 'time_in':
            // Check if the user already clocked in today
            $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = :employee_id AND date = CURDATE()");
            $stmt->execute(['employee_id' => $employee_id]);
            $attendance = $stmt->fetch();

            if ($attendance) {
                echo json_encode(['success' => false, 'message' => 'User has already checked in today.']);
                exit();
            }

            // Insert a new attendance record for time_in
            $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, check_in) VALUES (:employee_id, CURDATE(), :timestamp)");
            $stmt->execute(['employee_id' => $employee_id, 'timestamp' => $timestamp]);
            echo json_encode(['success' => true, 'message' => "Time In recorded for Employee ID: $employee_id at $timestamp"]);
            break;

        case 'time_out':
            // Update the check-out time if user is clocked in
            $stmt = $pdo->prepare("UPDATE attendance SET check_out = :timestamp WHERE user_id = :employee_id AND date = CURDATE() AND check_out IS NULL");
            $stmt->execute(['employee_id' => $employee_id, 'timestamp' => $timestamp]);
            echo json_encode(['success' => true, 'message' => "Time Out recorded for Employee ID: $employee_id at $timestamp"]);
            break;

        case 'break_in':
            // Update break-in time
            $stmt = $pdo->prepare("UPDATE attendance SET break_in = :timestamp WHERE user_id = :employee_id AND date = CURDATE() AND break_in IS NULL");
            $stmt->execute(['employee_id' => $employee_id, 'timestamp' => $timestamp]);
            echo json_encode(['success' => true, 'message' => "Break In recorded for Employee ID: $employee_id at $timestamp"]);
            break;

        case 'break_out':
            // Update break-out time
            $stmt = $pdo->prepare("UPDATE attendance SET break_out = :timestamp WHERE user_id = :employee_id AND date = CURDATE() AND break_out IS NULL");
            $stmt->execute(['employee_id' => $employee_id, 'timestamp' => $timestamp]);
            echo json_encode(['success' => true, 'message' => "Break Out recorded for Employee ID: $employee_id at $timestamp"]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
