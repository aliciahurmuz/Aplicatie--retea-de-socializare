<?php 
  include '../init.php';
   if(isset($_POST['hashtag'])){	
   	  if(!empty($_POST['hashtag'])){
   	  	 $hashtag = $getFromU->checkInput($_POST['hashtag']);
   	  	 $mention = $getFromU->checkInput($_POST['hashtag']);
   	  	 if(substr($mention, 0,1) === '@'){
   	  	 	$mention = str_replace('@', '', $mention);
				  $mentions = $getFromT->getMension($mention);   	  	 	
				  foreach ($mentions as $mention) {
   	  	 	  echo '<li><div class="nav-right-down-inner">
						<div class="nav-right-down-left">
							<span><img src="'.BASE_URL.$mention->profileImage.'"></span>
						</div>
						<div class="nav-right-down-right">
							<div class="nav-right-down-right-headline">
								<a>'.$mention->screenName.'</a><span class="getValue">@'.$mention->username.'</span>
							</div>
						</div>
					</div><!--nav-right-down-inner end-here-->
					</li>';
   	  	 	}
   	  	 }
   	  }
   }
?>
