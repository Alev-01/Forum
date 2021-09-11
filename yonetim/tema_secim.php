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
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// OTURUM KODU ÝÞLEMLERÝ  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

$tablo_portal_ayarlar = $tablo_oneki.'portal_ayarlar';


//	FORUM - PORTAL SEÇÝMÝ	//
//	FORUM - PORTAL SEÇÝMÝ	//

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'forum') ) $kip = 'forum';

elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'portal') )
{
	$strSQL = "SELECT * FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
	$pt_sonuc = @mysql_query($strSQL);
	$portal_temalari = mysql_fetch_assoc($pt_sonuc);

	$kip = 'portal';
}

else $kip = 'forum';




//	VARSAYILAN TEMAYI DEÐÝÞTÝR	//
//	VARSAYILAN TEMAYI DEÐÝÞTÝR	//

if ( (isset($_GET['temadizini'])) AND ($_GET['temadizini'] != '') )
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
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


	// forum için varsayýlan tema deðiþimi	//

	if ($kip == 'forum')
	{
		// tema bilgileri tema_bilgi.txt dosyasýndan alýnýyor

		$dosya = '../temalar/'.$_GET['temadizini'].'/tema_bilgi.txt';

		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyasý bulunamýyor!</b></font><p>';
			exit();
		}


		// forum tema sürüme bakýlýyor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// forum temasý seçeneklerde var mý bakýlýyor

		if (!preg_match("/$_GET[temadizini],/", $ayarlar['tema_secenek']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}


		// tema dizini veritabanýna giriliyor //

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$_GET[temadizini]' where etiket='temadizini' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal için varsayýlan tema deðiþimi //

	else
	{
		// tema bilgileri tema_bilgi.txt dosyasýndan alýnýyor

		$dosya = '../portal/temalar/'.$_GET['temadizini'].'/tema_bilgi.txt';

		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyasý bulunamýyor!</b></font><p>';
			exit();
		}


		// portal tema sürümüne bakýlýyor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// portal temasý seçeneklerde var mý bakýlýyor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if (!preg_match("/$_GET[temadizini],/", $portal_ayarlar['sayi']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}


		// tema dizini veritabanýna giriliyor //

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$_GET[temadizini]' where isim='temadizini' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}
}




//	KULLANICI SEÇÝMLERÝNÝ BU TEMAYA AYARLA	//
//	KULLANICI SEÇÝMLERÝNÝ BU TEMAYA AYARLA	//

