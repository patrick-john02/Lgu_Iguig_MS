<?php 
include('../config/config.php');

// Start session to get the logged-in user's ID
session_start();
$logged_in_user_id = $_SESSION['user_id'] ?? null;

// Query to fetch all public memorandums (not filtering by authored_by)
$query = "
    SELECT 
        d.document_id, 
        d.title, 
        d.document_type, 
        d.date, 
        d.subject, 
        df.file_path
    FROM Document d
    LEFT JOIN documentfiles df ON d.document_id = df.document_id
    WHERE d.document_type = 'Memorandum'  -- Filter for Memorandum documents only
    AND d.is_archived = 0               -- Exclude archived documents
    AND d.is_rejected = 0               -- Exclude rejected documents
    AND d.is_approved = 0               -- Exclude approved documents
    ORDER BY d.date DESC";

$stmt = $pdo->prepare($query);
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
          <p>Memorandum</p>
<div class="row no-print">
    <div class="">
        <button class="btn btn-default" onclick="printTable();"><i class="fa fa-print"></i> Print Table</button>
    </div>
</div>
            <div class="page-title">
              <div class="title_left">
                <h3> Memorandum Lists</h3>
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
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($result)): ?>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['document_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['document_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <!-- View File button -->
                        <?php if ($row['file_path']): ?>
                            <a href="view_file.php?file_path=<?= urlencode($row['file_path']); ?>" class="btn btn-primary btn-sm">
                                View File
                            </a>
                        <?php else: ?>
                            <span>No file uploaded</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No memorandums found.</td>
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

    <script>
    function printTable() {
        var printWindow = window.open('', '', 'height=600,width=800');
        var tableContent = document.querySelector('.table').outerHTML;  // Get the table HTML
        
        // Construct the content of the print window
        printWindow.document.write('<html><head><title>Print Table</title>');
        printWindow.document.write('<link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 0; }');
        printWindow.document.write('.header { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; }');
        printWindow.document.write('.header h1 { text-align: center; flex-grow: 1; margin: 0; }');
        printWindow.document.write('.header h2, .header h5 { text-align: center; margin: 0; }');
        printWindow.document.write('.header img { max-width: 100px; height: auto; }');
        
        // Style for the table to center it
        printWindow.document.write('.table-container { display: flex; justify-content: center; margin-top: 20px; }');
        printWindow.document.write('table { width: 80%; border-collapse: collapse; }');
        printWindow.document.write('table, th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }');
        
        // Hide specific columns (1, 3, and 6)
        printWindow.document.write('th:nth-child(1), td:nth-child(1), th:nth-child(3), td:nth-child(3), th:nth-child(6), td:nth-child(6) { display: none; }');
        
        printWindow.document.write('</style></head><body>');
        
        // Add custom header with logos and title centered
        printWindow.document.write('<div class="header">');
        // Left logo
        printWindow.document.write('<img src="../prod/assets/img/lgu.jpg" alt="Left Logo">');
        
        // Title (Centered)
        printWindow.document.write('<div>');
        printWindow.document.write('<h2>Republic of the Philippines</h2>');
        printWindow.document.write('<h5>Province of Cagayan</h5>');
        printWindow.document.write('<h2>MUNICIPALITY OF IGUIG</h2>');
        printWindow.document.write('<h2><strong>OFFICE OF THE SANGGUNIANG BAYAN</strong></h2>');
        printWindow.document.write('</div>');
        
        // Right logo
        printWindow.document.write('<img src="../prod/assets/img/astig.png" alt="Right Logo">');
        printWindow.document.write('</div>');  // End header div
        
        // Add the table content to the print window within a container to center it
        printWindow.document.write('<p>Below is the list of your submitted Memorandums:</p>');
        printWindow.document.write('<div class="table-container">');
       
        printWindow.document.write(tableContent);
        printWindow.document.write('</div>');  // End table-container div
        
        printWindow.document.write('</body></html>');

        // Ensure the content is fully loaded before printing
        printWindow.document.close();  // Close the document and open the print dialog
        printWindow.print();
    }
      // Search functionality
document.getElementById('tableSearch').addEventListener('input', function(event) {
    var searchTerm = event.target.value.toLowerCase();
    var rows = document.querySelectorAll('.table tbody tr');

    rows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        var matched = false;

        cells.forEach(function(cell) {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                matched = true;
            }
        });

        if (matched) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
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