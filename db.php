<?php
$config = parse_ini_file("db.ini");
$dbh = new PDO($config['dsn'], $config['username'], $config['password']);

echo "Working";
?>