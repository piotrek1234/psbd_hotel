<html>
<?php
require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once "funkcje.php";

if(isset($_REQUEST['id']))
{
	$idPokoju = $_REQUEST['id'];
	$czyEdycja = true;
}
else $czyEdycja = false;


$opis_g = "";
$osoby_g = 0;
$kategoria_g = 1;
$wyposazenie_g = array();

$wiadomosc = "";

/*if($idPokoju == "")
{
	$czyEdycja = false;
}*/
if($czyEdycja)
{
	$zapytanie_edycja = "select pojemnosc, opis, kategoria from pokoje where idPokoju = $idPokoju";
	
	if(!$zapytanie_edycja_run = mysql_query($zapytanie_edycja))
	{
		echo 'blad zaptyania edycja 1';
		echo mysql_error();
	}
	
	$zapytanie_edycja_row = mysql_fetch_assoc($zapytanie_edycja_run);
	
	$opis_g = $zapytanie_edycja_row['opis'];
	$osoby_g = $zapytanie_edycja_row['pojemnosc'];
	$kategoria_g = $zapytanie_edycja_row['kategoria'];
	
	$zapytanie_edycja_2 = "select idwyposazenia from pokoje_wyposazenie where idPokoju = $idPokoju";
	
	if(!$zapytanie_edycja_run_2 = mysql_query($zapytanie_edycja_2))
	{
		echo 'blad zaptyania edycja 2';
		echo mysql_error();
	}
	
	$i = 0;
	
	while($zapytanie_edycja_row_2 = mysql_fetch_assoc($zapytanie_edycja_run_2))
	{
		$wyposazenie_g[$i] = $zapytanie_edycja_row_2['idwyposazenia'];
		$i++;
	}
}

$sort = 'nazwa';

if(isset($_GET['sort']))
{
	$sort = $_GET['sort'];
}

$zaptanie_wyposazenie = "select idwyposazenia, wyposazeniepokoju.nazwa as nazwa, typyWyposazenia.nazwa as typ
						from wyposazeniepokoju join typyWyposazenia using (typ)
						order by $sort";
						
if(!$zaptanie_wyposazenie_run = mysql_query($zaptanie_wyposazenie))
{
  echo 'blad zaptyania wyposazenie';
  echo mysql_error();
}
//zapis
if(isset($_POST['zapisz'])&&(!$czyEdycja))
{
	$liczba_osob = $_POST['liczba_osob'];
	$kategoria = $_POST['Kategoria'];
	$opis = $_POST['opis'];
	
	if($liczba_osob != "")
	{
		if(czyCalkowita($liczba_osob))
		{
			$zapytanie_dodaj = "insert into pokoje (idPokoju, pojemnosc, opis, kategoria)
								values (NULL, $liczba_osob, 
								'$opis', 
								$kategoria)";
								
			if(!$zapytanie_dodaj_run = mysql_query($zapytanie_dodaj))
			{
				echo 'blad zaptyania dodaj';
				echo mysql_error();
			}
			
			$zapytanie_pomocnicze = "select idPokoju from pokoje where pojemnosc=$liczba_osob and kategoria=$kategoria";
			
			if(!$zapytanie_pomocnicze_run = mysql_query($zapytanie_pomocnicze))
			{
				echo 'blad zaptyania pomocniczego';
				echo mysql_error();
			}
			
			$zapytanie_pomocnicze_row = mysql_fetch_assoc($zapytanie_pomocnicze_run);
			
			$idPokoju_l = $zapytanie_pomocnicze_row['idPokoju'];
			
			foreach($_POST['a'] as $selected) 
			{
					$zapytanie = "insert into pokoje_wyposazenie (idWyposazenia, idPokoju) values ($selected, $idPokoju_l)";
					
					if(!$zapytanie_r = mysql_query($zapytanie))
					{
						echo 'blad zaptyania dodaj wyposazenie';
						echo mysql_error();
					}
			}
		}
		else
		{
			echo $wiadomosc = "zły format: sprawdź liczbę osób";
		}
	}
}
// edycja
if(isset($_POST['zapisz'])&&($czyEdycja))
{
	$liczba_osob = $_POST['liczba_osob'];
	$kategoria = $_POST['Kategoria'];
	$opis = $_POST['opis'];
	
	if($liczba_osob != "")
	{
		$zapytanie_e = "update pokoje set pojemnosc=$liczba_osob, opis='$opis', kategoria=$kategoria where idPokoju=$idPokoju";
							
		if(!$zapytanie_e_run = mysql_query($zapytanie_e))
		{
			echo 'blad zaptyania edytuj';
			echo mysql_error();
		}
		
		$zapytanie_usun_2 = "delete from pokoje_wyposazenie where idPokoju = $idPokoju";
		if(!$zapytanie_usun_run_2 = mysql_query($zapytanie_usun_2))
		{
			echo 'blad zaptyania usun 2';
			echo mysql_error();
		}
		
		foreach($_POST['a'] as $selected) 
		{
				$zapytanie = "insert into pokoje_wyposazenie (idWyposazenia, idPokoju) values ($selected, $idPokoju)";
				
				if(!$zapytanie_r = mysql_query($zapytanie))
				{
					echo 'blad zaptyania dodaj wyposazenie';
					echo mysql_error();
				}
		}
		
	}
//odśw
if($czyEdycja)
{
	$zaptanie_wyposazenie = "select idwyposazenia, wyposazeniepokoju.nazwa as nazwa, typyWyposazenia.nazwa as typ
						from wyposazeniepokoju join typyWyposazenia using (typ)
						order by $sort";
						
if(!$zaptanie_wyposazenie_run = mysql_query($zaptanie_wyposazenie))
{
  echo 'blad zaptyania wyposazenie';
  echo mysql_error();
}

	$zapytanie_edycja = "select pojemnosc, opis, kategoria from pokoje where idPokoju = $idPokoju";
	
	if(!$zapytanie_edycja_run = mysql_query($zapytanie_edycja))
	{
		echo 'blad zaptyania edycja 1';
		echo mysql_error();
	}
	
	$zapytanie_edycja_row = mysql_fetch_assoc($zapytanie_edycja_run);
	
	$opis_g = $zapytanie_edycja_row['opis'];
	$osoby_g = $zapytanie_edycja_row['pojemnosc'];
	$kategoria_g = $zapytanie_edycja_row['kategoria'];
	
	$zapytanie_edycja_2 = "select idwyposazenia from pokoje_wyposazenie where idPokoju = $idPokoju";
	
	if(!$zapytanie_edycja_run_2 = mysql_query($zapytanie_edycja_2))
	{
		echo 'blad zaptyania edycja 2';
		echo mysql_error();
	}
	
	$i = 0;
	$wyposazenie_g[$i] = array();
	while($zapytanie_edycja_row_2 = mysql_fetch_assoc($zapytanie_edycja_run_2))
	{
		$wyposazenie_g[$i] = $zapytanie_edycja_row_2['idwyposazenia'];
		$i++;
	}
}
//
}

