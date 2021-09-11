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


//	ÇEREZ BÝLGÝSÝ YOKSA KULLANICI UYARI SAYFASINA YÖNLENDÝRÝLÝYOR	//

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


//	ÇEREZ BÝLGÝSÝ VARSA VERÝTABANINDAKÝ ÝLE KARÞILAÞTIRILIYOR	//

elseif (isset($_COOKIE['kullanici_kimlik']))
{
	if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);

	$strSQL = "SELECT kullanici_kimlik,son_hareket,kul_ip FROM $tablo_kullanicilar
			WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
	$sonuc = mysql_query($strSQL);

	$satir = mysql_fetch_assoc($sonuc);


	//  KULLANICI KÝMLÝK UYUÞMUYORSA VEYA IP ADRESÝ DEÐÝÞMÝÞSE  //
	//  VEYA ÇEREZ SÜRESÝ DOLMUÞSA ÇEREZ TEMÝZLENÝYOR  //
	//  VE GÝRÝÞ SAYFASINA YÖNLENDÝRÝLÝYOR  //

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