<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
   header("Location: login.php");
   exit();
}
$usertype = $_SESSION['userType'];  
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="stylesheet" href="design.css?v=<?php echo time(); ?>">
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Student Side -->
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
    <div class="content">
      <h1>Welcome, Student <?php echo $username; ?>!</h1>
      <?php
      if ($usertype === 'student') {
          echo "<h2>Available Quizzes</h2>";

          $stmt = $pdo->query("SELECT quizzes.quiz_id, quizzes.title, quizzes.description, quizzes.created_at, quizzes.max_attempts, users.username AS teacher FROM quizzes JOIN users ON quizzes.teacher_id = users.user_id");

          while ($quiz = $stmt->fetch(PDO::FETCH_ASSOC)) {

              $attemptsQuery = $pdo->prepare("SELECT COUNT(*) as attempt_count FROM quiz_attempts WHERE quiz_id = ? AND user_id = ?");
              $attemptsQuery->execute([$quiz['quiz_id'], $_SESSION['user_id']]);
              $attemptsResult = $attemptsQuery->fetch(PDO::FETCH_ASSOC);
              $attempt_count = $attemptsResult['attempt_count'];

              ?>
              <a href='takeQuiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>'>
                  <div class='quiz'>
                      <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                      <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                      <p><strong>Teacher:</strong> <?php echo htmlspecialchars($quiz['teacher']); ?></p>
                      <p><strong>Created at:</strong> <?php echo htmlspecialchars($quiz['created_at']); ?></p>
                      <p>Attempts left: <?php echo ($quiz['max_attempts'] - $attempt_count); ?></p> 
                  </div>
              </a>
              <?php
          }
      }
      ?>
    </div>
    <div class="footer">
      <p>Footer</p>
    </div>
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
      <h1>Welcome, Teacher <?php echo $username; ?>!</h1>
      <h2>Your Quizzes</h2>
      <?php
      $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE teacher_id = (SELECT user_id FROM users WHERE username = ?)");
      $stmt->execute([$username]);

      while ($quiz = $stmt->fetch(PDO::FETCH_ASSOC)) {
          ?>
          <div class='quiz'>
              <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
              <p><?php echo htmlspecialchars($quiz['description']); ?></p>
              <p><strong>Created at:</strong> <?php echo htmlspecialchars($quiz['created_at']); ?></p>
              <a href='editQuiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>'>Edit</a> |
              <a href='deleteQuiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>'>Delete</a>
          </div>
          <?php
      }
      ?>
      <a href="addQuiz.php"><div class="Create">Create Quiz</div></a>
    </div>
    <div class="footer">
      <p>Footer</p>
    </div>
  </div>
<?php endif; ?>

<script>
  document.getElementById('menuButton').addEventListener('click', function() {
    <?php if ($usertype === 'student'): ?>
      document.querySelector('.student').classList.toggle('sidebar-active');
    <?php elseif ($usertype === 'teacher'): ?>
      document.querySelector('.teacher').classList.toggle('sidebar-active');
    <?php endif; ?>
  });

  document.getElementById('userIcon').addEventListener('click', function() {
    document.getElementById('transparentBox').classList.toggle('show');
  });
</script>
</body>
</html>
