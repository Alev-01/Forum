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
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
$tarih = time();


// özel ileti özelliði kapalýysa
if ($ayarlar['o_ileti'] == 0)
{
	header('Location: hata.php?uyari=2');
	exit();
}


// FORM DOLUYSA ÝÞLEMLERE DEVAM //

if ($_POST['kayit_yapildi_mi'] == 'form_dolu'):


$_POST['ozel_kime'] = zkTemizle(trim($_POST['ozel_kime']));

//  kullanýcý adý yoksa veya 4 karakterden kýsaysa
if (strlen($_POST['ozel_kime']) < 4)
{
	header('Location: hata.php?hata=63');
	exit();
}

//  mesaj baþlýðý ve içeriði denetleniyor
if (( strlen($_POST['mesaj_baslik']) < 3) or ( strlen($_POST['mesaj_baslik']) > 60) or ( strlen($_POST['mesaj_icerik']) < 3))
{
	header('Location: hata.php?hata=64');
	exit();
}


// zararlý kodlar temizleniyor

// magic_quotes_gpc açýksa
if (get_magic_quotes_gpc(1))
{
	$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),1);
	$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),2);
}

// magic_quotes_gpc kapalýysa
else
{
	$_POST['mesaj_baslik'] = @ileti_yolla($_POST['mesaj_baslik'],1);
	$_POST['mesaj_icerik'] = @ileti_yolla($_POST['mesaj_icerik'],2);
}


// bbcode kullanma bilgisi
if (isset($_POST['bbcode_kullan'])) $bbcode_kullan = 1;
else $bbcode_kullan = 0;


// ifade kullanma bilgisi
if (isset($_POST['ifade'])) $ifade_kullan = 1;
else $ifade_kullan = 0;


// üye adý denetleniyor
$strSQL = "SELECT id,kullanici_adi,posta,engelle,kul_etkin FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[ozel_kime]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$kime = mysql_fetch_array($sonuc);


// üye adý geçersizse
if (!$kime['id'])
{
	header('Location: hata.php?hata=66');
	exit();
}


// üye engellenmiþse
if ($kime['engelle'] == '1')
{
	header('Location: hata.php?hata=178');
	exit();
}


// üyenin hesabý etkin deðilse
if ($kime['kul_etkin'] == '0')
{
	header('Location: hata.php?hata=179');
	exit();
}


// gönderen yönetici veya yardýmcý deðilse engellenmiþ olabilir.
if ($kullanici_kim['yetki'] == 0)
{
	// gönderilen üyenin engelleme girdileri çekiliyor
	$strSQL = "SELECT * FROM $tablo_yasaklar WHERE etiket='$kime[id]' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$satir = mysql_fetch_array($sonuc);


	// engelleme tipi belirleniyor
	if (isset($satir['tip']))
	{
		if ($satir['tip'] == '1')
		{
			if (!preg_match("/$kullanici_kim[kullanici_adi],/i", $satir['deger']))
			{
				header('Location: hata.php?hata=176');
				exit();
				}
		}

		elseif ($satir['tip'] == '2')
		{
			if (preg_match("/$kullanici_kim[kullanici_adi],/i", $satir['deger']))
			{
				header('Location: hata.php?hata=177');
				exit();
			}
		}
	}
}


// iki ileti arasý süresi dolmamýþsa uyarý ver
if ( ($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']) )
{
	header('Location: hata.php?hata=65');
	exit();
}


// gönderilen kiþinin gelen kutusu doluysa uyarý ver
$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kime='$kime[kullanici_adi]' AND alan_kutu='1'");
$num_rows = mysql_num_rows($result);

if(($num_rows + 1) > $ayarlar['gelen_kutu_kota'])
{
	header('Location: hata.php?hata=67');
	exit();
}


// özel ileti veriabanýna giriliyor
$strSQL = "INSERT INTO $tablo_ozel_ileti (kimden,kime,ozel_baslik,ozel_icerik,gonderme_tarihi,gonderen_kutu,alan_kutu,bbcode_kullan,ifade)";
$strSQL .= "VALUES ('$kullanici_kim[kullanici_adi]','$kime[kullanici_adi]','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$tarih','3','1','$bbcode_kullan','$ifade_kullan')";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


// gönderilenin okunmmamýþ özel ileti sayýsý arttýrýlýyor
$strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi+1 WHERE id='$kime[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


// gönderenin son ileti tarihi güncelleniyor
$strSQL = "UPDATE $tablo_kullanicilar SET son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');




//		/POSTALAR/OZEL_POSTA.TXT DOSYASINDAKÝ YAZILAR ALINIYOR...		//
//		... BELÝRTÝLEN YERLERE YENÝ BÝLGÝLER GÝRÝLÝYOR		// 

if ($ayarlar['oi_uyari'])
{
	if (!($dosya_ac = fopen('./postalar/ozel_ileti_uyari.txt','r'))) die ('Dosya Açýlamýyor');
	$posta_metni = fread($dosya_ac,1024);
	fclose($dosya_ac);

	$ozel_adres = 'http://'.$ayarlar['alanadi'];
	if ($ayarlar['f_dizin'] != '/') $ozel_adres .= $ayarlar['f_dizin'];
	$ozel_adres .= '/ozel_ileti.php';

	$bul = array('{forumadi}',
	'{kullanici_adi}',
	'{ozel_ileti_sayfasi}');

	$cevir = array($ayarlar['title'],
	$kullanici_kim['kullanici_adi'],
	$ozel_adres);

	$posta_metni = str_replace($bul,$cevir,$posta_metni);


	// posta yollanýyor

	require('eposta_sinif.php');
	$mail = new eposta_yolla();

	if ($ayarlar['eposta_yontem'] == 'mail') $mail->MailKullan();
	elseif ($ayarlar['eposta_yontem'] == 'smtp') $mail->SMTPKullan();

	$mail->sunucu = $ayarlar['smtp_sunucu'];
	if ($ayarlar['smtp_kd'] == 'true') $mail->smtp_dogrulama = true;
	else $mail->smtp_dogrulama = false;
	$mail->kullanici_adi = $ayarlar['smtp_kullanici'];
	$mail->sifre = $ayarlar['smtp_sifre'];

	$mail->gonderen = $ayarlar['y_posta'];
	$mail->gonderen_adi = $ayarlar['anasyfbaslik'];
	$mail->GonderilenAdres($kime['posta']);

	if (!empty($_POST['eposta_kopya'])) $mail->DigerAdres($kullanici_kim['posta']);

	$mail->YanitlamaAdres($ayarlar['y_posta']);
	$mail->konu = $ayarlar['title'].' - Özel iletiniz Var';
	$mail->icerik = $posta_metni;

	$mail->Yolla();
}
//	E-POSTA YOLLANIYOR - SONU	//



header('Location: hata.php?bilgi=11');
exit();

endif;
?>