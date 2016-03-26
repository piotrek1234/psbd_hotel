<?php
  require_once('connect_to_database.inc.php');
  require_once('pracownik_czy_zalogowany.inc.php');
  require_once('funkcje.php');
  
function addRow($id, $persons, $category, $facility){
  echo "<tr>";
  echo "<td>$persons</td>";
  echo "<td>$category</td>";
  echo "<td>$facility</td>";
  echo "<td><a href=\"nowy_rodzaj_pokoju.php?id=$id&act=edycja\" class=\"btn btn-sm btn-default\">edycja</a></td>";
  echo "</tr>";
}

$wiadomosc =  "";
$l_od = false;
$l_do = false;

?>

<div class="container">
<center><h1>Lista rodzajów pokoju</h1></center>
<div class="panel panel-default">
<div class="panel-body">
<form action="lista_rodzajow_pokoju.php" method="POST" role="form">
<table style="width: 50%">
       <tr>
           <th style="width: 30%">Liczba osób:</td>
           <td>od<input type="text" name="od" style="width: 56px; margin-left: 10px; margin-right: 10px" maxlength="11" value="<?php if(isset($_POST['od']))echo $_POST['od'] ?>">
               do<input type="text" name="do" style="width: 57px; margin-left: 10px; margin-right: 10px" maxlength="11" value="<?php if(isset($_POST['do']))echo $_POST['do'] ?>"></td>
       </tr>
       <tr>
           <th>Kategoria:</td>
           <td><select name="Kategoria" style="width: 180px">
           
		   <?php
		   $query = 'select idKategorii as id, nazwa from kategorie';
		   
		   $wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				echo '<option value="'.$wiersz['id'].'">'.$wiersz['nazwa'].'</option>'."\n";
				}
		   ?>
           </select></td>
       </tr>
       <tr>
           <td colspan="2" style="text-align: center"><input type="submit" name="filtruj" value="filtruj" class="btn btn-primary"></td>
       </tr>
</table>
</form>
</div></div>
<table class="table table-striped">
		<thead>
       <tr>
           <th><a href="?sort=pojemnosc">Liczba osób</a></th>
           <th><a href="?sort=kategoria">Kategoria</a></th>
           <th>Wyposażenie</th>
           <th></th>
       </tr>
	   </thead>
	   <tbody>
	   <?php
			if(isset($_POST['od'])) 
			{ 
				$minOsob = $_POST['od']; 
				if($minOsob=='') 
					$minOsob = 0;
					
				if(!czyCalkowita($minOsob))
				{
					$minOsob = 0;
					echo $wiadomosc = "zły format: liczba osób od";
				}
				
				$l_od = true;
			}
			else 
			{
				$minOsob = 0;
			}
			if(isset($_POST['do'])) 
			{
				$maxOsob = $_POST['do']; 
				if($maxOsob=='') 
					$maxOsob=999999;
				
				if(!czyCalkowita($maxOsob))
				{
					$maxOsob="";
					echo $wiadomosc = "zły format: liczba osób do";
				}	
					
				$l_do = true;
			
			}
			else 
			{
				$maxOsob = 999999;
			}
			
			if(isset($_POST['od'])&&isset($_POST['do'])&&$l_do&&$l_od)
			{
				if(!czyOdDo($minOsob, $maxOsob))
				{
					$minOsob = 0;
					$maxOsob = 999999;
					$wiadomosc = "liczba osób od wieksza od liczby osob do";
				}
			}
			
			$query = "select idPokoju, nazwa as kategoria, pojemnosc from pokoje join kategorie on pokoje.kategoria = kategorie.idKategorii where pojemnosc >= $minOsob and  pojemnosc <= $maxOsob";
			if(isset($_POST['Kategoria']))
			{
				if($_POST['Kategoria'] != '4')
					$query .= ' and kategorie.idKategorii = ' . $_POST['Kategoria'];
			}
			if(isset($_GET['sort'])) $query .= ' order by ' . $_GET['sort'];
			//echo $query;
			
			$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				$query = 'select nazwa from pokoje join pokoje_wyposazenie using (idPokoju) join wyposazeniePokoju using (idWyposazenia) where idPokoju = ' . $wiersz['idPokoju'];
				$wynik2 = mysql_query($query);
				$wyposazenie = '';
				if($wynik2)while ($wiersz2 = mysql_fetch_array($wynik2, MYSQL_ASSOC)) {
					$wyposazenie .= $wiersz2['nazwa'] . ', ';
					}
					$wyposazenie = substr_replace($wyposazenie ,"",-2);
				addRow($wiersz['idPokoju'], $wiersz['pojemnosc'], $wiersz['kategoria'], $wyposazenie);
				}
	   ?>
</tbody>
</table>
</body>
</html>