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



		//		G�R�� YAP TIKLANMI�SA  -  BA�I		//

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


//	GE�ERS�Z B�R �EREZ VARSA �IKIS SAYFASINA Y�NLEND�R�L�YOR	//

if (isset($_COOKIE['kullanici_kimlik']))
{
	if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';

	if (empty($kullanici_kim['id']))
	{
		setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
		setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
		setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);

		header('Location: giris.php');
		exit();
	}


	//	G�R�� YAPILMI�SA PROF�LE Y�NLEND�R	//
	else
	{
		header('Location: profil.php');
		exit();
	}
}



//	FORM DOLU DE��LSE UYAR		//

if ((empty($_POST['kullanici_adi'])) OR (empty($_POST['sifre'])))
{
	header('Location: hata.php?hata=18');
	exit();
}

if ((strlen($_POST['kullanici_adi']) > 20) OR (strlen($_POST['kullanici_adi']) < 4))
{
	header('Location: hata.php?hata=19');
	exit();
}

if ((strlen($_POST['sifre']) > 20) OR ( strlen($_POST['sifre']) < 5))
{
	header('Location: hata.php?hata=20');
	exit();
}




if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


//	ZARARLI KODLAR TEM�ZLEN�YOR	//

$_POST['kullanici_adi'] = @zkTemizle($_POST['kullanici_adi']);
$_POST['sifre'] = @zkTemizle($_POST['sifre']);
$_SERVER['REMOTE_ADDR'] = @zkTemizle($_SERVER['REMOTE_ADDR']);
$_COOKIE['misafir_kimlik'] = @zkTemizle($_COOKIE['misafir_kimlik']);
$tarih = time();
$sayfa_adi = 'Kullan�c� giri� yapt�';



// ��FRE ANAHTAR �LE KARI�TIRILARAK VER�TABANINDAK� �LE KAR�ILA�TIRIYOR //

$karma = sha1(($anahtar.$_POST['sifre']));

$strSQL = "SELECT id,sifre,kul_etkin,engelle,giris_denemesi,kilit_tarihi,son_giris
		FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
$sonuc = mysql_query($strSQL);

$kullanici_denetim = mysql_fetch_assoc($sonuc);


//	HESAP K�L�T TAR�H� KONTROL ED�L�YOR	//

if ( (isset($kullanici_denetim['kilit_tarihi'])) AND
(($kullanici_denetim['kilit_tarihi'] + $ayarlar['kilit_sure']) > $tarih) AND
($kullanici_denetim['giris_denemesi'] > 4) )
{
	header('Location: hata.php?hata=21');
	exit();
}




//	KULLANICI ADI VE ��FRE UYU�MUYORSA	//

elseif ((!mysql_num_rows($sonuc)) OR ($kullanici_denetim['sifre'] != $karma))
{

	//	BA�ARISIZ G�R��LER BE�E ULA�TI�INDA HESAP K�L�TLEN�YOR	//

	$strSQL = "UPDATE $tablo_kullanicilar
				SET kilit_tarihi='$tarih',
				giris_denemesi=giris_denemesi + 1
				WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	if ($kullanici_denetim['giris_denemesi'] > 3)
	{
		header('Location: hata.php?hata=21');
		exit();
	}

	else
	{
		header('Location: hata.php?hata=22');
		exit();
	}
}


//	HESAP ETK�NLE�T�R�LMEM��SE	//

elseif ($kullanici_denetim['kul_etkin'] == 0)
{
	header('Location: hata.php?hata=23');
	exit();
}


//	HESAP ENGELLENM��SE	//

elseif ($kullanici_denetim['engelle'] == 1)
{
	header('Location: hata.php?hata=24');
	exit();
}




//	SORUN YOK G�R�� YAPILIYOR	//

//	ZAMAN DE�ER� SHA1 �LE ��FRELENEREK �EREZE YAZILIYOR //
//	BEN� HATIRLA ��ARETL� �SE �EREZ GE�ERL�L�K S�RES� EKLEN�YOR	//

