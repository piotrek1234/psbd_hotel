<?php
function addRow($id, $klient, $pokoj){
  echo "<tr>";
  echo "<td><a href=\"rachunek.php?idKlienta=$id\">$klient</a></td>";
  echo "<td>$pokoj</td>";
  echo "</tr>";
}
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');
?>
<div class="container" style="padding-top: 30px;">
<div class="panel panel-default panel-body">
<form action="glowna_barmana.php" method="POST">
<table>
       <tr>
           <td>Numer pokoju:</td>
           <td>od <input type="text" name="od" style="width: 50px" maxlength="5" value="<?php if(isset($_POST['od']))echo $_POST['od'] ?>"></td>
           <td>do <input type="text" name="do" style="width: 50px" maxlength="5" value="<?php if(isset($_POST['do']))echo $_POST['do'] ?>"></td>
           <td><input type="checkbox" name="zameldowani" <?php if(isset($_POST['zameldowani']))echo 'checked' ?>> szukaj tylko w zameldowanych</td>
		   </tr>
		   <tr>
		   <td style="padding-right:20px;">Nazwisko klienta zawiera </td>
		   <td colspan="2"><input type="text" name="nazwisko" value="<?php if(isset($_POST['nazwisko']))echo $_POST['nazwisko'] ?>"></td>
           <td align="center"><input type="submit" name="filtruj" value="filtruj" class="btn btn-sm btn-primary"></td>
		   </tr>
       
</table>
</form>
</div>
<div class="panel panel-default panel-body">
<table class="table table-striped">
		<thead>
       <tr style="text-align:left">
           <th><a href="?sort=nazwisko">Klient</a></th>
           <th><a href="?sort=pokoj">Pokój</a></th>
       </tr>
	   </thead>
<?php
if(isset($_POST['nazwisko']))
{
	$nazwisko = '%'.$_POST['nazwisko'].'%';
	
	if(!czyTylkoLitery($_POST['nazwisko']))
	{
		echo $wiadomosc = "Niepoprawny format: nazwisko zawiera niedozwolone znaki";
		$nazwisko = '%';
	}
}
else $nazwisko = '%';

$pokojOd = 0;
	$pokojDo = 99999;
	if(isset($_POST['od']))
	{
		if($_POST['od'] == '')
		{
			$pokojOd = 0;
		}
		else
		{ 
			$pokojOd = (int)$_POST['od'];
			
			if(!czyCalkowita($pokojOd))
			{
				$pokojOd = 0;
				echo $wiadomosc = "Niepoprawny format: numer od";
			}
		}
	}
	if(isset($_POST['do']))
	{
		if($_POST['do'] == '')
		{ 
			$pokojDo = 9999; 
		}
		else 
		{
			$pokojDo = (int)$_POST['do'];
			
			if(!czyCalkowita($pokojDo))
			{
				$pokojDo = 9999;
				echo $wiadomosc = "Niepoprawny format: numer do";
			}
		}
	}
	
if(!(isset($_POST['zameldowani']) && $_POST['zameldowani'] == true))
{
	$query = "select nazwisko,  concat_ws(' ', imie, nazwisko) as klient, numerPomieszczenia as pokoj, idKlienta from klienci left join rezerwacje using (idKlienta) where kontoAktywne = 1 and stan = 4 and nazwisko like '$nazwisko'";
	//if(isset($_POST['sort'])) $query .= ' order by ' . $_GET['sort'];
	$query .= " union select nazwisko, concat_ws(' ', imie, nazwisko) as klient, '-' as pokoj, idKlienta from rezerwacje right join klienci using (idKlienta) where kontoAktywne = 1 and (stan <> 4 or stan is null) and nazwisko like '$nazwisko' and idKlienta not in (select idKlienta from klienci left join rezerwacje using (idKlienta) where kontoAktywne = 1 and stan = 4 and nazwisko like '$nazwisko')";
	if(isset($_GET['sort'])) $query .= ' order by ' . $_GET['sort'];
}
else
{
	$query = "select nazwisko, concat_ws(' ', imie, nazwisko) as klient, numerPomieszczenia as pokoj, idKlienta from klienci left join rezerwacje using (idKlienta) where kontoAktywne = 1 and stan = 4 and nazwisko like '$nazwisko' and numerPomieszczenia >= $pokojOd and numerPomieszczenia <= $pokojDo";
	if(isset($_GET['sort'])) $query .= ' order by ' . $_GET['sort'];
}



//echo $query;
$wynik = mysql_query($query);
if($wynik)
{
	while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
	{
		addRow($wiersz['idKlienta'], $wiersz['klient'], $wiersz['pokoj']);
	}
}

?>
</table>
</div>
</div>
</body>
</html>