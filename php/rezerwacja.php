<?php

require_once "connect_to_database.inc.php";
require_once('pracownik_czy_zalogowany.inc.php');
$query ="SELECT stan, nazwa FROM stanyrezerwacji;";

if($query_run = mysql_query($query))
{
  //echo 'sukces<br>';

}else
{
  //echo 'nie można wykonać zapytania';
}

function createOptions($query_1){
         while($query_row = mysql_fetch_assoc($query_1)){
                          $option1 = $query_row['nazwa'];
                          $option2 = $query_row['stan'];
                          echo "<option value=$option2>$option1</option>";
         }
}
//$numer = 2;
$numer = $_REQUEST['idRezerwacji'];
//$numer = $_GET['idRezerwacji'];
$query_main = "SELECT idklienta, imie, nazwisko, adreskraj, adresmiasto, adresulica,
         adreskod, telefon, nazwa, zaliczka, okresod, okresdo, numerpomieszczenia,
         kluczwrecepcji FROM rezerwacje JOIN klienci USING(idklienta) JOIN pomieszczenia USING(numerpomieszczenia)
         JOIN stanyrezerwacji USING(stan)
         WHERE idrezerwacji = $numer;";

if(isset($_POST['zmien_stan'])&&isset($_POST['wplacona_zaliczka'])&&isset($_POST['zatwierdz']))
{
  $string = $_POST['wplacona_zaliczka'];
  $condition = $_POST['zmien_stan'];
  if(isset($_POST['klucz'])) $temp = true; else $temp = false;
  $pom = $_POST['pomieszczenie'];
  /*if($key=='tak')
  {
    $temp = 1;
  }else
  {
    $temp = 0;
  }*/
  if(!$string)
  {
  }
  else if(!is_numeric($string))
  {
  }else
  {
    $new_query = "UPDATE rezerwacje SET zaliczka='$string' WHERE idrezerwacji=$numer";
    if(!$query_run_new = mysql_query($new_query))
    {
         echo 'blad aktualizacji';
    }
    $new_query = "UPDATE rezerwacje SET stan='$condition' WHERE idrezerwacji=$numer";
    if(!$query_run_new = mysql_query($new_query))
    {
         echo 'blad aktualizacji';
    }
    $new_query = "UPDATE pomieszczenia SET kluczWRecepcji='$temp' WHERE numerPomieszczenia=$pom;";
    if(!$query_run_new = mysql_query($new_query))
    {
         echo 'blad aktualizacji';
    }
  }
}

if($query_run_main = mysql_query($query_main))
{
  //echo 'sukces<br>';
  $query_row = mysql_fetch_assoc($query_run_main);
  //$temp = $query_row['idklienta'];
  //echo $temp;

}else
{
  echo 'nie można wykonać zapytania';
}

function downloadValues($query_2)
{
  $query_row = mysql_fetch_assoc($query_2);
  echo $qurey_row['zaliczka'];
}

?>
<div class="container">
<h1><center>Rezerwacja</center></h1>
<div class="panel panel-default panel-body">
<table>
       <tr>
           <th style="padding-right: 10px;">Rezerwacja numer </th>
           <td><?php echo $numer?></td>
       </tr>
</table>
<br>
<div class="row">
     <div class="col-md-6">
          <table>
                 <tr>
                     <td>Dla:</td>
                 </tr><tr>
                     <td><a href="klient.php?idKlienta=<?php echo $query_row['idklienta']; ?>"><?php echo $query_row['imie'].' '.$query_row['nazwisko']?></a></td>
                 </tr><tr>
                     <td><?php echo $query_row['adreskraj'].', '.$query_row['adresmiasto']?></td>
                 </tr><tr>
                     <td><?php echo $query_row['adresulica'].', '.$query_row['adreskod']?></td>
                 </tr><tr>
                     <td>telefon: <?php echo $query_row['telefon']?></td>
                 </tr>
          </table>
     </div>
     <div class="col-md-6">
          <table>
                 <tr>
                     <td>Stan: <?php echo $query_row['nazwa']?></td>
                 </tr><tr>
                     <td>Wpłacona zaliczka: <?php echo $query_row['zaliczka']?> zł</td>
                 </tr><tr>
                     <td>Data przyjazdu: <?php echo $query_row['okresod']?></td>
                 </tr><tr>
                     <td>Data wyjazdu: <?php echo $query_row['okresdo']?></td>
                 </tr><tr>
                     <td>Pomieszczenie: <span class="label label-info"><?php echo $query_row['numerpomieszczenia']; ?></span></td>
                 </tr>
          </table>
     </div>
</div>
</div>
<div class="panel panel-default panel-body">
<form action="rezerwacja.php?idRezerwacji=<?php echo $_REQUEST['idRezerwacji']; ?>" method="POST">
<table cellspacing="10">
<tr></tr>
</table>
<table style="width: 40%; text-align: left" cellspacing="10">
       <tr>
           <th>Zmień stan:</th>
           <td>
               <select name="zmien_stan" style="width: 140px">
                       <?php createOptions($query_run);?>
               </select>
           </td>
       </tr>
       <tr>
           <th>Wpłacona zaliczka:</th>
           <td><input type="text" name="wplacona_zaliczka"  style="width: 140px" value=<?php echo $query_row['zaliczka']?>>
		   <input type="hidden" name="pomieszczenie" value="<?php echo $pomieszczenie;?>"></td>
           <td>

<?php

if(isset($_POST['zmien_stan'])&&isset($_POST['wplacona_zaliczka'])&&isset($_POST['zatwierdz']))
{
  $string = $_POST['wplacona_zaliczka'];
  if(!$string)
  {
  }
  else if(!is_numeric($string))
  {
    echo "<font color='red'>niepoprawne dane</font>";
  }else
  {
    echo "<font color='green'>OK</font>";
	$zaptanie_zaliczka = "update rezerwacje set zaliczka = $string where idRezerwacji = $numer";
						
	if(!$zaptanie_zaliczka_run = mysql_query($zaptanie_zaliczka))
	{
	  echo 'blad zaptyania zaliczka';
	  echo mysql_error();
	}
  }
}

?>

           </td>
       </tr>
       <tr>
           <td colspan="2"><input type="checkbox" name="klucz" value="tak" <?php if($query_row['kluczwrecepcji']==1){echo "checked";}?>>Klucz w recepcji</td>
       </tr>
       <tr style="text-align: center">
           <td colspan="2"><input type="submit" name="zatwierdz" value="zatwierdź" class="btn btn-sm btn-primary"></td>
       </tr>
</table>
</form>
</div>
</div>
</body>
</html>