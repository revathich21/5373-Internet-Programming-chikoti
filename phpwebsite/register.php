<?php
require('server.php');
if(isset($_SESSION['username'] )){
	header('Location:login.php');
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<style>
	body  {
  background-image: url("https://png.pngtree.com/thumb_back/fw800/back_pic/03/56/52/16579e13b334320.jpg");
  background-size:cover;
}
</style>
</head>
<body>
  <div class="header">
		<h2 style='color:brown'>Register </h2>
		
  </div>
	
  <form method="post" action="register.php">
  	<div class="input-group">
  	  <label>Username</label>
  	  <input type="text" name="username" >
  	</div>
  	<div class="input-group">
  	  <label>Email</label>
  	  <input type="email" name="email" >
  	</div>
  	<div class="input-group">
  	  <label>Password</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
  	  <label>Confirm password</label>
  	  <input type="password" name="password_2">
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
</body>
</html>