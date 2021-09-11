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


//	BA�LI�I TA�I TIKLANMI�SA	//

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



//	BA�LI�IN OLDU�U FORUM ��REN�L�YOR	//

$strSQL = "SELECT id,mesaj_baslik,hangi_forumdan FROM $tablo_mesajlar WHERE id='$_POST[mesaj_no]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$forum_no = mysql_fetch_array($sonuc);


//	Y�NET�C� VEYA FORUMUN YARDIMCISI �SE DEVAM	//

if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2))
{
	if ($kullanici_kim['yetki'] == 2)
	{
		// Konunun bulundu�u forum
		$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
					WHERE id='$forum_no[hangi_forumdan]'";
		$sonuc = mysql_query($strSQL);
		$kul_izin = mysql_fetch_assoc($sonuc);


		// Konunun bulundu�u forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
		if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
		{
			header('Location: hata.php?hata=3');
			exit();
		}


		// Konunun ta��nd��� forum
		$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
					WHERE id='$_POST[tasinan_forum]'";
		$sonuc = mysql_query($strSQL);
		$kul_izin2 = mysql_fetch_assoc($sonuc);


		// Konunun ta��nd��� forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
		if ( ($kul_izin2['okuma_izni'] == 1) OR ($kul_izin2['konu_acma_izni'] == 1) OR ($kul_izin2['yazma_izni'] == 1) OR ($kul_izin2['okuma_izni'] == 5) OR ($kul_izin2['konu_acma_izni'] == 5) OR ($kul_izin2['yazma_izni'] == 5) )
		{
			header('Location: hata.php?hata=195');
			exit();
		}
	}



	//  konu ta��n�yor
	$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[tasinan_forum]'
				WHERE id='$_POST[mesaj_no]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	// cevaplar� ta��n�yor
	$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[tasinan_forum]'
				WHERE hangi_basliktan='$_POST[mesaj_no]'";
	$sonuc = mysql_query($strSQL);



	// g�nderilen forumun cevap say�s� hesaplan�yor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[tasinan_forum]'");
	$cevap_sayi = mysql_num_rows($result);


	// g�nderilen forumun konu ve cevap say�s� artt�r�l�yor
	$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1,cevap_sayisi='$cevap_sayi'
				WHERE id='$_POST[tasinan_forum]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



	// al�nan forumun cevap say�s� hesaplan�yor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_no[hangi_forumdan]'");
	$cevap_sayi = mysql_num_rows($result);


	// al�nan forumun konu ve cevap say�s� eksiltiliyor
	$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi - 1,cevap_sayisi='$cevap_sayi'
				WHERE id='$forum_no[hangi_forumdan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	header('Location: hata.php?bilgi=9&fno1='.$forum_no['hangi_forumdan'].'&fno2='.$_POST['tasinan_forum']);
	exit();
}


elseif ($kullanici_kim['yetki'] == 3)
{
	// Konunun bulundu�u forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulundu�u forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}


	// Konunun ta��nd��� forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$_POST[tasinan_forum]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin2 = mysql_fetch_assoc($sonuc);


	// Konunun ta��nd��� forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
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


	// Konunun ta��nd��� forum �zel yetki
	$strSQL = "SELECT fno FROM $tablo_ozel_izinler
				WHERE $grupek1 kulad='$kullanici_kim[kullanici_adi]'
				AND fno='$_POST[tasinan_forum]' AND yonetme='1'";
	$kul_izin2 = mysql_query($strSQL);


	// Ta��nan forumda yetkisi yoksa
	if (!mysql_num_rows($kul_izin2))
	{
		header('Location: hata.php?hata=195');
		exit();
	}



	// Konunun bulundu�u forum �zel yetki
	$strSQL = "SELECT fno FROM $tablo_ozel_izinler
				WHERE $grupek2 kulad='$kullanici_kim[kullanici_adi]'
				AND fno='$forum_no[hangi_forumdan]' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);


	//	Y�NETME YETK�S� VARSA	//
	if (mysql_num_rows($kul_izin))
	{
		// konu ta��n�yor
		$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[tasinan_forum]'
					WHERE id='$_POST[mesaj_no]' LIMIT 1";
		$sonuc = mysql_query($strSQL);


		// cevaplar� ta��n�yor
		$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[tasinan_forum]'
					WHERE hangi_basliktan='$_POST[mesaj_no]'";
		$sonuc = mysql_query($strSQL);



		// g�nderilen forumun cevap say�s� hesaplan�yor
		$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[tasinan_forum]'");
		$cevap_sayi = mysql_num_rows($result);


		// g�nderilen forumun konu ve cevap say�s� artt�r�l�yor
		$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1,cevap_sayisi='$cevap_sayi'
					WHERE id='$_POST[tasinan_forum]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



		// al�nan forumun cevap say�s� hesaplan�yor
		$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_no[hangi_forumdan]'");
		$cevap_sayi = mysql_num_rows($result);


		// al�nan forumun konu ve cevap say�s� eksiltiliyor
		$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi - 1,cevap_sayisi='$cevap_sayi'
					WHERE id='$forum_no[hangi_forumdan]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		header('Location: hata.php?bilgi=9&fno1='.$forum_no['hangi_forumdan'].'&fno2='.$_POST['tasinan_forum']);
		exit();
	}

	//	YETK�S�Z �SE UYARILIYOR	//
	else
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}
//		YETK�S�Z �SE UYARILIYOR		//

