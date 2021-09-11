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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

$tablo_portal_ayarlar = $tablo_oneki.'portal_ayarlar';


//	FORUM - PORTAL SE��M�	//
//	FORUM - PORTAL SE��M�	//

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'forum') ) $kip = 'forum';

elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'portal') )
{
	$strSQL = "SELECT * FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
	$pt_sonuc = @mysql_query($strSQL);
	$portal_temalari = mysql_fetch_assoc($pt_sonuc);

	$kip = 'portal';
}

else $kip = 'forum';




//	VARSAYILAN TEMAYI DE���T�R	//
//	VARSAYILAN TEMAYI DE���T�R	//

if ( (isset($_GET['temadizini'])) AND ($_GET['temadizini'] != '') )
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if (strlen($_GET['temadizini']) >  20)
	{
		header('Location: ../hata.php?hata=77');
		exit();
	}

	$_GET['temadizini'] = @zkTemizle($_GET['temadizini']);


	// forum i�in varsay�lan tema de�i�imi	//

	if ($kip == 'forum')
	{
		// tema bilgileri tema_bilgi.txt dosyas�ndan al�n�yor

		$dosya = '../temalar/'.$_GET['temadizini'].'/tema_bilgi.txt';

		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyas� bulunam�yor!</b></font><p>';
			exit();
		}


		// forum tema s�r�me bak�l�yor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// forum temas� se�eneklerde var m� bak�l�yor

		if (!preg_match("/$_GET[temadizini],/", $ayarlar['tema_secenek']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}


		// tema dizini veritaban�na giriliyor //

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$_GET[temadizini]' where etiket='temadizini' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal i�in varsay�lan tema de�i�imi //

	else
	{
		// tema bilgileri tema_bilgi.txt dosyas�ndan al�n�yor

		$dosya = '../portal/temalar/'.$_GET['temadizini'].'/tema_bilgi.txt';

		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyas� bulunam�yor!</b></font><p>';
			exit();
		}


		// portal tema s�r�m�ne bak�l�yor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// portal temas� se�eneklerde var m� bak�l�yor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if (!preg_match("/$_GET[temadizini],/", $portal_ayarlar['sayi']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}


		// tema dizini veritaban�na giriliyor //

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$_GET[temadizini]' where isim='temadizini' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}
}




//	KULLANICI SE��MLER�N� BU TEMAYA AYARLA	//
//	KULLANICI SE��MLER�N� BU TEMAYA AYARLA	//

