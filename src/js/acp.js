function show_loading() {
    $('#main-content').html('<center><p class="lead">Loading...</p></center>');
}

function init() {
	$('#check-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_loading();
		check_deals();
	});
	$('#post-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_loading();
		show_post_deal();
	});
	$('#cancel-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_loading();
		my_deals();
	});
	$('#wconvert-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_wconvert();
	});
	$('#gconvert-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_gconvert();
	});
	$('#cconvert-menu').click( function () {
		reset_active();
		$('#mart-menu').addClass('active');
		show_cconvert();
	});
	$('#new-gift-btn').click ( function () {
		new_gift();
	});
	$('#tp-btn').click ( function () {
		offline_tp();
	});
	$('#player-menu').click( function () {
		reset_active();
		$('#ser-menu').addClass('active');
		show_char_rb();
	});
	$('#merc-menu').click( function () {
		reset_active();
		$('#ser-menu').addClass('active');
		show_merc_rb();
	});
	$('#rb-gift-menu').click( function () {
		reset_active();
		$('#ser-menu').addClass('active');
		show_rb_gift();
	});
	$('#buy-lore-menu').click( function () {
		reset_active();
		$('#ser-menu').addClass('active');
		show_buy_lore();
	});
	$('#cp-menu').click( function () {
		reset_active();
		$('#ser-menu').addClass('active');
		show_change_pass();
	});
	$('#po-menu').click( function () {
		reset_active();
		$('#ext-menu').addClass('active');
		show_loading();
		show_po();
	});
	$('#pk-menu').click( function () {
		reset_active();
		$('#ext-menu').addClass('active');
		show_loading();
		show_pk();
	});
	$('#boh-menu').click( function () {
		reset_active();
		$('#ext-menu').addClass('active');
		show_loading();
		show_boh();
	});
	hashBasedAct();
}

function reset_active() {
	$("#acp-home-menu").removeClass("active");
	$("#change-menu").removeClass("active");
	$("#mart-menu").removeClass("active");
	$('#ext-menu').removeClass('active');
	$('#ser-menu').removeClass('active');
}

function hashBasedAct() {
	switch(window.location.hash) {
		case "#PlayerRebirth":
			$('#player-menu').trigger('click');
			break;
		case "#MercenaryRebirth":
			$('#merc-menu').trigger('click');
			break;
		case "#RBGift":
			$('#rb-gift-menu').trigger('click');
			break;
		case "#BuyLore":
			$('#buy-lore-menu').trigger('click');
			break;
		case "#ChangePassword":
			$('#cp-menu').trigger('click');
			break;
		case "#PlayersOnline":
			$('#po-menu').trigger('click');
			break;
		case "#PlayerKills":
			$('#pk-menu').trigger('click');
			break;
		case "#TopPlayers":
			$('#boh-menu').trigger('click');
			break;
		case "#CheckDeals":
			$('#check-menu').trigger('click');
			break;
		case "#PostDeal":
			$('#post-menu').trigger('click');
			break;
		case "#CancelDeals":
			$('#cancel-menu').trigger('click');
			break;
		case "#WoonzToCoins":
			$('#wconvert-menu').trigger('click');
			break;
		case "#GoldToCoins":
			$('#gconvert-menu').trigger('click');
			break;
		case "#CoinsToGold":
			$('#cconvert-menu').trigger('click');
			break;
		default:
			window.location.hash = "Home";
			break;
	}
}

function show_boh() {
	$.get(http_host + 'index.php/main/get_boh', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Board Of Heros</b></u></caption><tr><th>Name</th><th>Class</th><th>Level</th><th>Rebirth</th></tr>";
			for(var i = 0; i < obj.DATA.PLAYERS.length; i++) 
				content += "<tr><td><center>" + obj.DATA.PLAYERS[i] + "</center></td><td><center>" + obj.DATA.TYPES[i] + "</center></td><td><center>" + obj.DATA.LEVELS[i] + "</center></td><td><center>" + obj.DATA.RBS[i] + "</center></td></tr>";
			$('#main-content').html(content);
		}			
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center><p class="lead">'+obj['REASON']+'</p></center>');
		}
	});
}

