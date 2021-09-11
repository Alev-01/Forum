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


if ( (isset($_GET['fno'])) AND (isset($_GET['kip'])) OR (isset($_POST['fno'])) AND (isset($_POST['kip'])) ):

@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


if ( isset($_GET['mesaj_no']) ) $mesaj_no = zkTemizle($_GET['mesaj_no']);
if ( isset($_POST['mesaj_no']) ) $mesaj_no = zkTemizle($_POST['mesaj_no']);

if ( isset($_GET['cevap_no']) ) $cevap_no = zkTemizle($_GET['cevap_no']);
else $cevap_no = 0;

if ( isset($_POST['cevap_no']) ) $cevap_no = zkTemizle($_POST['cevap_no']);

if ( isset($_GET['kip']) ) $kip = $_GET['kip'];
if ( isset($_POST['kip']) ) $kip = $_POST['kip'];

if ( isset($_GET['fsayfa']) ) $fsayfa = $_GET['fsayfa'];
elseif ( isset($_POST['fsayfa']) ) $fsayfa = $_POST['fsayfa'];
else $fsayfa = 0;

if ( isset($_GET['sayfa']) ) $sayfa = $_GET['sayfa'];
elseif ( isset($_POST['sayfa']) ) $sayfa = $_POST['sayfa'];
else $sayfa = 0;



//	DEÐÝÞTÝRÝLEN BAÞLIKSA	//

if ($kip == 'mesaj')
{
	$strSQL = "SELECT id,yazan,mesaj_baslik,mesaj_icerik,bbcode_kullan,hangi_forumdan,ust_konu,kilitli,ifade
				FROM $tablo_mesajlar WHERE id='$mesaj_no' AND silinmis='0' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	// konu yoksa uyarý ver //
	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=47');
		exit();
	}


	$mesaj_degistir_satir = mysql_fetch_assoc($sonuc);
	$fno = $mesaj_degistir_satir['hangi_forumdan'];
	$yazan = $mesaj_degistir_satir['yazan'];
	$baslik = $mesaj_degistir_satir['mesaj_baslik'];
	$cevap_baslik = '';


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$fno' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	ÝZÝNLERDEN BÝRÝ SADECE YÖNETÝCÝLER ÝÇÝNSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=52');
			exit();
		}
	}


	// konu kilitli ise deðiþtirilemez uyarýsý veriliyor //
	if ( ($mesaj_degistir_satir['kilitli'] == 1) AND (($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2)) )
	{
		header('Location: hata.php?hata=50');
		exit();
	}
}


//	DEÐÝÞTÝRÝLEN CEVAPSA	//

if ($kip == 'cevap')
{
	$strSQL = "SELECT id,cevap_yazan,cevap_baslik,cevap_icerik,bbcode_kullan,hangi_forumdan,hangi_basliktan,ifade
				FROM $tablo_cevaplar WHERE id='$cevap_no' AND silinmis='0' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	// cevap yoksa uyarý ver //
	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=55');
		exit();
	}


	$cevap_degistir_satir = mysql_fetch_assoc($sonuc);
	$fno = $cevap_degistir_satir['hangi_forumdan'];
	$yazan = $cevap_degistir_satir['cevap_yazan'];


	// konu kilitli ise deðiþtirilemez uyarýsý veriliyor //

	$strSQL = "SELECT kilitli,mesaj_baslik FROM $tablo_mesajlar WHERE id='$cevap_degistir_satir[hangi_basliktan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$konu_kilitlimi = mysql_fetch_assoc($sonuc);


	if ( ($konu_kilitlimi['kilitli'] == 1) AND (($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2)) )
	{
		header('Location: hata.php?hata=51');
		exit();
	}


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$fno' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	ÝZÝNLERDEN BÝRÝ SADECE YÖNETÝCÝLER ÝÇÝNSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=52');
			exit();
		}
	}


	$baslik = $konu_kilitlimi['mesaj_baslik'];
	$cevap_baslik = '&nbsp;&gt;&gt;&nbsp; '.$cevap_degistir_satir['cevap_baslik'];
}


//	DEÐÝÞTÝRMEYE YETKÝLÝ OLUP OLMADIÐINA BAKILIYOR	- BAÞI	//

//	YARDIMCI ÝSE	//
if ($kullanici_kim['yetki'] == 3)
{
	//	KENDÝ YAZISI DEÐÝLSE	//
	if ( ($yazan != $kullanici_kim['kullanici_adi']) )
	{
		if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$fno' AND yonetme='1' OR";
		else $grupek = "grup='0' AND";

		$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$fno' AND yonetme='1'";
		$kul_izin = mysql_query($strSQL);

		if ( !mysql_num_rows($kul_izin) )
		{
			header('Location: hata.php?hata=52');
			exit();
		}
	}
}

