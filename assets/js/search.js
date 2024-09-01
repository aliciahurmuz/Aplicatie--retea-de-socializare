$(function(){  //pt search din bara principala
	$('.search').keyup(function(){
		var search = $(this).val();
		$.post('http://localhost/buzzly/core/ajax/search.php', {search:search}, function(data){
			$('.search-result').html(data);
			if(search == ""){
				$('.search-result').html("");
				$('.search-result li').click(function(){ 
					$('.search-result li').hide(); 
				});	
			}
		});
	});

	$(document).on('keyup', '.search-user', function(){  //pt cautare useri in mesaje directe
		$('.message-recent').hide();
		var search = $(this).val();
		$.post('http://localhost/buzzly/core/ajax/searchUserInMsg.php', {search:search}, function(data){
			$('.message-body').html(data);  
		});
	});
});