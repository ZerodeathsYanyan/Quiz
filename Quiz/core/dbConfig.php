<?php 
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "quiz"; //eto yung database name
$dsn = "mysql:host={$host}; dbname={$dbname}";

$pdo = new PDO ($dsn, $user, $password);
$pdo -> exec("SET time_zone = '+8:00';");

?>