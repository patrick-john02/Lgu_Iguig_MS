<?php
include ('config/config.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars(trim($_POST['password']));

    // Basic validation (e.g., ensure no empty fields)
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // Check if email or username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        echo "Email or Username already taken.";
        exit;
    }

    // Hash password using SHA256 for security
    $hashed_password = hash('sha256', $password);

    // Insert new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password, role_id) 
                           VALUES (:first_name, :last_name, :username, :email, :password, :role_id)");

    // Assuming 'role_id' 2 is for employees, adjust as needed
    $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'role_id' => 2 // Default role for employee
    ]);

    // Output JavaScript alert for successful registration
    echo "<script>
            alert('Registration successful!');
            window.location.href = 'landing_page.php'; // Redirect to the landing page
          </script>";
} else {
    echo "Invalid request method.";
}
?>
