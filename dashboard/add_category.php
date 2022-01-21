<?php 
require_once "../system/userTypeController.php";
if(isadmin()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Kateqoriya əlavə et</h3>

			</div>
			<form action="../system/categoryController.php" method="POST">
			<!-- Row -->
				<div class="row">	
					<!-- Dashboard Box -->
						<div class="col-xl-12">
							<div class="dashboard-box margin-top-0">

								<!-- Headline -->
								<div class="headline">
									<h3><i class="icon-line-awesome-navicon"></i> Kateqoriya əlavə etmə formu</h3>
								</div>

								<div class="content with-padding padding-bottom-10">
									<div class="row">
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>Kateqoriya adı</h5>
												<input type="text" name="title" class="with-border" required>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12">
							<input type="submit" value="Əlavə et" name="addCategory">
						</div>
				</div>
			</form>
			<!-- Row / End -->
<?php include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?>