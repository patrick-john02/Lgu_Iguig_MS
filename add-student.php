<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (isset($_POST['student_name'], $_POST['course_section'], $_POST['generated_code'])) {
        $studentName = trim($_POST['student_name']);
        $studentCourse = trim($_POST['course_section']);
        $generatedCode = trim($_POST['generated_code']);

        try {
            // Prepare SQL statement
            $stmt = $conn->prepare("
                INSERT INTO tbl_student (student_name, course_section, generated_code) 
                VALUES (:student_name, :course_section, :generated_code)
            ");
            
            // Bind parameters to avoid SQL injection
            $stmt->bindParam(":student_name", $studentName, PDO::PARAM_STR); 
            $stmt->bindParam(":course_section", $studentCourse, PDO::PARAM_STR);
            $stmt->bindParam(":generated_code", $generatedCode, PDO::PARAM_STR);

            // Execute the query
            $stmt->execute();

            // Redirect to masterlist page
            header("Location: http://localhost/qr-code-attendance-system/masterlist.php");
            exit();
        } catch (PDOException $e) {
            // Handle database errors
            echo "
                <script>
                    alert('Database Error: " . addslashes($e->getMessage()) . "');
                    window.history.back();
                </script>
            ";
        }
    } else {
        // Handle missing fields
        echo "
            <script>
                alert('Please fill in all required fields!');
                window.history.back();
            </script>
        ";
    }
} else {
    // Redirect if accessed without POST
    header("Location: http://localhost/qr-code-attendance-system/masterlist.php");
    exit();
}
?>
