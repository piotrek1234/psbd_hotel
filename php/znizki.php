<?php

require_once "connect_to_database.inc.php";
require_once 'pracownik_czy_zalogowany.inc.php';
require_once 'funkcje.php';

$wiadomosc = "";

$nazwisko = '%';
$sort = 'nazwisko';

if(isset($_GET['sort']))
{
	$sort = $_GET['sort'];
}

////////////////////////////nazwisko
if(isset($_POST['filtruj']))
{
	$temp = $_POST['naz_zawiera'];
	
	if(czyTylkoLitery($temp))
	{
		if($temp != "")
		{
			$nazwisko = "%".$temp."%";
		}
	}
	else
	{
		echo $wiadomosc = "zły format: nazwisko zawiera";
	}
}
////////////////////////////

////////////////////////////nowa znizka
if(isset($_POST['udziel'])&&isset($_POST['Client'])&&($_POST['znizka'] <= 20))
{
	$znizka_g = $_POST['znizka'];
	
	if(czyCalkowita($znizka_g) && (((int)$znizka_g) <= 20))
	{
		if($znizka_g != "")
		{
			$idRachunku = $_POST['Client'];
			$zapytanie_pomocnicze = "update rachunki set znizka = $znizka_g where idRachunku = $idRachunku";
			
			if(!$zapytanie_pomocnicze_run = mysql_query($zapytanie_pomocnicze))
			{
			  echo 'blad zaptyania lista znizka<br>';
			  echo mysql_error();
			}
		}
	}
	else
	{
		echo $wiadomosc = "zły format: zniżka";
	}
}
////////////////////////////

////////////////////////////zapytanie lista rachunkow
$zapytanie_lista_rachunkow = "select idRachunku, concat_ws(' ', imie, nazwisko) as klient, dataWystawienia, znizka
								from rachunki join klienci using (idKlienta)
								where czyZaplacony = 0 and nazwisko like '$nazwisko'
								order by $sort";
								
if(!$zapytanie_lista_rachunkow_run = mysql_query($zapytanie_lista_rachunkow))
{
  echo 'blad zaptyania lista rachunkow<br>';
  echo mysql_error();
}

function addOptions($zapytanie_run)
{
	while($zapytanie_row = mysql_fetch_assoc($zapytanie_run))
	{
		$idRachunku = $zapytanie_row['idRachunku'];
		$klient = $zapytanie_row['klient'];
		$dataWystawienia = $zapytanie_row['dataWystawienia'];
		$znizka = $zapytanie_row['znizka'];
		$cena = 0;
		
		$zapytanie_pomocnicze = "select sum(cena*ilosc) as przedZnizka, sum(cena*ilosc)*(1-znizka/100) as poZnizce
								from pozycjerachunkow join uslugi using (idUslugi) join rachunki using (idRachunku)
								where idRachunku = $idRachunku";
								
		if(!$zapytanie_pomocnicze_run = mysql_query($zapytanie_pomocnicze))
		{
		  echo 'blad zaptyania lista pomocniczego<br>';
		  echo mysql_error();
		}
	
		$zapytanie_pomocnicze_row = mysql_fetch_assoc($zapytanie_pomocnicze_run);
		
		$cena = $zapytanie_pomocnicze_row['poZnizce'];
		
		$zapytanie_pomocnicze_rachunek = "select sum(ilosc*cena) as koszt from pozycjeRachunkow join uslugi using (idUslugi) where idRachunku = $idRachunku";
		
		if(!$zapytanie_pomocnicze_rachunek_run = mysql_query($zapytanie_pomocnicze_rachunek))
		{
		  echo 'blad zaptyania lista pomocniczego rachunek<br>';
		  echo mysql_error();
		}
		
		$zapytanie_pomocnicze_rachunek_row = mysql_fetch_assoc($zapytanie_pomocnicze_rachunek_run);
		
		$cena += $zapytanie_pomocnicze_rachunek_row['koszt'];
		/*if($znizka == 0)
		{
			$cena = $zapytanie_pomocnicze_row['przedZnizka'];
		}
		else
		{
			$cena = $zapytanie_pomocnicze_row['przedZnizka'];
		}*/
		
		addNewRowToCategories($idRachunku, $klient, $dataWystawienia, $cena, $znizka);
	}
}
function addNewRowToCategories($id, $Client, $Date, $Cost, $Discount)
{
  echo "<tr>";
  echo "<td><input type='radio' name='Client' value=$id>$Client</td>";
  echo "<td>$Date</td>";
  echo "<td>$Cost</td>";
  echo "<td>$Discount</td>";
  echo "</tr>";
}
////////////////////////////////


?>

<div class="container">
      <h1><center>Zniżki</center></h1>
	  <div class="panel panel-default panel-body">
<form action="znizki.php" method="POST">
<table style="width: 50%">
       <tr style="text-align: left">
           <th>Nazwisko zawiera:</th>
           <td><input type="text" name="naz_zawiera" maxLength = "45" style="width: 160" value="<?php if(isset($_POST['naz_zawiera'])) echo $_POST['naz_zawiera']; ?>"></td>
           <td><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
<table width="100%">
       <tr style="text-align: left">
           <th><a href="znizki.php?sort=nazwisko">Klient</a></th>
           <th><a href="znizki.php?sort=dataWystawienia">Data wystawienia</a></th>
           <th>Kwota do zapłaty</th>
           <th>Udzielona zniżka</th>
       </tr>
       <tr></tr><tr></tr><tr></tr>
       <?php addOptions($zapytanie_lista_rachunkow_run);?>
</table>
<br>
<table style="width: 50%">
       <tr style="text-align: left">
           <th>Udziel zniżki (0-20%):</th>
           <td><input type="text" name="znizka" style="width: 160"></td>
           <td><input type="submit" name="udziel" value="udziel" maxLength = "2" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div>
</div>
</body>
<html>