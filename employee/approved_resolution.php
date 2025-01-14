<?php 
include('../config/config.php');
// Start session to get the logged-in user's ID
session_start();
$logged_in_user_id = $_SESSION['user_id'];

// Fetch pending resolutions submitted by the logged-in user
$query = "SELECT title, document_type, date, subject, description, status 
          FROM Document 
          WHERE authored_by = :authored_by 
          AND status = 'Approved' 
          AND document_type = 'Resolution'";
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

    <title>DataTables | Gentelella</title>

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
  </head>
  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><span>LGU Iguig</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
             
              <div class="profile_info">
                <span>Welcome,</span>
                <h2>John sample</h2>
              </div>
            </div>

            <?php  include ('includes/sidebar.php');?>
            <?php include ('includes/navbar.php');?>


        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Approved Resolutions</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-secondary" type="button">Go!</button>
                    </span>
                  </div>
                </div>
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
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>Subject</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['document_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No approved resolutions found.</td>
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
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
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