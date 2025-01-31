<?php
session_start();
include('../config/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include necessary libraries
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/SMTP.php';
include('../phpqrcode/qrlib.php'); // Include the QR code library

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

    // If the status is being updated to active, generate a new QR code
    $newGeneratedCode = null;
    if ($_POST['status'] === 'active') {
        $newGeneratedCode = uniqid('code_'); // Generate a unique code
        $fields[] = "generated_code = :generated_code";
        $params[':generated_code'] = $newGeneratedCode;
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
        if ($_POST['status'] === 'active' && $newGeneratedCode) {
            // Generate QR code
            $qr_data = $newGeneratedCode; // Store the generated code in the QR code
            $qr_filename = '../qrcodes/' . uniqid() . '.png';
            QRcode::png($qr_data, $qr_filename, QR_ECLEVEL_L, 3, 3); // Create the QR code image

            // Fetch user's email and name for the email
            $user_sql = "SELECT email, first_name, last_name FROM users WHERE user_id = :user_id";
            $user_stmt = $pdo->prepare($user_sql);
            $user_stmt->execute([':user_id' => $user_id]);
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                sendApprovalEmail($user['email'], $user['first_name'] . ' ' . $user['last_name'], $qr_filename);
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
function sendApprovalEmail($email, $user_name, $qr_filename) {
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
        <p>Attached is your QR code for your attendance in the system.</p>
        <p>Best regards,<br>Admin Team</p>
    </body>
    </html>";

    // Create a PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'ityourboiaki@gmail.com'; // Your email
        $mail->Password = 'jfrn azmo ggtu tcwu'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('ityourboiaki@gmail.com', 'Admin LGU Iguig'); // Set the sender email
        $mail->addAddress($email, $user_name);  // Add recipient (user email)

        // Attach QR Code image
        $mail->addAttachment($qr_filename);  // Attach the QR code image

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
