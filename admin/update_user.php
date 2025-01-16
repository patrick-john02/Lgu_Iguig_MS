<?php
session_start();
include('../config/config.php');

// Check if the user is logged in and has appropriate permissions (optional)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user ID from the form
    $user_id = $_POST['user_id'];

    // Collect the form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $position_id = $_POST['position_id'];
    $status = $_POST['status'];

    // Validate input data if needed
    if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($position_id) || empty($status)) {
        echo "All fields are required.";
        exit;
    }

    try {
        // Update the user details in the database
        $sql = "UPDATE users
                SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, position_id = :position_id, status = :status
                WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':position_id' => $position_id,
            ':status' => $status,
            ':user_id' => $user_id
        ]);

        // Set the success message in the session
        $_SESSION['success_message'] = "User updated successfully!";

        // Redirect back to manage_user.php
        header("Location: http://localhost/DMS_Iguig/admin/manage_users.php");
        exit;
    } catch (PDOException $e) {
        echo "Error updating user: " . $e->getMessage();
        exit;
    }
}
?>
