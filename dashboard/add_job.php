<?php
require_once "../system/userTypeController.php";
if(isfreelancer()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	$categoriesQuery = $pdo->prepare("SELECT * FROM categories");
	$categoriesQuery->execute();

	$regionsQuery = $pdo->prepare("SELECT * FROM regions");
	$regionsQuery->execute();
?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>İş əlavə et</h3>
			</div>
			<form action="../system/jobFreelancerController.php" method="POST" enctype="multipart/form-data">
			<!-- Row -->
				<div class="row">	
					<!-- Dashboard Box -->
						<div class="col-xl-12">
							<div class="dashboard-box margin-top-0">

								<!-- Headline -->
								<div class="headline">
									<h3><i class="icon-material-outline-business-center"></i> İş əlavə etmə formu</h3>
								</div>

								<div class="content with-padding padding-bottom-10">
									<div class="row">
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş başlığı</h5>
												<input type="text" name="title" class="with-border" required>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş təsviri</h5>
												<textarea name="description" class="with-border" required></textarea>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş kateqoriyası</h5>
												<select class="selectpicker" name="category" required data-size="5">
													<?php while($category = $categoriesQuery->fetch(PDO::FETCH_ASSOC)){?>
														<option value="<?php echo $category['category_id']?>"><?php echo $category['category_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İşin görülə biləcəyi ərazilər</h5>
												<select class="selectpicker" name="location[]" multiple required data-size="5" title="Ərazi seçin">
													<?php while($region = $regionsQuery->fetch(PDO::FETCH_ASSOC)){?>
														<option value="<?php echo $region['region_id']?>"><?php echo $region['region_title']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
														
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş qiyməti</h5>
												<input type="number" name="price" class="with-border" required>
											</div>
										</div>
										
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İşinizə aid şəkillər(maksimum 4, minimum 1 şəkil (PNG,JPG))</h5>
												<div class="uploadButton margin-top-30">
													<input class="uploadButton-input" required type="file" name="pictures[]" accept="image/jpeg, image/png" id="upload" multiple/>
													<label class="uploadButton-button ripple-effect" for="upload">Şəkilləri yükləyin(hər biri 1MB-dan kiçik)</label>
													<span class="uploadButton-file-name">Öncəki gördüyünüz işlərdən nümunələr, və ya izahedici şəkillər.</span>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-12">
							<input type="submit" value="Əlavə et" name="addJobFreelancer">
						</div>
				</div>
			</form>
			<!-- Row / End -->
<?php  include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?>