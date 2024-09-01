$(function(){
	$(document).on('click','.retweet', function(){   //pt popup confirmare tweet
		var tweet_id    = $(this).data('tweet');
		var user_id     = $(this).data('user');
	    $counter        = $(this).find(".retweetsCount");
	    $count          = $counter.text();
	    $button         = $(this);

		$.post('http://localhost/buzzly/core/ajax/retweet.php', {showPopup:tweet_id,user_id:user_id}, function(data){
			$('.popupTweet').html(data);  
			$('.close-retweet-popup').click(function(){
				$('.retweet-popup').hide();
			})
		});
	});

	$(document).on('click', '.retweet-it', function(){  //pt popup confirmare tweet dupa adaugare comm
		var tweet_id   = $(this).data('tweet');
		var user_id    = $(this).data('user');
	    var comment    = $('.retweetMsg').val();

	    $.post('http://localhost/buzzly/core/ajax/retweet.php', {retweet:tweet_id,user_id:user_id,comment:comment}, function(){
	    	$('.retweet-popup').hide();
	    	$count++;
	    	$counter.text($count);
	    	$button.removeClass('retweet').addClass('retweeted');
	    });
	});
});