<?php
require_once 'dbConfig.php';
require_once 'handleForms.php';

function registerUser($usertype, $username, $hashedPassword) {
	global $pdo;

	try {
		 $stmt = $pdo->prepare("INSERT INTO users (userType, username, password) VALUES (:usertype, :username, :password)");
		 $stmt->bindParam(':usertype', $usertype);
		 $stmt->bindParam(':username', $username);
		 $stmt->bindParam(':password', $hashedPassword);
		 return $stmt->execute();
	} catch (PDOException $e) {
		 return false;
	}
}


function loginUser($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['userType'] = $user['userType'];  

            $_SESSION['message'] = "Login successful!";
            return true;
        } else {
            $_SESSION['message'] = "Invalid username or password.";
            return false;
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}
?>


