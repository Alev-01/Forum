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
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
$tarih = time();


// �zel ileti �zelli�i kapal�ysa
if ($ayarlar['o_ileti'] == 0)
{
	header('Location: hata.php?uyari=2');
	exit();
}


// FORM DOLUYSA ��LEMLERE DEVAM //

if ($_POST['kayit_yapildi_mi'] == 'form_dolu'):


$_POST['ozel_kime'] = zkTemizle(trim($_POST['ozel_kime']));

//  kullan�c� ad� yoksa veya 4 karakterden k�saysa
if (strlen($_POST['ozel_kime']) < 4)
{
	header('Location: hata.php?hata=63');
	exit();
}

//  mesaj ba�l��� ve i�eri�i denetleniyor
if (( strlen($_POST['mesaj_baslik']) < 3) or ( strlen($_POST['mesaj_baslik']) > 60) or ( strlen($_POST['mesaj_icerik']) < 3))
{
	header('Location: hata.php?hata=64');
	exit();
}


// zararl� kodlar temizleniyor

// magic_quotes_gpc a��ksa
if (get_magic_quotes_gpc(1))
{
	$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),1);
	$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),2);
}

// magic_quotes_gpc kapal�ysa
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


// �ye ad� denetleniyor
$strSQL = "SELECT id,kullanici_adi,posta,engelle,kul_etkin FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[ozel_kime]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$kime = mysql_fetch_array($sonuc);


// �ye ad� ge�ersizse
if (!$kime['id'])
{
	header('Location: hata.php?hata=66');
	exit();
}


// �ye engellenmi�se
if ($kime['engelle'] == '1')
{
	header('Location: hata.php?hata=178');
	exit();
}


// �yenin hesab� etkin de�ilse
if ($kime['kul_etkin'] == '0')
{
	header('Location: hata.php?hata=179');
	exit();
}


// g�nderen y�netici veya yard�mc� de�ilse engellenmi� olabilir.
if ($kullanici_kim['yetki'] == 0)
{
	// g�nderilen �yenin engelleme girdileri �ekiliyor
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


// iki ileti aras� s�resi dolmam��sa uyar� ver
if ( ($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']) )
{
	header('Location: hata.php?hata=65');
	exit();
}


// g�nderilen ki�inin gelen kutusu doluysa uyar� ver
$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kime='$kime[kullanici_adi]' AND alan_kutu='1'");
$num_rows = mysql_num_rows($result);

if(($num_rows + 1) > $ayarlar['gelen_kutu_kota'])
{
	header('Location: hata.php?hata=67');
	exit();
}


// �zel ileti veriaban�na giriliyor
$strSQL = "INSERT INTO $tablo_ozel_ileti (kimden,kime,ozel_baslik,ozel_icerik,gonderme_tarihi,gonderen_kutu,alan_kutu,bbcode_kullan,ifade)";
$strSQL .= "VALUES ('$kullanici_kim[kullanici_adi]','$kime[kullanici_adi]','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$tarih','3','1','$bbcode_kullan','$ifade_kullan')";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


// g�nderilenin okunmmam�� �zel ileti say�s� artt�r�l�yor
$strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi+1 WHERE id='$kime[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


// g�nderenin son ileti tarihi g�ncelleniyor
$strSQL = "UPDATE $tablo_kullanicilar SET son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');




//		/POSTALAR/OZEL_POSTA.TXT DOSYASINDAK� YAZILAR ALINIYOR...		//
//		... BEL�RT�LEN YERLERE YEN� B�LG�LER G�R�L�YOR		// 

if ($ayarlar['oi_uyari'])
{
	if (!($dosya_ac = fopen('./postalar/ozel_ileti_uyari.txt','r'))) die ('Dosya A��lam�yor');
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


	// posta yollan�yor

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
	$mail->konu = $ayarlar['title'].' - �zel iletiniz Var';
	$mail->icerik = $posta_metni;

	$mail->Yolla();
}
//	E-POSTA YOLLANIYOR - SONU	//



header('Location: hata.php?bilgi=11');
exit();

endif;
?>