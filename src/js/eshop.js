var coup_list = '';
function init() {
	$('#mem-menu').click(function () {
		display_menu('mem');
	});
	$('#mi-menu').click(function () {
		display_menu('mage');
	});
	$('#ai-menu').click(function () {
		display_menu('archer');
	});
	$('#hki-menu').click(function () {
		display_menu('holyknight');
	});
	$('#wi-menu').click(function () {
		display_menu('warrior');
	});
	$('#ej-menu').click(function () {
		display_menu('jewellery');
	});
	$('#mis-menu').click(function () {
		display_menu('mis');
	});
	$('#mc-menu').click(function () {
		display_menu('cart');
	});
	$('#eh-menu').click(function () {
		display_menu('hist');
	});
	hashBasedAct();
}

function hashBasedAct() {
	switch(window.location.hash) {
		case "#Memberships":
			$('#mem-menu').trigger('click');
			break;
		case "#MageItems":
			$('#mi-menu').trigger('click');
			break;
		case "#ArcherItems":
			$('#ai-menu').trigger('click');
			break;
		case "#HKItems":
			$('#hki-menu').trigger('click');
			break;
		case "#WarriorItems":
			$('#wi-menu').trigger('click');
			break;
		case "#ExclusiveJewellery":
			$('#ej-menu').trigger('click');
			break;
		case "#Miscellaneous":
			$('#mis-menu').trigger('click');
			break;
		case "#MyCart":
			$('#mc-menu').trigger('click');
			break;
		case "#History":
			$('#eh-menu').trigger('click');
			break;
		default:
			window.location.hash = "EshopHome";
			break;
	}
}

function display_menu(type) {
	show_loading();
	switch(type) {
		case "home":
			make_active("home-menu");
			display_home();
			break;
		case "cart":
			make_active("cart-menu");
			display_cart();
			break;
		case "mem":			
		case "mage":			
		case "holyknight":			
		case "archer":			
		case "warrior":			
		case "mis":			
		case "jewellery":
			make_active("buy-menu");
			display_buy(type);
			break;
		case "hist":
			make_active("hist-menu");
			display_history();
			break;
		default:
			make_active("home-menu");
			display_home();
			break;
	}
}

function display_buy(type) {
	var content = '';
	var heading = display_heading(type);
	var item_id, item_pic, item_name, buy_credits;
	switch(type) {
		case "mem":
			display_membership();			
			break;
		case "mage":			
		case "holyknight":			
		case "archer":			
		case "warrior":					
		case "jewellery":
		case "mis":
			$.get(http_host + 'index.php/utils/get_category/' + encode64(type), function (data) {
				var obj = JSON.parse(data);
				if(obj['RESULT'] == 'SUCCESS') {
					var content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>" + heading + "</b></u></caption><tr>";
					for(var i = 0; i < obj['DATA'].length; i++) {
						item_id = obj['DATA'][i]['item_id'];
						item_pic = img_loc + "items/" + obj['DATA'][i]['item_pic'];
						item_name = obj['DATA'][i]['item_name'];
						buy_credits = obj['DATA'][i]['buy_credits'];
						if(i != 0 && i%3 == 0)
							content = content + "</tr><tr>";
						content = content + "<td align='center' width='150'><br><table width='100%'><tr><td width='60%' align='center'><img src='" + item_pic + "' width='100' height='100'><br>" + item_name + "</td><td width='40%' align='center'>Credits : " + buy_credits + "<br><br><input type='text' value='1' id='" + item_id + "-quantity' style='width:30px;text-align:center;'><br><button data-loading-text='Processing...' class='btn btn-small btn-danger' id='" + item_id + "' onclick='add_to_cart(this.id, \""+ item_name + "\")' >Add to Cart</button></td></tr></table><br></td>";
					}
					$('#main-content').html(content);
				}
				else if(obj['REASON'] == 'Not logged in or not selected character')
					document.location.reload(true);
				else
					show_failure(obj['REASON']);
			});
			break;
		default:
			$('#main-content').html("No items found!");
			break;
	}
}

