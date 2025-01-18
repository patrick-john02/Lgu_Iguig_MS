<?php
include('../config/config.php'); // Include database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $name = $_POST['title'];
    $event_type = $_POST['document_type'];
    $event_date = $_POST['event_date']; // Capture the event date

    // Validate and process file upload
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file_name = $_FILES['file_upload']['name'];
        $file_tmp = $_FILES['file_upload']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Allowed file extensions (only images)
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $upload_dir = '/uploads/events/';
            $file_path = $upload_dir . basename($file_name);

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Insert the data into the database
                try {
                    // Prepare SQL statement (now with event_date)
                    $stmt = $pdo->prepare("INSERT INTO event (name, event_type, date, image_path) VALUES (:name, :event_type, :event_date, :image_path)");
                    // Execute the query
                    $stmt->execute([
                        ':name' => $name,
                        ':event_type' => $event_type,
                        ':event_date' => $event_date, // Insert the event date into the database
                        ':image_path' => $file_path,  // Save the file path in the database
                    ]);

                    // Redirect or display success message
                    echo "<script>alert('Event request submitted successfully!'); window.location.href = 'send_request_letter.php';</script>";
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF image.";
        }
    } else {
        echo "Please upload a file.";
    }
} else {
    echo "Form not submitted correctly.";
}
?>
