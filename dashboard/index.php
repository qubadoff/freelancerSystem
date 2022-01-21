<?php
require_once "dash_header.php";
require_once "dash_sidebar.php";

$getUserInfo = $pdo->prepare("SELECT * FROM users WHERE id=:id");
$getUserInfo->execute([':id'=>$_SESSION['id']]);
$user = $getUserInfo->fetch(PDO::FETCH_ASSOC);

$paramsOperationsAndReviews = [];
$sqlOperations = "SELECT COUNT(*) as count_operations FROM orders WHERE verification_status=1 AND completed_status=1";
$sqlReviews = "SELECT COUNT(*) as count_review FROM reviews";
if(isfreelancer()){
	$paramsOperationsAndReviews = [':id'=>$_SESSION['id']];
	$sqlOperations.=" AND (freelancer_id=:id OR employer_id=:id)";
	$sqlReviews.=" WHERE freelancer_id=:id OR employer_id=:id";
}
$completedOperationsQuery = $pdo->prepare($sqlOperations);
$completedOperationsQuery->execute($paramsOperationsAndReviews);
$completedOperations = $completedOperationsQuery->fetch(PDO::FETCH_ASSOC);

$countOfJobsQuery = $pdo->prepare("SELECT COUNT(*) as count_jobs FROM jobs WHERE verification_status=1 AND user_id=:id");
$countOfJobsQuery->execute([':id' => $_SESSION['id']]);
$countOfJobs = $countOfJobsQuery->fetch(PDO::FETCH_ASSOC);


$reviewCountQuery = $pdo->prepare($sqlReviews);
$reviewCountQuery->execute($paramsOperationsAndReviews);
$reviewCount = $reviewCountQuery->fetch(PDO::FETCH_ASSOC);

