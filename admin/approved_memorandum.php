<?php
session_start();

// Ensure only admin can perform this action
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');

// Handle archiving of approved Memorandum via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the document IDs from the POST request (AJAX)
    $documentIds = json_decode($_POST['document_ids'], true);

    if (!empty($documentIds)) {
        try {
            // Prepare the SQL query to update the is_archived field to 1
            $placeholders = implode(',', array_fill(0, count($documentIds), '?'));
            $query = "UPDATE document SET is_archived = 1 WHERE document_id IN ($placeholders)";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($documentIds);

            echo "Approved Memorandums archived successfully!";
        } catch (PDOException $e) {
            echo "Error archiving Memorandums: " . $e->getMessage();
        }
    } else {
        echo "No document IDs provided.";
    }
    exit;
}

// Fetch all non-archived, approved Memorandums
try {
    $query = "
        SELECT 
            d.document_id AS document_id, 
            d.title AS resolution_title,
            d.date AS resolution_date,
            d.subject AS resolution_subject,
            CONCAT(u.first_name, ' ', u.last_name) AS author_name,
            dt.status AS resolution_status
        FROM document d
        LEFT JOIN users u ON d.authored_by = u.user_id
        LEFT JOIN (
            SELECT 
                document_id, 
                status
            FROM documenttimeline
            WHERE status IN ('Approved', 'Rejected')
            ORDER BY status_date DESC
        ) dt ON dt.document_id = d.document_id
        WHERE d.document_type = 'Memorandum'
          AND d.is_archived = 0
          AND d.is_approved = 1
        ORDER BY d.date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $resolutions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching Memorandums: " . $e->getMessage();
    exit;
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
       <div class="x_content">

<p>Approved Memorandum's</p>
<div class="row no-print">
    <div class="">
        <button class="btn btn-default" onclick="printTable();"><i class="fa fa-print"></i> Print Table</button>
    </div>
</div>


<div class="table-responsive">
<input type="text" id="tableSearch" class="form-control" placeholder="Search all data" style="margin-bottom: 10px; width: 100%;">
<div class="table-responsive">
    

<table class="table table-striped jambo_table bulk_action">
    <thead>
        <tr class="headings">
            <td class="a-center">
                <input type="checkbox" id="check-all" class="flat">
            </td>
            <th class="column-title">Memorandum ID</th>
            <th class="column-title">Memorandum Title</th>
            <th class="column-title">Date</th>
            <th class="column-title">Author</th>
            <th class="column-title">Status</th>
            <th class="column-title no-link last"><span class="nobr">Action</span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($resolutions)): ?>
            <?php foreach ($resolutions as $resolution): ?>
                <tr class="even pointer">
                    <td class="a-center">
                        <input type="checkbox" class="flat" name="table_records" data-id="<?php echo htmlspecialchars($resolution['document_id']); ?>">
                    </td>
                    <td class=""><?php echo htmlspecialchars($resolution['document_id']); ?></td>
                    <td class=""><?php echo htmlspecialchars($resolution['resolution_title']); ?></td>
                    <td class=""><?php echo htmlspecialchars($resolution['resolution_date']); ?></td>
                    <td class=""><?php echo htmlspecialchars($resolution['author_name']); ?></td>
                    <td class=""><?php echo htmlspecialchars($resolution['resolution_status']); ?></td>
                    <td class="last">
                        <a href="document_info.php?document_id=<?php echo urlencode($resolution['document_id']); ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No Approved Memorandums found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<button id="archiveButton" class="btn btn-danger">Archive</button>

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
        printWindow.document.write('<p>Below is the list of Approved Memorandums:</p>');
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
<script>
document.getElementById('archiveButton').addEventListener('click', function() {
    var selectedIds = [];
    var checkboxes = document.querySelectorAll('.table tbody input[type="checkbox"]:checked'); // Get checked checkboxes

    checkboxes.forEach(function(checkbox) {
        var documentId = checkbox.getAttribute('data-id'); // Ensure it's string (document_id from database)
        if (documentId) {
            selectedIds.push(documentId); // Collect document_id (string) of checked checkboxes
        }
    });

    // Check if any Memorandums are selected
    if (selectedIds.length > 0) {
        console.log("Selected Memorandums to Archive: ", selectedIds); // Debugging

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'approved_memorandum.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Approved Memorandums archived successfully!');
                location.reload(); // Reload to reflect changes
            } else {
                alert('Failed to archive Approved Memorandums.');
            }
        };

        // Prepare the data to be sent (document_ids should be in the correct format)
        var data = 'document_ids=' + JSON.stringify(selectedIds);
        xhr.send(data);
    } else {
        alert('No selected documents to archive.');
    }
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