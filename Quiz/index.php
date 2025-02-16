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
        <h1>Header</h1>
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
      <p>This is your landing page. You can access student-specific resources here.</p>
    </div>
    <div class="footer">
      <p>Footer</p>
    </div>
  </div>


  <!-- Teacher side -->
<?php elseif ($usertype === 'teacher'): ?>
  <div class="teacher">
    <div class="header">
      <div class="header-left">
        <button id="menuButton">☰</button>
      </div>
      <div class="header-center">
        <h1>Header</h1>
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
      <p>This is your landing page. You can access teacher-specific resources here.</p>
     
        <a href="#"><div class="Create" >Create Quiz</div></a>
      
    </div>
    <div class="footer">
      <p>Footer</p>
    </div>
  </div>
<?php else: ?>
  <p>Error: Unknown user type.</p>
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