<?php

require_once "connect_to_database.inc.php";

if(isset($_POST['Zaloguj']))
{
  $login = $_POST['login'];
  $haslo = $_POST['haslo'];

  $query = "SELECT haslo, idKlienta, login FROM klienci WHERE login='$login'";

  if(!$query_run = mysql_query($query))
  {
    echo 'blad zapytania 1';
  }
  $query_row = mysql_fetch_assoc($query_run);

  if($query_row['login'] == NULL)
  {
     $blad = "<div class=\"alert alert-danger\">Brak loginu w bazie</div>";
  }
   else{
  if(md5($haslo) == $query_row['haslo'])
  {
    //otworz sesje
    session_start();
    $_SESSION['login']=$login;
    $_SESSION['haslo']=md5($haslo);
    $_SESSION['idklienta']=$query_row['idKlienta'];

    header("Location: panel_klienta.php");

  }
  else
  {
    $blad = "<div class=\"alert alert-danger\">Błędne hasło</div>";
  }
   }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	  <script src="bootstrap/js/bootstrap.min.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php require_once "naglowek_niezalogowany.php"; ?>
<div class="container">
<h1><center>Logowanie</center></h1>
<div class="panel panel-default panel-body">
<?php
	if(isset($blad)) echo $blad;
?>
     <form action="zaloguj_klient.php" method="POST">
     <center>
     Login <input type="text" name="login" maxlength="45" size="30" required autofocus><br>
     Hasło <input type="password" name="haslo" maxlength="45" size="30" required><br><br>
           <input type="submit" name="Zaloguj" value="Zaloguj"  class="btn btn-primary">
     </center>
     </form>
</div></div>
</body>
</html>