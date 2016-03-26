<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';

if(isset($_POST['zatwierdz']))
{
	if($_POST['nowe'] == $_POST['r_nowe'])
	{
		$query = "SELECT haslo FROM klienci WHERE idKlienta='$idKlienta'";
		if(!$query_run = mysql_query($query))
		{
			echo 'blad zapytania';
		}
		$query_row = mysql_fetch_assoc($query_run);
		if(md5($_POST['aktualne']) == $query_row['haslo'])
		{
			//zmiana hasła
			$nowe = md5($_POST['nowe']);
			$query = "UPDATE klienci SET haslo='$nowe' WHERE idKlienta='$idKlienta'";
			if(!$query_run = mysql_query($query))
			{
				echo 'blad zapytania';
			}
			else
			{
				$_SESSION['haslo'] = $nowe;
				$blad = "<div class=\"alert alert-success\">Hasło zostało zmienione</div>";
			}
		}
		else
		{
			$blad = "<div class=\"alert alert-danger\">Niepoprawne hasło</div>";
		}
	}
	else
	{
		$blad = "<div class=\"alert alert-danger\">Podane hasła nie pasują do siebie</div>";
	}
}
?>
<div class="container">
<h1><center>Zmiana hasła</center></h1>
<div class="panel panel-default panel-body">
<center>
<?php if(isset($blad)) echo $blad; ?>
<form action="zmiana_hasla.php" method="POST">
<table style="width: 400px">
       <tr>
           <td>Aktualne hasło:</td>
           <td style="width: 180"><input type="password" maxLength="45" name="aktualne" style="width: 180" required></td>
       </tr>
       <tr>
           <td>Nowe hasło:</td>
           <td><input type="password" name="nowe" maxLength="45" style="width: 180" required></td>
       </tr>
       <tr>
           <td>Powtórz nowe hasło:</td>
           <td><input type="password" name="r_nowe" maxLength="45" style="width: 180" required></td>
       </tr>
	   <tr><td></td><td>&nbsp;</td></tr>
       <tr style="text-align: left;">
           <td></td>
           <td><input type="submit" name="zatwierdz" value="zatwierdź" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</center>
</div></div>
</body>
</html>