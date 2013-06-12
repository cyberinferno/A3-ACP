function forgot() {
	var user;
	user = $.trim($('#username').val());
	if(user == "") {
		show_failure("Please enter username!");
		$('#username').val('');
		$('#username').focus();
	}
	else {
		show_success("Processing request...");
		$.get(http_host + 'index.php/main/do_forgot/' + user, function (data) {
			var obj = JSON.parse(data);
			if(obj['RESULT'] == 'SUCCESS') {
				show_success("Please check your registered E-mail address!");
				$('#username').val('').focus();
			}				
			else {
				show_failure("Invalid username!");
				$('#username').val('');
				$('#username').focus();
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