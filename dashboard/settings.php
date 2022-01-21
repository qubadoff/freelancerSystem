<?php
include "dash_header.php";

$userQuery = $pdo->prepare("SELECT * FROM users WHERE id=:uid");
$userQuery->execute([':uid' => $_SESSION['id']]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

?>


	<!-- Dashboard Content
	================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >
		
			<!-- Row -->
			<div class="row">
				<!-- Dashboard Box -->
				<form action="../system/userUpdateController.php" method="POST" enctype="multipart/form-data">
					<div class="col-xl-12">
						<div class="dashboard-box margin-top-0">

							<!-- Headline -->
							<div class="headline">
								<h3><i class="icon-material-outline-account-circle"></i> Hesabım</h3>
							</div>

							<div class="content with-padding padding-bottom-0">

								<div class="row">

									<div class="col-auto">
										<div class="avatar-wrapper" data-tippy-placement="bottom" title="Change Avatar">
											<img class="profile-pic" src="../assets/images/profile_pictures/<?=$user['avatar'];?>" alt="" />
											<div class="upload-button"></div>
											<input class="file-upload" type="file" name="avatar" accept="image/*"/>
										</div>
									</div>

									<div class="col">
										<div class="row">

											<div class="col-xl-6">
												<div class="submit-field">
													<h5>Ad</h5>
													<input type="text" name="first_name" class="with-border" required value="<?= $user['first_name'];?>">
												</div>
											</div>

											<div class="col-xl-6">
												<div class="submit-field">
													<h5>Soyad</h5>
													<input type="text" name="last_name" class="with-border" required value="<?= $user['last_name']; ?>">
												</div>
											</div>

											<div class="col-xl-6">
												<div class="submit-field">
													<h5>Email</h5>
													<input type="text" name="email" class="with-border" required value="<?= $user['email'];?>">
												</div>
											</div>
											
											<div class="col-xl-6">
												<div class="submit-field">
													<h5>Telefon nömrəsi</h5>
													<input type="text" name="number" class="with-border" required value="<?= $user['number']; ?>">
												</div>
											</div>
											<div class="col-xl-6">
												<div class="submit-field">
													<h5>Lokasiya</h5>
													<input type="text" name="location" class="with-border" value="<?= $user['location'];?>">
												</div>
											</div>

										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
					<!-- Button -->
					<div class="col-xl-12">
						<button type="submit" name="userUpdate" class="button ripple-effect big margin-top-30">Dəyişiklikləri yadda saxla</a>
					</div>
				</form>
				<!-- Dashboard Box -->

				<div class="col-xl-12">
					<form action="../system/userUpdateController.php" method="POST">

						<div id="test1" class="dashboard-box">

							<!-- Headline -->
							<div class="headline">
								<h3><i class="icon-material-outline-lock"></i> Parol</h3>
							</div>

							<div class="content with-padding">
								<div class="row">
									<div class="col-xl-4">
										<div class="submit-field">
											<h5>Hazırki parolunuz</h5>
											<input type="password" required name="currentPass" class="with-border">
										</div>
									</div>

									<div class="col-xl-4">
										<div class="submit-field">
											<h5>Yeni parol</h5>
											<input type="password" required name="newPass" class="with-border">
										</div>
									</div>

									<div class="col-xl-4">
										<div class="submit-field">
											<h5>Yeni parol təkrarı</h5>
											<input type="password" required name="newPassRepeated" class="with-border">
										</div>
									</div>
								</div>
							</div>
						</div>
											
						<!-- Button -->
						<div class="col-xl-12">
							<button type="submit" name="passwordUpdate" class="button ripple-effect big margin-top-30">Dəyişiklikləri yadda saxla</a>
						</div>
					</form>

				</div>

			</div>
			<!-- Row / End -->

<?php include "dash_footer.php"; ?>