//	YAZAN VEYA YÖNETÝCÝ ÝSE	//
elseif ( ($yazan == $kullanici_kim['kullanici_adi']) OR ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) );

//	HÝÇBÝRÝ DEÐÝLSE	//
else
{
	header('Location: hata.php?hata=52');
	exit();
}

//	DEÐÝÞTÝRMEYE YETKÝLÝ OLUP OLMADIÐINA BAKILIYOR	- SONU	//





if (isset($_POST['mesaj_onizleme']))
{
	if ($kip == 'mesaj')
	{
		$sayfano = '13,'.$mesaj_no;
		$sayfa_adi = 'Konu Deðiþtirme Önizlemesi: '.$baslik;
	}
	else
	{
		$sayfano = '14,'.$mesaj_no.','.$cevap_degistir_satir['id'];
		$sayfa_adi = 'Cevap Deðiþtirme Önizlemesi: '.$baslik;
	}
}

else
{
	if ($kip == 'mesaj')
	{
		$sayfano = '15,'.$mesaj_no;
		$sayfa_adi = 'Konu Deðiþtirme: '.$baslik;
	}
	else
	{
		$sayfano = '16,'.$mesaj_no.','.$cevap_degistir_satir['id'];
		$sayfa_adi = 'Cevap Deðiþtirme: '.$baslik;
	}
}

include 'baslik.php';



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/mesaj_yaz.html');




// üst forum - alt forum baþlýðý
if ($forum_satir['alt_forum'] != '0')
{
	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$forum_satir[alt_forum]' LIMIT 1";
	$sonuc_ust = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_satir_ust = mysql_fetch_assoc($sonuc_ust);

	$ust_forum_baslik = '<a href="forum.php?f='.$forum_satir_ust['id'].'">'.$forum_satir_ust['forum_baslik'].'</a> &nbsp;&raquo;&nbsp; ';

	$alt_forum_baslik = '<a href="forum.php?f='.$fno.'&amp;fs='.$fsayfa.'">'.$forum_satir['forum_baslik'].'</a><br>';
}

else
{
	$ust_forum_baslik = '<a href="forum.php?f='.$fno.'&amp;fs='.$fsayfa.'">'.$forum_satir['forum_baslik'].'</a>';
	$alt_forum_baslik = '<br>';
}




$sayfa_baslik = '<a href="konu.php?k='.$mesaj_no.'&amp;fs='.$fsayfa.'&amp;ks='.$sayfa.'">'.$baslik.'</a>';






			//		ÖNÝZLEME TABLOSU BAÞI		//


if ( isset($_POST['mesaj_onizleme']) ):

	if ( empty($_POST['mesaj_icerik']) ):

		$javascript_kapali = '<center><br><b><font size="3" color="red">Önizleme özelliði için taraycýnýzýn java özelliðinin açýk olmasý gereklidir.</b></center><br>';


	else:

$javascript_kapali = '';


// MESAJ SAHÝBÝNÝN PROFÝLÝ ÇEKÝLÝYOR //

$strSQL = "SELECT id,kullanici_adi,gercek_ad,resim,katilim_tarihi,mesaj_sayisi,sehir_goster,sehir,web,imza,yetki,ozel_ad
FROM $tablo_kullanicilar WHERE kullanici_adi='$yazan' LIMIT 1";

$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$mesaj_sahibi = mysql_fetch_assoc($sonuc);


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

//	magic_quotes_gpc açýksa	//
if (get_magic_quotes_gpc(1))
{
	$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),3);
	$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),4);
}

//	magic_quotes_gpc kapalýysa	//
else
{
	$_POST['mesaj_baslik'] = @ileti_yolla($_POST['mesaj_baslik'],3);
	$_POST['mesaj_icerik'] = @ileti_yolla($_POST['mesaj_icerik'],4);
}



$onizleme_uye_adi = '<a href="profil.php?kim='.$mesaj_sahibi['kullanici_adi'].'">'.$mesaj_sahibi['kullanici_adi'].'</a>';


if (!empty($mesaj_sahibi['ozel_ad']))
	$onizleme_yetki = '<font class="ozel_ad"><u>'.$mesaj_sahibi['ozel_ad'].'</u></font>';

elseif ($mesaj_sahibi['id'] == 1)
	$onizleme_yetki = '<font class="kurucu"><u>'.$ayarlar['kurucu'].'</u></font>';

