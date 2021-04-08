$(document).ready(function() {

$('#login-button').click(function () {
	$('#login-popup').show();
	$('#fade').show();
});

$('#btn-close').click(function(){
	$('#login-popup').hide();
	$('#fade').hide();
});

});

