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
if (!defined('DOSYA_YONETIM_BASLIK_KOD')) include 'yonetim_baslik_kod.php';


//	KULLANICI TEMA SE��M� UYGULANIYOR	//

if( (isset($kullanici_kim['temadizini'])) AND ($kullanici_kim['temadizini'] != '') )
	$ayarlar['temadizini'] = $kullanici_kim['temadizini'];

elseif ( (!isset($ayarlar['temadizini'])) OR ($ayarlar['temadizini'] == '') )
	$ayarlar['temadizini'] = '5renkli';


//	css �ablonlar� ve resim bilgileri i�in tema.php dosyas� dahil ediliyor

include '../temalar/'.$ayarlar['temadizini'].'/yonetim_tema.php';


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


$sayfa_baslik = $ayarlar['title'];

$site_baslik = str_replace('"', '', $ayarlar['title']);

if (isset($sayfa_adi)) $sayfa_baslik .= ' - '.$sayfa_adi;
else $sayfa_adi = '';


// oturum kodu
$o2 = $kullanici_kim['kullanici_kimlik'];
$o2 = $o2[3].$o2[6].$o2[8].$o2[10].$o2[13].$o2[17].$o2[19].$o2[25].$o2[30].$o2[33];


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

if (!defined('DOSYA_TEMA_SINIF')) include '../tema_sinif.php';

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('../temalar/'.$ayarlar['temadizini'].'/yonetim/baslik.html');



//  KULLANICI G�R�� YAPMI�SA    //

if ( isset($kullanici_kim['id']) )
{
	$kullanici_adi = $kullanici_kim['kullanici_adi'];
	$ornek1->kosul('9', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('{O}' => $o2), true);

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


$dongusuz = array('{FORUM_INDEX}' => $forum_index,
'{TELIF_BILGI}' => $telif_bilgi,
'{CSS_SATIRI}' => $css_satiri,
'{SAYFA_BASLIK}' => $sayfa_baslik,
'{SITE_BASLIK}' => $site_baslik,
'{KULLANICI_ADI}' => $kullanici_adi,
'{BASLIK_TABANI}' => $basliktabani);

$ornek1->dongusuz($dongusuz);

$ornek1->tema_uygula();

unset($dongusuz);
unset($ornek1);

?>