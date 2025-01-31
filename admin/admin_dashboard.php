<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');
$user_id = $_SESSION['user_id'];

// Fetch total users (admin users only)
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users WHERE role_id = 1 AND status = 'active'");
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Fetch total users with role_id = 2
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_role2_users FROM users WHERE role_id = 2 AND status = 'active'");
$stmt->execute();
$total_role2_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_role2_users'];

// Fetch total events (non-archived)
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_events FROM event WHERE is_archived = 0");
$stmt->execute();
$total_events = $stmt->fetch(PDO::FETCH_ASSOC)['total_events'];

// Fetch document statuses
$stmt = $pdo->prepare("SELECT status, COUNT(*) AS count FROM documenttimeline GROUP BY status");
$stmt->execute();
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pending = 0;
$approved = 0;
$rejected = 0;
foreach ($statuses as $status) {
    if ($status['status'] === 'Pending') {
        $pending = $status['count'];
    } elseif ($status['status'] === 'Approved') {
        $approved = $status['count'];
    } elseif ($status['status'] === 'Rejected') {
        $rejected = $status['count'];
    }
}

// Fetch recent documents (limit to 10)
$stmt = $pdo->prepare("SELECT d.id, d.document_id, d.title, u.first_name, u.last_name, d.date
                       FROM document d
                       JOIN users u ON d.authored_by = u.user_id
                       WHERE d.is_archived = 0
                       ORDER BY d.date DESC
                       LIMIT 10");
$stmt->execute();
$recent_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     <!-- top tiles -->
     <div class="row">
  <div class="tile_count">
    <!-- Total Users -->
    <div class="tile_stats_count">
      <div class="tile">
        <span class="count_top"><i class="fa fa-user"></i> Admin</span>
        <div class="count"><?= $total_users ?></div>
      </div>
    </div>
    <!-- Total Users with Role ID = 2 -->
    <div class="tile_stats_count">
      <div class="tile">
      <a href="manage_users.php" class="link">
        <span class="count_top"><i class="fa fa-users"></i> Employee's</span>
        <div class="count"><?= $total_role2_users ?></div>
        </a>
      </div>
    </div>


    <!-- Total Events -->
    <div class="tile_stats_count">
      <div class="tile">
      <a href="admin_events.php" class="link">
        <span class="count_top"><i class="fa fa-calendar"></i> Total Events</span>
        <div class="count"><?= $total_events ?></div>
        </a>
      </div>
    </div>
    
    <!-- Pending Documents -->
    <div class="tile_stats_count">
      <div class="tile">
        <a href="submitted_resolution.php" class="link">
          <span class="count_top"><i class="fa fa-clock-o"></i> Pending</span>
          <div class="count"><?= $pending ?></div>
        </a>
      </div>
    </div>
    
    <!-- Approved Documents -->
    <div class="tile_stats_count">
      <div class="tile">
        <a href="approved_resolution.php" class="link">
          <span class="count_top"><i class="fa fa-check"></i> Approved</span>
          <div class="count"><?= $approved ?></div>
        </a>
      </div>
    </div>
    
    <!-- Rejected Documents -->
    <div class="tile_stats_count">
      <div class="tile">
      <a href="rejected_resolutions.php" class="link">
        <span class="count_top"><i class="fa fa-times"></i> Rejected</span>
        <div class="count"><?= $rejected ?></div>
        </a>
     
  
    
  </div>
</div>
<!-- /top tiles -->
<style>
  .tile_count {
    display: flex;
    flex-wrap: wrap;
    gap: 80px;
    justify-content: space-evenly;
  }

  .tile_stats_count {
    flex: 1; /* Allows tiles to grow and occupy available space evenly */
    min-width: 180px; /* Ensures tiles remain readable on smaller screens */
    max-width: 250px; /* Prevents tiles from growing too large */
  }

  .tile {
    background-color: #ffffff;
    padding: 20px 15px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease-in-out;
    text-align: center;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .tile:hover {
    transform: translateY(-8px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
  }

  .tile .count {
    font-size: 36px;
    font-weight: bold;
    color: #333;
  }

  .count_top {
    font-size: 16px;
    color: #666;
    margin-bottom: 10px;
  }

  .fa {
    margin-right: 8px;
  }

  /* Add unique background colors for each tile */
  .tile:nth-child(1) {
    background-color: #f1f8ff;
  }

  .tile:nth-child(2) {
    background-color: #e8f7e7;
  }

  .tile:nth-child(3) {
    background-color: #fff4e6;
  }

  .tile:nth-child(4) {
    background-color: #e9ffe6;
  }

  .tile:nth-child(5) {
    background-color: #ffe6e6;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .tile_count {
      flex-direction: column;
      align-items: stretch;
    }

    .tile_stats_count {
      max-width: none; /* Remove the max-width on smaller screens */
      width: 100%; /* Ensure tiles take the full width */
    }
  }
</style>


<div class="col-md-12 col-sm-6">
    <div class="x_panel">
        <div class="x_title">
            <h2>Recent Documents</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Settings 1</a>
                        <a class="dropdown-item" href="#">Settings 2</a>
                    </div>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Document ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_documents as $index => $document): ?>
                        <tr>
                            <th scope="row"><?php echo $index + 1; ?></th>
                            <td><?php echo htmlspecialchars($document['document_id']); ?></td>
                            <td><?php echo htmlspecialchars($document['title']); ?></td>
                            <td><?php echo htmlspecialchars($document['first_name'] . ' ' . $document['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($document['date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_documents)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No recent documents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

                  </div>
                </div>
              </div>
          </div>
        </div>
        <footer>
          <div class="pull-right">
          
          </div>
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
	
  </body>
</html>
