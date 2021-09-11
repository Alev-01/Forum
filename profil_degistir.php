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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';


$_COOKIE['kullanici_kimlik'] = mysql_real_escape_string($_COOKIE['kullanici_kimlik']);


// oturum kodu
$o = $satir['kullanici_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

$_COOKIE['kullanici_kimlik'] = mysql_real_escape_string($_COOKIE['kullanici_kimlik']);


    //  E-POSTA - ��FRE DE���T�RME - BA�I  //

if ( (isset($_GET['kosul'])) AND ($_GET['kosul'] == 'sifre') ):


$sayfano = 29;
$sayfa_adi = 'E-Posta ve �ifre De�i�tir';
include 'baslik.php';

// tema dosyas�
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/profil_degistir.html');


// kullan�c� bilgilileri �ekiliyor

$strSQL = "SELECT id,posta,okunmamis_oi FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$satir = mysql_fetch_array($sonuc);

$javascript_kodu = '<script type="text/javascript">
<!--
function denetle(){
	var dogruMu = true;
	for (var i=0; i<7; i++){
		if (document.form1.elements[i].value==""){ 
			dogruMu = false; 
			alert("* ��ARETL� ALANLARIN DOLDURULMASI ZORUNLUDUR !");
			break}
	}

	if (document.form1.ysifre.value != document.form1.ysifre2.value){
		dogruMu = false; 
		alert("YAZDI�INIZ ��FRELER UYU�MUYOR !");}
	return dogruMu;}
//  -->
</script>';


// okunmam�� �zel iletisi varsa
if ($ayarlar['o_ileti'] == 1)
{
	if ($kullanici_kim['okunmamis_oi'])
		$okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
	else $okunmamis_oi = '';
}

else $okunmamis_oi = '';


//	TEMA UYGULANIYOR	//

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu,
'{FORM_BILGI}' => '<form name="form1" action="profil_degistir_yap.php?o='.$o.'" method="post" onsubmit="return denetle()">
<input type="hidden" name="profil_degisti_mi" value="form_dolu">
<input type="hidden" name="islem_turu" value="sifre">',
'{B_DEGISTIR}' => '<a href="profil_degistir.php">Bilgilerimi De�i�tir</a>',
'{ES_DEGISTIR}' => '<font style="font-size: 10px"><b>E-Posta - �ifre De�i�tir</b></font>',
'{YUKLEMELER}' => '<a href="profil_degistir.php?kosul=yuklemeler">Y�klemeler</a>',
'{SAYFA_BASLIK}' => 'E-Posta ve �ifre De�i�tir',
'{OKUNMAMIS_OI}' => $okunmamis_oi));

$dongusuz2 = array('{UYE_EPOSTA}' => $satir['posta']);

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('8', array('' => ''), false);
$ornek1->kosul('7', $dongusuz2, true);


    //  E-POSTA - ��FRE DE���T�RME - SONU  //





    //  Y�KLEMELER - BA�I  //

elseif ( (isset($_GET['kosul'])) AND ($_GET['kosul'] == 'yuklemeler') ):


$sayfano = 40;
$sayfa_adi = 'Y�klemeler';
include 'baslik.php';

// tema dosyas�
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/profil_degistir.html');



// okunmam�� �zel iletisi varsa
if ($ayarlar['o_ileti'] == 1)
{
	if ($kullanici_kim['okunmamis_oi'])
		$okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
	else $okunmamis_oi = '';
}

else $okunmamis_oi = '';



$strSQL = "SELECT * FROM $tablo_yuklemeler WHERE uye_id='$kullanici_kim[id]' ORDER BY id ASC";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$sira = 0;
$tboyut = 0;
$dosya_yolu = 'dosyalar/yuklemeler/';


// y�kl� dosya varsa

