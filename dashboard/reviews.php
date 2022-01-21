<?php 
require_once "../system/userTypeController.php";
if(isfreelancer() or isemployer()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	
#pagination	
	if (isset($_GET['page'])) {
		$page = htmlspecialchars(trim($_GET['page']));
	} else {
		$page = 1;
	}

	$url = "reviews.php?";
	if (isset($_GET['user'])) {
		$userId = htmlspecialchars(trim($_GET['user']));
		$url = "reviews.php?user={$userId}&";
	} else {
		$userId = $_SESSION['id'];
	}
	//check for id-s(if freelancer_id is really freelancer and employer_id is really employer) in review adding process
	$reviewCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM reviews WHERE freelancer_id=:uid OR employer_id=:uid");
	$reviewCountQuery->execute([':uid' => $userId]);
	$reviewCount = $reviewCountQuery->fetch(PDO::FETCH_ASSOC);
	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($reviewCount['count'] / $no_of_records_per_page);

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

	$reviewsQuery = $pdo->prepare("SELECT reviews.review_id, reviews.review_rating, reviews.review_text,
											U1.first_name AS fre_first, U1.last_name AS fre_last,
											U2.first_name AS emp_first, U2.last_name AS emp_last
											FROM reviews 
											INNER JOIN users as U1 ON reviews.freelancer_id = U1.id
											INNER JOIN users as U2 ON reviews.employer_id = U2.id
											WHERE freelancer_id=:uid OR employer_id=:uid
											ORDER BY review_id DESC
											LIMIT {$offset}, {$no_of_records_per_page}");
	$reviewsQuery->execute([':uid' => $userId]);


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
						</div>

						<div class="content">
							<ul class="dashboard-box-list">
							<?php while($review = $reviewsQuery->fetch(PDO::FETCH_ASSOC)){ ?>
								<li>
									<div class="boxed-list-item">
										<!-- Content -->
										<div class="item-content">
											<h4><strong><?php echo $review['emp_first'].' '.$review['emp_last'];?></strong>-in <strong><?php echo $review['fre_first'].' '.$review['fre_last'];?></strong> haqda dəyərləndirməsi:</h4>
											<div class="item-description">
												<p><?php echo $review['review_text']; ?></p>
											</div>
											<div class="item-details margin-top-10">
												<div class="star-rating" data-rating="<?php echo $review['review_rating'];?>"></div>
												&nbsp;
												<?php if(isemployer() && $userId == $_SESSION['id']){?>
													<a href="../system/reviewEmployerController.php?operation=delete&id=<?php echo $review['review_id'];?>" class="button">Dəyərləndirməni sil</a><br>													
												<?php } ?>
											</div>
										</div>
									</div>
								</li>
							<?php } ?>
							</ul>
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