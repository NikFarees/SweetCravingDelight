<?php
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', 'root');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'project_ip_dropship');
// Make the MySQL connection. (similar to = mysql -u root)
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect to MySQL: ' .mysqli_connect_error());
// Select database.
@mysqli_select_db($dbc, DB_NAME) or die('Could not connect to MySQL database: ' . mysqli_error($dbc));
