<?php
include_once 'connection.php';


class User{
	
	private $connection;
	
	public function __construct(){
		$this->connection=new Connection();
		$this->connection=$this->connection->getDb();
	}
	
	 public function is_logged_in()
	 {
	  if(isset($_SESSION['id']))
	  {
	   return true;
	  }
	 }
	 
	 public function logout()
		 {
		  session_destroy();
		  $_SESSION['id'] = false;
		  header ("Location: http://localhost/gmap-projekat");
		 }
	
	public function login($email,$password){
		
		if(empty($email) && empty($password)){
			$msg = 'Nisu popunjena sva polja za login'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			exit;
		}
		
		if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
			$msg = 'Email nije važeći'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		} 
		
		
		try{
		$sql = "SELECT * FROM user WHERE email=:email";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
      
        $stmt->execute();

       $row = $stmt->fetch(PDO::FETCH_ASSOC);
	   
	    
		
		if($row && (password_verify($password,$row['password']))){
            $_SESSION['id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];
             $msg ="Logged in";
				  //Jos uvek radim na ovome
			 header( "refresh:2;url=http://localhost/gmap-projekat/profile?submit_msg=$msg" );
        }
        else {
				$msg = 'Neispravni login podaci'; 		
				header ("Location: logreg.php?submit_msg=$msg");
				exit;
        }
		
		// if($row>0){
				 // echo "Uspeh";
				  //Jos uvek radim na ovome
				// header( "refresh:5;url=http://localhost/gmap-projekat/logreg.php" );
			// }
		
		// if($row['email']==0){
			// $msg = 'Email ne postoji u bazi'; 		
			// header ("Location: logreg.php?submit_msg=$msg");
			// exit;
		// }
		
		
		
		
		// if ( password_verify ( $enc_pass , $row['password'] ) )
		// { 
			  // session_start();
            // echo $_SESSION['name'] = $row['name'];
            // $_SESSION['id'] = $row['id'];
            // header("Location: http://localhost/gmap-projekat/profile.php?submit_msg=$n");
            // exit();
		// }  
         // else {
			// $msg = 'Neispravna lozinka'; 		
			// header ("Location: logreg.php?submit_msg=$msg");
			// exit;
          //}
		}catch(PDOException $e){
			echo $e->getMessage();
		}
    }
	
	
	public function register($first_name,$last_name,$email,$password){
		
		if(empty($first_name) || empty($last_name) || empty($email) || empty($password)){
			$msg = 'Nisu popunjena sva polja za registraciju'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		}
		
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $first_name))
		{
			$msg = 'Ime  ne može imati specijalne karaktere'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		}
		
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $last_name))
		{
			$msg = 'Prezime  ne može imati specijalne karaktere'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		}
		
		
		 
		if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
			$msg = 'Email nije važeći'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		} 
		
		if(strlen($password)<3){
			$msg = 'Lozinka je suviše kratka'; 		
			header ("Location: logreg.php?submit_msg=$msg");
			break;
		}
		 
		try{
			
			
			$activated="SELECT email FROM user WHERE email=:email";
			
			$stmt1=$this->connection->prepare($activated);
			$stmt1->bindParam(":email",$email,PDO::PARAM_STR);			
			$stmt1->execute();
			$result=$stmt1->fetch(PDO::FETCH_ASSOC);
			 if($result>0){
				 $msg="Email vec postoji u bazi";
				  //Jos uvek radim na ovome
				  
				 header( "refresh:3;url=http://localhost/gmap-projekat/logreg.php?submit_msg=$msg" );
				 exit;
			 }

			
			$verificationCode = md5($email.time());
			
			
			$sql="INSERT INTO user (firstname,lastname,email,password,activationcode)
			 VALUES (:first_name,:last_name,:email,:password,:activationcode)";
			 $enc_pass = password_hash ( $password , PASSWORD_DEFAULT ) ;
			 $stmt=$this->connection->prepare($sql);
			 $stmt->bindParam(":first_name",$first_name,PDO::PARAM_STR);
			 $stmt->bindParam(":last_name",$last_name,PDO::PARAM_STR);
			 $stmt->bindParam(":email",$email,PDO::PARAM_STR);
			 $stmt->bindParam(":password",$enc_pass,PDO::PARAM_STR);
			 $stmt->bindParam(":activationcode",$verificationCode,PDO::PARAM_STR);
			 $stmt->execute();
			 
			 
               
  
                // verifikacija emaila
                //$verificationLink = "http://bootstrap-vezba.netai.net/gmap-projekat/activate.php?code=".$verificationCode;
		$verificationLink = "http://localhost/gmap-projekat/activate.php?code=".$verificationCode."&?email=".$email;

                $htmlStr = "";
                $htmlStr .= "Zdravo " . $email . ",<br /><br />";
 
                $htmlStr .= "Klikni na dugme ispod kako bi verifikovao tvoj email.<br /><br /><br />";
                $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:blue; color:#fff;'>VERIFY EMAIL</a><br /><br /><br />";
 
                $htmlStr .= "Puno pozdrava,<br />";
                $htmlStr .= "<a href='http://bootstrap-vezba.netai.net/gmap-projekat' target='_blank'>Prijavi deponiju</a><br />";
 
 
                $name = "Prijavi deponiju";
                $email_sender = "no-reply@prijavideponiju.com";
                $subject = "Verifikacioni Link | Prijavi deponiju | Subscription";
                $recipient_email = $email;
 
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: {$name} <{$email_sender}> \n";
 
                $body = $htmlStr;
 
                // funkcija za slanje maila
                if( mail($recipient_email, $subject, $body, $headers) ){
 
                    // obavestenje za korisnika na mail
                    echo "<div id='successMessage'>Verifikacioni mail je poslat na  <b>" 
					. $email . "</b>, verifikuj mail.</div>";
 
  
 
                }else{
				$msg = 'Neuspela registracija'; 		
				header ("Location: logreg.php?submit_msg=$msg");
				exit;
				}
		}
		catch(PDOException $e)
		{ 
		echo $e->getMessage();
	}
	}
	
	
	public function activate($activationcode){
		try{	
			
			$activated="SELECT status FROM user";
			
			$stmt1=$this->connection->prepare($activated);; 
			$stmt1->execute();
			$result=$stmt1->fetch(PDO::FETCH_ASSOC);
			 if($result>0){
				 echo "Tvoj email je već aktiviran";
				 header( "refresh:5;url=http://localhost/gmap-projekat/logreg.php" );
			 }

			$status=1;	
			$sql="UPDATE user SET status=:status WHERE activationcode=:activationcode";
			
			$stmt=$this->connection->prepare($sql);
			$stmt->bindParam(":activationcode",$activationcode,PDO::PARAM_STR);
			$stmt->bindParam(":status",$status,PDO::PARAM_STR); 
			$stmt->execute();
			 return true;
		}			 
		catch(PDOException $e){
			echo $e->getMessage();
		}
	}
	
	public function insertToMap($name,$address,$lat,$lng,$type){
		
		try{			
			$sql="INSERT INTO markers (name,address,lat,lng,type) VALUES (:name,:address,:lat,:lng,:type)";
			
			$stmt=$this->connection->prepare($sql);
			$stmt->bindParam(":name",$name,PDO::PARAM_STR);
			$stmt->bindParam(":address",$address,PDO::PARAM_STR);
			$stmt->bindParam(":lat",$lat);
			$stmt->bindParam(":lng",$lng);
			$stmt->bindParam(":type",$type);
			$stmt->execute();
			 
		}			 
		catch(PDOException $e){
			echo $e->getMessage();
		}
	}
	
	public function getFromMap(){
		try{
			$sql="SELECT * FROM markers";
			$stmt=$this->connection->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}
	
}

