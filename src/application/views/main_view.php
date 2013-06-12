<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/common.css" type="text/css" media="all" />
		<title><?php echo $server; ?> E-shop</title>
	</head>
	<body bgcolor="white" onload="show_coupon()">
		<div class="container">
			<div class="navbar" style="width:635px;height:25px;">
				<div class="navbar-inner">
					<ul class="nav">
						<li><a href="<?php echo base_url(); ?>">ACP Home</a></li>
						<li id="home-menu" class="active"><a href="<?php echo base_url(); ?>index.php/eshop/">E-Shop Home</a></li>
						<li id="buy-menu" class="dropdown">
							<a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Buy Items</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
								<li><a tabindex="-1" href="#Memberships" id="mem-menu">Memberships</a></li>
								<li><a tabindex="-1" href="#MageItems" id="mi-menu">Mage Items</a></li>
								<li><a tabindex="-1" href="#Archeritems" id="ai-menu">Archer Items</a></li>
								<li><a tabindex="-1" href="#HKItems" id="hki-menu">Holy Knight Items</a></li>
								<li><a tabindex="-1" href="#WarriorItems" id="wi-menu">Warrior Items</a></li>
								<li><a tabindex="-1" href="#ExclusiveJewellery" id="ej-menu">Exclusive Jewellery</a></li>
								<li><a tabindex="-1" href="#Miscellaneous" id="mis-menu">Miscellaneous</a></li>
							</ul>
						</li>
						<li id="cart-menu"><a href="#MyCart" id="mc-menu">My Cart</a></li>
						<li id="hist-menu"><a href="#History" id="eh-menu">E-Shop History</a></li>
						<li id="logout-menu"><a href="<?php echo base_url(); ?>index.php/main/logout">Log Out</a></li>
					</ul>
				</div>
			</div>
			<table>
				<tr>
					<td><span class="label label-inverse">Account Name</span> : <span class="badge badge-inverse"><?php echo $user; ?></span></td><td>&nbsp;&nbsp;</td>
					<td><span class="label label-inverse">Character Name</span> : <span class="badge badge-inverse"><?php echo $char; ?></span></td><td>&nbsp;&nbsp;</td>
					<td><span class="label label-inverse">Credits</span> : <span id="credit-value" class="badge badge-inverse"><?php echo $credits; ?></span></td>
				</tr>
			</table>
			<div id='errordiv' class='alert' style='display:none'>
				<button type='button' class='close' onclick='$(this).parent().fadeOut("slow")'>x</button>
				<span id='error-msg'></span>
			</div><br>
			<div id="main-content" class="text-info">
				<p class="lead"><u>E-Shop Home</u></p>
				Welcome <?php echo $char; ?>, <br> Do you know that <strong>A3Flamez</strong> is giving flat 30% off on all credit purchases? Have fun!<br><br>
				<button id="show-time-btn" onclick="get_time();" class="btn btn-success" data-loading-text="Processing...">Time to Credits</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button onclick="$('#show-time').hide();$('#show-add-gold').hide();$('#show-add').show()" class="btn btn-success" data-loading-text="Processing...">Woonz to Credits</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button onclick="$('#show-time').hide();$('#show-add').hide();$('#show-add-gold').show()" class="btn btn-success" data-loading-text="Processing...">Gold to Credits</button><br><br>
				<div id="show-add" style="display:none">
					<strong>1bil = 10 credits</strong><br>
					<input type="text" id="wz" placeholder="Woonz in Bil"><br>
					<button id="wz-credits" onclick="convert_wz()" class="btn btn-danger" data-loading-text="Processing...">Convert</button>
				</div>
				<div id="show-add-gold" style="display:none">
					<strong>1bil = 10 credits</strong><br>
					<input type="text" id="slots" placeholder="No. of Slots"><br>
					<button id="gold-credits" onclick="convert_gold()" class="btn btn-danger" data-loading-text="Processing...">Convert</button>
				</div>
				<div id="show-time" style="display:none">
					<strong>1 hours = 1 credit</strong><br>
					<span id="dis-time"></span>
					<button id="time-credits" onclick="convert_time()" class="btn btn-danger" data-loading-text="Processing...">Convert</button>
				</div><br>
				<center><a href="<?php echo base_url(); ?>index.php/main/change_char" class="btn btn-info">Change Character</a></center>
				<div id="show-coupon" style="display:none">
				</div>
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
		<script type='text/javascript' src="<?php echo base_url(); ?>js/eshop.js"></script>
	</body>
</html>