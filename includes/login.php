<?php
ob_start();
require_once 'D:/wamp64/www/buzzly/core/classes/user.php'; 
require_once 'D:\wamp64\www\buzzly\core\database/connection.php';
$getfromU = new User($pdo);
if (isset($_POST['login']) && !empty($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; 

    if (!empty($email) && !empty($password)) { 
        $email = $getfromU->checkInput($email); 
        $password = $getfromU->checkInput($password); 

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
            $error = "Invalid format";
        } else {
            if($getfromU->login($email,$password)===false)
            {
                $error="The email or password is incorrect!";
            }

        }
    } else {
        $error = "Please enter username and password!";
    }
}
?>

<div class="login-div">
<form method="post"> 
    <h3>Log in </h3>
	<ul>
		<li>
		  <input type="text" name="email" placeholder="Please enter your email here"/>
		</li>
		<li>
		  <input type="password" name="password" placeholder="password"/>
		  <input type="submit" name="login" value="Log in"/>
		</li>
		
	</ul>
    <?php
	if (isset($error)) {
        echo '<li class="error-li">
        <div class="span-fp-error">'.$error.'</div>
       </li>'; 
    }
    ?>
	</form>
</div>
