<?php
include('../config/config.php');


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch username from the database
    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found. Please log in again.");
    }

    // Extract the username
    $username = htmlspecialchars($user['username']);
} catch (Exception $e) {
    // Handle errors (redirect with error message)
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../landing_page.php");
    exit();
}
?>


<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <nav class="nav navbar-nav">
            <ul class="navbar-right">
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                        <?php echo $username; ?>
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="employee_profile.php"> Profile</a>
                        <a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a>

                    </div>
                </li>
               
        </nav>
    </div>
</div>
<!-- /top navigation -->
