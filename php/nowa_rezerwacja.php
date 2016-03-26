<?php
require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once 'funkcje.php';

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

$sort1 = "k.nazwisko";
$czyFirma = 0;
$nazwisko = '';

$od = date('Y-m-d');
$do = date('Y-m-d');
$liczbaOsob = 100;
$czySala = false;
$sort2 = 'numerPomieszczenia';

$wolno = false;

if(isset($_POST['filtruj']))
{
	$czyod = false;
	$czydo = false;
	
	if($_POST['typ_klienta'] == 'indywidualny')
	{
		$czyFirma = 0; 
	}
	else
	{
		$czyFirma = 1;
	}
	if(isset($_POST['nazwisko']))
	{
		if(czyTylkoLitery($_POST['nazwisko']))
		{
			$nazwisko = $_POST['nazwisko'];
		}
		else
		{
			echo $wiadomosc = "zły format: nazwisko";
		}
	}
	if(isset($_POST['data_przyjazdu']))
	{
		if(sprCzyData($_POST['data_przyjazdu']))
		{
			$czyod = true;
		}
		else
		{
			echo $wiadomosc = "zły format: data przyjazdu";
		}
		//$od = $_POST['data_przyjazdu'];
	}
	if(isset($_POST['data_wyjazdu']))
	{
		if(sprCzyData($_POST['data_wyjazdu']))
		{
			$czydo = true;
		}
		else
		{
			echo $wiadomosc = "zły format: data wyjazdu";
		}
		//$do = $_POST['data_wyjazdu'];
	}
	if(isset($_POST['l_osob']))
	{ 
		if(czyCalkowita($_POST['l_osob']))
		{
			$liczbaOsob = $_POST['l_osob'];
		}
		else
		{
			echo $wiadomosc = "zły format: liczba osób";
		}
	}
	
	if($czyod && $czydo)
	{
		if(czyRoznicaDatOk($_POST['data_przyjazdu'], $_POST['data_wyjazdu']))
		{
			$od = $_POST['data_przyjazdu'];
			$do = $_POST['data_wyjazdu'];
		}
		else
		{
			echo $wiadomosc = "data przyjazdu późniejsza od daty wyjazdu";
		}
	}
	
	if($_POST['typ'] == 'pokoj') $czySala = 0; else $czySala = 1;
	
	if(isset($_POST['data_przyjazdu']) && isset($_POST['data_wyjazdu']) && (isset($_POST['filtruj']) || isset($_POST['utworz']))) $wolno= true;
}

if(isset($_POST['utworz']))
{
	if(isset($_POST['idPokoju']))
	{
		if(isset($_POST['klient']))
		{
			if(true /* warunek dat (kolejność, czy równe) */)
			{
				$query = "insert into rezerwacje (idRezerwacji, stan, zaliczka, okresOd, okresDo, idKlienta, numerPomieszczenia) values (NULL, '1', NULL, '$od', '$do', {$_POST['klient']}, {$_POST['idPokoju']})";
				if(!$query__run = mysql_query($query))
				{
					echo 'blad zapytania 1';
				}
				else
				{
					$wolno = false;
					$blad = '<div class="alert alert-success">Pomyślnie dokonano rezerwacji</div><meta http-equiv="refresh" content="2; url=lista_rezerwacji.php">';
				}
			}
			else $blad = '<div class="alert alert-danger">Wybrano niepoprawny zakres dat</div>';
		}
		else $blad = '<div class="alert alert-danger">Nie wybrano klienta</div>';
	}
	else $blad = '<div class="alert alert-danger">Nie wybrano pokoju</div>';
}

if($czyFirma)
$query_one = "SELECT k.idklienta as idKlienta, CONCAT_WS(' ',k.imie,k.nazwisko) AS Klient, ifnull(k.nazwaFirmy,'-') AS firma
             FROM klienci AS k WHERE k.czyfirma=$czyFirma AND k.nazwaFirmy LIKE '%$nazwisko%' ORDER BY 'k.nazwaFirmy'";