elseif ( $mesaj_sahibi['yetki'] == 1 )
	$onizleme_yetki = '<font class="yonetici"><u>'.$ayarlar['yonetici'].'</u></font>';

elseif ( $mesaj_sahibi['yetki'] == 2 )
	$onizleme_yetki = '<font class="yardimci"><u>'.$ayarlar['yardimci'].'</u></font>';

elseif ( $mesaj_sahibi['yetki'] == 3 )
	$onizleme_yetki = '<font class="blm_yrd"><u>'.$ayarlar['blm_yrd'].'</u></font>';

else $onizleme_yetki = '';


if ($mesaj_sahibi['resim'])
	$onizleme_resim = '<img src="'.$mesaj_sahibi['resim'].'" alt="Kulanýcý Resmi">';

else $onizleme_resim = '';



$onizleme_katilim = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $mesaj_sahibi['katilim_tarihi']);


if ($mesaj_sahibi['sehir_goster'] == 1)
	$onizleme_sehir = $mesaj_sahibi['sehir'];

else $onizleme_sehir = '';


$onizleme_eposta = '<a title="Forum üzerinden e-posta gönder" href="eposta.php?kim='.$mesaj_sahibi['kullanici_adi'].'">';


if ($mesaj_sahibi['web'])
$onizleme_web = '<br><a href="'.$mesaj_sahibi['web'].'" target="_blank">Web Adresi</a>';

else $onizleme_web = '';


$onizleme_io = '<a href="oi_yaz.php?ozel_kime='.$mesaj_sahibi['kullanici_adi'].'">';

$onizleme_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, time());




	//	BAÞLIK ÝÇERÝÐÝ YAZDIRILIYOR	//
	//	VARSA ÝMZA BÝLGÝLERÝ YAZDIRILIYOR	//


$onizleme_mesaj = $_POST['mesaj_icerik'];

if ($_POST['ifade'] == 1) $onizleme_mesaj = ifadeler($onizleme_mesaj);

