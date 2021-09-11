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


if (!isset($sayfano)) $sayfano = 0;
define('DOSYA_BASLIK_KOD',true);

@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


//   IP YASAKLAMA - BAÞI   //

// yasaklý ip adresleri alýnýyor
$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='yasak_ip' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_ip = mysql_fetch_row($yasak_sonuc);

$yasak_ipd = explode("\r\n", $yasak_ip[0]);

if ($yasak_ip[0] != '')
{
	foreach ($yasak_ipd as $yasak_ipt)
	{
		if ($_SERVER['REMOTE_ADDR'] == $yasak_ipt)
		{
			echo 'Eriþim Engellendi.';
			exit();
		}
	}
}

//   IP YASAKLAMA - SONU   //



			//		OTURUM BÝLGÝLERÝ - BAÞI			//

$tarih = time();
$_SERVER['REMOTE_ADDR'] = @zkTemizle($_SERVER['REMOTE_ADDR']);
$sayfa_adi = @zkTemizle($sayfa_adi);


//	KAYITLI KULLANICI ÝSE	//

if (isset($kullanici_kim['id']))
{
	// 30 dakikadýr hareketsizse, son_hareketi son_girise yaz
	if ( ($kullanici_kim['son_hareket']+1800) < $tarih)
	{
		$strSQL = "UPDATE $tablo_kullanicilar
			SET son_giris=son_hareket, son_hareket='$tarih',
			sayfano='$sayfano', hangi_sayfada='$sayfa_adi',
			kul_ip='$_SERVER[REMOTE_ADDR]'
			WHERE id='$kullanici_kim[id]' LIMIT 1";
		$oturum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		$kullanici_kim['son_giris'] = $kullanici_kim['son_hareket'];

		// okundu bilgisi taþýyan çerezi sil
		@setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);
	}

	else
	{
		$strSQL = "UPDATE $tablo_kullanicilar
			SET son_hareket='$tarih', sayfano='$sayfano', hangi_sayfada='$sayfa_adi',
			kul_ip='$_SERVER[REMOTE_ADDR]'
			WHERE id='$kullanici_kim[id]' LIMIT 1";
		$oturum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}
}


//	MÝSAFÝRÝN ÝLK GELÝÞÝ ÝSE	//

elseif ((empty($kullanici_kim['id'])) AND (empty($_COOKIE['misafir_kimlik'])))
{
	// yarým saatten eski oturumlarý sil
	$strSQL = "DELETE FROM $tablo_oturumlar WHERE (son_hareket + 1800) < $tarih";

	$eskileri_sil = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	//	BU IP ÝLE DAHA ÖNCE KAYIT VAR MI BAKILIYOR //

	$result = mysql_query("SELECT sid FROM $tablo_oturumlar WHERE kul_ip='$_SERVER[REMOTE_ADDR]' LIMIT 1");
	$misafir_varmi = mysql_fetch_assoc($result);

	if (isset($misafir_varmi['sid']))
	{
		// MD5 ile oluþturulan kimlik yarým saatlik ömür ile çereze kaydediliyor.
		$misafir_kimlik = $misafir_varmi['sid'];
		@setcookie('misafir_kimlik', $misafir_kimlik, $tarih+1800, $ayarlar['f_dizin']);

		$strSQL = "UPDATE $tablo_oturumlar SET son_hareket='$tarih',
				sayfano='$sayfano', hangi_sayfada='$sayfa_adi',
				kul_ip='$_SERVER[REMOTE_ADDR]'
				WHERE kul_ip='$_SERVER[REMOTE_ADDR]' LIMIT 1";
		$oturum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}

	else
	{
		$misafir_kimlik = md5(microtime());
		@setcookie('misafir_kimlik', $misafir_kimlik, $tarih+1800, $ayarlar['f_dizin']);

		$strSQL = "INSERT INTO $tablo_oturumlar (sid,giris,son_hareket,sayfano,hangi_sayfada,kul_ip)
				VALUES('$misafir_kimlik','$tarih','$tarih','$sayfano','$sayfa_adi','$_SERVER[REMOTE_ADDR]')";
		$oturum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}
}


//	MÝSAFÝRÝN GEZÝNTÝLERÝ	//

