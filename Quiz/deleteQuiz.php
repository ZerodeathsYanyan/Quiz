<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php';
$usertype = $_SESSION['userType'];  
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
    <link rel="stylesheet" href="design.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="header">
  <div class="header-left">
    <button id="menuButton">☰</button>
  </div>
  <div class="header-center">
    <h1><a href="index.php"><?php echo htmlspecialchars($usertype === 'student' ? 'Student' : 'Teacher'); ?></a></h1>
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

<?php if ($usertype === 'student'): ?>
  <!-- Student Interface -->
  <div class="student">
    <div class="sidemenu">
      <h2>Side Menu</h2>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Quests</a></li>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Leaderboard</a></li>
      </ul>
    </div>
    <p>Sorry, you do not have access to delete quizzes.</p>
  </div>
<?php else: ?>
<!-- Teacher Interface -->
  <div class="teacher">
    <div class="sidemenu">
      <h2>Side Menu</h2>
      <ul>
        <li><a href="#">Link 1</a></li>
        <li><a href="#">Link 2</a></li>
        <li><a href="#">Link 3</a></li>
      </ul>
    </div>
     <div class="content">
    <h2>Are you sure you want to delete this Quiz?</h2>   
    <center>
    <?php 
    $getQuizID = getQuizID($pdo, $_GET['quiz_id']); 
    if ($getQuizID): ?>
      <div class="container delete">
        <h3>Quiz Title: <?php echo htmlspecialchars($getQuizID['title']); ?></h3>
        <h3>Description: <?php echo htmlspecialchars($getQuizID['description']); ?></h3>
        <div class="dltbtn">
        <form action="core/handleForms.php" method="POST">
          <input type="hidden" name="quiz_id" value="<?php echo $_GET['quiz_id']; ?>">
          <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($username); ?>">
          <input type="submit" name="deleteQuizBtn" value="Confirm">
        </form>

        </div>  
          
      </div>
        
    <?php else: ?>
      <p>Quiz not found.</p>
    <?php endif; ?>
    </center>
     </div>
     
  </div>
  <div class="footer">
      <p>Footer</p>
    </div>
<?php endif; ?>
</body>
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
</html>