function display_home() {
	var content = '<p class="lead"><u>E-Shop Home</u></p>Welcome player, <br> Do you know that <strong>Server</strong> is giving flat 30% off on all credit purchases? Have fun!<br><br><a href="/" class="btn btn-info">Go Back to ACP</a>';
	$('#main-content').html( content);
}

function display_membership() {
	var content = "<p class='lead'><u>A3 server Memberships</u></p> Under construction!";
	$('#main-content').html( content);
}

function display_history() {
	var content = "<p class='lead'><u>E-shop history</u></p>";
	content += "<div class='accordion' id='trans'>";
	$.get(http_host + 'index.php/utils/get_history', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			for(var i = 0; i < obj.DATA.length; i++) {
				content += "<div class='accordion-group'><div class='accordion-heading'><a id = 'a-" + i + "' class='accordion-toggle collapsed' data-toggle='collapse' data-parent='#trans' href='#collapse" + i + "'><table border='0' width='100%'><tr><td>Transcation ID : " + obj.DATA[i]['transaction_id'] + "</td><td>Time : " + obj.DATA[i]['delivery_time'] + "</td><td>Total Credits Used : " + obj.DATA[i]['credits_used'] + "</td></tr></table></a></div>";
				content += '<div id="collapse' + i + '" class="accordion-body collapse in" style="height: auto;"><div class="accordion-inner"><center><table border="1">';
				for(var j = 0; j < obj.DATA[i]['items'].length; j++)
					content += "<tr><td width='50%' align='center'>" + obj.DATA[i]['items'][j]['item_name'] + "</td><td width='50%' align='center'>" + obj.DATA[i]['items'][j]['item_quantity'] + "</td></tr>";
				content += "</table></center></div></div></div>";
			}
			$('#main-content').html(content);
			for(var i = 0; i < obj.DATA.length; i++)
				$('#a-' + i).trigger('click');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			$('#main-content').html('<center>'+obj['REASON']+'</center>');
		}
	});
}

function display_error() {
	var content = "<p class='lead'><u>Page not found</u></p> The page you are looking for is not found!";
	$('#main-content').html( content);
}

