<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php'); // Database configuration

if (isset($_POST['create_event'])) {
    // Sanitize and validate input
    $title = $_POST['title'];
    $description = $_POST['descr'];
    $event_date = $_POST['event_date'];  // The event date will be populated automatically
    $event_type = $_POST['event_type'];
    $approved_by = $_SESSION['user_id']; // Admin who is creating the event

    // Query to insert event into the database using PDO
    $query = "INSERT INTO `event` (`name`, `date`, `event_type`, `approved_by`) 
              VALUES (:title, :event_date, :event_type, :approved_by)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':event_date', $event_date);
    $stmt->bindParam(':event_type', $event_type);
    $stmt->bindParam(':approved_by', $approved_by);
    
    if ($stmt->execute()) {
        $message = "Event created successfully.";
    } else {
        $message = "Error creating event: " . $stmt->errorInfo()[2]; // PDO error
    }
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
    <link href="../prod/vendors/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="../prod/vendors/fullcalendar/dist/fullcalendar.print.css" rel="stylesheet" media="print">

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

         
          <?php  include ('includes/admin_sidebar.php');?>
          <?php include ('includes/admin_navbar.php');?>

       <!-- page content -->
       <div class="right_col" role="main">
       <div class="x_content">

       <div class="row">
              <div class="col-md-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Events</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Settings 1</a>
                            <a class="dropdown-item" href="#">Settings 2</a>
                          </div>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div id='calendar'></div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
</div>
              </div>
          </div>
        </div>
        <footer>
          <div class="pull-right">
          
          </div>
          <div class="clearfix"></div>
        </footer>
         <!-- footer content -->
         <footer>
          <div class="pull-right">
           
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>
    <?php
// Fetch events from the database to display on the calendar
$events_query = "SELECT id, name, date FROM event WHERE is_archived = 0 ORDER BY date";
$stmt = $pdo->prepare($events_query);
$stmt->execute();
$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $events[] = [
        'title' => $row['name'],
        'start' => $row['date'],
        'id' => $row['id']
    ];
}

?>
<script>
  $(document).ready(function() {
    $('#calendar').fullCalendar({
      events: <?php echo json_encode($events); ?>, // Ensure the PHP array is passed correctly to JavaScript
      eventClick: function(event) {
        // If you need to handle event clicks, you can add an event handler here
        alert('Event: ' + event.title);
      }
    });
  });
</script>


    <!-- calendar modal -->
    <div id="CalenderModalNew" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">New Calendar Entry</h4>
          </div>
          <div class="modal-body">
            <div id="testmodal" style="padding: 5px 20px;">
            <form id="antoform" class="form-horizontal calender" role="form" method="POST" action="admin_events.php">
  <div class="form-group">
    <label class="col-sm-3 control-label">Title</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="title" name="title" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">Description</label>
    <div class="col-sm-9">
      <textarea class="form-control" style="height:55px;" id="descr" name="descr" required></textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">Event Date</label>
    <div class="col-sm-9">
      <input type="date" class="form-control" id="event_date" name="event_date" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">Event Type</label>
    <div class="col-sm-9">
      <select class="form-control" name="event_type" required>
        <option value="Cultural">Cultural</option>
        <option value="Special Session">Special Session</option>
        <option value="Governmental">Governmental</option>
      </select>
    </div>
  </div>
  <input type="submit" class="btn btn-primary antosubmit" name="create_event" value="Save Event">
</form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary antosubmit">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <div id="CalenderModalEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel2">Edit Calendar Entry</h4>
          </div>
          <div class="modal-body">

            <div id="testmodal2" style="padding: 5px 20px;">
              <form id="antoform2" class="form-horizontal calender" role="form">
                <div class="form-group">
                  <label class="col-sm-3 control-label">Title</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="title2" name="title2">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Description</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" style="height:55px;" id="descr2" name="descr"></textarea>
                  </div>
                </div>

              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default antoclose2" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary antosubmit2">Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <div id="fc_create" data-toggle="modal" data-target="#CalenderModalNew"></div>
    <div id="fc_edit" data-toggle="modal" data-target="#CalenderModalEdit"></div>
    <!-- /calendar modal -->
        
    <!-- jQuery -->
    <script src="../prod/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="../prod/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../prod/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../prod/vendors/nprogress/nprogress.js"></script>
    <!-- FullCalendar -->
    <script src="../prod/vendors/moment/min/moment.min.js"></script>
    <script src="../prod/vendors/fullcalendar/dist/fullcalendar.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../prod/build/js/custom.min.js"></script>

    <script>
  $(document).ready(function() {
    // Initialize FullCalendar
    $('#calendar').fullCalendar({
      events: <?php echo json_encode($events); ?>, // Pass the PHP events to JavaScript
      eventClick: function(event) {
        // When an event is clicked, populate the edit modal and show it
        alert('Event: ' + event.title);  // Optionally show event title
        $('#title2').val(event.title);  // Fill in the event title
        $('#descr2').val(event.description); // Fill in the event description (if available)
        $('#event_date').val(event.start.format('YYYY-MM-DD')); // Set the event date
        $('#CalenderModalEdit').modal('show'); // Show the edit modal
      },
      dayClick: function(date, jsEvent, view) {
        // When a day is clicked, pre-fill the event date field
        var clickedDate = date.format(); // Get the clicked date (e.g., 2025-01-16)
        
        // Set the date field in the event creation modal
        $('#event_date').val(clickedDate);  // Set the event date to the clicked date
        $('#CalenderModalNew').modal('show'); // Show the create event modal
      }
    });
  });
</script>
  </body>
</html>