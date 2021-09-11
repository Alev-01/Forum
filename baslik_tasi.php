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


//	BAÞLIÐI TAÞI TIKLANMIÞSA	//

if ( ( isset($_POST['kayit_yapildi_mi']) ) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') ):

if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


if ( (!isset($_POST['mesaj_no'])) OR (is_numeric($_POST['mesaj_no']) == false) )
{
	header('Location: hata.php?hata=47');
	exit();
}

else $_POST['mesaj_no'] = zkTemizle($_POST['mesaj_no']);


if ( (!isset($_POST['tasinan_forum'])) OR (is_numeric($_POST['tasinan_forum']) == false) )
{
	header('Location: hata.php?hata=14');
	exit();
}

else $_POST['tasinan_forum'] = zkTemizle($_POST['tasinan_forum']);



//	BAÞLIÐIN OLDUÐU FORUM ÖÐRENÝLÝYOR	//

$strSQL = "SELECT id,mesaj_baslik,hangi_forumdan FROM $tablo_mesajlar WHERE id='$_POST[mesaj_no]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$forum_no = mysql_fetch_array($sonuc);


//	YÖNETÝCÝ VEYA FORUMUN YARDIMCISI ÝSE DEVAM	//

if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2))
{
	if ($kullanici_kim['yetki'] == 2)
	{
		// Konunun bulunduðu forum
		$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
					WHERE id='$forum_no[hangi_forumdan]'";
		$sonuc = mysql_query($strSQL);
		$kul_izin = mysql_fetch_assoc($sonuc);


		// Konunun bulunduðu forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
		if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
		{
			header('Location: hata.php?hata=3');
			exit();
		}


		// Konunun taþýndýðý forum
		$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
					WHERE id='$_POST[tasinan_forum]'";
		$sonuc = mysql_query($strSQL);
		$kul_izin2 = mysql_fetch_assoc($sonuc);


		// Konunun taþýndýðý forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
		if ( ($kul_izin2['okuma_izni'] == 1) OR ($kul_izin2['konu_acma_izni'] == 1) OR ($kul_izin2['yazma_izni'] == 1) OR ($kul_izin2['okuma_izni'] == 5) OR ($kul_izin2['konu_acma_izni'] == 5) OR ($kul_izin2['yazma_izni'] == 5) )
		{
			header('Location: hata.php?hata=195');
			exit();
		}
	}



	//  konu taþýnýyor
	$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[tasinan_forum]'
				WHERE id='$_POST[mesaj_no]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	// cevaplarý taþýnýyor
	$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[tasinan_forum]'
				WHERE hangi_basliktan='$_POST[mesaj_no]'";
	$sonuc = mysql_query($strSQL);



	// gönderilen forumun cevap sayýsý hesaplanýyor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[tasinan_forum]'");
	$cevap_sayi = mysql_num_rows($result);


	// gönderilen forumun konu ve cevap sayýsý arttýrýlýyor
	$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1,cevap_sayisi='$cevap_sayi'
				WHERE id='$_POST[tasinan_forum]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');



	// alýnan forumun cevap sayýsý hesaplanýyor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_no[hangi_forumdan]'");
	$cevap_sayi = mysql_num_rows($result);


	// alýnan forumun konu ve cevap sayýsý eksiltiliyor
	$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi - 1,cevap_sayisi='$cevap_sayi'
				WHERE id='$forum_no[hangi_forumdan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	header('Location: hata.php?bilgi=9&fno1='.$forum_no['hangi_forumdan'].'&fno2='.$_POST['tasinan_forum']);
	exit();
}


