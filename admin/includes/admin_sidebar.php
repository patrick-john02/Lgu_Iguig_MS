
<?php
include('../config/config.php');


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user details from the database
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found. Please log in again.");
    }

    // Extract the first and last name
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
} catch (Exception $e) {
    // Handle errors (redirect with error message)
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../landing_page.php");
    exit();
}
?>

<div class="navbar nav_title" style="border: 0;">
    <a href="employee_dashboard.php" class="site_title"><span>LGU Iguig </span></a>
</div>

            <div class="clearfix"></div>

            <div class="clearfix"></div>

<!-- menu profile quick info -->
<div class="profile clearfix">
<div class="profile_info">
<span>Welcome,</span>
<h2><?php echo $first_name . " " . $last_name; ?></h2>
</div>
</div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
           

          
          </div>
        </div>
