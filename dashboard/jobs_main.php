<?php 
require_once "../system/userTypeController.php";
if(1){
	if(isset($_SESSION['id'])){
		require_once 'dash_header.php';
	}else{
		require_once 'designHeader.php';
	}


	$minimumBalanceQuery = $pdo->prepare("SELECT minimum_balance FROM admin_settings WHERE setting_id=1");
	$minimumBalanceQuery->execute();
	$minimumBalance = $minimumBalanceQuery->fetch(PDO::FETCH_ASSOC);

	$maxMinPriceq= $pdo->prepare("SELECT MAX(job_price) as maxPrice, MIN(job_price) as minPrice FROM jobs INNER JOIN users on jobs.user_id=users.id WHERE verification_status=1 AND users.balance >= {$minimumBalance['minimum_balance']}");
	$maxMinPriceq->execute();
	$maxMinPrice = $maxMinPriceq->fetch(PDO::FETCH_ASSOC);

	$keywordForSearch = '%%';
	$getKeyword ='';
	$searchSql = '';
	$requestCategories = [];
	$requestLocations = [];
	$requestMin = $maxMinPrice['minPrice'];
	$requestMax = $maxMinPrice['maxPrice'];

	$hasKeyword = false;
	$hasCategories = false;
	$hasLocation = false;
	$hasPrice = false;
	if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
		$hasKeyword = true;
		$searchSql.=' AND
		(jobs.job_title LIKE ? OR
		jobs.job_description LIKE ?)';
		$getKeyword = trim(htmlspecialchars($_GET['keyword']));
		$keywordForSearch = "%{$getKeyword}%";
	}

	if(isset($_GET['category']) && !empty($_GET['category'])){
		$hasCategories = true;
		$requestCategories = array_map('htmlspecialchars',$_GET['category']);
		$in  = str_repeat('?,', count($requestCategories) - 1) . '?';
		$searchSql.=" AND jobs.category_id IN ({$in})";
	}

	if(isset($_GET['location']) && !empty($_GET['location'])){
		$hasLocation = true;
		$requestLocations = array_map('htmlspecialchars',$_GET['location']);
	}
	if(isset($_GET['price']) && !empty($_GET['price'])){
		$hasPrice = true;
		$requestPrices = explode(',',htmlspecialchars($_GET['price']));
		$requestMin = $requestPrices[0];
		$requestMax = $requestPrices[1];
		$searchSql.=" AND job_price <= ? AND job_price >= ?";
	}


	$params = [];
	if($hasKeyword){
		$params = array_merge($params,[$keywordForSearch,$keywordForSearch]);
	}
	if($hasCategories){
		$params = array_merge($params,$requestCategories);
	}
	if($hasLocation){

	}
	if($hasPrice){
		$params = array_merge($params,[$requestMax,$requestMin]);
	}

