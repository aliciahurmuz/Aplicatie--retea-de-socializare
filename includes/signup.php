<?php
    include '../core/init.php';
    $user_id=$_SESSION['user_id'];
    $user   =$getFromU->userData($user_id);
    if(isset($_GET['step'])===true && empty($_GET['step'])===false)
    {
        if(isset($_POST['next']))
        {
            $username=$getFromU->checkInput($_POST['username']);
            if(!empty($user_id))   
            {   
                if(strlen($username)>20)
                {
                    $error="Username must be in 6-20 characters";
                }else if($getFromU->checkUsername($username)===true)
                {
                    $error="Username is already taken";

                }else
                {
                    $getFromU->update('users',$user_id,array('username'=>$username));
                    header('Location:signup.php?step=2');
                }
            }else
            {
                $error="Please enter your username to choose";
            }
        }
        ?>
        <!doctype html>
<html>
	<head>
		<title>Buzzly</title>
		<meta charset="UTF-8" />
 		<link rel="stylesheet" href="assets/css/font/css/font-awesome.css"/>
		<link rel="stylesheet" href="../assets/css/style-complete.css"/>
	</head>
	<!--Helvetica Neue-->
<body>
<div class="wrapper">
	
<!-- nav wrapper -->
<div class="nav-wrapper">
	
	<div class="nav-container">	
		<div class="nav-second">
			<ul>
				<li><a href="#"<i class="fa fa-buzzly" aria-hidden="true"></i></a></li>							
			</ul>
		</div><!-- nav second ends-->
	</div><!-- nav container ends -->

</div><!-- nav wrapper end -->

<!---Inner wrapper-->
<div class="inner-wrapper">
	<!-- main container -->
	<div class="main-container">
		<!-- step wrapper-->
    <?php if($_GET['step']=='1')
    {?>

 		<div class="step-wrapper">
		    <div class="step-container">
				<form method="post">
					<h2>Choose a username</h2>
					<h4>You can change it later.</h4>
					<div>
						<input type="text" name="username" placeholder="Username"/>
					</div>
					<div>
						<ul>
						  <li><?php if(isset($error)){echo $error;}?></li>
						</ul>
					</div>
					<div>
						<input type="submit" name="next" value="Next"/>
					</div>
				 </form>
			</div>
		</div>
        <?php }?>
        <?php if(isset($_GET['step']) && $_GET['step'] == '2'){ ?> 	<div class='lets-wrapper'>
		<div class='step-letsgo'>
        <h2>Welcome to the World of Buzzly, <?php echo htmlspecialchars($user->screenName); ?>!</h2>
		<p>Stay connected and updated with the latest happenings on our social network. Our platform offers a range of features designed to enhance your online experience.</p>
		<h3>Features:</h3>
		<ul>
			<li>News Feed: Stay updated with the latest posts and updates from the people you follow.</li>
			<li>Notification System: Get real-time alerts about messages, follows, and likes.</li>
			<li>Instant Messaging: Communicate easily with others in real-time through text messages.</li>
			<li>Profile Personalization: Customize your profile with personal details and photos.</li>
			<li>"Who to Follow" Feature: Discover people you might know or find interesting, helping you expand your social network.</li>
		</ul>
        <br/>
			<span>
				<a href='../home.php' class='backButton'>Let's go!</a>
			</span>
		</div>
	</div>
  	<?php }?>
		
	</div><!-- main container end -->

</div><!-- inner wrapper ends-->
</div><!-- ends wrapper -->

</body>
</html>
	
        <?php
    }
?>