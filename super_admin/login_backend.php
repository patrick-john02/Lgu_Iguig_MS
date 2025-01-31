<?php
session_start();
include('../config/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs to prevent SQL injection
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = 'Username and Password are required.';
        header("Location: admin_login.php");
        exit();
    }
    

    try {
        // Check if the user exists in the database
        $stmt = $pdo->prepare("SELECT u.user_id, u.username, u.password, r.role_name, u.role_id
                               FROM users u
                               JOIN roles r ON u.role_id = r.role_id
                               WHERE u.username = :username AND r.role_name = 'SuperAdmin'");
        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user is found and verify the password using bcrypt
        if ($user && password_verify($password, $user['password'])) {
            // Login successful, store only user_id and role_id in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];  // Store role_id in session

            // Redirect to the admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Invalid credentials
            $_SESSION['error_message'] = 'Invalid Username or Password';
            header("Location: admin_login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
        header("Location: admin_login.php");
        exit();
    }
}
?>
