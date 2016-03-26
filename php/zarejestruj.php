<?php
require_once "connect_to_database.inc.php";
//require_once "klient_czy_zalogowany.php";
require_once "funkcje.php";

$czyPracownik = false;

if(isset($_GET['pracownik']))
{
	//czyli jest zalogowany pracownik
	require_once 'pracownik_czy_zalogowany.inc.php';
	//$idKlienta = $_REQUEST['idKlienta'];
	$czyPracownik = true;
}
else	//zalogowany klient
{
	require_once 'klient_czy_zalogowany.php';
	$czyPracownik = false;
}

//echo "adfsfadsf";

$wiadomosc = "";

$komunikat = '';
$komunikat_zle_haslo = '';
$komunikat_login = '';

if(isset($_POST['zarejestruj']))
{
	$wszystkoOK = true;
	
    $login = $_POST["login"];
    $haslo = $_POST['haslo'];
    $p_haslo = $_POST['powtorz_haslo'];
	
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $kraj = $_POST['kraj'];
    $miasto = $_POST['miasto'];
    $ulica = $_POST['ulica'];
    $kod = $_POST['kod_pocztowy'];
    $nr_tel = $_POST['nr_telefonu'];
    $mail = $_POST['mail'];
    $nip = $_POST['NIP'];
	if($czyPracownik) $regon = $_POST['regon'];
	if($czyPracownik) $firma = $_POST['firma']; else $firma = 0;

	if(!czyTylkoLitery($imie))
	{
		echo $wiadomosc = "zły format: imie";
		$wszystkoOK = false;
	}
	if(!czyTylkoLitery($nazwisko))
	{
		echo $wiadomosc = "zły format: nazwisko";
		$wszystkoOK = false;
	}
	if(!czyTylkoLitery($kraj))
	{
		echo $wiadomosc = "zły format: kraj";
		$wszystkoOK = false;
	}
	if(!czyTylkoLitery($miasto))
	{
		echo $wiadomosc = "zły format: miasto";
		$wszystkoOK = false;
	}
	if(!czyTylkoLitery($miasto))
	{
		echo $wiadomosc = "zły format: miasto";
		$wszystkoOK = false;
	}
	if(!sprMail($mail))
	{
		echo $wiadomosc = "zły format: mail";
		$wszystkoOK = false;
	}
	if(!sprNIP($nip))
	{
		echo $wiadomosc = "zły format: NIP";
		$wszystkoOK = false;
	}
	if($czyPracownik)
	{
		if(!sprRegon($regon))
		{
			echo $wiadomosc = "zły format: regon";
			$wszystkoOK = false;
		}
	}
	
	if($wszystkoOK)
	{
	  if(!empty($_POST['login'])&&!empty($_POST['haslo'])&&!empty($_POST['powtorz_haslo'])&&!empty($_POST['imie'])
		  &&!empty($_POST['nazwisko'])&&!empty($_POST['kraj'])&&!empty($_POST['miasto'])&&!empty($_POST['ulica'])
		  &&!empty($_POST['kod_pocztowy'])&&!empty($_POST['kod_pocztowy'])&&!empty($_POST['nr_telefonu']))
	  {
		//sprawdzenie unikalnosci loginu
		$query_login = "SELECT * FROM klienci WHERE login='$login'";

		if(!$query_login_run = mysql_query($query_login))
		{
		  echo 'blad zapytania login unikalny';
		}

		$query_login_row = mysql_fetch_assoc($query_login_run);

		if(empty($query_login_row))
		{
			if($haslo == $p_haslo)
			{
			   //tutaj zapytanie
			   if($firma != 0)
			   {
			   //gdy klient to firma
				   $query_firm = "SELECT idFirmy, nazwaFirmy FROM klienci WHERE idFirmy=$firma";

				   if(!$query_frim_run = mysql_query($query_firm))
				   {
					 echo 'blad zapytania firma';
				   }
				   
				   $query_firm_row = mysql_fetch_assoc($query_firm_run);
				   $czy_firma = 1;
				   $firma_naz = $query_firm_row['nazwaFirmy'];
		
			   }
			   else
			   {
				 //gdy zwykły klient (nie firma)
				   $firma = 0;
				   $czy_firma = 0;
			   }
			   $haslo = md5($haslo);

			   if($czy_firma == 0)
			   {
					  $insert_query = "INSERT INTO klienci ( login, haslo, imie, nazwisko, nazwaFirmy, telefon, email, adresKraj, adresMiasto, adresUlica, adresKod,
													  kontoAktywne, NIP, regon, czyFirma, idFirmy)
							   VALUES
							   ( '$login', '$haslo', '$imie', '$nazwisko', '', '$nr_tel', '$mail', '$kraj', '$miasto',
							   '$ulica', '$kod', 0, '$nip', NULL, 0, NULL)";

			   }else
			   {
					$insert_query = "INSERT INTO klienci ( login, haslo, imie, nazwisko, nazwaFirmy, telefon, email, adresKraj, adresMiasto, adresUlica, adresKod,
													  kontoAktywne, NIP, regon, czyFirma, idFirmy)
							   VALUES
							   ( '$login', '$haslo', '$imie', '$nazwisko', '$firma_naz', '$nr_tel', '$mail', '$kraj', '$miasto',
							   '$ulica', '$kod', 1, '$nip', '$regon', '$czy_firma', $firma)";
			   }


			   if(!$insert_query_run = mysql_query($insert_query))
			   {
				 echo mysql_error();
			   }
			   else $komunikat = "<font color='green'>Rejestracja zakończona pomyślnie</font><meta http-equiv=\"refresh\" content=\"2; url=zaloguj_klient.php\">";
			   

			}
			else
			{
			  $komunikat/*_zle_haslo*/ = "<font color='red'>Niezgodne hasła</font>";
			}
		}
		else
		{
		  $komunikat/*_login*/ = "<font color='red'>Login zajęty</font>";
		}
	  }
	  else
	  {
		$komunikat = "<font color='red'>Puste obowiązkowe pola</font>";
	  }
	}
}
else
{
    $login = '';
    $imie = '';
    $nazwisko = '';
    $kraj = '';
    $miasto = '';
    $ulica = '';
    $kod = '';
    $nr_tel = NULL;
    $mail = '';
    $nip = NULL;
    $regon = NULL;
    
    $komunikat_zle_haslo = '';
}

