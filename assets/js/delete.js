$(function(){   
	$(document).on('click', '.deleteTweet', function(){  //confirmare
		var tweet_id = $(this).data('tweet');
		$.post('http://localhost/buzzly/core/ajax/deleteTweet.php', {showpopup:tweet_id}, function(data){
			$('.popupTweet').html(data);
			$('.close-retweet-popup,.cancel-it').click(function(){
				$('.retweet-popup').hide();
			});
		});
	});

	$(document).on('click','.delete-it', function(){  //stergere
		var tweet_id = $(this).data('tweet');
		$.post('http://localhost/buzzly/core/ajax/deleteTweet.php', {deleteTweet:tweet_id}, function(){
			$('.retweet-popup').hide();
			window.location = window.location.href;  
		});
	});

	$(document).on('click', '.deleteComment', function(){
		var tweet_id    = $(this).data('tweet');
		var commentID   = $(this).data('comment');
		$.post('http://localhost/buzzly/core/ajax/deleteComment.php', {deleteComment:commentID,tweet_id:tweet_id});
		$.post('http://localhost/buzzly/core/ajax/popuptweets.php', {showpopup:tweet_id}, function(data){
			$('.popupTweet').html(data);
			$('.tweet-show-popup-box-cut').click(function(){
				$('.tweet-show-popup-wrap').hide();
			});
		});
	});
});