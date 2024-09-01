<?php
class Tweet extends User {
    protected $message;

    public function __construct($pdo) {
        parent::__construct($pdo);  
        $this->message = new Message($pdo); 
    }
 
    public function tweets($user_id, $num) {
        $stmt = $this->pdo->prepare("
            SELECT tweets.*, users.* FROM `tweets` 
            JOIN `users` ON `tweets`.`tweetBy` = `users`.`user_id` 
            WHERE `tweets`.`tweetBy` = :user_id 
            OR `tweets`.`tweetBy` IN (
                SELECT `receiver` FROM `follow` WHERE `sender` = :user_id
            )
            ORDER BY `tweets`.`tweetID` DESC 
            LIMIT :num
        ");
        
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":num", $num, PDO::PARAM_INT);
        $stmt->execute();
        $tweets = $stmt->fetchAll(PDO::FETCH_OBJ);
    
        foreach ($tweets as $tweet) {
            $likes = $this->likes($user_id, $tweet->tweetID);
            $retweet = $this->checkRetweet($tweet->tweetID, $user_id);
            $user = $this->userData($tweet->retweetBy);
            
            echo '<div class="all-tweet">';
            echo '    <div class="t-show-wrap">';
            echo '        <div class="t-show-inner">';
            
            if (($retweet && $retweet['retweetID'] === $tweet->retweetID) || $tweet->retweetID > 0) {
                echo '            <div class="t-show-banner">';
                echo '                <div class="t-show-banner-inner">';
                echo '                    <span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>'.$user->screenName.' retweeted</span>';
                echo '                </div>';
                echo '            </div>';
            }
    
            if (!empty($tweet->retweetMsg) && is_array($retweet) && $tweet->tweetID === $retweet['tweetID'] || $tweet->retweetID > 0) {
                echo '            <div class="t-show-img">';
                echo '                <img src="'.BASE_URL.$user->profileImage.'"/>';
                echo '            </div>';
                echo '            <div class="t-s-head-content">';
                echo '                <div class="t-h-c-name">';
                echo '                    <span><a href="'.BASE_URL.$user->username.'">'.$user->screenName.'</a></span>';
                echo '                    <span>@'.$user->username.'</span>';
                echo '                    <span>'.(is_array($retweet) ? $this->timeAgo($retweet['postedOn']) : '').'</span>';
                echo '                </div>';
                echo '                <div class="t-h-c-dis">';
                echo '                    '.$this->getTweetLinks($tweet->retweetMsg);
                echo '                </div>';
                echo '            </div>';
                echo '            <div class="t-s-b-inner">';
                echo '                <div class="t-s-b-inner-in">';
                echo '                    <div class="retweet-t-s-b-inner">';
                
                if (!empty($tweet->tweetImage)) {
                    echo '                        <div class="retweet-t-s-b-inner-left">';
                    echo '                            <img src="'.BASE_URL.$tweet->tweetImage.'"/>'; 
                    echo '                        </div>';
                }
    
                echo '                        <div class="retweet-t-s-b-inner-right">';
                echo '                            <div class="t-h-c-name">';
                echo '                                <span><a href="'.BASE_URL.$tweet->username.'">'.$tweet->screenName.'</a></span>';
                echo '                                <span>@'.$tweet->username.'</span>';
                echo '                                <span>'.$this->timeAgo($tweet->postedOn).'</span>';
                echo '                            </div>';
                echo '                            <div class="retweet-t-s-b-inner-right-text">';
                echo '                                '.$this->getTweetLinks($tweet->status);
                echo '                            </div>';
                echo '                        </div>';
                echo '                    </div>';
                echo '                </div>';
                echo '            </div>';
            } else {
                echo '            <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">';
                echo '                <div class="t-show-head">';
                echo '                    <div class="t-show-img">';
                echo '                        <img src="'.$tweet->profileImage.'"/>';
                echo '                    </div>';
                echo '                    <div class="t-s-head-content">';
                echo '                        <div class="t-h-c-name">';
                echo '                            <span><a href="'.$tweet->username.'">'.$tweet->screenName.'</a></span>';
                echo '                            <span>@'.$tweet->username.'</span>';
                echo '                            <span>'.$this->timeAgo($tweet->postedOn).'</span>';
                echo '                        </div>';
                echo '                        <div class="t-h-c-dis">';
                echo '                            '.$this->getTweetLinks($tweet->status);
                echo '                        </div>';
                echo '                    </div>';
                echo '                </div>';
                
                if (!empty($tweet->tweetImage)) {
                    echo '                <div class="t-show-body">';
                    echo '                    <div class="t-s-b-inner">';
                    echo '                        <div class="t-s-b-inner-in">';
                    echo '                            <img src="'.$tweet->tweetImage.'" class="imagePopup"/>';
                    echo '                        </div>';
                    echo '                    </div>';
                    echo '                </div>';
                }
    
                echo '            </div>';
            }
    
            echo '            <div class="t-show-footer">';
            echo '                <div class="t-s-f-right">';
            echo '                    <ul>'; 
            echo '                        <li>'.((is_array($retweet) && $tweet->tweetID === $retweet['retweetID']) ? 
                '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.$tweet->retweetCount.'</span></a></button>' : 
                '<button class="retweet" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.($tweet->retweetCount > 0 ? $tweet->retweetCount : '0').'</span></a></button>').'</li>';
            echo '                        <li>'.((is_array($likes) && $likes['likeOn'] === $tweet->tweetID) ? 
                '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCounter">'.$tweet->likesCount.'</span></a></button>' : 
                '<button class="like-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="likesCounter">'.($tweet->likesCount > 0 ? $tweet->likesCount : '0').'</span></a></button>').'</li>';
            
            if ($tweet->tweetBy === $user_id) {
                echo '                        <li>';
                echo '                            <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>';
                echo '                            <ul>'; 
                echo '                                <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>';
                echo '                            </ul>';
                echo '                        </li>';
            } elseif ($tweet->retweetBy === $user_id) {
                echo '                        <li>';
                echo '                            <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>';
                echo '                            <ul>'; 
                echo '                                <li><label class="deleteRetweet" data-tweet="'.$tweet->tweetID.'">Delete Retweet</label></li>';
                echo '                            </ul>';
                echo '                        </li>';
            }
    
            echo '                    </ul>';
            echo '                </div>';
            echo '            </div>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
        }
    }

	public function getUserTweets($user_id){
		$stmt = $this->pdo->prepare("SELECT * FROM tweets LEFT JOIN users ON tweetBy = user_id WHERE tweetBy = :user_id AND retweetID = 0 OR retweetBy = :user_id ORDER BY tweetID DESC");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function addLike($user_id, $tweet_id, $get_id) {
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `likesCount` = `likesCount` + 1 WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
	
		$this->create('likes', array('likeBy' => $user_id, 'likeOn' => $tweet_id));
		if ($get_id != $user_id) {
			$this->message->sendNotification($get_id, $user_id, $tweet_id, 'like');
		}
	}
	
	public function unLike($user_id, $tweet_id, $get_id){
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `likesCount` = `likesCount`-1 WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->pdo->prepare("DELETE FROM `likes` WHERE `likeBy` = :user_id and `likeOn` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute(); 
	}

	public function likes($user_id, $tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `likes` WHERE `likeBy` = :user_id AND `likeOn` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function getMension($mension){
		$stmt = $this->pdo->prepare("SELECT `user_id`,`username`,`screenName`,`profileImage` FROM `users` WHERE `username` LIKE :mension OR `screenName` LIKE :mension LIMIT 5");
		$stmt->bindValue("mension", $mension.'%');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);

	}

	public function addMention($status,$user_id, $tweet_id){
		if(preg_match_all("/@+([a-zA-Z0-9_]+)/i", $status, $matches)){
			if($matches){
				$result = array_values($matches[1]);
			}
			$sql = "SELECT * FROM `users` WHERE `username` = :mention";
			foreach ($result as $trend) {
				if($stmt = $this->pdo->prepare($sql)){
					$stmt->execute(array(':mention' => $trend));
					$data = $stmt->fetch(PDO::FETCH_OBJ);
				}
			}
			if($data->user_id != $user_id){
				$this->message->sendNotification($data->user_id, $user_id, $tweet_id, 'mention');
			}
		}
	}

	public function getTweetLinks($tweet) {
		if ($tweet !== null) {
			$tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/", "<a href='$0' target='_blink'>$0</a>", $tweet);
			$tweet = preg_replace("/#([\w]+)/", "<a href='http://localhost/buzzly/hashtag/$1'>$0</a>", $tweet);     
			$tweet = preg_replace("/@([\w]+)/", "<a href='http://localhost/buzzly/$1'>$0</a>", $tweet);
		}
		return $tweet;     
	}
	
	public function getPopupTweet($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets`,`users` WHERE `tweetID` = :tweet_id AND `tweetBy` = `user_id`");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function retweet($tweet_id, $user_id, $get_id, $comment){
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `retweetCount` = `retweetCount`+1 WHERE `tweetID` = :tweet_id AND `tweetBy` = :get_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->bindParam(":get_id", $get_id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->pdo->prepare("INSERT INTO `tweets` (`status`,`tweetBy`,`retweetID`,`retweetBy`,`tweetImage`,`postedOn`,`likesCount`,`retweetCount`,`retweetMsg`) SELECT `status`,`tweetBy`,`tweetID`,:user_id,`tweetImage`,`postedOn`,`likesCount`,`retweetCount`,:retweetMsg FROM `tweets` WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":retweetMsg", $comment, PDO::PARAM_STR);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		$this->message->sendNotification($get_id, $user_id, $tweet_id, 'retweet');
	}

	public function checkRetweet($tweet_id, $user_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets` WHERE `retweetID` = :tweet_id AND `retweetBy` = :user_id or `tweetID` = :tweet_id and `retweetBy` = :user_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function tweetPopup($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM tweets,users WHERE tweetID = :tweet_id and user_id = `tweetBy`");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function comments($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM comments LEFT JOIN users ON commentBy = user_id WHERE commentOn = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function countTweets($user_id){
		$stmt = $this->pdo->prepare("SELECT COUNT(`tweetID`) AS `totalTweets` FROM `tweets` WHERE `tweetBy` = :user_id AND `retweetID` = '0' OR `retweetBy` = :user_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalTweets;
	}

	public function countLikes($user_id){
		$stmt = $this->pdo->prepare("SELECT COUNT(`likeID`) AS `totalLikes` FROM `likes` WHERE `likeBy` = :user_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalLikes;
	} 
}
?>	