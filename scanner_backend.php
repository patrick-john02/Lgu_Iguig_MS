<?php
session_start();
include('../config/config.php');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the 'qr_code_detail' field is provided
    if (!isset($_POST['qr_code_detail']) || empty($_POST['qr_code_detail'])) {
        echo json_encode(['status' => 'error', 'message' => 'QR Code is missing!']);
        exit;
    }

    // Extract the QR code detail
    $qr_code_detail = $_POST['qr_code_detail'];

    // Parse QR Code data (assuming the format is "AttendanceID:<user_id>")
    if (!preg_match('/^AttendanceID:(\d+)$/', $qr_code_detail, $matches)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid QR Code format.']);
        exit;
    }

    $user_id = $matches[1];

    try {
        // Fetch user details from the database
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit;
        }

        // Check if the user's status is 'active'
        if ($user['status'] !== 'active') {
            echo json_encode(['status' => 'error', 'message' => 'User account is not active.']);
            exit;
        }

        // Log attendance in the `attendance_logs` table
        $log_sql = "INSERT INTO attendance_logs (user_id, attendance_id, error_message, created_at) 
                    VALUES (:user_id, :attendance_id, '', NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            ':user_id' => $user_id,
            ':attendance_id' => $user_id // Attendance ID matches User ID for simplicity
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Attendance logged successfully!',
            'data' => [
                'user_id' => $user_id,
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email']
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
