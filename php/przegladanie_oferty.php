<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';
require_once "funkcje.php";

$wiadomosc = "";

function getDatesFromRange($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

//początkowe wartości filtru
$od = date('Y-m-d');
$do = date('Y-m-d');
$osob = 0;
$kategoria = 0;
$sort = 'numerPomieszczenia';
$wolno = false;

//wartości filtrowane
if(isset($_POST['szukaj']) || isset($_POST['zloz']))
{
	$odBool = false;
	$doBool = false;
	
	if(isset($_POST['data_przyjazdu']))
	{ 
		if(sprCzyData($_POST['data_przyjazdu']))
		{
			//$od = $_POST['data_przyjazdu'];
			$odBool = true;
		}
		else
		{
			$od = "0000-01-01";
			echo $wiadomosc = "zły format: data od";
		}
	}
	if(isset($_POST['data_odjazdu'])) 
	{
		if(sprCzyData($_POST['data_odjazdu']))
		{
			//$do = $_POST['data_odjazdu'];
			$doBool = true;
		}
		else
		{
			$do = "9999-12-31";
			echo $wiadomosc = "zły format: data do";
		}
	}
	if(isset($_POST['liczba_osob']))
	{ 
		if(czyCalkowita($liczba))
		{
			$osob = $_POST['liczba_osob'];
		}
		else
		{
			$osob = 0;
			echo $wiadomosc = "zły format: liczba osob";
		}
	}
	
	if($doBool && $odBool)
	{
		if(czyRoznicaDatOk($dataOd, $dataDo))
		{
			$od = $_POST['data_przyjazdu'];
			$do = $_POST['data_odjazdu'];
		}
		else
		{
			$od = "0000-01-01";
			$do = "9999-12-31";
			echo $wiadomosc = "data od większa od daty do";
		}
	}
	
	if(isset($_POST['sort'])) $sort = $_POST['sort'];
	if(isset($_POST['data_przyjazdu']) && isset($_POST['data_odjazdu']) && (isset($_POST['szukaj']) || isset($_POST['zloz']))) $wolno= true;
}

if(isset($_POST['zloz']))
{
	if(isset($_POST['pokoj']))
	{
		if(true /* warunek dat (kolejność, czy równe) */)
		{
			$query = "insert into rezerwacje (idRezerwacji, stan, zaliczka, okresOd, okresDo, idKlienta, numerPomieszczenia) values (NULL, '1', NULL, '$od', '$do', $idKlienta, {$_POST['pokoj']})";
			if(!$query__run = mysql_query($query))
			{
				echo 'blad zapytania 1';
			}
			else
			{
				$wonlo = false;
				$blad = '<div class="alert alert-success">Pomyślnie dokonano rezerwacji</div><meta http-equiv="refresh" content="2; url=panel_klienta.php">';
			}
		}
		else $blad = '<div class="alert alert-danger">Wybrano niepoprawny zakres dat</div>';
	}
	else $blad = '<div class="alert alert-danger">Nie wybrano pokoju</div>';
}

function pokoje($od, $do, $osob, $sort)
{
	$query_pokoje = "SELECT numerPomieszczenia, idPokoju, nazwa as kategoria FROM pomieszczenia JOIN pokoje USING(idPokoju) join kategorie on (kategoria=idKategorii) WHERE numerPomieszczenia
             NOT IN(SELECT DISTINCT numerPomieszczenia FROM rezerwacje WHERE
                    (
                            (('$do' between okresOd AND okresDo)AND('$do'<>okresOd))
                            OR
                            (('$od' between okresOd AND okresDo)AND('$od'<>okresDo))
                            OR
                            ((okresOd>='$od')AND(okresDo<='$do'))
                    )
             )
             AND czySala = 0 AND pojemnosc>=$osob ORDER BY '$sort'";
			 
	if(isset($_POST['szukaj']))
	{
		if(!$query_pokoje_run = mysql_query($query_pokoje))
		{
			echo 'blad zapytania 1';
		}
		while($query_pokoje_row = mysql_fetch_assoc($query_pokoje_run))
		{
			$numer = $query_pokoje_row['numerPomieszczenia'];
			$idPokoju = $query_pokoje_row['idPokoju'];
			$kategoriaId = $query_pokoje_row['kategoria'];
			
			//wyposażenie
			$Facilities = '';
			$query_wyp = "SELECT nazwa FROM pokoje JOIN pokoje_wyposazenie USING(idPokoju) JOIN wyposazeniePokoju USING(idWyposazenia) WHERE idPokoju='$idPokoju'";
			if(!$query_wyp_run = mysql_query($query_wyp))
			{
				echo 'blad zapytania 2';
			}
			while($query_wyp_row = mysql_fetch_assoc($query_wyp_run))
			{
				$Facilities .= $query_wyp_row['nazwa'].', ';
			}
			$Facilities = substr($Facilities, 0, -2);
			
			//cena
			$koszt = 0;
			$daty = getDatesFromRange($od, $do);
			array_pop($daty); //nie liczymy za ostatni dzień
			
			foreach($daty as $data)
			{
				 $dtyg = DateTime::createFromFormat('Y-m-d', $data);
				 $dtyg = $dtyg->format('w');
				 if($dtyg == 6) //sobota
				 {
					   $query_four_1 = "select cenaSobota from pokoje_sezony where idPokoju = $idPokoju
									and idSezonu = (select idSezonu from sezony where odDaty <= '$data' and doDaty >= '$data'
									order by datediff(doDaty, odDaty) asc limit 1)";

					   if(!$query_four_run = mysql_query($query_four_1)){echo 'blad zapytania 4_1';}

					   $query_four_row = mysql_fetch_assoc($query_four_run);

						  $koszt += $query_four_row['cenaSobota'];
				 }
				 else if($dtyg == 0) //niedziela
				 {
					   $query_four_2 = "select cenaNiedziela from pokoje_sezony where idPokoju = $idPokoju
									and idSezonu = (select idSezonu from sezony where odDaty <= '$data' and doDaty >= '$data'
									order by datediff(doDaty, odDaty) asc limit 1)";

					   if(!$query_four_run = mysql_query($query_four_2)){echo 'blad zapytania 4_2';}
					   
					   $query_four_row = mysql_fetch_assoc($query_four_run);

						  $koszt += $query_four_row['cenaNiedziela'];
				 }
				 else
				 {
					   $query_four_3 = "select cenaZwykla from pokoje_sezony where idPokoju = $idPokoju
									and idSezonu = (select idSezonu from sezony where odDaty <= '$data' and doDaty >= '$data'
									order by datediff(doDaty, odDaty) asc limit 1)";

					   if(!$query_four_run = mysql_query($query_four_3)){echo 'blad zapytania 4_3';}
					   
					   $query_four_row = mysql_fetch_assoc($query_four_run);

						  $koszt += $query_four_row['cenaZwykla'];
				 }
			}
			//cena jest w $koszt
			addNewRow($numer, $kategoriaId, $koszt, $Facilities);
		}
	}
}

function addNewRow($Number, $Category, $Overall_cost, $Facilities)
{
  echo "<tr>";
  echo "<td><input type=\"radio\" name=\"pokoj\" value=\"$Number\"><a href=\"opis_pokoju.php?numer=$Number\">$Number</a></td>";
  echo "<td>$Category</td>";
  echo "<td>$Facilities</td>";
  echo "<td>$Overall_cost</td>";
  echo "</tr>";
}
?>

<div class="container">
<h1><center>Przegladanie oferty</center></h1>
<div class="panel panel-default panel-body">
<form action="przegladanie_oferty.php" method="POST">
<div class="well">
<table style="width: 35%">
       <tr>
           <td>Data przyjazdu</td>
           <td style="width: 180px" colspan="2"><input type="text" name="data_przyjazdu" required style="width: 180px" value="<?php if(isset($_POST['data_przyjazdu'])) echo $_POST['data_przyjazdu']; ?>"></td>
       </tr>
       <tr>
           <td>Data odjazdu</td>
           <td style="width: 180px" colspan="2"><input type="text" name="data_odjazdu" required style="width: 180px" value="<?php if(isset($_POST['data_odjazdu'])) echo $_POST['data_odjazdu']; ?>"></td>
       </tr>
       <tr>
           <td>Liczba osób</td>
           <td style="width: 180px" colspan="2"><input type="text" required name="liczba_osob" style="width: 180px" value="<?php if(isset($_POST['liczba_osob'])) echo $_POST['liczba_osob']; ?>"></td>
       </tr>
       <!--<tr>
           <td>Kategoria</td>
           <td colspan="2">
           <select name="kategoria" style="width: 180px">
                   <option value="1">1</option>
                   <option value="2">2</option>
                   <option value="3">3</option>
           </select>
           </td>
       </tr>-->
	   <tr>
		<td>Sortowanie</td>
		<td><input type="radio" name="sort" value="numerPomieszczenia" required <?php if(isset($_POST['sort']) && ($_POST['sort'] == 'numerPomieszczenia')) echo 'checked'; ?>> po numerze pokoju</td>
		<td><input type="radio" name="sort" value="cenaZwykla" <?php if(isset($_POST['sort']) && ($_POST['sort'] == 'cena')) echo 'checked'; ?>> po cenie</td>
	   </tr>
       <tr style="text-align: right">
           <td></td>
           <td style="text-align: center"><input type="submit" name="szukaj" value="szukaj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</div>
<table class="table table-striped">
       <thead><tr>
           <th>Numer</th>
           <th>Kategoria</th>
           <th>Wyposażenie</th>
           <th>Cena</th>
       </tr></thead>
       <?php 
	   pokoje($od, $do, $osob, $sort);
	   if($kontoAktywne && $wolno)
	   echo '<tr style="text-align: right">
           <td style="text-align: center" colspan="4"><input type="submit" name="zloz" value="złóż rezerwację" class="btn btn-sm btn-primary"></td>
       </tr>';
	   else if(!$kontoAktywne)
		echo '<tr><td colspan="4"><div class="alert alert-warning">Twoje konto jest nieaktywne (lub nie jesteś zalogowany) i nie możesz składać rezerwacji. Skontaktuj się z hotelem lub zaloguj się.</div></td></tr>';
	   ?>
</table>
</form>
</div>
<?php if(isset($blad)) echo $blad; ?>
</div>
</body>
</html>