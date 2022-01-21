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

	$reviewCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM reviews");
	$reviewCountQuery->execute();
	$reviewCount = $reviewCountQuery->fetch(PDO::FETCH_ASSOC);
	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($reviewCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";
	$url = "reviews_admin.php";

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

	$reviewQuery = $pdo->prepare("SELECT 
									reviews.review_id, reviews.review_rating, reviews.review_text,
									U1.first_name AS fre_first, U1.last_name AS fre_last,
									U2.first_name AS emp_first, U2.last_name AS emp_last
									FROM reviews
									INNER JOIN users as U1 ON reviews.freelancer_id = U1.id
									INNER JOIN users as U2 on reviews.employer_id = U2.id
									ORDER BY review_id DESC
									LIMIT {$offset}, {$no_of_records_per_page}");
	$reviewQuery->execute();
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Dəyərləndirmələr</h3>
			</div>
	
			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">

						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-material-outline-rate-review"></i> Dəyərləndirmələr</h3>
							<br>
						</div>
						<br>
						<div class="content">
							<table class="basic-table">
								<tr>
									<th>Dəyərləndirmə ID</th>
									<th>İşəgötürən(rəy verən)</th>
									<th>Frilanser(dəyərləndirilən)</th>
									<th>Dəyərləndirmə mətni</th>
									<th>Reytinq</th>
									<th>Sil</th>
								</tr>
								<?php while($review = $reviewQuery->fetch(PDO::FETCH_ASSOC)){ ?>
									<tr>
										<td data-label="Dəyərləndirmə ID"><?php echo $review['review_id'];?></td>
										<td data-label="İşəgötürən(rəy verən)"><?php echo $review['emp_first'].' '.$review['emp_last'];?></td>
										<td data-label="Frilanser(dəyərləndirilən)"><?php echo $review['fre_first'].' '.$review['fre_last'];?></td>
										<td data-label="Dəyərləndirmə mətni"><?php echo $review['review_text'];?></td>
										<td data-label="Reytinq"><?php echo $review['review_rating'];?></td>
										<td data-label="Sil"><a href="../system/reviewAdminController.php?operation=delete&id=<?php echo $review['review_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-delete"></a></i></td>
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