else
$query_one = "SELECT k.idklienta as idKlienta, CONCAT_WS(' ',k.imie,k.nazwisko) AS Klient, ifnull(f.nazwaFirmy,'-') AS firma
             FROM klienci AS k LEFT JOIN klienci AS f  ON k.idfirmy=f.idklienta WHERE k.czyfirma=$czyFirma AND k.nazwisko LIKE '%$nazwisko%' ORDER BY $sort1";

if(!($czySala))
$query_two = "SELECT numerPomieszczenia, idPokoju, nazwa as kategoria FROM pomieszczenia JOIN pokoje USING(idPokoju) join kategorie on(kategoria=idKategorii) WHERE numerPomieszczenia
             NOT IN(SELECT DISTINCT numerPomieszczenia FROM rezerwacje WHERE
                    (
                            (('$do' between okresOd AND okresDo)AND('$do'<>okresOd))
                            OR
                            (('$od' between okresOd AND okresDo)AND('$od'<>okresDo))
                            OR
                            ((okresOd>='$od')AND(okresDo<='$do'))
                    )
             )
             AND czySala = '$czySala' AND pojemnosc>=$liczbaOsob ORDER BY $sort2";
else
$query_two = "SELECT numerPomieszczenia, idSali, cena FROM pomieszczenia JOIN salekonferencyjne USING(idSali) WHERE numerPomieszczenia
             NOT IN(SELECT DISTINCT numerPomieszczenia FROM rezerwacje WHERE
                    (
                            (('$do' between okresOd AND okresDo)AND('$do'<>okresOd))
                            OR
                            (('$od' between okresOd AND okresDo)AND('$od'<>okresDo))
                            OR
                            ((okresOd>='$od')AND(okresDo<='$do'))
                    )
             )
             AND czySala = '$czySala' AND iloscMiejsc>=$liczbaOsob ORDER BY $sort2";

if(!$query_one_run = mysql_query($query_one))
{
  echo 'blad zapytania 1';
}

if(!$query_two_run = mysql_query($query_two))
{
  echo 'blad zapytania 2';
}

function addNewRowClient($use_query)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $Client = $use_query_row['Klient'];
    $Firm = $use_query_row['firma'];
	$idKlienta = $use_query_row['idKlienta'];

    echo "<tr>\n";
    echo "<td><input type=\"radio\" name=\"klient\" value=\"$idKlienta\" required>$Client</td>\n";
    echo "<td>$Firm</td>\n";
    echo "</tr>\n";
  }
}
?>

<?php
function addNewRowRoom($use_query, $od, $do)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $Number = $use_query_row['numerPomieszczenia'];
    
	if(isset($use_query_row['idPokoju']))	//pokój
	{
		$Category = $use_query_row['kategoria'];
		$idPokoju = $use_query_row['idPokoju'];
	}
	else	//sala
	{
		$idPokoju = $use_query_row['idSali'];
		$Category = '';
		$cena = $use_query_row['cena'];
	}
	
	
    $Facilities = '';
	
	if(isset($use_query_row['idPokoju']))
    $query_three = "SELECT nazwa FROM pokoje JOIN pokoje_wyposazenie USING(idPokoju) JOIN wyposazeniePokoju USING(idWyposazenia) WHERE idPokoju='$idPokoju'";
	else $query_three = "SELECT nazwa FROM salekonferencyjne JOIN sale_wyposazenie USING(idSali) JOIN wyposazeniesali USING(idWyposazenia) WHERE idSali='$idPokoju'";

    if(!$query_three_run = mysql_query($query_three))
    {
        echo 'blad zapytania 3';
    }

    while($query_three_row = mysql_fetch_assoc($query_three_run))
    {
        $Facilities .= $query_three_row['nazwa'].', ';
    }
	$Facilities = substr($Facilities, 0, -2);

    $koszt = 0;
    $daty = getDatesFromRange($od, $do);
    array_pop($daty); //nie liczymy za ostatni dzień
    
	if(isset($use_query_row['idPokoju']))
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
	else	//sala
	{
		$koszt = count($daty)*$cena;
	}

    echo "<tr>";
    echo "<td><input type='radio' name=\"idPokoju\" value=\"$Number\" required>$Number</td>";
    echo "<td>$Category</td>";
    echo "<td>$koszt</td>";
    echo "<td>$Facilities</td>";
    echo "</tr>";
  }
}
?>
<div class="container">
<h1><center>Nowa rezerwacja</center></h1>
<div class="panel panel-default panel-body">
<form action="nowa_rezerwacja.php" method="POST">
<div class="row">
<div class="col-md-4 col-md-offset-2">
     <table style="text-align: left" cellspacing="10">
            <tr>
                <td>Typ klienta:</td>
                <td><input type="radio" name="typ_klienta" value="indywidualny" <?php if(isset($_POST['typ_klienta']) && $_POST['typ_klienta'] == 'indywidualny') echo 'checked'; ?> required>indywidualny</td>
                <td><input type="radio" name="typ_klienta" value="firma" <?php if(isset($_POST['typ_klienta']) && $_POST['typ_klienta'] == 'firma') echo 'checked'; ?> required>firma</td>
            </tr>
            <tr>
                <td style="padding-right: 5px;">Nazwisko (nazwa firmy) zawiera:</td>
                <td colspan="2"><input type="text" name="naz_zawiera" style="width: 200" maxLength="45"></td>
            </tr>
     </table>
