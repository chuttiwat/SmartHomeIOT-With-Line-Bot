<?php
$host = "localhost";    

$user = "XXXXXXXXXXXXXXXXX";    

$pass = "XXXXXXXXXXXXXXXXX";    

$db = "XXXXXXXXXXXXXXXXX";    

 

mysql_connect($host, $user, $pass) or die("Could not connect to database"); 

mysql_select_db($db) or die("Could not connect to database"); 

mysql_query("SET NAMES utf8")


?>