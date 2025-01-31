<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Super Admin Login</title>

    <!-- Bootstrap -->
    <link href="../prod/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../prod/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../prod/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../prod/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../prod/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <!-- Logo added here -->
            <div class="text-center">
              <img src="../prod/assets/img/lguiguig.png" alt="Logo" style="width: 150px; height: auto; margin-bottom: 20px;">
            </div>
            <form action="login_backend.php" method="POST">
              <h1>Super Admin</h1>
              <div>
                <input type="text" class="form-control" name="username" placeholder="Username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" name="password" placeholder="Password" required="" />
              </div>
              <div>
                <button class="btn btn-success submit" type="submit">Log in</button>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />

                <div>
  <p>Welcome to the LGU Iguig Admin Login. Please enter your credentials to log in.</p>
  <a href = "../admin/admin_login.php">Admin Login</a>
</div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
