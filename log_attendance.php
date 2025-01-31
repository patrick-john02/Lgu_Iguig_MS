<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


// Database configuration
$host = 'localhost';
$dbname = 'LGU_Iguig_ms';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

// Read raw input
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

if (!$data || !isset($data['user_id']) || !isset($data['scan_type'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing user_id or scan_type.']);
    exit;
}

// Sanitize input
$user_id = intval($data['user_id']);
$scan_type = $data['scan_type'];
$date = date('Y-m-d');
$current_time = date('H:i:s');

// Validate scan_type
$valid_scan_types = ['check_in', 'break_out', 'break_in', 'check_out'];
if (!in_array($scan_type, $valid_scan_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid scan type.']);
    exit;
}

try {
    // Inside your existing try block, add error handling for the SQL query
    $query = "INSERT INTO attendance (user_id, date, check_in, status) VALUES (:user_id, :date, :check_in, 'Present')";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $user_id, ':date' => $date, ':check_in' => $current_time]);
    echo json_encode(['status' => 'success', 'message' => 'Check-in time recorded successfully.']);
    exit;
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}


    // Check attendance record for the current date
    $query = "SELECT * FROM attendance WHERE user_id = :user_id AND date = :date";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $user_id, ':date' => $date]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    // Initialize attendance record if none exists
    if (!$attendance && $scan_type == 'check_in') {
        $query = "INSERT INTO attendance (user_id, date, check_in, status) VALUES (:user_id, :date, :check_in, 'Present')";
        $stmt = $conn->prepare($query);
        $stmt->execute([':user_id' => $user_id, ':date' => $date, ':check_in' => $current_time]);
        echo json_encode(['status' => 'success', 'message' => 'Check-in time recorded successfully.']);
        exit;
    } elseif (!$attendance) {
        echo json_encode(['status' => 'error', 'message' => 'No check-in record found for the user.']);
        exit;
    }

    // Process attendance updates
    switch ($scan_type) {
        case 'break_out':
            if ($attendance['check_in'] && !$attendance['break_out']) {
                $query = "UPDATE attendance SET break_out = :break_out WHERE user_id = :user_id AND date = :date";
                $stmt = $conn->prepare($query);
                $stmt->execute([':break_out' => $current_time, ':user_id' => $user_id, ':date' => $date]);
                echo json_encode(['status' => 'success', 'message' => 'Break-out time recorded.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cannot break out before check-in or break-out already recorded.']);
            }
            break;

        case 'break_in':
            if ($attendance['break_out'] && !$attendance['break_in']) {
                $query = "UPDATE attendance SET break_in = :break_in WHERE user_id = :user_id AND date = :date";
                $stmt = $conn->prepare($query);
                $stmt->execute([':break_in' => $current_time, ':user_id' => $user_id, ':date' => $date]);
                echo json_encode(['status' => 'success', 'message' => 'Break-in time recorded.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cannot break in without a valid break-out or break-in already recorded.']);
            }
            break;

        case 'check_out':
            if ($attendance['break_in'] && !$attendance['check_out']) {
                $query = "UPDATE attendance SET check_out = :check_out WHERE user_id = :user_id AND date = :date";
                $stmt = $conn->prepare($query);
                $stmt->execute([':check_out' => $current_time, ':user_id' => $user_id, ':date' => $date]);
                echo json_encode(['status' => 'success', 'message' => 'Check-out time recorded.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cannot check out without a valid break-in or check-out already recorded.']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid scan type.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}
?>
