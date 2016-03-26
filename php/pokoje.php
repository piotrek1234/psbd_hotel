<?php
require_once 'connect_to_database.inc.php';
require_once 'pracownik_czy_zalogowany.inc.php';
require_once 'funkcje.php';

$nrOd = 0;
$nrDo = 10000000;
$osobOd = 0;
$osobDo = 100000000;
$cenaOd = 0;
$cenaDo = 100000000;

$tylkoWolne = false;

$wiadomosc = "";

if(isset($_POST['filtruj']))
{
	$nrOdBool = false;
	$nrDoBool = false;
	
	$cenaOdBool = false;
	$cenaDoBool = false;
	
	$osobOdBool = false;
	$osobDoBool = false;

	if($_POST['od_numer'] != "")
	{
		if(czyCalkowita($_POST['od_numer']))
		{
			//$nrOd  = $_POST['od_numer'];
			$nrOdBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: numer od";
		}
	}
	if($_POST['do_numer'] != "")
	{
		if(czyCalkowita($_POST['do_numer']))
		{
			//$nrDo  = $_POST['do_numer'];
			$nrDoBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: numer do";
		}
	}
	if($_POST['od_cena'] != "")
	{
		if(sprCena($_POST['od_cena']))
		{
			//$cenaOd  = $_POST['od_cena'];
			$cenaOdBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: cena od";
		}
	}
	if($_POST['do_cena'] != "")
	{
		if(sprCena($_POST['do_cena']))
		{
			//$cenaDo  = $_POST['do_cena'];
			$cenaDoBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: cena do";
		}
	}
	if($_POST['od_osob'] != "")
	{
		if(czyCalkowita($_POST['od_osob']))
		{
			//$osobOd  = $_POST['od_osob'];
			$osobOdBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: osob od";
		}
	}
	if($_POST['do_osob'] != "")
	{
		if(czyCalkowita($_POST['do_osob']))
		{
			//$osobDo  = $_POST['do_osob'];
			$osobDoBool = true;
		}
		else
		{
			echo $wiadomosc = "zły format: osob do";
		}
	}
	if(isset($_POST['wolne']))
	{
		// tylko wolne pokoje
		$tylkoWolne = true;
	}
	
	if($cenaDoBool && $cenaOdBool)
	{
		if(czyCenaOdDo($_POST['od_cena'], $_POST['do_cena']))
		{
			$cenaDo  = $_POST['do_cena'];
			$cenaOd  = $_POST['od_cena'];
		}
		else
		{
			echo $wiadomosc = "cena od większa od ceny do";
		}
	}
	
	
	if($osobDoBool && $osobOdBool)
	{
		if(czyOdDo($_POST['od_osob'], $_POST['do_osob']))
		{
			$osobOd  = $_POST['od_osob'];
			$osobDo  = $_POST['do_osob'];
		}
		else
		{
			echo $wiadomosc = "liczba osob od większa od liczby osob do";
		}
	}
	
	
	if($nrDoBool && $nrOdBool)
	{
		if(czyOdDo($_POST['od_numer'], $_POST['do_numer']))
		{
			$nrOd  = $_POST['od_numer'];
			$nrDo  = $_POST['do_numer'];
		}
		else
		{
			echo $wiadomosc = "numer pokoju od większa od numeru pokoju do";
		}
	}
}

$sort = 'numerPomieszczenia';

if(isset($_GET['sort']))
{
	$sort = $_GET['sort'];
}

switch(date('w'))
{
	case '6':
		$ktoraCena = 'cenaSobota';
		break;
	case '0':
		$ktoraCena = 'cenaNiedziela';
		break;
	default:
		$ktoraCena = 'cenaZwykla';
		break;
}

$zapytanie_wybor = "select
					numerPomieszczenia, pojemnosc, kategorie.nazwa as kategoria, cenaSobota, cenaNiedziela, cenaZwykla
					from
					pomieszczenia join pokoje using (idPokoju)
					join kategorie on pokoje.kategoria = kategorie.idKategorii
					join pokoje_sezony using (idPokoju)
					where
					idSezonu = (
					select idSezonu from sezony
					where curdate() between oddaty and dodaty
					order by datediff(dodaty, oddaty) asc
					limit 1)
					and numerPomieszczenia between $nrOd and $nrDo
					and pojemnosc between $osobOd and $osobDo
					and $ktoraCena between $cenaOd and $cenaDo
					order by $sort";
					
if(!$zapytanie_wybor_run = mysql_query($zapytanie_wybor))
{
	echo 'blad zapytania o wybor';
	echo mysql_error();
}

