<?php 
ob_start();
@session_start();
require_once "../system/dbController.php";
require_once "../system/userTypeController.php";
    
  
    if(!$_SESSION['id']){
        header('location:../login.php');
    }
$userProfilePictureQuery = $pdo->prepare("SELECT user_type,avatar,first_name,last_name FROM users WHERE id=:uid");
$userProfilePictureQuery->execute([':uid'=>$_SESSION['id']]);
$userProfilePicture = $userProfilePictureQuery->fetch(PDO::FETCH_ASSOC);
//  Count of Categories 

$countOfCategoriesQuery = $pdo->prepare("SELECT COUNT(*) as count FROM categories");
$countOfCategoriesQuery->execute();
$countOfCategories = $countOfCategoriesQuery->fetch(PDO::FETCH_ASSOC);

// Count of Categories end

//Count of Freelancers

$countOfFreelancersQuery = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE user_type=1");
$countOfFreelancersQuery->execute();
$countOfFreelancers = $countOfFreelancersQuery->fetch(PDO::FETCH_ASSOC);

//Count of Freelancers end

//Count of Employers

$countOfEmployersQuery = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE user_type=2");
$countOfEmployersQuery->execute();
$countOfEmployers = $countOfEmployersQuery->fetch(PDO::FETCH_ASSOC);

//Count of Employers end


//Count of all Jobs
$countOfJobsQuery = $pdo->prepare("SELECT COUNT(*) as count FROM jobs");
$countOfJobsQuery->execute();
$countOfJobs = $countOfJobsQuery->fetch(PDO::FETCH_ASSOC);

//Count of all jobs end

//Count of all reviews
$countOfReviewsQuery = $pdo->prepare("SELECT COUNT(*) as count FROM reviews");
$countOfReviewsQuery->execute();
$countOfReviews = $countOfReviewsQuery->fetch(PDO::FETCH_ASSOC);
//Count of all reviews end

//count of freelancer jobs

if(isfreelancer()){
	$fid = $_SESSION['id'];
	$countOfFreelancerJobsQuery = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE user_id=:fid");
	$countOfFreelancerJobsQuery->execute(['fid' => $fid]);
	$countOfFreelancerJobs = $countOfFreelancerJobsQuery->fetch(PDO::FETCH_ASSOC);
}


//count of freelancer jobs end

?>
<!doctype html>
<html lang="en">
<head>

<!-- Basic Page Needs
================================================== -->
<title> Dashboard </title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- CSS
================================================== -->
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/colors/blue.css">
<style>
	.image-grid-job {
		width:100%;
		display:flex;
		flex-wrap:wrap;
		align-items:center;
	}



	.jobPhoto{
		flex:1;
		margin-right:5px;
	}

</style>

</head>
<body class="gray">

<!-- Wrapper -->
<div id="wrapper">

