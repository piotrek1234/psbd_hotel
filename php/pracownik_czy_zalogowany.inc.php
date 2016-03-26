<?php
if(!(isset($_SESSION['login'])))
{
session_start();
if(count($_SESSION) == 0)
{
	header("Location: zaloguj.php");
}
else
{
	echo '<!DOCTYPE html>
<html lang="pl">
<head>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	  <script src="bootstrap/js/bootstrap.min.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>';
  $login = $_SESSION['login'];
  $haslo = $_SESSION['haslo'];
  $query = "SELECT haslo, idPracownika, imie, nazwisko, stanowisko FROM pracownicy WHERE login='$login'";
  if(!$query_run = mysql_query($query))
  {
    echo 'blad zapytania';
  }
  $query_row = mysql_fetch_assoc($query_run);

  if($haslo == $query_row['haslo'])
  {
	$stanowisko = $query_row['stanowisko'];
	$pracownik = $query_row['imie'].' '.$query_row['nazwisko'].' (';
		$query = "SELECT nazwa FROM stanowiska WHERE idStanowiska='{$query_row['stanowisko']}'";
		if(!$query_run = mysql_query($query))
		{
			echo 'blad zapytania';
		}
		$query_row = mysql_fetch_assoc($query_run);
	$pracownik .= $query_row['nazwa'] . ')';
    require_once "naglowek_pracownik.php";
  }
  else
  {
    echo "<center><font color='red'>BLAD SESJI</font></center>";
  }
}
}


?>