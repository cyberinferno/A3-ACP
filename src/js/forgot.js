function forgot(){var a;a=$.trim($("#username").val());""==a?(show_failure("Please enter username!"),$("#username").val(""),$("#username").focus()):(show_success("Processing request..."),$.get(http_host+"index.php/main/do_forgot/"+a,function(a){"SUCCESS"==JSON.parse(a).RESULT?(show_success("Please check your registered E-mail address!"),$("#username").val("").focus()):(show_failure("Invalid username!"),$("#username").val(""),$("#username").focus())}));return!1}
function show_success(a){$("#errordiv").removeClass("alert-error alert-success");$("#errordiv").addClass("alert-success");$("#error-msg").text(a);$("#errordiv").fadeIn("slow")}function show_failure(a){$("#errordiv").removeClass("alert-error alert-success");$("#errordiv").addClass("alert-error");$("#error-msg").text(a);$("#errordiv").fadeIn("slow")};