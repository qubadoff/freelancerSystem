<?php 
require_once "../system/userTypeController.php";
if(isfreelancer()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
#pagination	
	if (isset($_GET['page'])) {
		$page = htmlspecialchars(trim($_GET['page']));
	} else {
		$page = 1;
	}

	$transactionCountQuery = $pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE user_id=:uid");
	$transactionCountQuery->execute([':uid' => $_SESSION['id']]);
	$transactionCount = $transactionCountQuery->fetch(PDO::FETCH_ASSOC);

	$balanceUserQuery = $pdo->prepare("SELECT balance FROM users WHERE id=:uid");
	$balanceUserQuery->execute([':uid' => $_SESSION['id']]);
	$balanceUser = $balanceUserQuery->fetch(PDO::FETCH_ASSOC);

	$minimumBalanceQuery = $pdo->prepare("SELECT minimum_balance FROM admin_settings WHERE setting_id=1");
	$minimumBalanceQuery->execute();
	$minimumBalance = $minimumBalanceQuery->fetch(PDO::FETCH_ASSOC);

	$no_of_records_per_page = 10;
	$offset = ($page-1) * $no_of_records_per_page; 
	$total_pages = ceil($transactionCount['count'] / $no_of_records_per_page);

	$adjacents = 2;
	$prev = $page - 1;
	$next = $page + 1;
	$lpm = $total_pages - 1;

	$pagination = "";
	$url = "balance_freelancer.php";

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

	$transactionQuery = $pdo->prepare("SELECT * FROM transactions WHERE user_id=:uid ORDER BY transaction_id DESC LIMIT {$offset}, {$no_of_records_per_page}");
	$transactionQuery->execute([':uid' => $_SESSION['id']]);
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Balans</h3>
			</div>
	
			<!-- Row -->
			<div class="row">


				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">

						<div class="notification success">
							<p><strong>Balansınız: <?= $balanceUser['balance'];?> AZN</strong></p>
						</div>

						<?php if(($balanceUser['balance'] < $minimumBalance['minimum_balance'])){ ?>
							<div class="notification error">
								<p><strong>Balansınız(<?= $balanceUser['balance'].'AZN';?>) minimal balansdan(<?= $minimumBalance['minimum_balance'].'AZN';?>) az olduğu üçün işləriniz axtarışda deaktivdir.</strong></p>
							</div>
						<?php } ?>
						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-material-outline-account-balance-wallet"></i> Balans əməliyyatları</h3>
						</div>
						<br>
						<div class="content">
							<table class="basic-table">
								<tr>
									<th>Tranzaksiya ID</th>
									<th>Tip</th>
									<th>Əməliyyat ID</th>
									<th>Məbləğ</th>
									<th>İlkin balans</th>
									<th>Sonrakı balans</th>
									<th>Tarix</th>
								</tr>
								<?php while($transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC)){
										$sign = '';
										if($transaction['balance_after'] > $transaction['balance_before']){
											$sign = '+';
										}
										else{
											$sign = '-';
										}
									?>
									<tr>
										<td data-label="Tranzaksiya ID"><?php echo $transaction['transaction_id'];?></td>
										<td data-label="Tip">
											<?php if($transaction['transaction_type'] == 1){ ?>
												<span class="dashboard-status-button red">Kommissiya</span>
											<?php } else if($transaction['transaction_type'] == 2){ ?>
												<span class="dashboard-status-button green">Pul yüklənişi</span>
											<?php } else if($transaction['transaction_type'] == 3){?>
												<span class="dashboard-status-button" style="background-color:blue !important;">Admin düzəlişi</span>
											<?php } ?>
										</td>
										<td data-label="Əməliyyat ID">
											<?php if($transaction['transaction_type'] == 1){ ?>
												<a href="view_operation.php?id=<?=$transaction['order_id'];?>"><?php echo $transaction['order_id'];?></a>
											<?php } ?>
										</td>
										<td data-label="Məbləğ"><?php echo $sign.$transaction['amount'];?></td>
										<td data-label="İlkin balans"><?php echo $transaction['balance_before'];?></td>
										<td data-label="Sonrakı balans"><?php echo $transaction['balance_after'];?></td>
										<td data-label="Tarix"><?php echo $transaction['transaction_time'];?></td>
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