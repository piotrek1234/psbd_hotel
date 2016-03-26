<?php
require_once "connect_to_database.inc.php";
require_once('pracownik_czy_zalogowany.inc.php');

$idKlienta = $_REQUEST['idKlienta'];


if(isset($_GET["konto"]))
{
	$konto = $_GET["konto"];
	
	if($konto == "nieaktywne")
	{
		$zapytanie_aktywne = "update klienci set kontoAktywne = 0 where idKlienta = $idKlienta";
	
		if(!$zapytanie_aktywne_run = mysql_query($zapytanie_aktywne))
		{
			echo 'blad zaptyania nieaktywne';
			echo mysql_error();
		}
	}
	else if($konto == "aktywne")
	{
		$zapytanie_aktywne = "update klienci set kontoAktywne = 1 where idKlienta = $idKlienta";
	
		if(!$zapytanie_aktywne_run = mysql_query($zapytanie_aktywne))
		{
			echo 'blad zaptyania aktywne';
			echo mysql_error();
		}
	}
	
	header("Location: klient.php?idKlienta=". $idKlienta);
}

$zapytanie_rezerwacje = "select idrezerwacji, okresod, okresdo, numerPomieszczenia, stanyRezerwacji.nazwa as stan
from rezerwacje join stanyRezerwacji using (stan) join klienci using (idKlienta)
where idKlienta = $idKlienta and stan <> 3 and stan <> 5";

if(!$zapytanie_rezerwacje_run = mysql_query($zapytanie_rezerwacje))
{
  echo 'blad zaptyania 1';
  echo mysql_error();
}

$zapytanie_jaki_klient = "select czyFirma from klienci where idKlienta = $idKlienta";

if(!$zapytanie_jaki_klient_run = mysql_query($zapytanie_jaki_klient))
{
  echo 'blad zaptyania 1';
  echo mysql_error();
}

$jaki_klient = mysql_fetch_assoc($zapytanie_jaki_klient_run);

function addOptions($use_query)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
	$date = $use_query_row['okresod'].' do '.$use_query_row['okresdo'];
	$room = $use_query_row['numerPomieszczenia'];
	$condition = $use_query_row["stan"];
	$idRezerwacji = $use_query_row["idrezerwacji"];
    addNewRow($date, $room, $condition, $idRezerwacji);
  }
}

