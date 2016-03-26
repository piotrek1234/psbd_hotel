<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';

$sort = 'numer';

if(isset($_GET["sort"]))
{
	$sort = $_GET["sort"];
}

$query = "SELECT idrezerwacji as numer, okresOd, okresDo, numerPomieszczenia, nazwa as stan
			FROM rezerwacje JOIN stanyrezerwacji USING (stan)
			WHERE idKlienta = $idKlienta
			ORDER BY $sort";
			
if(!$query_run = mysql_query($query))
{
  echo 'blad zaptyania 1';
  echo mysql_error();
}		

function addNewRow($Number, $From, $To, $Room, $Condition)
{
  echo "<tr>";
  echo "<td>$Number</td>";
  echo "<td>$From</td>";
  echo "<td>$To</td>";
  echo "<td>$Room</td>";
  echo "<td>$Condition</td>";
  echo "</tr>";
}

function addOptions($use_query)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $number = $use_query_row['numer'];
    $from = $use_query_row['okresOd'];
	$to = $use_query_row['okresDo'];
	$room = $use_query_row['numerPomieszczenia'];
	$condition = $use_query_row['stan'];
    addNewRow($number, $from, $to, $room, $condition);
  }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	  <script src="bootstrap/js/bootstrap.min.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div class="container">
<h1><center>Historia rezerwacji</center></h1>
<div class="panel panel-default panel-body">
<form action="historia_rezerwacji.php" method="GET">
<table style="width: 100%">
       <tr>
           <td><a href='historia_rezerwacji.php?sort=numer'>Numer</a></td>
           <td><a href='historia_rezerwacji.php?sort=okresOd'>Data zakwaterowania</a></td>
           <td><a href='historia_rezerwacji.php?sort=okresDo'>Data wykwaterowania</a></td>
           <td><a href='historia_rezerwacji.php?sort=numerPomieszczenia'>Pomieszczenie</a></td>
           <td><a href='historia_rezerwacji.php?sort=stan'>Stan</a></td>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
	   <?php addOptions($query_run);?>
</table>
</form>
</div></div>
</body>
</html>