if(isset($_POST['na_pewno'])&&isset($_POST['usun_rodzaj_pokoju'])&&($czyEdycja))
{
	$zapytanie_usun_2 = "delete from pokoje_wyposazenie where idPokoju = $idPokoju";
	
		if(!$zapytanie_usun_run_2 = mysql_query($zapytanie_usun_2))
		{
			echo 'blad zaptyania usun 2';
			echo mysql_error();
		}
		
	$zapytanie_usun_1 = "delete from pokoje where idPokoju = $idPokoju";
		
		if(!$zapytanie_usun_run_1 = mysql_query($zapytanie_usun_1))
		{
			echo 'blad zaptyania usun 1';
			echo mysql_error();
		}
	
	/*$page = $_SERVER['PHP_SELF'];
	$sec = "1";
	header("Refresh: $sec; url=$page");*/
}

function addCategories()
{
	$zapytanie = "select idKategorii, nazwa from kategorie";
	
	if(!$zapytanie_run = mysql_query($zapytanie))
	{
		echo 'blad zaptyania kategorie';
		echo mysql_error();
	}
	
	while($use_query_row = mysql_fetch_assoc($zapytanie_run))
	{
		$name = $use_query_row['nazwa'];
		$id = $use_query_row['idKategorii'];
		echo "<option value=$id>$name</option>";
	}
}

function addOptions($use_query, $wyposazenie)
{	
	//echo "trutru";
	
	if(!$use_query_run = mysql_query($use_query))
	{
		echo 'blad zaptyania wyposazenie';
		echo mysql_error();
	}

	while($use_query_row = mysql_fetch_assoc($use_query_run))
	{
		$name = $use_query_row['nazwa'];
		$id = $use_query_row['idwyposazenia'];
		$typ = $use_query_row['typ'];
		$czy = false;
		
		foreach($wyposazenie as $temp)
		{
			if($temp == $id)
			{
				$czy = true;
			}
		}
		
		addRow($id, $name, $typ, $czy);
	}
}

function addRow($id, $name, $typ, $czy)
{
if($czy)
{
echo "<tr><td><input type='checkbox' name='a[]' value='$id' checked> $typ</td>
		  <td>$name</td></tr>";
}
else{
	echo "<tr><td><input type='checkbox' name='a[]' value='$id'> $typ</td>
		  <td>$name</td></tr>";}
}
?>
<div class="container">

      <center><h1>Nowy rodzaj pokoju</h1></center>
<div class="panel panel-default panel-body">
<form action="nowy_rodzaj_pokoju.php<?php if(isset($idPokoju)) echo '?id='.$idPokoju; ?>" method="POST"> 
<table style="width: 60%">
       <tr>
           <td>Liczba osób</td>
           <td><input type="text" name="liczba_osob" style="width: 160px" maxLength="11" value=<?php echo $osoby_g;?>></td>
       </tr>
       <tr>
           <td>Kategoria</td>
           <td><select name="Kategoria" style="width: 160px" value=<?php echo $kategoria;?>>
                       <?php addCategories();?>
               </select>
           </td>
       </tr>
       <tr>
           <td style="vertical-align: top">Opis</td>
           <td><input type="text" name="opis" style="width: 160px; height: 120px" value="<?php echo $opis_g; ?>"></td>
       </tr>
</table>
<br>
<table width="85%" class="table-striped">
       <thead><tr>
           <td colspan="2">Wyposażenie:</td>
       </tr>
       <tr>
           <td style="width: 30%"><a href="nowy_rodzaj_pokoju.php?<?php if(isset($idPokoju)) echo 'id='.$idPokoju.'&'; ?>sort=typ">Typ</a></td>
           <td><a href="nowy_rodzaj_pokoju.php?<?php if(isset($idPokoju)) echo 'id='.$idPokoju.'&'; ?>sort=nazwa">Nazwa</a></td>
       </tr></thead>
		<?php addOptions($zaptanie_wyposazenie, $wyposazenie_g);?>
</table>
<br><br>
<table style=" width: 85%">
       <tr>
	   <?php if($czyEdycja){echo '<td style="text-align: right; width: 20%"><input type="checkbox" name="na_pewno" value="na_pewno">na pewno</td>
         <td style="width: 35%"><input type="submit" name="usun_rodzaj_pokoju" value="usuń rodzaj pokoju"></td>';}?>
         
         <td><input type="submit" name="zapisz" value="zapisz" class="btn btn-sm btn-primary"></td>
       </tr>
</table>

</form>
</div></div>
</body>
</html>