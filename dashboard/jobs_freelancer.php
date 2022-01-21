<?php 
require_once "../system/userTypeController.php";
if(isfreelancer()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	$freelancerId = $_SESSION['id'];
#pagination	
	if (isset($_GET['page'])) {
		$page = htmlspecialchars(trim($_GET['page']));
	} else {
		$page = 1;
	}

	$jobCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE user_id=:fid");
	$jobCountQuery->execute([':fid' => $freelancerId]);
	$jobCount = $jobCountQuery->fetch(PDO::FETCH_ASSOC);

	$balanceQuery = $pdo->prepare("SELECT balance FROM users WHERE id=:fid");
	$balanceQuery->execute([':fid' => $freelancerId]);
	$balance = $balanceQuery->fetch(PDO::FETCH_ASSOC);

	$minimumBalanceQuery = $pdo->prepare("SELECT minimum_balance FROM admin_settings WHERE setting_id=1");
	$minimumBalanceQuery->execute();
	$minimumBalance = $minimumBalanceQuery->fetch(PDO::FETCH_ASSOC);

	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($jobCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";
	$url = "jobs_freelancer.php";

	if($total_pages > 1){

		if($page > 1){
			$pagination.="<li class=\"pagination-arrow\"><a href=\"{$url}?page={$prev}\" class=\"ripple-effect\"><i class=\"icon-material-outline-keyboard-arrow-left\"></i></a></li>";
		
		}

		if ($total_pages < 7 + ($adjacents * 2)){   
			for ($counter = 1; $counter <= $total_pages; $counter++){
				if ($counter == $page){
					$pagination.="<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
				}
				else{
					$pagination.= "<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>";    
				}
							
			}
		}
		elseif($total_pages > 5 + ($adjacents * 2)){
			if($page < 1 + ($adjacents * 2)){
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
					if ($counter == $page){
						$pagination.="<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
					}
					else{
						$pagination.= "<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
					}                     
				}
				$pagination.= "...";
				$pagination.="<li><a href=\"{$url}?page={$lpm}\" class=\"ripple-effect\">{$lpm}</a></li>";
				$pagination.="<li><a href=\"{$url}?page={$total_pages}\" class=\"ripple-effect\">{$total_pages}</a></li>";

			}

			elseif($total_pages - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
				$pagination.="<li><a href=\"{$url}?page=1\" class=\"ripple-effect\">1</a></li>";
				$pagination.="<li><a href=\"{$url}?page=2\" class=\"ripple-effect\">2</a></li>";
				$pagination.="...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++){
					if ($counter == $page){
						$pagination.="<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
					}
					else{
						$pagination.= "<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
					}                 
				}
				$pagination.= "...";
				$pagination.="<li><a href=\"{$url}?page={$lpm}\" class=\"ripple-effect\">{$lpm}</a></li>";
				$pagination.="<li><a href=\"{$url}?page={$total_pages}\" class=\"ripple-effect\">{$total_pages}</a></li>";
			}

			else{
				$pagination.="<li><a href=\"{$url}?page=1\" class=\"ripple-effect\">1</a></li>";
				$pagination.="<li><a href=\"{$url}?page=2\" class=\"ripple-effect\">2</a></li>";
				$pagination.="...";
				for ($counter = $total_pages - (2 + ($adjacents * 2)); $counter <= $total_pages; $counter++){
					if ($counter == $page){
						$pagination.="<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect current-page\">{$counter}</a></li>";
					}
					else{
						$pagination.= "<li><a href=\"{$url}?page={$counter}\" class=\"ripple-effect\">{$counter}</a></li>"; 
					}                 
				}
			}
		}

	}
	if($page < $total_pages - 1){
		$pagination.="<li class=\"pagination-arrow\"><a href=\"{$url}?page={$next}\" class=\"ripple-effect\"><i class=\"icon-material-outline-keyboard-arrow-right\"></i></a></li>";
	}
#endpagination

	$jobsQuery = $pdo->prepare("SELECT 	jobs.job_id, jobs.job_price, jobs.job_title, jobs.job_description, jobs.verification_status,
										categories.category_name, users.first_name, users.last_name
								FROM jobs
								INNER JOIN categories on jobs.category_id=categories.category_id
								INNER JOIN users on jobs.user_id=users.id
								WHERE jobs.user_id=:fid
								ORDER BY job_id DESC
								LIMIT {$offset}, {$no_of_records_per_page}");
	$jobsQuery->execute([':fid' => $freelancerId]);


?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>İşlər</h3>
			</div>
	
			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">
						<?php if($balance['balance'] < $minimumBalance['minimum_balance']){ ?>
							<div class="notification error">
								<p><strong>Balansınız(<?= $balance['balance'].'AZN';?>) minimal balansdan(<?= $minimumBalance['minimum_balance'].'AZN';?>) az olduğu üçün işləriniz axtarışda deaktivdir.</strong></p>
							</div>
						<?php } ?>
						
						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-material-outline-business-center"></i> İşlər</h3>
							<br>
							<a href="add_job.php" class="button">Yeni iş <i class="icon-feather-plus"></i></a>
						</div>
						<br>
						<div class="content">

							<table class="basic-table">
								<tr>
									<th>İş ID</th>
									<th>İşin adı</th>
									<th>Kateqoriya</th>
									<th>Qiymət</th>
									<th>Status</th>
									<th>Düzəliş et</th>
									<th>İşə bax</th>
									<th>Sil</th>
								</tr>
								<?php while($job = $jobsQuery->fetch(PDO::FETCH_ASSOC)){ ?>
									<tr>
										<td data-label="İş ID"><?php echo $job['job_id'];?></td>
										<td data-label="İşin adı"><?php echo $job['job_title']; ?></td>
										<td data-label="Kateqoriya"><?php echo $job['category_name']; ?></td>
										<td data-label="Qiymət"><?php echo $job['job_price']; ?></td>
										<td data-label="Status"><?php if($job['verification_status']){ echo 'Aktiv'; } else{echo 'Deaktiv';}?></td>
										<td data-label="Düzəliş et"><a href="edit_job_freelancer.php?id=<?php echo $job['job_id'];?>" style="color:black; font-size:20px;"><i class="icon-feather-edit"></i></a></td>
										<td data-label="İşə bax"><a href="view_job.php?&id=<?php echo $job['job_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-search"></a></i></td>
										<td data-label="Sil"><a href="../system/jobFreelancerController.php?operation=delete&id=<?php echo $job['job_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-delete"></a></i></td>
									</tr>
								<?php }?>
							</table>
						</div>
					</div>
				</div>

			</div>

			<!-- Row / End -->
			
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

<?php include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?> 