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

        // Query to fetch user details
        $stmt = $pdo->prepare("SELECT u.user_id, u.password, u.role_id, r.role_name, u.status 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.role_id 
                               WHERE (u.username = :username_or_email OR u.email = :username_or_email)");
        $stmt->execute(['username_or_email' => $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check user status
            if ($user['status'] === 'inactive') {
                throw new Exception("Your account is inactive. Please contact support for assistance.");
            }

            if ($user['status'] === 'suspended') {
                throw new Exception("Your account has been suspended. Please contact support.");
            }

            $stored_password = $user['password'];

            // Check if the password is hashed using bcrypt
            if (password_verify($password, $stored_password)) {
                // If the password is valid and requires rehashing, update the hash
                if (password_needs_rehash($stored_password, PASSWORD_DEFAULT)) {
                    $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE user_id = :user_id");
                    $update_stmt->execute(['new_password' => $new_hashed_password, 'user_id' => $user['user_id']]);
                }

                // Start session and store user details
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role_name'];

                // Redirect to the dashboard
                header("Location: employee/employee_dashboard.php");
                exit();
            } elseif (hash('sha256', $password) === $stored_password) {
                // Legacy SHA-256 password handling (for users with old hashes)

                // Rehash the password with bcrypt and update it in the database
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE user_id = :user_id");
                $update_stmt->execute(['new_password' => $new_hashed_password, 'user_id' => $user['user_id']]);

                // Start session and store user details
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role_name'];

                // Redirect to the dashboard
                header("Location: employee/employee_dashboard.php");
                exit();
            } else {
                throw new Exception("Invalid credentials. Please try again.");
            }
        } else {
            throw new Exception("Invalid credentials or inactive account. Please try again.");
        }
    }
} catch (Exception $e) {
    // Store error message in session and display it as an alert on the landing page
    echo "<script>
            alert('{$e->getMessage()}');
            window.location.href = 'landing_page.php';
          </script>";
    exit();
}
?>
