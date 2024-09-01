$(function(){
	$(document).on('click', '#send', function(){
		var message = $('#msg').val();
		var get_id   = $(this).data('user');
		$.post('http://localhost/buzzly/core/ajax/messages.php', {sendMessage:message,get_id:get_id}, function(data){
			getMessages();  //refresh mesaje
			$('#msg').val('');  
		});
	});
});