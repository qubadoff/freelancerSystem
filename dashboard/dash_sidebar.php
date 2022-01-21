	<?php
    
    if(!$_SESSION['id']){
        header('location:../login.php');
    }
    
    ?>
    <!-- Dashboard Container -->
    <div class="dashboard-container">

    <!-- Dashboard Sidebar
	================================================== -->
	<div class="dashboard-sidebar">
		<div class="dashboard-sidebar-inner" data-simplebar>
			<div class="dashboard-nav-container">

				<!-- Responsive Navigation Trigger -->
				<a href="#" class="dashboard-responsive-nav-trigger">
					<span class="hamburger hamburger--collapse" >
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</span>
					<span class="trigger-title">Menyu</span>
				</a>
				
				<!-- Navigation -->
				<div class="dashboard-nav">
					<div class="dashboard-nav-inner">
						<?php if(isadmin()){ ?>
                            <ul data-submenu-title="Admin">
                                <li class=""><a href="index.php"><i class="icon-material-outline-dashboard"></i> Panel Ana Səhifə</a></li>
                                <li><a href="operations_admin.php"><i class="icon-line-awesome-cube"></i> Əməliyyatlar </a></li>
                                <li><a href="#"><i class="icon-line-awesome-navicon"></i> Kateqoriyalar</a>
                                    <ul>
                                        <li><a href="categories.php">Kateqoriya siyahısı<span class="nav-tag"><?php echo $countOfCategories['count']; ?></span></a></li>
                                        <li><a href="add_category.php">Yeni kateqoriya</a></li>
                                    </ul>	
                                </li>
                                <li><a href="#"><i class="icon-feather-user"></i> Frilanserlər </a>
                                    <ul>
                                        <li><a href="freelancers_admin.php">Frilanser siyahısı<span class="nav-tag"><?php echo $countOfFreelancers['count']; ?></span></a></li>
                                    </ul>
                                </li>
                                <li><a href="#"><i class="icon-feather-user"></i> İşəgötürənlər </a>
                                    <ul>
                                        <li><a href="employers_admin.php">İşəgötürənlərin siyahısı<span class="nav-tag"><?php echo $countOfEmployers['count']; ?></span></a></li>
                                    </ul>
                                </li>
                                <li><a href="#"><i class="icon-material-outline-business-center"></i> İşlər </a>
                                    <ul>
                                        <li><a href="jobs_admin.php">İşlərin siyahısı<span class="nav-tag"><?php echo $countOfJobs['count']; ?></span></a></li>
                                    </ul>
                                </li>
                                <li><a href="#"><i class="icon-material-outline-rate-review"></i> Dəyərləndirmələr </a>
                                    <ul>
                                        <li><a href="reviews_admin.php">Dəyərləndirmələrin siyahısı<span class="nav-tag"><?php echo $countOfReviews['count']; ?></span></a></li>
                                    </ul>
                                </li>
                                <li><a href="admin_settings.php"><i class="icon-material-outline-settings"></i> Ümumi tənzimləmələr</a></li>
                                <li><a href="balance_admin.php"><i class="icon-material-outline-account-balance-wallet"></i> Balans əməliyyatları</a></li>
                            </ul>
						<?php } ?>
						<?php if(isfreelancer()){?>
                            <ul data-submenu-title="Frilanser">
                                <li class=""><a href="index.php"><i class="icon-material-outline-dashboard"></i> Panel Ana Səhifə</a></li>
                                <li><a href="operations_freelancer.php"><i class="icon-line-awesome-cube"></i> Əməliyyatlar </a></li>
                                <li><a href="#"><i class="icon-material-outline-business-center"></i> Sizin işləriniz</a>
                                    <ul>
                                        <li><a href="jobs_freelancer.php">Sizin işlərinizin siyahısı<span class="nav-tag"><?php echo $countOfFreelancerJobs['count']; ?></span></a></li>
                                        <li><a href="add_job.php">Yeni iş</a></li>
                                    </ul>	
                                </li>
                                <li><a href="jobs_main.php"><i class="icon-material-outline-business-center"></i> Bütün işlər</a></li>
                                <li><a href="reviews.php"><i class="icon-material-outline-rate-review"></i> Dəyərləndirmələr</a></li>
                                <li><a href="balance_freelancer.php"><i class="icon-material-outline-account-balance-wallet"></i> Balans</a></li>
                            </ul>
						<?php } ?>
						<?php if(isemployer()) { ?>
                            <ul data-submenu-title="İşəgötürən">
                                <li class=""><a href="index.php"><i class="icon-material-outline-dashboard"></i> Panel Ana Səhifə</a></li>
                                <li><a href="operations_employer.php"><i class="icon-line-awesome-cube"></i> Əməliyyatlar </a></li>
                                <li><a href="reviews.php"><i class="icon-material-outline-rate-review"></i> Dəyərləndirmələr</a></li>
                                <li><a href="jobs_main.php"><i class="icon-material-outline-business-center"></i> Bütün işlər</a></li>
                                <li><a href="freelancer.php"><i class="icon-material-outline-star-border"></i> Find Freelancer</a></li>
                            </ul>
						<?php } ?>
						<ul data-submenu-title="Hesab">
							<li><a href="settings.php"><i class="icon-material-outline-settings"></i> Tənzimləmələr</a></li>
							<li><a href="logout.php"><i class="icon-material-outline-power-settings-new"></i> Çıxış edin</a></li>
						</ul>
					</div>
				</div>
				<!-- Navigation / End -->

			</div>
		</div>
	</div>
	<!-- Dashboard Sidebar / End -->
