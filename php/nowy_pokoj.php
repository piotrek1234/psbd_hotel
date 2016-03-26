<?php
require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once 'funkcje.php';

$wiadomosc = "";

if(isset($_REQUEST['nrPomieszczenia']))
{
	$czy_edycja = true;
	$g_idPokoju = $_REQUEST['nrPomieszczenia'];
}
else
{
	$czy_edycja = false;
	$g_idPokoju = '';
}

/*$g_idPokoju = 3002;
$czy_edycja = false;*/

$numer_pokoju = "";
$zdjecie = "";

$get_path = "";

if($g_idPokoju != "")
{
	$czy_edycja = true;
	
	$zapytanie_edycja = "SELECT zdjecie FROM pomieszczenia WHERE numerPomieszczenia = $g_idPokoju";
	
	if(!$zapytanie_edycja_run = mysql_query($zapytanie_edycja))
	{
		echo 'blad zaptyania edycja';
	}

	$query_one_row = mysql_fetch_assoc($zapytanie_edycja_run);
	
	$numer_pokoju = $g_idPokoju;
	$zdjecie = $query_one_row['zdjecie'];
}

if(isset($_POST['usun_pokoj'])&&isset($_POST['na_pewno'])&&$czy_edycja)
{
	$zapytanie_usun = "delete from pomieszczenia where numerPomieszczenia = $g_idPokoju";
	
	if(!$zapytanie_usun_run = mysql_query($zapytanie_usun))
	{
		echo 'blad zaptyania usun';
	}
}
// zapis
if(isset($_POST['zapisz'])&&(!$czy_edycja)&&isset($_POST['persons_count']))
{
	$numer = $_POST['nr_pokoju'];
	$zdjecie = $_POST['zdjecie'];
	$idPokoju = $_POST['persons_count'];

	if($numer != "")
	{
		$zapytanie_dodaj = "insert into pomieszczenia (numerPomieszczenia, kluczWRecepcji, zdjecie, idSali, idPokoju, czySala)
							values ($numer, 1, '$zdjecie', NULL, '$idPokoju', 0)";
		
		if(!$zapytanie_dodaj_run = mysql_query($zapytanie_dodaj))
		{
			echo 'blad zaptyania dodaj';
			echo mysql_error();
		}
	}
}

if(isset($_POST['zapisz'])&&($czy_edycja)&&isset($_POST['persons_count']))
{
	$numer = $_POST['nr_pokoju'];
	$zdjecie = $_POST['zdjecie'];
	$idPokoju = $_POST['persons_count'];
	
	//echo "kuku";
	
	if($numer != "")
	{
	$zapytanie_edytuj = "update pomieszczenia set zdjecie = '$zdjecie', idPokoju = $idPokoju where numerPomieszczenia = $g_idPokoju";
	
	if(!$zapytanie_edytuj_run = mysql_query($zapytanie_edytuj))
	{
		echo 'blad zaptyania edytuj';
		echo mysql_error();
	}
	}
}

$sort = 'pojemnosc';
$osobDo = 10000;
$osobOd = 0;
$kierunek = 'asc';
$kategoria = '200';

if(isset($_GET['sort']))
{
	$s = $_GET['sort'];
	if($s == "os")
	{
		$sort = 'pojemnosc';
	}
	else
	{
		$sort = 'kategoria';
	}
}

$zapytanie_lista_rodzajow_pokoju = "";

if(isset($_POST['filtruj']))
{
	$od = $_POST['od'];
	$do = $_POST['do'];
	
	$odBool = false;
	$doBool = false;
	
	if($od != "")
	{
		if(czyCalkowita($od))
		{
			$odBool = true;
			//$osobOd = $od;
		}
		else
		{
			echo $wiadomosc = "zły format: liczba osób od";
		}
	}
	
	if($do != "")
	{
		if(czyCalkowita($do))
		{
			$doBool = true;
			//$osobDo = $do;
		}
		else
		{
			echo $wiadomosc = "zły format: liczba osób do";
		}
	}
	
	if($doBool && $odBool)
	{
		if(czyOdDo($od, $do))
		{
			$osobDo = $do;
			$osobOd = $od;
		}
		else
		{
			echo $wiadomosc = "liczba osób od większa od liczby osób do";
		}
	}
	
	$kategoria = $_POST['Kategoria'];
}

if($kategoria == 200)
{
	$zapytanie_lista_rodzajow_pokoju = "select idPokoju, nazwa as kategoria, pojemnosc
									from pokoje join kategorie on pokoje.kategoria = kategorie.idKategorii
									where pojemnosc >= $osobOd and pojemnosc <= $osobDo
									order by $sort";
}
else
{
$zapytanie_lista_rodzajow_pokoju = "select idPokoju, nazwa as kategoria, pojemnosc
									from pokoje join kategorie on pokoje.kategoria = kategorie.idKategorii
									where pojemnosc >= $osobOd and pojemnosc <= $osobDo
									and kategorie.idKategorii = $kategoria
									order by $sort";
}
	if(!$zapytanie_lista_rodzajow_pokoju_run = mysql_query($zapytanie_lista_rodzajow_pokoju))
	{
		echo 'blad zapytania o lista_rodzajów';
		echo mysql_error();
	}
									
