<?php
require_once('connect_to_database.inc.php');
require_once 'pracownik_czy_zalogowany.inc.php';
require_once 'funkcje.php';

////////////////////////////pobranie typu
if(isset($_REQUEST['typ_wyposazenia'])) $typ = $_REQUEST['typ_wyposazenia']; else $typ = 0;

/*if(isset($_POST['typ_wyposazenia']))
{
	$typ = $_POST['typ_wyposazenia'];
}*/
///////////////////////////

/////////////////////////// pobranie typu
$zapytanie_typ_wyposazenia = "select typ, nazwa from typyWyposazenia";

if(!$zapytanie_typ_wyposazenia_run = mysql_query($zapytanie_typ_wyposazenia))
{
  echo 'blad zaptyania o typ<br>';
  echo mysql_error();
}

function addSelect($zapytanie_run, $dom)
{
	if($dom == 0)
		echo "<option value=0 selected>dowolne</option>";
	else
		echo "<option value=0>dowolne</option>";
	
	while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
	{
		$typ = $zapytanie_row['typ'];
		$nazwa = $zapytanie_row['nazwa'];
		if($typ == $dom)
			echo "<option value=$typ selected>$nazwa</option>";
		else
			echo "<option value=$typ>$nazwa</option>";
	}
}
////////////////////////////

////////////////////////////////////dodanie wyposazenia
if(isset($_POST['dodaj'])&&isset($_POST['dodaj_wyposazenie']))
{
	$nazwa = $_POST['dodaj_wyposazenie'];
	
	if(($nazwa != "" ) && ($typ != 0))
	{
		$zapytanie_dodaj = "insert into wyposazeniepokoju (idWyposazenia, typ, nazwa) values (NULL, $typ, '$nazwa')";
		
		if(!$zapytanie_dodaj_run = mysql_query($zapytanie_dodaj))
		{
		  echo 'blad zaptyania dodaj<br>';
		  echo mysql_error();
		}
	}
}
////////////////////////////////////

//////////////////////////////usun
if(isset($_GET['usun']))
{
	$idWyposazenia = $_GET['usun'];
	$zapytanie_usun = "delete from wyposazeniepokoju where idWyposazenia = $idWyposazenia";
	if(!$zapytanie_usun_run = mysql_query($zapytanie_usun))
	{
		$wiadomosc = "nie można usunac bo jest częścią wyposażenia istniejącego pokoju";
	}
	
	$page = $_SERVER['PHP_SELF'];
	$sec = "1";
	//echo $page;
	header("Refresh: $sec; url=$page");
	
}
//////////////////////////////

/////////////////////////// drukowanie wyposazenia
$zapytanie_glowne = "";

if($typ == 0)
{
	$zapytanie_glowne = "select idWyposazenia, nazwa from wyposazeniepokoju";
}
else
{
	$zapytanie_glowne = "select idWyposazenia, nazwa from wyposazeniepokoju where typ = $typ";
}

if(!$zapytanie_glowne_run = mysql_query($zapytanie_glowne))
{
  echo 'blad zaptyania główne<br>';
  echo mysql_error();
}

function addOptions($zapytanie_run, $typ)
{
	while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
	{
		$idWyposazenia = $zapytanie_row['idWyposazenia'];
		$nazwa = $zapytanie_row['nazwa'];
		
		addRow($nazwa, $idWyposazenia, $typ);
	}
}

function addRow($nazwa, $id, $typ){
  echo "<tr>";
  echo "<td style='width:50'>$nazwa</td>";
  echo "<td style='width:50'><a href='wyposazenie_pokoju.php?usun=$id&typ_wyposazenia=$typ'>usuń</a></td>";
  echo "</tr>";
}

/////////////////////////////////////
?>

<div class="container">
<h1><center>Wyposażenie pokoju</center></h1>

<form action="wyposazenie_pokoju.php" method="POST">
<div class="panel panel-default panel-body">
<table style="width: 50%">
       <tr style="text-align: left">
           <th>Typ wyposażenia:</th>
           <td><select name="typ_wyposazenia" style="width: 160">
           <?php addSelect($zapytanie_typ_wyposazenia_run, $typ);?>
           </select></td>
           <td><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</div>
<table style="width: 35%; text-align: left" class="table-striped">
       <thead><tr>
           <th colspan="2">Nazwa wyposażenia</th>
       </tr></thead>
       <?php addOptions($zapytanie_glowne_run, $typ)?>
</table>
<br>
<div class="well">
<table style="width: 50%">
       <tr style="text-align: left">
           <th>Dodaj wyposażenie:</th>
           <td><input type="text" name="dodaj_wyposazenie" maxLength= "45" style="width: 160"></td>
           <td><input type="submit" name="dodaj" value="dodaj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</div>
</form>
</div></div>
</body>
</html>