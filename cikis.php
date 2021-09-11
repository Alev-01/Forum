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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


// oturum bilgisine bakýlýyor
if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $kullanici_kim['kullanici_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

if ($_GET['o'] != $o)
{
	header('Location: hata.php?hata=45');
	exit();
}


$_SERVER['REMOTE_ADDR'] = zkTemizle($_SERVER['REMOTE_ADDR']);
$sayfano = '-1';
$sayfa_adi = 'Kullanýcý çýkýþ yaptý';
$tarih = time();


$strSQL = "UPDATE $tablo_kullanicilar
		SET son_hareket='$tarih', hangi_sayfada='$sayfa_adi', sayfano='$sayfano',
		kul_ip='$_SERVER[REMOTE_ADDR]',kullanici_kimlik='',yonetim_kimlik=''
		WHERE id='$kullanici_kim[id]'";
$sonuc = mysql_query($strSQL) or die('<h2>sorgu baþarýsýz</h2>');


setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);


if ( ( empty($_SERVER['HTTP_REFERER']) ) OR ($_SERVER['HTTP_REFERER'] == '') 
	OR ( preg_match('/hata.php/i', $_SERVER['HTTP_REFERER'])) )
{
	header('Location: index.php');
	exit();
}

else
{
	if (preg_match('/.php\?/i', $_SERVER['HTTP_REFERER']))
	{
		header('Location: '.$_SERVER['HTTP_REFERER'].'&cikiss=1');
		exit();
	}

	else
	{
		header('Location: '.$_SERVER['HTTP_REFERER'].'?cikiss=1');
		exit();
	}
}
?>