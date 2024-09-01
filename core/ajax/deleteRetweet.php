<?php
include '../init.php';

if(isset($_POST['deleteRetweet']) && !empty($_POST['deleteRetweet'])) {
    $tweet_id = $_POST['deleteRetweet'];
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM `tweets` WHERE `tweetID` = :tweet_id AND `retweetBy` = :user_id");
    $stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $retweet = $stmt->fetch(PDO::FETCH_OBJ);

    if ($retweet) {
        $stmt = $pdo->prepare("DELETE FROM `tweets` WHERE `tweetID` = :tweet_id AND `retweetBy` = :user_id");
        $stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("UPDATE `tweets` SET `retweetCount` = `retweetCount` - 1 WHERE `tweetID` = :original_tweet_id");
        $stmt->bindParam(":original_tweet_id", $retweet->retweetID, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
