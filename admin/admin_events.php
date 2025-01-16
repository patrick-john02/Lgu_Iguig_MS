<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php'); // Database configuration

$message = ''; // Variable to store success/error messages

if (isset($_POST['create_event'])) {
    // Sanitize and validate input
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['descr']));
    $event_date = $_POST['event_date']; // The event date populated by the FullCalendar
    $event_type = htmlspecialchars(trim($_POST['event_type']));
    $approved_by = $_SESSION['user_id']; // Admin who is creating the event

    // Debugging: Check if input is received correctly
    if (empty($title) || empty($description) || empty($event_date) || empty($event_type)) {
        $message = "All fields must be filled.";
    } else {
        // Query to insert event into the database using PDO
        $query = "INSERT INTO `event` (`name`, `date`, `event_type`, `approved_by`) 
                  VALUES (:title, :event_date, :event_type, :approved_by)";
        
        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->bindParam(':event_type', $event_type);
            $stmt->bindParam(':approved_by', $approved_by);
            
            if ($stmt->execute()) {
                $message = "Event created successfully.";
            } else {
                $message = "Error creating event: " . implode(", ", $stmt->errorInfo()); // PDO error
            }
        } catch (Exception $e) {
            $message = "Error creating event: " . $e->getMessage();
        }
    }
}

// Fetch existing events to display (add a query to get events)
$events = [];
try {
    $stmt = $pdo->query("SELECT * FROM `event`");
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
                                    <h3>Upcoming Events</h3>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Date</th>
                                                <th>Event Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($events as $event): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($event['date']); ?></td>
                                                    <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="calendar"></div>
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
                <form id="eventForm" action="admin_events.php" method="post">
                    <div class="form-group">
                        <label for="title">Event Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="descr">Description</label>
                        <input type="text" class="form-control" id="descr" name="descr" required>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <!-- Date Picker Input with min and max attributes -->
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

<script>
    $(document).ready(function() {
        // Initialize the datepicker
        $('#event_date').datepicker({
            format: 'yyyy-mm-dd', // Use the format YYYY-MM-DD
            startDate: '0d', // Disable past dates
            autoclose: true // Close datepicker after selection
        });
    });
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- jQuery -->
<script src="../prod/vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../prod/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            events: [
                <?php foreach ($events as $event): ?>
                    {
                        title: '<?php echo htmlspecialchars($event['name']); ?>',
                        start: '<?php echo htmlspecialchars($event['date']); ?>',
                        description: '<?php echo htmlspecialchars($event['event_type']); ?>'
                    },
                <?php endforeach; ?>
            ],
            eventClick: function(event) {
                alert('Event: ' + event.title + '\n' + event.description);
            }
        });
    });
</script>
</body>
</html>