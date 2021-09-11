<?php
/*
 +-=====================================================================-+
 |                     php Kolay Forum (phpKF) v1.90                     |
 +-----------------------------------------------------------------------+
 |             Telif - Copyright (c) 2007 - 2012 Adem YILMAZ             |
 |               http://www.phpkf.com   -   phpkf@phpkf.com              |
 |               T�m haklar� sakl�d�r - All Rights Reserved              |
 +-----------------------------------------------------------------------+
 |  Bu betik �zerinde de�i�iklik yaparak/yapmayarak kullanabilirsiniz.   |
 |  Beti�i da��tma ve resmi s�r�m ��kartma haklar� sadece yazara aittir. |
 |  Hi�bir �ekilde para ile sat�lamaz veya ba�ka bir yerde da��t�lamaz.  |
 |  Beti�in (script) tamam� veya bir k�sm�, kaynak belirtilerek          |
 |  dahi olsa, ba�ka bir betikte kesinlikle kullan�lamaz.                |
 |  Kodlardaki ve sayfalar�n en alt�ndaki telif yaz�lar� silinemez,      |
 |  de�i�tirilemez, veya bu telif ile �eli�en ba�ka bir telif eklenemez. |
 |                                                                       |
 |  Telif maddelerinin de�i�tirilme hakk� sakl�d�r.                      |
 |  G�ncel ve tam telif maddeleri i�in www.phpkf.com`u ziyaret edin.     |
 |  Eme�e sayg� g�stererek bu kurallara uyunuz ve bu b�l�m� silmeyiniz.  |
 +-=====================================================================-+*/


if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


//	FORM DOLU DE��LSE UYAR		//

if ( (empty($_GET['kulid'])) OR (empty($_GET['kulkod'])) OR ($_GET['kulkod'] == '0') ):
header('Location: hata.php?hata=48');
exit();




// E-POSTA ONAYI ��LEMLER�  //

elseif ( (isset($_GET['onay'])) AND ($_GET['onay'] == 'eposta') ):

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


if (is_numeric($_GET['kulid']) == false)
{
	header('Location: hata.php?hata=49');
	exit();
}


if (( strlen($_GET['kulkod']) != 10))
{
	header('Location: hata.php?hata=49');
	exit();
}


$_GET['kulid'] = zkTemizle($_GET['kulid']);
$_GET['kulkod'] = zkTemizle($_GET['kulkod']);


//	KUL ID �LE KUL KOD VER�TABANINDAK� �LE KAR�ILA�TIRIYOR //

$strSQL = "SELECT posta2,kul_etkin_kod FROM $tablo_kullanicilar
			WHERE id='$_GET[kulid]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$etkin_mi = mysql_fetch_assoc($sonuc);


// YEN� E-POSTA YOKSA   //

if ($etkin_mi['posta2'] == '')
{
	header('Location: hata.php?hata=49');
	exit();
}


//	KUL ID �LE KUL KOD UYU�MUYORSA	//

elseif ($etkin_mi['kul_etkin_kod'] != $_GET['kulkod'])
{
	header('Location: hata.php?hata=49');
	exit();
}


//  SORUN YOK ��LEM GER�EKLE�T�R�L�YOR  //

else
{
	$strSQL = "UPDATE $tablo_kullanicilar SET posta='$etkin_mi[posta2]',kul_etkin_kod='0',posta2='' WHERE id='$_GET[kulid]'";
	$sonuc = mysql_query($strSQL);

	header('Location: hata.php?bilgi=45');
	exit();
}





//	G�R�� YAPILMI�SA FORUM ANA SAYFASINA Y�NLEND�R	//

elseif ( isset($kullanici_kim['id']) ):
header('Location: index.php');
exit();




// HESAP ETK�NLE�T�RME ��LEMLER�    //

else:

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


if (is_numeric($_GET['kulid']) == false)
{
	header('Location: hata.php?hata=49');
	exit();
}


if (( strlen($_GET['kulkod']) != 10))
{
	header('Location: hata.php?hata=49');
	exit();
}


$_GET['kulid'] = zkTemizle($_GET['kulid']);
$_GET['kulkod'] = zkTemizle($_GET['kulkod']);


//	KUL ID �LE KUL KOD VER�TABANINDAK� �LE KAR�ILA�TIRIYOR //

$strSQL = "SELECT kul_etkin,kul_etkin_kod FROM $tablo_kullanicilar
			WHERE id='$_GET[kulid]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$etkin_mi = mysql_fetch_assoc($sonuc);


//	KUL ID �LE KUL KOD UYU�MUYORSA	//

if ($etkin_mi['kul_etkin'] == 1)
{
	header('Location: hata.php?bilgi=18');
	exit();
}

elseif ($etkin_mi['kul_etkin_kod'] != $_GET['kulkod'])
{
	header('Location: hata.php?hata=49');
	exit();
}

else
{
	$strSQL = "UPDATE $tablo_kullanicilar SET kul_etkin='1',kul_etkin_kod='0' WHERE id='$_GET[kulid]'";
	$sonuc = mysql_query($strSQL);

	header('Location: hata.php?bilgi=19');
	exit();
}
endif;
?>