function display_cart() {
	$.get(http_host + 'index.php/utils/get_cart', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {			
			if(obj['DATA']['coupon'] == 'NIL' ) {
				if(coup_list != 'NIL')
					content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Shopping Cart</b></u><br><span style='float: left;margin-top: 11px;'>Total credits required : " + obj['DATA']['credits_required'] + "</span><span style='float:right;'>"+coup_list+"&nbsp;&nbsp;&nbsp;<button id='coupon-add-btn' class='btn btn-medium btn-info' data-loading-text='Processing...' onclick='apply_coupon()'>Apply Coupon</button>&nbsp;&nbsp;&nbsp;<button id='clear-btn' class='btn btn-medium btn-warning' data-loading-text='Processing...' onclick='clear_cart()' >Clear Cart</button>&nbsp;&nbsp;&nbsp;<button id='checkout-btn' class='btn btn-medium btn-success' data-loading-text='Processing...' onclick='checkout()' >Checkout</button></span></caption><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total Credits</th><th>Action</th></tr>";
				else
					content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Shopping Cart</b></u><br><span style='float: left;margin-top: 11px;'>Total credits required : " + obj['DATA']['credits_required'] + "</span><span style='float:right;'><button id='clear-btn' class='btn btn-medium btn-warning' data-loading-text='Processing...' onclick='clear_cart()' >Clear Cart</button>&nbsp;&nbsp;&nbsp;<button id='checkout-btn' class='btn btn-medium btn-success' data-loading-text='Processing...' onclick='checkout()' >Checkout</button></span></caption><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total Credits</th><th>Action</th></tr>";
				for(var i = 0; i < obj['DATA']['items'].length; i++) {
					item_id = obj['DATA']['items'][i]['item_id'];
					item_pic = img_loc + "items/" + obj['DATA']['items'][i]['item_pic'];
					item_name = obj['DATA']['items'][i]['item_name'];
					buy_credits = obj['DATA']['items'][i]['buy_credits'];
					quantity = obj['DATA']['items'][i]['quantity'];
					total_credits = parseInt(obj['DATA']['items'][i]['buy_credits'])*parseInt(obj['DATA']['items'][i]['quantity']);
					content = content + "<tr><td align='center'><img src='" + item_pic + "' width='100' height='100'><br>" + item_name + "</td><td align='center'>" + quantity + "</td><td align='center'>" + buy_credits + "</td><td align='center'>" + total_credits.toString() + "</td><td align='center'><button class='btn btn-small btn-danger' data-loading-text='Processing...' onclick='activate_remove(\"" + item_id + "\", \"" + item_name + "\", \"" + quantity + "\")' >Remove/Edit</button></td></tr>";
				}
				content = content + "</table>";
			}
			else {
				content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Shopping Cart</b></u><br><span style='float: left;margin-top: 11px;'>Total credits required : " + obj['DATA']['credits_required'] + "</span><span style='float:right;'><button data-original-title='Coupon used : "+obj['DATA']['coupon']+"' onmouseover='$(this).tooltip(\"show\")' onmouseout='$(this).tooltip(\"hide\")' id='coupon-remove-btn' class='btn btn-medium btn-info' data-loading-text='Processing...' onclick='remove_coupon()'>Remove Coupon</button>&nbsp;&nbsp;&nbsp;<button id='clear-btn' class='btn btn-medium btn-warning' data-loading-text='Processing...' onclick='clear_cart()' >Clear Cart</button>&nbsp;&nbsp;&nbsp;<button id='checkout-btn' class='btn btn-medium btn-success' data-loading-text='Processing...' onclick='checkout()' >Checkout</button></span></caption><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total Credits</th><th>Action</th></tr>";
				for(var i = 0; i < obj['DATA']['items'].length; i++) {
					item_id = obj['DATA']['items'][i]['item_id'];
					item_pic = img_loc + "items/" + obj['DATA']['items'][i]['item_pic'];
					item_name = obj['DATA']['items'][i]['item_name'];
					buy_credits = obj['DATA']['items'][i]['buy_credits'];
					quantity = obj['DATA']['items'][i]['quantity'];
					total_credits = parseInt(obj['DATA']['items'][i]['buy_credits'])*parseInt(obj['DATA']['items'][i]['quantity']);
					content = content + "<tr><td align='center'><img src='" + item_pic + "' width='100' height='100'><br>" + item_name + "</td><td align='center'>" + quantity + "</td><td align='center'>" + buy_credits + "</td><td align='center'>" + total_credits.toString() + "</td><td align='center'><button class='btn btn-small btn-danger' data-loading-text='Processing...' onclick='activate_remove(\"" + item_id + "\", \"" + item_name + "\", \"" + quantity + "\")' >Remove/Edit</button></td></tr>";
				}
				content = content + "</table>";
			}
			$('#main-content').html( content);
		}
		else if(obj['REASON'] == 'Undefined option')
			display_error();
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			content = "<table border='4' width='100%'><caption class='text-info lead'><u><b>Shopping Cart</b></u></caption><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total Credits</th><th>Action</th></tr></table><br><center>Total credits required : 0</center>";
			$('#main-content').html( content);
			show_failure(obj['REASON']);
		}
	});
}

function display_heading(type) {
	var heading;
	switch(type) {
		case "home":
			heading = "A3 Elements E-shop Home";
			break;
		case "cart":
			heading = "Shopping Cart";
			break;
		case "mem":	
			heading = "Memberships";
			break;
		case "mage":
			heading = "Mage Items";
			break;
		case "holyknight":
			heading = "Holy Knight Items";
			break;
		case "archer":
			heading = "Archer Items";
			break;
		case "warrior":
			heading = "Warrior Items";
			break;
		case "mis":
			heading = "Miscellaneous Items";
			break;
		case "jewellery":	
			heading = "Exclusive Jewellery";
			break;
		case "hist":
			heading = "E-shop History";
			break;
	}
	return heading;
}

