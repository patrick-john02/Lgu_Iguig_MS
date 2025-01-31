<?php
include('../config/config.php'); // Include the PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_name = $_POST['meeting_name'];
    $subject = $_POST['subject'];
    $meeting_date = $_POST['meeting_date']; // Format: 'YYYY-MM-DDTHH:MM'
    $description = $_POST['meeting_description'];
    $host_id = 1; // Set the host ID, based on session or current user
    $event_type = 'Meeting'; // Set the event type as 'Meeting'

    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO sessions (name, subject, event_type, host_id, date, description, created_by)
                VALUES (:name, :subject, :event_type, :host_id, :date, :description, :created_by)";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $meeting_name);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':event_type', $event_type);
        $stmt->bindParam(':host_id', $host_id);
        $stmt->bindParam(':date', $meeting_date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':created_by', $host_id);

        // Execute the statement
        $stmt->execute();

        // Set success message
        $_SESSION['success_message'] = "Meeting created successfully.";
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    // Redirect back to the meeting creation page
    header("Location: create_meeting_page.php");
    exit();
}
?>
