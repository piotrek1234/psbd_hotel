<?php
require_once "connect_to_database.inc.php";

$ID = $_REQUEST['idRachunku'];

if(isset($_POST['zatw'])&&isset($_POST['rodzaj_rachunku'])&&isset($_POST['zaplacono']))
{
  $czy_faktura = $_POST['rodzaj_rachunku'];
  if($czy_faktura=='faktura')
  {
       $CzyFaktura = 1;
  }
  else
  {
       $CzyFaktura = 0;
  }
  $query_update = "UPDATE rachunki SET czyZaplacony = 1, czyFaktura = $CzyFaktura WHERE idRachunku=$ID";
  
  if(!$query_update_run = mysql_query($query_update))
  {
    echo 'blad akturalizacji1';
  }
  else
  {
    if($CzyFaktura == 0)
    {
       header("Location: paragon.php?idRachunku=$ID");
    }else if($CzyFaktura == 1)
    {
       header("Location: faktura.php?idRachunku=$ID");
    }

  }
}

require_once 'pracownik_czy_zalogowany.inc.php';

$query = "SELECT imie, nazwisko, kosztpomieszczenia, znizka, czyfaktura, SUM(cena*ilosc)+kosztPomieszczenia as Suma
         FROM pozycjerachunkow JOIN uslugi USING(iduslugi) JOIN rachunki USING(idrachunku) JOIN klienci USING(idklienta)
         WHERE idrachunku=$ID";

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

if(!$query_run = mysql_query($query))
{
  echo 'blad zapytania2';
}

$query_row = mysql_fetch_assoc($query_run);

$query_bill = "SELECT uslugi.nazwa AS Usluga, cena, ilosc, (cena*ilosc) AS Koszt FROM pozycjerachunkow JOIN uslugi USING(iduslugi) WHERE idrachunku=$ID";

if(!$query_run_bill = mysql_query($query_bill))
{
  echo 'blad zapytania3';
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

function addZaliczka($ile)
{
	echo "<tr><td>Zaliczka</td><td></td><td>-$ile</td></tr>";
}

$room_cost = $query_row['kosztpomieszczenia'];

$sum=$query_row['Suma']; 
//
$sum -= $zaliczka;
//
$sum=$sum-$sum*round(($query_row['znizka']/100),2);

$paragon = '';
$faktura = '';

if($query_row['czyfaktura'] == 1)
{
    $faktura = 'checked';
}
else
{
    $paragon = 'checked';
}
?>

<div class="container">
<h1><center>Rachunek</center></h1>
<div class="panel panel-default panel-body">
<table style="width: 60%; text-align: left" cellspacing="10">
       <tr>
           <th colspan="3"><span class="label label-success"><?php echo $query_row['imie'].' '.$query_row['nazwisko']?></span></th>
       </tr>
       <tr>
           <th>Usługa</th>
           <th>Ilość</th>
           <th>Koszt</th>
       </tr>
       <?php addNewRows($query_run_bill);?>
       <?php addNewRowsRoom($query_run_room);?>
	   <?php addZaliczka($zaliczka); ?>
       <tr>
           <th>Razem:</th>
           <td></td>
           <th><?php echo $query_row['Suma']?></th>
       </tr>
       <tr>
           <td>Uwzględniajac zaliczkę i zniżkę (<?php echo $query_row['znizka']?>%):</td>
           <td></td>
           <td><?php echo $sum;?></td>
       </tr>
</table>
</div>
<div class="panel panel-default panel-body">
<form action="rachunek_28.php?idRachunku=<?php echo $ID; ?>" method="POST">
<table style="width: 50%; text-align: left" cellspacing="10">
       <tr>
           <th>Rodzaj rachunku</th>
           <td><input type="radio" name="rodzaj_rachunku" value="paragon" <?php echo $paragon?> required> paragon</td>
           <td><input type="radio" name="rodzaj_rachunku" value="faktura" <?php echo $faktura?> required> faktura</td>
       </tr>
       <tr>
           <td colspan="2"><input type="checkbox" name="zaplacono" value="zapłacono" required> zapłacono (zamknij rachunek)</td>
           <td><?php
               if(isset($_POST['zatw'])&&isset($_POST['rodzaj_rachunku']))
               {
                  if(!isset($_POST['zaplacono']))
                  {
                    echo "<font color='red'>brak potwierdzenia</font>";
                  }
               }
               ?>
           </td>
       </tr>
       <tr style="text-align: center">
           <td colspan="3"><input type="submit" name="zatw" value="zatwierdź i przejdź do wydruku" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div>
</div>
</body>
</html>