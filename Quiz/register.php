<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <link rel="stylesheet" href="design.css?v=<?php echo time(); ?>">
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
</head>
<body>
  <div class="form_container">
    <div class="title">Register</div>

    <form action="core/handleForms.php" method="POST">
    <?php
    if (isset($_GET['username'])){
      $username = $_GET['username'];
      echo '<div><label for="username">Username</label></div>
			<div><input type="text" name="username" value="'.$username.'"></div>';

    }else{
        echo'<div><label for="username">Username</label></div>
			<div><input type="text" name="username"></div>';
    }
    ?>    

			<div><label for="username">Password</label></div>
			<div><input type="password" name="password"></div>
         <div><label for="username">Confirm Password</label></div>
			<div><input type="password" name="confirmPassword"></div>


         <div><label for="username">Are you a Student or a Teacher?</label></div>
         <div><input type="radio" id="student" value="student"name="usertype" checked>
               <label for="student">Student</label></div>
         <div><input type="radio" id="teacher" value="teacher" name="usertype">
               <label for="teacher">Teacher</label></div>
			<div><input type="submit" name="registerBtn"></div>
      <center>Already have an account? Sign up
        <a href="login.php" style="text-decoration: underline; color: blue;">here</a>
      </center>
      <?php
        $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (strpos($link, "register=empty") == true) {
          echo '<span class="error">Fields are empty!</span>';
          exit;
        }
        elseif (strpos($link, "register=pwd") == true) {
          echo '<span class="error">Password is not the same!</span>';
          exit;
        }
        elseif (strpos($link, "register=char") == true) {
          echo '<span class="error">It should only contain alphabetical characters!</span>';
          exit;
        }
        elseif (strpos($link, "register=username") !== false) { 
          echo '<span class="error">Username already exists!</span>';
          exit;
      }
      
        
      ?>
	</form>
  </div>


</body>
</html>