if ( ($_POST['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
	$onizleme_mesaj = bbcode_acik($onizleme_mesaj,1);

else $onizleme_mesaj = bbcode_kapali($onizleme_mesaj);

if ( (isset($mesaj_sahibi['imza'])) AND ($mesaj_sahibi['imza'] != '') )
{
	if ($ayarlar['bbcode'] == 1)
	$onizleme_imza = bbcode_acik(ifadeler($mesaj_sahibi['imza']),0);

	else $onizleme_imza = bbcode_kapali(ifadeler($mesaj_sahibi['imza']));
}

else $onizleme_imza = '';



//	veriler tema motoruna yollanýyor	//

$ornek1->kosul('1', array('{ONIZLEME_BASLIK}' => $_POST['mesaj_baslik'],
'{ONIZLEME_UYE_ADI}' => $onizleme_uye_adi,
'{ONIZLEME_GERCEK_AD}' => $mesaj_sahibi['gercek_ad'],
'{ONIZLEME_YETKISI}' => $onizleme_yetki,
'{ONIZLEME_RESIM}' => $onizleme_resim,
'{ONIZLEME_KATILIM}' => $onizleme_katilim,
'{ONIZLEME_MESAJ_SAYI}' => NumaraBicim($mesaj_sahibi['mesaj_sayisi']),
'{ONIZLEME_SEHIR}' => $onizleme_sehir,
'{ONIZLEME_EPOSTA}' => $onizleme_eposta,
'{ONIZLEME_WEB}' => $onizleme_web,
'{ONIZLEME_OI}' => $onizleme_io,
'{ONIZLEME_TARIH}' => $onizleme_tarih,
'{ONIZLEME_MESAJ}' => $onizleme_mesaj,
'{ONIZLEME_IMZA}' => $onizleme_imza), true);


endif;

else: $ornek1->kosul('1', array('' => ''), false);

endif;



						//	ÖNÝZLEME TABLOSU SONU	//






if (isset($_POST['mesaj_baslik']))
	$form_baslik = $_POST['mesaj_baslik'];

elseif (isset($mesaj_degistir_satir['mesaj_baslik']))
	$form_baslik = $mesaj_degistir_satir['mesaj_baslik'];

elseif (isset($cevap_degistir_satir['cevap_baslik']))
	$form_baslik = $cevap_degistir_satir['cevap_baslik'];



if (isset($_POST['mesaj_icerik']))
	$form_icerik = $_POST['mesaj_icerik'];

elseif (isset($mesaj_degistir_satir['mesaj_icerik']))
	$form_icerik = $mesaj_degistir_satir['mesaj_icerik'];

elseif (isset($cevap_degistir_satir['cevap_icerik']))
	$form_icerik = $cevap_degistir_satir['cevap_icerik'];



//  BBCODE AÇMA - KAPATMA    //

$form_ozellik = '';

if ($ayarlar['bbcode'] == 1)
{
	$form_ozellik .= '<label style="cursor: pointer;"><input type="checkbox" name="bbcode_kullan" ';

	if ( (isset($_POST['bbcode_kullan'])) AND ($_POST['bbcode_kullan'] == 1) )
		$form_ozellik .= 'checked="checked">Bu iletide BBCode kullan</label>';

	elseif ( (isset($_POST['bbcode_kullan'])) AND ($_POST['bbcode_kullan'] != 1) )
		$form_ozellik .= '>Bu iletide BBCode kullan</label>';

	else
	{
		if ( (isset($mesaj_degistir_satir['bbcode_kullan'])) AND ($mesaj_degistir_satir['bbcode_kullan'] == 1) )
			$form_ozellik .= 'checked="checked">Bu iletide BBCode kullan</label>';

		elseif ( (isset($cevap_degistir_satir['bbcode_kullan'])) AND ($cevap_degistir_satir['bbcode_kullan'] == 1) )
			$form_ozellik .= 'checked="checked">Bu iletide BBCode kullan</label>';

		else $form_ozellik .= '>Bu iletide BBCode kullan</label>';
	}
}

// bbcode kapalý ise
else $form_ozellik .= '<input type="hidden" name="bbcode_kullan">&nbsp;BBCode Kapalý';




//  ÝFADE AÇMA - KAPATMA    //

$form_ozellik .= '<br><label style="cursor: pointer;"><input type="checkbox" name="ifade" ';

if ( (isset($_POST['ifade'])) AND ($_POST['ifade'] == 1) )
    $form_ozellik .= 'checked="checked">Bu iletide ifade kullan</label>';

elseif ( (isset($_POST['ifade'])) AND ($_POST['ifade'] != 1) )
    $form_ozellik .= '>Bu iletide ifade kullan</label>';

else
{
    if ( (isset($mesaj_degistir_satir['ifade'])) AND ($mesaj_degistir_satir['ifade'] == 1) )
        $form_ozellik .= 'checked="checked">Bu iletide ifade kullan</label>';

    elseif ( (isset($cevap_degistir_satir['ifade'])) AND ($cevap_degistir_satir['ifade'] == 1) )
        $form_ozellik .= 'checked="checked">Bu iletide ifade kullan</label>';

    else $form_ozellik .= '>Bu iletide ifade kullan</label>';
}




//	ÜST KONU SEÇENEÐÝ KULLANICIYA GÖRE GÖSTERÝLÝYOR	- BAÞI //

//	YÖNETÝCÝ ÝSE	//

if ( ($kip == 'mesaj') AND (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2)) )
{
	$form_ozellik .= '<br><label style="cursor: pointer;">
	<input type="checkbox" name="ust_konu" ';

	if ( (isset($_POST['ust_konu'])) AND $_POST['ust_konu'] == 1)
		$form_ozellik .= 'checked="checked">Mesajý üst konu yap</label>';

	elseif ( (isset($_POST['ust_konu'])) AND $_POST['ust_konu'] != 1)
		$form_ozellik .= '>Mesajý üst konu yap</label>';

	else
	{
		if ($mesaj_degistir_satir['ust_konu'] == 1)
			$form_ozellik .= 'checked="checked">Mesajý üst konu yap</label>';

		else $form_ozellik .= '>Mesajý üst konu yap</label>';
	}
}


//	YARDIMCI ÝSE	//

elseif ( ($kip == 'mesaj') AND ($kullanici_kim['yetki'] == 3) )
{
	if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$fno' AND yonetme='1' OR";
	else $grupek = "grup='0' AND";

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$fno' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);

	//	YÖNETME YETKÝSÝ VARSA	//
	if (mysql_num_rows($kul_izin))
	{
		$form_ozellik .= '<br><label style="cursor: pointer;">
		<input type="checkbox" name="ust_konu"';


		if ( (isset($_POST['ust_konu'])) AND $_POST['ust_konu'] == 1)
			$form_ozellik .= 'checked="checked">Mesajý üst konu yap</label>';


		elseif ( (isset($_POST['ust_konu'])) AND $_POST['ust_konu'] != 1)
			$form_ozellik .= '>Mesajý üst konu yap</label>';


		else
		{
			if ($mesaj_degistir_satir['ust_konu'] == 1)
				$form_ozellik .= 'checked="checked">Mesajý üst konu yap</label>';

			else $form_ozellik .= '>Mesajý üst konu yap</label>';
		}
	}
}

//	ÜST KONU SEÇENEÐÝ KULLANICIYA GÖRE GÖSTERÝLÝYOR	- SONU //





if (isset($_GET['alinti'])) $mesaj_alinti = $_GET['alinti'];

else $mesaj_alinti = '';


$form_bilgi1 = '<form action="mesaj_degistir_yap.php" method="post" onsubmit="return yolla(\'mesaj_icerik_div\',\'mesaj_icerik\',\'yolla\',\'cevir\'), denetle()" name="form1">
<input type="hidden" name="fno" value="'.$fno.'">
<input type="hidden" name="mesaj_degisti_mi" value="form_dolu">
<input type="hidden" name="kip" value="'.$kip.'">
<input type="hidden" name="mesaj_no" value="'.$mesaj_no.'">
<input type="hidden" name="cevap_no" value="'.$cevap_no.'">
<input type="hidden" name="fsayfa" value="'.$fsayfa.'">
<input type="hidden" name="sayfa" value="'.$sayfa.'">';

$form_bilgi2 = '<form action="mesaj_degistir.php#onizleme" method="post" name="form2" onsubmit="return yolla(\'mesaj_icerik_div\',\'mesaj_icerik\',\'yolla\',\'cevir\'), onizle(), denetle()">
<input type="hidden" name="fno" value="'.$fno.'">
<input type="hidden" name="mesaj_degisti_mi" value="form_dolu">
<input type="hidden" name="kip" value="'.$kip.'">
<input type="hidden" name="mesaj_no" value="'.$mesaj_no.'">
<input type="hidden" name="cevap_no" value="'.$cevap_no.'">
<input type="hidden" name="fsayfa" value="'.$fsayfa.'">
<input type="hidden" name="sayfa" value="'.$sayfa.'">
<input type="hidden" name="alinti" value="'.$mesaj_alinti.'">
<input type="hidden" name="bbcode_kullan" value="">
<input type="hidden" name="ifade" value="">
<input type="hidden" name="ust_konu" value="">
<input type="hidden" name="mesaj_baslik" value="">
<input type="hidden" name="mesaj_icerik" value="">';

$javascript_kodu = '<script type="text/javascript" src="dosyalar/betik_zengin.php"></script>
<script type="text/javascript" src="dosyalar/betik_mesaj.js"></script>';

$javascript_kodu2 = '<script type="text/javascript">
<!-- //
yolla2(\'mesaj_icerik\',\'mesaj_icerik_div\',\'cevir\');
var alan1 = document.getElementById(\'mesaj_icerik_div\');
alan1.designMode="On";
alan1.contentEditable = "true";
alan1.indicateeditable="true";
alan1.useCSS="false";
alan1.styleWithCSS = "false";
alan1.useHeader="false";
var alan2 = document.getElementById(\'mesaj_icerik\');
alan2.focus();
var zengin=cerez_oku("zengin");
if (zengin==1)duzenleyici_degis();
var zenginboyut=cerez_oku("zenginboyut");
if (zenginboyut==0);
else if(zenginboyut>0){for(i=0;zenginboyut>i;i++)alan_buyut("buyut",true);}
else if(zenginboyut<0){for(i=0;zenginboyut<i;i--)alan_buyut("kucult",true);}
//  -->
</script>';


if (!isset($javascript_kapali)) $javascript_kapali = '';


//	TEMA UYGULANIYOR	//

$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), false);


$dongusuz = array('{FORUM_ANASAYFA}' => '<a href="'.$forum_index.'">Forum Ana Sayfasý</a>',
'{FORUM_BASLIK}' => $ust_forum_baslik,
'{ALT_FORUM_BASLIK}' => $alt_forum_baslik,
'{SAYFA_BASLIK}' => $sayfa_baslik,
'{CEVAP_BASLIK}' => $cevap_baslik,
'{SAYFA_KIP}' => 'Ýleti Deðiþtir',
'{FORM_BASLIK}' => $form_baslik,
'{FORM_ICERIK}' => $form_icerik,
'{FORM_OZELLIK}' => $form_ozellik,
'{JAVASCRIPT_KAPALI}' => $javascript_kapali,
'{FORM_BILGI1}' => $form_bilgi1,
'{FORM_BILGI2}' => $form_bilgi2,
'{IFADELER}' => ifade_olustur('5'),
'{JAVASCRIPT_KODU}' => $javascript_kodu,
'{JAVASCRIPT_KODU2}' => $javascript_kodu2);


$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
$gec='';



else:
header('Location: hata.php?hata=14');
exit();


endif;
?>