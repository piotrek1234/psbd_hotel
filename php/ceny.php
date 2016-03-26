<?php
require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once "funkcje.php";
///////////////////////////////zmienne do zapytania
$idKategorii = 1;
$odOsoby = 0;
$doOsoby = 1000000;
$sort_pokoje = "pojemnosc";

$idPokoju_g = 0;
$idSezonu_g = 0;

$doData = "4000-12-31";
$odData = "1800-01-01";
$nazwa = '%';
$sort_sezony = "nazwaSezonu";

$wiadomosc = "";
///////////////////////////////

///////////////////////////////pobieranie danych
if(isset($_POST['filtruj'])||isset($_POST['wczytaj']))
{
	$odOsoby_l = $_POST['od_l_osob'];
	$doOsoby_l = $_POST['do_l_osob'];
	$idKategorii = $_POST['kategoria'];
	$doData_l = $_POST['do_l_data'];
	$odData_l = $_POST['od_l_data'];
	$nazwa_l = $_POST['nazwa_zawiera'];
	
	$odOsobaPoprawnie = false;
	$doOsobaPaprawnie = false;
	
	$odDataPoprawnie = false;
	$doDataPoprawnie = false;
	
	if($odOsoby_l != "")
	{
		if(czyCalkowita($odOsoby_l))
		{
			//$odOsoby = $odOsoby_l;
			$odOsobaPoprawnie = true;
		}
		else
		{
			echo $wiadomosz = "zły format: Liczba osób od";
		}
	}
	if($doOsoby_l != "")
	{
		if(czyCalkowita($doOsoby_l))
		{
			//$doOsoby = $doOsoby_l;
			$doOsobaPaprawnie = true;
		}
		else
		{
			echo $wiadomosz = "zły format: Liczba osób do";
		}
	}
	if($odData_l != "")
	{
		if(sprCzyData($odData_l))
		{
			//$doData = $doData_l;
			$odDataPoprawnie = true;
		}
		else
		{
			echo $wiadomosz = "zły format: Data od";
		}
	}
	if($doData_l != "")
	{
		if(sprCzyData($doData_l))
		{
			//$odData = $odData_l;
			$doDataPoprawnie = true;
		}
		else
		{
			echo $wiadomosz = "zły format: Data do";
		}
	}
	if($nazwa_l != "")
	{
		if(czyTylkoLitery($nazwa_l))
		{
			if(isset($_POST['filtruj']))
				$nazwa = $nazwa_l;
		}
		else
		{
			echo $wiadomosz = "zły format: Nazwa zawiera";
		}
	}
	
	if($odOsobaPoprawnie && $doOsobaPaprawnie)
	{
		if(czyOdDo($odOsoby_l, $doOsoby_l))
		{
			$odOsoby = $odOsoby_l;
			$doOsoby = $doOsoby_l;
		}
		else
		{
			echo $wiadomosz = "liczba osob od wieksza od liczby osob do";
		}
	}
	
	if($odDataPoprawnie && $odDataPoprawnie)
	{
		if(czyRoznicaDatOk($odData_l, $doData_l))
		{
			$doData = $doData_l;
			$odData = $odData_l;
		}
		else
		{
			echo $wiadomosz = "data od późniejsza od daty do";
		}
	}
	
	//echo $idKategori;
}

//echo $idKategorii;

if(isset($_REQUEST['sort_typ']))
{
	$sort_pokoje = $_REQUEST['sort_typ'];
}

if(isset($_REQUEST['sort_sezon']))
{
	$sort_sezony = $_REQUEST['sort_sezon'];
}
///////////////////////////////

///////////////////////////filtruj rodzaje pokoju
$zapytanie_filtruj_rodaj_pokoju = "";

