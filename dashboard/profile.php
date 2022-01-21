<?php 
require_once "../system/userTypeController.php";

if(isset($_SESSION['id'])){
	require_once 'dash_header.php';
}else{
	require_once 'designHeader.php';
}

if(isset($_GET['id'])){
	$id = htmlspecialchars(trim($_GET['id']));
	$freelancerQuery = $pdo->prepare("SELECT * FROM users WHERE id=:uid AND user_type=1");
	$freelancerQuery->execute([':uid' => $id]);
	$freelancer = $freelancerQuery->fetch(PDO::FETCH_ASSOC);

	if(!$freelancer){
		header("Location:../index.php");
	}
	else{
		$countOfJobsQuery = $pdo->prepare("SELECT COUNT(*) as count_jobs FROM jobs WHERE verification_status=1 AND user_id=:id");
		$countOfJobsQuery->execute([':id' => $id]);
		$countOfJobs = $countOfJobsQuery->fetch(PDO::FETCH_ASSOC);

		$countOfOperationsQuery = $pdo->prepare("SELECT COUNT(*) as count_operations FROM orders WHERE verification_status=1 AND completed_status=1 AND freelancer_id=:uid");
		$countOfOperationsQuery->execute([':uid' => $id]);
		$countOfOperations = $countOfOperationsQuery->fetch(PDO::FETCH_ASSOC);

		$reviewsQuery = $pdo->prepare("SELECT reviews.review_id, reviews.review_rating, reviews.review_text,
		U2.first_name AS emp_first, U2.last_name AS emp_last
		FROM reviews 
		INNER JOIN users as U2 ON reviews.employer_id = U2.id
		WHERE freelancer_id=:uid
		ORDER BY review_id DESC
		LIMIT 5");
		$reviewsQuery->execute([':uid' => $id]);

#pagination	
		if (isset($_GET['page'])) {
			$page = htmlspecialchars(trim($_GET['page']));
		} else {
			$page = 1;
		}

		$url = "profile.php?";
		if (isset($_GET['id'])) {
			$userId = htmlspecialchars(trim($_GET['id']));
			$url = "profile.php?id={$userId}&";
		}
		//check for id-s(if freelancer_id is really freelancer and employer_id is really employer) in review adding process
		
		$jobCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE user_id=:fid AND verification_status=1");
		$jobCountQuery->execute([':fid' => $id]);
		$jobCount = $jobCountQuery->fetch(PDO::FETCH_ASSOC);

		$no_of_records_per_page = 5;
		$offset = ($page-1) * $no_of_records_per_page; 
		$total_pages = ceil($jobCount['count'] / $no_of_records_per_page);

		$adjacents = 2;
		$prev = $page - 1;
		$next = $page + 1;
		$lpm = $total_pages - 1;



		$pagination = "";


		if($total_pages > 1){

			if($page > 1){
				$pagination.="<li class=\"pagination-arrow\"><a href=\"{$url}page={$prev}\" class=\"ripple-effect\"><i class=\"icon-material-outline-keyboard-arrow-left\"></i></a></li>";
			
			}

			if ($total_pages < 7 + ($adjacents * 2)){   
				for ($counter = 1; $counter <= $total_pages; $counter++){
					if ($counter == $page){
						$pagination.="<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
					}
					else{
						$pagination.= "<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>";    
					}
								
				}
			}
			elseif($total_pages > 5 + ($adjacents * 2)){
				if($page < 1 + ($adjacents * 2)){
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
						if ($counter == $page){
							$pagination.="<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
						}
						else{
							$pagination.= "<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
						}                     
					}
					$pagination.= "...";
					$pagination.="<li><a href=\"{$url}page={$lpm}\" class=\"ripple-effect\">{$lpm}</a></li>";
					$pagination.="<li><a href=\"{$url}page={$total_pages}\" class=\"ripple-effect\">{$total_pages}</a></li>";

				}

				elseif($total_pages - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
					$pagination.="<li><a href=\"{$url}page=1\" class=\"ripple-effect\">1</a></li>";
					$pagination.="<li><a href=\"{$url}page=2\" class=\"ripple-effect\">2</a></li>";
					$pagination.="...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++){
						if ($counter == $page){
							$pagination.="<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
						}
						else{
							$pagination.= "<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
						}                 
					}
					$pagination.= "...";
					$pagination.="<li><a href=\"{$url}page={$lpm}\" class=\"ripple-effect\">{$lpm}</a></li>";
					$pagination.="<li><a href=\"{$url}page={$total_pages}\" class=\"ripple-effect\">{$total_pages}</a></li>";
				}

				else{
					$pagination.="<li><a href=\"{$url}page=1\" class=\"ripple-effect\">1</a></li>";
					$pagination.="<li><a href=\"{$url}page=2\" class=\"ripple-effect\">2</a></li>";
					$pagination.="...";
					for ($counter = $total_pages - (2 + ($adjacents * 2)); $counter <= $total_pages; $counter++){
						if ($counter == $page){
							$pagination.="<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
						}
						else{
							$pagination.= "<li><a href=\"{$url}page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
						}                 
					}
				}
			}

		}
		if($page < $total_pages - 1){
			$pagination.="<li class=\"pagination-arrow\"><a href=\"{$url}page={$next}\" class=\"ripple-effect\"><i class=\"icon-material-outline-keyboard-arrow-right\"></i></a></li>";
		}
#endpagination


		$jobsQuery = $pdo->prepare("SELECT 	jobs.job_id, jobs.job_price, jobs.job_title, jobs.job_description, jobs.verification_status,
											categories.category_name
									FROM jobs
									INNER JOIN categories on jobs.category_id=categories.category_id
									WHERE jobs.user_id=:fid AND jobs.verification_status=1
									ORDER BY job_id DESC
									LIMIT {$offset}, {$no_of_records_per_page}");
		$jobsQuery->execute([':fid' => $id]);

	}

}
else{
	header("Location:../index.php");
}


