<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');

$wiadomosc = "";

if(isset($_REQUEST['id']))
{
	$idSezonu = $_REQUEST['id'];
	}
if(isset($_POST['act']))
{
	$wszystko_ok = true;

	if(!sprCzyData($_POST['od']))
	{
		$wszystko_ok = false;
		echo $wiadomosc = "zły format: data od";
	}
	
	if(!sprCzyData($_POST['do']))
	{
		$wszystko_ok = false;
		echo $wiadomosc = "zły format: data do";
	}

	if($wszystko_ok)
	{	
		if(!czyRoznicaDatOk($_POST['od'], $_POST['do']))
		{
			$wszystko_ok = false;
			echo $wiadomosc = "data od większa od daty do";
		}
	}
	
	if($wszystko_ok)
	{
		if($_POST['act'] == 'dodaj')
		{
			$nazwa = $_POST['nazwa'];
			$odDaty = $_POST['od'];
			$doDaty = $_POST['do'];
			$query = "INSERT INTO  sezony (idSezonu, nazwaSezonu, odDaty, doDaty) VALUES (NULL, '$nazwa', '$odDaty', '$doDaty')";
			$wynik = mysql_query($query);
			header('Location: lista_sezonow.php');
		}
		else if($_POST['act'] == 'zmien')
		{
			$nazwa = $_POST['nazwa'];
			$odDaty = $_POST['od'];
			$doDaty = $_POST['do'];
			$query = "update sezony set nazwaSezonu = '$nazwa', odDaty = '$odDaty', doDaty = '$doDaty' where idSezonu = $idSezonu";
			$wynik = mysql_query($query);
			header('Location: lista_sezonow.php');
		}
		else if($_POST['act'] == 'usun')
		{
			if(isset($_POST['na_pewno']))
			{
				$query = "delete from sezony where idSezonu = $idSezonu";
				$wynik = mysql_query($query);
				header('Location: lista_sezonow.php');
			}
		}
	}
}

if(isset($_REQUEST['id']))
{
	$query = "select nazwaSezonu, odDaty, doDaty from sezony where idSezonu = $idSezonu";
	//echo $query;
	$wynik = mysql_query($query);
if($wynik)
{
	while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC))
	{
		$nazwa = $wiersz['nazwaSezonu'];
		$od = $wiersz['odDaty'];
		$do = $wiersz['doDaty'];
	}
}
}
?>

<div class="container">
<h1><center>Sezon</center></h1>
<div class="row"><div class="col-md-4 col-md-offset-4">
<div class="panel panel-default">
<div class="panel-body">
<form action="sezon.php" method="POST">
<center><table>
       <tr>
           <th style="width: 100">Nazwa</td>
           <td><input type="text" name="nazwa" maxlength="45" style="width: 180px;" value="<?php if(isset($_REQUEST['id']))echo $nazwa ?>" required></td>
       </tr>
       <tr>
           <th>Od</td>
           <td><input type="text" name="od" maxlength="10" style="width: 180px;" value="<?php if(isset($_REQUEST['id']))echo $od ?>" required></td>
       </tr>
       <tr>
           <th>Do</td>
           <td><input type="text" name="do" maxlength="10" style="width: 180px;" value="<?php if(isset($_REQUEST['id']))echo $do ?>" required></td>
       </tr>
</table></center>


<center><table >
       <tr>
	   <?php
	   if(isset($idSezonu))
	   {
		echo '<input type="hidden" name="id" value="'.$idSezonu.'">';
	   }
           
        ?>   
           <td><input type="submit" name="zapisz" value="zapisz" class="btn btn-primary"></td>
		    <input type="hidden" name="act" value="<?php if(isset($idSezonu)) echo 'zmien'; else echo 'dodaj'; ?>">
       </tr>
</table></center></form>
</div></div>

<?php
if(isset($idSezonu))
	   {
	   echo '<div class="panel panel-warning">
<div class="panel-heading">
Usuwanie sezonu
</div>
<div class="panel-body">
<center><table><tr>';
	   echo '<form action="sezon.php?id='.$idSezonu.'" method="POST">';
		echo '<th><input type="checkbox" name="na_pewno" value="na_pewno" required> na pewno&nbsp;</th>';
		echo '<td><input type="submit" name="usun_sezon" value="usuń sezon" class="btn btn-sm btn-warning"></td>';
		echo '<input type="hidden" name="act" value="usun">';
		echo '</form></tr></table></center>
</div>
</div>';
	   }
?>
</div></div>
</body>
</html>