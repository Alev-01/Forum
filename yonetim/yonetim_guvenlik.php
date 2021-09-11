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


if (!defined('DOSYA_AYAR')) include '../ayar.php';


//	�EREZ B�LG�LER� YOKSA KULLANICI G�R�� SAYFASINA Y�NLEND�R�L�YOR	//

if ( (empty($_COOKIE['kullanici_kimlik'])) OR ($_COOKIE['kullanici_kimlik'] == '') OR
	(empty($_COOKIE['yonetim_kimlik'])) OR ($_COOKIE['yonetim_kimlik'] == '') )
{
	if ((isset($_SERVER['REQUEST_URI'])) AND (preg_match('/ip_yonetimi.php\?/', $_SERVER['REQUEST_URI'])) )
		$git = '?git='.@str_replace('&', 'veisareti', $_SERVER['REQUEST_URI']);
	else $git = '';

	header('Location: giris.php'.$git);
	exit();
}


//	�EREZ B�LG�S� VARSA VER�TABANINDA �LE KAR�ILA�TIRILIYOR	//

elseif ((isset($_COOKIE['kullanici_kimlik'])) AND (isset($_COOKIE['yonetim_kimlik'])))
{
	if (!defined('DOSYA_GERECLER')) include '../gerecler.php';

	$_COOKIE['yonetim_kimlik'] = @zkTemizle($_COOKIE['yonetim_kimlik']);
	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);


	// �erez ge�erlilik s�resi
	if ($ayarlar['k_cerez_zaman'] > 86400) $k_cerez_zaman = 86400;
	else $k_cerez_zaman = $ayarlar['k_cerez_zaman'];


	$strSQL = "SELECT id,yonetim_kimlik,kullanici_kimlik,yetki,son_hareket,kul_ip FROM $tablo_kullanicilar WHERE yonetim_kimlik='$_COOKIE[yonetim_kimlik]' AND kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL);
	$satir = mysql_fetch_array($sonuc);


	//  KULLANICI VEYA Y�NET�M K�ML�K UYU�MUYORSA VEYA IP ADRES� DE���M��SE  //
	//  VEYA �EREZ B�R G�NDEN ESK�YSE �EREZ TEM�ZLEN�YOR  //
	//  VE G�R�� SAYFASINA Y�NLEND�R�L�YOR  //

	if(!mysql_num_rows($sonuc))
	{
		setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
		header('Location: giris.php');
		exit();
	}

	elseif ( ($satir['kullanici_kimlik'] != $_COOKIE['kullanici_kimlik']) OR
			($satir['yonetim_kimlik'] != $_COOKIE['yonetim_kimlik']) OR
			($satir['kul_ip'] != $_SERVER['REMOTE_ADDR']) OR
			(($satir['son_hareket'] + $k_cerez_zaman) < time()) )
	{
		setcookie('kullanici_kimlik','',0,$ayarlar['f_dizin']);
		setcookie('yonetim_kimlik','',0,$ayarlar['f_dizin']);

		header('Location: giris.php');
		exit();
	}

	elseif ($satir['yetki'] != 1)
	{
		header('Location: ../hata.php?hata=144');
		exit();
	}
}


else
{
	if ((isset($_SERVER['REQUEST_URI'])) AND (preg_match('/ip_yonetimi.php\?/', $_SERVER['REQUEST_URI'])) )
		$git = '?git='.@str_replace('&', 'veisareti', $_SERVER['REQUEST_URI']);
	else $git = '';

	header('Location: giris.php'.$git);
	exit();
}


define('DOSYA_YONETIM_GUVENLIK',true);
?>