elseif ($kullanici_kim['yetki'] == 3)
{
	// Konunun bulunduðu forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulunduðu forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}


	// Konunun taþýndýðý forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$_POST[tasinan_forum]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin2 = mysql_fetch_assoc($sonuc);


	// Konunun taþýndýðý forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
	if ( ($kul_izin2['okuma_izni'] == 1) OR ($kul_izin2['konu_acma_izni'] == 1) OR ($kul_izin2['yazma_izni'] == 1) OR ($kul_izin2['okuma_izni'] == 5) OR ($kul_izin2['konu_acma_izni'] == 5) OR ($kul_izin2['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=195');
		exit();
	}


	if ($kullanici_kim['grupid'] != '0')
	{
		$grupek1 = "grup='$kullanici_kim[grupid]' AND fno='$_POST[tasinan_forum]' AND yonetme='1' OR";
		$grupek2 = "grup='$kullanici_kim[grupid]' AND fno='$forum_no[hangi_forumdan]' AND yonetme='1' OR";
	}

	else
	{
		$grupek1 = "grup='0' AND";
		$grupek2 = "grup='0' AND";
	}


	// Konunun taþýndýðý forum özel yetki
	$strSQL = "SELECT fno FROM $tablo_ozel_izinler
				WHERE $grupek1 kulad='$kullanici_kim[kullanici_adi]'
				AND fno='$_POST[tasinan_forum]' AND yonetme='1'";
	$kul_izin2 = mysql_query($strSQL);


	// Taþýnan forumda yetkisi yoksa
	if (!mysql_num_rows($kul_izin2))
	{
		header('Location: hata.php?hata=195');
		exit();
	}



	// Konunun bulunduðu forum özel yetki
	$strSQL = "SELECT fno FROM $tablo_ozel_izinler
				WHERE $grupek2 kulad='$kullanici_kim[kullanici_adi]'
				AND fno='$forum_no[hangi_forumdan]' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);


	//	YÖNETME YETKÝSÝ VARSA	//
	if (mysql_num_rows($kul_izin))
	{
		// konu taþýnýyor
		$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[tasinan_forum]'
					WHERE id='$_POST[mesaj_no]' LIMIT 1";
		$sonuc = mysql_query($strSQL);


		// cevaplarý taþýnýyor
		$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[tasinan_forum]'
					WHERE hangi_basliktan='$_POST[mesaj_no]'";
		$sonuc = mysql_query($strSQL);



		// gönderilen forumun cevap sayýsý hesaplanýyor
		$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[tasinan_forum]'");
		$cevap_sayi = mysql_num_rows($result);


		// gönderilen forumun konu ve cevap sayýsý arttýrýlýyor
		$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1,cevap_sayisi='$cevap_sayi'
					WHERE id='$_POST[tasinan_forum]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');



		// alýnan forumun cevap sayýsý hesaplanýyor
		$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_no[hangi_forumdan]'");
		$cevap_sayi = mysql_num_rows($result);


		// alýnan forumun konu ve cevap sayýsý eksiltiliyor
		$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi - 1,cevap_sayisi='$cevap_sayi'
					WHERE id='$forum_no[hangi_forumdan]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		header('Location: hata.php?bilgi=9&fno1='.$forum_no['hangi_forumdan'].'&fno2='.$_POST['tasinan_forum']);
		exit();
	}

	//	YETKÝSÝZ ÝSE UYARILIYOR	//
	else
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}
//		YETKÝSÝZ ÝSE UYARILIYOR		//

else
{
	header('Location: hata.php?hata=3');
	exit();
}






			//	SAYFAYA ÝLK GÝRÝÞ KISMI	//


elseif ( ( isset($_GET['kip']) ) AND ($_GET['kip'] == 'tasi') ):


if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


if ( (!isset($_GET['mesaj_no'])) OR (is_numeric($_GET['mesaj_no']) == false) )
{
	header('Location: hata.php?hata=47');
	exit();
}

else $_GET['mesaj_no'] = zkTemizle($_GET['mesaj_no']);



//	BAÞLIÐIN OLDUÐU FORUM ÖÐRENÝLÝYOR	//

$strSQL = "SELECT id,mesaj_baslik,hangi_forumdan FROM $tablo_mesajlar WHERE id='$_GET[mesaj_no]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$forum_no = mysql_fetch_array($sonuc);


//	YÖNETÝCÝ, FORUM YARDIMCI VEYA BÖLÜMÜN YARDIMCISI ÝSE DEVAM	//

if ($kullanici_kim['yetki'] == 1);


// forum yardýmcýsý
elseif ($kullanici_kim['yetki'] == 2)
{
	// Konunun bulunduðu forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulunduðu forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}


elseif ($kullanici_kim['yetki'] == 3)
{
	// Konunun bulunduðu forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulunduðu forum yetkilerinden biri sadece yöneticilerse veya kapalýysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}


	// Konunun bulunduðu forum özel yetki
	if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$forum_no[hangi_forumdan]' AND yonetme='1' OR";
	else $grupek = "grup='0' AND";

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$forum_no[hangi_forumdan]' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);


	if (mysql_num_rows($kul_izin));

	//	YETKÝSÝZ ÝSE UYARILIYOR	//
	else
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}

//		YETKÝSÝZ ÝSE UYARILIYOR		//
else
{
	header('Location: hata.php?hata=3');
	exit();
}


$sayfano = '11,'.$forum_no['id'];
$sayfa_adi = $forum_no['mesaj_baslik'];
include 'baslik.php';




$options_forum = '';


// forum dalý adlarý çekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$options_forum .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar
					WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
		{
			if ($forum_no['hangi_forumdan'] != $forum_satir['id']) $options_forum .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];

			else $options_forum .= '
			<option value="'.$forum_satir['id'].'" selected="selected"> &nbsp; - '.$forum_satir['forum_baslik'];
		}


		else
		{
			if ($forum_no['hangi_forumdan'] != $forum_satir['id']) $options_forum .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];

			else $options_forum .= '
			<option value="'.$forum_satir['id'].'" selected="selected"> &nbsp; - '.$forum_satir['forum_baslik'];

			while ($alt_forum_satir = mysql_fetch_array($sonuca))
			{
				if ($forum_no['hangi_forumdan'] != $alt_forum_satir['id']) $options_forum .= '
				<option value="'.$alt_forum_satir['id'].'"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];

				else $options_forum .= '
				<option value="'.$alt_forum_satir['id'].'" selected="selected"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
			}
		}
	}
}



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/baslik_tasi.html');

$ornek1->dongusuz(array('{MESAJ_NO}' => $_GET['mesaj_no'],
						'{OPTION_FORUM}' => $options_forum));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>