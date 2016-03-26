<?php
require_once "connect_to_database.inc.php";
require_once 'klient_czy_zalogowany.php';

//$idKlienta = 2;

$query_one = "SELECT czyFirma FROM klienci WHERE idKlienta=$idKlienta";

if(!$query_one_run = mysql_query($query_one))
{
  echo 'blad zapytania 1';
}

$query_one_row = mysql_fetch_assoc($query_one_run);

$czy_firma = $query_one_row['czyFirma'];

//klient prywatny
if(!$czy_firma)
{
    $query_two = "select
                  k.idklienta, concat_ws(' ', k.imie, k.nazwisko) as Klient, ifnull(f.nazwafirmy, '-') as firma, k.adreskraj, k.adresmiasto,
                  k.adresulica, k.adreskod, k.telefon, ifnull(k.email, 'brak') as email, ifnull(k.nip, 'brak') as nip, k.kontoAktywne
                  from klienci as k left join klienci as f on k.idfirmy = f.idklienta
                  where
                  k.idKlienta = $idKlienta";

    if(!$query_two_run = mysql_query($query_two))
    {
      echo 'blad zapytania 2';
    }
    
    $query_two_row = mysql_fetch_assoc($query_two_run);
}
else
{
   $query_two = "select
                k.idklienta, nazwaFirmy, concat_ws(' ', imie, nazwisko) as Opiekun, adreskraj, adresmiasto, adresulica, adreskod,
                telefon, ifnull(email, 'brak') as email, nip, regon, kontoAktywne
                from klienci as k
                where
                idKlienta = $idKlienta";
                
    if(!$query_two_run = mysql_query($query_two))
    {
      echo 'blad zapytania 2';
    }
    
    $query_two_row = mysql_fetch_assoc($query_two_run);
}
?>
<div class="container">
<h1><center>Panel klienta</center></h1>
<div class="panel panel-default panel-body">
<?php
if(!$czy_firma){
echo $query_two_row['Klient'];
if($query_two_row['firma'] != '-') echo " (".$query_two_row['firma'].")";
echo "<br>";
echo $query_two_row['adresulica']."<br>";
echo $query_two_row['adreskod']." ".$query_two_row['adresmiasto']."<br>";
echo $query_two_row['adreskraj']."<br>";
echo "telefon: ".$query_two_row['telefon']."<br>";
echo "email: ".$query_two_row['email']."<br>";
echo "NIP: ".$query_two_row['nip']."<br>";}
?>

<?php
if($czy_firma){
echo $query_two_row['nazwaFirmy']."<br>";
echo $query_two_row['adresulica']."<br>";
echo $query_two_row['adreskod']." ".$query_two_row['adresmiasto']."<br>";
echo $query_two_row['adreskraj']."<br>";
echo "Osoba kontaktowa: ".$query_two_row['Opiekun']."<br>";
echo "telefon: ".$query_two_row['telefon']."<br>";
echo "email: ".$query_two_row['email']."<br>";
echo "NIP: ".$query_two_row['nip']."<br>";
echo "Regon: ".$query_two_row['regon']."<br>";}
?>
</div>
<div class="panel panel-default panel-body">
Stan konta: <?php
if($query_two_row['kontoAktywne'])
	echo '<span class="label label-success">aktywne</span>';
	else echo '<span class="label label-danger">nieaktywne</span>';
?><br>
<?php if(!($czy_firma)) echo '<a href="edycja_danych.php">edytuj dane</a><br>'; ?>
<a href="zmiana_hasla.php">zmień hasło</a><br>
<a href="usuwanie_konta.php">usuń konto</a><br>
<a href="historia_rezerwacji.php">historia rezerwacji</a><br>
<?php if($czy_firma) echo '<a href="rezerwacja_sali.php">zarezerwuj salę konferencyjna</a>'; ?>
</div>
</div>
</body>
</html>