?>


<!-- Titlebar
================================================== -->
<div class="single-page-header freelancer-header" data-background-image="../assets/images/single-freelancer.jpg">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="single-page-header-inner">
					<div class="left-side">
						<div class="header-image freelancer-avatar"><img src="../assets/images/profile_pictures/<?php echo $freelancer['avatar']?>" alt=""></div>
						<div class="header-details">
							<h3><?php echo $freelancer['first_name'].' '.$freelancer['last_name'];?></h3>
							<ul>
								<li><div class="star-rating" data-rating="<?php echo $freelancer['rating']?>"></div></li>
								<li><?php echo $freelancer['location']?></li>
							</ul>
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
			
			<!-- Boxed List -->
			<div class="boxed-list margin-bottom-60">
				<div class="boxed-list-headline">
					<h3><i class="icon-material-outline-business-center"></i> Frilanserə aid xidmətlər</h3>
				</div>
				<ul class="boxed-list-ul">
					<?php while($job = $jobsQuery->fetch(PDO::FETCH_ASSOC)){ ?>
						<li style="border-bottom: 2px black solid;">
							<div class="boxed-list-item">
								<!-- Content -->
								<div class="item-content">
									<a href="view_job.php?id=<?php echo $job['job_id'];?>"><h4><?php echo $job['job_title'];?><span><?php echo $job['category_name'];?></span></h4></a>
									<div class="item-description">
										<p><?php echo $job['job_description'];?> </p>
									</div>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>

				<!-- Pagination -->
				<div class="clearfix"></div>
				<div class="pagination-container margin-top-40 margin-bottom-10">
					<nav class="pagination">
						<ul>
							<?php echo $pagination; ?>
						</ul>
					</nav>
				</div>
				<div class="clearfix"></div>
				<!-- Pagination / End -->

			</div>
			<!-- Boxed List / End -->

			<!-- Boxed List -->
			<div class="boxed-list margin-bottom-60">

				<div class="boxed-list-headline">
					<h3><i class="icon-material-outline-thumb-up"></i> Frilanserə verilən son rəylər</h3>
				</div>
				<ul class="boxed-list-ul">
					<?php while($review = $reviewsQuery->fetch(PDO::FETCH_ASSOC)){?>
						<li>
							<div class="boxed-list-item">
								<!-- Content -->
								<div class="item-content">
									<h4><strong><?php echo $review['emp_first'].' '.$review['emp_last'];?></strong> tərəfindən</h4>
									<div class="item-details margin-top-10">
										<div class="star-rating" data-rating="<?php echo $review['review_rating'];?>"></div>
									</div>
									<div class="item-description">
										<p><?php echo $review['review_text'];?></p>
									</div>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>

			</div>
			<!-- Boxed List / End -->
		</div>
		

		<!-- Sidebar -->
		<div class="col-xl-4 col-lg-4">
			<div class="sidebar-container">
				
				<!-- Profile Overview -->
				<div class="profile-overview">
					<div class="overview-item"><strong><?php echo $countOfJobs['count_jobs'];?></strong><span>Xidmət</span></div>
					<div class="overview-item"><strong><?php echo $countOfOperations['count_operations'];?></strong><span>Uğurlu əməliyyat</span></div>
				</div>




				<!-- Sidebar Widget -->
				<div class="sidebar-widget">
					<h3>Linki kopyala</h3>

					<!-- Copy URL -->
					<div class="copy-url">
						<input id="copy-url" type="text" value="" class="with-border">
						<button class="copy-url-button ripple-effect" data-clipboard-target="#copy-url" title="Kopyala" data-tippy-placement="top"><i class="icon-material-outline-file-copy"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Spacer -->
<div class="margin-top-15"></div>
<!-- Spacer / End-->

<?php require_once 'dash_footer.php'; ?>