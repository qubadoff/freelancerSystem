<?php
require_once "../system/userTypeController.php";
if(isfreelancer()){
	require_once 'dash_header.php';
	require_once 'dash_sidebar.php';
	if(isset($_GET['id'])){
		$id = htmlspecialchars(trim($_GET['id']));
		$freelancerId = $_SESSION['id'];
		$jobQuery = $pdo->prepare("SELECT * FROM jobs WHERE job_id=:id AND user_id=:fid");
		$jobQuery->execute([':id' => $id,':fid'=>$freelancerId]);
		$job = $jobQuery->fetch(PDO::FETCH_ASSOC);

		if(!$job){
			header("Location:jobs_freelancer.php");
		}
		else{
			$categoriesQuery = $pdo->prepare("SELECT * FROM categories");
			$categoriesQuery->execute();
	
			$regionsQuery = $pdo->prepare("SELECT * FROM regions");
			$regionsQuery->execute();
	
			$regionsJobsQuery = $pdo->prepare("SELECT region_id FROM jobs_regions WHERE job_id=:id");
			$regionsJobsQuery->execute([':id' => $id]);
			$regionsJobs = $regionsJobsQuery->fetchAll(PDO::FETCH_COLUMN);

			$jobsPhotosQuery = $pdo->prepare("SELECT * FROM jobs_pictures WHERE job_id=:jid");
			$jobsPhotosQuery->execute([':jid'=>$id]);
		}

	}
	else{
		header("Location:jobs_freelancer.php");
	}

?>

	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
			
			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3>İşə düzəliş et</h3>

			</div>
			<form action="../system/jobFreelancerController.php" method="POST" enctype="multipart/form-data">
			<!-- Row -->
				<div class="row">	
					<!-- Dashboard Box -->
						<div class="col-xl-12">
							<div class="dashboard-box margin-top-0">

								<!-- Headline -->
								<div class="headline">
									<h3><i class="icon-material-outline-business-center"></i> İşə düzəliş etmə formu</h3>
								</div>

								<div class="content with-padding padding-bottom-10">
									<div class="row">
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş başlığı</h5>
												<input type="hidden" name="id" value="<?php echo $id;?>">
												<input type="text" name="title" value="<?php echo $job['job_title'];?>"class="with-border" required>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş təsviri</h5>
												<textarea name="description" class="with-border" required><?php echo $job['job_description'];?></textarea>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş kateqoriyası</h5>
												<select class="selectpicker" name="category" data-size="5">
													<?php while($category = $categoriesQuery->fetch(PDO::FETCH_ASSOC)){?>
														<option <?php if($category['category_id'] == $job['category_id']){?> selected <?php } ?> value="<?php echo $category['category_id']?>"><?php echo $category['category_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										
										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İşin görülə biləcəyi ərazilər</h5>
												<select class="selectpicker" name="location[]" multiple required data-size="5" data-title="Ərazi seçin">
													<?php while($region = $regionsQuery->fetch(PDO::FETCH_ASSOC)){?>
														<option value="<?php echo $region['region_id']?>" <?php if(in_array($region['region_id'],$regionsJobs)){?> selected <?php } ?>><?php echo $region['region_title']?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İş qiyməti</h5>
												<input type="number" name="price" value="<?php echo $job['job_price'];?>"class="with-border" required>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İşə aid şəkillər</h5>
												<table class="basic-table">
													<tr>
														<th>Şəkil</th>
														<th>Sil</th>

													</tr>
													<?php while($jobPhoto = $jobsPhotosQuery->fetch(PDO::FETCH_ASSOC)){?>
														<tr>
															<td data-label="Şəkil"><img src="../assets/images/job_pictures/<?=$jobPhoto['picture_url'];?>" width="100" height="100"></td>
															<td data-label="Sil"><a href="../system/jobFreelancerController.php?operation=deletePic&id=<?php echo $jobPhoto['jobs_pictures_id'];?>&jobId=<?php echo $jobPhoto['job_id'];?>" style="color:black;font-size:20px;"><i class="icon-feather-delete"></i></a></td>	
														</tr>	
													<?php } ?>
												</table>
											</div>
										</div>

										<div class="col-xl-12">
											<div class="submit-field">
												<h5>İşinizə aid şəkillər(Ümumilikdə maksimum 4 şəkil olmalıdır, PNG,JPG)</h5>
												<div class="uploadButton margin-top-30">
													<input class="uploadButton-input"  type="file" name="pictures[]" accept="image/jpeg, image/png" id="upload" multiple/>
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
							<input type="submit" value="Düzəliş et" name="editJobFreelancer">
						</div>
				</div>
			</form>
			<!-- Row / End -->
<?php  include 'dash_footer.php'; }

else{
	header("Location:index.php");
}?>