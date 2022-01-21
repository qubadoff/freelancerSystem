<?php
require_once "../system/userTypeController.php";
if(1){
	if(isset($_SESSION['id'])){
		require_once 'dash_header.php';
	}else{
		require_once 'designHeader.php';
	}
	if(isset($_GET['id'])){
		$id = htmlspecialchars(trim($_GET['id']));
		$sql = 'SELECT 
		jobs.job_id, jobs.user_id, jobs.job_title, jobs.job_description,jobs.verification_status,jobs.job_price,
		users.first_name, users.last_name, users.number, users.rating, users.avatar,
		categories.category_name
		FROM jobs
		INNER JOIN users ON jobs.user_id = users.id
		INNER JOIN categories ON jobs.category_id = categories.category_id
		WHERE job_id=:id';

		$verificationQuery = $pdo->prepare("SELECT user_id FROM jobs WHERE job_id=:id");
		$verificationQuery->execute([':id' =>$id]);
		$verification = $verificationQuery->fetch(PDO::FETCH_ASSOC);

		
		$balanceQuery = $pdo->prepare("SELECT balance FROM users WHERE id=:fid");
		$balanceQuery->execute([':fid' => $verification['user_id']]);
		$balance = $balanceQuery->fetch(PDO::FETCH_ASSOC);

		$minimumBalanceQuery = $pdo->prepare("SELECT minimum_balance FROM admin_settings WHERE setting_id=1");
		$minimumBalanceQuery->execute();
		$minimumBalance = $minimumBalanceQuery->fetch(PDO::FETCH_ASSOC);

		if(isset($_SESSION['id'])){
			if(isemployer() || (isfreelancer() && $_SESSION['id'] != $verification['user_id'] )){
				$sql.=' AND jobs.verification_status=1';
				if($balance['balance'] < $minimumBalance['minimum_balance']){
					header("Location:jobs_main.php");
				}
				
			}
		}

		$jobQuery = $pdo->prepare($sql);
		$jobQuery->execute([':id' => $id]);
		$job = $jobQuery->fetch(PDO::FETCH_ASSOC);

		$urlToGoBack = 'jobs_freelancer.php';

		if(isset($_GET['back_to'])){
			if($_GET['back_to'] == 'main'){
				$urlToGoBack = 'jobs_main.php';
			}
		}
		if(!$job){
			header("Location:index.php");
		}
		
		
		$regionsQuery = $pdo->prepare("SELECT jobs.job_title, jobs.user_id, regions.region_title
										FROM jobs
										LEFT JOIN jobs_regions on jobs.job_id = jobs_regions.job_id
										LEFT JOIN regions on jobs_regions.region_id = regions.region_id
										WHERE jobs_regions.job_id=:id");
		$regionsQuery->execute([':id' => $id]);
		
		$photosQuery = $pdo->prepare("SELECT * FROM jobs_pictures WHERE job_id=:jid");
		$photosQuery->execute([':jid' => $id]);
	}else{
		header("Location:jobs_freelancer.php");
	}
?>


<!-- Titlebar
================================================== -->
<div class="single-page-header" data-background-image="../assets/images/single-job.jpg">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="single-page-header-inner">
					<div class="left-side">
						<div class="header-details">
							<h3><?php echo $job['job_title']; ?></h3>
							<h5><?php echo $job['category_name']; ?></h5>
							<br>
							<ul>

								<?php 
								if(isset($_SESSION['id'])){
									if(isfreelancer() && $_SESSION['id'] == $verification['user_id'] ){?>
									<li>Status: <b><?php echo $job['verification_status'] ? 'Aktiv':'Deaktiv';?></b></li>
								<?php }} ?>
							</ul>
						</div>
					</div>
					<div class="right-side">
						<div class="salary-box">
							<div class="salary-type">İşin qiyməti</div>
							<div class="salary-amount"><?php echo $job['job_price'];?> AZN</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Page Content
================================================== -->
<div class="container">
	<div class="row">
		
		<!-- Content -->
		<div class="col-xl-8 col-lg-8 content-right-offset">
			<?php 
			if(isfreelancer()){
				if(($balance['balance'] < $minimumBalance['minimum_balance']) && $_SESSION['id'] == $verification['user_id']){ ?>
					<div class="notification error">
						<p><strong>Balansınız(<?= $balance['balance'].'AZN';?>) minimal balansdan(<?= $minimumBalance['minimum_balance'].'AZN';?>) az olduğu üçün bu işiniz axtarışda deaktivdir.</strong></p>
					</div>
			<?php }} ?>
			<div class="single-page-section">
				<h3 class="margin-bottom-25">İşin təsviri</h3>
				<?php echo $job['job_description'];?>
			</div>
			<div class="clearfix"></div>
			

			<div class="single-page-section">
				<h3 class="margin-bottom-25">İşə aid şəkillər</h3>
				<div class="image-grid-job">
					<?php while($photo = $photosQuery->fetch(PDO::FETCH_ASSOC)){?>
						<img class="jobPhoto" width="400" height="500" src="../assets/images/job_pictures/<?= $photo['picture_url'];?>" alt="">
					<?php } ?>
				</div>
			</div>
			
			<!-- Freelancers Bidding -->
			<div class="boxed-list margin-bottom-60">
				<div class="boxed-list-headline">
					<h3><i class="icon-material-outline-group"></i> Frilanser</h3>
				</div>
				<ul class="boxed-list-ul">
					<li>
						<div class="bid">
							<div class="bids-avatar">
								<div class="freelancer-avatar">
									<a href="profile.php?id=<?php echo $job['user_id'];?>"><img src="../assets/images/profile_pictures/<?=$job['avatar'];?>" alt=""></a>
								</div>
							</div>
	
							<div class="bids-content">
								<div class="freelancer-name">
									<h4><a href="profile.php?id=<?php echo $job['user_id'];?>"><?php echo $job['first_name'].' '.$job['last_name'];?></a></h4>
									<span><?php echo $job['number']?></span>
									<div class="star-rating" data-rating="<?php echo $job['rating']?>"></div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>


		</div>
		

		<!-- Sidebar -->
		<div class="col-xl-4 col-lg-4">
			<div class="sidebar-container">
				<?php if(isemployer()){?>						
					<a href="../system/operationEmployerController.php?operation=new&job_id=<?php echo $job['job_id'];?>" class="button"><i class="icon-feather-arrow-left"></i>Sifariş et</a><br>
				<?php } ?>
				<a href="<?php echo $urlToGoBack; ?>" class="button"><i class="icon-feather-arrow-left"></i>İşlər siyahısına geri dön</a>

				<div class="single-page-section">
					<h3 class="margin-top-30 margin-bottom-10">Lokasiya</h3>
					<div class="numbered">
						<ol>
							<?php while($region = $regionsQuery->fetch(PDO::FETCH_ASSOC)){?>
								<li><?php echo $region['region_title']; ?></li>
							<?php } ?>
						</ol>
					</div>
				</div>						


				<!-- Sidebar Widget -->
				<div class="sidebar-widget">
					<h3>Linki kopyala</h3>

					<!-- Copy URL -->
					<div class="copy-url">
						<input id="copy-url" type="text" value="" class="with-border">
						<button class="copy-url-button ripple-effect" data-clipboard-target="#copy-url" title="Copy to Clipboard" data-tippy-placement="top"><i class="icon-material-outline-file-copy"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>





<!-- Scripts
================================================== -->
<script src="../assets/js/jquery-3.4.1.min.js"></script>
<script src="../assets/js/jquery-migrate-3.1.0.min.html"></script>
<script src="../assets/js/mmenu.min.js"></script>
<script src="../assets/js/tippy.all.min.js"></script>
<script src="../assets/js/simplebar.min.js"></script>
<script src="../assets/js/bootstrap-slider.min.js"></script>
<script src="../assets/js/bootstrap-select.min.js"></script>
<script src="../assets/js/snackbar.js"></script>
<script src="../assets/js/clipboard.min.js"></script>
<script src="../assets/js/counterup.min.js"></script>
<script src="../assets/js/magnific-popup.min.js"></script>
<script src="../assets/js/slick.min.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- Snackbar // documentation: https://www.polonel.com/snackbar/ -->
<script>
// Snackbar for user status switcher
$('#snackbar-user-status label').click(function() { 
	Snackbar.show({
		text: 'Your status has been changed!',
		pos: 'bottom-center',
		showAction: false,
		actionText: "Dismiss",
		duration: 3000,
		textColor: '#fff',
		backgroundColor: '#383838'
	}); 
}); 

// Snackbar for copy to clipboard button
$('.copy-url-button').click(function() { 
	Snackbar.show({
		text: 'Kopyalandı',
	}); 
}); 
</script>

<!-- Google API & Maps -->
<!-- Geting an API Key: https://developers.google.com/maps/documentation/javascript/get-api-key -->
<script src="../assets/https://maps.googleapis.com/maps/api/js?key=AIzaSyAaoOT9ioUE4SA8h-anaFyU4K63a7H-7bc&amp;libraries=places"></script>
<script src="../assets/js/infobox.min.js"></script>
<script src="../assets/js/markerclusterer.js"></script>
<script src="../assets/js/maps.js"></script>

</body>

<!-- Mirrored from www.vasterad.com/themes/hireo/single-job-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 11 Aug 2021 11:36:56 GMT -->
</html>

<?php
}

else{
	header("Location:index.php");
}
?>