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


if (!defined('PHPKF_ICINDEN')) exit();
if (!defined('DOSYA_BASLIK_KOD')) include 'baslik_kod.php';


//	KULLANICI TEMA SE��M� UYGULANIYOR	//

if( (isset($kullanici_kim['temadizini'])) AND ($kullanici_kim['temadizini'] != '') )
	$ayarlar['temadizini'] = $kullanici_kim['temadizini'];

elseif ( (!isset($ayarlar['temadizini'])) OR ($ayarlar['temadizini'] == '') )
	$ayarlar['temadizini'] = '5renkli';


//	css �ablonlar� ve resim bilgileri i�in tema.php dosyas� dahil ediliyor

include 'temalar/'.$ayarlar['temadizini'].'/tema.php';


//	META ET�KETLER�		//

header('Content-Type:text/html; charset=windows-1254');
header('Content-Type:text/html; charset=iso-8859-9');
header('Content-Language: tr');


$telif_bilgi = '<!--
 +===========================================================+
 |                  php Kolay Forum (phpKF)                  |
 +===========================================================+
 |                                                           |
 |            Telif - Copyright (c) 2007 - 2013              |
 |       http://www.phpkf.com   -   phpkf @ phpkf.com        |
 |        T�m haklar� sakl�d�r - All Rights Reserved         |
 |                                                           |
 +===========================================================+
-->';



if (isset($sayfa_adi)) $sayfa_adi = stripslashes($sayfa_adi);
else $sayfa_adi = '';

$sayfa_baslik = $sayfa_adi.' - '.$ayarlar['title'];

$site_baslik = str_replace('"', '', $ayarlar['title']);


//	RSS BA�LANTILARI	//

if ( (isset($_GET['f'])) AND ($_GET['f'] != '') AND (is_numeric($_GET['f']) == true) )
{
	$rss_satiri = '<link rel="alternate" type="application/rss+xml" title="phpKF Anasayfa - Forum '.$_GET['f'].'" href="rss.php?f='.$_GET['f'].'">';
	$rssadres = 'rss.php?f='.$_GET['f'];
}

elseif (isset($mesaj_satir['hangi_forumdan']))
{
	$rss_satiri = '<link rel="alternate" type="application/rss+xml" title="phpKF Anasayfa - Forum '.$mesaj_satir['hangi_forumdan'].'" href="rss.php?f='.$mesaj_satir['hangi_forumdan'].'">';
	$rssadres = 'rss.php?f='.$mesaj_satir['hangi_forumdan'];
}

else
{
	$rss_satiri = '<link rel="alternate" type="application/rss+xml" title="phpKF Anasayfa" href="rss.php">';
	$rssadres = 'rss.php';
}


// DUYURU B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT * FROM $tablo_duyurular WHERE fno!='por' AND fno!='ozel' ORDER BY fno='tum' desc";
$duyuru_sonuc = mysql_query($strSQL) or die ('<h2>duyuru sorgu ba�ar�s�z</h2>');


// DUYURU VARSA D�NG�YE G�R�L�YOR //

if (mysql_num_rows($duyuru_sonuc)) 
{
	while ($duyurular = mysql_fetch_assoc($duyuru_sonuc))
	{
		if ($duyurular['fno'] == 'tum') $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);

		if (isset($kullanici_kim['id']))
		{
			if ($duyurular['fno'] == 'uye') $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);

			if (($duyurular['fno'] == 'byar') AND ($kullanici_kim['yetki'] == '3')) $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);

			if (($duyurular['fno'] == 'fyar') AND ($kullanici_kim['yetki'] == '2')) $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);

			if (($duyurular['fno'] == 'yon') AND ($kullanici_kim['yetki'] == '1')) $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);
		}

		else {if ($duyurular['fno'] == 'mis') $tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);}

		if ( (isset($_GET['f']) AND ($duyurular['fno'] == $_GET['f'])) OR (isset($mesaj_satir['hangi_forumdan']) AND ($duyurular['fno'] == $mesaj_satir['hangi_forumdan'])) )
			$tekli1[] = array('{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);
	}
}



// oturum kodu
$o = $kullanici_kim['kullanici_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


$javascript_kodu = '<script type="text/javascript">
<!-- //
setInterval(\'ziplama()\',500);
function ziplama(){
if (!document.all) return;
else{
var zipla = document.getElementById(\'zipla\');
zipla.style.visibility=(zipla.style.visibility==\'visible\')?\'hidden\':\'visible\';}}
//  -->
</script>';



//	TEMA UYGULANIYOR	//

if (!defined('DOSYA_TEMA_SINIF')) include 'tema_sinif.php';

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/baslik.html');



//  KULLANICI G�R�� YAPMI�SA    //

if ( isset($kullanici_kim['id']) )
{
	$kullanici_adi = $kullanici_kim['kullanici_adi'];
	$ornek1->kosul('9', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('{O}' => $o), true);

	if ($ayarlar['o_ileti'] == 1)
	{
		if ($kullanici_kim['okunmamis_oi'])
		{
			$ornek1->kosul('3', array('' => ''), false);
			$ornek1->kosul('4', array('{OKUNMAMIS_OI}' => $kullanici_kim['okunmamis_oi'],
			'{JAVASCRIPT_KODU}' => $javascript_kodu), true);
		}
		else $ornek1->kosul('4', array('' => ''), false);
	}

	else
	{
		$ornek1->kosul('3', array('' => ''), false);
		$ornek1->kosul('4', array('' => ''), false);
	}
}


//  KULLANICI G�R�� YAPMAMI�SA  //

else
{
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('3', array('' => ''), false);
	$ornek1->kosul('9', array('' => ''), true);
	$kullanici_adi = '';
}


	//	DUYURU TABLOSU AYARLARI	//

// duyuru varsa ko�ul 5 alan� tekli d�ng�ye sokuluyor ve ko�ul 6 alan� gizleniyor
if (isset($tekli1))
{
	$ornek1->kosul('6', array('' => ''), false);
	$ornek1->tekli_dongu('1',$tekli1);
	unset($tekli1);
}

// duyuru yoksa ko�ul 5 alan� gizleniyor
else $ornek1->kosul('5', array('' => ''), false);


// portal kullan�l�yorsa portal ba�lant�s� ekleniyor

if ($portal_kullan == 1)
{
	$ornek1->kosul('7', array('' => ''), false);
	$ornek1->kosul('8', array('{FORUM_INDEX}' => $forum_index, '{PORTAL_INDEX}' => $portal_index), true);
}

else
{
	$ornek1->kosul('8', array('' => ''), false);
	$ornek1->kosul('7', array('{FORUM_INDEX}' => $forum_index), true);
}


$dongusuz = array('{TELIF_BILGI}' => $telif_bilgi,
'{CSS_SATIRI}' => $css_satiri,
'{SAYFA_BASLIK}' => $sayfa_baslik,
'{SITE_BASLIK}' => $site_baslik,
'{KULLANICI_ADI}' => $kullanici_adi,
'{RSS_SATIRI}' => $rss_satiri,
'{BASLIK_TABANI}' => $basliktabani);

$ornek1->dongusuz($dongusuz);

$ornek1->tema_uygula();

unset($dongusuz);
unset($tekli1);
unset($ornek1);

?>