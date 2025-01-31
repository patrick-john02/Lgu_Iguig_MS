<?php
include('../config/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}
$user_id = $_SESSION['user_id'];
try {
  // Fetch user details including the profile picture
  $stmt = $pdo->prepare("SELECT first_name, last_name, profile_picture FROM users WHERE user_id = :user_id");
  $stmt->execute(['user_id' => $user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
      throw new Exception("User not found. Please log in again.");
  }

  // Extract the details
  $first_name = htmlspecialchars($user['first_name']);
  $last_name = htmlspecialchars($user['last_name']);
  $profile_picture = htmlspecialchars($user['profile_picture']);
} catch (Exception $e) {
  // Handle errors (redirect with error message)
  $_SESSION['error_message'] = $e->getMessage();
  header("Location: ./admin_login.php");
  exit();
}
?>

<div class="navbar nav_title" style="border: 0;">
    <a href="admin_dashboard.php" class="site_title"><span>LGU Iguig </span></a>
</div>

            <div class="clearfix"></div>

            <div class="clearfix"></div>

            <div class="profile clearfix">
            <div class="profile_pic">
        <?php 
        // Set a default profile picture if none is uploaded
        $defaultProfilePicture = "./default.jpg"; // Update with the actual path
        $displayPicture = !empty($profile_picture) ? $profile_picture : $defaultProfilePicture;
        ?>
        <img src="<?php echo htmlspecialchars($displayPicture); ?>" alt="Profile Picture" class="profile_img">
    </div>
    <div class="profile_info">
        <span>Welcome,</span>
        <h2><?php echo htmlspecialchars($first_name . " " . $last_name); ?></h2>
    </div>
</div>

<!-- Inline CSS -->
<style>
    .profile_pic img {
        width: 100px; /* Fixed width */
        height: 100px; /* Fixed height */
        object-fit: cover; /* Ensures the image covers the dimensions while maintaining aspect ratio */
        border-radius: 50%; /* Makes it perfectly circular */
        border: 2px solid #ddd; /* Optional: Adds a light border for aesthetics */
        padding: 3px; /* Optional: Adds space inside the border */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Adds a subtle shadow for a polished look */
    }
</style>
             <!-- sidebar menu -->
             <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>Reports</h3>
                
                <ul class="nav side-menu">
                
                <li><a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a><i class="fa fa-file"></i>Attendance <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                    <li><a href="attendance.php"><i class="fa fa-calendar"></i> Daily Attendance </a></li>
                    <li><a href="session_attendance.php"><i class="fa fa-calendar"></i> Sessions and Meeting </a></li>
                    </ul>
                  </li>
               
                <!-- <li><a href="scan_attendance.php"><i class="fa fa-gears"></i> Scanner </a></li> -->
                <li><a href="admin_send_letter.php"><i class="fa fa-paper-plane"></i> Create Letter </a></li>

                
                  <!-- <li><a><i class="fa fa-file"></i> Lists of Ordinances <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="submitted_ordinance.php">Submitted Ordinances</a></li>
                      <li><a href="approved_ordinance.php">Approved Ordinances</a></li>
                      <li><a href="rejected_ordinance.php">Rejected Ordinances</a></li>
                      <li><a href="archived_ordinance.php">Archived Ordinances</a></li>
                     
                    </ul> -->
                  </li>

                  <!-- <li><a><i class="fa fa-file"></i>Lists of Memorandum <span class="fa fa-chevron-down"></span></a> -->
                    <ul class="nav child_menu">
                      <!-- <li><a href="submitted_memorandum.php">Submitted Memorandum</a></li> -->
                      <!-- <li><a href="approved_memorandum.php">Approved Memorandum</a></li>
                      <li><a href="rejected_memorandum.php">Rejected Memorandum</a></li>
                      <li><a href="archived_memorandum.php">Archived Memorandum</a></li> -->
                     
                    </ul>
                  </li>
                 
                 
                 
                  </li>                  
                  
                  <li><a href="admin_events.php"><i class="fa fa-calendar"></i> Events</a></li>
                  <li><a href="landispute.php"><i class="fa fa-calendar"></i> Landisputes</a></li>
                  <li><a href="manage_users.php"><i class="fa fa-user"></i> Users</a></li>
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->
          </div>
        </div>