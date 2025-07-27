<?php 
$db_user = "root";
$db_pass = "";
$db_name = "practice1";

// ✅ Correct PDO connection string
$db = new PDO("mysql:host=localhost;dbname=$db_name;charset=utf8", $db_user, $db_pass);

// ✅ Set error mode
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>