<?php
session_start();
include('../config/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $position_id = $_POST['position_id'];

    // 1. Generate username from first and last name
    $username = strtolower($first_name . '.' . $last_name);

    // 2. Generate random password with specific requirements
    $password = generatePassword(8); // 8 characters password

    // 3. Hash the password (SHA256)
    $hashed_password = hash('sha256', $password);  // For consistency with your earlier password hash example

    try {
        // Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, role_id, position_id)
                VALUES (:username, :email, :password, :first_name, :last_name, :role_id, :position_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,  // Store the hashed password (SHA256)
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':role_id' => $role_id,
            ':position_id' => $position_id
        ]);

        // Send email to the user
        sendEmail($email, $first_name, $username, $password);

        // Redirect with success message
        $_SESSION['success_message'] = "User created successfully!";
        header("Location: manage_user.php");
        exit;

    } catch (PDOException $e) {
        // Check for duplicate email error
        if ($e->getCode() == 23000) {  // 23000 is the SQLSTATE code for integrity constraint violation
            if (strpos($e->getMessage(), 'email') !== false) {
                $_SESSION['error_message'] = "The email address is already in use. Please choose a different email.";
            }
        } else {
            $_SESSION['error_message'] = "Error creating user: " . $e->getMessage();
        }

        // Redirect with error message
        header("Location: http://localhost/DMS_Iguig/admin/manage_users.php");
        exit;
    }
}

// Function to send email using PHPMailer
function sendEmail($email, $first_name, $username, $password) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'ityourboiaki@gmail.com';
        $mail->Password = 'jfrn azmo ggtu tcwu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipient
        $mail->setFrom('ityourboiaki@gmail.com', 'LGU-IGUIG-ADMIN');
        $mail->addAddress($email, $first_name);  // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Account Details';
        $mail->Body    = "Hello $first_name,<br><br>MABUHAY! I am pleased to inform you that your registration has been successfully completed. You may now proceed to log in using your credentials.<br>Username: $username<br>Password: $password<br><br>Best regards,<br>LGU Iguig Admin";

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Function to generate a password with specific requirements
function generatePassword($length = 8) {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $specialChars = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    // Ensure we have at least one character from each category
    $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

    // Fill the rest of the password length with random characters from all categories
    $allChars = $uppercase . $lowercase . $numbers . $specialChars;
    $remainingLength = $length - strlen($password);

    for ($i = 0; $i < $remainingLength; $i++) {
        $password .= $allChars[random_int(0, strlen($allChars) - 1)];
    }

    // Shuffle the password to randomize the order
    return str_shuffle($password);
}
?>
