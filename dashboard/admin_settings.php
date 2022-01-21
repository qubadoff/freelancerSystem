<?php
require_once "../system/userTypeController.php";
if(isadmin()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	$getSettingsQuery = $pdo->prepare("SELECT * FROM admin_settings WHERE setting_id=1");
	$getSettingsQuery->execute();
	$settings = $getSettingsQuery->fetch(PDO::FETCH_ASSOC);
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>Ümumi Tənzimləmələr</h3>

			</div>
			<form action="../system/settingsAdminController.php" method="POST">
			<!-- Row -->
				<div class="row">	
					<!-- Dashboard Box -->
						<div class="col-xl-12">
							<div class="dashboard-box margin-top-0">

								<!-- Headline -->
								<div class="headline">
									<h3><i class="icon-material-outline-business-center"></i> Ümumi tənzimləmələr</h3>
								</div>

								<div class="content with-padding padding-bottom-10">
									<div class="row">
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>Kommisiyya faizi</h5>
												<input type="number" name="rate" value="<?php echo $settings['commission_rate'];?>"class="with-border" required>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>Frilanserlərin minimal balansı</h5>
												<input type="number" name="minimum_balance" value="<?php echo $settings['minimum_balance'];?>"class="with-border" required>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12">
							<input type="submit" value="Düzəliş et" name="editSettingsAdmin">
						</div>
				</div>
			</form>
			<!-- Row / End -->
<?php  include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?>