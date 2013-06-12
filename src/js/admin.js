function display_menu(type) {
	switch(type) {
		case 'credits':
			make_active(type + '-menu');
			display_add_credits();
			break;
		case 'items':
			make_active(type + '-menu');
			display_add_items();
			break;
	}
}

function display_add_credits() {
	var content = '<p class="lead"><u>Add credits</u></p><br><input type="text" id="charac" placeholder="Character Name"><br><input type="text" id="credits" placeholder="Credits to add"><br><button id="add-credits-btn" onclick="add_credits()" class="btn btn-small btn-success" data-loading-text="Processing...">Add Credits</button>';
	$('#main-content').html(content);
}

function display_add_items() {
	var content = '<p class="lead"><u>Add Items</u></p><br><input type="text" id="item-name" placeholder="Item Name"><br><input type="text" id="item-count" placeholder="Items Count"><br><input type="text" id="item-cat" placeholder="Item Category"><br><input type="text" id="item-pic" placeholder="Item Picture eg. g8.jpg"><br><input type="text" id="item-ids" placeholder="Item ID chain eg 1145;455555:1146;4554455"><br><input type="text" id="buy-cred" placeholder="Buy Credits"><br><input type="text" id="rent-cred" placeholder="Rent Credits"><br><button id="add-items-btn" onclick="add_items()" class="btn btn-small btn-success" data-loading-text="Processing...">Add Items</button>';
	$('#main-content').html(content);	
}

function add_credits() {
	var credits = $.trim($('#credits').val());
	var charac = $.trim($('#charac').val());
	if(credits == '' || charac == '')
		show_error("Please fill all the details");
	else {
		$('#add-credits-btn').button('loading');
		$.get(http_host + 'index.php/admin_utils/add_credits/' + charac + '/' + credits, function (data) {
			var obj = JSON.parse(data);
			if(obj.RESULT == 'SUCCESS') {
				show_success(credits.toString() + " credits where added to character named " + charac);
				$('#credits').val('');
				$('#charac').val('');
				$('#add-credits-btn').button('reset');
			}
			else {
				$('#add-credits-btn').button('reset');
				show_error("ERROR : " + obj.REASON);
			}
		});
	}
}

function add_items() {
	var name = $.trim($('#item-name').val());
	var cat = $.trim($('#item-cat').val());
	var pic = $.trim($('#item-pic').val());
	var ids = $.trim($('#item-ids').val());
	var buy = $.trim($('#buy-cred').val());
	var rent = $.trim($('#rent-cred').val());
	var count = $.trim($('#item-count').val());
	if(name == '' || cat == '' || pic == '' || ids == '' || buy == '' || count == '')
		show_error("Please enter all details!");
	else {
		if(rent == '')
			rent = "0";
		$('#add-items-btn').button('loading');
		$.get(http_host + 'index.php/admin_utils/add_items/' + name + '/' + cat + '/' + encode64(ids) + '/' + count + '/' + encode64(pic) + '/' + buy + '/' + rent, function (data) {
			var obj = JSON.parse(data);
			if(obj.RESULT == 'SUCCESS') {
				show_success("Item was added successfully!");
				$('#item-name').val('');
				$('#item-cat').val('');
				$('#item-pic').val('');
				$('#item-ids').val('');
				$('#buy-cred').val('');
				$('#rent-cred').val('');
				$('#item-count').val('');
				$('#add-items-btn').button('reset');
			}
			else {
				$('#add-items-btn').button('reset');
				show_error("ERROR : " + obj.REASON);
			}
		});
	}
}

function reset_active() {
	$("#credits-menu").removeClass("active");
	$("#items-menu").removeClass("active");
	$("#home-menu").removeClass("active");
}

function make_active(myid) {
	reset_active();
	$('#' + myid).addClass("active");
}

function show_success(str) {
    $('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-success');
    $('#error-msg').html(str);
    $('#errordiv').fadeIn("slow");
}

function show_error(str) {
    $('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-error');
    $('#error-msg').html(str);
    $('#errordiv').fadeIn("slow");
}