$operationPageLink = 'view_operation_admin.php';
if(isfreelancer() || isadmin()){
	$sql = "SELECT * FROM transactions INNER JOIN users ON transactions.user_id=users.id ";
	$paramsTransaction = [];
	if(isfreelancer()){
		$operationPageLink = 'view_operation.php';
		$sql.="WHERE user_id=:uid ";
		$paramsTransaction = [':uid' => $_SESSION['id']];
	}
	$lastTransactionsQuery = $pdo->prepare($sql."ORDER BY transaction_id DESC LIMIT 5");
	$lastTransactionsQuery->execute($paramsTransaction);
}
?>
	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Salam, <?php echo $user['first_name'];?>!</h3>
				<span>Sizi yenidən görməyə şadıq</span>


			</div>
	
			<!-- Fun Facts Container -->
			<div class="fun-facts-container">
				<div class="fun-fact" data-fun-fact-color="#36bd78">
					<div class="fun-fact-text">
						<span>Bitmiş əməliyyatlar</span>
						<h4><?php echo $completedOperations['count_operations'];?></h4>
					</div>
					<div class="fun-fact-icon"><i class="icon-material-outline-gavel"></i></div>
				</div>
				<?php if($user['user_type'] == 1){?>
					<div class="fun-fact" data-fun-fact-color="#b81b7f">
						<div class="fun-fact-text">
							<span>Təqdim olunan xidmətlər</span>
							<h4><?php echo $countOfJobs['count_jobs'];?></h4>
						</div>
						<div class="fun-fact-icon"><i class="icon-material-outline-business-center"></i></div>
					</div>
				<?php } ?>
				<div class="fun-fact" data-fun-fact-color="#efa80f">
					<div class="fun-fact-text">
						<span>Dəyərləndirmələr</span>
						<h4><?php echo $reviewCount['count_review']; ?></h4>
					</div>
					<div class="fun-fact-icon"><i class="icon-material-outline-rate-review"></i></div>
				</div>

				<!-- Last one has to be hidden below 1600px, sorry :( -->
				<div class="fun-fact" data-fun-fact-color="#2a41e6">
					<div class="fun-fact-text">
						<span>This Month Views</span>
						<h4>987</h4>
					</div>
					<div class="fun-fact-icon"><i class="icon-feather-trending-up"></i></div>
				</div>
			</div>
			


			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-6">
					<div class="dashboard-box">
						<div class="headline">
							<h3><i class="icon-material-baseline-notifications-none"></i> Notifications</h3>
							<button class="mark-as-read ripple-effect-dark" data-tippy-placement="left" title="Mark all as read">
									<i class="icon-feather-check-square"></i>
							</button>
						</div>
						<div class="content">
							<ul class="dashboard-box-list">
								<li>
									<span class="notification-icon"><i class="icon-material-outline-group"></i></span>
									<span class="notification-text">
										<strong>Michael Shannah</strong> applied for a job <a href="#">Full Stack Software Engineer</a>
									</span>
									<!-- Buttons -->
									<div class="buttons-to-right">
										<a href="#" class="button ripple-effect ico" title="Mark as read" data-tippy-placement="left"><i class="icon-feather-check-square"></i></a>
									</div>
								</li>
								<li>
									<span class="notification-icon"><i class=" icon-material-outline-gavel"></i></span>
									<span class="notification-text">
										<strong>Gilber Allanis</strong> placed a bid on your <a href="#">iOS App Development</a> project
									</span>
									<!-- Buttons -->
									<div class="buttons-to-right">
										<a href="#" class="button ripple-effect ico" title="Mark as read" data-tippy-placement="left"><i class="icon-feather-check-square"></i></a>
									</div>
								</li>
								<li>
									<span class="notification-icon"><i class="icon-material-outline-autorenew"></i></span>
									<span class="notification-text">
										Your job listing <a href="#">Full Stack Software Engineer</a> is expiring
									</span>
									<!-- Buttons -->
									<div class="buttons-to-right">
										<a href="#" class="button ripple-effect ico" title="Mark as read" data-tippy-placement="left"><i class="icon-feather-check-square"></i></a>
									</div>
								</li>
								<li>
									<span class="notification-icon"><i class="icon-material-outline-group"></i></span>
									<span class="notification-text">
										<strong>Sindy Forrest</strong> applied for a job <a href="#">Full Stack Software Engineer</a>
									</span>
									<!-- Buttons -->
									<div class="buttons-to-right">
										<a href="#" class="button ripple-effect ico" title="Mark as read" data-tippy-placement="left"><i class="icon-feather-check-square"></i></a>
									</div>
								</li>
								<li>
									<span class="notification-icon"><i class="icon-material-outline-rate-review"></i></span>
									<span class="notification-text">
										<strong>David Peterson</strong> left you a <span class="star-rating no-stars" data-rating="5.0"></span> rating after finishing <a href="#">Logo Design</a> task
									</span>
									<!-- Buttons -->
									<div class="buttons-to-right">
										<a href="#" class="button ripple-effect ico" title="Mark as read" data-tippy-placement="left"><i class="icon-feather-check-square"></i></a>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<?php if(isfreelancer() || isadmin()){ ?>
					<!-- Dashboard Box -->
					<div class="col-xl-6">
						<div class="dashboard-box">
							<div class="headline">
								<h3><i class="icon-line-awesome-money"></i> Son balans əməliyyatları</h3>
							</div>
							<div class="content">
								<ul class="dashboard-box-list">
									<?php while($lastTransaction = $lastTransactionsQuery->fetch(PDO::FETCH_ASSOC)){
										$sign = '';
										if($lastTransaction['balance_after'] > $lastTransaction['balance_before']){
											$sign = '+';
										}
										else{
											$sign = '-';
										}
										?>
										<li>
											<div class="invoice-list-item">
											<strong><?= $sign.$lastTransaction['amount'].' AZN'?></strong>
												<ul>
													<?php if($lastTransaction['transaction_type'] == 1){ ?>
														<li><span class="unpaid">Kommissiya</span></li>
													<?php } else if($lastTransaction['transaction_type'] == 2){ ?>
														<li><span class="paid">Pul yüklənişi</span></li>
													<?php } else if($lastTransaction['transaction_type'] == 3){?>
														<li><span class="paid" style="background-color:blue !important;">Admin düzəlişi</span></li>
													<?php } ?>

													<?php if(isfreelancer()){?>
														<li>Balans: <?= $lastTransaction['balance_after'];?> </li>
													<?php } else if(isadmin()){ ?>
														<li>Frilanser: <?= $lastTransaction['first_name'].' '. $lastTransaction['last_name'];?> </li>
													<?php } ?>
													<li><?= $lastTransaction['transaction_time'];?></li>
												</ul>
											</div>
											<!-- Buttons -->
											<?php if($lastTransaction['transaction_type'] == 1){ ?>
												<div class="buttons-to-right">
													<a href="<?= $operationPageLink; ?>?id=<?= $lastTransaction['order_id'];?>" class="button">Əməliyyata bax</a>
												</div>
											<?php } ?>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
				<?php } ?>

			</div>
			<!-- Row / End -->

<?php include "dash_footer.php"; ?>