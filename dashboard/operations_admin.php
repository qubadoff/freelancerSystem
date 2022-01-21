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

	$orderCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
	$orderCountQuery->execute();
	$orderCount = $orderCountQuery->fetch(PDO::FETCH_ASSOC);
	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($orderCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";
	$url = "operations_admin.php";

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

	$ordersQuery = $pdo->prepare("SELECT jobs.job_id, jobs.job_price, jobs.job_title, jobs.job_description,
										orders.order_id, orders.verification_status, orders.created_at, orders.completed_status,
										U1.first_name AS fre_first, U1.last_name AS fre_last,
										U2.first_name AS emp_first, U2.last_name AS emp_last
										FROM orders
										INNER JOIN jobs ON orders.job_id = jobs.job_id
										INNER JOIN users as U1 ON orders.freelancer_id = U1.id
										INNER JOIN users as U2 on orders.employer_id = U2.id
										ORDER BY order_id DESC
										LIMIT {$offset}, {$no_of_records_per_page}");
	$ordersQuery->execute();


?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Əməliyyatlar</h3>

			</div>
	
			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">

						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-line-awesome-cube"></i> Əməliyyatlar</h3>
							<br>
						</div>
						<br>
						<div class="content">
							<table class="basic-table">
								<tr>
									<th>Əməliyyat ID</th>
									<th>İş adı</th>
									<th>Frilanser Ad/Soyad</th>
									<th>İşəgötürən Ad/Soyad</th>
									<th>Status</th>
									<th>Tarix</th>
									<th>Əməliyyata bax</th>
									<th>Sil</th>
									<th>Aktiv/Deaktiv et</th>
								</tr>
								<?php while($order = $ordersQuery->fetch(PDO::FETCH_ASSOC)){ ?>
									<tr>
										<td data-label="Əməliyyat ID"><?php echo $order['order_id'];?></td>
										<td data-label="İş adı"><a href="view_job_admin.php?id=<?php echo $order['job_id'];?>"><?php echo $order['job_title']; ?></a></td>
										<td data-label="Frilanser Ad/Soyad"><?php echo $order['fre_first'].' '.$order['fre_last']; ?></td>
										<td data-label="İşəgötürən Ad/Soyad"><?php echo $order['emp_first'].' '.$order['emp_last']; ?></td>
										<td data-label="Status"><?php if($order['verification_status']){ echo 'Aktiv'; } else{echo 'Deaktiv';}?></td>
										<td data-label="Tarix"><?php echo $order['created_at'];?></td>
										<td data-label="Əməliyyata bax"><a href="view_operation_admin.php?&id=<?php echo $order['order_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-search"></a></i></td>
										<td data-label="Sil"><a href="../system/operationAdminController.php?operation=delete&id=<?php echo $order['order_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-delete"></a></i></td>	
										<?php
											if(!$order['completed_status']){
												if($order['verification_status']){
										?>
											<td data-label="Aktiv/Deaktiv et"><a href="../system/operationAdminController.php?operation=toggle&id=<?php echo $order['order_id'];?>" class="button" style="background-color:red;color:white;font-size:13px;">Deaktiv et</a></td>
										<?php } else{?>	
											<td data-label="Aktiv/Deaktiv et"><a href="../system/operationAdminController.php?operation=toggle&id=<?php echo $order['order_id'];?>" class="button" style="background-color:green;color:white;">Aktiv et</a></td>
										<?php }} ?>
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