<?php

//UWAGA NA POLE Z FIRMĄ!!!

$idKlienta = 1;
$czyPracownik = true;

//echo $czyPracownik;
require_once "connect_to_database.inc.php";
require_once "funkcje.php";

$wiadomosc = "";

if(isset($_GET['idKlienta']))
{
	//czyli jest zalogowany pracownik
	require_once 'pracownik_czy_zalogowany.inc.php';
	$idKlienta = $_REQUEST['idKlienta'];
	$czyPracownik = true;
}
else	//zalogowany klient
{
	require_once 'klient_czy_zalogowany.php';
	$czyPracownik = false;
}	

if(isset($_POST["zapisz"]))
{
  if(isset($_POST["imie"])&&isset($_POST["nazwisko"])&&isset($_POST["kraj"])&&isset($_POST["miasto"])
     &&isset($_POST["ulica"])&&isset($_POST["kod_pocztowy"])&&isset($_POST["nr_tel"]))
  {
      $tel = $_POST["nr_tel"];
      $kod = $_POST["kod_pocztowy"];
	  $imie = $_POST["imie"];
	  $nazwisko = $_POST["nazwisko"];
	  $kraj = $_POST["kraj"];
	  $miasto = $_POST["miasto"];
	  $ulica = $_POST["ulica"];
	  if(isset($_POST['mail'])) $email = $_POST['mail']; else $email = '';
	  if(isset($_POST['NIP'])) $nip = $_POST['NIP']; else $nip = '';
	  
	  $czy_poprawnie = true;
	  
	  if(!czyTylkoLitery($nazwisko))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: nazwisko";
	  }
	  
	  if(!czyTylkoLitery($imie))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: imie";
	  }
	  
	  if(!czyTylkoLitery($kraj))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: kraj";
	  }
	  
	  if(!czyTylkoLitery($miasto))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: miasto";
	  }
	  
	  if(!sprNIP($nip))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: nip";
	  }
	  
	  if(!sprMail($email))
	  {
		$czy_poprawnie = false;
		echo $wiadomosc = "zły format: mail";
	  }
	  
	if($czy_poprawnie)
	{	 
		  if(false)
		  {
			  $wiadomosc = "<font color='red'>Niepoprawny format numeru</font>";
		  }
		  else
		  {      
			if($czyPracownik)
			{
				$query = "UPDATE klienci SET 
			  imie = '$imie',
			  nazwisko = '$nazwisko',
			  adreskraj = '$kraj',
			  adresMiasto = '$miasto',
			  adresUlica = '$ulica',
			  telefon = '$tel',
			  adresKod = '$kod',
			  email = '$email',
			  nip = '$nip'";
			  if($_POST['firma'] != 0) $query .= ", idFirmy = '{$_POST['firma']}'"; else $query .= ", idFirmy = NULL";
			  $query .= " WHERE idKlienta = $idKlienta";
			  //echo $query;
			}
			else
			{
				$query = "UPDATE klienci SET 
			  imie = '$imie',
			  nazwisko = '$nazwisko',
			  adreskraj = '$kraj',
			  adresMiasto = '$miasto',
			  adresUlica = '$ulica',
			  telefon = '$tel',
			  adresKod = '$kod',
			  email = '$email',
			  nip = '$nip'
			  WHERE idKlienta = $idKlienta";
			}
			  
			  
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

$query_help = "select nazwaFirmy, idKlienta from klienci where czyFirma = 1";

if(!$query_help_run = mysql_query($query_help))
{
  echo 'blad zapytania help';
}

//echo $idKlienta;

$query_one = "SELECT imie, nazwisko, adreskraj, adresmiasto, adresulica, adreskod, telefon, IFNULL(nazwafirmy, '-') AS firma, IFNULL(email, '') AS mail,
         IFNULL(nip, '') AS NIP FROM klienci WHERE idKlienta=$idKlienta";

if(!$query_one_run = mysql_query($query_one))
{
  echo 'blad zaptyania 1';
}

$query_one_row = mysql_fetch_assoc($query_one_run);

function addOptions($use_query)
{
  echo "<option value=0>(żadna)</option>";

  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $name = $use_query_row['nazwaFirmy'];
    $id = $use_query_row['idKlienta'];
    echo "<option value=\"$id\">$name</option>";
  }
}
?>

<div class="container">
<h1><center>Edycja danych</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<form action="edycja_danych.php<?php if(isset($pracownik)) echo '?idKlienta=', $idKlienta; ?>" method="POST">
<center>
<table style="width: 50%">
       <tr>
           <td>Imię</td>
           <td><input type="text" name="imie" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['imie']?>" required></td>
       </tr>
       <tr>
           <td>Nazwisko</td>
           <td><input type="text" name="nazwisko" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['nazwisko']?>" required></td>
       </tr>
       <tr>
           <td>Kraj</td>
           <td><input type="text" name="kraj" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['adreskraj']?>" required></td>
       </tr>
       <tr>
           <td>Miasto</td>
           <td><input type="text" name="miasto" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['adresmiasto']?>" required></td>
       </tr>
       <tr>
           <td>Ulica i numer</td>
           <td><input type="text" name="ulica" style="width: 220px" maxLength="100" value="<?php echo $query_one_row['adresulica']?>" required></td>
       </tr>
       <tr>
           <td>Kod pocztowy</td>
           <td><input type="text" name="kod_pocztowy" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['adreskod']?>" required></td>
       </tr>
       <tr>
           <td>Numer telefonu</td>
           <td><input type="text" name="nr_tel" style="width: 220px" maxLength="20" value="<?php echo $query_one_row['telefon']?>" required></td>
       </tr>
       <tr>
           <td>Adres email*</td>
           <td><input type="text" name="mail" style="width: 220px" maxLength="45" value="<?php echo $query_one_row['mail']?>"></td>
       </tr>
       <tr>
           <td>NIP*</td>
           <td><input type="text" name="NIP" style="width: 220px" maxLength="10" value="<?php echo $query_one_row['NIP']?>"></td>
       </tr>
<?php
	   //echo "asdfasdfasg";
	   if(isset($_GET['idKlienta']))
	   {
			$czyPracownik = true;
	   }
	   else
	   {
			$czyPracownik = false;
	   }
	   
	   if($czyPracownik)
	   {
		//echo "fasdf";
	   echo '<tr>
           <td>Firma</td>
           <td>
               <select style="width: 220px" name="firma">';
       addOptions($query_help_run);
        echo '</select>
           </td>
       </tr>';
	   }
	   //echo "dfgsdg";
?>
       <tr>
           <td colspan="2">*-nieobowiązkowe</td>
       </tr>
       <tr>
           <td colspan="2"><?php echo $wiadomosc?></td>
       </tr>
       <tr style="text-align: left">
           <td></td>
           <td><input type="submit" name="zapisz" value="zapisz" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</center>
</form>
</div></div>
</div>
</body>
</html>