elseif ($kullanici_denetim['sifre'] == $karma)
{
	$kullanici_kimlik = sha1(microtime());

	if (isset($_POST['hatirla']))
		setcookie('kullanici_kimlik', $kullanici_kimlik, $tarih +$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);

	else
		setcookie('kullanici_kimlik', $kullanici_kimlik, 0, $ayarlar['f_dizin']);

	setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);


	//	KULLANICI G�R�� YAPINCA A�ILAN M�SAF�R OTURUMU VE �EREZ� S�L�N�YOR	//

	if ((isset($_COOKIE['misafir_kimlik'])) OR ($_COOKIE['misafir_kimlik'] != ''))
	{
		$strSQL = "DELETE FROM $tablo_oturumlar WHERE sid='$_COOKIE[misafir_kimlik]'";
		$sonuc = mysql_query($strSQL);
		setcookie('misafir_kimlik', '', 0, $ayarlar['f_dizin']);
	}


	//	KULLANICI K�ML�K VER�TABANINA YAZILIYOR //
	// son_hareket tarihi son_girise yazd�r�l�yor

	$strSQL = "UPDATE $tablo_kullanicilar SET
				kullanici_kimlik='$kullanici_kimlik', yonetim_kimlik='',
				giris_denemesi=0, kilit_tarihi=0, yeni_sifre=0,
				son_giris=son_hareket, son_hareket='$tarih', 
				hangi_sayfada='$sayfa_adi', kul_ip='$_SERVER[REMOTE_ADDR]' 
				WHERE id='$kullanici_denetim[id]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	//	KULLANICI G�R�� SAYFASINA Y�NLEND�R�LM��SE AYNI ADRESE GER� YOLLANIYOR	//

	if ( ( empty($_POST['git']) ) AND ($_POST['git'] == '') 
		OR (@preg_match('/hata.php/i', $_POST['git'])) )
	{
		header('Location: index.php');
		exit();
	}

	else
	{
		if (@preg_match('/http:\/\//i', $_POST['git']))
		{
			if (@preg_match('/http:\/\/'.$ayarlar['alanadi'].'/i', $_POST['git']))
			{
				$git = @str_replace('veisareti', '&', $_POST['git']);
				$git = @zkTemizle($git);
				header('Location: '.$git);
				exit();
			}

			else
			{
				header('Location: index.php');
				exit();
			}
		}

		else
		{
			$git = @str_replace('veisareti', '&', $_POST['git']);
			$git = zkTemizle($git);
			header('Location: '.$git);
			exit();
		}
	}
}
$gec = '';

        //      G�R�� YAP TIKLANMI�SA   -   SONU    //





//	GE�ERS�Z B�R �EREZ VARSA S�L�N�YOR	//

elseif ((isset($_COOKIE['kullanici_kimlik'])) AND ($_COOKIE['kullanici_kimlik'] != '')):
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';

if (empty($kullanici_kim['id']))
{
	setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
	setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
	setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);
	header('Location: giris.php');
}


//	G�R�� YAPILMI�SA PROF�LE Y�NLEND�R	//

elseif (isset($kullanici_kim['id']))
{
	header('Location: profil.php');
	exit();
}
$gec = '';





// G�R�� YAPILMAMI�SA G�R�� EKRANINI VER    //

else:
$sayfano = 8;
$sayfa_adi = 'Kullan�c� Giri�';
include 'baslik.php';

if (!defined('DOSYA_GERECLER')) include 'gerecler.php';



if (isset($_GET['git']))
{
	$gelinen_adres = @zkTemizle3($_GET['git']);
	$gelinen_adres = @zkTemizle4($gelinen_adres);
}

elseif (isset($_SERVER['HTTP_REFERER']))
{
	$gelinen_adres = @zkTemizle3($_SERVER['HTTP_REFERER']);
	$gelinen_adres = @zkTemizle4($gelinen_adres);
}

else $gelinen_adres = '';



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
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
else if ( !girdi_deger.match(desen) )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
else document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/dogru.png" alt="do�ru">\';}
//  -->
</script>';




$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/giris.html');

$dongusuz = array('{GELINEN_ADRES}' => $gelinen_adres,
'{JAVASCRIPT_KODU}' => $javascript_kodu);

$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>