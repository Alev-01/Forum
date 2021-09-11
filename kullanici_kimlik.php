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

if (!defined('DOSYA_KULLANICI_KIMLIK')) define('DOSYA_KULLANICI_KIMLIK',true);


//	KULLANICI TANINIYOR	//

if ((isset($_COOKIE['kullanici_kimlik'])) AND ($_COOKIE['kullanici_kimlik'] != ''))
{
	if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);

	$strSQL = "SELECT id,kullanici_kimlik,yetki,kullanici_adi,okunmamis_oi,son_ileti,son_giris,son_hareket,kul_ip,grupid,temadizini,temadizinip FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$kullanici_kim = mysql_fetch_assoc($sonuc);


	if (!mysql_num_rows($sonuc)) $kullanici_kim = 0;

	else
	{
		//  IP ADRESÝ DEÐÝÞMÝÞSE VEYA ÇEREZ SÜRESÝ DOLMUÞSA  //
		//  ÇEREZ TEMÝZLENÝYOR VE KÝMLÝK BÝLGÝSÝ SÝLÝNÝYOR  //

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