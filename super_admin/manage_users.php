<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');

try {
    $sql = "SELECT 
        u.user_id, 
        u.username, 
        u.email, 
        u.first_name, 
        u.last_name, 
        u.status, 
        r.role_name, 
        p.position_name,
        u.is_deleted,
        u.profile_picture
    FROM 
        users u
    JOIN 
        roles r ON u.role_id = r.role_id
    LEFT JOIN 
        positions p ON u.position_id = p.id
    WHERE 
        u.role_id = 2 
    AND 
        u.is_deleted = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch roles from the roles table
    $roles_sql = "SELECT role_id, role_name FROM roles";
    $roles_stmt = $pdo->prepare($roles_sql);
    $roles_stmt->execute();
    $roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch positions from the positions table
    $positions_sql = "SELECT id, position_name FROM positions";
    $positions_stmt = $pdo->prepare($positions_sql);
    $positions_stmt->execute();
    $positions = $positions_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit;
}

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after showing it
} else {
    $success_message = null;
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
      

<div class="col-md-12 col-sm-6">
    <div class="x_panel">
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
        <div class="x_title">
            <h2>List of Users</h2>
           


            <?php if ($success_message): ?>
    <script>
        alert("<?php echo $success_message; ?>");
    </script>
<?php endif; ?>
            <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addUserModal">
                                <i class="fa fa-plus"></i> Add User
                            </button>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
               
              
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
        <table class="table table-striped">
    <thead>
        <tr>
            <th hidden>Profile Picture</th>
            <th>Username</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Position</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td hidden>
                        <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'default.jpg'); ?>" 
                             alt="Profile Picture" 
                             class="img-thumbnail" 
                             style="width: 50px; height: 50px;">
                    </td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['position_name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-user-btn" 
                                data-user='<?php echo json_encode($user); ?>'>
                            <i class="fa fa-pencil"></i> Edit
                        </button>
                        <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this user?');">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No users found.</td>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="create_user.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_id">Position</label>
                        <select class="form-control" id="position_id" name="position_id">
                            <option value="">--Select Position--</option>
                            <?php foreach ($positions as $position): ?>
                                <option value="<?php echo htmlspecialchars($position['id']); ?>">
                                    <?php echo htmlspecialchars($position['position_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit User Modal -->
<div id="editUserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editUserForm" method="POST" action="update_user.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="form-group">
                        <label for="editUsername" hidden>Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required hidden>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" class="form-control" id="editLastName" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editPosition">Position</label>
                        <select class="form-control" id="editPosition" name="position_id">
                            <option value="">Select Position</option>
                            <?php
                            $positionSql = "SELECT id, position_name FROM positions";
                            $positionStmt = $pdo->prepare($positionSql);
                            $positionStmt->execute();
                            $positions = $positionStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($positions as $position) {
                                echo "<option value='" . $position['id'] . "'>" . htmlspecialchars($position['position_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(user) {
        // Populate modal fields with user data
        document.getElementById('editUserId').value = user.user_id;
        document.getElementById('editUsername').value = user.username;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editFirstName').value = user.first_name;
        document.getElementById('editLastName').value = user.last_name;
        document.getElementById('editPosition').value = user.position_id || ""; // Handle null positions
        document.getElementById('editStatus').value = user.status;

        // Show the modal
        $('#editUserModal').modal('show');
    }

    // Attach click event to all edit buttons
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-user-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const user = JSON.parse(this.dataset.user);
                openEditModal(user);
            });
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
