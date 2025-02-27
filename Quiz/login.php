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
    <div class="title">Welcome</div>
    <span class="subtitle">Sign Up right here!</span>
    <form action="core/handleForms.php" method="POST">
        
			<div><label for="username">Username</label></div>
			<div><input type="text" name="username"></div>
			<div><label for="username">Password</label></div>
			<div><input type="password" name="password"></div>

			<div><input type="submit" name="loginBtn"></div>
	</form>
  <center>Don't have an account? You may register 
  <a href="register.php" style="text-decoration: underline; color: blue;">here</a></center>
    <?php
        $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (strpos($link, "login=correct") == true) {
          header("Refresh: 3; Location: index.php");
          exit;
        }
        elseif (strpos($link, "login=incorrect") == true) {
          echo '<span class="error">Username or Password is incorrect</span>';
          exit;
        }
        elseif (strpos($link, "login=empty") == true) {
          echo '<span class="error">All fields are required</span>';
          exit;
        }
      ?>


  </div>


</body>
</html>