</div>
<div class="col-md-4">
     <table style="text-align: left" cellspacing="10">
            <tr>
                <td style="padding-right: 10px;">Data przyjazdu:</td>
                <td><input type="text" name="data_przyjazdu" maxLength="10" value="<?php if(isset($_POST['data_przyjazdu'])) echo $_POST['data_przyjazdu']; ?>" required></td>
            </tr>
            <tr>
                <td>Data wyjazdu:</td>
                <td><input type="text" name="data_wyjazdu" maxLength="10" value="<?php if(isset($_POST['data_wyjazdu'])) echo $_POST['data_wyjazdu']; ?>" required></td>
            </tr>
            <tr>
                <td>Liczba osób:</td>
                <td><input type="text" name="l_osob" maxLength="11" value="<?php if(isset($_POST['l_osob'])) echo $_POST['l_osob']; ?>" required></td>
            </tr>
            <tr>
                <td><input type="radio" name="typ" value="pokoj" <?php if(isset($_POST['typ']) && $_POST['typ'] == 'pokoj') echo 'checked'; ?> required>pokój</td>
                <td><input type="radio" name="typ" value="sala_konferencyjna" <?php if(isset($_POST['typ']) && $_POST['typ'] == 'sala_konferencyjna') echo 'checked'; ?> required>sala konferencyjna</td>
            </tr>
     </table>
</div>
</div>
<div style="width: 100%; float: right">
<center><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></center>
</div>
</form>
</div>

<div class="panel panel-default panel-body">
<div class="row">
<form action="nowa_rezerwacja.php" method="POST">
<div class="col-md-4">
<center>Klient</center>
     <table class="table table-striped">
            <thead>
            <tr>
                <th>Imię i nazwisko</th>
                <th>Firma</th>
            </tr>
			</thead>
            <?php addNewRowClient($query_one_run);?>
     </table>
</div>
<div class="col-md-8">
<center>Pokój</center>
     <table class="table table-striped">
        <thead>
            <tr>
                <th>Numer</th>
                <th>Kategoria</th>
                <th>Całkowity koszt</th>
                <th>Wyposażenie</th>
            </tr>
			</thead>
            <?php addNewRowRoom($query_two_run, $od, $do);?>
     </table>
</div>

<div style="width: 100%; float: right">
<input type="hidden" name="data_przyjazdu" value="<?php if(isset($_POST['data_przyjazdu'])) echo $_POST['data_przyjazdu']; ?>">
<input type="hidden" name="data_wyjazdu" value="<?php if(isset($_POST['data_wyjazdu'])) echo $_POST['data_wyjazdu']; ?>">
<?php
if($wolno) echo '<center><input type="submit" name="utworz" value="utwórz rezerwację" class="btn btn-primary"></center>';
?>
</div>

</form>
</div>
</div>
<?php if(isset($blad)) echo $blad; ?>
</div>
</body>
</html>