function show_pk() {
	$.get(http_host + 'index.php/main/get_pk', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<center><p style='color: green' class='text-info lead'><u><b>Top 10 Player Kills</b></u></p></center>";
			for(var i = 0; i < obj.DATA.KILLER.length; i++) {
				if(obj.DATA.KILLER_TOWN[i]=="T")
					obj.DATA.KILLER_TOWN[i] = " of <font color='red'>Temoz</font> <font color='white'>Killed </font>" + obj.DATA.KILLED[i];
				else
					obj.DATA.KILLER_TOWN[i] = " of <font color='skyblue'>Quanato</font> <font color='white'>Killed </font>" + obj.DATA.KILLED[i];
				
				if(obj.DATA.KILLED_TOWN[i]=="T")
					obj.DATA.KILLED_TOWN[i] = " of <font color='red'>Temoz</font> <font color='white'>in </font>" + obj.DATA.LOCATION[i];
				else
					obj.DATA.KILLED_TOWN[i] = " of <font color='skyblue'>Quanato</font> <font color='white'>in </font>" + obj.DATA.LOCATION[i];
				content += "<center style='color: green'>"+ obj.DATA.KILLER[i] + '(' + obj.DATA.KILLER_RB[i] + ',' + obj.DATA.KILLER_LVL[i] + ')' + obj.DATA.KILLER_TOWN[i] +'(' + obj.DATA.KILLED_RB[i] + ',' + obj.DATA.KILLED_LVL[i] + ')' + obj.DATA.KILLED_TOWN[i] + "</center>";
			}
			$('#main-content').html(content);
		}			
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center><p class="lead">'+obj['REASON']+'</p></center>');
		}
	});
}

function show_po() {
	$.get(http_host + 'index.php/main/get_online_players', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Players Online</b></u></caption><tr><th>Name</th><th>Class</th><th>Level</th><th>Rebirth</th><th>Town</th></tr>";
			for(var i = 0; i < obj.DATA.PLAYERS.length; i++) {
				if(obj.DATA.TOWNS[i]=="Temoz")
					content += "<tr><td><center>" + obj.DATA.PLAYERS[i] + "</center></td><td><center>" + obj.DATA.TYPES[i] + "</center></td><td><center>" + obj.DATA.LEVELS[i] + "</center></td><td><center>" + obj.DATA.RBS[i] + "</center></td><td><center style='color: red'>" + obj.DATA.TOWNS[i] + "</center></td></tr>";
				else
					content += "<tr><td><center>" + obj.DATA.PLAYERS[i] + "</center></td><td><center>" + obj.DATA.TYPES[i] + "</center></td><td><center>" + obj.DATA.LEVELS[i] + "</center></td><td><center>" + obj.DATA.RBS[i] + "</center></td><td><center style='color: green'>" + obj.DATA.TOWNS[i] + "</center></td></tr>";
			}
			$('#main-content').html(content);
		}			
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center><p class="lead">'+obj['REASON']+'</p></center>');
		}
	});
}

function show_char_rb() {
	var content = '<div><u><strong>Player Rebirth</strong></u><br>Please keep your inventry clear else it will be wiped out!!!<br><br><center><button id="char-rb-btn" class="btn btn-danger" data-loading-text="Processing...">Take Rebirth</button></center></div>'
	$('#main-content').html(content);
	$('#char-rb-btn').click ( function () {
		do_char_rb();
	});
}

function show_merc_rb() {
	var content = '<div><strong>Mercenary Rebirth</strong><br><input type="text" id="merc-name" placeholder="Mercenary Name"><br><button id="merc-rb-btn" onclick="do_merc_rb()" class="btn btn-danger" data-loading-text="Processing...">Merc RB</button></div>'
	$('#main-content').html(content);
}

function show_rb_gift() {
	var content = '<div><strong>Rebirth Gift</strong><br><input type="text" id="char-rb-gift" placeholder="Rebirth"><br><button id="char-rb-gift-btn" class="btn btn-danger" data-loading-text="Processing...">Take RB Gift</button></div>'
	$('#main-content').html(content);
	$('#char-rb-gift-btn').click ( function () {
		do_gift_rb();
	});
}

function show_buy_lore() {
	var content = '<div><strong><u>Buy Lore</u></strong><br><br><b style="color:red">For 150mil you get 1mil lore</b><br><button id="char-buy-lore-btn" class="btn btn-danger" data-loading-text="Processing...">Buy Lore</button></div>'
	$('#main-content').html(content);
	$('#char-buy-lore-btn').click ( function () {
		do_buy_lore();
	});
}

function show_change_pass() {
	var content = '<div><strong>Change Password</strong><br><input type="password" id="opasswd" placeholder="Old Password"><br><input type="password" id="npasswd" placeholder="New Password"><br><input type="password" id="rpasswd" placeholder="Repeat New Password"><br><button id="pass-btn" onclick="change_pass()" class="btn btn-danger" data-loading-text="Processing...">Change Password</button></div>'
	$('#main-content').html(content);
}

