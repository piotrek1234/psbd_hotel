<?php
$conn_error = 'Nie mo�na po�aczy� si� z baza danych. Skontaktuj si� z administratorem';

$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = '';

$mysql_db = 'hotel';

if(!@mysql_connect($mysql_host, $mysql_user, $mysql_pass)||!@mysql_select_db($mysql_db)){
die($conn_error);
}else{
//echo 'Connected<br>';
mysql_query("SET NAMES 'utf8'");
}

?>