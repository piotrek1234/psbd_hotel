<?php
session_start();
$_SESSION = array();
session_unset();
if(!(isset($_GET['p']))) header('Location: index.php'); else header('Location: zaloguj.php');
?>