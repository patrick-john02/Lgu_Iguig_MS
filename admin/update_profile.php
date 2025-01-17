<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');
$user_id = $_SESSION['user_id'];

try {
    // Fetch current user details
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.username, u.email, u.profile_picture, u.password
        FROM users u
        WHERE u.user_id = :user_id AND u.is_deleted = 0 AND u.status = 'active'
    ");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        $_SESSION['error_message'] = "No active user found for this ID.";
        header("Location: admin_profile.php");
        exit();
    }

    // Get current user information
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
    $email = htmlspecialchars($user['email']);
    $profile_picture = $user['profile_picture'];
    $current_password_hash = $user['password']; // Store the current password hash for verification

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the updated values from the form
        $new_first_name = htmlspecialchars($_POST['first_name']);
        $new_last_name = htmlspecialchars($_POST['last_name']);
        $new_email = htmlspecialchars($_POST['email']);

        // Password Change Logic
        if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            // Check if the current password matches
            if (password_verify($_POST['current_password'], $current_password_hash)) {
                // Check if new password and confirm password match
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    // Hash the new password
                    $new_password_hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

                    // Prepare SQL to update the password
                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET password = :password
                        WHERE user_id = :user_id
                    ");
                    // Execute the query to update password
                    $stmt->execute([
                        'password' => $new_password_hash,
                        'user_id' => $user_id
                    ]);

                    $_SESSION['success_message'] = "Password changed successfully!";
                } else {
                    $_SESSION['error_message'] = "New password and confirm password do not match.";
                }
            } else {
                $_SESSION['error_message'] = "Current password is incorrect.";
            }
        }

        // Handle Profile Picture Upload (if a new profile picture is uploaded)
        if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            // Define allowed file types and directory for uploads
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $upload_dir = 'profile/';  // Adjust the path as needed

            $file_type = $_FILES['profile_picture']['type'];
            $file_name = basename($_FILES['profile_picture']['name']);
            $target_file = $upload_dir . $file_name;

            // Check if file type is allowed
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                    $profile_picture = $target_file;
                } else {
                    $_SESSION['error_message'] = "File upload failed. Please try again.";
                    header("Location: admin_profile.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
                header("Location: admin_profile.php");
                exit();
            }
        }

        // Prepare the SQL query to update the user's profile (name, email, profile picture)
        $stmt = $pdo->prepare("
            UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email, profile_picture = :profile_picture
            WHERE user_id = :user_id
        ");

        // Execute the update query
        $stmt->execute([
            'first_name' => $new_first_name,
            'last_name' => $new_last_name,
            'email' => $new_email,
            'profile_picture' => $profile_picture,
            'user_id' => $user_id
        ]);

        // Set a success message and redirect to the profile page
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: admin_profile.php");
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: admin_profile.php");
    exit();
}
?>
