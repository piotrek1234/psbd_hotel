<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
?>
<div class="container" style="padding-top: 30px;">
<div class="panel panel-default panel-body">
<table style="width: 60%" cellspacing="15">
       <tr style="text-align: left">
           <th>Sezony</th>
           <td><a href="lista_sezonow.php">Zobacz listę</a></td>
           <td><a href="sezon.php">Dodaj nowy</a></td>
       </tr>
       <tr style="text-align: left">
           <th>Usługi</th>
           <td><a href="lista_uslug.php">Zobacz listę</a></td>
           <td><a href="kategorie_uslug.php">Zobacz kategorie usług</a></td>
       </tr>
       <tr style="text-align: left">
           <th>Wyposażenie</th>
           <td colspan="2"><a href="wyposazenie_pokoju.php">Zobacz listę dla pokoi</a></td>
       </tr>
       <!--<tr style="text-align: left">
           <th></th>
           <td colspan="2"><a href="wyposazenie_sali.php">Zobacz listę dla sal konferencyjnych</a></td>
       </tr>-->
       <tr style="text-align: left">
           <th>Ceny pomieszczeń</th>
           <td colspan="2"><a href="ceny.php">Ceny pokoi</a></td>
       </tr>
       <!--<tr style="text-align: left">
           <th></th>
           <td colspan="2"><a href="">Ceny sal konferencyjnych</a></td>
       </tr>-->
       <tr style="text-align: left">
           <th>Zniżki</th>
           <td colspan="2"><a href="znizki.php">Przejrzyj udzielone zniżki</a></td>
       </tr>
</table>
</div></div>
</body>
</html>