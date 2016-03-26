<?php
function addRow($id, $from, $to, $sezon){
  echo "<tr>";
  echo "<td>$from</td>";
  echo "<td>$to</td>";
  echo "<td><a href=\"sezon.php?id=$id\">$sezon</a></td>";
  echo "</tr>";
}
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
require_once('funkcje.php');

$wiadomosc = "";

?>

<div class="container">
<h1><center>Lista sezonów</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<form action="lista_sezonow.php" method="POST" role="form">

<table style="width: 50%;">
       <tr>
           <th>Zakres dat:</td>
           <td>od</td>
           <td><input type="text" name="od" style="width: 100px" value="<?php if(isset($_POST['od']))echo $_POST['od'] ?>"></td>
           <td>do</th>
           <td><input type="text" name="do" style="width: 100px" value="<?php if(isset($_POST['do']))echo $_POST['do'] ?>"></td></tr>
		   <tr><th>Nazwa sezonu zawiera</td><td><input type="text" name="nazwa" style="width: 150px" value="<?php if(isset($_POST['nazwa']))echo $_POST['nazwa']; ?>"></th>
           <td><input type="submit" name="filtruj" value="filtruj" class="btn btn-primary"></td>
       </tr>
</table>
</form>
</div></div>
<table class="table table-striped">
	<thead>
       <tr>
           <th><a href="?sort=odDaty">Od</a></th>
           <th><a href="?sort=doDaty">Do</a></th>
           <th><a href="?sort=nazwaSezonu">Nazwa sezonu</a></th>
       </tr>
	   </thead>
	   <?php

			$odPoprawnie = false;
			$doPoprawnie = false;
			$odDaty = "0000-01-01";
			$doDaty = "9999-12-31";
			
			if(isset($_POST['nazwa']))
			{
				if(czyTylkoLitery($_POST['nazwa']))
				{
					$nazwa = $_POST['nazwa']; 
				}
				else
				{
					$nazwa = '%';
					echo $wiadomosc = "zły format: nazwa sezonu zawiera";
				}
			}	
			else 
			{
				$nazwa = '';
			}
			
			/*//$odPoprawnie = true;
			//$doPoprawnie = true;
			
			if(isset($_POST['do']) && $_POST['do'] != "")
			{
				if(sprCzyData($_POST['do']))
				{
					$doPoprawnie = true;
				}
				else
				{
					echo $wiadomosc = "zły format data do";
					$doPoprawnie = false;
				}
			}
			
			if(isset($_POST['od']) && $_POST['od'] != "")
			{
				if(sprCzyData($_POST['od']))
				{
					$odPoprawnie = true;
				}
				else
				{
					echo $wiadomosc = "zły format data od";
					$odPoprawnie = false;
				}
			}
			
			if($doPoprawnie)
				$doDaty = $_POST['do'];
			if($odPoprawnie)
				$odDaty = $_POST['od'];
			
			if($doPoprawnie && $odPoprawnie)
			{
				//if(czyRoznicaDatOk($_POST['od'], $_POST['do']))
				if(czyRoznicaDatOk($odDaty, $doDaty))
				{
					//$odDaty = $_POST['od'];
					//$doDaty = $_POST['do'];
				}
				else
				{
					$odDaty = "0000-01-01";
					$doDaty = "9999-12-31";
					echo $wiadomosc = "data od większa od daty do";
				}
			}*/
			
			if(isset($_POST['od']) && ($_POST['od'] != ''))
			{
				if(sprCzyData($_POST['od']))
					$odDaty = $_POST['od'];
			}
			if(isset($_POST['do']) && ($_POST['do'] != ''))
			{
				if(sprCzyData($_POST['do']))
					$doDaty = $_POST['do'];
			}
			if(!czyRoznicaDatOk($odDaty, $doDaty))
			{
				echo $blad = 'Zły przedział dat!';
				$odDaty = "0000-01-01";
				$doDaty = "9999-12-31";
			}
			
			$query = 'select idSezonu, nazwaSezonu, odDaty, doDaty from sezony where nazwaSezonu like \'%'.$nazwa.'%\'';
			$query .= " and (
                (('$doDaty' between odDaty AND doDaty)AND('$doDaty'<>odDaty))
                OR
                (('$odDaty' between odDaty AND doDaty)AND('$odDaty'<>doDaty))
                OR
                ((odDaty>='$odDaty')AND(doDaty<='$doDaty'))
            )";
			if(isset($_GET['sort'])) $query .= ' order by ' . $_GET['sort'];
			//echo $query;
			$wynik = mysql_query($query);
			
			echo mysql_error();
			
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addRow($wiersz['idSezonu'], $wiersz['odDaty'], $wiersz['doDaty'], $wiersz['nazwaSezonu']);
				}
	   ?>
</table>

</div>
</body>
</html>