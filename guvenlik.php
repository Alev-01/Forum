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


//	�EREZ B�LG�S� YOKSA KULLANICI UYARI SAYFASINA Y�NLEND�R�L�YOR	//

if ((empty($_COOKIE['kullanici_kimlik'])) OR ($_COOKIE['kullanici_kimlik'] == ''))
{
	if (isset($_GET['cikiss']))
	{
		header('Location: index.php');
		exit();
	}

	else
	{
		$git = @str_replace('&', 'veisareti', $_SERVER['REQUEST_URI']);

		header('Location: hata.php?uyari=6&git='.$git);
		exit();
	}
}


//	�EREZ B�LG�S� VARSA VER�TABANINDAK� �LE KAR�ILA�TIRILIYOR	//

elseif (isset($_COOKIE['kullanici_kimlik']))
{
	if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);

	$strSQL = "SELECT kullanici_kimlik,son_hareket,kul_ip FROM $tablo_kullanicilar
			WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
	$sonuc = mysql_query($strSQL);

	$satir = mysql_fetch_assoc($sonuc);


	//  KULLANICI K�ML�K UYU�MUYORSA VEYA IP ADRES� DE���M��SE  //
	//  VEYA �EREZ S�RES� DOLMU�SA �EREZ TEM�ZLEN�YOR  //
	//  VE G�R�� SAYFASINA Y�NLEND�R�L�YOR  //

	if (!mysql_num_rows($sonuc))
	{
		setcookie('kullanici_kimlik','',0,$ayarlar['f_dizin']);
		setcookie('yonetim_kimlik','',0,$ayarlar['f_dizin']);
		header('Location: giris.php');
		exit();
	}

	elseif ( ($satir['kullanici_kimlik'] != $_COOKIE['kullanici_kimlik']) OR
			($satir['kul_ip'] != $_SERVER['REMOTE_ADDR']) OR
			(($satir['son_hareket'] + $ayarlar['k_cerez_zaman']) < time()) )
	{
		setcookie('kullanici_kimlik','',0,$ayarlar['f_dizin']);
		setcookie('yonetim_kimlik','',0,$ayarlar['f_dizin']);
		header('Location: giris.php');
		exit();
	}
}


else
{
	header('Location: hata.php?uyari=6');
	exit();
}

define('DOSYA_GUVENLIK',true);
?>