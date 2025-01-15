<?php
session_start();
include('config/config.php');

try {
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate user inputs
        $username_or_email = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Basic validation
        if (empty($username_or_email) || empty($password)) {
            throw new Exception("All fields are required.");
        }

        // Query to verify user credentials
        $stmt = $pdo->prepare("SELECT u.user_id, u.password, u.role_id, r.role_name 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.role_id 
                               WHERE (u.username = :username_or_email OR u.email = :username_or_email) 
                               AND r.role_name = 'Employee' 
                               AND u.status = 'active'");
        $stmt->execute(['username_or_email' => $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && hash('sha256', $password) === $user['password']) {
            // Start session and store user details
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role_name'];

            // Redirect to the dashboard
            header("Location: employee/employee_dashboard.php");
            exit();
        } else {
            throw new Exception("Invalid credentials or inactive account. Please try again.");
        }
    }
} catch (Exception $e) {
    // Store error message in session and redirect to the login page
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: landing_page.php");
    exit();
}
?>