if (mysql_num_rows($sonuc2))
{
	while ($yukleme = mysql_fetch_array($sonuc2))
	{
		$sira++;

		$dosya = $yukleme['dosya'];

		$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $yukleme['tarih']);

		$boyut = NumaraBicim($yukleme['boyut']).' <b>kb.</b>';

		$sil = '<a href="profil_degistir.php?kosul=silme&o='.$o.'&sil='.$yukleme['id'].'" onclick="return window.confirm(\'Dosyay� silmek istedi�inize emin misiniz ?\nDosyay� herhangi bir iletide kulland�ysan�z sildikten sonra eri�ilemez olacakt�r.\')">Sil</a>';

		$ara = '<a href="arama.php?a=1&b=1&forum=tum&tarih=tum_zamanlar&sozcuk_hepsi='.$yukleme['dosya'].'">Ara</a>';

		$ac = '<a href="'.$dosya_yolu.$yukleme['dosya'].'" target="_blank">A�</a>';

		$tboyut += $yukleme['boyut'];

		$tekli1[] = array('{SIRA}' => $sira.')',
		'{DOSYA}' => $dosya,
		'{TARIH}' => $tarih,
		'{BOYUT}' => $boyut,
		'{SIL}' => $sil,
		'{ARA}' => $ara,
		'{AC}' => $ac);
	}

	$toplam = '<b>Toplam dosya boyutu:&nbsp; '.NumaraBicim($tboyut).' kb.</b>';
}


// y�kl� dosya yoksa

else
{
	$tekli1[] = array('{SIRA}' => '</b></td><td colspan="6" width="99%"><br><center><b>Y�kledi�iniz dosya yok</b></center><br><!-- ',
	'{DOSYA}' => '',
	'{TARIH}' => '',
	'{BOYUT}' => '',
	'{SIL}' => '',
	'{ARA}' => '',
	'{AC}' => '-->');

	$toplam = '';
}



//	TEMA UYGULANIYOR	//

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => '',
'{FORM_BILGI}' => '',
'* i�aretli alanlar�n doldurulmas� zorunludur!' =>
'<font style="font-style:normal; font-size: 11px;">Forum �zerinden y�kledi�iniz dosyalar a�a��da s�ralanmaktad�r.
<br>Dosyan�n hangi iletilerde kullan�ld���n� bulmak i�in <b>Ara</b>y� t�klay�n. Dosyay� indirmek veya adresini almak i�in <b>A�</b>� t�klay�n. Dosyay� silmek i�in <b>Sil</b>i t�klay�n. Dosyay� herhangi bir iletide kulland�ysan�z sildikten sonra eri�ilemez olacakt�r.
<br><br>',
'<input class="dugme" type="submit" value="De�i�tir">' => '',
'<input class="dugme" type="reset">' => $toplam,
'{B_DEGISTIR}' => '<a href="profil_degistir.php">Bilgilerimi De�i�tir</a>',
'{ES_DEGISTIR}' => '<a href="profil_degistir.php?kosul=sifre">E-Posta - �ifre De�i�tir</a>',
'{YUKLEMELER}' => '<font style="font-size: 10px"><b>Y�klemeler</b></font>',
'{SAYFA_BASLIK}' => 'Y�klemeler',
'{OKUNMAMIS_OI}' => $okunmamis_oi));

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('7', array('' => ''), false);
$ornek1->kosul('8', array('' => ''), true);
$ornek1->tekli_dongu('1',$tekli1);


    //  Y�KLEMELER - SONU  //





    //  DOSYA S�LME ��LEMLER� - BA�I  //

elseif ( (isset($_GET['kosul'])) AND ($_GET['kosul'] == 'silme') ):


if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
$dosya_yolu = 'dosyalar/yuklemeler/';


