<?php

/* lista funkcji

sprCzyData($data);
czyRoznicaDatOk($dataOd, $dataDo);
sprCena($cena);
sprNIP($nip);
sprRegon($regon);
czyTylkoLitery($slowo);
sprMail($mail);
czyOdDo($od, $do);  porównuje czy wartosc $do jest wieksza od $od
czyCalkowita($liczba);
czyCenaOdDo($od, $do);

*/
///////////////////////////////czy data jest ok
//jak string jest pusty to też zwraca true - pole daty moze byc puste
function sprCzyData($data)
{
	if($data == "")
		return true;
	
	$data_temp = explode("-", $data);
	$liczba = count($data_temp);
	
	if($liczba == 3)
	{
		$rok = $data_temp[0];
		$miesiac = $data_temp[1];
		$dzien = $data_temp[2];
		
		//echo "data<br>";
		
		if((strlen($rok) == 4)
			&&(strlen($miesiac) == 2)
			&&(strlen($dzien) == 2))
		{
			if(is_numeric($rok)
			   &&is_numeric($miesiac)
			   &&is_numeric($dzien))
			{
				if(((int)$rok) < 0)
				{
					return false;
				}
				
				if(((int)$miesiac) < 0 || ((int)$miesiac) > 12)
				{
					return false;
				}
				
				if(((int)$dzien) < 0 || ((int)$dzien) > 31)
				{
					return false;
				}
				
				return true;
			}
		}
	}
	
	return false;
}

////////////////////////////czy roznica dat jest ok
///// najpierw sprawdzamy format daty poprzednia funkcja
///// jeżeli którakolwiek z dat jest pustym stringiem to zwraca true
function czyRoznicaDatOk($dataOd, $dataDo)
{
	if(($dataOd == "") || ($dataDo == ""))
	{
		return true;
	}

	$dataOd_temp = explode("-", $dataOd);
	$dataDo_temp = explode("-", $dataDo);
	
	$rokOd = intval($dataOd_temp[0]);
	$miesiacOd = intval($dataOd_temp[1]);
	$dzienOd = intval($dataOd_temp[2]);
	
	$rokDo = intval($dataDo_temp[0]);
	$miesiacDo = intval($dataDo_temp[1]);
	$dzienDo = intval($dataDo_temp[2]);
	
	/*if(($rokDo - $rokOd) < 0)
	{
		return false;
	}
	
	else if(($miesiacDo - $miesiacOd) < 0)
	{
		return false;
	}
	
	else if((($dzienDo - $dzienOd) < 0))
	{
		return false;
	}*/
	
	if(($rokDo - $rokOd) > 0)
		return true;
	else if(($miesiacDo - $miesiacOd) > 0)	//lata równe, sprawdzam miesiące
		return true;
	else if(($dzienDo - $dzienOd) >= 0)	//miesiące równe, sprawdzam dni
		return true;
		//teraz zwraca też true dla równych dat
	return false;
}

$cena = "2005";

///////////////////////////////spr ceny
////////////czy jest liczbą i ile cyfr po przecinku
function sprCena($cena)
{
	if($cena == "")
	{
		return true;
	}
	
	if(is_numeric($cena))
	{
		$cena_temp = explode(".", $cena);
		
		if(count($cena_temp) == 1)
		{
			return true;
		}
		
		if((strlen($cena_temp[1]) == 2) || (strlen($cena_temp[1]) == 1))
		{
			return true;
		}
	}
	
	return false;
}
///////////////////////////////

///////////////////////////// czy tylko litery
////// problem dla polskich znaków 
function czyTylkoLitery($slowo)
{
	if($slowo == "")
	{
		return true;
	}

	$temp = preg_replace("/[^a-zA-Z]+/", "", $slowo);
	
	if($temp == $slowo)
	{
		return true;
	}
	
	return true;
}
/////////////////////////////

//////////////////////////// czy nip poprawny
function sprNIP($nip)
{
	if($nip == "")
	{
		return true;
	}
	
	if(is_numeric($nip))
	{
		if(strlen($nip) == 10)
		{
			return true;
		}
	}
	
	return false;
}
////////////////////////////

//////////////////////////// czy regon
function sprRegon($regon)
{
	if($regon == "")
	{
		return true;
	}

	if(is_numeric($regon))
	{
		if(strlen($regon) == 9)
		{
			return true;
		}
	}
	return false;
}
////////////////////////////

////////////////////////////czy email
function sprMail($mail)
{
	if($mail == "")
	{
		return true;
	}

	$temp = explode("@", $mail);
	
	if(count($temp) == 2)
	{
		$temp_k = explode(".", $temp[1]);
		
		if(count($temp_k) > 1)
		{
			return true;
		}
	}
	
	return false;
}
////////////////////////////

////////////////////////////czy liczby od do poprawnie
function czyOdDo($od, $do)
{
	if(($od == "") || ($do == ""))
	{
		return true;
	}

	$intOd = intval($od);
	$intDo = intval($do);
	
	if(($intDo - $intOd) > 0)
	{
		return true;
	}
	
	return false;
}
////////////////////////////

/////////////////////////// czy liczba calkowita
function czyCalkowita($liczba)
{
	if(is_numeric($liczba))
	{
		$temp = explode(".", $liczba);
		
		if(count($temp) == 1)
		{
			if($liczba < 0)
			{
				return false;
			}
			
			return true;
		}
	}
	return false;
}
////////////////////////////

///////////////////////////czy cena od do
function czyCenaOdDo($od, $do)
{
	if(($od == "") || ($do == ""))
	{
		return true;
	}
	
	$fod = (float) $od;
	$fdo = (float) $do;
	
	if(($fdo-$fod) > 0)
	{
		return true;
	}
	
	return false;
}
///////////////////////////
?>