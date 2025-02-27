<?php
require_once 'core/dbConfig.php'; 
require_once 'core/models.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
 }
 $usertype = $_SESSION['userType'];  
 $username = $_SESSION['username'];

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo "Invalid quiz ID.";
    exit();
}

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    echo "Quiz not found.";
    exit();
}

// Fetch questions categorized by difficulty
$difficulties = ['easy', 'normal', 'hard'];
$questions = [];
foreach ($difficulties as $difficulty) {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? AND difficulty = ? ORDER BY question_id");
    $stmt->execute([$quiz_id, $difficulty]);
    $questions[$difficulty] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="design.css">
</head>
<body class="addQuiz">
<?php if ($usertype === 'student'): ?>
  <div class="student">
    <div class="header">
      <div class="header-left">
        <button id="menuButton">☰</button>
      </div>
      <div class="header-center">
        <h1><a href="index.php">Header</a></h1>
      </div>
      <div class="header-right">
        <span class="user-icon" id="userIcon">👤</span>
        <span class="notification-bell">🔔</span>
      </div>
      <div class="transparent-box" id="transparentBox">
        <ul>
          <li><a href="#">Dashboard</a></li>
          <li><a href="#">Options</a></li>
          <li><a href="core/handleForms.php?logoutAUser=1">Log Out</a></li>
        </ul>
      </div>
    </div>
    <div class="sidemenu">
      <h2>Side Menu</h2>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Quests</a></li>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Leaderboard</a></li>
      </ul>
    </div>
    <?php else: ?>
<!-- Teacher Side -->
  <div class="teacher">
    <div class="header">
      <div class="header-left">
        <button id="menuButton">☰</button>
      </div>
      <div class="header-center">
      <h1><a href="index.php">Header</a></h1>
      </div>
      <div class="header-right">
        <span class="user-icon" id="userIcon">👤</span>
        <span class="notification-bell">🔔</span>
      </div>
      <div class="transparent-box" id="transparentBox">
        <ul>
          <li><a href="#">Dashboard</a></li>
          <li><a href="#">Options</a></li>
          <li><a href="core/handleForms.php?logoutAUser=1">Log Out</a></li>
        </ul>
      </div>
    </div>
    <div class="sidemenu">
      <h2>Side Menu</h2>
      <ul>
        <li><a href="#">Link 1</a></li>
        <li><a href="#">Link 2</a></li>
        <li><a href="#">Link 3</a></li>
      </ul>
    </div>
    
    <div class="content">
    <div class="form_container">
        <h1>Edit Quiz</h1>
        <form action="handleForms.php" method="post">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <label>Quiz Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>
            
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($quiz['description']) ?></textarea>
            
            <label>Max Attempts:</label>
            <input type="number" name="max_attempts" value="<?= $quiz['max_attempts'] ?>" min="1" required>
            
            <h2>Questions</h2>
            <div class="question-container">
                <?php foreach ($difficulties as $difficulty): ?>
                    <div class="question-column">
                        <h3><?= ucfirst($difficulty) ?></h3>
                        <?php foreach ($questions[$difficulty] as $index => $question): ?>
                            <div class="question-box">
                                <?php if ($difficulty === 'easy' && $index === 0): ?>
                                    <span class="question-number">Q<?= $index + 1 ?></span>
                                <?php endif; ?>
                                <input type="hidden" name="question_ids[<?= $difficulty ?>][]" value="<?= $question['question_id'] ?>">
                                <label>Question Text:</label>
                                <textarea name="questions[<?= $difficulty ?>][]" required><?= htmlspecialchars($question['question_text']) ?></textarea>
                                
                                <label>Question Type:</label>
                                <select name="question_type[<?= $difficulty ?>][]" required>
                                    <option value="multiple_choice" <?= $question['question_type'] == 'multiple_choice' ? 'selected' : '' ?>>Multiple Choice</option>
                                    <option value="fill_in_blank" <?= $question['question_type'] == 'fill_in_blank' ? 'selected' : '' ?>>Fill in the Blank</option>
                                    <option value="problem_solving" <?= $question['question_type'] == 'problem_solving' ? 'selected' : '' ?>>Problem Solving</option>
                                </select>
                                
                                <?php if ($question['question_type'] == 'multiple_choice'): ?>
                                    <label>Options:</label>
                                    <input type="text" name="option_a[<?= $difficulty ?>][]" value="<?= htmlspecialchars($question['option_a']) ?>">
                                    <input type="text" name="option_b[<?= $difficulty ?>][]" value="<?= htmlspecialchars($question['option_b']) ?>">
                                    <input type="text" name="option_c[<?= $difficulty ?>][]" value="<?= htmlspecialchars($question['option_c']) ?>">
                                    <input type="text" name="option_d[<?= $difficulty ?>][]" value="<?= htmlspecialchars($question['option_d']) ?>">
                                    <label>Correct Answer:</label>
                                    <select name="correct_option[<?= $difficulty ?>][]" required>
                                        <option value="A" <?= $question['correct_answer'] == $question['option_a'] ? 'selected' : '' ?>>A</option>
                                        <option value="B" <?= $question['correct_answer'] == $question['option_b'] ? 'selected' : '' ?>>B</option>
                                        <option value="C" <?= $question['correct_answer'] == $question['option_c'] ? 'selected' : '' ?>>C</option>
                                        <option value="D" <?= $question['correct_answer'] == $question['option_d'] ? 'selected' : '' ?>>D</option>
                                    </select>
                                <?php endif; ?>
                                <button type="button" class="delete-set" onclick="deleteQuestionRow(this)">Delete Question Set</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" name="editBtn">Save Changes</button>
        </form>
    </div>
    </div>
    <div class="footer">
      <p>Footer</p>
    </div>
    <?php endif; ?>
    <script>
        function deleteQuestionRow(button) {
            const row = button.closest('.question-box');
            if (row) row.remove();
        }
    </script>
</body>
</html>
