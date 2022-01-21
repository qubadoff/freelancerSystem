<?php 
require_once "../system/userTypeController.php";
if(isadmin()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
#pagination	
	if (isset($_GET['page'])) {
		$page = htmlspecialchars(trim($_GET['page']));
	} else {
		$page = 1;
	}

	$freelancerCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM users where user_type=1");
	$freelancerCountQuery->execute();
	$freelancerCount = $freelancerCountQuery->fetch(PDO::FETCH_ASSOC);
	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($freelancerCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";
	$url = "freelancers_admin.php";

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

	$freelancerQuery = $pdo->prepare("SELECT * FROM users WHERE user_type=1 ORDER BY id DESC LIMIT {$offset}, {$no_of_records_per_page}");
	$freelancerQuery->execute();
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Frilanserlər</h3>
			</div>
	
			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">

						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-feather-user"></i> Frilanserlər</h3>
							<br>
						</div>
						<br>
						<div class="content">
							<table class="basic-table">
								<tr>
									<th>Frilanser ID</th>
									<th>Frilanser Ad Soyad</th>
									<th>Email</th>
									<th>Nömrə</th>
									<th>Reytinq</th>
									<th>Balans</th>
									<th>Düzəliş et</th>
									<th>Sil</th>
								</tr>
								<?php while($freelancer = $freelancerQuery->fetch(PDO::FETCH_ASSOC)){ ?>
									<tr>
										<td data-label="Frilanser ID"><?php echo $freelancer['id'];?></td>
										<td data-label="Frilanser Ad Soyad"><?php echo $freelancer['first_name'].' '.$freelancer['last_name']; ?></td>
										<td data-label="Email"><?php echo $freelancer['email']; ?></td>
										<td data-label="Nömrə"><?php echo $freelancer['number']; ?></td>
										<td data-label="Reytinq"><?php echo $freelancer['rating']; ?></td>
										<td data-label="Balans"><?php echo $freelancer['balance']; ?> AZN</td>
										<td data-label="Düzəliş et"><a href="edit_freelancer.php?id=<?php echo $freelancer['id'];?>" style="color:black; font-size:20px;"><i class="icon-feather-edit"></i></a></td>
										<td data-label="Sil"><a href="../system/freelancerAdminController.php?operation=delete&id=<?php echo $freelancer['id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-delete"></a></i></td>
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