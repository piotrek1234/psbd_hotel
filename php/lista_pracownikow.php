<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
     function addRow($id, $name, $position, $telephone)
     {
       echo "<tr>";
       echo "<td><a href=\"pracownik.php?idPracownika=$id\">$name</a></td>";
       echo "<td>$position</td>";
       echo "<td>$telephone</td>";
       echo "</tr>";
     }
	 

	 
?>

<div class="container">
<center><h1>Lista pracowników</h1></center>
<table class="table table-striped">
       <thead><tr>
           <th><a href="?sort=nazwisko">Imię i nazwisko</a></th>
           <th><a href="?sort=stanowisko">Stanowisko</a></th>
           <th>Numer telefonu</th>
       </tr></thead>
	   <tbody>
	   <?php
			$query = 'select idPracownika, concat_ws(\' \', imie, nazwisko) as pracownik, nazwa as stanowisko, telefon from pracownicy join stanowiska on pracownicy.stanowisko = stanowiska.idStanowiska';
			if(isset($_GET['sort'])) $query .= ' order by ' . $_GET['sort'];
			//echo $query;
			$wynik = mysql_query($query);
			if($wynik)while ($wiersz = mysql_fetch_array($wynik, MYSQL_ASSOC)) {
				addRow($wiersz['idPracownika'], $wiersz['pracownik'], $wiersz['stanowisko'], $wiersz['telefon']);
				}
	   ?>
</tbody></table>
</div>
</body>
</html>