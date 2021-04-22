<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<style>
canvas {
    border:1px solid #d3d3d3;
    background-color: #f1f1f1;
}

.center {
	margin-left: auto;
	margin-right: auto;
}
#grad1 {
  height: 300px;
  background-color: grey; 
  background-image: linear-gradient(to bottom  , grey, white);
}

.button {
  border: none;
  color: white;
  padding: 16px 50px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}

.button1 {
  background-color: #04BD24;
  border-radius: 40px;
  color: black;
  border: 4px solid black;
  padding: 20px;
  text-align:center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  position: relative; left:auto;
}

.divSect{   
	text-align: center;
}
</style>
</head>
<body>

<div id="grad1"></div>
	

<div class="divSect">
	<?php
	try {
 $config = parse_ini_file("db.ini");
 $dbh = new PDO($config['dsn'], $config['username'],$config['password']);
 $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 echo "<table class = 'center' border='1'>";
 echo "<TR>";
 echo "<TH> Name </TH> ";
 echo "<TH> Score </TH>";
 echo "</TR>";

 foreach ( $dbh->query("SELECT name, score FROM leaderboard ORDER BY score LIMIT 10") as $row ) {

 echo "<TR>";
 echo "<TD>".$row[0]."</TD>";
 echo "<TD>".$row[1]."</TD>";

 echo "</TR>";

 echo '</form>';
 }

 echo "</table>";
} catch (PDOException $e) {
 print "Error!" . $e->getMessage()."<br/>";
 die();
}
?>
<p style = "font-family:Arial, Helvetica, sans-serif;font-size:16px;font-style:normal;"> <b>LEADERBOARD</b> </p>
<button class="button button1" onClick="window.location='StartGameResize.html';">Go Back</button>
</div>

</body>
</html>
<?php


