<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';

if(isset($_POST['usun']))
{
	if($_POST['konto'] == 'konto')
	{
		$query = "SELECT haslo FROM klienci WHERE idKlienta='$idKlienta'";
		if(!$query_run = mysql_query($query))
		{
			echo 'blad zapytania';
		}
		$query_row = mysql_fetch_assoc($query_run);
		if(md5($_POST['haslo']) == $query_row['haslo'])
		{
			
			//usuwanie konta
			//pozycja nie usunie się jeśli jest coś w bazie z tym idKlienta!!!
			$query = "DELETE from klienci where idKlienta='$idKlienta'";
			if(!$query_run = mysql_query($query))
			{
				echo 'blad zapytania';
			}
			else
			{
				$_SESSION = array();
				session_unset();
				echo '<meta http-equiv="refresh" content="3; url=index.php">';
				$blad = "<div class=\"alert alert-success\">Konto zostało usunięte</div>";
			}
		}
		else
		{
			//złe hasło
			$blad = "<div class=\"alert alert-danger\">Niepoprawne hasło</div>";
		}
	}
}

?>
<div class="container">
<h1><center>Usuwanie konta</center></h1>
<div class="panel panel-default panel-body">
<form action="usuwanie_konta.php" method="POST">
<center>
<table style="width: 300">
       <tr>
           <td colspan="2"><input type="checkbox" name="konto" value="konto" required> Chcę usunać swoje konto</td>
       </tr>
       <tr>
           <td style="width: 150px;">Aktualne hasło</td>
           <td style="width: 180"><input type="password" name="haslo" maxLength = "45" style="width: 180" required></td>
       </tr>
	   <tr><td colspan="2">&nbsp;</tr>
       <tr style="text-align: right">
           <td colspan="2"><center><input type="submit" name="usun" value="usuń konto" class="btn btn-sm btn-danger"></center></td>
       </tr>
</table>
</center>
</form>
</div>
<div class="alert alert-warning">Uwaga! Jeśli dokonałeś jakiejkolwiek rezerwacji, konto może być usunięte tylko przez pracownika hotelu.</div>
<?php if(isset($blad)) echo $blad; ?>
</div>
</body>
</html>