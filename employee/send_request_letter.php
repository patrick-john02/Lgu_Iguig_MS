<?php
session_start();
include('../config/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];
     
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

<body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
        

            <div class="clearfix"></div>


        
	  <?php include ('includes/sidebar.php');?>
      <?php include ('includes/navbar.php');?>
					

			<!-- page content -->
			<div class="right_col" role="main">
				<div class="">
					<div class="page-title">
						<div class="title_left">
							<h3>Send a Request Letter</h3>
						</div>

						
					</div>
					<div class="clearfix"></div>
					<div class="row">
						<div class="col-md-12 col-sm-12 ">
							<div class="x_panel">
								<div class="x_title">
									<h2> <small>request letter </small></h2>
									<ul class="nav navbar-right panel_toolbox">
										<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
										</li>
										<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i></a>
											<ul class="dropdown-menu" role="menu">
												<li><a class="dropdown-item" href="#">Settings 1</a>
												</li>
												<li><a class="dropdown-item" href="#">Settings 2</a>
												</li>
											</ul>
										</li>
										<li><a class="close-link"><i class="fa fa-close"></i></a>
										</li>
									</ul>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<br />
									<form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

										<div class="item form-group">
											<label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Title <span class="required">*</span>
											</label>
											<div class="col-md-6 col-sm-6 ">
												<input type="text" id="first-name" required="required" class="form-control ">
											</div>
										</div>
										<div class="item form-group">
											<label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">document type <span class="required">*</span>
											</label>
											<div class="col-md-6 col-sm-6 ">
												<input type="text" id="last-name" name="last-name" required="required" class="form-control">
											</div>
										</div>
										<div class="item form-group">
											<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align">description</label>
											<div class="col-md-6 col-sm-6 ">
												<input id="middle-name" class="form-control" type="text" name="middle-name">
											</div>
										</div>
										<div class="item form-group">
											<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align">upload your document</label>
											<div class="col-md-6 col-sm-6 ">
												<input id="middle-name" class="form-control" type="text" name="middle-name">
											</div>
										</div>
										
										<div class="ln_solid"></div>
										<div class="item form-group">
											<div class="col-md-6 col-sm-6 offset-md-3">
												<button class="btn btn-primary" type="button">Cancel</button>
												<button class="btn btn-primary" type="reset">Reset</button>
												<button type="submit" class="btn btn-success">Request</button>
											</div>
										</div>

									</form>
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

</body></html>
