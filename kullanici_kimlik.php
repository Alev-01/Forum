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

if (!defined('DOSYA_KULLANICI_KIMLIK')) define('DOSYA_KULLANICI_KIMLIK',true);


//	KULLANICI TANINIYOR	//

if ((isset($_COOKIE['kullanici_kimlik'])) AND ($_COOKIE['kullanici_kimlik'] != ''))
{
	if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);

	$strSQL = "SELECT id,kullanici_kimlik,yetki,kullanici_adi,okunmamis_oi,son_ileti,son_giris,son_hareket,kul_ip,grupid,temadizini,temadizinip FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$kullanici_kim = mysql_fetch_assoc($sonuc);


	if (!mysql_num_rows($sonuc)) $kullanici_kim = 0;

	else
	{
		//  IP ADRES� DE���M��SE VEYA �EREZ S�RES� DOLMU�SA  //
		//  �EREZ TEM�ZLEN�YOR VE K�ML�K B�LG�S� S�L�N�YOR  //

		if ( ($kullanici_kim['kul_ip'] != $_SERVER['REMOTE_ADDR']) OR
			(($kullanici_kim['son_hareket'] + $ayarlar['k_cerez_zaman']) < time()) )
		{
			setcookie('kullanici_kimlik','',0,$ayarlar['f_dizin']);
			setcookie('yonetim_kimlik','',0,$ayarlar['f_dizin']);

			$strSQL = "UPDATE $tablo_kullanicilar SET kullanici_kimlik='', yonetim_kimlik='' WHERE id='$kullanici_kim[id]' LIMIT 1";
			$sonuc = mysql_query($strSQL);

			$kullanici_kim = 0;
		}
	}
}

else $kullanici_kim = 0;

?>