function show_wconvert() {
	var content = '<div><strong>1bil = 1 coin (100mil = 0.1 coins)</strong><br><input type="text" id="wz" placeholder="Woonz"><br><button id="wz-coins" onclick="wconvert()" class="btn btn-danger" data-loading-text="Processing...">Convert</button></div>';
	$('#main-content').html(content);
}

function show_gconvert() {
	var content = '<div><strong>1bil = 1 coin</strong><br><input type="text" id="slots" placeholder="No. of Slots"><br><button id="gold-coins" onclick="gconvert()" class="btn btn-danger" data-loading-text="Processing...">Convert</button></div>';
	$('#main-content').html(content);
}

function show_cconvert() {
	var content = '<div><strong>1coin = 1bil</strong><br><input type="text" id="quantity" placeholder="Quantity"><br><button id="cquantity" onclick="cconvert()" class="btn btn-danger" data-loading-text="Processing...">Convert</button></div>';
	$('#main-content').html(content);
}

function wconvert() {
	var wz = $.trim($('#wz').val());
	wz = wz.replace(/[^0-9]/g,'');
	if(wz == '')
		show_failure("Please enter woonz value!");
	else {
		$('#wz-coins').button('loading');
		$.get(http_host + 'index.php/acp_utils/wz_to_coins/' + wz, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				$('#coin-value').text(obj['TOTAL']);
				show_success('Flamez coins were added to your account!');
			}
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#wz-coins').button('reset');
			$('#wz').val('');
		});
	}
}

function cconvert() {
	var quantity = $.trim($('#quantity').val());
	quantity = quantity.replace(/[^0-9]/g,'');
	if(quantity == '')
		show_failure("Please enter quantity value!");
	else {
		$('#cquantity').button('loading');
		$.get(http_host + 'index.php/acp_utils/coins_to_gold/' + quantity, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				$('#coin-value').text(obj['TOTAL']);
				show_success('Flamez coins were removed from your account!');
			}
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#cquantity').button('reset');
			$('#quantity').val('');
		});
	}
}

function gconvert() {
	var slots = $.trim($('#slots').val());
	slots = slots.replace(/[^0-9]/g,'');
	if(slots == '')
		show_failure("Please enter slots value!");
	else {
		$('#gold-coins').button('loading');
		$.get(http_host + 'index.php/acp_utils/gold_to_coins/' + slots, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				$('#coin-value').text(obj['TOTAL']);
				show_success('Flamez coins were added to your account!');
			}
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#gold-coins').button('reset');
			$('#slots').val('');
		});
	}
}

function do_char_rb() {
	$('#char-rb-btn').button('loading');
	$.get(http_host + 'index.php/acp_utils/char_rb', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')			
			show_success('Character rebirth successfull!');
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#char-rb-btn').button('reset');
	});
}

function do_merc_rb() {
	$('#merc-rb-btn').button('loading');
	var merc = $.trim($('#merc-name').val());
	merc = merc.replace(/[^A-Za-z0-9]/g,'');
	if(merc == '')
		show_failure("Please enter a merc name!");
	else {
		$.get(http_host + 'index.php/acp_utils/merc_rb/' + merc, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS')			
				show_success('Mercenary rebirth successfull!');
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
		});
	}
	$('#merc-name').val('');
	$('#merc-rb-btn').button('reset');
}

function do_gift_rb() {
	$('#char-rb-gift-btn').button('loading');
	var rb = $.trim($('#char-rb-gift').val());
	rb = rb.replace(/[^0-9]/g,'');
	if(rb == '')
		show_failure("Please enter some rebirth value!");
	else {
		$.get(http_host + 'index.php/acp_utils/rb_gift/' +rb , function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS')			
				show_success('Rebirth Gift successfully acquired!');
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
		});
	}
	$('#char-rb-gift').val('');
	$('#char-rb-gift-btn').button('reset');
}

function do_buy_lore() {
	$('#char-buy-lore-btn').button('loading');
	$.get(http_host + 'index.php/acp_utils/buy_lore', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')			
			show_success('Lore purchased successfull!');
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#char-buy-lore-btn').button('reset');
	});
}

function new_gift() {
	$('#new-gift-btn').button('loading');
	$.get(http_host + 'index.php/acp_utils/new_gift', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')			
			show_success('Gift delivered successfully!');
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#new-gift-btn').button('reset');
	});
}

function offline_tp() {
	$('#tp-btn').button('loading');
	$.get(http_host + 'index.php/acp_utils/offline_tp', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')			
			show_success('Your character was returned to town!');
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#tp-btn').button('reset');
	});
}

