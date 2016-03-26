<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');

function addNewRow($id, $Client, $Firm, $Account)
{
  echo "<tr>";
  echo "<td><a href=\"klient.php?idKlienta=$id\">$Client</a></td>";
  echo "<td>$Firm</td>";
  echo "<td>";
  if($Account) echo 'tak'; else echo 'nie';
  echo "</td>";
  echo "</tr>";
}

function addNewRow2($id, $Firm, $Account)
{
  echo "<tr>";
  echo "<td><a href=klient.php?idKlienta=$id>$Firm</a></td>";
  echo "<td>";
  if($Account) echo 'tak'; else echo 'nie';
  echo "</td>";
  echo "</tr>";
}

if(isset($_GET['sort']))
{
  $sort = $_GET['sort'];
}
else
{
  $sort = 'Numer';
}

?>

<div class="container">
<h1><center>Lista klientów</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<form action="lista_klientow.php" method="POST" role="form">

<table style="width: 50%; text-align: left" cellspacing="10">
       <tr>
           <th>Typ klienta</th>
           <td><input type="radio" name="typ_klienta" value="indywidualny" <?php if(isset($_POST['typ_klienta']) && $_POST['typ_klienta']=='indywidualny') echo 'checked'; ?>>indywidualny</td>
           <td><input type="radio" name="typ_klienta" value="firma" <?php if(isset($_POST['typ_klienta']) && $_POST['typ_klienta']=='firma') echo 'checked'; ?>>firma</td>
       </tr>
       <tr>
           <th>Konto aktywne</th>
           <td colspan="2">
           <select name="konto_aktywne"  style="width: 160">
                   <option value="tak" <?php if(isset($_POST['konto_aktywne']) && $_POST['konto_aktywne']=='tak') echo 'selected'; ?>>tak</option>
                   <option value="nie" <?php if(isset($_POST['konto_aktywne']) && $_POST['konto_aktywne']=='nie') echo 'selected'; ?>>nie</option>
                   <option value="obojetnie" <?php if(isset($_POST['konto_aktywne']) && $_POST['konto_aktywne']=='obojętnie') echo 'selected'; ?>>obojętnie</option>
           </select>
           </td>
       </tr>
       <tr>
           <th>Nazwisko (nazwa firmy) zawiera</th>
           <td  colspan="2"><input type="text" name="zawiera" style="width: 160" maxlength="45" value="<?php if(isset($_POST['zawiera'])) echo $_POST['zawiera']; ?>"></td>
       </tr>
       <tr>
           <th></th>
           <td><input type="submit" name="filtruj" value="filtruj" style="width: 100" class="btn btn-primary"></td>
		   <?php if(isset($_GET['sort']))
				echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'">'; ?>
           <td></td>
       </tr>
</table>
</form>
</div></div>

<table class="table table-striped">
       <thead><tr>
           <?php if(!(isset($_POST['typ_klienta']) && $_POST['typ_klienta'] == 'firma')) echo '<th><a href="?sort=k.nazwisko">Imię i nazwisko</a></th>'; ?>
           <th><a href="?sort=firma">Firma</a></th>
           <th>konto aktywne</th>
       </tr></thead>
<tbody>
	   <?php
	   if(isset($_POST['typ_klienta']) && $_POST['typ_klienta'] == 'firma')
	   {
			$query = 'select idklienta, nazwaFirmy, kontoAktywne from klienci where czyfirma = 1 and nazwaFirmy like \'%';
			if(isset($_POST['zawiera'])) $query .= $_POST['zawiera'];
			$query .='%\'';
			
			if(isset($_POST['konto_aktywne']))
			{
				if($_POST['konto_aktywne'] == 'tak')
					$query .= ' and kontoAktywne = 1';
				else if($_POST['konto_aktywne'] == 'nie')
					$query .= ' and kontoAktywne = 0';
			}
			$query .= ' order by nazwaFirmy';
			//echo $query;
			$wynik = mysql_query($query);
			//echo $query;
		if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addNewRow2($wiersz['idklienta'], $wiersz['nazwaFirmy'], $wiersz['kontoAktywne']);
				}
	   }
	   else	//wyświetlanie klientów
	   {
		$query = 'select k.idklienta, concat_ws(\' \', k.imie, k.nazwisko) as Klient, ifnull(f.nazwafirmy, \'-\') as firma, k.kontoAktywne from klienci as k left join klienci as f on k.idfirmy = f.idklienta where k.czyfirma = 0 and k.nazwisko like \'%';
		if(isset($_POST['zawiera'])) $query .= $_POST['zawiera'];
		$query .='%\'';
		
		if(isset($_POST['konto_aktywne']))
		{
				if($_POST['konto_aktywne'] == 'tak')
					$query .= ' and k.kontoAktywne = 1';
				else if($_POST['konto_aktywne'] == 'nie')
					$query .= ' and k.kontoAktywne = 0';
		}
		if(isset($_REQUEST['sort'])) $query .= ' order by ' . $_REQUEST['sort'];
		//echo $query;
		$wynik = mysql_query($query);
	   if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addNewRow($wiersz['idklienta'], $wiersz['Klient'], $wiersz['firma'], $wiersz['kontoAktywne']);
			}
	   }
	   ?>
</tbody>
</table>
</div>
</body>
</html>