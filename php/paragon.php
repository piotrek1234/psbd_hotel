<?php
require_once "connect_to_database.inc.php";
//$ID = 1;
$ID = $_REQUEST['idRachunku'];
$date = date("Y-m-d");

$query_update = "UPDATE rachunki SET dataWystawienia='$date' WHERE idRachunku=$ID";
// i zamknąć rachunek

if(!$query_update_run = mysql_query($query_update))
{
  echo 'blad aktualizacji 1';
}

$query = "SELECT imie, nazwisko, adreskraj, adresmiasto, adresulica, adreskod, nip, kosztpomieszczenia, znizka, czyfaktura, SUM(cena*ilosc)+kosztPomieszczenia as Suma
         FROM pozycjerachunkow JOIN uslugi USING(iduslugi) JOIN rachunki USING(idrachunku) JOIN klienci USING(idklienta) WHERE idrachunku = $ID";
         
if(!$query_run = mysql_query($query))
{
  echo 'blad zapytania 1';
}

$query_row = mysql_fetch_assoc($query_run);

$query_services = "SELECT uslugi.nazwa AS Usluga, cena, ilosc, (cena*ilosc) AS Koszt
                  FROM pozycjerachunkow JOIN uslugi USING(iduslugi) WHERE idrachunku=$ID";

if(!$query_services_run = mysql_query($query_services))
{
  echo 'blad zapytania 2';
}

$query_room = "SELECT numerPomieszczenia, okresOd, okresDo, kosztPomieszczenia, zaliczka FROM rachunki
              JOIN klienci USING(idKlienta) JOIN rezerwacje USING(idKlienta) WHERE idRachunku = $ID and stan = 4 limit 1";

if(!$query_run_room = mysql_query($query_room))
{
  echo 'blad zapytania1';
}

//
$query_zal = mysql_fetch_assoc($query_run_room);
$zaliczka = $query_zal['zaliczka'];
//

if(!$query_run_room = mysql_query($query_room))
{
  echo 'blad zapytania1';
}

function addNewRows($use_query)
{
while($use_query_row = mysql_fetch_assoc($use_query))
{
     $Service = $use_query_row['Usluga'];
     $Count = $use_query_row['ilosc'];
     $Cost = $use_query_row['Koszt'];
    echo "<tr>";
    echo "<td>$Service</td>";
    echo "<td>$Count</td>";
    echo "<td>$Cost</td>";
    echo "</tr>";
}
}

function addZaliczka($ile)
{
	echo "<tr><td>Zaliczka</td><td></td><td>-$ile</td></tr>";
}

function addNewRowsRoom($use_query)
{
while($use_query_row = mysql_fetch_assoc($use_query))
{    
     $Service = $use_query_row['numerPomieszczenia'];
     $from = $use_query_row['okresOd'];
     $to = $use_query_row['okresDo'];
     $Cost = $use_query_row['kosztPomieszczenia'];

    echo "<tr>";
    echo "<td>Pokój $Service (od $from do $to)</td>";
    echo "<td></td>";
    echo "<td>$Cost</td>";
    echo "</tr>";
}
}
$sum=$query_row['Suma'];
$sum -= $zaliczka;
$sum=$sum-$sum*round(($query_row['znizka']/100),2);

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
<div class="panel panel-body">
<table style="width: 60%, text-align: left">
       <tr>
           <td>Data wystawienia: <?php echo $date?></td>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <tr>
           <td>Hotel Tulipan sp. z o.o.</td>
       </tr>
       <tr>
           <td>Akacjowa 23</td>
       </tr>
       <tr>
           <td>04-203, Krynica Zdrój</td>
       </tr>
       <tr>
           <td>NIP: 213264</td>
       </tr>
       <tr><td><hr style="width: 200px"></td></tr>
       <tr>
           <td>Nabywca:</td>
       </tr>
       <tr>
           <td><?php echo $query_row['imie'].' '.$query_row['nazwisko']?></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adreskraj']?></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adresulica']?></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adreskod'].', '.$query_row['adresmiasto']?></td>
       </tr>
       <tr><td><hr style="width: 200"></td></tr>
</table>
<table style="width: 50%; text-align: left">
       <tr>
           <th>Usługa</th>
           <th>Ilość</th>
           <th>Koszt</th>
       </tr>
       <?php addNewRows($query_services_run);?>
       <?php addNewRowsRoom($query_run_room )?>
	   <?php addZaliczka($zaliczka); ?>
       <tr>
           <td>Razem:</td>
           <td></td>
           <td><?php echo $query_row['Suma']?></td>
       </tr>
       <tr>
           <td>Zniżka</td>
           <td></td>
           <td><?php echo $query_row['znizka']?>%</td>
       </tr>
       <tr>
           <th>Po uwzględnieniu zniżki i zaliczki</th>
           <td></td>
           <th><?php echo $sum;?></th>
       </tr>
</table>
</div>
</div>
</body>
</html>