function addNewRow($Date, $Room, $Condition, $id)
{
  echo "<tr>";
  echo "<td><a href='rezerwacja.php?idRezerwacji=$id'>$Date</a></td>";
  echo "<td>$Room</td>";
  echo "<td>$Condition</td>";
  echo "</tr>";
}
?>
<div class="container">
<h1><center>Klient</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<?php 
if(!$jaki_klient["czyFirma"])
{
$zwykly = true;
$zapytanie_inf_klienta = "select
k.idklienta, concat_ws(' ', k.imie, k.nazwisko) as Klient, ifnull(f.nazwafirmy, '') as firma, k.adreskraj, k.adresmiasto,
k.adresulica, k.adreskod, k.telefon, ifnull(k.email, '-') as email, ifnull(k.nip, '-') as nip, k.kontoAktywne
from klienci as k left join klienci as f on k.idfirmy = f.idklienta
where
k.idKlienta = $idKlienta";

if(!$zapytanie_inf_klienta_run = mysql_query($zapytanie_inf_klienta))
{
  echo 'blad zapytania klient';
}

$inf_klienta = mysql_fetch_assoc($zapytanie_inf_klienta_run);

$nazwa = $inf_klienta["Klient"];
if($inf_klienta["firma"] == "")
{
	$nazwa_firmy = $inf_klienta["firma"];
}
else
{
	$nazwa_firmy = "(".$inf_klienta["firma"].")";
}
$kraj = $inf_klienta["adreskraj"];
$ulica = $inf_klienta["adresulica"];
$miasto = $inf_klienta["adresmiasto"];
$kod = $inf_klienta["adreskod"];
$tel = $inf_klienta["telefon"];
$email = $inf_klienta["email"];
$nip = $inf_klienta["nip"];

echo
"
Klient: $nazwa $nazwa_firmy<br>
$kraj, $miasto, $ulica, $kod<br>
telefon: $tel<br>
email: $email <br>
NIP: $nip      <br>";
}else
{
$zwykly=false;
$zapytanie_inf_firmy = "select nazwaFirmy, concat_ws(' ', imie, nazwisko) as opiekun, adreskraj, adresmiasto, adresulica, adreskod, telefon,
ifnull(email, '-') as email, nip, regon, kontoAktywne
from klienci
where idKlienta = $idKlienta";

if(!$zapytanie_inf_firmy_run = mysql_query($zapytanie_inf_firmy))
{
  echo 'blad zapytania firma';
}

$inf_firmy = mysql_fetch_assoc($zapytanie_inf_firmy_run);

$opiekun = $inf_firmy["opiekun"];
$nazwa = $inf_firmy["nazwaFirmy"];
$kraj = $inf_firmy["adreskraj"];
$ulica = $inf_firmy["adresulica"];
$miasto = $inf_firmy["adresmiasto"];
$kod = $inf_firmy["adreskod"];
$tel = $inf_firmy["telefon"];
$email = $inf_firmy["email"];
$nip = $inf_firmy["nip"];
$regon = $inf_firmy["regon"];

echo
"Klient: $nazwa<br>
$kraj, $miasto, $ulica, $kod<br>
Osoba kontaktowa: $opiekun<br>
telefon: $tel<br>
email: $email<br>
NIP: $nip Regon: $regon<br>";
}
echo '<a href="';
if($zwykly) echo 'edycja_danych.php'; else echo 'edycja_firmy.php';
echo '?idKlienta=', $idKlienta, '">Edycja danych</a>';
?>
</div>
</div>
<div class="panel panel-default">
<div class="panel-body">
<form action="klient.php" method="GET">
<table style="width: 50%; text-align: left" cellspacing="10">
       <th>Konto aktywne:</th>
	   
		<?php 
	   
		$zapytanie_inf_klienta = "select
		k.kontoAktywne
		from klienci as k left join klienci as f on k.idfirmy = f.idklienta
		where
		k.idKlienta = $idKlienta";

		if(!$zapytanie_inf_klienta_run = mysql_query($zapytanie_inf_klienta))
		{	
			echo 'blad zapytania klient';
		}

		$inf_klienta = mysql_fetch_assoc($zapytanie_inf_klienta_run);
		
		$aktywne = $inf_klienta['kontoAktywne'];
		
		if($aktywne == 1)
		{
			echo 
			"<td>tak</td>
			 <td><a href=\"klient.php?idKlienta=$idKlienta&konto=nieaktywne\" class=\"btn btn-sm btn-primary\">dezaktywuj</a></td>";
		}
		else
		{
			echo
			"<td>nie</td>
			 <td><a href=\"klient.php?idKlienta=$idKlienta&konto=aktywne\" class=\"btn btn-sm btn-primary\">aktywuj</a></td>";
		}
		?>
</table>
</div></div>
<div class="panel panel-default panel-body">
<table style="width: 65%; text-align:left" cellspacing="10">
       <tr>
           <th colspan="3">Najbliższe rezerwacje:</th>
       </tr>
       <tr>
           <th>Okres rezerwacji</th>
           <th>Pomieszczenie</th>
           <th>Stan</th>
       </tr>
       <tr></tr>
       <?php addOptions($zapytanie_rezerwacje_run)?>
</table>
</div>
<div class="panel panel-default panel-body">
<table style="width: 20%; text-align: left" cellspacing="10">
       <tr>
			
			<?php
			$zapytanie_rachunek = "select sum(cena*ilosc)*(1-znizka/100) as kwota, idRachunku
									from pozycjerachunkow join uslugi using (idUslugi) join rachunki using (idRachunku)
									where idRachunku = (select idRachunku
									from rachunki join klienci using (idKlienta)
									where czyZaplacony = 0 and idKlienta = $idKlienta)";

			if(!$zapytanie_rachunek_run = mysql_query($zapytanie_rachunek))
			{
				echo 'blad zaptyania rachunek';
				echo mysql_error();
			}

			$rachunek = mysql_fetch_assoc($zapytanie_rachunek_run);
			$kasa = $rachunek["kwota"];
			$idRachunku = $rachunek["idRachunku"];
			
			if($kasa == "NULL")
			{
				$kasa = 0;
			}
			if(isset($idRachunku))
			echo "<th><a href='rachunek_28.php?idRachunku=$idRachunku'>Rachunek:</a></th>
					<td>", number_format((float)$kasa, 2, '.', ''), " zł</td>";
			else echo "<th>Rachunek:</th>
					<td>brak</td>";
			
			?>
			
       </tr>
</table>
</div>
</body>
</html>