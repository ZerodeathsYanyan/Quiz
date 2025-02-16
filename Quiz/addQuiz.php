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
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
</head>
<body>
   
</body>
</html>