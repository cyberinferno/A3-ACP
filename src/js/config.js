var http_host = SERVER_HTTP_HOST() + "/";
var img_loc = http_host + "img/";

function SERVER_HTTP_HOST() {  
	var url = window.location.href;  
	url = url.replace("http://", "");	  
	var urlExplode = url.split("/");  
	var serverName = urlExplode[0];	  
	serverName = 'http://' + serverName;  
	return serverName;
}