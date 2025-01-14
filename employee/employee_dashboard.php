<?php 
session_start();
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
    // Handle errors (optional: display error message or redirect)
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

    <title>Employee Dashboard</title>

    <!-- Bootstrap -->
    <link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="../prod/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../prod/build/css/custom.min.css" rel="stylesheet">

    <style>
      .nav_title {
    display: flex;
    justify-content: center; /* Centers the text horizontally */
    align-items: center; /* Centers the text vertically */
    height: 60px; /* Adjust height if necessary */
    background-color: #2A3F54; /* Set your desired background color */
    text-align: center; /* Ensures text is centered inside */
    border-radius: 5px; /* Optional: Add rounded corners */
    padding: 10px; /* Add padding for breathing space */
}

.nav_title .site_title {
    font-size: 20px; /* Adjust font size for visibility */
    font-weight: bold; /* Make the text bold */
    color: white; /* Text color */
    text-decoration: none; /* Remove underline from the link */
}

/* Hover effect for LGU Iguig */
.nav_title .site_title:hover {
    color: #1ABB9C; /* Optional: Change color on hover */
    text-decoration: none; /* Keep text underline off */
}
      .profile {
    display: flex;
    flex-direction: column;
    align-items: center; /* Centers content horizontally */
    justify-content: center; /* Centers content vertically */
    text-align: center; /* Ensures text alignment is centered */
    padding: 1px; /* Optional: Adjust padding as needed */
}

.profile_info span {
    font-size: 16px; /* Adjust font size if necessary */
    color: white; /* Optional: Change color to match your theme */
}

.profile_info h2 {
    font-size: 15px; /* Adjust the font size of the name */
    margin: 5px 0; /* Add spacing between the span and the name */
    font-weight: bold; /* Make the name bold */
    color: white; /* Optional: Change color to match your theme */
}

  .fixed-size-box {
    width: 100%; /* Make sure they are responsive */
    height: 250px; /* Fixed height */
    padding: 90px;
    box-sizing: border-box;
    border-radius: 5px;
    background-color: #f5f5f5; /* Optional background color */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center; /* Align items center */
  }

  .icon-title {
    display: flex;
    align-items: center; /* Align icon and title horizontally */
    justify-content: center;
  }

  .icon {
    font-size: 2em;
    margin-right: 10px; /* Space between icon and title */
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

  /* Ensuring responsiveness */
  .top_tiles .col-lg-3, .top_tiles .col-md-3, .top_tiles .col-sm-6 {
    padding: 10px;
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

        

   <!-- Page Content -->
<div class="right_col" role="main">
  <div class="container-fluid">
    <div class="row">
     <!-- Pending Box -->
<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
  <div class="tile-stats fixed-size-box shadow-sm text-center">
    <div class="icon-title">
      <div class="icon mb-3">
        <!-- Spinner icon for pending -->
        <i class="fa fa-spinner fa-pulse fa-2x text-warning"></i>
      </div>
      <h3 class="text-dark">Pending</h3>
    </div>
    <div class="count display-4 text-success">179</div>
    <p class="text-muted">Pending requests</p>
  </div>
</div>

     <!-- Approved Box -->
<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
  <div class="tile-stats fixed-size-box shadow-sm text-center">
    <div class="icon-title">
      <div class="icon mb-3">
        <!-- Checkmark icon for approved -->
        <i class="fa fa-check-circle fa-2x text-success"></i>
      </div>
      <h3 class="text-dark">Approved</h3>
    </div>
    <div class="count display-4 text-success">179</div>
    <p class="text-muted">Approved requests</p>
  </div>
</div>

     <!-- Rejected Box -->
<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
  <div class="tile-stats fixed-size-box shadow-sm text-center">
    <div class="icon-title">
      <div class="icon mb-3">
        <!-- Cross icon for rejected -->
        <i class="fa fa-times-circle fa-2x text-danger"></i>
      </div>
      <h3 class="text-dark">Rejected</h3>
    </div>
    <div class="count display-4 text-danger">179</div>
    <p class="text-muted">Rejected requests</p>
  </div>
</div>

      <!-- On Process Box -->
<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
  <div class="tile-stats fixed-size-box shadow-sm text-center">
    <div class="icon-title">
      <div class="icon mb-3">
        <!-- Spinner icon for On Process -->
        <i class="fa fa-spinner fa-spin fa-2x text-warning"></i>
      </div>
      <h3 class="text-dark">On Process</h3>
    </div>
    <div class="count display-4 text-warning">179</div>
    <p class="text-muted">Requests in process</p>
  </div>
</div>



<!-- Carousel Section -->
<div id="overviewCarousel" class="carousel slide mt-1" data-ride="carousel" style="max-width: 95%; margin: auto;">
  <div class="carousel-inner">
  <h1 style="text-align: center;">Events and Announcement</h1>

    <?php
    // Fetch carousel content
    $carouselQuery = "SELECT image, caption, link FROM CarouselContent ORDER BY created_at DESC";
    $carouselStmt = $pdo->query($carouselQuery);
    $isActive = true;

    while ($carouselRow = $carouselStmt->fetch(PDO::FETCH_ASSOC)) {
        $activeClass = $isActive ? 'active' : '';
        $isActive = false; // Only the first item is active
        echo "
        <div class='carousel-item {$activeClass}' style='height: 800px; overflow: hidden;'>

          <!-- Image with clickable functionality -->
          <img class='d-block w-100' src='{$carouselRow['image']}' alt='Carousel Image' style='height: 100%; object-fit: cover;' data-toggle='modal' data-target='#carouselModal' data-image='{$carouselRow['image']}'>

          <!-- Caption with semi-transparent background -->
          
          <div class='carousel-caption d-none d-md-block'>
            <h5>{$carouselRow['caption']}</h5>
            <a href='{$carouselRow['link']}' class='btn btn-primary'>Learn More</a>
          </div>
        </div>";
    }
    ?>
  </div>

  <!-- Carousel Controls -->
  <a class="carousel-control-prev" href="#overviewCarousel" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#overviewCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<!-- Modal for Image Display -->
<div class="modal fade" id="carouselModal" tabindex="-1" role="dialog" aria-labelledby="carouselModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="carouselModalLabel">Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" alt="Modal Image" class="w-100">
      </div>
    </div>
  </div>
</div>

<!-- Additional CSS -->
<style>
  .carousel-item {
    position: relative;
    padding: 50px;
  }

  .carousel-item img {
    cursor: pointer; /* Indicates the image is clickable */
  }

  /* Semi-transparent dark background for the caption */
  .carousel-caption {
    background-color: rgba(0, 0, 0, 0.5); /* Black background with 50% opacity */
    color: white; /* White text for contrast */
    padding: 20px; /* Add padding for the caption text */
  }

  /* Optional: Ensure the image inside the modal is responsive */
  #modalImage {
    width: 100%;
    height: auto;
  }
</style>


 
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
         
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
    <!-- jQuery Sparklines -->
    <script src="../prod/vendors/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
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
    <!-- bootstrap-daterangepicker -->
    <script src="../prod/vendors/moment/min/moment.min.js"></script>
    <script src="../prod/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="../prod/build/js/custom.min.js"></script>
<!-- JavaScript to Change Modal Image -->
    <script>
  // When an image is clicked, change the src of the modal image
  $('#overviewCarousel').on('click', 'img', function() {
    var imageSrc = $(this).data('image');
    $('#modalImage').attr('src', imageSrc);
  });
</script>
  </body>
</html>