function add_to_cart(myid, item_name) {
	$('#' + myid).button('loading');
	var quantity = $.trim($('#' + myid + '-quantity').val());
	quantity = quantity.replace(/[^0-9]/g,'');
	$.get(http_host + 'index.php/utils/add_item/' + myid + '/' + quantity, function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			show_success(item_name + ' successfully added!');
		}
		else if(obj['REASON'] == 'Undefined option')
			display_error();
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#' + myid).button('reset');
	});
}

function remove_from_cart(myid, item_name) {
	$('#' + myid).button('loading');
	var quantity = $.trim($('#' + myid + '-quantity').val());
	quantity = quantity.replace(/[^0-9]/g,'');
	$.get(http_host + 'index.php/utils/remove_item/' + myid + '/' + quantity, function (data) {
		$('#' + myid).button('reset');
		$('#display-remove').modal('hide');
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			display_cart();
			show_success(item_name + ' successfully removed/edited!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
	});
}

function checkout() {
	$('#checkout-btn').button('loading');
	$.get(http_host + 'index.php/utils/deliver', function (data) {
		$('#checkout-btn').button('reset');
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			display_cart();
			$('#credit-value').text(obj['CREDITS']);
			show_success('Items successfully delivered to your character!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
	});
}

function clear_cart() {
	$('#clear-btn').button('loading');
	$.get(http_host + 'index.php/utils/clear_cart', function (data) {
		$('#clear-btn').button('reset');
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			display_cart();
			show_success('Cart was cleared successfully!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
	});
}

function make_active(myid) {
	reset_active();
	$('#' + myid).addClass("active");
}

function reset_active() {
	$("#home-menu").removeClass("active");
	$("#buy-menu").removeClass("active");
	$("#cart-menu").removeClass("active");
	$("#hist-menu").removeClass("active");
}

function activate_remove(item_id, item_name, quantity) {
	$('<div id="display-remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:535px;height:250px;color:black;"><div id ="display-remove-body" class="modal-body"></div></div>').appendTo('body');
	var content = "<center><h4>Remove/Edit Item</h4></center><table class='table tabler-bordered'><tr><td>Item Name :</td><td>" + item_name + "</td></tr><tr><td>Current Quantity :</td><td>" + quantity + "</td></tr><tr><td>Remove Quantity :</td><td><input type='text' id='" + item_id + "-quantity'></td></tr></table><center><button id='" + item_id + "' class='btn btn-info' data-loading-text='Generating...' onclick='remove_from_cart(this.id, \"" + item_name + "\")'>Remove/Edit</button>&nbsp;&nbsp;<button class='btn btn-info' onclick='$(\"#display-remove\").modal(\"hide\")'>Cancel</button></center>";
	$('#display-remove-body').html(content);
	$('#display-remove').modal('show');
}

function convert_time() {
	$('#time-credits').button('loading');
	$.get(http_host + 'index.php/utils/convert_time', function (data) {
		$('#clear-btn').button('reset');
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			$('#credit-value').text(obj['TOTAL']);
			show_success(obj['CREDITS'] + ' credits were added to your account!');
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#time-credits').button('reset');
		$('#show-time').hide();
		$('#dis-time').empty();
	});
}

function convert_wz() {
	var wz = $.trim($('#wz').val());
	wz = wz.replace(/[^0-9]/g,'');
	if(wz == '')
		show_failure("Please enter woonz value!");
	else {
		$('#wz-credits').button('loading');
		$.get(http_host + 'index.php/utils/convert_wz/' + wz, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				$('#credit-value').text(obj['TOTAL']);
				show_success(obj['CREDITS'] + ' credits were added to your account!');
			}
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#wz-credits').button('reset');
			$('#wz').val('');
			$('#show-add').hide();
		});
	}
}

function convert_gold() {
	var slots = $.trim($('#slots').val());
	slots = slots.replace(/[^0-9]/g,'');
	if(slots == '')
		show_failure("Please enter slots value!");
	else {
		$('#gold-credits').button('loading');
		$.get(http_host + 'index.php/utils/convert_gold/' + slots, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				$('#credit-value').text(obj['TOTAL']);
				show_success(obj['CREDITS'] + ' credits were added to your account!');
			}
			else if(obj['REASON'] == 'Not logged in or not selected character')
				document.location.reload(true);
			else
				show_failure(obj['REASON']);
			$('#gold-credits').button('reset');
			$('#slots').val('');
			$('#show-add-gold').hide();
		});
	}
}