elseif ( (isset($_GET['kullanici'])) AND ($_GET['kullanici'] != '') )
{
	$_GET['kullanici'] = @zkTemizle($_GET['kullanici']);

	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if (strlen($_GET['kullanici']) >  20)
	{
		header('Location: ../hata.php?hata=77');
		exit();
	}



	// forum i�in kullan�c� se�imi //

	if ($kip == 'forum')
	{
		// forum tema s�r�m�ne bak�l�yor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// forum temas� se�eneklerde var m� bak�l�yor

		if (!preg_match("/$_GET[kullanici],/", $ayarlar['tema_secenek']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}



		$strSQL = "UPDATE $tablo_kullanicilar SET temadizini='$_GET[kullanici]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal i�in kullan�c� se�imi //

	else
	{
		// portal tema s�r�m�ne bak�l�yor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// portal temas� se�eneklerde var m� bak�l�yor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if (!preg_match("/$_GET[kullanici],/", $portal_ayarlar['sayi']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}



		$strSQL = "UPDATE $tablo_kullanicilar SET temadizinip='$_GET[kullanici]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}
}




//	TEMAYI SE�ENEKLERE EKLE	//
//	TEMAYI SE�ENEKLERE EKLE	//

elseif ( (isset($_GET['ekle'])) AND ($_GET['ekle'] != '') )
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if (strlen($_GET['ekle']) >  20)
	{
		header('Location: ../hata.php?hata=77');
		exit();
	}



	// forum i�in se�eneklere ekle //

	if ( ($kip == 'forum') AND (!preg_match("/$_GET[ekle],/", $ayarlar['tema_secenek'])) )
	{
		$tema_ekle = @zkTemizle($_GET['ekle']);
		$tema_ekle = $ayarlar['tema_secenek'].$tema_ekle.',';


		// forum tema s�r�m�ne bak�l�yor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// tema se�enekler aras�na ekleniyor

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$tema_ekle' where etiket='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal i�in se�eneklere ekle //

	elseif ( ($kip == 'portal') AND (!preg_match("/$_GET[ekle],/", $portal_temalari['sayi'])) )
	{
		$tema_ekle = @zkTemizle($_GET['ekle']);
		$tema_ekle = $portal_temalari['sayi'].$tema_ekle.',';


		// portal tema s�r�m�ne bak�l�yor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// tema se�enekler aras�na ekleniyor

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$tema_ekle' where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}

	header('Location: tema_secim.php');
	exit();
}




//	TEMAYI SE�ENEKLERDEN KALDIR	//
//	TEMAYI SE�ENEKLERDEN KALDIR	//

elseif ( (isset($_GET['kaldir'])) AND ($_GET['kaldir'] != '') )
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if (strlen($_GET['kaldir']) >  20)
	{
		header('Location: ../hata.php?hata=77');
		exit();
	}


	if ($_GET['kaldir'] == '5renkli')
	{
		header('Location: ../hata.php?hata=150');
		exit();
	}


	// forum i�in se�eneklerden kal�r //

	if ( ($kip == 'forum') AND (preg_match("/$_GET[kaldir],/", $ayarlar['tema_secenek'])) )
	{
		$_GET['kaldir'] = @zkTemizle($_GET['kaldir']);
		$tema_cikart = str_replace($_GET['kaldir'].',','',$ayarlar['tema_secenek']);


		// tema varsay�lan ise kald�r�l�yor

		if ($ayarlar['temadizini'] == $_GET['kaldir'])
		{
			$strSQL = "UPDATE $tablo_ayarlar SET deger='5renkli' where etiket='temadizini' LIMIT 1";
			$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');
		}


		// tema se�enekler aras�ndan kald�r�l�yor

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$tema_cikart' where etiket='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');


		// temay� kullanan �yelerin se�imleri siliniyor

		$strSQL = "UPDATE $tablo_kullanicilar SET temadizini='' where temadizini='$_GET[kaldir]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal i�in se�eneklerden kald�r //

	elseif ( ($kip == 'portal') AND (preg_match("/$_GET[kaldir],/", $portal_temalari['sayi'])) )
	{
		$_GET['kaldir'] = @zkTemizle($_GET['kaldir']);

		$tema_cikart = str_replace($_GET['kaldir'].',','',$portal_temalari['sayi']);


		// tema se�enekler aras�ndan kald�r�l�yor

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$tema_cikart' where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');


		// temay� kullanan �yelerin se�imleri siliniyor

		$strSQL = "UPDATE $tablo_kullanicilar SET temadizinip='' where temadizinip='$_GET[kaldir]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}

	header('Location: tema_secim.php');
	exit();
}






//	tema dosyas� a�ma fonksiyonu	//
function tema_dosyasi($dosya)
{
	if (!($dosya_ac = fopen($dosya,'r')))
		die ('<p><font color="red"><b>Tema Dosyas� A��lam�yor '.$dosya.'</b></font></p>');

	$boyut = filesize($dosya);
	$dosya_metni = fread($dosya_ac,$boyut);
	fclose($dosya_ac);
	
	return $dosya_metni;
}




$sayfa_adi = 'Y�netim Tema Sayfas�';
include 'yonetim_baslik.php';



// portal s�r�m� al�n�yor
if ($kip == 'portal')
{
	$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
	$portal_ayarlar = mysql_fetch_assoc($sonuc);
}



//	SUNUCUDA Y�KL� TEMALAR SIRALANIYOR - BA�I	//

if ($kip == 'forum') $dizin_adi = '../temalar/';	// forum tema dizini
else $dizin_adi = '../portal/temalar/';	// portal tema dizini

$dizin = @opendir($dizin_adi);	// dizini a��yoruz
$yanlis_tema = 'where';


//	D�Z�NDEK� DOSYALAR D�NG�YE SOKULARAK G�R�NT�LEN�YOR	//

while ( @gettype($bilgi = @readdir($dizin)) != 'boolean' )
{
	if ( (@is_dir($dizin_adi.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
	{
		// tema bilgileri tema_bilgi.txt dosyas�ndan al�n�yor

		$dosya = $dizin_adi.$bilgi.'/tema_bilgi.txt';
		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyas� bulunam�yor!</b></font><p>';
			continue;
		}

		$boyut = filesize($dosya);
		$dosya_metni = fread($dosya_ac,$boyut);
		fclose($dosya_ac);


		//	tema bilgileri par�alan�yor

		preg_match('|<TEMA_ADI>(.*?)</TEMA_ADI>|si', $dosya_metni, $tema_adi, PREG_OFFSET_CAPTURE);
		preg_match('|<YAPIMCI>(.*?)</YAPIMCI>|si', $dosya_metni, $tema_yapimci, PREG_OFFSET_CAPTURE);
		preg_match('|<BAGLANTI>(.*?)</BAGLANTI>|si', $dosya_metni, $tema_baglanti, PREG_OFFSET_CAPTURE);
		preg_match('|<SURUM>(.*?)</SURUM>|si', $dosya_metni, $tema_surum, PREG_OFFSET_CAPTURE);
		preg_match('|<TARIH>(.*?)</TARIH>|si', $dosya_metni, $tema_tarih, PREG_OFFSET_CAPTURE);
		preg_match('|<DEMO>(.*?)</DEMO>|si', $dosya_metni, $tema_demo, PREG_OFFSET_CAPTURE);
		preg_match('|<ACIKLAMA>(.*?)</ACIKLAMA>|si', $dosya_metni, $tema_aciklama, PREG_OFFSET_CAPTURE);
		preg_match('|<DUZENLEME>(.*?)</DUZENLEME>|si', $dosya_metni, $tema_duzenleme, PREG_OFFSET_CAPTURE);


		// bilgiler temizleniyor

		$tema_adi[1][0] = @zkTemizle($tema_adi[1][0]);
		$tema_yapimci[1][0] = @zkTemizle($tema_yapimci[1][0]);
		$tema_baglanti[1][0] = @zkTemizle($tema_baglanti[1][0]);
		$tema_surum[1][0] = @zkTemizle($tema_surum[1][0]);
		$tema_tarih[1][0] = @zkTemizle($tema_tarih[1][0]);
		$tema_demo[1][0] = @zkTemizle($tema_demo[1][0]);
		$tema_aciklama[1][0] = @zkTemizle($tema_aciklama[1][0]);

		if (isset($tema_duzenleme[1][0])) 
			$tema_duzenleme = '<p><b>D�zenleme : &nbsp; </b>'.@zkTemizle($tema_duzenleme[1][0]);

		else $tema_duzenleme = '';


		//	veriler tema motoruna yollan�yor	//

		$tema_resim = $dizin_adi.$bilgi.'/onizleme.jpg';
		$tema_yapimci = '<a href="http://'.$tema_baglanti[1][0].'">'.$tema_yapimci[1][0].'</a>';
		$tema_demo = '<a href="http://'.$tema_demo[1][0].'">T�klay�n</a>';


		// forum i�in	//

		if ($kip == 'forum')
		{
			// bu temay� kullananlar�n say�s�
		
			$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE temadizini='$bilgi'");
			$tema_kullanim = mysql_num_rows($result);


			$tema_uygulama = '<a href="tema_secim.php?kip=forum&amp;temadizini='.$bilgi.'&amp;surum='.$tema_surum[1][0].'&amp;o='.$o.'" onclick="return window.confirm(\'Forumun varsay�lan temas�n� de�i�tirmek istedi�inize eminmisiniz ?\')">- varsay�lan tema yap -</a>';
			$tema_kullanici = '<a href="tema_secim.php?kip=forum&amp;kullanici='.$bilgi.'&amp;surum='.$tema_surum[1][0].'&amp;o='.$o.'" onclick="return window.confirm(\'T�m �ye se�imlerini bu tema ile de�i�tirmek istedi�inize eminmisiniz ?\')">- �ye se�imlerini de�i�tir -</a>';


			if (preg_match("/$bilgi,/", $ayarlar['tema_secenek']))
				$ekle_kaldir = '<a href="tema_secim.php?kip=forum&amp;kaldir='.$bilgi.'&amp;o='.$o.'">-KALDIR-</a>';

			else $ekle_kaldir = '<a href="tema_secim.php?kip=forum&amp;ekle='.$bilgi.'&amp;surum='.$tema_surum[1][0].'&amp;o='.$o.'">- EKLE -</a>';


			if ($tema_surum[1][0] != $ayarlar['surum'])
				$ftema_surum = $tema_surum[1][0].' &nbsp; <font color="#ff0000"><i>( Uyumsuz )</i></font>';
			else $ftema_surum = $tema_surum[1][0];


			$tekli1[] = array('{TEMA_RESIM}' => $tema_resim,
						'{TEMA_ADI}' => $tema_adi[1][0],
						'{TEMA_YAPIMCI}' => $tema_yapimci,
						'{TEMA_SURUM}' => $ftema_surum,
						'{TEMA_TARIH}' => $tema_tarih[1][0],
						'{TEMA_DEMO}' => $tema_demo,
						'{TEMA_ACIKLAMA}' => $tema_aciklama[1][0].$tema_duzenleme,
						'{TEMA_UYGULAMA}' => $tema_uygulama,
						'{EKLE_KALDIR}' => $ekle_kaldir,
						'{TEMA_KULLANICI}' => $tema_kullanici,
						'{TEMA_KULLANIM}' => $tema_kullanim.'<br>ki�i');


			// y�kl� olmayan tema se�imleri i�in sorgu
			$yanlis_tema .= " temadizini!='$bilgi' AND";
		}



		// portal i�in	//

		elseif ($kip == 'portal')
		{
			// bu temay� kullananlar�n say�s�
		
			$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE temadizinip='$bilgi'");
			$tema_kullanim = mysql_num_rows($result);
			
			$ptemas = str_replace('Portal - ', '',$tema_surum[1][0]);


			$tema_uygulama = '<a href="tema_secim.php?kip=portal&amp;temadizini='.$bilgi.'&amp;surum='.$ptemas.'&amp;o='.$o.'" onclick="return window.confirm(\'Portal�n varsay�lan temas�n� de�i�tirmek istedi�inize eminmisiniz ?\')">- varsay�lan tema yap -</a>';
			$tema_kullanici = '<a href="tema_secim.php?kip=portal&amp;kullanici='.$bilgi.'&amp;surum='.$ptemas.'&amp;o='.$o.'" onclick="return window.confirm(\'T�m �ye se�imlerini bu tema ile de�i�tirmek istedi�inize eminmisiniz ?\')">- �ye se�imlerini de�i�tir -</a>';


			if (preg_match("/$bilgi,/", $portal_temalari['sayi']))
				$ekle_kaldir = '<a href="tema_secim.php?kip=portal&amp;kaldir='.$bilgi.'&amp;o='.$o.'">-KALDIR-</a>';

			else $ekle_kaldir = '<a href="tema_secim.php?kip=portal&amp;ekle='.$bilgi.'&amp;surum='.$ptemas.'&amp;o='.$o.'">- EKLE -</a>';


			if ($ptemas != $portal_ayarlar['sayi'])
				$ptema_surum = $tema_surum[1][0].' &nbsp; <font color="#ff0000"><i>( Uyumsuz )</i></font>';
			else $ptema_surum = $tema_surum[1][0];


			$tekli1[] = array('{TEMA_RESIM}' => $tema_resim,
						'{TEMA_ADI}' => $tema_adi[1][0],
						'{TEMA_YAPIMCI}' => $tema_yapimci,
						'{TEMA_SURUM}' => $ptema_surum,
						'{TEMA_TARIH}' => $tema_tarih[1][0],
						'{TEMA_DEMO}' => $tema_demo,
						'{TEMA_ACIKLAMA}' => $tema_aciklama[1][0].$tema_duzenleme,
						'{TEMA_UYGULAMA}' => $tema_uygulama,
						'{EKLE_KALDIR}' => $ekle_kaldir,
						'{TEMA_KULLANICI}' => $tema_kullanici,
						'{TEMA_KULLANIM}' => $tema_kullanim.'<br>ki�i');


			// y�kl� olmayan tema se�imleri i�in sorgu
			$yanlis_tema .= " temadizinip!='$bilgi' AND";
		}
	}
}


@closedir($dizin);	// dizin kapat�l�yor



	//	SUNUCUDA Y�KL� TEMALAR SIRALANIYOR - SONU	//



// forum i�in

if ($kip == 'forum')
{
	//	Y�KL� OLMAYAN TEMA KULLANALAR	//

	$yanlis_tema .= " temadizini!=''";

	$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar $yanlis_tema";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	if (mysql_num_rows($sonuc))
	{
		$profil_degistir = '';

		while ($uye_adi = mysql_fetch_assoc($sonuc))
			$profil_degistir .= '<a href="kullanici_degistir.php?u='.$uye_adi['id'].'">'.$uye_adi['kullanici_adi'].'</a> , ';


		$yanlis_kullananlar = '<p align="left"><u><b>Dikkat:</b></u> &nbsp; Bir temay� kald�rmadan dosyalar�n� sildi�iniz i�in alttaki kullan�c�lar, sunucunuzda y�kl� olmayan bir tema se�mi� g�r�n�yor. E�er bu kullan�c�lar�n se�imlerini d�zeltmezseniz foruma giremezler.
	
		<br><br> �sterseniz kullan�c� adler�n� teker teker t�klayarak profillerini de�i�tirebilir, ya da yukar�daki temalardan birinin yan�ndaki
		<br> "- �ye se�imlerini de�i�tir -" ba�lant�s�n� t�klayarak bu durumu d�zeltebilirsiniz. </p>
	
		<b>Yanl�� Tema Se�enler:</b> &nbsp; '.$profil_degistir;
	}


	else $yanlis_kullananlar = '';



	$sayfa_aciklama = '
<b>&nbsp; &nbsp; /phpkf/temalar/</b> dizininde y�kl� olan temalar a�a��da s�ralanmaktad�r. Yeni tema y�klemek i�in tek yapman�z gereken temay� klas�r�yle beraber bu dizine kopyalamak ve a�a��dan <b>- varsay�lan tema yap -</b> ba�lant�s�n� t�klamak. 
<br><br><br>
&nbsp; &nbsp; Kullan�c�lar�n y�kledi�iniz temalar aras�ndan se�im yapabilmesi i�in, teman�n sol taraf�ndaki <b>- EKLE -</b> ba�lant�s� t�klay�n. Se�enekler aras�ndan ��kartmak i�inse yine ayn� yerdeki <b>-KALDIR-</b> ba�lant�s�n� t�klay�n.

<br><br>&nbsp; &nbsp; Her teman�n sol taraf�nda g�r�nen <b>Kullan�m</b> alan�nda, o teman�n ka� ki�i taraf�ndan se�ildi�ini g�rebilirsiniz. 

<br><br>&nbsp; &nbsp; Sunucunuzda y�kl� olan temalar� silmek istedi�inizde, bu temay� se�mi� ki�ilerin hata almamas� i�in �nce <b>-KALDIR-</b> ba�lant�s�n� t�klay�n. Aksi bir durum oldu�unda sayfan�n en alt�nda bir uyar� belirecektir.

<br><br>&nbsp; &nbsp; �stedi�iniz teman�n yan�ndaki <b>- �ye se�imlerini de�i�tir -</b> ba�lant�s�n� t�klayarak, t�m �yelerin se�imlerini bu tema ile de�i�tirebilirsiniz.';

	$kip_portal = '<a href="tema_secim.php?kip=portal" style="text-decoration: none"><b>Portal Temalar�&nbsp; &raquo;</b></a>';


	// �uan kullan�lan tema dizini al�n�yor

	$strSQL = "SELECT deger FROM $tablo_ayarlar where etiket='temadizini' LIMIT 1";

	$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');

	$suanki_tema = mysql_fetch_assoc($sonuc);


	$dongusuz = array('{KIP_FORUM}' => '&laquo; &nbsp;Forum Temalar�',
	'{KIP_PORTAL}' => $kip_portal,
	'{SAYFA_ACIKLAMA}' => $sayfa_aciklama,
	'{SUANKI_TEMA}' => $suanki_tema['deger'],
	'{YANLIS_KULLANAN}' => $yanlis_kullananlar);
}



// portal i�in

elseif ($kip == 'portal')
{
	//	Y�KL� OLMAYAN TEMA KULLANALAR	//

	$yanlis_tema .= " temadizinip!=''";

	$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar $yanlis_tema";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	if (mysql_num_rows($sonuc))
	{
		$profil_degistir = '';

		while ($uye_adi = mysql_fetch_assoc($sonuc))
			$profil_degistir .= '<a href="kullanici_degistir.php?u='.$uye_adi['id'].'">'.$uye_adi['kullanici_adi'].'</a> , ';


		$yanlis_kullananlar = '<p align="left"><u><b>Dikkat:</b></u> &nbsp; Bir temay� kald�rmadan dosyalar�n� sildi�iniz i�in alttaki kullan�c�lar, sunucunuzda y�kl� olmayan bir tema se�mi� g�r�n�yor. E�er bu kullan�c�lar�n se�imlerini d�zeltmezseniz foruma giremezler.
	
		<br><br> �sterseniz kullan�c� adler�n� teker teker t�klayarak profillerini de�i�tirebilir, ya da yukar�daki temalardan birinin yan�ndaki
		<br> "- �ye se�imlerini de�i�tir -" ba�lant�s�n� t�klayarak bu durumu d�zeltebilirsiniz. </p>
	
		<b>Yanl�� Tema Se�enler:</b> &nbsp; '.$profil_degistir;
	}


	else $yanlis_kullananlar = '';



	$sayfa_aciklama = '
&nbsp; &nbsp; Portal i�in; &nbsp; <b>/phpkf/portal/temalar/</b> dizininde y�kl� olan temalar a�a��da s�ralanmaktad�r. Yeni tema y�klemek i�in tek yapman�z gereken temay� klas�r�yle beraber bu dizine kopyalamak ve a�a��dan <b>- varsay�lan tema yap -</b> ba�lant�s�n� t�klamak.
<br><br><br>
&nbsp; &nbsp; Kullan�c�lar�n y�kledi�iniz temalar aras�ndan se�im yapabilmesi i�in, teman�n sol taraf�ndaki <b>- EKLE -</b> ba�lant�s� t�klay�n. Se�enekler aras�ndan ��kartmak i�inse yine ayn� yerdeki <b>-KALDIR-</b> ba�lant�s�n� t�klay�n.

<br><br>&nbsp; &nbsp; Her teman�n sol taraf�nda g�r�nen <b>Kullan�m</b> alan�nda, o teman�n ka� ki�i taraf�ndan se�ildi�ini g�rebilirsiniz. 

<br><br>&nbsp; &nbsp; Sunucunuzda y�kl� olan temalar� silmek istedi�inizde, bu temay� se�mi� ki�ilerin hata almamas� i�in �nce <b>-KALDIR-</b> ba�lant�s�n� t�klay�n. Aksi bir durum oldu�unda sayfan�n en alt�nda bir uyar� belirecektir.

<br><br>&nbsp; &nbsp; �stedi�iniz teman�n yan�ndaki <b>- �ye se�imlerini de�i�tir -</b> ba�lant�s�n� t�klayarak, t�m �yelerin se�imlerini bu tema ile de�i�tirebilirsiniz.';

	$kip_forum = '<a href="tema_secim.php?kip=forum" style="text-decoration: none"><b>&laquo; &nbsp;Forum Temalar�</b></a>';


	// �uan kullan�lan tema dizini al�n�yor

	$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='temadizini' LIMIT 1";

	$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');

	$suanki_tema = mysql_fetch_assoc($sonuc);


	$dongusuz = array('{KIP_FORUM}' =>$kip_forum,
	'{KIP_PORTAL}' => 'Portal Temalar�&nbsp; &raquo;',
	'{SAYFA_ACIKLAMA}' => $sayfa_aciklama,
	'{SUANKI_TEMA}' => $suanki_tema['sayi'],
	'{YANLIS_KULLANAN}' => $yanlis_kullananlar);
}




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/tema_secim.html');

$ornek1->dongusuz($dongusuz);
$ornek1->tekli_dongu('1',$tekli1);


if ($portal_kullan == 1)
$ornek1->kosul('1', array('' => ''), true);

else $ornek1->kosul('1', array('' => ''), false);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>