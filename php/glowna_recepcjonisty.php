<?php
require_once('connect_to_database.inc.php');
require_once('pracownik_czy_zalogowany.inc.php');
?>
<div class="container" style="padding-top: 30px;">
<div class="panel panel-default panel-body">
<table style="width: 50%; text-align: left" cellspacing="10">
       <tr>
           <td>Klienci</td>
           <td><a href="lista_klientow.php">Zobacz listę</a></td>
           <td><a href="edycja_danych.php?act=nowy">Dodaj nowego klienta</a></td>
		   <td><a href="nowa_firma.php">Dodaj nową firmę</a></td>
       </tr>
       <tr>
           <td>Rezerwacje</td>
           <td><a href="lista_rezerwacji.php">Zobacz listę</a></td>
           <td colspan="2"><a href="nowa_rezerwacja.php">Dodaj nową</a></td>
       </tr>
</table>
</div>
</div>
</body>
</html>