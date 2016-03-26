<?php
require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once "funkcje.php";

$wiadomosc = "";

function sprDaty($data)
{
  $array = explode('-', $data);
  $num = count($array);

  if($num == 3)
  {
    return checkdate($array[1], $array[2], $array[0]);
  }
  return false;
}

if(isset($_GET['sort']))
{
  $sort = $_GET['sort'];
}
else
{
  $sort = 'Numer';
}

$od = '0000-00-00';
$do = '9999-12-30';
$stan = 0;
$nazwisko = '%';
$numer_od = 0;
$numer_do = 1000000;

  $is_od = false;
  $is_do = false;
  $is_numer_od = false;
  $is_numer_do = false;
  $is_nazwisko = false;
  $od_poprawnie = true;
  $do_poprawnie = true;

if(isset($_POST["filtruj"]))
{
  $numer_od = 0;
  $numer_do = 100000;
  $od = $_POST["data_poczatkowa"];
  $do = $_POST["data_koncowa"];
  $stan = $_POST["stan"];
  if(isset($_POST["num_pom_od"])) $numer_od = $_POST["num_pom_od"];
  if(isset($_POST["num_pom_do"])) $numer_do = $_POST["num_pom_do"];
  $naz = $_POST["nazw"];
  $nazwisko = '%'.$_POST["nazw"].'%';
  //do sprawdzania czy wpisać dane
  $is_od = true;
  $is_do = true;
  $is_numer_od = true;
  $is_numer_do = true;
  $is_nazwisko = true;

  //echo "$numer_od";
  
  if($od == "")
  {
    $is_od = false;
    $od = '0001-01-01';
  }
  if($do == "")
  {
    $is_do = false;
    $do = '9999-12-30';
  }
  
  if($nazwisko == "")
  {
    $is_nazwisko = false;
    $nazwisko = '%';
  }
  
 
  
  if($numer_od == "")
  {
    $is_numer_od = false;
    $numer_od = 0;
  }
  if($numer_do == "")
  {
    $is_numer_do = false;
    $numer_do = 1000000;
  }
  
  if(!czyCalkowita($numer_od))
  {
	$is_numer_od = false;
    $numer_od = 0;
	echo $wiadomosc = "zły format: numer od";
  }
  
  if(!czyCalkowita($numer_do))
  {
	$is_numer_do = false;
    $numer_do = 1000000;
	echo $wiadomosc = "zły format: numer do";
  }
  
  if($is_numer_do && $is_numer_od)
  {
	if(!czyOdDo($numer_od, $numer_do))
	{
		$is_numer_od = false;
		$numer_od = 0;
		$is_numer_do = false;
		$numer_do = 1000000;
		$wiadomosc = "numer od większy od numeru do";
	}
  }
  
  if(!sprCzyData($od))
  {
	echo $wiadomosc = "zły format: data od";
	$od = '0001-01-01';
	$is_od = false;
  }
  
  if(!sprCzyData($do))
  {
	echo $wiadomosc = "zły format: data do";
	$is_do = false;
    $do = '9999-12-30';
  }
  
  if($is_od && $is_do)
  {
	if(czyRoznicaDatOk($od, $do))
	{
		
	}
	else
	{
		$do = '9999-12-30';
		$od = '0001-01-01';
		$is_od = false;
		$is_do = false;
		echo $wiadmosco = "data od późniejsza od daty do";
	}
  }

}
else{
$od = '0000-00-00';
$do = '9999-12-30';
$stan = 0;
$nazwisko = '%';
$numer_od = 0;
$numer_do = 1000000;

  $is_od = false;
  $is_do = false;
  $is_numer_od = false;
  $is_numer_do = false;
  $is_nazwisko = false;
  $od_poprawnie = true;
  $do_poprawnie = true;
}

$query_help = "SELECT stan, nazwa FROM stanyrezerwacji;";

if(!$query_help_run = mysql_query($query_help))
{
  echo 'blad zapytania help';
}
/*echo $numer_od;
echo $numer_do;*/
if($stan != 0)
{
    $query="SELECT idRezerwacji AS Numer, numerPomieszczenia AS Pomieszczenie, okresOd, okresDo, idKlienta, CONCAT_WS(' ',imie,nazwisko) AS Klient, stan, nazwa
           FROM rezerwacje JOIN klienci USING(idKlienta) JOIN stanyrezerwacji USING(stan)
           WHERE
			(
                (('$do' between okresOd AND okresDo)AND('$do'<>okresOd))
                OR
                (('$od' between okresOd AND okresDo)AND('$od'<>okresDo))
                OR
                ((okresOd>='$od')AND(okresDo<='$do'))
            )
			AND (numerPomieszczenia between $numer_od AND $numer_do)
			AND nazwisko LIKE '$nazwisko' AND stan=$stan ORDER BY $sort";
}
else
{
    $query="SELECT idRezerwacji AS Numer, numerPomieszczenia AS Pomieszczenie, okresOd, okresDo, idKlienta, CONCAT_WS(' ',imie,nazwisko) AS Klient, stan, nazwa
           FROM rezerwacje JOIN klienci USING(idKlienta) JOIN stanyrezerwacji USING(stan)
           WHERE 
			(
                (('$do' between okresOd AND okresDo)AND('$do'<>okresOd))
                OR
                (('$od' between okresOd AND okresDo)AND('$od'<>okresDo))
                OR
                ((okresOd>='$od')AND(okresDo<='$do'))
            )
			AND (numerPomieszczenia between $numer_od AND $numer_do)
		   AND nazwisko LIKE '$nazwisko' ORDER BY $sort";
}

