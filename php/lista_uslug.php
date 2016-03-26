<?php
function addRow($id, $service, $price, $kat){
  echo "<tr>";
  echo "<td>$service</td>";
  echo "<td>$price</td>";
  echo "<td><a href=\"?id=$id&act=usun&kategoria=$kat\" class=\"btn btn-sm btn-default\">usuń</a></td>";
  echo "</tr>";
}
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');

$wiadomosc = "";

if(isset($_GET['act']) && $_GET['act'] == 'usun')
{
	$query = 'delete from uslugi where idUslugi = ' . $_GET['id'];
	mysql_query($query);
}
else if(isset($_POST['act']) && $_POST['act'] == 'dodaj')
{
	$cena = str_replace(',', '.', $_POST['cena']);
	if(sprCena($cena))
	{
		$query = "INSERT INTO uslugi (idUslugi, kategoria, nazwa, cena) VALUES (NULL, '{$_POST['kategoria']}', '{$_POST['nazwa']}', '{$cena}')";
		//echo $query;
		mysql_query($query);
	}
	else
	{
		echo $widomosc = "zły format wprowadzonych danych";
	}
}

?>

<div class="container">
<h1><center>Lista usług</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<form action="lista_uslug.php" method="POST">
<table style="width: 50%">
       <tr>
           <th style="text-align: left">Kategoria:</th>
           <td><select name="kategoria" style="width: 180">
		   <?php
				$query = 'select kategoria as idKategorii, nazwa from kategorieuslug';
				$wynik = mysql_query($query);
			if($wynik)
			while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				echo '<option value="'.$wiersz['idKategorii'].'"';
				if(isset($_REQUEST['kategoria']) && $_REQUEST['kategoria'] == $wiersz['idKategorii']) echo ' selected="selected"';
				echo '>'.$wiersz['nazwa'].'</option>'."\n";
				}
		   ?>
           </select></td>
		   <td><input type="submit" name="filtruj" value="filtruj" class="btn btn-primary"></td>
       </tr>
</table>
</form>
</div></div>
<table class="table table-striped">
	<thead>
       <tr>
           <th>Nazwa</th>
           <th>Cena</th>
           <th></th>
       </tr>
	   </thead>
	   <?php
			if(isset($_REQUEST['kategoria'])) 
			{
				$kategoria = $_REQUEST['kategoria']; 
			}
			else
			{
				$kategoria = '1';
			}
			$query = 'select idUslugi, nazwa, cena from uslugi where kategoria = '.$kategoria;
			//echo $query;
			$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addRow($wiersz['idUslugi'], $wiersz['nazwa'], $wiersz['cena'], $kategoria);
				}
	   ?>
</table>

<div class="well well-sm">
<form action="lista_uslug.php" method="POST">
<table style="width: 50%">
	<thead>
       <tr style="text-align: left">
           <th colspan="2">Dodaj nowa usługę:</th>
       </tr>
	</thead>
       <tr>
           <td style="width: 30">Nazwa</td>
           <td><input type="text" name="nazwa" maxlength="45" style="width: 180px;" required></td>
       </tr>
       <tr>
           <td>Cena</td>
           <td><input type="text" name="cena" maxlength="13" style="width: 180px;" required></td>
       </tr>
       <tr style="text-align: center">
           <td colspan="2"><input type="submit" name="dodaj" value="dodaj" class="btn btn-primary"></td>
       </tr>
	   <input type="hidden" name="act" value="dodaj">
	   <input type="hidden" name="kategoria" value="<?php echo $kategoria; ?>">
</table>
</form>
</div>
</div>

</body>
</html>