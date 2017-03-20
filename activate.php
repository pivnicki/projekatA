<?php 
 include_once 'includes/User.php'; 
$activationcode=$_GET['code'] ? $_GET['code'] : '';
echo $activationcode;
echo "<br>";
 
$email=(isset($_GET['email'])) ? $_GET['email'] : '';
echo $email;


$user=new User();

$a=$user->activate($activationcode);

 if($a==true){
	 echo " uspesno ";
 }