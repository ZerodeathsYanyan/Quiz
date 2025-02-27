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
// Creating quiz
if (isset($_POST['createQuiz'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$max_attempts = $_POST['max_attempts'];

	if (isset($_SESSION['user_id'])) {
			$teacher_id = $_SESSION['user_id'];

			// Insert the quiz into the database
			$stmt = $pdo->prepare("INSERT INTO quizzes (teacher_id, title, description, max_attempts) VALUES (?, ?, ?, ?)");
			$stmt->execute([$teacher_id, $title, $description, $max_attempts]);
			$quiz_id = $pdo->lastInsertId();

			$difficulties = ['easy', 'normal', 'hard'];
			foreach ($difficulties as $difficulty) {
					 if (!empty($_POST['questions'][$difficulty])) {
								$stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

								for ($i = 0; $i < count($_POST['questions'][$difficulty]); $i++) {
										 $question_text = $_POST['questions'][$difficulty][$i];
										 $question_type = $_POST['question_type'][$difficulty][$i];

										 $option_a = $_POST['option_a'][$difficulty][$i] ?? null;
										 $option_b = $_POST['option_b'][$difficulty][$i] ?? null;
										 $option_c = $_POST['option_c'][$difficulty][$i] ?? null;
										 $option_d = $_POST['option_d'][$difficulty][$i] ?? null;
										 $correct_answer = $_POST['correct_option'][$difficulty][$i] ?? $_POST['problem_answer'][$difficulty][$i];

										 $stmt->execute([$quiz_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty]);
								}
					 }
			}

			header("Location: ../index.php?quiz=created");
			exit();
	} else {
			echo "Error: Teacher ID is not set.";
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
// submitting quiz
if (isset($_POST['submitQuiz'])) {
	$quiz_id = $_POST['quiz_id'];
	$user_id = $_SESSION['user_id'];
	$answers = $_POST['answers'];


	$attemptsQuery = $pdo->prepare("SELECT COUNT(*) as attempt_count FROM quiz_attempts WHERE quiz_id = ? AND user_id = ?");
	$attemptsQuery->execute([$quiz_id, $user_id]);
	$attemptsResult = $attemptsQuery->fetch(PDO::FETCH_ASSOC);
	$attempt_count = $attemptsResult['attempt_count'];


	$quizQuery = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
	$quizQuery->execute([$quiz_id]);
	$quiz = $quizQuery->fetch(PDO::FETCH_ASSOC);

	if ($attempt_count >= $quiz['max_attempts']) {
		 header("Location: ../takeQuiz.php?quiz_id=$quiz_id&submitted=1");
		 exit();
	}


	$attempt_number = $attempt_count + 1;

	foreach ($answers as $question_id => $answer_text) {
		 $stmt = $pdo->prepare("INSERT INTO answers (question_id, user_id, answer_text, attempt_number) VALUES (?, ?, ?, ?)");
		 $stmt->execute([$question_id, $user_id, $answer_text, $attempt_number]);
	}


	$recordAttemptQuery = $pdo->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, attempt_number) VALUES (?, ?, ?)");
	$recordAttemptQuery->execute([$quiz_id, $user_id, $attempt_number]);


	header("Location: ../takeQuiz.php?quiz_id=$quiz_id&submitted=1");
	exit();
}


	
?>