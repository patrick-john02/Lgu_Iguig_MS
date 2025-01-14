<?php
session_start();
include('config/config.php');

// Initialize response array
$response = array('status' => 'error', 'message' => 'An error occurred. Please try again later.');

try {
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate user inputs
        $username_or_email = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars(trim($_POST['password']));

        // Basic validation (ensure no empty fields)
        if (empty($username_or_email) || empty($password)) {
            throw new Exception("All fields are required.");
        }

        // Query to check if the user exists and is an employee (role_id = 2) with active status
        $stmt = $pdo->prepare("SELECT u.user_id, u.password, u.role_id, r.role_name 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.role_id 
                               WHERE (u.username = :username_or_email OR u.email = :username_or_email) 
                               AND r.role_name = 'Employee' 
                               AND u.status = 'active'");
        $stmt->execute(['username_or_email' => $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and the role is 'Employee'
        if ($user && hash('sha256', $password) === $user['password']) {
            // Start session and store user details
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role_name'];
        
            // Send success response with redirect URL
            $response['status'] = 'success';
            $response['redirect'] = 'employee/employee_dashboard.php';  // Update this URL to your dashboard
        } else {
            throw new Exception("Invalid credentials or inactive account. Please try again.");
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();  // Set error message
}

// Redirect back to the login page with a message
if ($response['status'] == 'error') {
    $_SESSION['error_message'] = $response['message'];
    header("Location: landing_page.php"); // Redirect to login page or any other page
    exit();
} else {
    // Redirect to the dashboard upon successful login
    header("Location: " . $response['redirect']);
    exit();
}
?>