function addSelect()
{
	echo "<option value=200>Dowolna</option>";
	$zaptyanie_kategorie = "SELECT idKategorii, nazwa FROM kategorie";

	if(!$zaptyanie_kategorie_run = mysql_query($zaptyanie_kategorie))
	{
		echo 'blad zapytania o kategorie';
		echo mysql_error();
	}
	
	while($use_query_row = mysql_fetch_assoc($zaptyanie_kategorie_run))
	{
		$name = $use_query_row['nazwa'];
		$id = $use_query_row['idKategorii'];
		echo "<option value=$id>$name</option>";
	}
}

function addOptions($use_query, $edycja, $nr)
{
	if($use_query != false){
		  while($use_query_row = mysql_fetch_assoc($use_query))
		  {
			$persons = $use_query_row['pojemnosc'];
			$category = $use_query_row['kategoria'];
			$idPokoju = $use_query_row['idPokoju'];
			$facility = facility($idPokoju);
			//$facility = $use_query_row['stan'];
			addRow($persons, $category, $facility, $idPokoju, $edycja, $nr);
		  }
    }
}

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
	
	$wynik = array();
	$i = 0;
	while($use_query_row = mysql_fetch_assoc($zapytanie_run))
	{
		$wynik[$i] = $use_query_row['nazwa'];
		$i++;
	}
	
	return implode(",", $wynik);
}

function addRow($persons, $category, $facility, $id, $edycja, $nrPom){

	$temp = 0;
	if($edycja)
	{
		$zapytanie = "SELECT idPokoju FROM pomieszczenia WHERE numerPomieszczenia = $nrPom";
		
		if(!$zapytanie_run = mysql_query($zapytanie))
		{
			echo 'blad zaptyania edycja';
		}

		$row = mysql_fetch_assoc($zapytanie_run);
	
		$temp = $row['idPokoju'];
	}

	
	echo "<tr>";
	if($edycja && ($id == $temp))
	{
		echo "<td><input type='radio' name='persons_count' value=\"$id\" checked required>$persons</td>";
	}
	else
	{
		echo "<td><input type='radio' name='persons_count' value=\"$id\" required>$persons</td>";
	}
	echo "<td>$category</td>";
	echo "<td>$facility</td>";
	echo "</tr>";
}?>

<div class="container">
<h1><center>Nowy pokój</center></h1>

<div class="panel panel-default panel-body">
Rodzaj pokoju (wybierz z listy):
<div class="well">
<form action="nowy_pokoj.php<?php if(isset($_REQUEST['nrPomieszczenia'])) echo '?nrPomieszczenia='.$_REQUEST['nrPomieszczenia'];  ?>" method="POST">
<table width="70%">
       <tr>
           <td>Liczba osób:</td>
           <td>od<input type="text" name="od" style="width: 55px; margin-left: 10px; margin-right: 10px" maxLength="11">
               do<input type="text" name="do" style="width: 55px; margin-left: 10px; margin-right: 10px" maxLength="11"></td>
       </tr>
       <tr>
           <td>Kategoria:</td>
           <td><select name="Kategoria" style="width: 180px">
           <?php addSelect();?>
           </select></td>
       </tr>
       <tr>
           <td colspan="2" style="text-align: center"><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div>

<form action="nowy_pokoj.php<?php if(isset($_REQUEST['nrPomieszczenia'])) echo '?nrPomieszczenia='.$_REQUEST['nrPomieszczenia'];  ?>" method="POST">

<table class="table table-striped">
       <thead><tr>
           <th><a href='nowy_pokoj.php?sort=os'>Liczba osób</a></th>
           <th><a href="nowy_pokoj.php?sort=kat">Kategoria</a></th>
           <th>Wyposażenie</th>
       </tr></thead>
	   <?php addOptions($zapytanie_lista_rodzajow_pokoju_run, $czy_edycja, $g_idPokoju);?>
</table>


<div class="panel panel-default panel-body">
<table width="70%">
       <tr>
           <td width="30%">Numer pokoju</td>
           <td width="70%"><input type="text" name="nr_pokoju" style="width: 180" maxLength="6" value="<?php echo $numer_pokoju;?>" required></td>
       </tr>
       <tr>
           <td width="25%">Zdjęcie</td>
           <td width="50%"><input type="text" name="zdjecie" style="width: 180" maxLength="45" value="<?php echo $zdjecie;?>" required></td>
       </tr>
</table>
</div>
<table style=" width: 85%">
       <tr>
         <td colspan="2"><center><input type="submit" name="zapisz" value="zapisz" class="btn btn-primary btn-sm"></center></td>
       </tr>
</table>
</form>
<?php
//$czyEdycja = 1;
if(isset($czyEdycja)){
echo '<div class="panel panel-warning">
<div class="panel-heading">
Usuwanie pokoju
</div>
<div class="panel-body">
<center><table><tr>';
	   echo '<form action="nowy_pokoj.php?id='.$idPokoju.'" method="POST">';
		echo '<th><input type="checkbox" name="na_pewno" value="na_pewno" required> na pewno&nbsp;</th>';
		echo '<td><input type="submit" name="usun_sezon" value="usuń sezon" class="btn btn-sm btn-warning"></td>';
		echo '<input type="hidden" name="act" value="usun">';
		echo '</form></tr></table></center>
</div>
</div>';
}
?>
</div>
</div>
</body>
</html>