<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');

$document_id = $_GET['document_id'] ?? null;

if (!$document_id) {
    echo "Document ID is required.";
    exit;
}

$options = [
    'Pending',
    'First Reading',
    'Second Reading',
    'Third Reading',
    'Committee Review',
    'For Approval',
    'Approved',
    'Rejected'
];


// Fetch document details, including the latest status from documenttimeline
$query = "
    SELECT 
        dt.status, dt.status_date, dt.action_reason, 
        CONCAT(u.first_name, ' ', u.last_name) AS action_by,
        d.title, d.description, d.date, d.authored_by, d.file_path, d.document_type
    FROM documenttimeline dt
    LEFT JOIN users u ON dt.action_by = u.user_id
    LEFT JOIN document d ON dt.document_id = d.document_id
    WHERE dt.document_id = :document_id
    ORDER BY dt.status_date DESC
    LIMIT 1
";


$stmt = $pdo->prepare($query);
$stmt->bindParam(':document_id', $document_id, PDO::PARAM_STR);
$stmt->execute();
$document = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch author name if not present in the document data
if ($document && isset($document['authored_by'])) {
    $author_query = "
        SELECT CONCAT(first_name, ' ', last_name) AS author_name
        FROM users
        WHERE user_id = :authored_by
    ";
    $author_stmt = $pdo->prepare($author_query);
    $author_stmt->bindParam(':authored_by', $document['authored_by'], PDO::PARAM_INT);
    $author_stmt->execute();
    $author = $author_stmt->fetch(PDO::FETCH_ASSOC);
    $author_name = $author ? $author['author_name'] : "Unknown Author";
} else {
    $author_name = "Unknown Author";
}

$timeline_query = "
    SELECT 
        dt.status, dt.status_date, dt.action_reason, 
        CONCAT(u.first_name, ' ', u.last_name) AS action_by
    FROM documenttimeline dt
    LEFT JOIN users u ON dt.action_by = u.user_id
    WHERE dt.document_id = :document_id
    ORDER BY dt.status_date DESC
";

$toast_message = "";
if (isset($_SESSION['status_update_success'])) {
    $toast_message = $_SESSION['status_update_success'];
    unset($_SESSION['status_update_success']); // Unset the message after it has been displayed
}

$timeline_stmt = $pdo->prepare($timeline_query);
$timeline_stmt->bindParam(':document_id', $document_id, PDO::PARAM_STR);
$timeline_stmt->execute();
$timeline = $timeline_stmt->fetchAll(PDO::FETCH_ASSOC);
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
  .message_date {
    width: 120px; /* Fixed width for date section */
    text-align: center;
    padding-right: 10px; /* Add padding for better spacing */
    position: relative;
    display: inline-block;
}

.message_date .date {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
}

.message_date .month {
    font-size: 14px;
    margin: 0;
}

.message_date .time {
    font-size: 12px;
    margin: 0;
}

.message_wrapper {
    display: inline-block; /* Keeps the message and date side by side */
    width: calc(100% - 140px); /* Adjust width for message wrapper to not overlap the date */
    vertical-align: top;
    padding-left: 10px; /* Spacing between date and message */
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
    <div class="">
        <div class="page-title">
            <div class="title_left">
            <a href="http://localhost/DMS_Iguig/super_admin/submitted_resolution.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
                <h3>Document Detail</h3>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Title <?php echo htmlspecialchars($document['title']); ?></h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="col-md-9 col-sm-9">
                        <br />
                        <div>
                            <h4>Document Timeline</h4>
                            <ul class="messages">
                                <?php foreach ($timeline as $entry): ?>
                                <li>
                                <div class="message_date">
                                    <h3 class="date text-info"><?php echo htmlspecialchars(date('d', strtotime($entry['status_date']))); ?></h3>
                                    <p class="month"><?php echo htmlspecialchars(date('F', strtotime($entry['status_date']))); ?></p>
                                    <p class="time"><?php echo htmlspecialchars(date('h:i A', strtotime($entry['status_date']))); ?></p>
                                </div>
                                    <div class="message_wrapper">
                                        <h4 class="heading"><?php echo htmlspecialchars($entry['action_by']); ?></h4>
                                        <blockquote class="message">Comment: <?php echo htmlspecialchars($entry['action_reason']); ?></blockquote>
                                        <p class="url">
                                            <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                                            <a href="#"><i class="fa fa-paperclip"></i> <?php echo htmlspecialchars($entry['status']); ?></a>
                                        </p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <!-- Project sidebar -->
                    <div class="col-md-3 col-sm-3">
                        <section class="panel">
                            <div class="x_title">
                                <h2>Project Description</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                           <h6>Document Type: <strong><?php echo htmlspecialchars($document['document_type']); ?></strong> </h6>
                                <h6>Description: <strong><?php echo htmlspecialchars($document['description']); ?></strong></h6>
                                <div class="project_detail">
                                    <h2>Author: <strong><?php echo htmlspecialchars($author_name); ?></strong></h2>
                                    <p>Date of Submission: <?php echo htmlspecialchars($document['date']); ?></p>
                                </div>
                                <h6>File: </h6>
                                <ul class="list-unstyled project_files">
                                    <li><a href="<?php echo htmlspecialchars($document['file_path']); ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> View Document</a></li>
                                </ul>
                                <br />
                                <form method="POST" action="update_status.php"> 
    <input type="hidden" name="document_id" value="<?php echo htmlspecialchars($document_id); ?>">

    <?php
    // Define the valid transitions for each status
    $valid_transitions = [
        'Pending' => ['First Reading', 'Rejected'],
        'First Reading' => ['Second Reading', 'Rejected'],
        'Second Reading' => ['Third Reading', 'Rejected'],
        'Third Reading' => ['Committee Review', 'Rejected'],
        'Committee Review' => ['For Approval', 'Rejected'],
        'For Approval' => ['Approved', 'Rejected'],
        'Approved' => [] // No transitions from Approved
    ];

    // Get the valid options for the current status
    $current_status = $document['status'];
    $available_options = $valid_transitions[$current_status] ?? [];
    ?>

    <select class="form-control" name="status" required>
        <option value="" disabled selected>--STATUS--</option>
        <?php foreach ($available_options as $status): ?>
            <option value="<?php echo $status; ?>" <?php echo ($current_status == $status) ? 'selected' : ''; ?>>
                <?php echo $status; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <textarea class="form-control" name="action_reason" rows="4" placeholder="Comment (optional)"></textarea>
    <br>

    <?php
    // Disable the button and change its class based on the status
    $button_disabled = ($current_status == 'Approved' || $current_status == 'Rejected') ? 'disabled' : '';
    $button_class = ($current_status == 'Approved') ? 'btn btn-success' : 'btn btn-primary';
    ?>

    <button type="submit" class="<?php echo $button_class; ?>" <?php echo $button_disabled; ?>>Update Status</button>
</form>

                            </div>
                        </section>
                    </div>
                    <!-- end project-detail sidebar -->
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
    <!-- Toast Notification -->
<?php if ($toast_message): ?>
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '<?php echo $toast_message; ?>'
            });
        });
    </script>
<?php endif; ?>

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

    <!-- ECharts -->
    <!-- <script src="../prod/vendors/echarts/dist/echarts.min.js"></script> -->


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


	
  </body>
</html>
