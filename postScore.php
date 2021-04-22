<?php

if (isset($_POST["score"])) {

try { 

 $config = parse_ini_file("db.ini");
 $dbh = new PDO($config['dsn'], $config['username'],$config['password']);
 $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 $statement = $dbh-> prepare("SELECT * FROM leaderboard where name = ?");

 $statement->execute([$_POST['name']]);

 if ($statement->rowCount() == 1) {
    $statement = $dbh->prepare("UPDATE leaderboard set score = ? where name = ?");
    $statement->execute([$_POST['score'], $_POST['name']]);
 }
 else {
    $statement = $dbh-> prepare("INSERT INTO leaderboard values (? , ?);");
 
    $statement->execute([$_POST['name'], $_POST['score']]);
}

} catch (PDOException $e) {
 print "Error!" . $e -> getMessage()."<br/>";
 die();
}
}
?>