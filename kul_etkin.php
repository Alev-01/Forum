<?php
/*
 +-=====================================================================-+
 |                     php Kolay Forum (phpKF) v1.90                     |
 +-----------------------------------------------------------------------+
 |             Telif - Copyright (c) 2007 - 2012 Adem YILMAZ             |
 |               http://www.phpkf.com   -   phpkf@phpkf.com              |
 |               Tüm haklarý saklýdýr - All Rights Reserved              |
 +-----------------------------------------------------------------------+
 |  Bu betik üzerinde deðiþiklik yaparak/yapmayarak kullanabilirsiniz.   |
 |  Betiði daðýtma ve resmi sürüm çýkartma haklarý sadece yazara aittir. |
 |  Hiçbir þekilde para ile satýlamaz veya baþka bir yerde daðýtýlamaz.  |
 |  Betiðin (script) tamamý veya bir kýsmý, kaynak belirtilerek          |
 |  dahi olsa, baþka bir betikte kesinlikle kullanýlamaz.                |
 |  Kodlardaki ve sayfalarýn en altýndaki telif yazýlarý silinemez,      |
 |  deðiþtirilemez, veya bu telif ile çeliþen baþka bir telif eklenemez. |
 |                                                                       |
 |  Telif maddelerinin deðiþtirilme hakký saklýdýr.                      |
 |  Güncel ve tam telif maddeleri için www.phpkf.com`u ziyaret edin.     |
 |  Emeðe saygý göstererek bu kurallara uyunuz ve bu bölümü silmeyiniz.  |
 +-=====================================================================-+*/


if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


//	FORM DOLU DEÐÝLSE UYAR		//

if ( (empty($_GET['kulid'])) OR (empty($_GET['kulkod'])) OR ($_GET['kulkod'] == '0') ):
header('Location: hata.php?hata=48');
exit();




// E-POSTA ONAYI ÝÞLEMLERÝ  //

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


//	KUL ID ÝLE KUL KOD VERÝTABANINDAKÝ ÝLE KARÞILAÞTIRIYOR //

$strSQL = "SELECT posta2,kul_etkin_kod FROM $tablo_kullanicilar
			WHERE id='$_GET[kulid]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$etkin_mi = mysql_fetch_assoc($sonuc);


// YENÝ E-POSTA YOKSA   //

if ($etkin_mi['posta2'] == '')
{
	header('Location: hata.php?hata=49');
	exit();
}


//	KUL ID ÝLE KUL KOD UYUÞMUYORSA	//

elseif ($etkin_mi['kul_etkin_kod'] != $_GET['kulkod'])
{
	header('Location: hata.php?hata=49');
	exit();
}


//  SORUN YOK ÝÞLEM GERÇEKLEÞTÝRÝLÝYOR  //

else
{
	$strSQL = "UPDATE $tablo_kullanicilar SET posta='$etkin_mi[posta2]',kul_etkin_kod='0',posta2='' WHERE id='$_GET[kulid]'";
	$sonuc = mysql_query($strSQL);

	header('Location: hata.php?bilgi=45');
	exit();
}





//	GÝRÝÞ YAPILMIÞSA FORUM ANA SAYFASINA YÖNLENDÝR	//

elseif ( isset($kullanici_kim['id']) ):
header('Location: index.php');
exit();




// HESAP ETKÝNLEÞTÝRME ÝÞLEMLERÝ    //

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


//	KUL ID ÝLE KUL KOD VERÝTABANINDAKÝ ÝLE KARÞILAÞTIRIYOR //

$strSQL = "SELECT kul_etkin,kul_etkin_kod FROM $tablo_kullanicilar
			WHERE id='$_GET[kulid]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$etkin_mi = mysql_fetch_assoc($sonuc);


//	KUL ID ÝLE KUL KOD UYUÞMUYORSA	//

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