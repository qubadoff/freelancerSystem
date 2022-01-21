<?php
require_once "../system/userTypeController.php";
if(isadmin()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	if(isset($_GET['id'])){
		$id = htmlspecialchars(trim($_GET['id']));
		$employerQuery = $pdo->prepare("SELECT * FROM users WHERE id=:id");
		$employerQuery->execute([':id' => $id]);
		$employer = $employerQuery->fetch(PDO::FETCH_ASSOC);
	}

?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>İşəgötürənə düzəliş et</h3>
			</div>
			<form action="../system/employerAdminController.php" method="POST">
			<!-- Row -->
				<div class="row">	
					<!-- Dashboard Box -->
						<div class="col-xl-12">
							<div class="dashboard-box margin-top-0">

								<!-- Headline -->
								<div class="headline">
									<h3><i class="icon-feather-user"></i> İşəgötürənə düzəliş etmə formu</h3>
								</div>

								<div class="content with-padding padding-bottom-10">
									<div class="row">
										<div class="col-xl-6">
											<div class="submit-field">
												<h5>İşəgötürənin adı</h5>
												<input type="hidden" name="id" value="<?php echo $id;?>">
												<input type="text" name="first_name" value="<?php echo $employer['first_name'];?>"class="with-border" required>
											</div>
										</div>
										<div class="col-xl-6">
											<div class="submit-field">
												<h5>İşəgötürənin soyadı</h5>
												<input type="text" name="last_name" value="<?php echo $employer['last_name'];?>"class="with-border" required>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xl-4">
											<div class="submit-field">
												<h5>Email</h5>
												<input type="text" name="email" value="<?php echo $employer['email'];?>"class="with-border" required>
											</div>
										</div>
										<div class="col-xl-4">
											<div class="submit-field">
												<h5>Nömrə</h5>
												<input type="text" name="number" value="<?php echo $employer['number'];?>"class="with-border" required>
											</div>
										</div>
										<div class="col-xl-4">
											<div class="submit-field">
												<h5>Lokasiya</h5>
												<input type="text" name="location" value="<?php echo $employer['location'];?>"class="with-border">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12">
							<input type="submit" value="Düzəliş et" name="editEmployer">
						</div>
				</div>
			</form>
			<!-- Row / End -->
<?php  include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?>