function addOption($zapytanie_run, $tylko_wolne)
{
switch(date('w'))
{
	case '6':
		$ktoraCena = 'cenaSobota';
		break;
	case '0':
		$ktoraCena = 'cenaNiedziela';
		break;
	default:
		$ktoraCena = 'cenaZwykla';
		break;
}
	while($use_query_row = mysql_fetch_assoc($zapytanie_run))
	{
		$number = $use_query_row['numerPomieszczenia'];
		$persons = $use_query_row['pojemnosc'];
		$category = $use_query_row['kategoria'];
		$actual_cost = $use_query_row[$ktoraCena]; // tutaj zmienic jak bedzie net
		
		addRow($number, $persons, $category, $actual_cost, $tylko_wolne);
	}
}

function addRow($number, $persons, $category, $actual_cost, $tylko_wolne)
{
	if($tylko_wolne)
	{
		$zapytanie = 	"select count (*) as czy from rezerwacje
						where
						curdate() between '0000-00-00' and '3000-12-31'
						and numerPomieszczenia = $number
						limit 1";
		
		if(!$zapytanie_run = mysql_query($zapytanie))
		{
			echo 'blad zapytania o wolny';
			echo mysql_error();
		}		
		
		if($zapytanie_run == "")
		{
			return;
		}
		
		$zapytanie_row = mysql_fetch_assoc($zapytanie_run);
		
		if($zapytanie_row['czy'] == 1)
		{
			echo "<tr>";
			echo "<td><a href=\"nowy_pokoj.php?act=edytuj&idPokoju=$number\">$number</a></td>";
			echo "<td>$persons</td>";
			echo "<td>$category</td>";
			echo "<td>$actual_cost</td>";
			echo "</tr>";
		}
	}
	else
	{
		echo "<tr>";
		echo "<td><a href=\"nowy_pokoj.php?nrPomieszczenia=$number\">$number</a></td>";
		echo "<td>$persons</td>";
		echo "<td>$category</td>";
		echo "<td>$actual_cost</td>";
		echo "</tr>";
	}
}


?>

<div class="container">
<center><h1>Pokoje</h1></center> 
<div class="panel panel-default panel-body">
<div class="well">
<form action "pokoje.php" method="POST">
<table width="60%">

       <tr>
           <td style="width: 30%">Numer pokoju:</td>
           <td style="width: 10%">od</td>
           <td style="width: 25%"><input type="text" name="od_numer" class="input_to" maxlength="6" <?php if(isset($_POST['od_numer'])) echo 'value="', $_POST['od_numer'], '"'; ?>></td>
           <td style="width: 10%">do</td>
           <td style="width: 25%"><input type="text" name="do_numer" class="input_to" maxlength="6" <?php if(isset($_POST['do_numer'])) echo 'value="', $_POST['do_numer'], '"'; ?>></td>
       </tr>
       <tr>
           <td style="width: 30%">Liczba osób:</td>
           <td style="width: 10%">od</td>
           <td style="width: 25%"><input type="text" name="od_osob" class="input_to" maxlength="11" <?php if(isset($_POST['od_osob'])) echo 'value="', $_POST['od_osob'], '"'; ?>></td>
           <td style="width: 10%">do</td>
           <td style="width: 25%"><input type="text" name="do_osob" class="input_to" maxlength="11" <?php if(isset($_POST['do_osob'])) echo 'value="', $_POST['do_osob'], '"'; ?>></td>
       </tr>
       <tr>
           <td style="width: 30%">Cena:</td>
           <td style="width: 10%">od</td>
           <td style="width: 25%"><input type="text" name="od_cena" class="input_to" maxlength="13" <?php if(isset($_POST['od_cena'])) echo 'value="', $_POST['od_cena'], '"'; ?>></td>
           <td style="width: 10%">do</td>
           <td style="width: 25%"><input type="text" name="do_cena" class="input_to" maxlength="13" <?php if(isset($_POST['do_cena'])) echo 'value="', $_POST['do_cena'], '"'; ?>></td>
       </tr>

</table>
	<table>
	<tr>
		<!--<td style="width:200px;">
			<input type="checkbox" name="wolne" value="wolne" style="width: 80" <?php if(isset($_POST['wolne'])) echo 'checked'; ?>>
		Tylko wolne
		</td>-->
		<td>
			<center><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></center>
		</td>
	</tr>
       
	</table>
</form>
</div>
<table class="table table-striped">
       <thead><tr>
           <th><a href="pokoje.php?sort=numerPomieszczenia">Numer</a></th>
           <th><a href="pokoje.php?sort=pojemnosc">Liczba osób</a></th>
           <th><a href="pokoje.php?sort=kategoria">Kategoria</a></th>
           <th><a href="pokoje.php?sort=<?php echo $ktoraCena; ?>">Dzisiejsza cena</a></th>
       </tr></thead>
       <?php addOption($zapytanie_wybor_run, $tylkoWolne);?>
</table>
</div>
</div>
</body>
</html>