<?php 
session_start();
include('../config/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/SMTP.php';
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
        header("Location: http://localhost/DMS_Iguig/admin/manage_users.php");
        exit;
    }

    // Build dynamic SQL query
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Check if the status was updated to 'active'
        if ($_POST['status'] == 'active') {
            // Fetch the user's email from the database
            $user_sql = "SELECT email, first_name, last_name FROM users WHERE user_id = :user_id";
            $user_stmt = $pdo->prepare($user_sql);
            $user_stmt->execute([':user_id' => $user_id]);
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $user_email = $user['email'];
                $user_name = $user['first_name'] . ' ' . $user['last_name'];

                // Send email notification using PHPMailer
                sendApprovalEmail($user_email, $user_name);
            }
        }

        // Success message
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: http://localhost/DMS_Iguig/admin/manage_users.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating user: " . $e->getMessage();
        header("Location: http://localhost/DMS_Iguig/admin/manage_users.php");
        exit;
    }
}

/**
 * Function to send email notification when user is approved (status set to active)
 */
function sendApprovalEmail($email, $user_name) {
    $subject = "Your Account has been Approved!";
    $message = "
    <html>
    <head>
        <title>Account Approved</title>
    </head>
    <body>
        <p>Dear $user_name,</p>
        <p>Your account has been approved by the administrator and is now active.</p>
        <p>You can now log in and access your account.</p>
        <p>Best regards,<br>Admin Team</p>
    </body>
    </html>";

    // Create a PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'ityourboiaki@gmail.com';
        $mail->Password = 'jfrn azmo ggtu tcwu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('ityourboiaki@gmail.com', 'Admin LGU Iguig');        // Set the sender email
        $mail->addAddress($email, $user_name);                       // Add recipient (user email)

        // Content
        $mail->isHTML(true);                                         // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
