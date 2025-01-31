<?php
session_start();
include('../config/config.php'); // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get a list of upcoming sessions
$sql = "
    SELECT session_id, name, subject, start_time, event_type, date 
    FROM sessions 
    WHERE (date >= NOW() AND recurrence = 'One-time') 
       OR (recurrence != 'One-time') 
    ORDER BY date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Request Letter</title>

	<!-- Bootstrap -->
	<link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- NProgress -->
	<link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
	<!-- iCheck -->
	<link href="../prod/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	<!-- bootstrap-wysiwyg -->
	<link href="../prod/vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
	<!-- Select2 -->
	<link href="../prod/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
	<!-- Switchery -->
	<link href="../prod/vendors/switchery/dist/switchery.min.css" rel="stylesheet">
	<!-- starrr -->
	<link href="../prod/vendors/starrr/dist/starrr.css" rel="stylesheet">
	<!-- bootstrap-daterangepicker -->
	<link href="../prod/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

	<!-- Custom Theme Style -->
	<link href="../prod/build/css/custom.min.css" rel="stylesheet">
</head>
<style>
        /* Page Layout */
        .container.body {
            margin-top: 20px;
        }

        .page-title h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
        }
        .table tr:hover {
            background-color: #f1f1f1;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #1ABB9C;
            border-color: #1ABB9C;
        }
        .btn-primary:hover {
            background-color: #16a085;
            border-color: #16a085;
        }

        /* Page Layout - Profile Section */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .profile_info span {
            font-size: 16px;
            color: white;
        }

        .profile_info h2 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
            color: white;
        }

        /* Fixed-size box */
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

        /* Ensuring responsiveness */
        @media (max-width: 767px) {
            .tile-stats.fixed-size-box {
                height: 220px;
            }
            .table th, .table td {
                padding: 8px;
            }
        }
    </style>

<body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
        

          


        
	  <?php include ('includes/sidebar.php');?>
      <?php include ('includes/navbar.php');?>
					

      <div class="right_col" role="main">
      <div class="container-fluid">
      <div class="row">
        
      
      
                <h3>Meeting and Sessions Attendance</h3>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <ul class="nav navbar-right panel_toolbox">
        <div class="clearfix"></div>

        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
        </div>  </div>


<?php if (count($sessions) > 0): ?>
    <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
    <div class="card-box table-responsive">
    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Session Name</th>
                <th>Subject</th>
                <th>Start Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['name']); ?></td>
                    <td><?php echo htmlspecialchars($session['subject']); ?></td>
                    <td><?php echo date('H:i', strtotime($session['start_time'])); ?></td>
                    <td>
                        <!-- Check if attendance is already marked -->
                        <?php
                        $attendance_check_sql = "SELECT * FROM session_attendance WHERE session_id = :session_id AND user_id = :user_id";
                        $attendance_stmt = $pdo->prepare($attendance_check_sql);
                        $attendance_stmt->bindParam(':session_id', $session['session_id']);
                        $attendance_stmt->bindParam(':user_id', $user_id);
                        $attendance_stmt->execute();
                        $attendance = $attendance_stmt->fetch(PDO::FETCH_ASSOC);
                        ?>

                        <?php if ($attendance): ?>
                            <span>Attendance Already Marked</span>
                        <?php else: ?>
                            <!-- Form to mark attendance -->
                            <form action="mark_attendance.php" method="POST">
                                <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No upcoming sessions.</p>
<?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>

      



               
            </div>
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
	<!-- bootstrap-progressbar -->
	<script src="../prod/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
	<!-- iCheck -->
	<script src="../prod/vendors/iCheck/icheck.min.js"></script>
	<!-- bootstrap-daterangepicker -->
	<script src="../prod/vendors/moment/min/moment.min.js"></script>
	<script src="../prod/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
	<!-- bootstrap-wysiwyg -->
	<script src="../prod/vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
	<script src="../prod/vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
	<script src="../prod/vendors/google-code-prettify/src/prettify.js"></script>
	<!-- jQuery Tags Input -->
	<script src="../prod/vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
	<!-- Switchery -->
	<script src="../prod/vendors/switchery/dist/switchery.min.js"></script>
	<!-- Select2 -->
	<script src="../prod/vendors/select2/dist/js/select2.full.min.js"></script>
	<!-- Parsley -->
	<script src="../prod/vendors/parsleyjs/dist/parsley.min.js"></script>
	<!-- Autosize -->
	<script src="../prod/vendors/autosize/dist/autosize.min.js"></script>
	<!-- jQuery autocomplete -->
	<script src="../prod/vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
	<!-- starrr -->
	<script src="../prod/vendors/starrr/dist/starrr.js"></script>
	<!-- Custom Theme Scripts -->
	<script src="../prod/build/js/custom.min.js"></script>

</body>
</html>
