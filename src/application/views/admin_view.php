<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/common.css" type="text/css" media="all" />
		<title><?php echo $server; ?> E-shop Admin Panel</title>
	</head>
	<body bgcolor="white">
		<div class="container">
			<div class="navbar" style="width:635px;height:25px;">
				<div class="navbar-inner">
					<ul class="nav">
						<li><a href="http://acp.a3flamez.com">ACP Home</a></li>
						<li id="home-menu" class="active"><a href="<?php echo base_url(); ?>">E-Shop Home</a></li>
						<li id="credits-menu"><a href="#" onclick="display_menu('credits')">Add Credits</a></li>
						<li id="items-menu"><a href="#" onclick="display_menu('items')">Add Items</a></li>
						<li id="logout-menu"><a href="<?php echo base_url(); ?>/index.php/main/logout">Log Out</a></li>
					</ul>
				</div>
			</div>
			<table>
				<tr>
					<td><span class="label label-inverse">Account Name</span> : <span class="badge badge-inverse"><?php echo $user; ?></span></td><td>&nbsp;&nbsp;</td>
					<td><span class="label label-inverse">Character Name</span> : <span class="badge badge-inverse"><?php echo $char; ?></span></td><td>&nbsp;&nbsp;</td>
				</tr>
			</table>
			<div id='errordiv' class='alert' style='display:none'>
				<button type='button' class='close' onclick='$(this).parent().fadeOut("slow")'>x</button>
				<span id='error-msg'></span>
			</div><br>
			<div id="main-content" class="text-info">
				<p class="lead"><u>Admin Panel</u></p>
				Use it and enjoy!
			</div>
		</div>
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:300px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top:-3px;">×</button>
				<h3 id="myModalLabel" style="line-height:10px;font-size:17px;color:black;">A3Flamez</h3>
			</div>
			<div id ="myModalBody" class="modal-body" style="text-align:center;color:black;">
				Welcome to A3Flamez
			</div>
			<div class="modal-footer" style="padding:6px 117px 7px;">
				<button class="btn btn-inverse btn-small" data-dismiss="modal" aria-hidden="true">Close</button>
			</div>
		</div>
		<div id="myLoader" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="background-color:transparent;border:0px;text-align:center;-webkit-box-shadow:0 0 0;-moz-box-shadow:0 0 0;box-shadow:0 0 0;">
			<img src="<?php echo base_url(); ?>img/ajax-loader.gif" style="width:35px;">
		</div>
		<script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script type='text/javascript' src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
		<script type='text/javascript' src="<?php echo base_url(); ?>js/config.js"></script>
		<script type='text/javascript' src="<?php echo base_url(); ?>js/common.js"></script>
		<script type='text/javascript' src="<?php echo base_url(); ?>js/admin.js"></script>
	</body>
</html>