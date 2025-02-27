<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

$usertype = $_SESSION['userType'];  
$username = $_SESSION['username'];

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $user_id = $_SESSION['user_id'];

    // Check the number of attempts the user has made
    $attemptsQuery = $pdo->prepare("SELECT COUNT(*) as attempt_count FROM quiz_attempts WHERE quiz_id = ? AND user_id = ?");
    $attemptsQuery->execute([$quiz_id, $user_id]);
    $attemptsResult = $attemptsQuery->fetch(PDO::FETCH_ASSOC);
    $attempt_count = $attemptsResult['attempt_count'];

    // Fetch quiz details and max attempts
    $quizQuery = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
    $quizQuery->execute([$quiz_id]);
    $quiz = $quizQuery->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        die("Quiz not found.");
    }

    // Handle the case where the user has used up all their attempts
    if ($attempt_count >= $quiz['max_attempts'] && !isset($_GET['submitted']) && !isset($_GET['review'])) {
        header("Location: takeQuiz.php?quiz_id=$quiz_id&review=1");
        exit();
    }
}

// Fetch questions using PDO
$questionsQuery = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questionsQuery->execute([$quiz_id]);
$questions = $questionsQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch all attempts by the user for this quiz if the quiz is reviewed or all attempts are used
if (isset($_GET['review']) || ($attempt_count >= $quiz['max_attempts'])) {
    $attemptsQuery = $pdo->prepare("SELECT attempt_number FROM quiz_attempts WHERE quiz_id = ? AND user_id = ?");
    $attemptsQuery->execute([$quiz_id, $_SESSION['user_id']]);
    $attempts = $attemptsQuery->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
</head>
<body>

<h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($quiz['description'])); ?></p>

<?php if (!empty($quiz['image_path'])): ?>
    <img src="<?php echo htmlspecialchars($quiz['image_path']); ?>" alt="Quiz Image" width="300">
<?php endif; ?>

<?php if (isset($_GET['submitted']) && $_GET['submitted'] == 1 || ($attempt_count >= $quiz['max_attempts'] && !isset($_GET['review']))): ?>
    <h2>Quiz Results</h2>
    <?php
    // Fetch user answers and correct answers
    $answersQuery = $pdo->prepare("SELECT q.question_text, q.correct_answer, a.answer_text 
                                   FROM questions q 
                                   LEFT JOIN answers a ON q.question_id = a.question_id 
                                   WHERE q.quiz_id = ? AND a.user_id = ?");
    $answersQuery->execute([$quiz_id, $_SESSION['user_id']]);
    $results = $answersQuery->fetchAll(PDO::FETCH_ASSOC);

    // Calculate score
    $score = 0;
    foreach ($results as $result) {
        if ($result['answer_text'] === $result['correct_answer']) {
            $score++;
        }
    }
    $totalQuestions = count($questions); // Use the total number of questions
    $percentage = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;
    ?>

    <p><strong>Your Score:</strong> <?php echo $score; ?>/<?php echo $totalQuestions; ?> (<?php echo round($percentage); ?>%)</p>

    <button onclick="location.href='takeQuiz.php?quiz_id=<?php echo $quiz_id; ?>&review=1'">Review Quiz</button>
    <button onclick="location.href='index.php'">Done</button>

<?php elseif (isset($_GET['review']) && $_GET['review'] == 1): ?>
    <h2>Review Quiz</h2>
    <?php foreach ($attempts as $attempt): ?>
        <h3>Attempt <?php echo $attempt['attempt_number']; ?></h3>
        <ul>
            <?php 
            // Fetch user answers for this attempt
            $answersQuery = $pdo->prepare("SELECT q.question_text, q.correct_answer, a.answer_text, q.difficulty 
                                           FROM questions q 
                                           LEFT JOIN answers a ON q.question_id = a.question_id 
                                           WHERE q.quiz_id = ? AND a.user_id = ? AND a.attempt_number = ?");
            $answersQuery->execute([$quiz_id, $_SESSION['user_id'], $attempt['attempt_number']]);
            $results = $answersQuery->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate score for this attempt
            $score = 0;
            foreach ($results as $result) {
                if ($result['answer_text'] === $result['correct_answer']) {
                    $score++;
                }
            }
            $totalQuestions = count($questions); // Use the total number of questions
            $percentage = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;

            foreach ($results as $result): ?>
                <li>
                    <p><strong>Question (<?php echo htmlspecialchars($result['difficulty']); ?>):</strong> <?php echo htmlspecialchars($result['question_text']); ?></p>
                    <p><strong>Your Answer:</strong> <?php echo htmlspecialchars($result['answer_text'] ? $result['answer_text'] : 'No answer'); ?></p>
                    <p><strong>Correct Answer:</strong> <?php echo htmlspecialchars($result['correct_answer']); ?></p>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Score for Attempt <?php echo $attempt['attempt_number']; ?>:</strong> <?php echo $score; ?>/<?php echo $totalQuestions; ?> (<?php echo round($percentage); ?>%)</p>
    <?php endforeach; ?>

    <button onclick="location.href='index.php'">Done</button>
<?php else: ?>
    <form action="core/handleForms.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <?php
        $questionNumber = 1;
        if (!empty($questions)) { 
            foreach ($questions as $question): ?>
                <div class="question">
                    <p><strong>Question <?php echo $questionNumber; ?>:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>

                    <?php if ($question['question_type'] === 'multiple_choice'): ?>
                        <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="A"> <?php echo htmlspecialchars($question['option_a']); ?></label><br>
                        <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="B"> <?php echo htmlspecialchars($question['option_b']); ?></label><br>
                        <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="C"> <?php echo htmlspecialchars($question['option_c']); ?></label><br>
                        <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="D"> <?php echo htmlspecialchars($question['option_d']); ?></label><br>
                    <?php endif; ?> 

                </div>
                <hr>
            <?php 
            $questionNumber++;
            endforeach; 
        } else {
            echo "<p>No questions found for this quiz.</p>";
        }
        ?>
        <button type="submit" name="submitQuiz">Submit Quiz</button>
    </form>
<?php endif; ?>

</body>
</html>