// oturum bilgisine bak�l�yor
if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['kullanici_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

if ($_GET['o'] != $o)
{
	header('Location: hata.php?hata=45');
	exit();
}


if (!isset($_GET['sil'])) $_GET['sil'] = 0;
$_GET['sil'] = @zkTemizle($_GET['sil']);
$_GET['sil'] = @str_replace(array('-','x'), '', $_GET['sil']);
if ($_GET['sil'] < 0) $_GET['sil'] = 0;


// Veri rakam de�ilse hata ver
if ((!is_numeric($_GET['sil'])) OR ($_GET['sil'] == 0))
{
	header('Location: hata.php?hata=45');
	exit();
}


// dosyan�n bilgileri �ekiliyor
$strSQL = "SELECT id,dosya FROM $tablo_yuklemeler WHERE uye_id='$kullanici_kim[id]' AND id='$_GET[sil]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$dosya = mysql_fetch_array($sonuc);


// dosya yoksa hata ver
if (!isset($dosya['id']))
{
	header('Location: hata.php?hata=206');
	exit();
}


// dosya sunucudan siliniyor
@unlink($dosya_yolu.$dosya['dosya']);


// dosya girdisi veritaban�ndan siliniyor
$strSQL = "DELETE FROM $tablo_yuklemeler WHERE id='$_GET[sil]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


header('Location: hata.php?bilgi=50');
exit();


    //  DOSYA S�LME ��LEMLER� - SONU  //





    // NORMAL PROF�L DE���T�RME - BA�I  //

else:


$sayfano = 30;
$sayfa_adi = 'Profil De�i�tir';
include 'baslik.php';

// tema dosyas�
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/profil_degistir.html');


// kullan�c� bilgilileri �ekiliyor

$strSQL = "SELECT
id,kullanici_adi,gercek_ad,dogum_tarihi,sehir,web,resim,imza,posta_goster,dogum_tarihi_goster,sehir_goster,gizli,icq,msn,yahoo,aim,skype,temadizini,temadizinip,okunmamis_oi
FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$satir = mysql_fetch_array($sonuc);



			//	RES�M Y�KLEME AYARLARI - BA�I	//


if ( ($ayarlar['uzak_resim'] == 1) OR ($ayarlar['resim_yukle'] == 1) OR
	($ayarlar['resim_galerisi'] == 1) )
{
	$resim_yuleme_bilgi = 'Resim sadece jpeg, gif veya png tipinde olabilir.<br>
Dosya <b>boyutu '.($ayarlar['resim_boyut']/1024).'</b> kilobayt, <b>y�ksekli�i '.$ayarlar['resim_yukseklik'].'</b> ve <b>geni�li�i '.$ayarlar['resim_genislik'].'</b> noktadan b�y�k olamaz.';


	// GE�ERL� RES�M G�STER�L�YOR	//

	if ( (isset($_POST['secim_yap'])) AND (isset($_POST['galeri_resimi']))
			AND	($_POST['galeri_resimi'] != '') )
		$gecerli_resim = '<img src="'.$_POST['galeri_resimi'].'" alt="Kullan�c� Resmi">&nbsp;
<label style="cursor: pointer;">
<input type="checkbox" name="resim_sil">Ge�erli Resmi Sil</label>';

	elseif ($satir['resim'])
		$gecerli_resim = '<img src="'.$satir['resim'].'" alt="Kullan�c� Resmi">&nbsp;
<label style="cursor: pointer;">
<input type="checkbox" name="resim_sil">Ge�erli Resmi Sil</label>';

	else $gecerli_resim = 'YOK';

	$ornek1->kosul('1', array('{RESIM_YUKLEME_BILGI}' => $resim_yuleme_bilgi,
								'{GECERLI_RESIM}' => $gecerli_resim), true);



	// RES�M Y�KLEME A�IKSA	//

	if ($ayarlar['resim_yukle'] == 1)
		$ornek1->kosul('2', array('' => ''), true);

	else $ornek1->kosul('2', array('' => ''), false);


	// UZAK RES�M Y�KLEME A�IKSA	//

	if ($ayarlar['uzak_resim'] == 1)
		$ornek1->kosul('3', array('' => ''), true);

	else $ornek1->kosul('3', array('' => ''), false);


	// RES�M GALER�S� A�IKSA	//

	if ($ayarlar['resim_galerisi'] == 1)
	{
		if ( (isset($_POST['secim_yap'])) AND (isset($_POST['galeri_resimi']))
				AND	($_POST['galeri_resimi'] != '') )
			$uzak_resim2 = $_POST['galeri_resimi'];

		else $uzak_resim2 = '';
		
		$ornek1->kosul('4', array('{UZAK_RESIM2}' => $uzak_resim2), true);
	}

	else $ornek1->kosul('4', array('' => ''), false);
}


//	T�M RES�M Y�KLEME AYARLARI KAPALIYSA	//

else
{
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('3', array('' => ''), false);
	$ornek1->kosul('4', array('' => ''), false);
}

				//	RES�M Y�KLEME AYARLARI - SONU	//







$imza_bilgi = '�mzan�z en fazla '.$ayarlar['imza_uzunluk'].' karakter olabilir.<br>
<i>BBCode kullanabilirsiniz.</i>';



if($satir['posta_goster'] == 1) $posta_goster_evet = 'checked="checked"';
else $posta_goster_evet = '';

if($satir['posta_goster'] == 0) $posta_goster_hayir = 'checked="checked"';
else $posta_goster_hayir = '';



if($satir['dogum_tarihi_goster'] == 1) $dogum_goster_evet = 'checked="checked"';
else $dogum_goster_evet = '';

if($satir['dogum_tarihi_goster'] == 0) $dogum_goster_hayir = 'checked="checked"';
else $dogum_goster_hayir = '';



if($satir['sehir_goster'] == 1) $sehir_goster_evet = 'checked="checked"';
else $sehir_goster_evet = '';

if($satir['sehir_goster'] == 0) $sehir_goster_hayir = 'checked="checked"';
else $sehir_goster_hayir = '';



if($satir['gizli'] == 0) $cevrimici_goster_evet = 'checked="checked"';
else $cevrimici_goster_evet = '';

if($satir['gizli'] == 1) $cevrimici_goster_hayir = 'checked="checked"';
else $cevrimici_goster_hayir = '';


// forum tema se�imi alan�

$temalar = explode(',',$ayarlar['tema_secenek']);

$adet = count($temalar);

$uye_tema = '<select class="formlar" name="tema_secim">';


for ($i=0; $adet-1 > $i; $i++)
{
	if ($satir['temadizini'] != $temalar[$i])
		$uye_tema .= '<option value="'.$temalar[$i].'">'.$temalar[$i].'</option>';
	
	else $uye_tema .= '<option value="'.$temalar[$i].'" selected="selected">'.$temalar[$i].'</option>';
}

$uye_tema .= '</select>';



// portal tema se�imi alan�

if ($portal_kullan == '1')
{
	$tablo_portal_ayarlar = $tablo_oneki.'portal_ayarlar';

	$strSQL = "SELECT * FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
	$pt_sonuc = @mysql_query($strSQL);
	$portal_temalari = mysql_fetch_assoc($pt_sonuc);


    $ptemalar = explode(',',$portal_temalari['sayi']);
	$adet = count($ptemalar);
	$uye_portal_tema = '<select class="formlar" name="tema_secimp">';

	for ($i=0; $adet-1 > $i; $i++)
	{
		if ($satir['temadizinip'] != $ptemalar[$i])
			$uye_portal_tema .= '<option value="'.$ptemalar[$i].'">'.$ptemalar[$i].'</option>';

		else $uye_portal_tema .= '<option value="'.$ptemalar[$i].'" selected="selected">'.$ptemalar[$i].'</option>';
	}

	$uye_portal_tema .= '</select>';
	$ornek1->kosul('5', array('{UYE_PORTAL_TEMA}' => $uye_portal_tema), true);
}


else $ornek1->kosul('5', array('' => ''), false);


$javascript_kodu = '<script type="text/javascript">
<!--
function denetle(){
	var dogruMu = true;
	for (var i=0; i<5; i++){
		if (document.form1.elements[i].value==""){
			dogruMu = false; 
			alert("* ��ARETL� ALANLARIN DOLDURULMASI ZORUNLUDUR !");
			break;}
	}
	return dogruMu;}
//  -->
</script>';


$javascript_kodu2 = '<script type="text/javascript">
<!-- //
function imzaUzunluk(){
	var div_katman = document.getElementById(\'imza_uzunluk\');
	div_katman.innerHTML = \'Eklenebilir Karakter: \' + ('.$ayarlar['imza_uzunluk'].'-document.form1.imza.value.length);

	if (document.form1.imza.value.length > '.$ayarlar['imza_uzunluk'].'){
		alert(\'En fazla '.$ayarlar['imza_uzunluk'].' karakter girebilirsiniz.\');
		document.form1.imza.value = document.form1.imza.value.substr(0,'.$ayarlar['imza_uzunluk'].');
		div_katman.innerHTML = \'Eklenebilir Karakter: 0\';}
	return true;}
imzaUzunluk();
//  -->
</script>';



// okunmam�� �zel iletisi varsa
if ($ayarlar['o_ileti'] == 1)
{
    if ($kullanici_kim['okunmamis_oi'])
        $okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
    else $okunmamis_oi = '';
}

else $okunmamis_oi = '';


//	TEMA UYGULANIYOR	//

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu,
'{SAYFA_BASLIK}' => 'Profil De�i�tir',
'{FORM_BILGI}' => '<form name="form1" action="profil_degistir_yap.php?o='.$o.'" method="post" enctype="multipart/form-data" onsubmit="return denetle()">
<input type="hidden" name="profil_degisti_mi" value="form_dolu">
<input type="hidden" name="MAX_FILE_SIZE" value="1022999">
<input type="hidden" name="islem_turu" value="normal">',
'{B_DEGISTIR}' => '<font style="font-size: 10px"><b>Bilgilerimi De�i�tir</b></font>',
'{ES_DEGISTIR}' => '<a href="profil_degistir.php?kosul=sifre">E-Posta - �ifre De�i�tir</a>',
'{YUKLEMELER}' => '<a href="profil_degistir.php?kosul=yuklemeler">Y�klemeler</a>',
'{OKUNMAMIS_OI}' => $okunmamis_oi));

$dongusuz1 = array('{JAVASCRIPT_KODU2}' => $javascript_kodu2,
'{UYE_ADI}' => $satir['kullanici_adi'],
'{UYE_GERCEK_AD}' => $satir['gercek_ad'],
'{UYE_DOGUM}' => $satir['dogum_tarihi'],
'{UYE_SEHIR}' => $satir['sehir'],
'{UYE_WEB}' => $satir['web'],
'{UYE_TEMA}' => $uye_tema,
'{IMZA_BILGI}' => $imza_bilgi,
'{UYE_IMZA}' => $satir['imza'],
'{UYE_ICQ}' => $satir['icq'],
'{UYE_AIM}' => $satir['aim'],
'{UYE_MSN}' => $satir['msn'],
'{UYE_YAHOO}' => $satir['yahoo'],
'{UYE_SKYPE}' => $satir['skype'],
'{POSTA_GOSTER_EVET}' => $posta_goster_evet,
'{POSTA_GOSTER_HAYIR}' => $posta_goster_hayir,
'{DOGUM_GOSTER_EVET}' => $dogum_goster_evet,
'{DOGUM_GOSTER_HAYIR}' => $dogum_goster_hayir,
'{SEHIR_GOSTER_EVET}' => $sehir_goster_evet,
'{SEHIR_GOSTER_HAYIR}' => $sehir_goster_hayir,
'{CEVRIMICI_GOSTER_EVET}' => $cevrimici_goster_evet,
'{CEVRIMICI_GOSTER_HAYIR}' => $cevrimici_goster_hayir);


$ornek1->kosul('6', $dongusuz1, true);
$ornek1->kosul('7', array('' => ''), false);
$ornek1->kosul('8', array('' => ''), false);


    // NORMAL PROF�L DE���T�RME - SONU  //

endif;

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>