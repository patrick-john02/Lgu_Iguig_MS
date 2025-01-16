<?php
// Include the database connection
include('../config/config.php');

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to update the document status.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_id'], $_POST['status'])) {
    $document_id = $_POST['document_id'];
    $new_status = $_POST['status'];
    $action_reason = $_POST['action_reason'] ?? null;

    // Fetch the current status of the document from documenttimeline
    $query = "SELECT status FROM documenttimeline WHERE document_id = :document_id ORDER BY status_date DESC LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':document_id', $document_id, PDO::PARAM_STR);
    $stmt->execute();
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        $current_status = $document['status'];

        // Define valid status transitions
        $valid_transitions = [
            'Pending' => ['First Reading', 'Rejected'],
            'First Reading' => ['Second Reading', 'Rejected'],
            'Second Reading' => ['Third Reading', 'Rejected'],
            'Third Reading' => ['Committee Review', 'Rejected'],
            'Committee Review' => ['For Approval', 'Rejected'],
            'For Approval' => ['Approved', 'Rejected'],
            'Approved' => [] // No transition from Approved
        ];

        // Check if the new status is valid based on the current status
        if (in_array($new_status, $valid_transitions[$current_status])) {
            // Log the status update in the documenttimeline table
            $log_query = "INSERT INTO documenttimeline (document_id, status, action_by, action_reason) 
                          VALUES (:document_id, :status, :action_by, :action_reason)";
            $log_stmt = $pdo->prepare($log_query);
            $log_stmt->bindParam(':document_id', $document_id, PDO::PARAM_STR);
            $log_stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
            $log_stmt->bindParam(':action_by', $_SESSION['user_id'], PDO::PARAM_INT);
            $log_stmt->bindParam(':action_reason', $action_reason, PDO::PARAM_STR);
            $log_stmt->execute();

            // Set the success message in session and redirect
            $_SESSION['status_update_success'] = "Status updated successfully!";
            header("Location: document_info.php?document_id=" . $document_id);
            exit;
        } else {
            echo "Invalid status transition.";
        }
    } else {
        echo "Document not found.";
    }
} else {
    echo "Invalid request.";
}
?>