function change_pass() {
	var opasswd = $.trim($('#opasswd').val());
	var rpasswd = $.trim($('#rpasswd').val());
	var npasswd = $.trim($('#npasswd').val());
	opasswd = opasswd.replace(/[^A-Za-z0-9]/g,'');
	if(opasswd == '' || rpasswd == '' || npasswd == '')
		show_failure('Please enter all details!');
	else if(npasswd != rpasswd)
		show_failure('New passwords should match!');
	else {
		npasswd = npasswd.replace(/[^A-Za-z0-9]/g,'');
		if(npasswd != rpasswd)
			show_failure('Password can only have alphanumeric characters!');
		else {
			$('#pass-btn').button('loading');
			$.get(http_host + 'index.php/acp_utils/change_pass/'+ encode64(opasswd) +'/' + encode64(npasswd), function (data) {
				var obj = JSON.parse(data);
				if(obj['RESULT'] == 'SUCCESS')			
					show_success('Your password has been changed!');
				else if(obj['REASON'] == 'Not logged in or not selected character')
					document.location.reload(true);
				else
					show_failure(obj['REASON']);
				$('#pass-btn').button('reset');
			});
		}
	}
}

function check_deals() {
	$.get(http_host + 'index.php/acp_utils/check_deals', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Flamez Mart Deals</b></u></caption><tr><th>Deal ID</th><th>Item Name</th><th>Seller Name</th><th>Required Coins</th><th>Action</th></tr>";
			for(var i = 0; i < obj.DATA.length; i++)
				content += "<tr><td>" + obj.DATA[i].deal_id + "</td><td>" + obj.DATA[i].item_name + "</td><td>" + obj.DATA[i].char_name + "</td><td>" + obj.DATA[i].flamez_coins + "</td><td><center><button class='btn btn-success' onclick='buy_from_mart(\"" + obj.DATA[i].deal_id + "\", this)'>Buy Item</button></center></td></tr>";
			$('#main-content').html(content);
		}			
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center><p class="lead">'+obj['REASON']+'</p></center>');
		}
	});
}

function my_deals() {
	$.get(http_host + 'index.php/acp_utils/my_deals', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Flamez Mart Deals</b></u></caption><tr><th>Deal ID</th><th>Item Name</th><th>Seller Name</th><th>Required Coins</th><th>Action</th></tr>";
			for(var i = 0; i < obj.DATA.length; i++)
				content += "<tr><td>" + obj.DATA[i].deal_id + "</td><td>" + obj.DATA[i].item_name + "</td><td>" + obj.DATA[i].char_name + "</td><td>" + obj.DATA[i].flamez_coins + "</td><td><center><button class='btn btn-success' onclick='cancel_deal(\"" + obj.DATA[i].deal_id + "\", this)'>Cancel Deal</button></center></td></tr>";
			$('#main-content').html(content);
		}			
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center><p class="lead">'+obj['REASON']+'</p></center>');
		}
	});
}

function buy_from_mart(deal_id, me) {
	$(me).button('loading');
	deal_id = deal_id.replace(/[^0-9]/g,'');
	$.get(http_host + 'index.php/acp_utils/buy_from_mart/' + deal_id, function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')	{
			$(me).parent().parent().parent().remove();
			$('#coin-value').text(obj['FCOINS']);
			show_success('Item bought successfully!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$(me).button('reset');
	});
}

function cancel_deal(deal_id, me) {
	$(me).button('loading');
	deal_id = deal_id.replace(/[^0-9]/g,'');
	$.get(http_host + 'index.php/acp_utils/cancel_deal/' + deal_id, function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS')	{
			$(me).parent().parent().parent().remove();
			show_success('Item bought successfully!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$(me).button('reset');
	});
}

function show_post_deal() {
	var content = '<div><strong>Post Deal</strong><br><input type="text" id="iname" placeholder="Item Name with eles"><br><input type="text" id="fcoins" placeholder="Flamez Coins"><br><button id="post-btn" onclick="post_deal()" class="btn btn-danger" data-loading-text="Processing...">Post Deal</button></div>'
	$('#main-content').html(content);
}

function post_deal() {
	$('#post-btn').button('loading');
	var name = $.trim($('#iname').val());
	var fcoins = $.trim($('#fcoins').val());
	name = encode64(name);
	fcoins = fcoins.replace(/[^0-9]/g,'');
	if(name == '' || fcoins == '')
		show_failure('Please enter all details!');
	else{
		$.get(http_host + 'index.php/acp_utils/post_deal/' + name + '/' + fcoins, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS')
				show_success('Deal was posted successfully!');
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#post-btn').button('reset');
		});
	}
}

function show_success(str) {
    $('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-success');
    $('#error-msg').html(str);
    $('#errordiv').fadeIn("slow");
}

function show_failure(str) {
    $('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-error');
    $('#error-msg').html(str);
    $('#errordiv').fadeIn("slow");
}

init();