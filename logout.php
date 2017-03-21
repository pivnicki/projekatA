<?php

session_start();
include_once 'includes/User.php';
$user=new User();
if($user->is_logged_in()==false)
{
 $user->logout(); 
 $user->redirect('index.php');
}