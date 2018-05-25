<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	header('location: app.php/browsePage');
  }
  else {
  	session_destroy();
  	//unset($_SESSION['username']);
  	header("Location:app.php/browsePage");
  }
?>