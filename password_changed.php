<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Lgu_Iguig_MS";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT user_id, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['user_id'];
        $sha256Password = $row['password'];

        // script for changing pasword on database 
        $newHashedPassword = password_hash('admin123', PASSWORD_BCRYPT); 

        // Update the password 
        $updateSql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $newHashedPassword, $userId);

        if ($stmt->execute()) {
            echo "Password for user ID $userId updated successfully!<br>";
        } else {
            echo "Error updating password for user ID $userId: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
} else {
    echo "No users found.";
}

$conn->close();
?>
