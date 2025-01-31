<?php
// Include the PDO connection
include('../config/config.php');

// Get current date
$currentDate = date('Y-m-d');

try {
    // Check if the daily session for today already exists
    $sqlCheck = "SELECT * FROM sessions WHERE DATE(date) = :currentDate AND recurrence = 'Daily'";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':currentDate', $currentDate);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() == 0) {
        // No session for today, so create a new session
        $sessionName = 'Daily Staff Meeting';
        $sessionSubject = 'Daily team progress check';
        $eventType = 'Session';
        $hostId = 1; // assuming 1 is the ID of the host/creator
        $facilitatorId = NULL; // assuming facilitator is optional
        $startTime = '13:00:00'; // session starts at 1 PM
        $recurrence = 'Daily'; // recurrence type
        $description = 'Daily check-in meeting for the staff.';

        // Insert the new session
        $sqlInsert = "INSERT INTO sessions (name, subject, event_type, host_id, facilitator_id, start_time, recurrence, date, description, created_by)
                      VALUES (:sessionName, :sessionSubject, :eventType, :hostId, :facilitatorId, :startTime, :recurrence, :currentDateTime, :description, :hostId)";
        $stmtInsert = $pdo->prepare($sqlInsert);

        // Bind the parameters
        $stmtInsert->bindParam(':sessionName', $sessionName);
        $stmtInsert->bindParam(':sessionSubject', $sessionSubject);
        $stmtInsert->bindParam(':eventType', $eventType);
        $stmtInsert->bindParam(':hostId', $hostId);
        $stmtInsert->bindParam(':facilitatorId', $facilitatorId);
        $stmtInsert->bindParam(':startTime', $startTime);
        $stmtInsert->bindParam(':recurrence', $recurrence);
        $stmtInsert->bindParam(':currentDateTime', $currentDate . ' ' . $startTime);
        $stmtInsert->bindParam(':description', $description);

        // Execute the insert statement
        $stmtInsert->execute();

        echo "New session created successfully!";
    } else {
        echo "Session for today already exists.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection (optional with PDO)
$pdo = null;
?>
