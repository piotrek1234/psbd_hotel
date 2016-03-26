<?php
require_once "connect_to_database.inc.php";
//$ID = 1;
$ID = $_REQUEST['idRachunku'];
$vat = 23;
$date = date("Y-m-d");
$numer_faktury = date("Y/m").'/'.$ID;

$query_update = "UPDATE rachunki SET dataWystawienia='$date' WHERE idRachunku=$ID";
//DODAĆ JESZCZE ŻE RACHUNEK JEST ZAMKNIĘTY

$query_if_firm = "SELECT czyFirma, czyFaktura, dataWystawienia FROM rachunki JOIN klienci USING(idklienta) WHERE idrachunku=$ID";

if(!$query_update_run = mysql_query($query_update))
{
  echo 'blad aktualizacji 1';
}

if(!$query_if_firm_run = mysql_query($query_if_firm))
{
  echo 'blad zapytania 3';
}

$query_if_firm_run_row = mysql_fetch_assoc($query_if_firm_run);
$is_firm = $query_if_firm_run_row['czyFirma'];

if($is_firm == 0)
{
     $query = "SELECT imie, nazwisko, adreskraj, adresmiasto, adresulica, adreskod, nip, kosztpomieszczenia, znizka, czyfaktura, SUM(cena*ilosc)+kosztPomieszczenia as Suma
              FROM pozycjerachunkow JOIN uslugi USING(iduslugi) JOIN rachunki USING(idrachunku) JOIN klienci USING(idklienta) WHERE idrachunku = $ID";
}else
{
     $query = "SELECT nazwaFirmy, adreskraj, adresmiasto, adresulica, adreskod, nip, kosztpomieszczenia, znizka, czyfaktura, SUM(cena*ilosc)+kosztPomieszczenia as Suma
              FROM pozycjerachunkow JOIN uslugi USING(iduslugi) JOIN rachunki USING(idrachunku) JOIN klienci USING (idklienta) WHERE idrachunku=$ID";
}
if(!$query_run = mysql_query($query))
{
  echo 'blad zapytania 1';
}

$query_row = mysql_fetch_assoc($query_run);

if($is_firm == 0)
{
  $nz = $query_row['imie'].' '.$query_row['nazwisko'];
}else
{
  $nz = $query_row['nazwaFirmy'];
}

$query_services = "SELECT uslugi.nazwa AS Usluga, cena, ilosc, (cena*ilosc) AS Koszt
                  FROM pozycjerachunkow JOIN uslugi USING(iduslugi) WHERE idrachunku=$ID";

if(!$query_services_run = mysql_query($query_services))
{
  echo 'blad zapytania 2';
}

$query_room = "SELECT numerPomieszczenia, okresOd, okresDo, kosztPomieszczenia, zaliczka FROM rachunki
              JOIN klienci USING(idKlienta) JOIN rezerwacje USING(idKlienta) WHERE idRachunku = $ID and stan = 4";

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
     $Cost_per_one = $use_query_row['cena'];
    echo "<tr>";
    echo "<td>$Service</td>";
    echo "<td>$Count</td>";
    echo "<td>$Cost_per_one</td>";
    echo "<td>$Cost</td>";
    echo "</tr>";
}
}

function addZaliczka($ile)
{
	echo "<tr><td>Zaliczka</td><td></td><td></td><td>-$ile</td></tr>";
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
    echo "<td colspan='3'>Pokój $Service (od $from do $to)</td>";
    echo "<td>$Cost</td>";
    echo "</tr>";
}
}
$sum=$query_row['Suma'];
$sum -= $zaliczka;
$sum=$sum-round($sum*($query_row['znizka']/100), 2);
$sum_vat = round($sum*($vat/100),2);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	  <script src="bootstrap/js/bootstrap.min.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	  <style type="text/css">
      .table1{ width: 90%;
               text-align: left;
               }
      .table2{ width: 50%;
               text-align: left;
               }
      </style>
</head>
<body>
<div class="container">
<div class="panel panel-body">
<table class="table1">
       <tr>
           <td colspan="3" style="width: 20%">FAKTURA VAT NR <?php echo $numer_faktury?></td>
           <td>Krynica Zdrój, <?php echo date("d.m.y")?></td>
       </tr>
       <tr>
           <th>Sprzedawca:</th>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <tr>
           <td>Hotel Tulipan sp. z o.o.</td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td>Akacjowa 23</td>
           <td></td>
       </tr>
       <tr>
           <td>04-200, Krynica Zdrój</td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td colspan="3">Numer rachunku: 12 3456 7890 1234 5678 9012 3456</td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td>NIP: 213264</td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <tr>
           <th>Nabywca:</th>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <tr>
           <td><?php echo $nz?></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adreskraj']?></td>
           <td></td><td></td>
           <td></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adresulica']?></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td><?php echo $query_row['adreskod'].', '.$query_row['adresmiasto']?></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td>NIP: <?php echo $query_row['nip']?></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr>
           <td colspan="3"><hr style="width: 400"></td>
       </tr>
</table>
<table class="table2">
       <tr>
           <th style="width=30%">Usługa</th>
           <th>Ilosć</th>
           <th style="width=20%">Cena jednostkowa</th>
           <th>Wartosć brutto</th>
       </tr>
       <tr><td></td></tr>
       <tr><td></td></tr>
       <?php addNewRows($query_services_run);?>
       <?php addNewRowsRoom($query_run_room)?>
	   
       <tr>
           <td>Razem:</td>
           <td></td><td></td>
           <td><?php echo $query_row['Suma']?></td>
       </tr>
	   <?php addZaliczka($zaliczka); ?>
       <tr>
           <td>Zniżka</td>
           <td></td><td></td>
           <td><?php echo $query_row['znizka']?>%</td>
       </tr>
       <tr>
           <th>Po uwzględnieniu zniżki</th>
           <td></td><td></td>
           <th><?php echo $sum;?></th>
       </tr>
       <tr>
           <td colspan="3"><hr style="width: 400"></td>
       </tr>
</table>
<table style="width: 50%;text-align: left">
       <tr>
           <th>Stawka VAT</th>
           <th>Wartość netto</th>
           <th>Wartość VAT</th>
           <th>Wartość brutto</th>
       </tr>
       <tr>
           <td><?php echo $vat?>%</td>
           <td><?php echo $sum-$sum_vat;?></td>
           <td><?php echo $sum_vat;?></td>
           <td><?php echo $sum;?></td>
       </tr>
</table>
</div>
</div>
</body>
</html>