elseif ( (isset($_GET['kullanici'])) AND ($_GET['kullanici'] != '') )
{
	$_GET['kullanici'] = @zkTemizle($_GET['kullanici']);

	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
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



	// forum için kullanýcý seçimi //

	if ($kip == 'forum')
	{
		// forum tema sürümüne bakýlýyor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// forum temasý seçeneklerde var mý bakýlýyor

		if (!preg_match("/$_GET[kullanici],/", $ayarlar['tema_secenek']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}



		$strSQL = "UPDATE $tablo_kullanicilar SET temadizini='$_GET[kullanici]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal için kullanýcý seçimi //

	else
	{
		// portal tema sürümüne bakýlýyor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// portal temasý seçeneklerde var mý bakýlýyor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if (!preg_match("/$_GET[kullanici],/", $portal_ayarlar['sayi']))
		{
			header('Location: ../hata.php?hata=197');
			exit();
		}



		$strSQL = "UPDATE $tablo_kullanicilar SET temadizinip='$_GET[kullanici]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}
}




//	TEMAYI SEÇENEKLERE EKLE	//
//	TEMAYI SEÇENEKLERE EKLE	//

elseif ( (isset($_GET['ekle'])) AND ($_GET['ekle'] != '') )
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
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



	// forum için seçeneklere ekle //

	if ( ($kip == 'forum') AND (!preg_match("/$_GET[ekle],/", $ayarlar['tema_secenek'])) )
	{
		$tema_ekle = @zkTemizle($_GET['ekle']);
		$tema_ekle = $ayarlar['tema_secenek'].$tema_ekle.',';


		// forum tema sürümüne bakýlýyor

		if ($_GET['surum'] != $ayarlar['surum'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// tema seçenekler arasýna ekleniyor

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$tema_ekle' where etiket='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal için seçeneklere ekle //

	elseif ( ($kip == 'portal') AND (!preg_match("/$_GET[ekle],/", $portal_temalari['sayi'])) )
	{
		$tema_ekle = @zkTemizle($_GET['ekle']);
		$tema_ekle = $portal_temalari['sayi'].$tema_ekle.',';


		// portal tema sürümüne bakýlýyor

		$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
		$portal_ayarlar = mysql_fetch_assoc($sonuc);

		if ($_GET['surum'] != $portal_ayarlar['sayi'])
		{
			header('Location: ../hata.php?hata=196');
			exit();
		}


		// tema seçenekler arasýna ekleniyor

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$tema_ekle' where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}

	header('Location: tema_secim.php');
	exit();
}




//	TEMAYI SEÇENEKLERDEN KALDIR	//
//	TEMAYI SEÇENEKLERDEN KALDIR	//

elseif ( (isset($_GET['kaldir'])) AND ($_GET['kaldir'] != '') )
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
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


	// forum için seçeneklerden kalýr //

	if ( ($kip == 'forum') AND (preg_match("/$_GET[kaldir],/", $ayarlar['tema_secenek'])) )
	{
		$_GET['kaldir'] = @zkTemizle($_GET['kaldir']);
		$tema_cikart = str_replace($_GET['kaldir'].',','',$ayarlar['tema_secenek']);


		// tema varsayýlan ise kaldýrýlýyor

		if ($ayarlar['temadizini'] == $_GET['kaldir'])
		{
			$strSQL = "UPDATE $tablo_ayarlar SET deger='5renkli' where etiket='temadizini' LIMIT 1";
			$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');
		}


		// tema seçenekler arasýndan kaldýrýlýyor

		$strSQL = "UPDATE $tablo_ayarlar SET deger='$tema_cikart' where etiket='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


		// temayý kullanan üyelerin seçimleri siliniyor

		$strSQL = "UPDATE $tablo_kullanicilar SET temadizini='' where temadizini='$_GET[kaldir]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=forum');
		exit();
	}


	// portal için seçeneklerden kaldýr //

	elseif ( ($kip == 'portal') AND (preg_match("/$_GET[kaldir],/", $portal_temalari['sayi'])) )
	{
		$_GET['kaldir'] = @zkTemizle($_GET['kaldir']);

		$tema_cikart = str_replace($_GET['kaldir'].',','',$portal_temalari['sayi']);


		// tema seçenekler arasýndan kaldýrýlýyor

		$strSQL = "UPDATE $tablo_portal_ayarlar SET sayi='$tema_cikart' where isim='tema_secenek' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


		// temayý kullanan üyelerin seçimleri siliniyor

		$strSQL = "UPDATE $tablo_kullanicilar SET temadizinip='' where temadizinip='$_GET[kaldir]'";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

		header('Location: tema_secim.php?kip=portal');
		exit();
	}

	header('Location: tema_secim.php');
	exit();
}






//	tema dosyasý açma fonksiyonu	//
function tema_dosyasi($dosya)
{
	if (!($dosya_ac = fopen($dosya,'r')))
		die ('<p><font color="red"><b>Tema Dosyasý Açýlamýyor '.$dosya.'</b></font></p>');

	$boyut = filesize($dosya);
	$dosya_metni = fread($dosya_ac,$boyut);
	fclose($dosya_ac);
	
	return $dosya_metni;
}




$sayfa_adi = 'Yönetim Tema Sayfasý';
include 'yonetim_baslik.php';



// portal sürümü alýnýyor
if ($kip == 'portal')
{
	$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='portal_surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
	$portal_ayarlar = mysql_fetch_assoc($sonuc);
}



//	SUNUCUDA YÜKLÜ TEMALAR SIRALANIYOR - BAÞI	//

if ($kip == 'forum') $dizin_adi = '../temalar/';	// forum tema dizini
else $dizin_adi = '../portal/temalar/';	// portal tema dizini

$dizin = @opendir($dizin_adi);	// dizini açýyoruz
$yanlis_tema = 'where';


//	DÝZÝNDEKÝ DOSYALAR DÖNGÜYE SOKULARAK GÖRÜNTÜLENÝYOR	//

while ( @gettype($bilgi = @readdir($dizin)) != 'boolean' )
{
	if ( (@is_dir($dizin_adi.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
	{
		// tema bilgileri tema_bilgi.txt dosyasýndan alýnýyor

		$dosya = $dizin_adi.$bilgi.'/tema_bilgi.txt';
		if (!($dosya_ac = fopen($dosya,'r')))
		{
			echo '<p><font color="red"><b>'.$dosya.' dosyasý bulunamýyor!</b></font><p>';
			continue;
		}

		$boyut = filesize($dosya);
		$dosya_metni = fread($dosya_ac,$boyut);
		fclose($dosya_ac);


		//	tema bilgileri parçalanýyor

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
			$tema_duzenleme = '<p><b>Düzenleme : &nbsp; </b>'.@zkTemizle($tema_duzenleme[1][0]);

		else $tema_duzenleme = '';


		//	veriler tema motoruna yollanýyor	//

		$tema_resim = $dizin_adi.$bilgi.'/onizleme.jpg';
		$tema_yapimci = '<a href="http://'.$tema_baglanti[1][0].'">'.$tema_yapimci[1][0].'</a>';
		$tema_demo = '<a href="http://'.$tema_demo[1][0].'">Týklayýn</a>';


		// forum için	//

		if ($kip == 'forum')
		{
			// bu temayý kullananlarýn sayýsý
		
			$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE temadizini='$bilgi'");
			$tema_kullanim = mysql_num_rows($result);


			$tema_uygulama = '<a href="tema_secim.php?kip=forum&amp;temadizini='.$bilgi.'&amp;surum='.$tema_surum[1][0].'&amp;o='.$o.'" onclick="return window.confirm(\'Forumun varsayýlan temasýný deðiþtirmek istediðinize eminmisiniz ?\')">- varsayýlan tema yap -</a>';
			$tema_kullanici = '<a href="tema_secim.php?kip=forum&amp;kullanici='.$bilgi.'&amp;surum='.$tema_surum[1][0].'&amp;o='.$o.'" onclick="return window.confirm(\'Tüm üye seçimlerini bu tema ile deðiþtirmek istediðinize eminmisiniz ?\')">- üye seçimlerini deðiþtir -</a>';


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
						'{TEMA_KULLANIM}' => $tema_kullanim.'<br>kiþi');


			// yüklü olmayan tema seçimleri için sorgu
			$yanlis_tema .= " temadizini!='$bilgi' AND";
		}



		// portal için	//

		elseif ($kip == 'portal')
		{
			// bu temayý kullananlarýn sayýsý
		
			$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE temadizinip='$bilgi'");
			$tema_kullanim = mysql_num_rows($result);
			
			$ptemas = str_replace('Portal - ', '',$tema_surum[1][0]);


			$tema_uygulama = '<a href="tema_secim.php?kip=portal&amp;temadizini='.$bilgi.'&amp;surum='.$ptemas.'&amp;o='.$o.'" onclick="return window.confirm(\'Portalýn varsayýlan temasýný deðiþtirmek istediðinize eminmisiniz ?\')">- varsayýlan tema yap -</a>';
			$tema_kullanici = '<a href="tema_secim.php?kip=portal&amp;kullanici='.$bilgi.'&amp;surum='.$ptemas.'&amp;o='.$o.'" onclick="return window.confirm(\'Tüm üye seçimlerini bu tema ile deðiþtirmek istediðinize eminmisiniz ?\')">- üye seçimlerini deðiþtir -</a>';


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
						'{TEMA_KULLANIM}' => $tema_kullanim.'<br>kiþi');


			// yüklü olmayan tema seçimleri için sorgu
			$yanlis_tema .= " temadizinip!='$bilgi' AND";
		}
	}
}


@closedir($dizin);	// dizin kapatýlýyor



	//	SUNUCUDA YÜKLÜ TEMALAR SIRALANIYOR - SONU	//



// forum için

if ($kip == 'forum')
{
	//	YÜKLÜ OLMAYAN TEMA KULLANALAR	//

	$yanlis_tema .= " temadizini!=''";

	$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar $yanlis_tema";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	if (mysql_num_rows($sonuc))
	{
		$profil_degistir = '';

		while ($uye_adi = mysql_fetch_assoc($sonuc))
			$profil_degistir .= '<a href="kullanici_degistir.php?u='.$uye_adi['id'].'">'.$uye_adi['kullanici_adi'].'</a> , ';


		$yanlis_kullananlar = '<p align="left"><u><b>Dikkat:</b></u> &nbsp; Bir temayý kaldýrmadan dosyalarýný sildiðiniz için alttaki kullanýcýlar, sunucunuzda yüklü olmayan bir tema seçmiþ görünüyor. Eðer bu kullanýcýlarýn seçimlerini düzeltmezseniz foruma giremezler.
	
		<br><br> Ýsterseniz kullanýcý adlerýný teker teker týklayarak profillerini deðiþtirebilir, ya da yukarýdaki temalardan birinin yanýndaki
		<br> "- üye seçimlerini deðiþtir -" baðlantýsýný týklayarak bu durumu düzeltebilirsiniz. </p>
	
		<b>Yanlýþ Tema Seçenler:</b> &nbsp; '.$profil_degistir;
	}


	else $yanlis_kullananlar = '';



	$sayfa_aciklama = '
<b>&nbsp; &nbsp; /phpkf/temalar/</b> dizininde yüklü olan temalar aþaðýda sýralanmaktadýr. Yeni tema yüklemek için tek yapmanýz gereken temayý klasörüyle beraber bu dizine kopyalamak ve aþaðýdan <b>- varsayýlan tema yap -</b> baðlantýsýný týklamak. 
<br><br><br>
&nbsp; &nbsp; Kullanýcýlarýn yüklediðiniz temalar arasýndan seçim yapabilmesi için, temanýn sol tarafýndaki <b>- EKLE -</b> baðlantýsý týklayýn. Seçenekler arasýndan çýkartmak içinse yine ayný yerdeki <b>-KALDIR-</b> baðlantýsýný týklayýn.

<br><br>&nbsp; &nbsp; Her temanýn sol tarafýnda görünen <b>Kullaným</b> alanýnda, o temanýn kaç kiþi tarafýndan seçildiðini görebilirsiniz. 

<br><br>&nbsp; &nbsp; Sunucunuzda yüklü olan temalarý silmek istediðinizde, bu temayý seçmiþ kiþilerin hata almamasý için önce <b>-KALDIR-</b> baðlantýsýný týklayýn. Aksi bir durum olduðunda sayfanýn en altýnda bir uyarý belirecektir.

<br><br>&nbsp; &nbsp; Ýstediðiniz temanýn yanýndaki <b>- üye seçimlerini deðiþtir -</b> baðlantýsýný týklayarak, tüm üyelerin seçimlerini bu tema ile deðiþtirebilirsiniz.';

	$kip_portal = '<a href="tema_secim.php?kip=portal" style="text-decoration: none"><b>Portal Temalarý&nbsp; &raquo;</b></a>';


	// þuan kullanýlan tema dizini alýnýyor

	$strSQL = "SELECT deger FROM $tablo_ayarlar where etiket='temadizini' LIMIT 1";

	$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');

	$suanki_tema = mysql_fetch_assoc($sonuc);


	$dongusuz = array('{KIP_FORUM}' => '&laquo; &nbsp;Forum Temalarý',
	'{KIP_PORTAL}' => $kip_portal,
	'{SAYFA_ACIKLAMA}' => $sayfa_aciklama,
	'{SUANKI_TEMA}' => $suanki_tema['deger'],
	'{YANLIS_KULLANAN}' => $yanlis_kullananlar);
}



// portal için

elseif ($kip == 'portal')
{
	//	YÜKLÜ OLMAYAN TEMA KULLANALAR	//

	$yanlis_tema .= " temadizinip!=''";

	$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar $yanlis_tema";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	if (mysql_num_rows($sonuc))
	{
		$profil_degistir = '';

		while ($uye_adi = mysql_fetch_assoc($sonuc))
			$profil_degistir .= '<a href="kullanici_degistir.php?u='.$uye_adi['id'].'">'.$uye_adi['kullanici_adi'].'</a> , ';


		$yanlis_kullananlar = '<p align="left"><u><b>Dikkat:</b></u> &nbsp; Bir temayý kaldýrmadan dosyalarýný sildiðiniz için alttaki kullanýcýlar, sunucunuzda yüklü olmayan bir tema seçmiþ görünüyor. Eðer bu kullanýcýlarýn seçimlerini düzeltmezseniz foruma giremezler.
	
		<br><br> Ýsterseniz kullanýcý adlerýný teker teker týklayarak profillerini deðiþtirebilir, ya da yukarýdaki temalardan birinin yanýndaki
		<br> "- üye seçimlerini deðiþtir -" baðlantýsýný týklayarak bu durumu düzeltebilirsiniz. </p>
	
		<b>Yanlýþ Tema Seçenler:</b> &nbsp; '.$profil_degistir;
	}


	else $yanlis_kullananlar = '';



	$sayfa_aciklama = '
&nbsp; &nbsp; Portal için; &nbsp; <b>/phpkf/portal/temalar/</b> dizininde yüklü olan temalar aþaðýda sýralanmaktadýr. Yeni tema yüklemek için tek yapmanýz gereken temayý klasörüyle beraber bu dizine kopyalamak ve aþaðýdan <b>- varsayýlan tema yap -</b> baðlantýsýný týklamak.
<br><br><br>
&nbsp; &nbsp; Kullanýcýlarýn yüklediðiniz temalar arasýndan seçim yapabilmesi için, temanýn sol tarafýndaki <b>- EKLE -</b> baðlantýsý týklayýn. Seçenekler arasýndan çýkartmak içinse yine ayný yerdeki <b>-KALDIR-</b> baðlantýsýný týklayýn.

<br><br>&nbsp; &nbsp; Her temanýn sol tarafýnda görünen <b>Kullaným</b> alanýnda, o temanýn kaç kiþi tarafýndan seçildiðini görebilirsiniz. 

<br><br>&nbsp; &nbsp; Sunucunuzda yüklü olan temalarý silmek istediðinizde, bu temayý seçmiþ kiþilerin hata almamasý için önce <b>-KALDIR-</b> baðlantýsýný týklayýn. Aksi bir durum olduðunda sayfanýn en altýnda bir uyarý belirecektir.

<br><br>&nbsp; &nbsp; Ýstediðiniz temanýn yanýndaki <b>- üye seçimlerini deðiþtir -</b> baðlantýsýný týklayarak, tüm üyelerin seçimlerini bu tema ile deðiþtirebilirsiniz.';

	$kip_forum = '<a href="tema_secim.php?kip=forum" style="text-decoration: none"><b>&laquo; &nbsp;Forum Temalarý</b></a>';


	// þuan kullanýlan tema dizini alýnýyor

	$strSQL = "SELECT sayi FROM $tablo_portal_ayarlar where isim='temadizini' LIMIT 1";

	$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');

	$suanki_tema = mysql_fetch_assoc($sonuc);


	$dongusuz = array('{KIP_FORUM}' =>$kip_forum,
	'{KIP_PORTAL}' => 'Portal Temalarý&nbsp; &raquo;',
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