<?php
include('../config/config.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        // Update the user to soft delete by setting is_deleted to 1
        $sql = "UPDATE users SET is_deleted = 1 WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the user list
        header('Location: admin_dashboard.php');
        exit;
    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
} else {
    echo "No user ID provided.";
}
?>