if($idKategorii == 0)
{
$zapytanie_filtruj_rodaj_pokoju = "select idPokoju, pojemnosc, nazwa from pokoje join kategorie on (kategoria = idKategorii) 
									where pojemnosc between '$odOsoby' and '$doOsoby' order by $sort_pokoje";
}
else
{
$zapytanie_filtruj_rodaj_pokoju = "select idPokoju, pojemnosc, nazwa from pokoje join kategorie on (kategoria = idKategorii) 
									where idKategorii = '$idKategorii' and pojemnosc between '$odOsoby' and '$doOsoby' order by $sort_pokoje";
}
									
if(!$zapytanie_filtruj_rodaj_pokoju_run = mysql_query($zapytanie_filtruj_rodaj_pokoju))
{
	echo 'blad zaptyania rodzaj pokoju<br>';
	echo mysql_error();
}

function addPokojTyp($zapytanie_run, $idK)
{
  while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
  {
    $name = $zapytanie_row['nazwa'];
    $id = $zapytanie_row['idPokoju'];
	$ilosc = $zapytanie_row['pojemnosc'];
    
	$wyposazenie = facility($id);
	
	addRowPokojTyp($id, $ilosc, $name, $wyposazenie, $idK);
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
	
	$wynik = "";
	$i = 0;
	while($use_query_row = mysql_fetch_assoc($zapytanie_run))
	{
		$wynik[$i] = $use_query_row['nazwa'];
		$i++;
	}
	
	if($wynik == "")
		return;
	
	return implode(", ", $wynik);
}

function addRowPokojTyp($id, $liczba, $kategoria, $sprzet, $idK)
{
	if($id == $idK)
	{
		echo "<tr>";
		echo "<td><input type='radio' name='idPokoju' value=$id checked>$liczba</td>";
		echo "<td>$kategoria</td>";
		echo "<td>$sprzet</td>";
	}
	else
	{
		echo "<tr>";
		echo "<td><input type='radio' name='idPokoju' value=$id>$liczba</td>";
		echo "<td>$kategoria</td>";
		echo "<td>$sprzet</td>";
	}
}
///////////////////////////

///////////////////////////uzupełnij kategorie
function addSelect($idK)
{
	$zapytanie = "select idKategorii, nazwa from kategorie";
	if(!$zapytanie_run = mysql_query($zapytanie))
	{
		echo 'blad zaptyania kategorie<br>';
		echo mysql_error();
	}
	
	while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
	{
		$name = $zapytanie_row['nazwa'];
		$id = $zapytanie_row['idKategorii'];
		
		if($id == $idK)
		{
			echo "<option value=\"$id\" selected>$name</option>";
		}
		else
		{
			echo "<option value=\"$id\">$name</option>";
		}
	}
}
///////////////////////////

///////////////////////////filtruj sezony


$zapytanie_filtruj_sezon = "select idSezonu, doDaty, odDaty, nazwaSezonu from sezony where nazwaSezonu like '%$nazwa%' and 
							(
							   (('$doData' between doDaty and odDaty)and('$doData' <> odDaty))
								or
								(('$odData' between doDaty and odDaty)and('$odData' <> doDaty))
								or
								((odDaty>='$odData')and(doDaty<='$doData'))
							)
							order by $sort_sezony";

if(!$zapytanie_filtruj_sezon_run = mysql_query($zapytanie_filtruj_sezon))
{
	echo 'blad zaptyania filtruj sezon<br>';
	echo mysql_error();
}

//echo $zapytanie_filtruj_sezon_run."<br>";

function addSezon($zapytanie_run, $idS)
{
  while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
  {
    $name = $zapytanie_row['nazwaSezonu'];
    $id = $zapytanie_row['idSezonu'];
	$do = $zapytanie_row['doDaty'];
	$od = $zapytanie_row['odDaty'];
	//echo "tutut<br>";
	addRowSezon($id, $name, $do, $od, $idS);
  }
}	
function addRowSezon($id, $name, $do, $od, $idS)
{
	if($id == $idS)
	{
		echo "<tr>";
		echo "<td><input type='radio' name='idSezonu' value=\"$id\" checked>$od</td>";
		echo "<td>$do</td>";
		echo "<td>$name</td>";
		echo "</tr>";
	}
	else
	{
				echo "<tr>";
		echo "<td><input type='radio' name='idSezonu' value=\"$id\">$od</td>";
		echo "<td>$do</td>";
		echo "<td>$name</td>";
		echo "</tr>";
	}
}					
////////////////////////////

/////////////////////////////////wybor 
$zapytanie_wczytaj_row;
$zapytanie_wczytaj = "";

if(isset($_POST['wczytaj'])&&isset($_POST['idSezonu'])&&isset($_POST['idPokoju']))
{
	$idPokoju_l = $_POST['idPokoju'];
	$idSezonu_l = $_POST['idSezonu'];
	
	$zapytanie_wczytaj = "select cenaZwykla, cenaSobota, cenaNiedziela from pokoje_sezony where idPokoju = $idPokoju_l and idSezonu = $idSezonu_l";
	
	if(!$zapytanie_wczytaj_run = mysql_query($zapytanie_wczytaj))
	{
		echo 'blad zaptyania wczytaj<br>';
		echo mysql_error();
	}
	
	$idPokoju_g = $idPokoju_l;
	$idSezonu_g = $idSezonu_l;
	
	$zapytanie_wczytaj_row = mysql_fetch_assoc($zapytanie_wczytaj_run);
}

$cenaZwykla = 0;
$cenaSobota = 0;
$cenaNiedziela = 0;

if($zapytanie_wczytaj != "")
{
	if(!empty($zapytanie_wczytaj_row))
	{
		$cenaZwykla = $zapytanie_wczytaj_row['cenaZwykla'];
		$cenaSobota = $zapytanie_wczytaj_row['cenaSobota'];
		$cenaNiedziela = $zapytanie_wczytaj_row['cenaNiedziela'];
		//echo "tututututut";
	}
}
//////////////////////////////////

/////////////////////////////////dodanie lub update do bazy
if(isset($_POST['zapisz_ceny'])&&isset($_POST['idSezonu'])&&isset($_POST['idPokoju']))
{
	$idS = $_POST['idSezonu'];
	$idP = $_POST['idPokoju'];
	
	$cenaZwykla = $_POST['cena_w_dni_robocze'];
	$cenaSobota = $_POST['cena_w_soboty'];
	$cenaNiedziela = $_POST['cena_w_niedziele'];
	
	if(sprCena($cenaZwykla) && sprCena($cenaSobota) && sprCena($cenaNiedziela))
	{
		$zapytanie_temp = "SELECT * FROM pokoje_sezony WHERE idSezonu = $idS and idPokoju = $idP";
		
		if(!$zapytanie_temp_run = mysql_query($zapytanie_temp))
		{
			echo 'blad zaptyania temp<br>';
			echo mysql_error();
		}
		
		$zapytanie_temp_row = mysql_fetch_assoc($zapytanie_temp_run);
		
		if(($cenaZwykla != 0)&&($cenaSobota != 0)&&($cenaNiedziela != 0))
		{
			if(empty($zapytanie_temp_row))
			{
				// insert
				$zapytanie_dodaj = "insert into pokoje_sezony (idSezonu, idPokoju, cenaSobota, cenaNiedziela, cenaZwykla)
									values ($idS, $idP, $cenaSobota, $cenaNiedziela, $cenaZwykla)";
									
				if(!$zapytanie_dodaj_run = mysql_query($zapytanie_dodaj))
				{
					echo 'blad zaptyania dodaj<br>';
					echo mysql_error();
				}
			}
			else
			{
				// update
				$zapytanie_uaktualnij = "update pokoje_sezony set cenaSobota = $cenaSobota, 
											cenaNiedziela = $cenaNiedziela, cenaZwykla = $cenaZwykla where idSezonu = $idS and idPokoju = $idP";
											
				if(!$zapytanie_uaktualnij_run = mysql_query($zapytanie_uaktualnij))
				{
					echo 'blad zaptyania uaktualnij<br>';
					echo mysql_error();
				}
			}
		}
	}
	else
	{
		$wiadomosc = "niepoprawna cena";
	}
}
/////////////////////////////////

?>

<div class="container">
      <center><h1>Ceny</h1></center>
	  
	  <form action="ceny.php" method="POST">
	  <div class="panel panel-default panel-body">
      <div class="well"> 
	   <table width="100%">

      <tr>
          <td>    <div class="filtrujrodzaj">Filtruj rodzaje pokoju:<br>
                   Liczba osób: od <input type="text" maxlength="6" name="od_l_osob" size="4"  value=<?php if($odOsoby == 0) echo '""';
																													else echo '"'.$odOsoby.'"';?>>  
																													do  
									<input type="text" name="do_l_osob" size="4" maxlength="6" value=<?php if($doOsoby == 1000000) echo '""';
																													else echo '"'.$doOsoby.'"';?>><br>
                   Kategoria       <select name="kategoria" style="margin-left: 39px"><?php addSelect($idKategorii);?></select>
                   </div> <!--Koniec bloczka filtracji rodzaju-->
          </td>
          <td>
                   <div class="filtrujsezon">Filtruj sezony: <br>
                   Data: od <input type="text" name="od_l_data" size="4" maxlength="10" value=<?php if($odData == "1800-01-01") echo '""';
																													else echo '"'.$odData.'"';?>>  do  
									<input type="text" name="do_l_data" size="4" maxlength="10" value=<?php if($doData == "4000-12-31") echo '""';
																													else echo '"'.$doData.'"';?>><br>
                   Nazwa zawiera:      <input type="text" name="nazwa_zawiera" style="margin-left: 39px" size="18" maxlength="45" value="<?php if(isset($_POST['nazwa_zawiera'])) echo $_POST['nazwa_zawiera']; ?>">
                   </div> <!--Koniec bloczka filtracji sezonu-->
          </td>
      </tr>
      <tr>
          <td colspan="2">
		  <center><input type="submit" name="filtruj" value="Filtruj" class="btn btn-sm btn-primary"></center>
          </td>
      </tr>
      </table>
	  </div>
      <table width="100%">
             <tr>
                 <td><a href="ceny.php?sort_sezon=odDaty">Od</a></td>
                 <td><a href="ceny.php?sort_sezon=doDaty">Do</a></td>
                 <td><a href="ceny.php?sort_sezon=nazwaSezonu">Nazwa sezonu</a></td>
             </tr>
			 <?php addSezon($zapytanie_filtruj_sezon_run, $idSezonu_g);?>
             <tr></tr><tr></tr><tr></tr>
      </table>
	  <table width="100%">
			<tr>
			     <td><a href="ceny.php?sort_typ=pojemnosc">Liczba osob</a></td>
                 <td><a href="ceny.php?sort_typ=nazwa">Kategoria</a></td>
                 <td>Wyposażenie</td>
			</tr>
			<?php addPokojTyp($zapytanie_filtruj_rodaj_pokoju_run, $idPokoju_g);?>
	  </table>
	  <center><input type="submit" name="wczytaj" value="wczytaj" class="btn btn-sm btn-primary"></center>
      </div>
      <div class="panel panel-default panel-body">
	  Cena w dni robocze:<input type="text" name="cena_w_dni_robocze" size="8" maxlength="8" style="margin-left: 9px" value=<?php echo $cenaZwykla?>><br>
      Cena w soboty:<input type="text" name="cena_w_soboty" size="8" maxlength="8" style="margin-left: 39px" value=<?php echo $cenaSobota?>>          <br>
      Cena w niedziele:<input type="text" name="cena_w_niedziele" size="8" maxlength="8" style="margin-left: 29px" value=<?php echo $cenaNiedziela?>>    <br>

      <input type="submit" name="zapisz_ceny" value="zapisz ceny" style="margin-left: 132px" class="btn btn-sm btn-primary">
      </div>
	  </form>
	  </div>
</body>
</html>