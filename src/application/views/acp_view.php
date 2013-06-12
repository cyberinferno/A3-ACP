<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/common.css" type="text/css" media="all" />
		<title><?php echo $server; ?> ACP</title>
	</head>
	<body bgcolor="white">
		<div class="container">
			<div class="navbar" style="width:635px;height:25px;">
				<div class="navbar-inner">
					<ul class="nav">
						<li id="acp-home-menu" class="active"><a href="<?php echo base_url(); ?>">Home</a></li>
						<li id="ser-menu" class="dropdown">
							<a id="drop" role="button" class="dropdown-toggle" data-toggle="dropdown" href="#">Services</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="drop">
								<li><a tabindex="-1" href="#PlayerRebirth" id="player-menu">Player Rebirth</a></li>
								<li><a tabindex="-1" href="#MercenaryRebirth" id="merc-menu">Mercenary Rebirth</a></li>
								<li><a tabindex="-1" href="#RBGift" id="rb-gift-menu">Get RB Gift</a></li>
								<li><a tabindex="-1" href="#BuyLore" id="buy-lore-menu">Buy Lore</a></li>
								<li><a tabindex="-1" href="#ChangePassword" id="cp-menu">Change Password</a></li>
								<li class="divider"></li>
								<li><a tabindex="-1" href="<?php echo base_url(); ?>index.php/main/change_char">Change Character</a></li>
							</ul>
						</li>
						<li id="ext-menu" class="dropdown">
							<a id="drop" role="button" class="dropdown-toggle" data-toggle="dropdown" href="#">Extras</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="drop">
								<li><a tabindex="-1" href="#PlayersOnline" id="po-menu">Players Online</a></li>
								<li><a tabindex="-1" href="#PlayerKills" id="pk-menu">Player Kills</a></li>
								<li><a tabindex="-1" href="#TopPlayers" id="boh-menu">Top Players</a></li>								
							</ul>
						</li>
						<li id="mart-menu" class="dropdown">
							<a id="drop1" role="button" class="dropdown-toggle" data-toggle="dropdown" href="#">Flamez Mart</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
								<li><a tabindex="-1" href="#CheckDeals" id="check-menu">Check Deals</a></li>
								<li><a tabindex="-1" href="#PostDeal" id="post-menu">Post Deal</a></li>
								<li><a tabindex="-1" href="#CancelDeals" id="cancel-menu">Cancel Deals</a></li>
								<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Convert Currency</a>
									<ul class="dropdown-menu">
    									<li><a tabindex="-1" href="#WoonzToCoins" id="wconvert-menu">Woonz to Coins</a></li>
    									<li><a tabindex="-1" href="#GoldToCoins" id="gconvert-menu">Gold to Coins</a></li>
    									<li><a tabindex="-1" href="#CoinsToGold" id="cconvert-menu">Coins to Gold</a></li>
   									</ul>
								</li>							
							</ul>
						</li>
						<li id="eshop-menu"><a href="<?php echo base_url(); ?>index.php/eshop">E-Shop Home</a></li>
						<li id="logout-menu"><a href="<?php echo base_url(); ?>index.php/main/logout">Log Out</a></li>
					</ul>
				</div>
			</div>
			<table>
				<tr>
					<td><span class="label label-inverse">Account Name</span> : <span class="badge badge-inverse"><?php echo $user; ?></span></td><td>&nbsp;&nbsp;</td>
					<td><span class="label label-inverse">Character Name</span> : <span class="badge badge-inverse"><?php echo $char; ?></span></td><td>&nbsp;&nbsp;</td>
					<td><span class="label label-inverse">Flamez Coins</span> : <span id="coin-value" class="badge badge-inverse"><?php echo $fcoins; ?></span></td>
				</tr>
			</table>
			<div id='errordiv' class='alert' style='display:none'>
				<button type='button' class='close' onclick='$(this).parent().fadeOut("slow")'>x</button>
				<span id='error-msg'></span>
			</div><br>
			<div id="main-content" class="text-info">
				<p class="lead"><u>Acp Home</u></p>
				Welcome <?php echo $char; ?>, <br> 
				Please keep your character <strong>OFFLINE</strong> before doing any operation!<br><br>
				<button id="tp-btn" class="btn btn-success" data-loading-text="Processing...">Offline Teleport</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button id="new-gift-btn" class="btn btn-success" data-loading-text="Processing...">Beginner's Gift</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
		<script type='text/javascript' src="<?php echo base_url(); ?>js/acp.js"></script>
	</body>
</html>