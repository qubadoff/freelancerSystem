<?php
include "header.php";

$getJobsCountQuery = $pdo->prepare("SELECT COUNT(*) as job_count FROM jobs WHERE verification_status=1");
$getJobsCountQuery->execute();
$getJobsCount = $getJobsCountQuery->fetch(PDO::FETCH_ASSOC);

$getOrdersCountQuery = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE verification_status=1");
$getOrdersCountQuery->execute();
$getOrdersCount = $getOrdersCountQuery->fetch(PDO::FETCH_ASSOC);

$getFreelancerCountQuery = $pdo->prepare("SELECT COUNT(*) as freelancer_count FROM users WHERE user_type=1");
$getFreelancerCountQuery->execute();
$getFreelancerCount = $getFreelancerCountQuery->fetch(PDO::FETCH_ASSOC);

$regionsQuery = $pdo->prepare("SELECT * FROM regions");
$regionsQuery->execute();
$regions = $regionsQuery->fetchAll(PDO::FETCH_ASSOC);

$selectHtml = "<select class=\"selectpicker\" name=\"location[]\" multiple data-size=\"7\" title=\"Bütün lokasiyalar\" data-selected-text-format=\"count\">";
foreach($regions as $region){
	$selectHtml .= "<option value={$region['region_id']}> {$region['region_title']}</option>";
}

$selectHtml.="</select>";

$popularCategoriesQuery = $pdo->prepare("SELECT COUNT(jobs.job_id) as count_jobs, categories.category_name,categories.category_id
										 FROM jobs
										INNER JOIN categories ON jobs.category_id=categories.category_id
										WHERE jobs.verification_status=1
										GROUP BY categories.category_name
										ORDER BY count_jobs DESC");
$popularCategoriesQuery->execute();

$popularFreelancersQuery = $pdo->prepare("SELECT orders.freelancer_id,  COUNT(order_id) as count, users.first_name, users.last_name,users.avatar,users.rating
											FROM orders
											INNER JOIN users on orders.freelancer_id=users.id
											WHERE verification_status =1 AND completed_status=1
											GROUP BY freelancer_id
											ORDER BY COUNT(order_id) DESC
											LIMIT 5
										");
$popularFreelancersQuery->execute();

?>
<!-- Intro Banner
================================================== -->
<!-- add class "disable-gradient" to enable consistent background overlay -->
<div class="intro-banner" data-background-image="assets/images/home-background.jpg">
	<div class="container">

		<!-- Intro Headline -->
		<div class="row">
			<div class="col-md-12">
				<div class="banner-headline">
					<h3>
						<strong>Frilanserlərin xidmətlərini axtarın və sifariş edin.</strong>
						<br>
						<span>Minlərlə işəgötürən <strong class="color">Hireo</strong> vasitəsilə frilanser tapıb.</span>
					</h3>
				</div>
			</div>
		</div>

		<!-- Search Bar -->
		<div class="row">
			<div class="col-md-12">
				<form action="dashboard/jobs_main.php" method="GET">
					<div class="intro-banner-search-form margin-top-95">

							<!-- Search Field -->
							<div class="intro-search-field with-autocomplete">
								<label class="field-title ripple-effect">Harada?</label>
								<div class="input-with-icon" >
									<?= $selectHtml ?>
								</div>
							</div>

							<!-- Search Field -->
							<div class="intro-search-field">
								<label for ="intro-keywords" class="field-title ripple-effect">Açar söz</label>
								<input id="intro-keywords" type="text" name="keyword" placeholder="Açar söz">
							</div>

							<!-- Button -->
							<div class="intro-search-button">
								<button class="button ripple-effect">Axtar</button>
							</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Stats -->
		<div class="row">
			<div class="col-md-12">
				<ul class="intro-stats margin-top-45 hide-under-992px">
					<li>
						<strong class="counter"><?php echo $getJobsCount['job_count'];?></strong>
						<span>Xidmət</span>
					</li>
					<li>
						<strong class="counter"><?php echo $getOrdersCount['order_count'];?></strong>
						<span>Əməliyyat</span>
					</li>
					<li>
						<strong class="counter"><?php echo $getFreelancerCount['freelancer_count'];?></strong>
						<span>Frilanser</span>
					</li>
				</ul>
			</div>
		</div>

	</div>
</div>


<!-- Content
================================================== -->
<!-- Category Boxes -->
<div class="section margin-top-65">
	<div class="container">
		<div class="row">
			<div class="col-xl-12">

				<div class="section-headline centered margin-bottom-15">
					<h3>Populyar kateqoriyalar</h3>
				</div>

				<!-- Category Boxes Container -->
				<div class="categories-container">

					<?php while($popularCategory = $popularCategoriesQuery->fetch(PDO::FETCH_ASSOC)){?>
						<a href="dashboard/jobs_main.php?keyword=&category[]=<?=$popularCategory['category_id'];?>" class="category-box">
							<div class="category-box-content">
								<h3><?= $popularCategory['category_name'];?></h3>
							</div>
						</a>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>
</div>
<!-- Category Boxes / End -->


<!-- Features Jobs -->
<div class="section gray margin-top-45 padding-top-65 padding-bottom-75">
	<div class="container">
		<div class="row">
			<div class="col-xl-12">

				<!-- Section Headline -->
				<div class="section-headline margin-top-0 margin-bottom-35">
					<h3>Ən çox sifariş tamamlayan frilanserlər</h3>
				</div>

				<!-- Jobs Container -->
				<div class="listings-container compact-list-layout margin-top-35">
					<?php while($popularFreelancer = $popularFreelancersQuery->fetch(PDO::FETCH_ASSOC)){?>
					<!-- Job Listing -->
						<a href="dashboard/profile.php?id=<?php echo $popularFreelancer['freelancer_id'];?>" class="job-listing with-apply-button">

							<!-- Job Listing Details -->
							<div class="job-listing-details">

								<!-- Logo -->
								<div class="job-listing-company-logo">
									<img src="assets/images/profile_pictures/<?php echo $popularFreelancer['avatar'];?>" alt="">
								</div>

								<!-- Details -->
								<div class="job-listing-description">
									<h3 class="job-listing-title"><?php echo $popularFreelancer['first_name'].' '.$popularFreelancer['last_name'];?></h3>

									<!-- Job Listing Footer -->
									<div class="job-listing-footer">
										<ul>
											<div class="star-rating" data-rating="<?php echo $popularFreelancer['rating'];?>"></div>
										</ul>
									</div>
								</div>

								<!-- Apply Button -->
								<p style="color:black;"><i class="icon-material-outline-business-center"></i>  <?php echo $popularFreelancer['count'];?> tamamlanmış iş</p>
							</div>
						</a>
					<?php } ?>
				</div>
				<!-- Jobs Container / End -->

			</div>
		</div>
	</div>
</div>
<!-- Featured Jobs / End -->
<?php include "footer.php"; ?>
