<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sign in &middot; <?php echo $server; ?> ACP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="icon" type="image/ico" href="<?php echo base_url(); ?>img/favicon.ico"></link> 
	<link rel="shortcut icon" href="<?php echo base_url(); ?>img/favicon.ico"></link>
    <link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>css/login.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
	  <?php if(!isset($chars)) {?>
      <form class="form-signin" method="POST" id="login-form" onsubmit="return login();return false;">
		<div id='errordiv' class='alert' style='display:none'>
			<button type='button' class='close' onclick='$(this).parent().fadeOut("slow")'>x</button>
			<span id='error-msg'></span>
		</div>
        <h2 class="form-signin-heading">ACP Sign In</h2>
        <input type="text" class="input-block-level" placeholder="Username" name="username" id="username">
        <input type="password" class="input-block-level" placeholder="Password" name="password" id="password">
        <button class="btn btn-medium btn-primary" type="submit">Sign in</button>
        <button class="btn btn-medium btn-inverse" style="float:right;" type="reset" onclick="window.location = '<?php echo base_url(); ?>index.php/main/forgot_pass'">Forgot Password</button><br>
        <br><center><button class="btn btn-medium btn-success" type="reset" onclick="window.location = '<?php echo base_url(); ?>index.php/main/create'">Create Account</button></center>
      </form>
	  <?php } else { ?>
	  <form class="form-signin" method="POST" id="login-form" onsubmit="return select_char();return false;">
		<h2 class="form-signin-heading">Select Character</h2>
		<div id='errordiv' class='alert' style='display:none'>
			<button type='button' class='close' onclick='$(this).parent().fadeOut("slow")'>x</button>
			<span id='error-msg'></span>
		</div>
		<center>
			<select id="character">
				<?php
				for($i = 0; $i < count($chars); $i++)
					echo "<option value='".$chars[$i]."'>".$chars[$i]."</option>";
				?>
			</select><br>
			<button class="btn btn-medium btn-primary" type="submit">Select</button>
		</center>
      </form>
	  <?php } ?>
    </div> <!-- /container -->
	<div id="footer">
      <div class="container">
        <center><p class="muted credit">&copy; <?php echo $server; ?></p></center>
      </div>
    </div>
	<script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
	<script type='text/javascript' src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script type='text/javascript' src="<?php echo base_url(); ?>js/config.js"></script>
	<script type='text/javascript' src="<?php echo base_url(); ?>js/common.js"></script>
	<script type='text/javascript' src="<?php echo base_url(); ?>js/login.js"></script>
	<script>
	$(document).ready(function(){
		if(!Modernizr.input.placeholder){
			$('[placeholder]').focus(function() {
			  var input = $(this);
			  if (input.val() == input.attr('placeholder')) {
				input.val('');
				input.removeClass('placeholder');
			  }
			}).blur(function() {
			  var input = $(this);
			  if (input.val() == '' || input.val() == input.attr('placeholder')) {
				input.addClass('placeholder');
				input.val(input.attr('placeholder'));
			  }
			}).blur();
			$('[placeholder]').parents('form').submit(function() {
			  $(this).find('[placeholder]').each(function() {
				var input = $(this);
				if (input.val() == input.attr('placeholder')) {
				  input.val('');
				}
			  })
			});
		}
	});
	</script>
  </body>
</html>