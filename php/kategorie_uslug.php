<?php
require_once "connect_to_database.inc.php";
require_once('pracownik_czy_zalogowany.inc.php');

$zaptyanie_dostepne_kategorie = "SELECT kategoria as idKategorii, nazwa FROM kategorieuslug";

if(!$dostepne_kategorie_run = mysql_query($zaptyanie_dostepne_kategorie))
{
  echo 'blad zapytania kategaria';
  echo mysql_error();
}

function addOptions($use_query)
{
  while($use_query_row = mysql_fetch_assoc($use_query))
  {
    $name = $use_query_row['nazwa'];
	$id = $use_query_row['idKategorii'];
    addRow($name, $id);
  }
}

function addRow($name, $id){
  echo "<tr>";
  echo "<td>$name</td>";;
  echo "<td><a href='kategorie_uslug.php?act=usun&id=$id'>usuń</a></td>";
  echo "</tr>";
}

if(isset($_GET["id"]) && isset($_GET['act']) && ($_GET['act'] == 'usun'))
{
	$delete_id = $_GET["id"];
	$zapytanie_usuniecie_kategorii = "delete from kategorieuslug where kategoria = $delete_id";

		if(!$usunieta_kategoria_run = mysql_query($zapytanie_usuniecie_kategorii))
		{
			echo 'blad zapytania delete';
			echo mysql_error();
		}
		header("Location: kategorie_uslug.php");
}

if(isset($_POST["dodaj"]))
{
	if(isset($_POST["dodaj_kategorie"]))
	{
		$nowa_kategoria = $_POST["dodaj_kategorie"];
		if($nowa_kategoria != "")
		{
			$zaptyanie_dodanie_kategorii = "insert into kategorieuslug (kategoria, nazwa) values (NULL, '$nowa_kategoria')";

			if(!$dodana_kategoria_run = mysql_query($zaptyanie_dodanie_kategorii))
			{
				echo 'blad zapytania insert';
				echo mysql_error();
			}
			header("Location: kategorie_uslug.php");
			//$dodana_kategoria = mysql_fetch_assoc($dodana_kategoria_run);
		}
		
	}
}

?>
<div class="container">
<h1><center>Kategorie usług</center></h1>
<div class="panel panel-default">
<div class="panel-body">
<table style="width: 35%; text-align: left" class="table table-striped">
       <thead>
	   <tr>
           <th>Nazwa kategorii</th>
           <th></th>
       </tr>
	   </thead>
	   <?php addOptions($dostepne_kategorie_run);?>
</table>
</div></div>
<div class="panel panel-default">
<div class="panel-body">
<form action="kategorie_uslug.php" method="POST">
<table style="width: 50%">
       <tr>
           <th>Dodaj kategorię: </th>
           <td><input type="text" name="dodaj_kategorie" style="width: 160" maxlength="45" required>
		   <input type="submit" name="dodaj" value="dodaj" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div></div>
</div>
</body>
</html>