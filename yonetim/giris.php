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


if (!defined('PHPKF_ICINDEN')) define('PHPKF_ICINDEN', true);



        //      G�R�� YAP TIKLANMI�SA   -   BA�I    //

if ( (isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') ):


//	G�R�� YAPILMI�SA Y�NET�M ANA SAYFASINA Y�NLEND�R	//

if ( (isset($_COOKIE['yonetim_kimlik'])) AND ($_COOKIE['yonetim_kimlik'] != '') ):
	header('Location: index.php');
	exit();

else:

//	FORM DOLU DE��LSE UYAR		//

if ((empty($_POST['kullanici_adi'])) OR (empty($_POST['sifre'])))
{
	header('Location: ../hata.php?hata=18');
	exit();
}

if (( strlen($_POST['kullanici_adi']) >  20) or ( strlen($_POST['kullanici_adi']) <  4))
{
	header('Location: ../hata.php?hata=19');
	exit();
}

if (( strlen($_POST['sifre']) >  20) or ( strlen($_POST['sifre']) <  5))
{
	header('Location: ../hata.php?hata=20');
	exit();
}


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


//	ZARARLI KODLAR TEM�ZLEN�YOR	//

$_POST['kullanici_adi'] = @zkTemizle($_POST['kullanici_adi']);
$_POST['sifre'] = @zkTemizle($_POST['sifre']);
$_COOKIE['misafir_kimlik'] = @zkTemizle($_COOKIE['misafir_kimlik']);
$tarih = time();



// ��FRE ANAHTAR �LE KARI�TIRILARAK VER�TABANINDAK� �LE KAR�ILA�TIRIYOR //

$karma = sha1(($anahtar.$_POST['sifre']));

$strSQL = "SELECT id,sifre,kul_etkin,engelle,yetki,giris_denemesi,kilit_tarihi
		FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
$sonuc = mysql_query($strSQL);

$yonetim_denetim = mysql_fetch_assoc($sonuc);


//	HESAP K�L�T TAR�H� KONTROL ED�L�YOR	//

if ( (isset($yonetim_denetim['kilit_tarihi'])) AND
( ($yonetim_denetim['kilit_tarihi'] + $ayarlar['kilit_sure']) > $tarih ) AND
($yonetim_denetim['giris_denemesi'] > 4) )
{
	header('Location: ../hata.php?hata=21');
	exit();
}




//	KULLANICI ADI VE ��FRE UYU�MUYORSA	//

elseif ( (!mysql_num_rows($sonuc)) OR ($yonetim_denetim['sifre'] != $karma))
{

	//	BA�ARISIZ G�R��LER BE�E ULA�TI�INDA HESAP K�L�TLEN�YOR	//

	$strSQL = "UPDATE $tablo_kullanicilar
				SET kilit_tarihi='$tarih',
				giris_denemesi=giris_denemesi + 1
				WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
	$sonuc = mysql_query($strSQL);

	if ($yonetim_denetim['giris_denemesi'] > 3)
	{
		header('Location: ../hata.php?hata=21');
		exit();
	}

	else
	{
		header('Location: ../hata.php?hata=22');
		exit();
	}
}


//	HESAP ETK�NLE�T�R�LMEM��SE	//

elseif ($yonetim_denetim['kul_etkin'] == 0)
{
	header('Location: ../hata.php?hata=23');
	exit();
}


//	HESAP ENGELLENM��SE	//

elseif ($yonetim_denetim['engelle'] == 1)
{
	header('Location: ../hata.php?hata=24');
	exit();
}


//	Y�NET�C� YETK�S� YOKSA	//

elseif ($yonetim_denetim['yetki'] != 1)
{
	header('Location: ../hata.php?hata=144');
	exit();
}




//	SORUN YOK G�R�� YAPILIYOR	//

//	ZAMAN DE�ER� SHA1 �LE ��FRELENEREK �EREZE YAZILIYOR //
//	BEN� HATIRLA ��ARETL� �SE �EREZ GE�ERL�L�K S�RES� EKLEN�YOR	//

elseif ($yonetim_denetim['sifre'] == $karma)
{
	$yonetim_kimlik = sha1(microtime());
	$kullanici_kimlik = sha1(microtime());

	if (isset($_POST['hatirla']))
	{
		setcookie('kullanici_kimlik', $kullanici_kimlik, time()+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		setcookie('yonetim_kimlik', $yonetim_kimlik, 0, $ayarlar['f_dizin']);
	}

	else
	{
		setcookie('kullanici_kimlik', $kullanici_kimlik, 0, $ayarlar['f_dizin']);
		setcookie('yonetim_kimlik', $yonetim_kimlik, 0, $ayarlar['f_dizin']);
	}


	//	KULLANICI G�R�� YAPINCA A�ILAN M�SAF�R OTURUMU VE �EREZ� S�L�N�YOR	//

	if ( (isset($_COOKIE['misafir_kimlik'])) OR ($_COOKIE['misafir_kimlik'] != '') )
	{
		$strSQL = "DELETE FROM $tablo_oturumlar WHERE sid='$_COOKIE[misafir_kimlik]'";
		$sonuc = mysql_query($strSQL);
		setcookie('misafir_kimlik', '', 0, $ayarlar['f_dizin']);
	}


	//	Y�NET�C� VE KULLANICI K�ML�K VER�TABANINA YAZILIYOR //

	$strSQL = "UPDATE $tablo_kullanicilar
				SET yonetim_kimlik='$yonetim_kimlik', kullanici_kimlik='$kullanici_kimlik',
				giris_denemesi=0,kilit_tarihi=0,yeni_sifre=0,
				son_giris=son_hareket, son_hareket='$tarih', kul_ip='$_SERVER[REMOTE_ADDR]'
				WHERE id='$yonetim_denetim[id]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	if (isset($_POST['git']))
	{
		$_POST['git'] = @str_replace('veisareti', '&', $_POST['git']);
		if ($_POST['git'] == 'portal') header('Location: ../portal/index.php');
		elseif (preg_match('/ip_yonetimi.php\?/', $_POST['git'])) header('Location: '.$_POST['git']);
		else header('Location: index.php');
		exit();
	}

	else
	{
		header('Location: index.php');
		exit();
	}
}
endif;


        //      G�R�� YAP TIKLANMI�SA   -   SONU    //





//	G�R�� YAPILMI�SA Y�NET�M ANA SAYFASINA Y�NLEND�R	//

elseif ( (isset($_COOKIE['yonetim_kimlik'])) AND ($_COOKIE['yonetim_kimlik'] != '') ):

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';

header('Location: index.php');
exit();




// G�R�� YAPILMAMI�SA G�R�� EKRANINI VER	//

else:
$sayfa_adi = 'Y�netim Giri�';
include 'yonetim_baslik.php';



if (isset($_GET['git']))
{
	if ($_GET['git'] == 'portal') $portala_git = 'portal';
	elseif (preg_match('/ip_yonetimi.php\?/', $_GET['git'])) $portala_git = $_GET['git'];
	else $portala_git = '';
}
else $portala_git = '';


if ( isset($kullanici_kim['kullanici_adi']) ) $kulllanici_adi = $kullanici_kim['kullanici_adi'];
else $kulllanici_adi = '';



$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2012 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  T�m haklar� sakl�d�r - All Rights Reserved

function denetle(){ 
var dogruMu = true;
if ((document.giris.kullanici_adi.value.length < 4) || (document.giris.sifre.value.length < 5)){ 
	dogruMu = false; 
	alert("L�tfen kullan�c� ad� ve �ifrenizi giriniz !");}
else;
return dogruMu;}
function dogrula(girdi_ad, girdi_deger){
var alan = girdi_ad + \'-alan\';
if (girdi_ad == \'kullanici_adi\'){
	var kucuk = 4;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_������������.]+$/;}
else if (girdi_ad == \'sifre\'){
	var kucuk = 5;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_.&]+$/;}
if ( girdi_deger.length < kucuk || girdi_deger.length > buyuk )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
else if ( !girdi_deger.match(desen) )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
	else document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/dogru.png" alt="do�ru">\';}
//  -->
</script>';




$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/giris.html');

$dongusuz = array('{PORTALA_GIT}' => $portala_git,
				'{KULLANICI_ADI}' => $kulllanici_adi,
				'{JAVASCRIPT_KODU}' => $javascript_kodu);

$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>