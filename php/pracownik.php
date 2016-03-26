<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once 'funkcje.php';

$wiadomosc = "";

if(isset($_REQUEST['idPracownika'])) $idPracownika1 = $_REQUEST['idPracownika']; else $idPracownika1 = '';

if(isset($_POST['act']))
{
	if(sprMail($_POST['mail']) && czyTylkoLitery($_POST['nazwisko']) && czyTylkoLitery($_POST['imie']))
	{
		if($_POST['act'] == 'dodaj')
		{
			if($_POST['haslo'] == $_POST['powtorz_haslo'])
			{
				$haslo = md5($_POST['haslo']);
				$query = "INSERT INTO pracownicy (idPracownika, login, haslo, imie, nazwisko, telefon, email, stanowisko) VALUES (NULL, '{$_POST['login']}', '$haslo', '{$_POST['imie']}', '{$_POST['nazwisko']}', '{$_POST['nr_telefonu']}', '{$_POST['mail']}', '{$_POST['stanowisko']}')";
				$wynik = mysql_query($query);
				header('Location: lista_pracownikow.php');
			}
			else
			{
				$blad = '<font color="red">Hasła się nie zgadzają</font>';
			}
		}
		else if($_POST['act'] == 'edytuj')
		{
			if($_POST['haslo'] == $_POST['powtorz_haslo'])
			{
				$haslo = md5($_POST['haslo']);
				$query = "UPDATE pracownicy SET login='{$_POST['login']}', haslo='$haslo', imie='{$_POST['imie']}', nazwisko='{$_POST['nazwisko']}', telefon='{$_POST['nr_telefonu']}', email='{$_POST['mail']}', stanowisko='{$_POST['stanowisko']}' WHERE idPracownika={$idPracownika1}";
				$wynik = mysql_query($query);
				header('Location: lista_pracownikow.php');
			}
			else
			{
				$blad = '<font color="red">Hasła się nie zgadzają</font>';
			}
		}
		else if($_POST['act'] == 'usun')
		{
			if(isset($_POST['usun']))
			{
				$query = 'delete from pracownicy where idPracownika = \'' . $idPracownika1 . '\'';
				$wynik = mysql_query($query);
				$idPracownika1 = '';
				header('Location: lista_pracownikow.php');
			}
		}
	}
	else
	{
		 $blad = "zły format: imie i/lub nazwisko i/lub adres email";
	}
}

$query = "select login, imie, nazwisko, stanowisko, email, telefon from pracownicy where idPracownika = $idPracownika1";
$wynik = mysql_query($query);
if($wynik)
{
	while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
	{
		$login1 = $wiersz['login'];
		$imie = $wiersz['imie'];
		$nazwisko = $wiersz['nazwisko'];
		$nr_telefonu = $wiersz['telefon'];
		$email = $wiersz['email'];
		$stanowisko = $wiersz['stanowisko'];
	}
}

?>

<div class="container">
<div class="row col-md-4 col-md-offset-4">
<center><h1>Pracownik</h1></center>
<div class="panel panel-default panel-body">
<form action="pracownik.php" method="POST">
      <table>
	  <tr>
	  <td>Login</td><td><input type="text" name="login" maxlength="45" required value="<?php if(isset($login1))echo $login1; ?>"></td>
	  </tr>
      <tr>
	  <td>Hasło</td><td><input type="password" name="haslo" maxlength="45" required></td>
	  </tr>
      <tr>
	  <td>Powtórz hasło</td><td><input type="password" name="powtorz_haslo" maxlength="45" required></td>
	  </tr>
      <tr><td colspan="2"><hr></td></tr>
      <tr>
	  <td>Imię</td><td><input type="text" name="imie" maxlength="45" required value="<?php if(isset($imie))echo $imie; ?>"></td>
	  </tr>
      <tr>
	  <td>Nazwisko</td><td><input type="text" name="nazwisko" maxlength="45" required value="<?php if(isset($nazwisko))echo $nazwisko; ?>"></td>
	  </tr>
      <tr>
	  <td>Numer telefonu</td><td><input type="text" name="nr_telefonu" maxlength="20" required  value="<?php if(isset($nr_telefonu))echo $nr_telefonu; ?>"></td>
	  </tr>
      <tr>
	  <td>Adres email</td><td><input type="text" name="mail" maxlength="45" value="<?php if(isset($email))echo $email; ?> required"></td>
	  </tr>
      <tr>
	  <td>Stanowisko</td><td><select name="stanowisko" style="width:100%;">
      <?php
		$query = 'select idStanowiska, nazwa from stanowiska';
				$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				echo '<option value="'.$wiersz['idStanowiska'].'"';
				if(isset($stanowisko) && $stanowisko == $wiersz['idStanowiska']) echo ' selected="selected"';
				echo '>'.$wiersz['nazwa'].'</option>'."\n";
				}
	  ?>
      </select>
	  </td>
	  </tr>
	  <tr>
      <td colspan="2" style="padding-top:10px;">
	  <center><input type="submit" name="zapisz" value="zapisz pracownika" class="btn btn-sm btn-primary"></p>
	  <?php
		if($idPracownika1 != '') echo '<input type="hidden" name="act" value="edytuj">';
		else echo '<input type="hidden" name="act" value="dodaj">';
		echo '<input type="hidden" name="idPracownika" value="'.$idPracownika1.'">';
	  ?>
      </center></td></tr>
	  </form>
	  </table>
	  <?php if(isset($blad)) echo $blad; ?>
	  </div>
	  <?php
	  if($idPracownika1 != '')
	  {
	  echo '<div class="panel panel-warning"><div class="panel-heading">Usuwanie pracownika</div>';
	  echo '<div class="panel-body"><form action="pracownik.php" method="POST">
	  <center>
      <input type="checkbox" name="usun"> na pewno</input>';
	  echo '<input type="hidden" name="idPracownika" value="'.$idPracownika1.'">';
	  echo '<input type="hidden" name="act" value="usun">
      <input type="submit" name="usun_pracownika" value="usuń pracownika" class="btn btn-sm btn-warning">
      </center>
	  </form></div></div>';
	  }
	  ?>
	  </div></div>
</body>
</html>