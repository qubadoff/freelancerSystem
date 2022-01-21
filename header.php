<?php
ob_start();
session_start();

require 'system/dbController.php';
?>
<!doctype html>
<html lang="en">
<head>

<!-- Basic Page Needs
================================================== -->
<title>Freelancer</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- CSS
================================================== -->
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/colors/blue.css">

</head>
<body>

<!-- Wrapper -->
<div id="wrapper">

<!-- Header Container
================================================== -->
<header id="header-container" class="fullwidth">

	<!-- Header -->
	<div id="header">
		<div class="container">
			
			<!-- Left Side Content -->
			<div class="left-side">
				
				<!-- Logo -->
				<div id="logo">
					<a href="index.php"><img src="assets/images/logo.png" alt=""></a>
				</div>

				<!-- Main Navigation -->
				<nav id="navigation">
					<ul id="responsive">
						<?php if(isset($_SESSION['id'])){ ?>
							<li><a href="dashboard/index.php">Panelə keçin</a></li>
						<?php } ?>
					</ul>
				</nav>
				<div class="clearfix"></div>
				<!-- Main Navigation / End -->
				
			</div>
			<!-- Left Side Content / End -->


			<!-- Right Side Content / End -->
			<div class="right-side">

				<!--  User Notifications -->
				<div class="header-widget">
					
					<!-- Notifications -->
					<div class="header-notifications">

						<!-- Trigger -->
						<div class="header-notifications-trigger">
							<a href="#"><i class="icon-line-awesome-language"></i></a>
						</div>

						<!-- Dropdown -->
						<div class="header-notifications-dropdown">

							<div class="header-notifications-headline">
								<h4>Set Language</h4>
							</div>

							<div class="header-notifications-content">
								<div class="header-notifications-scroll" data-simplebar>
									<ul>
										<!-- Notification -->
										<li class="notifications-not-read">
											<a href="en.php">
												<span class="notification-icon"><i class="icon-feather-flag"></i></span>
												<span class="notification-text">
													<strong>English</strong></span>
												</span>
											</a>
										</li>
											<!-- Notification -->
											<li class="notifications-not-read">
											<a href="ru.php">
												<span class="notification-icon"><i class="icon-feather-flag"></i></span>
												<span class="notification-text">
													<strong>Russian</strong></span>
												</span>
											</a>
										</li>

									</ul>
								</div>
							</div>

						</div>

					</div>
					
					<!-- Notifications -->
					<div class="header-notifications">

						<!-- Trigger -->
						<div class="header-notifications-trigger">
							<a href="#"><i class="icon-feather-unlock"></i></a>
						</div>

						<!-- Dropdown -->
						<div class="header-notifications-dropdown">

							<div class="header-notifications-content">
								<div class="header-notifications-scroll" data-simplebar>
									<ul>
										<!-- Notification -->
										<li class="notifications-not-read">
											<a href="login.php">
												<span class="notification-icon"><i class="icon-feather-user-check	"></i></span>
												<span class="notification-text">
													<strong>Login</strong></span>
												</span>
											</a>
										</li>
										<!-- Notification -->
										<li class="notifications-not-read">
											<a href="register.php">
												<span class="notification-icon"><i class="icon-feather-user-plus"></i></span>
												<span class="notification-text">
													<strong>Qeydiyyatdan keç</strong></span>
												</span>
											</a>
										</li>

									</ul>
								</div>
							</div>

						</div>

					</div>


				<!-- Mobile Navigation Button -->
				<span class="mmenu-trigger">
					<button class="hamburger hamburger--collapse" type="button">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</span>

			</div>
			<!-- Right Side Content / End -->

		</div>
	</div>
	<!-- Header / End -->

</header>
<div class="clearfix"></div>
<!-- Header Container / End -->