function login() {
	var user,password;
	user = $.trim($('#username').val());
	password = $.trim($('#password').val());	
	if(user == "" || password == "") {
		show_failure("Please enter all details");
		$('#username').val('');
		$('#password').val('');
		$('#username').focus();
	}
	else {
		show_success("Processing request...");
		$.get(http_host + 'index.php/main/login/' + encode64(user) + '/' + encode64(password), function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS')
				location.reload(true);
			else {
				show_failure("Invalid Username/Password");
				$('#username').val('');
				$('#password').val('');
				$('#username').focus();
			}
		});
	}
	return false;
}

function select_char() {
	var character = $.trim($('#character').val());
	if(character == "")
		show_failure("Please select a character!");
	else {
		$.get(http_host + 'index.php/main/select/' + encode64(character), function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS')
				location.reload(true);
			else {
				show_failure(obj['REASON']);
				$('#char').focus();
			}
		});
	}
	return false;
}

function show_success(str) {
	$('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-success');
    $('#error-msg').text(str);
    $('#errordiv').fadeIn("slow");
}

function show_failure(str) {
	$('#errordiv').removeClass('alert-error alert-success');
    $('#errordiv').addClass('alert-error');
    $('#error-msg').text(str);
    $('#errordiv').fadeIn("slow");
}