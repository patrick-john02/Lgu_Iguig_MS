<?php
include("config/config.php");

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qr_code']) && isset($_POST['attendance_type'])) {
        $qrCode = $_POST['qr_code'];
        $attendanceType = $_POST['attendance_type']; // Get the attendance type from the form

        // Step 1: Check if the QR code matches a user in the `users` table and join with `positions` to get position name
        $selectStmt = $pdo->prepare("
            SELECT 
                u.user_id, 
                u.first_name, 
                u.last_name, 
                p.position_name 
            FROM 
                users u
            LEFT JOIN 
                positions p 
            ON 
                u.position_id = p.id
            WHERE 
                u.generated_code = :generated_code
        ");
        $selectStmt->bindParam(":generated_code", $qrCode, PDO::PARAM_STR);

        if ($selectStmt->execute()) {
            $result = $selectStmt->fetch();
            if ($result !== false) {
                $userID = $result["user_id"];
                $fullName = $result["first_name"] . ' ' . $result["last_name"];
                $positionName = $result["position_name"];
                $currentTimestamp = date("Y-m-d H:i:s"); // Current timestamp for attendance action

                try {
                    // Step 2: Update the attendance based on the selected type
                    switch ($attendanceType) {
                        case 'time_in':
                            $stmt = $pdo->prepare("UPDATE tbl_attendance SET time_in = :time_in WHERE user_id = :user_id");
                            $stmt->bindParam(":time_in", $currentTimestamp, PDO::PARAM_STR);
                            break;

                        case 'break_out':
                            $stmt = $pdo->prepare("UPDATE tbl_attendance SET break_out = :break_out WHERE user_id = :user_id");
                            $stmt->bindParam(":break_out", $currentTimestamp, PDO::PARAM_STR);
                            break;

                        case 'break_in':
                            $stmt = $pdo->prepare("UPDATE tbl_attendance SET break_in = :break_in WHERE user_id = :user_id");
                            $stmt->bindParam(":break_in", $currentTimestamp, PDO::PARAM_STR);
                            break;

                        case 'time_out':
                            $stmt = $pdo->prepare("UPDATE tbl_attendance SET time_out = :time_out WHERE user_id = :user_id");
                            $stmt->bindParam(":time_out", $currentTimestamp, PDO::PARAM_STR);
                            break;

                        default:
                            echo "<script>alert('Invalid attendance action!'); window.location.href = 'scanner.php';</script>";
                            exit();
                    }

                    // Bind the user ID parameter
                    $stmt->bindParam(":user_id", $userID, PDO::PARAM_INT);

                    // Execute the query
                    $stmt->execute();

                    // Redirect after successful update with success message and employee details
                    header("Location: scanner.php?status=success&action=" . urlencode($attendanceType) . "&name=" . urlencode($fullName) . "&position=" . urlencode($positionName));
                    exit();

                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // No matching user found for the QR code
                echo "<script>alert('No user found for the provided QR code!'); window.location.href = 'scanner.php';</script>";
            }
        } else {
            echo "Failed to execute the statement.";
        }
    } else {
        echo "<script>alert('Please provide a valid QR code and select an attendance action!'); window.location.href = 'scanner.php';</script>";
    }
}
?>
