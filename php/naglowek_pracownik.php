<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container-fluid">
	<div class="navbar-header">
	<a class="navbar-brand" href="index.php">Hotel Tulipan</a>
    </div>
	<ul class="nav navbar-nav">
<?php
if(isset($_SESSION['login']))
{
	//konkretna strona główna
	switch($stanowisko)
	{
		case 1:
			echo '		<li><a href="glowna_recepcjonisty.php">Strona główna</a></li>';
			break;
		case 2:
			echo '		<li><a href="glowna_menagera.php">Strona główna</a></li>';
			break;
		case 3:
			echo '		<li><a href="glowna_szefa.php">Strona główna</a></li>';
			break;
		case 4:
			echo '		<li><a href="glowna_barmana.php">Strona główna</a></li>';
			break;
	}
}
else
echo '		<li><a href="zaloguj.php">Strona główna</a></li>';
?>
	</ul>
	<ul class="nav navbar-nav navbar-right">
<?php
if(isset($_SESSION['login']))
echo '		<p class="navbar-text">Zalogowany jako ', $pracownik, '</p>
		<li><a href="wyloguj.php?p">Wyloguj</a></li>';
		else
echo '	<li><a href="zaloguj.php">Zaloguj</a></li>';
?>
	</ul>
</div>
</nav>
<div class="container" style="padding-top:40px;"></div>