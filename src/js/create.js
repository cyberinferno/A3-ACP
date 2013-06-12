function create() {
	var user = $.trim($('#username').val());
	var passwd = $.trim($('#password').val());
	var rpasswd = $.trim($('#rpassword').val());
	var code = $.trim($('#code').val());
	var name = $.trim($('#name').val());
	var email = $.trim($('#email').val());
	var contact = $.trim($('#contact').val());
	user = user.replace(/[^A-Za-z0-9]/g,'');
	contact = contact.replace(/[^0-9]/g,'');
	passwd = passwd.replace(/[^A-Za-z0-9]/g,'');
	code = code.replace(/[^A-Za-z0-9]/g,'');
	name = name.replace(/[^A-Za-z0-9]/g,'');
	if(checkEmail(email)) {
		if(user == '' || contact == '' || code == '' || passwd == '' || name == '')
			show_failure('All fields are mandatory!')
		else {
			if(passwd == rpasswd) {
				show_success('Processing...');
				$.get(http_host + 'index.php/main/do_create/' + encode64(user) + '/' + encode64(passwd) + '/' + name + '/' + email + '/' + contact + '/' + code, function (data) {
					var obj = JSON.parse(data);
					if(obj['RESULT'] == 'SUCCESS') {
						show_success('Account successfully created! Please check your E-mail to activate your account!');
						$('#password').val('');
						$('#rpassword').val('');
					}
					else {
						show_failure(obj['REASON']);						
						$('#username').focus();
						$('#captcha').attr('src','./index.php/captcha/make/100/40/5?v=' + Math.random().toString());
					}
				});
			}
			else
				show_failure('Passwords do not match!');
		}
	}
	else
		show_failure('Please enter valid email address!');
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