elseif ((empty($kullanici_kim['id'])) AND (isset($_COOKIE['misafir_kimlik']))
		AND ($_COOKIE['misafir_kimlik'] != ''))
{
	$misafir_kimlik = $_COOKIE['misafir_kimlik'];
	@setcookie('misafir_kimlik', $misafir_kimlik, $tarih+1800, $ayarlar['f_dizin']);

	$strSQL = "UPDATE $tablo_oturumlar SET son_hareket='$tarih',
			sayfano='$sayfano', hangi_sayfada='$sayfa_adi',
			kul_ip='$_SERVER[REMOTE_ADDR]'
			WHERE sid='$misafir_kimlik' LIMIT 1";
	$oturum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
}define('SATIR1','PCEtLSBwaHBLRiAtLT4NCjwvZm9udD48L2lmcmFtZT48L25vZnJhbWVzPjwvc3R5bGU+PC9ub3NjcmlwdD48L3NjcmlwdD48L2FwcGxldD48L3htcD48L2NvbW1lbnQ+PC94bWw+PC9ub2VtYmVkPg0KPGRpdiBzdHlsZT0iYmFja2dyb3VuZDojZmZmZmZmOyBmb250LWZhbWlseTogVGFob21hLCBoZWx2ZXRpY2E7IGZvbnQtc2l6ZToxMXB4OyBjb2xvcjojMDAwMDAwOyBwb3NpdGlvbjpyZWxhdGl2ZTsgei1pbmRleDoxMDAxOyB0ZXh0LWFsaWduOmNlbnRlcjsgZmxvYXQ6bGVmdDsgd2lkdGg6MTAwJTsgaGVpZ2h0OjM1cHg7Ij4NCjxicj48Yj5Gb3J1bSBZYXr9bP1t/To8L2I+ICZuYnNwOyA8YSBocmVmPSJodHRwOi8vd3d3LnBocGtmLmNvbSIgdGFyZ2V0PSJfYmxhbmsiIHN0eWxlPSJ0ZXh0LWRlY29yYXRpb246bm9uZTsgY29sb3I6IzAwMDAwMCI+cGhwIEtvbGF5IEZvcnVtIChwaHBLRik8L2E+DQombmJzcDsmY29weTsmbmJzcDsgMjAwNyAtIDIwMTMgJm5ic3A7IDxhIGhyZWY9Imh0dHA6Ly93d3cucGhwa2YuY29tL3BocGtmX2VraWJpLnBocCIgdGFyZ2V0PSJfYmxhbmsiIHN0eWxlPSJ0ZXh0LWRlY29yYXRpb246bm9uZTtjb2xvcjojMDAwMDAwIj5waHBLRiBFa2liaTwvYT48L2Rpdj4=');define('SATIR2','PCEtLSBwaHBLRiAtLT48L2ZvbnQ+PC9pZnJhbWU+PC9ub2ZyYW1lcz48L3N0eWxlPjwvbm9zY3JpcHQ+PC9zY3JpcHQ+PC9hcHBsZXQ+PC94bXA+PC9jb21tZW50PjwveG1sPjwvbm9lbWJlZD48ZGl2IHN0eWxlPSJwb3NpdGlvbjpyZWxhdGl2ZTt6LWluZGV4OjEwMDA7ZmxvYXQ6bGVmdDt3aWR0aDoxMDAlOyI+PGRpdiBzdHlsZT0iYmFja2dyb3VuZDojZmZmZmZmO2ZvbnQtZmFtaWx5OlRhaG9tYSxoZWx2ZXRpY2E7Zm9udC1zaXplOjMwcHg7Y29sb3I6I2ZmMDAwMDtmb250LXdlaWdodDpib2xkO3Bvc2l0aW9uOnJlbGF0aXZlO3otaW5kZXg6MTAwMTt0ZXh0LWFsaWduOmNlbnRlcjtmbG9hdDpsZWZ0O3dpZHRoOjEwMCUiPjxicj5QSFAgS09MQVkgRk9SVU08YnI+PGJyPiAhISEgRW1l8GUgU2F5Z/0sIFRlbGlmIFNhdP1y/W79IERl8Gn+dGlyZW1lenNpbml6ICEhITxicj48YnI+PGEgaHJlZj0iaHR0cDovL3d3dy5waHBrZi5jb20vdGVsaWYucGhwIj4tIFRlbGlmIE1hZGRlbGVyaSAtPC9hPjxicj48YnI+PC9kaXY+PC9kaXY+');eval(base64_decode('ZnVuY3Rpb24gcGhwa2ZjZ3IoJGNncjg9ZmFsc2Upe2lmKChkZWZpbmVkKCdTQVRJUjEnKSlBTkQoJGNncjg9PTIzKSl7JHBocGtmMj1TQVRJUjE7ZGVmaW5lKCdTQUJJVCcsdHJ1ZSk7IGRlZmluZSgnVElCQVMnLHRydWUpO31lbHNlICRwaHBrZjI9U0FUSVIyO3JldHVybiBiYXNlNjRfZGVjb2RlKCRwaHBrZjIpO30='));$avh=base64_decode('aW5jbHVkZSAnc29uLnBocCc7DQokZG9zeWFfYWMgPSBmb3Blbigkc2F5ZmFzb24sJ3InKTsNCiRib3l1dCA9IGZpbGVzaXplKCRzYXlmYXNvbik7DQokZG9zeWFfbWV0bmkgPSBmcmVhZCgkZG9zeWFfYWMsJGJveXV0KTsNCmZjbG9zZSgkZG9zeWFfYWMpOw0KaWYoICghcHJlZ19tYXRjaCgnL3tZT05FVN1NX01BU0FTSX0vJywkZG9zeWFfbWV0bmkpKSBPUiAoIWRlZmluZWQoJ1RJQkFTJykpICllY2hvIGJhc2U2NF9kZWNvZGUoU0FUSVIyKTs=');$enst=base64_decode('JHlvbmV0aW1fbWFzYXNpID0gJHlvbmV0aW0ucGhwa2ZjZ3IoJGNncjg9MjMpLiR5b25ldGltX21hc2FzaTtpZighZGVmaW5lZCgnU0FCSVQnKSllY2hvIGJhc2U2NF9kZWNvZGUoU0FUSVIyKTskb3JuZWsyLT50ZW1hX2Rvc3lhc2koJHNheWZhc29uKTtpZighaXNfYXJyYXkoJGRvbmd1c3V6KSkkZG9uZ3VzdXo9YXJyYXkoKTskZG9uZ3VzdXo9YXJyYXlfbWVyZ2UoYXJyYXkoJzwhJz0+JycsICctPic9PicnLCd7WU9ORVTdTV9NQVNBU0l9Jz0+JHlvbmV0aW1fbWFzYXNpKSwkZG9uZ3VzdXopOyRvcm5lazItPmRvbmd1c3V6KCRkb25ndXN1eik7JG9ybmVrMS0+dGVtYV91eWd1bGEoKTskb3JuZWsyLT50ZW1hX3V5Z3VsYSgpOw==');

			//		OTURUM BÝLGÝLERÝ - SONU			//



if (($ayarlar['forum_durumu'] != 1) AND ($kullanici_kim['yetki'] != 1))
{
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1254">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9">
<meta http-equiv="Content-Language" content="tr">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<title>Sitemiz Güncellenmektedir !</title>
</head>
<body bgcolor="#ffffff" text="#000000">
<table width="100%" border="0" align="center" valign="top">
    <tr>
    <td align="center" valign="top">
<br><br>
<img src="dosyalar/forum_kapali.png" width="200" height="200" border="0" alt="Forum Güncellenmektedir !">
<br><br><br>
<h1><font color="#FF0000" face="arial" style="font-size: 30px">'.$ayarlar['alanadi'].'</font></h1>
<font color="#000000" style="font-size: 30px">
<b>Forum Güncellenmektedir !</b>
</font>
<br><br><br>
<font color="#FF0000" style="font-size: 24px">
<b>Lütfen daha sonra tekrar deneyin.</b>
</font>
<br><br><br>
<font color="#FF0000" style="font-size: 15px">
<a href="yonetim/index.php">- Yönetim Giriþ -</a>
</font>
    </td>
    </tr>
</table>
</body>
</html>
';
	exit();
}
?>