#pagination	
	if (isset($_GET['page'])) {
		$page = htmlspecialchars(trim($_GET['page']));
	} else {
		$page = 1;
	}

	$url = "jobs_main.php?";
	if (isset($_GET['searchJobMain'])){
		$cut = $_SERVER['REQUEST_URI'];
		$url = $cut;
		if(isset($_GET['page'])){
			$url = substr($cut, 0, strrpos( $cut, '&'));
		}
		$url.='&';
	}

	$jobCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM jobs INNER JOIN users on jobs.user_id=users.id WHERE verification_status=1 AND users.balance >= {$minimumBalance['minimum_balance']} {$searchSql}"); 
	$jobCountQuery->execute($params);
	$jobCount = $jobCountQuery->fetch(PDO::FETCH_ASSOC);
	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($jobCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";


	$categoriesQuery = $pdo->prepare("SELECT * FROM categories");
	$categoriesQuery->execute();
	$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

	$categoryIds = array_column($categories,'category_id');

	$regionsQuery = $pdo->prepare("SELECT * FROM regions");
	$regionsQuery->execute();
	$regions = $regionsQuery->fetchAll(PDO::FETCH_ASSOC);

	$regionIds = array_column($regions, 'region_id');
	


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
									categories.category_name, users.first_name, users.last_name
									FROM jobs
									INNER JOIN categories on jobs.category_id=categories.category_id
									INNER JOIN users on jobs.user_id=users.id
									WHERE verification_status=1 AND users.balance >= {$minimumBalance['minimum_balance']}
									{$searchSql}
									ORDER BY job_id DESC
									LIMIT {$offset}, {$no_of_records_per_page}");
	$jobsQuery->execute($params);

?>
<!-- Spacer -->
<div class="margin-top-90"></div>
<!-- Spacer / End-->

<!-- Page Content
================================================== -->
<div class="container">
	<div class="row">
		<div class="col-xl-3 col-lg-4">
			<div class="sidebar-container">
				
				<form action="jobs_main.php" method="GET">
					<!-- Search -->
					<div class="sidebar-widget">
						<h3>Açar söz</h3>
						<div>
							<div>
								<input type="text" placeholder="Açar söz" name="keyword" value="<?php echo $getKeyword;?>">
							</div>
						</div>
					</div>

					<!-- Kateqoriya -->
					<div class="sidebar-widget">
						<h3>Kateqoriya</h3>
						<select class="selectpicker" name="category[]" multiple data-size="7" title="Bütün kateqoriyalar">
							<?php if($hasCategories){?>
								<?php foreach($categories as $category){?>
									<?php if(in_array($category['category_id'], $requestCategories)){?>
										<option value="<?php echo $category['category_id']?>" selected><?php echo $category['category_name']?></option>
									<?php } else{?>
										<option value="<?php echo $category['category_id']?>"><?php echo $category['category_name']?></option>
									<?php } ?>
								<?php } ?>
							<?php } else{?>
								<?php foreach($categories as $category){?>
									<option value="<?php echo $category['category_id']?>"><?php echo $category['category_name']?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>

					<!-- Location -->
					<div class="sidebar-widget">
						<h3>Lokasiya</h3>
						<select class="selectpicker" name="location[]" multiple data-size="7" title="Bütün lokasiyalar">
						<?php if($hasLocation){?>
								<?php foreach($regions as $region){?>
									<?php if(in_array($region['region_id'], $requestLocations)){?>
										<option value="<?php echo $region['region_id']?>" selected><?php echo $region['region_title']?></option>
									<?php } else{?>
										<option value="<?php echo $region['region_id']?>"><?php echo $region['region_title']?></option>
									<?php } ?>
								<?php } ?>
							<?php } else{?>
								<?php foreach($regions as $region){?>
									<option value="<?php echo $region['region_id']?>"><?php echo $region['region_title']?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>

					<!-- Price -->
					<div class="sidebar-widget">
						<h3>Qiymət</h3>
						<div class="margin-top-55"></div>

						<!-- Range Slider -->
						<input class="range-slider" type="text" name="price" data-slider-currency="AZN" data-slider-min="<?php echo $maxMinPrice['minPrice'];?>" data-slider-max="<?php echo $maxMinPrice['maxPrice'];?>" data-slider-step="5" data-slider-value="[<?php echo $requestMin.','.$requestMax; ?>]"/>
					</div>

					<div class="sidebar-widget">
						<input type="submit" value="Axtarış et" name="searchJobMain">
					</div>
				</form>

				<div class="clearfix"></div>

			</div>
		</div>
		<div class="col-xl-9 col-lg-8 content-left-offset">

			<h3 class="page-title">İşlərin axtarışı</h3>

			<!-- Tasks Container -->
			<div class="tasks-list-container margin-top-35">
				<?php while($job = $jobsQuery->fetch(PDO::FETCH_ASSOC)){ 
						$regionsJobsQuery = $pdo->prepare("SELECT jobs.job_title, regions.region_title,regions.region_id
						FROM jobs
						LEFT JOIN jobs_regions on jobs.job_id = jobs_regions.job_id
						LEFT JOIN regions on jobs_regions.region_id = regions.region_id
						WHERE jobs_regions.job_id=:id");
						$regionsJobsQuery->execute([':id' => $job['job_id']]);
						$regionsJobs = $regionsJobsQuery->fetchAll(PDO::FETCH_ASSOC);
						$regionsJobsId = array_column($regionsJobs,'region_id');

						if($hasLocation){
							$bakuRegions = [9,10,11,12,13,14,15,16,17,18,19,20];
							if(!(count(array_intersect($bakuRegions, $requestLocations)) !=0 && in_array(8, $regionsJobsId))){ //baku regions 
								if(count(array_intersect($requestLocations,$regionsJobsId)) == 0){
									continue;
								}
							}
						}
					?>

					<a href="view_job.php?id=<?php echo $job['job_id']; ?>&back_to=main" class="task-listing">
						<!-- Job Listing Details -->
						<div class="task-listing-details">

							<!-- Details -->
							<div class="task-listing-description">
								<h3 class="task-listing-title"><?php echo $job['job_title'];?></h3>
								<ul class="task-icons">
									<li><i class="icon-feather-user"></i> <?php echo $job['first_name'].' '.$job['last_name'];?></li>
								</ul>
								<p class="task-listing-text"><?php echo $job['job_description']; ?></p>
								<h5>Kateqoriya - <?php echo $job['category_name']; ?></h5>
								<div class="task-tags">
									<?php foreach($regionsJobs as $region){?>
										<span><?php echo $region['region_title']; ?></span>
									<?php } ?>
								</div>
							</div>

						</div>

						<div class="task-listing-bid">
							<div class="task-listing-bid-inner">
								<div class="task-offers">
									<strong><?php echo $job['job_price'];?> AZN</strong>
								</div>
								<span class="button button-sliding-icon ripple-effect">Sifariş et <i class="icon-material-outline-arrow-right-alt"></i></span>
							</div>
						</div>
					</a>
				<?php }?>
				<!-- Pagination -->
				<div class="clearfix"></div>
				<div class="pagination-container margin-top-20 margin-bottom-20">
					<nav class="pagination">
						<ul>
							<?php echo $pagination; ?>
						</ul>				
					</nav>
				</div>
				<div class="clearfix"></div>
				<!-- Pagination / End -->

			</div>
			<!-- Tasks Container / End -->

		</div>
	</div>
</div>

<?php include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?> 