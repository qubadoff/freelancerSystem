<?php
include "header.php";
require_once "system/regController.php";
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
					<h3 style="font-size: 26px;">Qeydiyyat forması</h3>
					<span>Artıq qeydiyyatınız var? <a href="login.php">Login edin!</a></span>
				</div>
				<?php 
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="notification error closeable">
						<p>' .$error_msg. '</p>
					</div>';
					}
                }
                
                if(isset($success))
                {
                    
                    echo '<div class="notification success closeable">
					<p>' .$success. '</p></div>';
                }
			?>


							<!-- Form -->
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="register-account-form">
				<!-- Account Type -->
				<div class="account-type">
					<div>
						<input type="radio" name="user_type" id="freelancer-radio" class="account-type-radio" value="1" checked/>
						<label for="freelancer-radio" class="ripple-effect-dark"><i class="icon-material-outline-account-circle"></i> Frilanser</label>
					</div>

					<div>
						<input type="radio" name="user_type" id="employer-radio" class="account-type-radio" value="2" />
						<label for="employer-radio" class="ripple-effect-dark"><i class="icon-material-outline-business-center"></i> İşəgötürən</label>
					</div>
				</div>
					<div class="input-with-icon-left">
						<i class="icon-feather-users"></i>
						<input type="text" class="input-text with-border>" name="first_name" id="emailaddress-register" placeholder="Ad" required/>
					</div>
					<div class="input-with-icon-left">
						<i class="icon-feather-users"></i>
						<input type="text" class="input-text with-border>" name="last_name" id="emailaddress-register" placeholder="Soyad" required/>
					</div>
					<div class="input-with-icon-left">
						<i class="icon-material-outline-email"></i>
						<input type="email" class="input-text with-border>" name="email" id="emailaddress-register" placeholder="Email" required/>
					</div>
					<div class="input-with-icon-left">
						<i class="icon-feather-phone"></i>
						<input type="text" class="input-text with-border>" name="number" id="emailaddress-register" placeholder="Telefon nömrəsi" required/>
					</div>
					<div class="input-with-icon-left" title="Should be at least 8 characters long" data-tippy-placement="bottom">
						<i class="icon-material-outline-lock"></i>
						<input type="password" class="input-text with-border" name="password" id="password-register" placeholder="Parol" required/>
					</div>
				<!-- Button -->
				<button type="submit" name="submit" class="button full-width button-sliding-icon ripple-effect margin-top-10"> Qeydiyyatdan keç <i class="icon-material-outline-arrow-right-alt"></i></button>
				</form>
			</div>

		</div>
	</div>
</div>


<!-- Spacer -->
<div class="margin-top-70"></div>
<!-- Spacer / End-->

<?php include "footer.php"; ?>