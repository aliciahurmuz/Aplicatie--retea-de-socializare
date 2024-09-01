$(function(){
    $(document).on('click', '.addTweetBtn', function(){
        $.post('http://localhost/buzzly/core/ajax/tweetForm.php', function(data){
            $('.popupTweet').html(data);
            $('.closeTweetPopup').on('click', function(){
            });
        });
    });
    $(document).on('submit','#popupForm', function(e){
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        formData.append('file', $('#file')[0].files[0]);
        $.ajax({
            url: "http://localhost/buzzly/core/ajax/addTweet.php",
            type: "POST",
            data: formData,
            success: function(data){
                result = JSON.parse(data);
                if(result['error']){
                    $('<div class="error-banner"><div class="error-banner-inner"><p id="errorMsg">'+result.error+'</p></div></div>').insertBefore('.header-wrapper');
                    $('.popup-tweet-wrap').hide();
                }else if (result['success']){
                    $('<div class="error-banner"><div class="error-banner-inner"><p id="errorMsg">'+result.success+'</p></div></div>').insertBefore('.header-wrapper');
                    $('.popup-tweet-wrap').hide();
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
});
