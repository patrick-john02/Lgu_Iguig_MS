<?php
session_start();
include('../config/config.php');

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = htmlspecialchars(trim($_POST['title']));
        $document_type = htmlspecialchars(trim($_POST['document_type']));
        $description = htmlspecialchars(trim($_POST['description']));
        $user_id = $_SESSION['user_id']; // Ensure the user is logged in

        // Validate the uploaded file
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['file_upload']['tmp_name'];
            $file_name = $_FILES['file_upload']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_extensions = ['pdf', 'jpg', 'png'];

            if (!in_array($file_ext, $allowed_extensions)) {
                throw new Exception("Invalid file type. Allowed types: PDF, JPG, PNG");
            }

            // Generate a unique file name and save the file
            $file_new_name = uniqid() . '.' . $file_ext;
            $file_path = '../uploads/' . $file_new_name;

            if (!move_uploaded_file($file_tmp, $file_path)) {
                throw new Exception("Failed to upload the file. Please try again.");
            }

            // Get the current date and year
            $current_date = date('d-m-Y');
            $current_year = date('Y');

            // Get the count of documents for the selected document_type
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM document WHERE document_type = :document_type");
            $stmt->execute(['document_type' => $document_type]);
            $document_count = $stmt->fetchColumn();

            // Increment the document count
            $increment_number = $document_count + 1;

            // Format the new document_id (with leading zeros for 5 digits)
            $document_id = $document_type . '-' . str_pad($increment_number, 5, '0', STR_PAD_LEFT) . '-' . $current_date;

            // Insert data into the `document` table
            $stmt = $pdo->prepare("
                INSERT INTO document (document_type, document_id, title, authored_by, date, subject, description, file_path) 
                VALUES (:document_type, :document_id, :title, :authored_by, NOW(), :subject, :description, :file_path)
            ");
            $stmt->execute([
                'document_type' => $document_type,
                'document_id' => $document_id,
                'title' => $title,
                'authored_by' => $user_id,
                'subject' => $title, // For simplicity, using title as subject
                'description' => $description,
                'file_path' => $file_path,
            ]);

            // Insert into `documentfiles` table
            $stmt = $pdo->prepare("
                INSERT INTO documentfiles (document_id, file_type, file_path, uploaded_by) 
                VALUES (:document_id, :file_type, :file_path, :uploaded_by)
            ");
            $stmt->execute([
                'document_id' => $document_id,
                'file_type' => $file_ext,
                'file_path' => $file_path,
                'uploaded_by' => $user_id,
            ]);

            // Insert initial 'Pending' status into the documenttimeline table
            $stmt = $pdo->prepare("
                INSERT INTO documenttimeline (document_id, status, action_by, action_reason) 
                VALUES (:document_id, 'Pending', :action_by, :action_reason)
            ");
            $stmt->execute([
                'document_id' => $document_id,
                'action_by' => $user_id,
                'action_reason' => 'Document is pending approval.' // Default action reason
            ]);

            $_SESSION['success_message'] = "Request letter successfully submitted";
            header("Location: admin_send_letter.php");
            exit();
        } else {
            throw new Exception("Please upload a valid document.");
        }
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: admin_send_letter.php");
    exit();
}
?>
