<?php
// Fetch events from the database to display on the calendar
$events_query = "SELECT id, name, date FROM event WHERE is_archived = 0 ORDER BY date";
$result = mysqli_query($conn, $events_query);
$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'title' => $row['name'],
        'start' => $row['date'], // Assuming date is stored in 'Y-m-d' format
        'id' => $row['id']
    ];
}
?>
<script>
  $(document).ready(function() {
    $('#calendar').fullCalendar({
      events: <?php echo json_encode($events); ?>, // Populate events from PHP
      // Other calendar settings can be adjusted here
    });
  });
</script>
