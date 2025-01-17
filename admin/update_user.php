<?php
session_start();
include('../config/config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user ID
    $user_id = $_POST['user_id'];

    // Collect and filter the provided fields
    $fields = [];
    $params = [':user_id' => $user_id];

    if (!empty($_POST['username'])) {
        $fields[] = "username = :username";
        $params[':username'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $fields[] = "email = :email";
        $params[':email'] = $_POST['email'];
    }
    if (!empty($_POST['first_name'])) {
        $fields[] = "first_name = :first_name";
        $params[':first_name'] = $_POST['first_name'];
    }
    if (!empty($_POST['last_name'])) {
        $fields[] = "last_name = :last_name";
        $params[':last_name'] = $_POST['last_name'];
    }
    if (!empty($_POST['position_id'])) {
        $fields[] = "position_id = :position_id";
        $params[':position_id'] = $_POST['position_id'];
    }
    if (!empty($_POST['status'])) {
        $fields[] = "status = :status";
        $params[':status'] = $_POST['status'];
    }

    // If no fields are provided, return an error
    if (empty($fields)) {
        $_SESSION['error_message'] = "No fields to update.";
        header("Location: /admin/manage_users.php");
        exit;
    }

    // Build dynamic SQL query
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Success message
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: /admin/manage_users.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating user: " . $e->getMessage();
        header("Location: /admin/manage_users.php");
        exit;
    }
}

?>
