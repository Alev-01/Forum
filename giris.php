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


if (!defined('PHPKF_ICINDEN')) define('PHPKF_ICINDEN', true);



		//		GÝRÝÞ YAP TIKLANMIÞSA  -  BAÞI		//

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


//	GEÇERSÝZ BÝR ÇEREZ VARSA ÇIKIS SAYFASINA YÖNLENDÝRÝLÝYOR	//

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


	//	GÝRÝÞ YAPILMIÞSA PROFÝLE YÖNLENDÝR	//
	else
	{
		header('Location: profil.php');
		exit();
	}
}



//	FORM DOLU DEÐÝLSE UYAR		//

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


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

$_POST['kullanici_adi'] = @zkTemizle($_POST['kullanici_adi']);
$_POST['sifre'] = @zkTemizle($_POST['sifre']);
$_SERVER['REMOTE_ADDR'] = @zkTemizle($_SERVER['REMOTE_ADDR']);
$_COOKIE['misafir_kimlik'] = @zkTemizle($_COOKIE['misafir_kimlik']);
$tarih = time();
$sayfa_adi = 'Kullanýcý giriþ yaptý';



// ÞÝFRE ANAHTAR ÝLE KARIÞTIRILARAK VERÝTABANINDAKÝ ÝLE KARÞILAÞTIRIYOR //

$karma = sha1(($anahtar.$_POST['sifre']));

$strSQL = "SELECT id,sifre,kul_etkin,engelle,giris_denemesi,kilit_tarihi,son_giris
		FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
$sonuc = mysql_query($strSQL);

$kullanici_denetim = mysql_fetch_assoc($sonuc);


//	HESAP KÝLÝT TARÝHÝ KONTROL EDÝLÝYOR	//

if ( (isset($kullanici_denetim['kilit_tarihi'])) AND
(($kullanici_denetim['kilit_tarihi'] + $ayarlar['kilit_sure']) > $tarih) AND
($kullanici_denetim['giris_denemesi'] > 4) )
{
	header('Location: hata.php?hata=21');
	exit();
}




//	KULLANICI ADI VE ÞÝFRE UYUÞMUYORSA	//

elseif ((!mysql_num_rows($sonuc)) OR ($kullanici_denetim['sifre'] != $karma))
{

	//	BAÞARISIZ GÝRÝÞLER BEÞE ULAÞTIÐINDA HESAP KÝLÝTLENÝYOR	//

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


//	HESAP ETKÝNLEÞTÝRÝLMEMÝÞSE	//

elseif ($kullanici_denetim['kul_etkin'] == 0)
{
	header('Location: hata.php?hata=23');
	exit();
}


//	HESAP ENGELLENMÝÞSE	//

elseif ($kullanici_denetim['engelle'] == 1)
{
	header('Location: hata.php?hata=24');
	exit();
}




//	SORUN YOK GÝRÝÞ YAPILIYOR	//

//	ZAMAN DEÐERÝ SHA1 ÝLE ÞÝFRELENEREK ÇEREZE YAZILIYOR //
//	BENÝ HATIRLA ÝÞARETLÝ ÝSE ÇEREZ GEÇERLÝLÝK SÜRESÝ EKLENÝYOR	//

elseif ($kullanici_denetim['sifre'] == $karma)
{
	$kullanici_kimlik = sha1(microtime());

	if (isset($_POST['hatirla']))
		setcookie('kullanici_kimlik', $kullanici_kimlik, $tarih +$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);

	else
		setcookie('kullanici_kimlik', $kullanici_kimlik, 0, $ayarlar['f_dizin']);

	setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);


	//	KULLANICI GÝRÝÞ YAPINCA AÇILAN MÝSAFÝR OTURUMU VE ÇEREZÝ SÝLÝNÝYOR	//

	if ((isset($_COOKIE['misafir_kimlik'])) OR ($_COOKIE['misafir_kimlik'] != ''))
	{
		$strSQL = "DELETE FROM $tablo_oturumlar WHERE sid='$_COOKIE[misafir_kimlik]'";
		$sonuc = mysql_query($strSQL);
		setcookie('misafir_kimlik', '', 0, $ayarlar['f_dizin']);
	}


	//	KULLANICI KÝMLÝK VERÝTABANINA YAZILIYOR //
	// son_hareket tarihi son_girise yazdýrýlýyor

	$strSQL = "UPDATE $tablo_kullanicilar SET
				kullanici_kimlik='$kullanici_kimlik', yonetim_kimlik='',
				giris_denemesi=0, kilit_tarihi=0, yeni_sifre=0,
				son_giris=son_hareket, son_hareket='$tarih', 
				hangi_sayfada='$sayfa_adi', kul_ip='$_SERVER[REMOTE_ADDR]' 
				WHERE id='$kullanici_denetim[id]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	//	KULLANICI GÝRÝÞ SAYFASINA YÖNLENDÝRÝLMÝÞSE AYNI ADRESE GERÝ YOLLANIYOR	//

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

        //      GÝRÝÞ YAP TIKLANMIÞSA   -   SONU    //





//	GEÇERSÝZ BÝR ÇEREZ VARSA SÝLÝNÝYOR	//

elseif ((isset($_COOKIE['kullanici_kimlik'])) AND ($_COOKIE['kullanici_kimlik'] != '')):
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';

if (empty($kullanici_kim['id']))
{
	setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
	setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
	setcookie('kfk_okundu', '', 0, $ayarlar['f_dizin']);
	header('Location: giris.php');
}


//	GÝRÝÞ YAPILMIÞSA PROFÝLE YÖNLENDÝR	//

elseif (isset($kullanici_kim['id']))
{
	header('Location: profil.php');
	exit();
}
$gec = '';





// GÝRÝÞ YAPILMAMIÞSA GÝRÝÞ EKRANINI VER    //

else:
$sayfano = 8;
$sayfa_adi = 'Kullanýcý Giriþ';
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
//  Tüm haklarý saklýdýr - All Rights Reserved

function denetle(){ 
var dogruMu = true;
if ((document.giris.kullanici_adi.value.length < 4) || (document.giris.sifre.value.length < 5)){ 
	dogruMu = false; 
	alert("Lütfen kullanýcý adý ve þifrenizi giriniz !");}
else;
return dogruMu;}
function dogrula(girdi_ad, girdi_deger){
var alan = girdi_ad + \'-alan\';
if (girdi_ad == \'kullanici_adi\'){
	var kucuk = 4;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_ðÐüÜÞþÝýÖöÇç.]+$/;}
else if (girdi_ad == \'sifre\'){
	var kucuk = 5;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_.&]+$/;}
if ( girdi_deger.length < kucuk || girdi_deger.length > buyuk )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanlýþ">\';
else if ( !girdi_deger.match(desen) )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanlýþ">\';
else document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/dogru.png" alt="doðru">\';}
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