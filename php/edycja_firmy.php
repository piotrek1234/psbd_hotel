<?php
require_once "connect_to_database.inc.php";
require_once('pracownik_czy_zalogowany.inc.php');

$idKlienta = $_REQUEST['idKlienta'];

$wiadomosc = ""; // do komunikowania błędu

require_once "funkcje.php";
$idKlienta = 2;

if(isset($_POST["zapisz"]))
{
  if(isset($_POST["naz_firmy"])&&isset($_POST["nazwisko"])&&isset($_POST["kraj"])&&isset($_POST["miasto"])
     &&isset($_POST["ulica"])&&isset($_POST["kod_pocztowy"])&&isset($_POST["nr_tel"])&&isset($_POST["imie"])
	 &&isset($_POST["NIP"])&&isset($_POST["regon"]))
  {
		$tel = $_POST["nr_tel"];
		$kod = $_POST["kod_pocztowy"];
		$imie = $_POST["imie"];
		$nazwisko = $_POST["nazwisko"];
		$kraj = $_POST["kraj"];
		$miasto = $_POST["miasto"];
		$ulica = $_POST["ulica"];
		$naz_firmy = $_POST["naz_firmy"];
		$nip = $_POST["NIP"];
		$regon = $_POST["regon"];
		$mail = $_POST["mail"];
	  
		if(!czyTylkoLitery($imie) || !czyTylkoLitery($nazwisko) || !czyTylkoLitery($nazwisko) || !czyTylkoLitery($kraj) || !czyTylkoLitery($miasto))
		{
			echo $wiadomosc = "Niepoprawny typ danej (cyfry/znaki w polu literowym)";
		}
		else if(!sprNIP($nip))
		{
			echo $wiadomosc = "Niepoprawny NIP)";
		}
		else if(!sprRegon($regon))
		{
			echo $wiadomosc = "Niepoprawny regon";
		}
		else if(!sprMail($mail))
		{
			echo $wiadomosc = "Niepoprawny mail";
		}
		else
		{
		  if(false)
		  {
			  $wiadomosc = "<font color='red'>Niepoprawny format numeru</font>";
		  }
		  else
		  {      
			  $query = "UPDATE klienci SET
			  nazwaFirmy = '$naz_firmy',
			  NIP = '$nip',
			  regon = '$regon',
			  imie = '$imie',
			  nazwisko = '$nazwisko',
			  adreskraj = '$kraj',
			  adresMiasto = '$miasto',
			  adresUlica = '$ulica',
			  telefon = '$tel',
			  adresKod = '$kod'
			  WHERE idKlienta = $idKlienta";
			  
			  if(!$query_run = mysql_query($query))
			  {
				echo 'blad zapytania update';
				echo mysql_error();
			  }
			  $wiadomosc = "<div class=\"alert alert-success\">Zapisano zmiany</div>";
		  }
		}
  }
  else
  {
     $wiadomosc = "<div class=\"alert alert-danger\">Wprowadź wszystkie obowiązkowe dane</div>";
  }
}
else
{
    $wiadomosc = '';
}

$pobranie_danych_firmy_zapytanie = "SELECT nazwaFirmy, imie, nazwisko, adreskraj, adresmiasto, adresulica, adreskod, telefon, IFNULL(email, '') AS mail,
         IFNULL(nip, '') AS NIP, IFNULL(regon, '') AS regon FROM klienci WHERE idKlienta=$idKlienta";

if(!$zapytanie_dzialanie = mysql_query($pobranie_danych_firmy_zapytanie))
{
  echo 'blad zaptyania 1';
}

$pobrane_dane_firm = mysql_fetch_assoc($zapytanie_dzialanie);
		 
?>
<div class="container">
<h1><center>Edycja firmy</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<form action="edycja_firmy.php?idKlienta=<?php echo $idKlienta; ?>" method="POST">
<center><table style="width: 40%" cellspacing="10">
       <tr>
           <td>Nazwa firmy</td>
           <td><input type="text" name="naz_firmy" style="width: 180" value="<?php echo $pobrane_dane_firm['nazwaFirmy']?>" required></td>
       </tr>
       <tr>
           <td>Kraj</td>
           <td><input type="text" name="kraj" style="width: 180" value="<?php echo $pobrane_dane_firm['adreskraj']?>" required></td>
       </tr>
       <tr>
           <td>Miasto</td>
           <td><input type="text" name="miasto" style="width: 180" value="<?php echo $pobrane_dane_firm['adresmiasto']?>" required></td>
       </tr>
       <tr>
           <td>Ulica i numer</td>
           <td><input type="text" name="ulica" style="width: 180" value="<?php echo $pobrane_dane_firm['adresulica']?>" required></td>
       </tr>
       <tr>
           <td>Kod pocztowy</td>
           <td><input type="text" name="kod_pocztowy" style="width: 180" value="<?php echo $pobrane_dane_firm['adreskod']?>" required></td>
       </tr>
       <tr>
           <td>Numer telefonu</td>
           <td><input type="text" name="nr_tel" style="width: 180" value="<?php echo $pobrane_dane_firm['telefon']?>" required></td>
       </tr>
       <tr>
           <td>Adres email*</td>
           <td><input type="text" name="mail" style="width: 180" value="<?php echo $pobrane_dane_firm['mail']?>"></td>
       </tr>
       <tr>
           <td>NIP</td>
           <td><input type="text" name="NIP" style="width: 180" value="<?php echo $pobrane_dane_firm['NIP']?>" required></td>
       </tr>
       <tr>
           <td>Regon</td>
           <td><input type="text" name="regon" style="width: 180" value="<?php echo $pobrane_dane_firm['regon']?>" required></td>
       </tr>
       <tr>
           <th>Osoba kontaktowa</th>
           <td></td>
       <tr>
       <tr>
           <td>Imię</td>
           <td><input type="text" name="imie" style="width: 180" value="<?php echo $pobrane_dane_firm['imie']?>" required></td>
       </tr>
       <tr>
           <td>Nazwisko</td>
           <td><input type="text" name="nazwisko" style="width: 180" value="<?php echo $pobrane_dane_firm['nazwisko']?>" required></td>
       </tr>
	   <tr>
			<td>* - nieobowązkowe</td>
			<td></td>
	   </tr>
	   <tr>
           <td colspan="2"><?php echo $wiadomosc?></td>
       </tr>
       <tr>
           <td></td>
           <td style="text-align: left; width: 180"><input type="submit" name="zapisz" value="zapisz" class="btn btn-sm btn-primary"></td>
       </tr>
</table></center>
</form>
</div></div></div>
</body>
</html>