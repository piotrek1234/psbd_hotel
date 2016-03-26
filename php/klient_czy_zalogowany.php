<?php
echo 
'<!DOCTYPE html>
<html lang="pl">
<head>
      <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	  <script src="bootstrap/js/bootstrap.min.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>';

//$idKlienta = 0;

if(!(isset($_SESSION['login'])))
{
session_start();
if(count($_SESSION) == 0)
{
   //require_once "naglowek_niezalogowany.php";
   $nagl_niezal = true;
  //header("Location: zaloguj_klient.php");
	$klient = '';
	$idKlienta = '';
}
else
{
  $login = $_SESSION['login'];
  $haslo = $_SESSION['haslo'];
  $query = "SELECT haslo, idKlienta, imie, nazwisko, nazwaFirmy, kontoAktywne FROM klienci WHERE login='$login'";
  if(!$query_run = mysql_query($query))
  {
    echo 'blad zapytania';
  }
  $query_row = mysql_fetch_assoc($query_run);

  if($haslo == $query_row['haslo'])
  {
	
	$klient = $query_row['imie']. ' ' . $query_row['nazwisko'];
	if($query_row['nazwaFirmy'] != NULL)
	{
		$klient = $query_row['nazwaFirmy'];
	}
	$klient = '<b>'.$klient.'</b>';
    require_once "naglowek_zalogowany.php";
	$idKlienta = $query_row['idKlienta'];
	$kontoAktywne = $query_row['kontoAktywne'];

  }
  else
  {
    echo "<center><font color='red'>BLAD SESJI</font></center>";
	
  }
}
if(isset($nagl_niezal))
{
	$kontoAktywne = false;
	require_once 'naglowek_niezalogowany.php';
}
}
else header('Location: zaloguj_klient.php');


?>