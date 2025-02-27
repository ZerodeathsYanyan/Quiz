<?php
require_once 'dbConfig.php';
require_once 'handleForms.php';

function createQuiz($teacher_id, $quiz_title) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO quizzes (teacher_id, quiz_title) VALUES (:teacher_id, :quiz_title)");
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':quiz_title', $quiz_title);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}


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
function updateQuiz($quiz_id, $title, $description, $max_attempts) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE quizzes SET title = :title, description = :description, max_attempts = :max_attempts WHERE quiz_id = :quiz_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':max_attempts', $max_attempts);
        $stmt->bindParam(':quiz_id', $quiz_id);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

function updateQuestion($question_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE questions SET question_text = :question_text, question_type = :question_type, option_a = :option_a, option_b = :option_b, option_c = :option_c, option_d = :option_d, correct_answer = :correct_answer, difficulty = :difficulty WHERE question_id = :question_id");
        $stmt->bindParam(':question_text', $question_text);
        $stmt->bindParam(':question_type', $question_type);
        $stmt->bindParam(':option_a', $option_a);
        $stmt->bindParam(':option_b', $option_b);
        $stmt->bindParam(':option_c', $option_c);
        $stmt->bindParam(':option_d', $option_d);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':difficulty', $difficulty);
        $stmt->bindParam(':question_id', $question_id);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

function insertQuestion($quiz_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, difficulty) VALUES (:quiz_id, :question_text, :question_type, :option_a, :option_b, :option_c, :option_d, :correct_answer, :difficulty)");
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->bindParam(':question_text', $question_text);
        $stmt->bindParam(':question_type', $question_type);
        $stmt->bindParam(':option_a', $option_a);
        $stmt->bindParam(':option_b', $option_b);
        $stmt->bindParam(':option_c', $option_c);
        $stmt->bindParam(':option_d', $option_d);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':difficulty', $difficulty);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

function getQuizID($pdo, $quiz_id) {
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>


