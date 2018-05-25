<?php
session_start();
require('./includes/settings.php');
$db = new mysqli("localhost", $settings['username'], $settings['password'], $settings['dbname']);
// If we have an error connecting to the db, then exit page
if ($db->connect_errno) {
    print_response(['success'=>false,"error"=>"Connect failed: ".$db->connect_error]);
}
  require_once('./config.php');
   
  $token  = $_POST['stripeToken'];
  $email  = $_POST['stripeEmail'];
  $item_price = $_POST['item_price'];
  $customer = \Stripe\Customer::create(array(
      'email' => $email,
      'source'  => $token
  ));
 
  $charge = \Stripe\Charge::create(array(
      'customer' => $customer->id,
      'amount'   => $item_price*1000,
      'currency' => 'usd'
  ));

 $_SESSION['pay_success'] = $item_price;
   
  $session =  $_SESSION['userid'];
  if(!isset($_SESSION['username'])){
      $guest = 1;
      $uid = -1;
      $sql = "DELETE FROM `shopping_cart` WHERE uid=-1 and  session_id='{$session}'";
     // $result = $db->query($sql);
     $result = $db->query($sql);
  }
  else{
      $usename = $_SESSION['username'];
      $sql = "SELECT id FROM `users` WHERE username= '{$usename}'";
      $result = $db->query($sql);
      while ($row = $result->fetch_assoc()) {
  
          $item = $row;
  
      }
      $uid = $item['id'];
      $guest = 0;
      $sql = "DELETE FROM `shopping_cart` WHERE uid='{$uid}'";
      // $result = $db->query($sql);
      $result = $db->query($sql);
  }  
  header('location: app.php/browsePage');
?>

 