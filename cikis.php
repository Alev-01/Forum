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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


// oturum bilgisine bak�l�yor
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
$sayfa_adi = 'Kullan�c� ��k�� yapt�';
$tarih = time();


$strSQL = "UPDATE $tablo_kullanicilar
		SET son_hareket='$tarih', hangi_sayfada='$sayfa_adi', sayfano='$sayfano',
		kul_ip='$_SERVER[REMOTE_ADDR]',kullanici_kimlik='',yonetim_kimlik=''
		WHERE id='$kullanici_kim[id]'";
$sonuc = mysql_query($strSQL) or die('<h2>sorgu ba�ar�s�z</h2>');


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