if(!$query_run = mysql_query($query))
{
  echo 'blad zapytania 1';
}

function addNewRowRoom($use_query)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $Number = $use_query_row['Numer'];
    $From = $use_query_row['okresOd'];
    $To = $use_query_row['okresDo'];
    $Room = $use_query_row['Pomieszczenie'];
    $Client = $use_query_row['Klient'];
    $Condition = $use_query_row['nazwa'];
	$idKlienta = $use_query_row['idKlienta'];

    echo "<tr>";
    echo "<td><a href=\"rezerwacja.php?idRezerwacji=$Number\">$Number</a></td>";
    echo "<td>$From</td>";
    echo "<td>$To</td>";
    echo "<td>$Room</td>";
    echo "<td><a href=\"klient.php?idKlienta=$idKlienta\">$Client</a></td>";
    echo "<td>$Condition</td>";
    echo "</tr>";
  }
}

function addOptions($use_query)
{
  echo "<option value=0>Dowolny</option>";

  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $name = $use_query_row['nazwa'];
    $id = $use_query_row['stan'];
    echo "<option value=\"$id\">$name</option>";
  }
}


?>
<!DOCTYPE html>
<div class="container">
<h1><center>Lista rezerwacji</center></h1>
<div class="panel panel-default panel-body">
<form action="lista_rezerwacji.php" method="POST">
<div class="well">
<table style="text-align: left; width: 50%">
       <tr>
           <td style="width: 60%">Data poczatkowa (rrrr-mm-dd):</td>
           <td><input type="text" name="data_poczatkowa" style="width: 120" value=<?php if($is_od&&$od_poprawnie){echo $od;}?>></td>
           <td><?php if(!$od_poprawnie){echo "<font color='red'>niepoprawny format daty</font>";}?></td>
       </tr>
       <tr>
           <td>Data końcowa (rrrr-mm-dd):</td>
           <td><input type="text" name="data_koncowa" style="width: 120" value=<?php if($is_do&&$do_poprawnie){echo $do;}?>></td>
           <td><?php if(!$do_poprawnie){echo "<font color='red'>niepoprawny format daty</font>";}?></td>
       </tr>
       <tr>
           <td>Numer pomieszczenia (od):</td>
           <td><input type="text" name="num_pom_od" style="width: 120" value=<?php if($is_numer_od){echo $numer_od;}?>></td>
       </tr>
       <tr>
           <td>Numer pomieszczenia (do):</td>
           <td><input type="text" name="num_pom_do" style="width: 120" value=<?php if($is_numer_do){echo $numer_do;}?>></td>
       </tr>
       <tr>
           <td>Stan:</td>
           <td>
               <select style="width: 120" name="stan">
                       <?php addOptions($query_help_run)?>
               </select>
           </td>
       </tr>
       <tr>
           <td>Nazwisko klienta zawiera:</td>
           <td><input type="text" name="nazw" style="width: 120" value=<?php if($is_nazwisko){echo $naz;}?>></td>
       </tr>
       <tr>
           <td></td>
           <td><input type="submit" name="filtruj" value="filtruj" style="width: 120"></td>
       </tr>
</table>
</div>
<table style="width: 100%">
       <tr style="border-bottom: 1px solid #bbbbbb;">
           <td><a href='lista_rezerwacji.php?sort=numer'>Numer</a></td>
           <td><a href='lista_rezerwacji.php?sort=okresOd'>Od</a></td>
           <td><a href='lista_rezerwacji.php?sort=okresDo'>Do</a></td>
           <td><a href='lista_rezerwacji.php?sort=Pomieszczenie'>Pomieszczenie</a></td>
           <td><a href='lista_rezerwacji.php?sort=klient'>Klient</a></td>
           <td><a href='lista_rezerwacji.php?sort=stan'>Stan</a></td>
       </tr>
       <?php addNewRowRoom($query_run);?>
</table>
</form>
</div>
</div>
</body>
</html>