$(document).ready(function() {
	$('#btn-chat').on('click', function(e) {
		var message = $('#btn-input').val();
		$.post("ajax.php", {message: message, chat: 'Y'}).done(function(data){
			console.log(data);
		});
	});
});
