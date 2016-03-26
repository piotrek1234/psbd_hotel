<?php
require_once('connect_to_database.inc.php');
require_once 'pracownik_czy_zalogowany.inc.php';

////////////////////////////////////dodanie wyposazenia
if(isset($_POST['dodaj'])&&isset($_POST['dodaj_wyposazenie']))
{
	$nazwa = $_POST['dodaj_wyposazenie'];
	
	if(($nazwa != "" ))
	{
		$zapytanie_dodaj = "insert into wyposazeniesali (idWyposazenia, nazwa) values (NULL, '$nazwa')";
		
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
	$zapytanie_usun = "delete from wyposazeniesali where idWyposazenia = $idWyposazenia";
	if(!$zapytanie_usun_run = mysql_query($zapytanie_usun))
	{
	  $wiadomosc = "Nie można usunac, część wyposażenia istniejącej sali";
	}
	
	$page = $_SERVER['PHP_SELF'];
	$sec = "1";
	//echo $page;
	header("Refresh: $sec; url=$page");
	
}
//////////////////////////////

/////////////////////////// drukowanie wyposazenia
$zapytanie_glowne = "select idWyposazenia, nazwa from wyposazeniesali";


if(!$zapytanie_glowne_run = mysql_query($zapytanie_glowne))
{
  echo 'blad zaptyania główne<br>';
  echo mysql_error();
}

function addOptions($zapytanie_run)
{
	while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
	{
		$idWyposazenia = $zapytanie_row['idWyposazenia'];
		$nazwa = $zapytanie_row['nazwa'];
		
		addRow($nazwa, $idWyposazenia);
	}
}

function addRow($nazwa, $id){
  echo "<tr>";
  echo "<td style='width:50'>$nazwa</td>";
  echo "<td style='width:50'><a href='wyposazenie_sali.php?usun=$id'>usuń</a></td>";
  echo "</tr>";
}

/////////////////////////////////////
?>

<div class="container">
<h1><center>Wyposażenie sali</center></h1>
<div class="panel panel-default panel-body">
<table style="width: 35%; text-align: left" class="table-striped">
       <thead><tr>
           <th colspan="2">Nazwa wyposażenia</th>
       </tr></thead>
       <?php addOptions($zapytanie_glowne_run)?>
</table>
</div>
<div class="well">
<form action="wyposazenie_sali.php" method="POST">
<table style="width: 50%">
       <tr style="text-align: left">
           <th>Dodaj wyposażenie:</th>
           <td><input type="text" maxLength="45" name="dodaj_wyposazenie" style="width: 160"></td>
           <td><input type="submit" name="dodaj" value="dodaj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div>
</div>

</body>
</html>