<?php
include("config/config.php"); // This file uses $pdo

if (isset($_GET['qr_code'])) {
    $qrCode = $_GET['qr_code'];

    // Retrieve user info based on the QR code
    $stmt = $pdo->prepare("SELECT user_id, name, position FROM users WHERE generated_code = :generated_code");
    $stmt->bindParam(":generated_code", $qrCode, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(null);
        }
    } else {
        echo json_encode(null);
    }
}
?>