<!-- Header Container
================================================== -->
<header id="header-container" class="fullwidth dashboard-header not-sticky">

	<!-- Header -->
	<div id="header">
		<div class="container">
			
			<!-- Left Side Content -->
			<div class="left-side">
				
				<!-- Logo -->
				<div id="logo">
					<a href="index.php"><img src="../assets/images/logo.png" alt=""></a>
				</div>

				<!-- Main Navigation -->
				<nav id="navigation">
					<ul id="responsive">
						<?php if(isadmin()){ ?>
							<li><a href="index.php">Panel Ana Səhifə</a></li>
							<li><a href="operations_admin.php">Əməliyyatlar</a></li>
							<li><a href="#">Admin</a>
								<ul class="dropdown-nav">
									<li><a href="#">Kateqoriyalar</a>
										<ul class="dropdown-nav">
											<li><a href="categories.php">Kateqoriya siyahısı</a></li>
											<li><a href="add_category.php">Yeni kateqoriya</a></li>
										</ul>
									</li>
									<li><a href="#">Frilanserlər</a>
										<ul class="dropdown-nav">
											<li><a href="freelancers_admin.php">Frilanser siyahısı</a></li>
										</ul>
									</li>	
									<li><a href="#">İşəgötürənlər</a>
										<ul class="dropdown-nav">
											<li><a href="employers_admin.php">İşəgötürənlərin siyahısı</a></li>
										</ul>
									</li>
									<li><a href="#">İşlər</a>
										<ul class="dropdown-nav">
											<li><a href="jobs_admin.php">İşlərin siyahısı</a></li>
										</ul>
									</li>
									<li><a href="#">Dəyərləndirmələr</a>
										<ul class="dropdown-nav">
											<li><a href="reviews_admin.php">Dəyərləndirmələrin siyahısı</a></li>
										</ul>
									</li>			
									<li><a href="admin_settings.php">Ümumi tənzimləmələr</a></li>
									<li><a href="balance_admin.php">Balans əməliyyatları</a></li>
								</ul>
							</li>
						<?php } ?>

						<?php if(isfreelancer()){ ?>
							<li><a href="index.php">Panel Ana Səhifə</a></li>
							<li><a href="operations_freelancer.php">Əməliyyatlar</a></li>
							<li><a href="#">Frilanser</a>
								<ul class="dropdown-nav">
									<li><a href="#">Sizin işləriniz</a>
										<ul class="dropdown-nav">
											<li><a href="jobs_freelancer.php">Sizin işlərinizin siyahısı</a></li>
											<li><a href="add_job.php">Yeni iş</a></li>
										</ul>
									</li>
									<li><a href="jobs_main.php">Bütün işlər</a></li>																
									<li><a href="reviews.php">Dəyərləndirmələr</a></li>
									<li><a href="balance_freelancer.php">Balans</a></li>						
								</ul>
							</li>
						<?php } ?>

						<?php if(isemployer()){ ?>
							<li><a href="index.php">Panel Ana Səhifə</a></li>
							<li><a href="operations_employer.php">Əməliyyatlar</a></li>
							<li><a href="reviews.php">Dəyərləndirmələr</a></li>
							<li><a href="jobs_main.php">Bütün işlər</a></li>																
						<?php } ?>
						<li><a href="#">Hesab</a>
							<ul class="dropdown-nav">
								<li><a href="settings.php"><i class="icon-material-outline-settings"></i> Tənzimləmələr</a></li>
								<li><a href="logout.php"><i class="icon-material-outline-power-settings-new"></i> Çıxış edin</a></li>							
							</ul>
						</li>	
					</ul>
				</nav>
				<div class="clearfix"></div>
				<!-- Main Navigation / End -->
				
			</div>
			<!-- Left Side Content / End -->


			<!-- Right Side Content / End -->
			<div class="right-side">
				<!-- User Menu -->
				<div class="header-widget">

					<!-- Messages -->
					<div class="header-notifications user-menu">
						<div class="header-notifications-trigger">
							<a href="#"><div class="user-avatar status-online"><img src="../assets/images/profile_pictures/<?=$userProfilePicture['avatar'];?>" alt=""></div></a>
						</div>

						<!-- Dropdown -->
						<div class="header-notifications-dropdown">

							<!-- User Status -->
							<div class="user-status">

								<!-- User Name / Avatar -->
								<div class="user-details">
									<div class="user-avatar status-online"><img src="../assets/images/profile_pictures/<?=$userProfilePicture['avatar'];?>" alt=""></div>
									<div class="user-name">
										<?=$userProfilePicture['first_name'];?> <?=$userProfilePicture['last_name']?>
										<span>
											<?php
												if($userProfilePicture['user_type'] == 1){
													echo "Frilanser";
												}else if($userProfilePicture['user_type'] == 2){
													echo "İşəgötürən";
												}else{
													echo "Admin";
												}
											?>

										</span>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<!-- User Menu / End -->

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


