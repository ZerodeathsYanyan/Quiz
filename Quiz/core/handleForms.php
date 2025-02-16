<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['registerBtn'])) {
	$username = trim($_POST['username']);
	$password = $_POST['password'];
	$confirmPassword = $_POST['confirmPassword'];
	$usertype = $_POST['usertype'];

	if (empty($username) || empty($password) || empty($confirmPassword) || empty($usertype)) {
		 header("Location: ../register.php?register=empty");
		 exit();
	} else{
		//check if pwd and cpwd is correct
		if ($password !== $confirmPassword) {
			header("Location: ../register.php?register=pwd");
			exit();
		}	else{
			//if characters are valid. Use A-z only
			if (!preg_match("/^[a-zA-Z]*$/", $username)){
			header("Location: ../register.php?register=char");
			exit();
			} 	else{
				//if correct, go to login, if username exist, try again
				$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
				try {
					 if (registerUser($usertype, $username, $hashedPassword)) {
						  header("Location: ../login.php");
						  exit();
					 } else {
							header("Location: ../register.php?register=username");
					 }
				} catch (PDOException $e) { 
						echo "Error: Could not register user.";
					 exit();
				}

		}
	}
}
}

//login button
if (isset($_POST['loginBtn'])) {
	$username = trim($_POST['username']);
	$password = $_POST['password'];
 
	if (empty($username) || empty($password)) {
	  header("Location: ../login.php?login=empty");
	  exit();
	}
 
	if (loginUser($pdo, $username, $password)) {
	  $stmt = $pdo->prepare("SELECT userType FROM users WHERE username = :username");
	  $stmt->bindParam(':username', $username);
	  $stmt->execute();
	  $user = $stmt->fetch(PDO::FETCH_ASSOC);

	  $usertype = $_SESSION['userType'];  
	  $username = $_SESSION['username']; 

	  header("Location: ../index.php");
	  exit();
	} else {
	  header("Location: ../login.php?login=incorrect");
	  exit();
	}
 }

 //log out
 if (isset($_GET['logoutAUser'])) {
	unset($_SESSION['username']);
	header('Location: ../login.php');
}


	
?>