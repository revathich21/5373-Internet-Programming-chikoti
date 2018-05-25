<?php require('server.php');
if(isset($_SESSION['username'] )){
	header('Location:app.php/browsePage');

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
		<h2 style='color:brown'>Login</h2>
		
  </div>
	 
  <form method="post" action="login.php">
  	<div class="input-group">
  		<label>Username</label>
  		<input type="text" name="username" >
  	</div>
  	<div class="input-group">
  		<label>Password</label>
  		<input type="password" name="password">
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn" name="login_user">Login</button>
  	</div>
  	<p>
  		Not yet a member? <a href="/register.php">Sign up</a>
  	</p>
  </form>
</body>
</html>