else
{
	header('Location: hata.php?hata=3');
	exit();
}






			//	SAYFAYA �LK G�R�� KISMI	//


elseif ( ( isset($_GET['kip']) ) AND ($_GET['kip'] == 'tasi') ):


if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


if ( (!isset($_GET['mesaj_no'])) OR (is_numeric($_GET['mesaj_no']) == false) )
{
	header('Location: hata.php?hata=47');
	exit();
}

else $_GET['mesaj_no'] = zkTemizle($_GET['mesaj_no']);



//	BA�LI�IN OLDU�U FORUM ��REN�L�YOR	//

$strSQL = "SELECT id,mesaj_baslik,hangi_forumdan FROM $tablo_mesajlar WHERE id='$_GET[mesaj_no]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$forum_no = mysql_fetch_array($sonuc);


//	Y�NET�C�, FORUM YARDIMCI VEYA B�L�M�N YARDIMCISI �SE DEVAM	//

if ($kullanici_kim['yetki'] == 1);


// forum yard�mc�s�
elseif ($kullanici_kim['yetki'] == 2)
{
	// Konunun bulundu�u forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulundu�u forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}


elseif ($kullanici_kim['yetki'] == 3)
{
	// Konunun bulundu�u forum
	$strSQL = "SELECT okuma_izni,konu_acma_izni,yazma_izni FROM $tablo_forumlar
				WHERE id='$forum_no[hangi_forumdan]'";
	$sonuc = mysql_query($strSQL);
	$kul_izin = mysql_fetch_assoc($sonuc);


	// Konunun bulundu�u forum yetkilerinden biri sadece y�neticilerse veya kapal�ysa
	if ( ($kul_izin['okuma_izni'] == 1) OR ($kul_izin['konu_acma_izni'] == 1) OR ($kul_izin['yazma_izni'] == 1) OR ($kul_izin['okuma_izni'] == 5) OR ($kul_izin['konu_acma_izni'] == 5) OR ($kul_izin['yazma_izni'] == 5) )
	{
		header('Location: hata.php?hata=3');
		exit();
	}


	// Konunun bulundu�u forum �zel yetki
	if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$forum_no[hangi_forumdan]' AND yonetme='1' OR";
	else $grupek = "grup='0' AND";

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$forum_no[hangi_forumdan]' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);


	if (mysql_num_rows($kul_izin));

	//	YETK�S�Z �SE UYARILIYOR	//
	else
	{
		header('Location: hata.php?hata=3');
		exit();
	}
}

//		YETK�S�Z �SE UYARILIYOR		//
else
{
	header('Location: hata.php?hata=3');
	exit();
}


$sayfano = '11,'.$forum_no['id'];
$sayfa_adi = $forum_no['mesaj_baslik'];
include 'baslik.php';




$options_forum = '';


// forum dal� adlar� �ekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$options_forum .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlar� �ekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bak�l�yor
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