function addOptions()
{
  $query_temp = "SELECT DISTINCT idKlienta, nazwaFirmy FROM klienci WHERE czyFirma=1";
  if(!$query_temp_run=mysql_query($query_temp))
  {
     echo 'blad opcji';
  }

  echo "<option value=0>Brak</option>";

  while($query_temp_row = mysql_fetch_assoc($query_temp_run))
  {
    $name = $query_temp_row['nazwaFirmy'];
    $id = $query_temp_row['idKlienta'];
    echo "<option value=$id>$name</option>";
  }
}

?>

<div class="container">
<center><h1>Zarejestruj</h1></center>
<div class="panel panel-body panel-default">
<form action="zarejestruj.php" method="POST">
<center><table>
       <tr>
               <td style="width: 60">Login</td>
               <td><input type="text" name="login" maxlength="45" style="width: 150px;" value=<?php echo $login;?>></td>
               <td><?php echo $komunikat_login?></td>
       </tr>
       <tr>
               <td>Hasło</td>
               <td><input type="password" name="haslo" maxlength="45" style="width: 150px;"></td>
       </tr>
       <tr>
               <td>Powtórz hasło</td>
               <td><input type="password" name="powtorz_haslo" maxlength="45" style="width: 150px;"></td>
               <td><?php echo $komunikat_zle_haslo;?></td>
       </tr>
       <tr>
               <td colspan="2"><hr width="270"></td>
       </tr>
       <tr>
               <td>Imię</td>
               <td><input type="text" name="imie" maxlength="45" style="width: 150px;" value=<?php echo $imie;?>></td>
       </tr>
       <tr>
               <td>Nazwisko</td>
               <td><input type="text" name="nazwisko" maxlength="45" style="width: 150px;" value=<?php echo $nazwisko;?>></td>
       </tr>
       <tr>
               <td>Kraj</td>
               <td><input type="text" name="kraj" maxlength="45" style="width: 150px;" value=<?php echo $kraj;?>></td>
       </tr>
       <tr>
               <td>Miasto</td>
               <td><input type="text" name="miasto" maxlength="45" style="width: 150px;" value=<?php echo $miasto;?>></td>
       </tr>
       <tr>
               <td>Ulica i numer</td>
               <td><input type="text" name="ulica" maxlength="100" style="width: 150px;" value=<?php echo $ulica;?>></td>
       </tr>
       <tr>
               <td>Kod pocztowy</td>
               <td><input type="text" name="kod_pocztowy" maxlength="45" style="width: 150px;" value=<?php echo $kod;?>></td>
       </tr>
       <tr>
               <td>Numer telefonu</td>
               <td><input type="text" name="nr_telefonu" maxlength="20" style="width: 150px;" value=<?php echo $nr_tel;?>></td>
       </tr>
       <tr>
               <td>Adres email*</td>
               <td><input type="text" name="mail" maxlength="45" style="width: 150px;" value=<?php echo $mail;?>></td>
       </tr>
       <tr>
               <td>NIP*</td>
               <td><input type="text" name="NIP" maxlength="20" style="width: 150px;" value=<?php echo $nip;?>></td>
       </tr>
       <?php
	   
		//$czyPracownik = false;
	   
		//echo "dfasdfa";
	   
		if($czyPracownik)
		{
		   echo 		   "<tr>
				   <td>Regon*</td>
				   <td><input type='text' name='regon' maxlength='45' style='width: 150px;' value=";
				   echo $regon;
				   echo "></td>
		   </tr>
		   <tr>
				   <td>Firma</td>
				   <td>
					   <select name='firma' style='width: 150px'>";
							   addOptions();
					   echo "</select>
				   </td>
		   </tr>";

		}
	   ?>
       <tr>
               <td style="text-align: center" colspan="2"><?php echo $komunikat;?></td>
       </tr>
       <tr>
               <td>*-nieobowiazkowe</td>
               <td style="text-align: right"><input type="submit" name="zarejestruj" value="zarejestruj" class="btn btn-sm btn-primary"></td>
       </tr>
</table></center>

</form>
</div>
</div>
</body>
</html>