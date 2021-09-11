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


if (!defined('DOSYA_AYAR')) include '../ayar.php';


//	ÇEREZ BÝLGÝLERÝ YOKSA KULLANICI GÝRÝÞ SAYFASINA YÖNLENDÝRÝLÝYOR	//

if ( (empty($_COOKIE['kullanici_kimlik'])) OR ($_COOKIE['kullanici_kimlik'] == '') OR
	(empty($_COOKIE['yonetim_kimlik'])) OR ($_COOKIE['yonetim_kimlik'] == '') )
{
	if ((isset($_SERVER['REQUEST_URI'])) AND (preg_match('/ip_yonetimi.php\?/', $_SERVER['REQUEST_URI'])) )
		$git = '?git='.@str_replace('&', 'veisareti', $_SERVER['REQUEST_URI']);
	else $git = '';

	header('Location: giris.php'.$git);
	exit();
}


//	ÇEREZ BÝLGÝSÝ VARSA VERÝTABANINDA ÝLE KARÞILAÞTIRILIYOR	//

elseif ((isset($_COOKIE['kullanici_kimlik'])) AND (isset($_COOKIE['yonetim_kimlik'])))
{
	if (!defined('DOSYA_GERECLER')) include '../gerecler.php';

	$_COOKIE['yonetim_kimlik'] = @zkTemizle($_COOKIE['yonetim_kimlik']);
	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);


	// çerez geçerlilik süresi
	if ($ayarlar['k_cerez_zaman'] > 86400) $k_cerez_zaman = 86400;
	else $k_cerez_zaman = $ayarlar['k_cerez_zaman'];


	$strSQL = "SELECT id,yonetim_kimlik,kullanici_kimlik,yetki,son_hareket,kul_ip FROM $tablo_kullanicilar WHERE yonetim_kimlik='$_COOKIE[yonetim_kimlik]' AND kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL);
	$satir = mysql_fetch_array($sonuc);


	//  KULLANICI VEYA YÖNETÝM KÝMLÝK UYUÞMUYORSA VEYA IP ADRESÝ DEÐÝÞMÝÞSE  //
	//  VEYA ÇEREZ BÝR GÜNDEN ESKÝYSE ÇEREZ TEMÝZLENÝYOR  //
	//  VE GÝRÝÞ SAYFASINA YÖNLENDÝRÝLÝYOR  //

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