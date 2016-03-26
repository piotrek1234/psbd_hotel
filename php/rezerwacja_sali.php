<?php
require_once('connect_to_database.inc.php');
require_once 'klient_czy_zalogowany.php';
require_once 'funkcje.php';

$wiadomosc = "";

//$idKlienta = 1; // pobrane getem

$do = '4000-12-30';
$od = '1800-01-01';
/*$do = '2015-01-15';
$od = '2014-12-30';*/
$sort = 'numerPomieszczenia'; // pobierane getem
$liczbaOsob = 0;

if(isset($_GET['sort']))
{
	$sort = $_GET['sort'];
}

if(isset($_POST['szukaj']))
{
	$odBool = false;
	$doBool = false;
	
	if($_POST['data_przyjazdu'] != "")
	{
		if(sprCzyData($_POST['data_przyjazdu']))
		{
			//$od = $_POST['data_przyjazdu'];
			$odBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: data od";
		}
	}
	if($_POST['data_odjazdu'] != "")
	{	
		if(sprCzyData($_POST['data_odjazdu']))
		{
			//$do = $_POST['data_odjazdu'];
			$doBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: data do";
		}
	}
	if($_POST['liczba_osob'] != "")
	{
		if(czyCalkowita($_POST['liczba_osob']))
		{
			$liczbaOsob = $_POST['liczba_osob'];
		}
		else
		{
			echo $wiadomosc = "zły format: liczba osób";
		}
	}
	
	if($odBool && $doBool)
	{
		if(czyRoznicaDatOk($_POST['data_przyjazdu'], $_POST['data_odjazdu']))
		{
			$od = $_POST['data_przyjazdu'];
			$do = $_POST['data_odjazdu'];
		}
		else
		{
			echo $wiadomosc = "data od późniejsza od daty do";
		}
	}
}
if(isset($_POST['zloz']))
{
	if($_POST['data_przyjazdu'] != "")
	{
		$od = $_POST['data_przyjazdu'];
	}
	if($_POST['data_odjazdu'] != "")
	{
		$do = $_POST['data_odjazdu'];
	}
}

if(isset($_POST['zloz']) && isset($_POST['sala']))
{
	$numerPomieszczenia = $_POST['sala'];
	$zapytanie_zloz = 	"insert into rezerwacje (idRezerwacji, stan, zaliczka, okresOd, okresDo, idKlienta, numerPomieszczenia)
						values (NULL, '1', NULL, '$od', '$do', '$idKlienta', '$numerPomieszczenia')";
						
	if(!$zapytanie_zloz_run = mysql_query($zapytanie_zloz))
	{
		echo 'blad zaptyania insert';
		echo mysql_error();
	}
	else
		$komunikat = "<div class=\"alert alert-success\">Pomyślnie zarezerwowano salę</div>";
}

$zapytanie_glowne = "select numerPomieszczenia, idSali, iloscMiejsc, cena*datediff('$do', '$od') as koszty 
					from pomieszczenia join saleKonferencyjne using (idSali)
					where
					numerPomieszczenia not in
					(
					select distinct numerPomieszczenia
					from rezerwacje
					where
					(
					(('$do' between okresOd and okresDo)and('$do' <> okresOd))
					or
					(('$od' between okresOd and okresDo)and('$od' <> okresDo))
					or
					((okresOd>='$od')and(okresDo<='$do'))
					)
					)
					and czySala = 1 and iloscMiejsc >= $liczbaOsob
					order by $sort";

if(!$zapytanie_glowne_run = mysql_query($zapytanie_glowne))
{
  echo 'blad zaptyania glownego';
  echo mysql_error();
}

function addSelect($zapytanie)
{
	while($zapytanie_row = mysql_fetch_assoc($zapytanie))
	{
		//echo $zapytanie_row['koszty'];
		$numer = $zapytanie_row['numerPomieszczenia'];
		$count = $zapytanie_row['iloscMiejsc'];
		if(isset($_POST['data_przyjazdu']) && ($_POST['data_przyjazdu'] != '')) $cost = $zapytanie_row['koszty'];
			else $cost = '';
		$idsali = $zapytanie_row['idSali'];
		//$cost = 2;
		//echo $cost;
		//echo "tutaj";
		
		$facilities = facility($idsali);
		
		addNewRow($numer, $count, $facilities, $cost);
	}
}

function facility($idSali)
{
	$zapytanie = 	"select nazwa
					from saleKonferencyjne join sale_wyposazenie using (idSali) join wyposazenieSali using (idWyposazenia)
					where idSali = $idSali";
	
	if(!$zapytanie_run = mysql_query($zapytanie))
	{
		echo 'blad zapytania o wyposażenie';
		echo mysql_error();
	}
	
	$wynik = "";
	$i = 0;

	while($use_query_row = mysql_fetch_assoc($zapytanie_run))
	{
		$wynik[$i] = $use_query_row['nazwa'];
		//echo "adsf";
		$i++;
	}
	
	if($wynik == "")
	{
		return "";
	}
	
	return implode(", ", $wynik);
}
	
function addNewRow($Number, $Count, $Facilities, $Cost)
{
  echo "<tr>";
  echo "<td><input type='radio' name='sala' value=\"$Number\" required>$Number</td>";
  echo "<td>$Count</td>";
  echo "<td>$Facilities</td>";
  echo "<td>$Cost</td>";
  echo "</tr>";
}
?>

<div class="container">
<h1><center>Rezerwacja sali</center></h1>
<div class="panel panel-default panel-body">
<form action="rezerwacja_sali.php" method="POST">
<div class="well">
<table style="width: 35%">
       <tr>
           <td>Data (od)</td>
           <td style="width: 180px"><input type="text" name="data_przyjazdu" style="width: 180px" value="<?php if(isset($_POST['data_przyjazdu'])) echo $_POST['data_przyjazdu']; ?>"></td>
       </tr>
       <tr>
           <td>Data (do)</td>
           <td style="width: 180px"><input type="text" name="data_odjazdu" style="width: 180px" value="<?php if(isset($_POST['data_odjazdu'])) echo $_POST['data_odjazdu']; ?>"></td>
       </tr>
       <tr>
           <td>Liczba osób</td>
           <td style="width: 180px"><input type="text" name="liczba_osob" style="width: 180px" value="<?php if(isset($_POST['liczba_osob'])) echo $_POST['liczba_osob']; ?>"></td>
       </tr>
       <tr style="text-align: right">
           <td></td>
           <td style="text-align: right"><input type="submit" name="szukaj" value="szukaj" class="btn btn-sm btn-primary"></td>
       </tr>

</table>
</div>
</form>
<form action="rezerwacja_sali.php" method="POST">
<input type="hidden" name="data_przyjazdu" value="<?php if(isset($_POST['data_przyjazdu'])) echo $_POST['data_przyjazdu']; ?>">
<input type="hidden" name="data_odjazdu" value="<?php if(isset($_POST['data_odjazdu'])) echo $_POST['data_odjazdu']; ?>">
<table class="table table-striped">
       <thead><tr>
           <th>Numer sali</th>
           <th>Liczba miejsc</th>
           <th>Wyposażenie</th>
           <th>Koszt</th>
       </tr></thead>
       <?php addSelect($zapytanie_glowne_run);?>
</table>
<center><input type="submit" name="zloz" value="złóż rezerwację" class="btn btn-sm btn-primary"></center>
</form>
</div>
<?php
if(isset($komunikat))
{
	echo $komunikat;
	echo '<meta http-equiv="refresh" content="2; url=panel_klienta.php">';
}
?>
</div>
</body>
</html>