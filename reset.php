<?php
include "header.php";
require_once "system/loginController.php";
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
					<h3>Reset your password !</h3>
					<span>have an account? <a href="login.php">Login</a></span>
				</div>
				<!-- Form -->
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form">
					<div class="input-with-icon-left">
						<i class="icon-material-outline-email"></i>
						<input type="email" class="input-text with-border" name="email" id="username" placeholder="Email" required/>
					</div>
				<!-- Button -->
				<button type="submit" class="button full-width button-sliding-icon ripple-effect margin-top-10" form="login-form"> Reset <i class="icon-material-outline-arrow-right-alt"></i></button>
				</form>
			</div>

		</div>
	</div>
</div>


<!-- Spacer -->
<div class="margin-top-70"></div>
<!-- Spacer / End-->

<?php include "footer.php"; ?>