function get_time() {
	$('#show-time').hide();
	$('#show-add').hide();
	$('#show-add-gold').hide();
	$('#dis-time').empty();
	$('#show-time-btn').button('loading');
	$.get(http_host + 'index.php/utils/get_time', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var content = "<table><tr><td>Total Online Time : </td><td>" + obj.DATA.total_time + "</td></tr><tr><td>Used Online Time : </td><td>" + obj.DATA.used_time + "</td></tr><tr><td>Remaining Online Time : </td><td>" + obj.DATA.rem_time + "</td></tr></table>";
			$('#dis-time').html(content);
			$('#show-time').show();
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);
		$('#show-time-btn').button('reset');
	});
}

function apply_coupon(){
	var coupon = $("#coupon-list option:selected").text();
	$.get(http_host + 'index.php/utils/apply_coupon/' + coupon, function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			show_success('Coupon Applied Successfully!!');
			display_cart();			
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);	
		$('#coupon-add-btn').button('reset');	
	});
}

function remove_coupon(){
	$.get(http_host + 'index.php/utils/remove_coupon/', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			show_success('Coupon Removed Successfully!!');
			display_cart();			
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else
			show_failure(obj['REASON']);	
		$('#coupon-remove-btn').button('reset');	
	});
}

function show_coupon(){
	$.get(http_host + 'index.php/utils/get_coupons/', function (data) {
		var obj = JSON.parse(data);
		if(obj['RESULT'] == 'SUCCESS') {
			var coup_code = '';
			var coup_status = '';
			var min_credit_required = '';
			var coup_discount = '';
			coup_list = '<select id="coupon-list" style="width: 134px;margin-top: 12px;">';
			var content = "<table border='4' width='100%'><caption class='text-info lead'><u>List of Coupons</u><br><span id='used-coupon' style='float: left;margin-top: 11px;'></span></caption><tr><th>Coupon Code</th><th>Status</th><th>Minimum Credit Required</th><th>Discount Percentage</th></tr>";
			for(var i = 0; i < obj['DATA'].length; i++) {
				coup_code = obj['DATA'][i]['coupon'];
				coup_status = obj['DATA'][i]['used'];
				min_credit_required = obj['DATA'][i]['min'];
				coup_discount = obj['DATA'][i]['percent'];
				if(coup_status==1)
					content = content + "<tr><td align='center'>" + coup_code + "</td><td style='color:red' align='center'>Used</td><td align='center'>" + min_credit_required + "</td><td align='center'>" + coup_discount + "</td></tr>";
				else{
					content = content + "<tr><td align='center'>" + coup_code + "</td><td style='color:green' align='center'>Not used</td><td align='center'>" + min_credit_required + "</td><td align='center'>" + coup_discount + "</td></tr>";
					coup_list = coup_list + '<option value="'+coup_code+'">'+coup_code+'</option>';
				}
			}
			content = content + "</table>";
			coup_list = coup_list + "</select>";
			$('#show-coupon').html(content);
			$('#show-coupon').show();
		}
		else if(obj['REASON'] == 'Not logged in or not selected character')
			document.location.reload(true);
		else {
			show_failure(obj['REASON']);
			coup_list = 'NIL';
			$('#show-coupon').html('<br><center><u class="lead">List of Coupons</u></center><br><center>'+obj['REASON']+'</center>');
			$('#show-coupon').show();
		}
		$('#coupon-show-btn').button('reset');	
	});
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

function show_loading() {
    $('#main-content').html('<center><p class="lead">Loading...</p></center>');
}
init();