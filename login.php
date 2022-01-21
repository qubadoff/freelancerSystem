<?php
require "header.php";
require_once "system/loginController.php";

if(isset($_SESSION['id'])){
	header("Location:dashboard/index.php");
}
?>
<!-- Titlebar
================================================== -->
<div id="titlebar" class="gradient">
	<div class="container">
		<div class="row">
		</div>
	</div>
</div>


<!-- Page Content
================================================== -->
<div class="container">
	<div class="row">
		<div class="col-xl-5 offset-xl-3">


			<div class="login-register-page">
				<!-- Welcome Text -->
				<div class="welcome-text">
					<h3>Login forması</h3>
					<span>Hesabınız yoxdur? <a href="register.php">Qeydiyyatdan keçin!</a></span>
				</div>
				<?php 
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="notification error closeable"><p>' .$error_msg. '</p></div>';
					}
				}
			?>
				<!-- Form -->
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form">
					<div class="input-with-icon-left">
						<i class="icon-material-outline-email"></i>
						<input type="email" class="input-text with-border" name="email" id="username" placeholder="Email" required/>
					</div>
					<div class="input-with-icon-left">
						<i class="icon-material-outline-lock"></i>
						<input type="password" class="input-text with-border>" name="password" id="password" placeholder="Parol" required/>
					</div>
					<!-- <a href="reset.php" class="forgot-password">Forgot Password?</a> -->
			
				
				<!-- Button -->
				<button type="submit" name="submit" class="button full-width button-sliding-icon ripple-effect margin-top-10" form="login-form"> Login <i class="icon-material-outline-arrow-right-alt"></i></button>
				</form>
			</div>

		</div>
	</div>
</div>


<!-- Spacer -->
<div class="margin-top-70"></div>
<!-- Spacer / End-->

<?php include "footer.php"; ?>