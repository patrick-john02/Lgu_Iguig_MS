<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php'); // Database configuration

$message = ''; // Variable to store success/error messages
$events = [];  // Initialize $events array

if (isset($_POST['create_event'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $event_date = $_POST['event_date'];
    $event_type = htmlspecialchars(trim($_POST['event_type']));
    $approved_by = $_SESSION['user_id'];

    // Handle file upload
$image_path = null;
if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['event_image']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/DMS_Iguig/uploads/events/';
        $image_name = basename($_FILES['event_image']['name']);
        $unique_file = uniqid() . '_' . $image_name;
        $target_file = $upload_dir . $unique_file;

        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $image_path = '/uploads/events/' . $unique_file;
        } else {
            $message = "Failed to upload the image.";
        }
    } else {
        $message = "Invalid file type. Please upload JPG, PNG, or GIF images.";
    }
}

    if (empty($title) || empty($event_date) || empty($event_type)) {
        $message = "All fields must be filled.";
    } else {
        $query = "INSERT INTO `event` (`name`, `date`, `event_type`, `approved_by`, `image_path`) 
          VALUES (:title, :event_date, :event_type, :approved_by, :image_path)";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->bindParam(':event_type', $event_type);
            $stmt->bindParam(':approved_by', $approved_by);
            $stmt->bindParam(':image_path', $image_path);

            if ($stmt->execute()) {
                $message = "Event created successfully.";
            } else {
                $message = "Error creating event: " . implode(", ", $stmt->errorInfo());
            }
        } catch (Exception $e) {
            $message = "Error creating event: " . $e->getMessage();
        }
    }
}


// Fetch events for the calendar and table
try {
    $stmt = $pdo->query("SELECT id, name AS title, date AS start, event_type, image_path FROM `event` WHERE is_archived = FALSE");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching events: " . $e->getMessage();
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

    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- FullCalendar -->

    <!-- Pikaday CSS -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">

<!-- Pikaday JavaScript -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>

<!-- FullCalendar CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet">

<!-- FullCalendar JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>


    <!-- Custom styling plus plugins -->
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
  #calendar {
    width: 100%;   /* Ensure it takes full width */
    height: 600px;  /* Adjust height as needed */
    margin-top: 20px;
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
            <?php include('includes/admin_sidebar.php'); ?>
            <?php include('includes/admin_navbar.php'); ?>
        </div>
        <div class="right_col" role="main">
            <div class="x_content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_title">
                            <h2>Events</h2>
<!-- Button to trigger the modal positioned at the top-right corner -->
<div class="text-right">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#eventModal">
        Create New Event
    </button>
</div>
                            </div>
                            <div class="x_content">
                                <div class="events-list">
                                  <!--  <h3>Upcoming Events</h3>
                                    <table class="table table-striped">
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Date</th>
            <th>Event Type</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td>
                    <?php if (!empty($event['image_path'])): ?>
                        <!-- Button to view the image in a new tab
                        <a href="<?php echo htmlspecialchars('..' . $event['image_path']); ?>" target="_blank" class="btn btn-primary">View</a>
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($event['title']); ?></td>
                <td><?php echo htmlspecialchars($event['start']); ?></td>
                <td><?php echo htmlspecialchars($event['event_type']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>  -->

                               
</div>
                                <div id="calendar"></div>
                            </div>
                            <div id="eventModalDetails" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" alt="Event Image" class="event-image img-fluid mb-3" style="max-width: 100%; height: auto; display: none;">
                <p><strong>Date:</strong> <span class="event-date"></span></p>
                <p><strong>Type:</strong> <span class="event-type"></span></p>
            </div>
        </div>
    </div>
</div>
           
   <!-- Modal for creating event -->
   <div id="eventModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="eventForm" action="admin_events.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
    <label for="title">Event Description/Caption</label>
    <textarea class="form-control" id="title" name="title" rows="4" required></textarea>
</div>

    <div class="form-group">
        <label for="event_date">Event Date</label>
        <input type="date" class="form-control" id="event_date" name="event_date" required min="2000-01-02" max="2030-12-31">
    </div>
    <div class="form-group">
        <label for="event_type">Event Type</label>
        <select class="form-control" id="event_type" name="event_type">
            <option value="Cultural">Cultural</option>
            <option value="Special Session">Special Session</option>
            <option value="Governmental">Governmental</option>
        </select>
    </div>
    <div class="form-group">
        <label for="event_image">Event Image</label>
        <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
    </div>
    <button type="submit" name="create_event" class="btn btn-primary">Create Event</button>
</form>

                    <!-- Show message if any -->
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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

<!-- jQuery -->
<script src="../prod/vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../prod/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
<script>
   $(document).ready(function () {
    $('#calendar').fullCalendar({
        events: <?php echo json_encode($events); ?>,  
        height: 'auto',  
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month'
        },
        eventClick: function (event) {
            $('#eventModalDetails .modal-title').text(event.title);
            
            // Format the date to remove time
            var eventDate = new Date(event.start);
            var formattedDate = eventDate.toLocaleDateString('en-GB');  // You can adjust this for your locale if needed

            $('#eventModalDetails .event-date').text(formattedDate);  // Display the formatted date
            $('#eventModalDetails .event-type').text(event.event_type);

            // Display the event image if available
            if (event.image_path) {
                // Prepend '../' to the image path if necessary (relative to the current script)
                var imagePath = '../' + event.image_path;
                $('#eventModalDetails .event-image').attr('src', imagePath).show();
            } else {
                $('#eventModalDetails .event-image').hide();
            }

            $('#eventModalDetails').modal('show');
        },
    });

    // Initialize the datepicker
    $('#event_date').datepicker({
        format: 'yyyy-mm-dd',
        startDate: '0d',
        autoclose: true
    });
});
</script>

</body>
</html>