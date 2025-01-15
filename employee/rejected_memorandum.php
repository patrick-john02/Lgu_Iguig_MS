<?php 
include('../config/config.php');
// Start session to get the logged-in user's ID
session_start();
$logged_in_user_id = $_SESSION['user_id'] ?? null;

// Ensure the user is logged in
if (!$logged_in_user_id) {
    echo "You must be logged in to view this page.";
    exit;
}

$query = "
    SELECT 
        d.document_id, 
        d.title, 
        d.document_type, 
        d.date, 
        d.subject, 
        -- Get the latest status from the DocumentTimeline table
        (SELECT dt.status 
         FROM DocumentTimeline dt 
         WHERE dt.document_id = d.document_id 
         ORDER BY dt.status_date DESC LIMIT 1) AS timeline_status,
        -- Get the latest status change date from DocumentTimeline
        (SELECT dt.status_date 
         FROM DocumentTimeline dt 
         WHERE dt.document_id = d.document_id 
         ORDER BY dt.status_date DESC LIMIT 1) AS timeline_date,
        -- Get the reason for the latest status change
        (SELECT dt.action_reason 
         FROM DocumentTimeline dt 
         WHERE dt.document_id = d.document_id 
         ORDER BY dt.status_date DESC LIMIT 1) AS action_reason
    FROM Document d
    LEFT JOIN DocumentTimeline dt ON d.document_id = dt.document_id
    WHERE d.authored_by = :authored_by
    AND d.document_type = 'Memorandum'  -- Filter for Memorandum documents only
    AND (SELECT dt.status 
         FROM DocumentTimeline dt 
         WHERE dt.document_id = d.document_id 
         ORDER BY dt.status_date DESC LIMIT 1) = 'Rejected'  -- Filter for Rejected status from DocumentTimeline
    ORDER BY dt.status_date DESC";


$stmt = $pdo->prepare($query);
$stmt->bindValue(':authored_by', $logged_in_user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Submitted Memorandum</title>

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

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Rejected Memorandum Lists</h3>
              </div>

            </div>

            <div class="clearfix"></div>

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><small>Table</small></h2>
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
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Memorandum No.</th>
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Last Status Change Date</th>
            <th>Action Reason</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['document_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['document_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['timeline_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['timeline_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['action_reason'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No Rejected Memorandum found.</td>
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
        <footer>
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