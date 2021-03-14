<?php
$host = "localhost";    /* Host name */
$user = "usreservice";         /* User */
$password = "sVr#019#";         /* Password */
$dbname = "eservice";   /* Database name */

// Create connection
$con = mysqli_connect($host, $user, $password,$dbname);
$con->set_charset("utf8");
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

