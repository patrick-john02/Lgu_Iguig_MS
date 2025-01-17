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
  $stmt = $pdo->query("SELECT name AS title, date AS start, event_type, image_path FROM `event` WHERE is_archived = FALSE");
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $events = [];
  $error_message = "Error fetching events: " . $e->getMessage();
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

     <!-- FullCalendar CSS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">


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
<head>

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

<div class="container-fluid">
        <div class="row">
            <!-- Calendar Section -->
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Event Calendar</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <!-- Calendar Container -->
                        <div id="calendar"></div>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger mt-3">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

 <!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- FullCalendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>



    <script>
        $(document).ready(function () {
            // Ensure events are in a valid JSON format
            var events = <?php echo isset($events) ? json_encode($events) : '[]'; ?>;

            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: events,
                height: 'auto',
                eventClick: function (event) {
                    // Modal to display event details
                    let details = `
                        <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">${event.title}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Date:</strong> ${moment(event.start).format('MMMM DD, YYYY')}</p>
                                        <p><strong>Type:</strong> ${event.event_type || 'N/A'}</p>
                                        ${event.image_path ? `<img src="../${event.image_path}" class="img-fluid" alt="Event Image">` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(details);
                    $('#eventDetailsModal').modal('show');
                    $('#eventDetailsModal').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                }
            });
        });
    </script>
</body>
</html>