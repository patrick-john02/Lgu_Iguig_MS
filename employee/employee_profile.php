<?php
include('../config/config.php');
session_start(); // Start session if not already started

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}
$user_id = $_SESSION['user_id'];
try {
    //  Check if session contains user_id
    // echo "Session User ID: " . $user_id; 

    // Fetch user details from the database
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.username, u.email, u.profile_picture, r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = :user_id AND u.is_deleted = 0 AND u.status = 'active'
    ");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user data exists
    if (!$user) {
        echo "No active user found for this ID.";  // Debugging message
        exit();  // Prevent further execution if the user is not found
    }

    // Extract user information
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
    $username = htmlspecialchars($user['username']);
    $email = htmlspecialchars($user['email']);
    $profile_picture = $user['profile_picture'] ? $user['profile_picture'] : './default.jpg'; // Default picture if none is set
    $role_name = htmlspecialchars($user['role_name']);
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../landing_page.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>Employee Profile</title>

    <!-- Bootstrap -->
    <link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../prod/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="../prod/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="../prod/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="../prod/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../prod/build/css/custom.min.css" rel="stylesheet">
    <style>
      .nav_title {
    display: flex;
    justify-content: center; 
    align-items: center; 
    height: 60px; 
    background-color: #2A3F54; 
    text-align: center;
    border-radius: 5px;
    padding: 10px; 
}

.nav_title .site_title {
    font-size: 20px; 
    font-weight: bold; 
    color: white; 
    text-decoration: none; 
}


.nav_title .site_title:hover {
    color: #1ABB9C; 
    text-decoration: none; 
}
      .profile {
    display: flex;
    flex-direction: column;
    align-items: center; 
    justify-content: center; 
    text-align: center; 
    padding: 1px; 
}

.profile_info span {
    font-size: 16px; /
    color: white;
}

.profile_info h2 {
    font-size: 15px; 
    margin: 5px 0; 
    font-weight: bold; 
    color: white; 
}

  .fixed-size-box {
    width: 100%; 
    height: 250px; 
    padding: 90px;
    box-sizing: border-box;
    border-radius: 5px;
    background-color: #f5f5f5;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center; 
  }

  .icon-title {
    display: flex;
    align-items: center; 
    justify-content: center;
  }

  .icon {
    font-size: 2em;
    margin-right: 10px; 
  }

  .tile-stats .count {
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .tile-stats h3 {
    font-size: 1.2em;
    font-weight: bold;
    margin: 0;
  }

 
  .top_tiles .col-lg-3, .top_tiles .col-md-3, .top_tiles .col-sm-6 {
    padding: 10px;
  }
  /* Add a class for the circular profile image */
.profile-img-circle {
    width: 150px; /* Set the size of the profile picture */
    height: 150px; /* Set the height of the profile picture */
    border-radius: 50%; /* Make the image circular */
    object-fit: cover; /* Ensure the image maintains its aspect ratio and covers the area */
    border: 2px solid #ddd; /* Optional: add a border to make the image more defined */
}


  @media (max-width: 767px) {
    .tile-stats.fixed-size-box {
      height: 220px;
    }
  }
</style>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">

         
           <?php  include ('includes/sidebar.php');?>
          <?php include ('includes/navbar.php');?>
       <!-- page content -->
       <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
              <?php
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']);
}
?>
                <h3>User Profile</h3>
              </div>
            <div class="clearfix"></div>

            <div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>User Profile</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                   
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="col-md-3 col-sm-3  profile_left">
                <div class="profile_img">
    <div id="crop-avatar">
        <!-- Current avatar -->
        <img class="img-responsive avatar-view profile-img-circle" src="<?php echo $profile_picture; ?>" alt="Avatar" title="Change the avatar">
    </div>
</div>
                    <h3><?php echo $first_name . " " . $last_name; ?></h3>

                    <ul class="list-unstyled user_data">
                                               
                                                <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                                    <i class="fa fa-envelope" style="margin-right: 10px;"></i>
                                                    <?php echo $email; ?>
                                                </li>
                                                <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                                    <i class="fa fa-briefcase" style="margin-right: 10px;"></i>
                                                    <?php echo $role_name; ?>
                                                </li>
                                            </ul>


                </div>
                <div class="col-md-9 col-sm-9 ">
                    <div role="tabpanel" data-example-id="togglable-tabs">
                       
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_content1" aria-labelledby="home-tab">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Update your Profile here</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <br />
                                       
                                        <form class="form-label-left input_mask" method="post" action="update_profile_user.php" enctype="multipart/form-data">
    <!-- Form fields for first name, last name, email, and profile picture -->
    <div class="row">
        <div class="col-md-6 col-sm-6 form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $first_name; ?>" placeholder="First Name">
        </div>

        <div class="col-md-6 col-sm-6 form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $last_name; ?>" placeholder="Last Name">
        </div>

        <div class="col-md-6 col-sm-6 form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email">
        </div>

        <div class="col-md-6 col-sm-6 form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" class="form-control" name="profile_picture" id="profile_picture">
        </div>
    </div>

    <!-- Change Password Section -->
    <div class="row">
        <div class="col-md-6 col-sm-6 form-group">
            <label for="current_password">Current Password:</label>
            <div class="position-relative">
                <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password">
                <i class="fa fa-eye position-absolute" id="eye-icon-current_password" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('current_password')"></i>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 form-group">
            <label for="new_password">New Password:</label>
            <div class="position-relative">
                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                <i class="fa fa-eye position-absolute" id="eye-icon-new_password" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('new_password')"></i>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <div class="position-relative">
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password">
                <i class="fa fa-eye position-absolute" id="eye-icon-confirm_password" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('confirm_password')"></i>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="form-group row">
        <div class="col-md-5 col-sm-5 offset-md-0">
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </div>
</form>

<script>
    function togglePassword(id) {
        var inputField = document.getElementById(id);
        var icon = document.getElementById('eye-icon-' + id);

        if (inputField.type === "password") {
            inputField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            inputField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
                         
                                        </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                


        <!-- /page content -->
        <footer>
          <div class="pull-right">
          
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../prod/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../prod/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../prod/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../prod/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="../prod/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="../prod/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../prod/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../prod/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="../prod/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="../prod/vendors/Flot/jquery.flot.js"></script>
    <script src="../prod/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="../prod/vendors/Flot/jquery.flot.time.js"></script>
    <script src="../prod/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="../prod/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="../prod/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="../prod/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="../prod/vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="../prod/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="../prod/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="../prod/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="../prod/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../prod/vendors/moment/min/moment.min.js"></script>
    <script src="../prod/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../prod/build/js/custom.min.js"></script>
	
  </body>
</html>
