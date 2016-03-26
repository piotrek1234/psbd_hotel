<?php
function addService($id, $service, $amount, $cost, $rachunek){
  echo "<tr>";
  echo "<td>$service</td>";
  echo "<td>$amount</td>";
  echo "<td>$cost</td>";
  echo "<td><a href=\"?act=usun&id=$id&rachunek=$rachunek&idKlienta={$_REQUEST['idKlienta']}\">usuń</a></td>";
  echo "</tr>";
}
?>

<?php
function addRow($id, $service, $cost){
  echo "<tr>";
  echo "<td><input type='radio' name='service' value=$id required>$service</td>";
  echo "<td>$cost</td>";
  echo "</tr>";
}

require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');

$wiadomosc = "";

if(isset($_GET['act']) && $_GET['act'] == 'usun')
{
	$query = "delete from pozycjerachunkow where idRachunku = {$_REQUEST['rachunek']} and idUslugi = {$_REQUEST['id']}";
	$wynik = mysql_query($query);
}
else if(isset($_POST['act']) && $_POST['act'] == 'dodaj')
{
	if(czyCalkowita($_POST['ilosc']))
	{
		$query = "INSERT INTO pozycjerachunkow (idRachunku, idUslugi, ilosc) VALUES ('{$_POST['idRachunku']}', '{$_POST['service']}', '{$_POST['ilosc']}')";
		//echo $query;
		$wynik = mysql_query($query);
	}
	else
	{
		echo $wiadomosc = "zły format: cena";
	}
}
?>

<div class="container">
<h1><center>Rachunek</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<?php
$query = "select concat_ws(' ', imie, nazwisko) as klient, idrachunku from rachunki join klienci using (idklienta) where idklienta = {$_REQUEST['idKlienta']} and czyzaplacony = 0";
$wynik = mysql_query($query);
$num = 0;
if($wynik)
{
	while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
	{
		$klient = $wiersz['klient'];
		$idRachunku = $wiersz['idrachunku'];
		$num++;
	}
}
if($num == 0)	//brak rachunku
{
	$query = "INSERT INTO rachunki (idRachunku, idKlienta, kosztPomieszczenia, dataWystawienia, znizka, czyZaplacony, czyFaktura) VALUES (NULL, {$_REQUEST['idKlienta']}, '0.00', curdate(), '0', '0', '0')";
	$wynik = mysql_query($query);
	$query = "select concat_ws(' ', imie, nazwisko) as klient, idrachunku from rachunki join klienci using (idklienta) where idklienta = {$_REQUEST['idKlienta']} and czyzaplacony = 0";
	$wynik = mysql_query($query);
	if($wynik)
	{
		while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
		{
			$klient = $wiersz['klient'];
			$idRachunku = $wiersz['idrachunku'];
		}
	}
}
echo '<h4><span class="label label-success">'.$klient.'</span></h4>';
?>

<table style="width: 50%;">
       <tr style="text-align: left">
           <th>Usługa</th>
           <th>ilość</th>
           <th>koszt</th>
           <th></th>
       </tr>
       <tr>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <?php
	   $query = 'select nazwa, ilosc, (ilosc*cena) as koszt, idUslugi from pozycjerachunkow join uslugi using (idUslugi) where idRachunku = '.$idRachunku;
	   $wynik = mysql_query($query);
	if($wynik)
	{
		while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
		{
			addService($wiersz['idUslugi'], $wiersz['nazwa'], $wiersz['ilosc'], $wiersz['koszt'], $idRachunku);
		}
	}
	   ?>
       <tr>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
       </tr>
       <tr style="text-align: left">
           <th>Razem:</th>
           <td></td>
           <th>
<?php
$query = 'select ifnull(sum(cena*ilosc), 0) as razem from pozycjerachunkow join rachunki using (idrachunku) join uslugi using (iduslugi) where idrachunku = '.$idRachunku;
//echo $query;
$wynik = mysql_query($query);
	if($wynik)
	{
		while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
		{
			echo $wiersz['razem'];
		}
}
?>
			</th>
           <td></td>
       </tr>
</table>
</div></div>
<div class="panel panel-default">
<div class="panel-body">
<form action="rachunek.php" method="POST">
<input type="hidden" name="idKlienta" value="<?php echo $_REQUEST['idKlienta']; ?>">
<input type="hidden" name="idRachunku" value="<?php echo $idRachunku; ?>">
<table style="width: 50%">
       <tr>
           <th colspan="3">Dopisz do rachunku:</th>
       </tr>
       <tr>
           <td>Kategoria:</td>
           <td><select name="kategoria">
           <?php
				$query = 'select kategoria as idKategorii, nazwa from kategorieuslug';
				$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				echo '<option value="'.$wiersz['idKategorii'].'"';
				if(isset($_POST['kategoria']) && $_POST['kategoria'] == $wiersz['idKategorii']) echo ' selected="selected"';
				echo '>'.$wiersz['nazwa'].'</option>'."\n";
				}
		   ?>
           </select></td>
           <td><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
<br>
<form action="rachunek.php" method="POST">
<input type="hidden" name="idKlienta" value="<?php echo $_REQUEST['idKlienta']; ?>">
<input type="hidden" name="idRachunku" value="<?php echo $idRachunku; ?>">
<input type="hidden" name="act" value="dodaj">
<table style="width: 35%">
<?php
if(isset($_POST['kategoria'])) $kategoria = $_POST['kategoria']; else $kategoria = '1';
$query = 'select idUslugi, nazwa, cena from uslugi where kategoria = '.$kategoria;
			//echo $query;
			$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addRow($wiersz['idUslugi'], $wiersz['nazwa'], $wiersz['cena']);
				}
?>
</table>
<br>
<table style="width: 50%">
       <tr>
           <td>Ilość:</td>
           <td><input type="text" name="ilosc" style="width: 80" maxlength="4" required></td>
           <td><input type="submit" name="dopisz_do_rachunku" value="dopisz do rachunku" class="btn btn-sm btn-primary"></td>
       </tr>
</table>

</form>
</div>
</div>
</body>
</html>