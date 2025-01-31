<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "Lgu_Iguig_MS";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle Event Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $event_type = $conn->real_escape_string($_POST['event_type']);
    $date = $conn->real_escape_string($_POST['date']);
    $image_path = "";

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/events/";
        $image_path = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Insert Event into Database
    $query = "INSERT INTO event (name, event_type, date, image_path) VALUES ('$name', '$event_type', '$date', '$image_path')";
    if ($conn->query($query) === TRUE) {
        $message = "Event created successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch Events for Calendar
$events = [];
$query = "SELECT id, name, event_type, date, image_path FROM event";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['name'] . " (" . $row['event_type'] . ")",
            'start' => $row['date'],
            'backgroundColor' => '#0073b7',
            'borderColor' => '#0073b7',
            'imagePath' => $row['image_path']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Calendar</title>
  <link rel="stylesheet" href="../lte/plugins/fullcalendar/main.css">
  <link rel="stylesheet" href="../lte/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create Event</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Event Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="event_type">Event Type:</label>
            <select name="event_type" id="event_type" class="form-control" required>
                <option value="Cultural">Cultural</option>
                <option value="Special Session">Special Session</option>
                <option value="Governmental">Governmental</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="image">Upload Image (optional):</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <button type="submit" name="create_event" class="btn btn-primary">Create Event</button>
    </form>

    <p class="mt-3"><?php if (isset($message)) echo $message; ?></p>

    <h2 class="mt-5">Calendar</h2>
    <div id="calendar"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title fw-bold" id="eventModalLabel">Event Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex flex-column align-items-center text-center">
          <h6 class="fw-bold mb-2">Event Name:</h6>
          <p id="eventName" class="fs-5 text-primary mb-3"></p>

          <h6 class="fw-bold mb-2">Event Type:</h6>
          <p id="eventType" class="fs-5 text-secondary mb-3"></p>

          <h6 class="fw-bold mb-2">Event Date:</h6>
          <p id="eventDate" class="fs-5 text-success mb-3"></p>

          <div id="eventImageContainer" class="mt-4">
            <h6 class="fw-bold mb-2">Event Image:</h6>
            <img id="eventImage" src="" alt="Event Image" class="img-thumbnail rounded shadow-sm" style="max-width: 80%; display: none;">
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light d-flex justify-content-center">
        <button type="button" class="btn btn-outline-primary px-4 py-2" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Scripts -->
<script src="../lte/plugins/jquery/jquery.min.js"></script>
<script src="../lte/plugins/fullcalendar/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: <?php echo json_encode($events); ?>,
      eventClick: function(info) {
        var event = info.event;

        // Populate modal with event details
        document.getElementById('eventName').textContent = event.title.split(" (")[0];
        document.getElementById('eventType').textContent = event.title.split(" (")[1].replace(")", "");
        document.getElementById('eventDate').textContent = event.start.toISOString().split('T')[0];

        if (event.extendedProps.imagePath) {
          var eventImage = document.getElementById('eventImage');
          eventImage.src = event.extendedProps.imagePath;
          eventImage.style.display = 'block';
        } else {
          document.getElementById('eventImage').style.display = 'none';
        }

        // Show modal
        var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        eventModal.show();
      }
    });
    calendar.render();
  });
</script>
</body>
</html>
