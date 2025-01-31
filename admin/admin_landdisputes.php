<?php
// Include database connection
require '../config/config.php'; // Make sure this file contains your PDO connection setup

if (isset($_POST['create_landdispute'])) {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO landisputes (title, description, date) VALUES (:title, :description, :date)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to the desired page after successful insert
            header("Location: landispute.php?success=1"); // Pass a success flag in the query string
            exit(); // Make sure to call exit() after header() to stop further script execution
        } else {
            echo "Failed to create the land dispute record.";
        }
    } catch (PDOException $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
    }
}
?>
