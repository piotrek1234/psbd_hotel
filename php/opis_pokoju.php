<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';

$numerPomieszczenia = $_GET['numer'];

$zapytanie = "select opis, kategoria, pojemnosc, nazwa, idPokoju, zdjecie from pomieszczenia join pokoje using(idPokoju) join kategorie on (idkategorii=kategoria) where numerPomieszczenia=$numerPomieszczenia";

if(!$zapytanie_run = mysql_query($zapytanie))
{
	echo 'blad zaptyania glowne';
	echo mysql_error();
}

$zapytanie_row = mysql_fetch_assoc($zapytanie_run);

$opis = $zapytanie_row['opis'];
$kategoria = $zapytanie_row['nazwa'];
$id = $zapytanie_row['idPokoju'];
$zdjecie = $zapytanie_row['zdjecie'];
$pojemnosc = $zapytanie_row['pojemnosc'];

$wyposazenie = facility($id);

//$zdjecie_path = "";

function facility($idpokoju)
{
	$zapytanie = 	"select nazwa
					from pokoje join pokoje_wyposazenie using (idPokoju) join wyposazeniePokoju using (idWyposazenia)
					where idPokoju = $idpokoju";
	
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

?>

<div class="container">
<h1>Pokój numer <?php echo $numerPomieszczenia; ?></h1>
<div class="panel panel-default panel-body">
<div class="row">
<div class="col-md-4"><center>
<?php
echo '<img src="';
if(isset($zdjecie)) echo $zdjecie; else echo 'img/bez_fotki.png';
echo '" class="thumbnail">';
?>
</center></div>
<div class="col-md-8">
<b>Kategoria:</b> <?php echo $kategoria?><br>

<b>Opis:</b> <br>
<?php echo $opis."<br>"?>
<b>Wyposażenie:</b><br> <?php echo $wyposazenie?><br>
<b>Liczba miejsc:</b> <?php echo $pojemnosc; ?>
</div>
</div>
</div>
</div>
</body>
</html>