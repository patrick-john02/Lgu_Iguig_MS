<?php
session_start();

// Restrict access to authorized users
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php'); // Database connection

// Fetch all attendance records, including session and user details
$attendance_sql = "SELECT 
                        s.name AS session_name, 
                        s.subject, 
                        s.event_type, 
                        u.first_name, 
                        u.last_name, 
                        p.position_name, 
                        sa.status, 
                        DATE_FORMAT(sa.time_recorded, '%Y-%m-%d %H:%i:%s') AS time_recorded
                   FROM session_attendance sa
                   JOIN sessions s ON sa.session_id = s.session_id
                   JOIN users u ON sa.user_id = u.user_id
                   LEFT JOIN positions p ON u.position_id = p.id
                   ORDER BY s.name ASC, sa.time_recorded ASC";

$attendance_stmt = $pdo->prepare($attendance_sql);
$attendance_stmt->execute();
$attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);
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
      <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../prod/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    
    <link href="../prod/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../prod/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../prod/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../prod/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../prod/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


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
                <h3>Employee Sessions and Meeting Attendance</h3>
            </div>
        </div>

        

        <div class="clearfix"></div>

        

        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <ul class="nav navbar-right panel_toolbox">
                    <!-- <form method="GET" action="" class="form-inline">
    <div class="form-group">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
</form> -->

                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    
                      </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                            <div id="attendanceTableContainer">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Session Name</th>
                    <th>Subject</th>
                    <th>Event Type</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Time Recorded</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendance_records)) : ?>
                    <?php foreach ($attendance_records as $record) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['session_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['subject']); ?></td>
                            <td><?php echo htmlspecialchars($record['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($record['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['position_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                            <td><?php echo htmlspecialchars($record['time_recorded']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="text-center">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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

        <!-- footer content -->
        <footer>
          <div class="pull-right">
          
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>
    <script>
    function checkDateChange() {
        const currentDate = new Date();
        const storedDate = localStorage.getItem('lastCheckedDate');
        const currentDateString = currentDate.toISOString().split('T')[0]; // Get only the date part

        if (storedDate !== currentDateString) {
            // Update the stored date in localStorage
            localStorage.setItem('lastCheckedDate', currentDateString);

            // Reload the page or fetch new data via AJAX
            location.reload(); // Reloads the entire page
        }
    }

    // Check the date every minute
    setInterval(checkDateChange, 60000); // 60 seconds
</script>

<script>
    function loadAttendanceTable() {
        $.ajax({
            url: 'fetch_attendance.php', // Separate PHP file to handle data fetching
            method: 'GET',
            success: function(response) {
                $('#attendanceTableContainer').html(response); // Replace table content
            },
            error: function(xhr, status, error) {
                console.error('Error loading table:', error);
            }
        });
    }

    // Check the date every minute
    setInterval(() => {
        const currentDate = new Date();
        const storedDate = localStorage.getItem('lastCheckedDate');
        const currentDateString = currentDate.toISOString().split('T')[0]; // Get only the date part

        if (storedDate !== currentDateString) {
            localStorage.setItem('lastCheckedDate', currentDateString);
            loadAttendanceTable(); // Refresh table content
        }
    }, 60000); // 60 seconds
</script>


    <!-- jQuery -->
    <script src="../prod/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="../prod/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../prod/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../prod/vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="../prod/vendors/iCheck/icheck.min.js"></script>
    <!-- Datatables -->
    <script src="../prod/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../prod/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../prod/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../prod/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../prod/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../prod/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../prod/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../prod/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../prod/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../prod/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../prod/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../prod/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../prod/vendors/jszip/dist/jszip.min.js"></script>
    <script src="../prod/vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../prod/vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../prod/build/js/custom.min.js"></script>
	
  </body>
</html>
