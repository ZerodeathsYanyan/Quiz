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

// editing the quiz
if (isset($_POST['editBtn'])) {
	$quiz_id = $_POST['quiz_id'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$max_attempts = $_POST['max_attempts'];

	try {
		 // Update quiz details
		 $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ?, max_attempts = ? WHERE quiz_id = ?");
		 $stmt->execute([$title, $description, $max_attempts, $quiz_id]);

		 // Loop through and update questions
		 $difficulties = ['easy', 'normal', 'hard'];
		 foreach ($difficulties as $difficulty) {
			  if (!empty($_POST['questions'][$difficulty])) {
					foreach ($_POST['questions'][$difficulty] as $index => $question_text) {
						 $question_type = $_POST['question_type'][$difficulty][$index];
						 $option_a = $_POST['option_a'][$difficulty][$index] ?? null;
						 $option_b = $_POST['option_b'][$difficulty][$index] ?? null;
						 $option_c = $_POST['option_c'][$difficulty][$index] ?? null;
						 $option_d = $_POST['option_d'][$difficulty][$index] ?? null;
						 $correct_answer = $_POST['correct_option'][$difficulty][$index] ?? $_POST['problem_answer'][$difficulty][$index];

						 // Assuming you have a way to identify the question id, e.g., using a hidden input field in the form
						 $question_id = $_POST['question_ids'][$difficulty][$index] ?? null;

						 if ($question_id) {
							  // Update existing question
							  $updateQuestionStmt = $pdo->prepare("UPDATE questions SET question_text = ?, question_type = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, difficulty = ? WHERE question_id = ?");
							  $updateQuestionStmt->execute([$question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty, $question_id]);
						 } else {
							  // Insert new question if question_id is not set
							  $insertQuestionStmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
							  $insertQuestionStmt->execute([$quiz_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty]);
						 }
					}
			  }
		 }

		 header("Location: ../index.php?quiz=updated");
		 exit();
	} catch (PDOException $e) {
		 echo "Error: Could not update the quiz.";
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

//deleting quiz


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteQuizBtn'])) {
	session_start(); // Ensure session is started
	if ($_SESSION['userType'] === 'teacher') {
		 $quiz_id = $_POST['quiz_id']; // Corrected quiz_id retrieval

		 // Add your SQL deletion logic here
		 $stmt = $pdo->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
		 $stmt->execute([$quiz_id]);

		 // Redirect to the correct index.php with a success message
		 header('Location: ../index.php?msg=QuizDeleted');
		 exit;
	} else {
		 // Redirect to the correct index.php with an error message
		 header('Location: ../index.php?msg=Unauthorized');
		 exit;
	}
}
	
?>