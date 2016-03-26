<?php
require_once "connect_to_database.inc.php";
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');

$wiadomosc = "";

if(isset($_POST['dod_firme']))
{
	if(isset($_POST['login'])&&isset($_POST['haslo'])&&isset($_POST['r_haslo'])
	   &&isset($_POST['naz_firmy'])&&isset($_POST['kraj'])&&isset($_POST['miasto'])
	   &&isset($_POST['ulica'])&&isset($_POST['kod_pocztowy'])&&isset($_POST['nr_tel'])
	   &&isset($_POST['NIP'])&&isset($_POST['regon'])
	   &&isset($_POST['imie'])&&isset($_POST['nazwisko']))
	{
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		$r_haslo = $_POST['r_haslo'];
		$naz_firmy = $_POST['naz_firmy'];
		$kraj = $_POST['kraj'];
		$miasto = $_POST['miasto'];
		$ulica = $_POST['ulica'];
		$kod_pocztowy = $_POST['kod_pocztowy'];
		$nr_tel = $_POST['nr_tel'];
		$mail = $_POST['mail'];
		$NIP = $_POST['NIP'];
		$regon = $_POST['regon'];
		$imie = $_POST['imie'];
		$nazwisko = $_POST['nazwisko'];
		
		if($login == "" || $haslo == "" || $r_haslo == "" || $naz_firmy == "" || $kraj == ""
		 || $miasto == "" || $ulica == "" || $kod_pocztowy == "" || $nr_tel == ""
		  || $NIP == "" || $NIP == "" || $imie == "" || $nazwisko == "")
		  {$wiadomosc = "<div class=\"alert alert-danger\">Wprowadź wszystkie obowiązkowe dane</div>";
		}
		else if(!czyTylkoLitery($kraj) || !czyTylkoLitery($miasto))
		{
			echo $wiadomosc = "zły format danych: sprawdź miasto/kraj";
		}
		else if(!sprMail($mail))
		{
			echo $wiadomosc = "zły format danych: sprawdź adres email";
		}
		else if(!czyTylkoLitery($imie) || !czyTylkoLitery($imie))
		{
			echo $widomosc = "zły format danych: sprawdz imie/nazwisko";
		}
		else if(!sprNIP($NIP))
		{
			echo $widomosc = "zły format danych: NIP";
		}
		else if(!sprRegon($regon))
		{
			echo $widomosc = "zły format danych: REGON";
		}
		else{
		if($haslo == $r_haslo)
		{
			$haslo = md5($haslo);
			$query_one = "INSERT INTO klienci (idKlienta, login, haslo, imie, nazwisko, nazwaFirmy, telefon, email, adresKraj, adresMiasto,
							adresUlica, adresKod, kontoAktywne, NIP, regon, czyFirma, idFirmy) VALUES (NULL, '$login', '$haslo', '$imie',
							'$nazwisko', '$naz_firmy', '$nr_tel', '$mail', '$kraj', '$miasto', '$ulica', '$kod_pocztowy', '1', '$NIP', '$regon', '1', NULL);";

			if(!$query_one_run = mysql_query($query_one))
			{
				echo 'blad zaptyania 1';
				echo mysql_error();
			}
			$wiadomosc = "<div class=\"alert alert-success\">Zapisano zmiany</div>";
		}
		else
		{
			$wiadomosc = "<div class=\"alert alert-danger\">Hasła się nie zgadzają</div>";
		}
		}
	}
	else
	{
		$wiadomosc = "<div class=\"alert alert-danger\">Wprowadź wszystkie obowiązkowe dane</div>";
	}
}

?>

<div class="container">
<h1><center>Nowa firma</center></h1>
<div class="panel panel-default panel-body">
<form action="nowa_firma.php" method="POST">
<center><table style="width: 40%" cellspacing="10">
       <tr>
           <td>Login</td>
           <td><input type="text" name="login" style="width: 180" maxlength="45" value="<?php if(isset($_POST['login'])) echo $_POST['login'];?>" required></td>
       </tr>
       <tr>
           <td>Hasło</td>
           <td><input type="password" name="haslo" style="width: 180" maxlength="45" required></td>
       </tr>
       <tr>
           <td>Powtórz hasło</td>
           <td><input type="password" name="r_haslo" style="width: 180" maxlength="45" required></td>
       </tr>
       <tr>
           <td>Nazwa firmy</td>
           <td><input type="text" name="naz_firmy" style="width: 180" maxlength="45" value="<?php if(isset($_POST['naz_firmy'])) echo $_POST['naz_firmy'];?>" required></td>
       </tr>
       <tr>
           <td>Kraj</td>
           <td><input type="text" name="kraj" style="width: 180" maxlength="45" value="<?php if(isset($_POST['kraj'])) echo $_POST['kraj'];?>" required></td>
       </tr>
       <tr>
           <td>Miasto</td>
           <td><input type="text" name="miasto" style="width: 180" maxlength="45" value="<?php if(isset($_POST['miasto'])) echo $_POST['miasto'];?>" required></td>
       </tr>
       <tr>
           <td>Ulica i numer</td>
           <td><input type="text" name="ulica" style="width: 180" maxlength="100" value="<?php if(isset($_POST['ulica'])) echo $_POST['ulica'];?>" required></td>
       </tr>
       <tr>
           <td>Kod pocztowy</td>
           <td><input type="text" name="kod_pocztowy" style="width: 180" maxlength="10" value="<?php if(isset($_POST['kod_pocztowy'])) echo $_POST['kod_pocztowy'];?>" required></td>
       </tr>
       <tr>
           <td>Numer telefonu</td>
           <td><input type="text" name="nr_tel" style="width: 180" maxlength="20" value="<?php if(isset($_POST['nr_tel'])) echo $_POST['nr_tel'];?>" required></td>
       </tr>
       <tr>
           <td>Adres email*</td>
           <td><input type="text" name="mail" style="width: 180" maxlength="45" value="<?php if(isset($_POST['mail'])) echo $_POST['mail'];?>"></td>
       </tr>
       <tr>
           <td>NIP</td>
           <td><input type="text" name="NIP" style="width: 180" maxlength="10" value="<?php if(isset($_POST['NIP'])) echo $_POST['NIP'];?>" required></td>
       </tr>
       <tr>
           <td>Regon</td>
           <td><input type="text" name="regon" style="width: 180" maxlength="9" value="<?php if(isset($_POST['regon'])) echo $_POST['regon'];?>" required></td>
       </tr>
       <tr>
           <th>Osoba kontaktowa</th>
           <td></td>
       <tr>
       <tr>
           <td>Imię</td>
           <td><input type="text" name="imie" style="width: 180" maxlength="45" value="<?php if(isset($_POST['imie'])) echo $_POST['imie'];?>" required></td>
       </tr>
       <tr>
           <td>Nazwisko</td>
           <td><input type="text" name="nazwisko" style="width: 180" maxlength="45" value="<?php if(isset($_POST['nazwisko'])) echo $_POST['nazwisko'];?>" required></td>
       </tr>
	   <tr>
			<td colspan="2">* - nieobowiązkowe</td>
		<tr>
			<td colspan="2"><?php echo $wiadomosc;?></td>
		</tr>
		</tr>
       <tr>
           <td></td>
           <td style="text-align: left; width: 180"><input type="submit" name="dod_firme" value="dodaj firmę" class="btn btn-sm btn-primary"></td>
       </tr>
</table></center>
</form>
</div></div>
</body>
</html>