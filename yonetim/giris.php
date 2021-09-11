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



        //      GÝRÝÞ YAP TIKLANMIÞSA   -   BAÞI    //

if ( (isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') ):


//	GÝRÝÞ YAPILMIÞSA YÖNETÝM ANA SAYFASINA YÖNLENDÝR	//

if ( (isset($_COOKIE['yonetim_kimlik'])) AND ($_COOKIE['yonetim_kimlik'] != '') ):
	header('Location: index.php');
	exit();

else:

//	FORM DOLU DEÐÝLSE UYAR		//

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


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

$_POST['kullanici_adi'] = @zkTemizle($_POST['kullanici_adi']);
$_POST['sifre'] = @zkTemizle($_POST['sifre']);
$_COOKIE['misafir_kimlik'] = @zkTemizle($_COOKIE['misafir_kimlik']);
$tarih = time();



// ÞÝFRE ANAHTAR ÝLE KARIÞTIRILARAK VERÝTABANINDAKÝ ÝLE KARÞILAÞTIRIYOR //

$karma = sha1(($anahtar.$_POST['sifre']));

$strSQL = "SELECT id,sifre,kul_etkin,engelle,yetki,giris_denemesi,kilit_tarihi
		FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]' LIMIT 1";
$sonuc = mysql_query($strSQL);

$yonetim_denetim = mysql_fetch_assoc($sonuc);


//	HESAP KÝLÝT TARÝHÝ KONTROL EDÝLÝYOR	//

if ( (isset($yonetim_denetim['kilit_tarihi'])) AND
( ($yonetim_denetim['kilit_tarihi'] + $ayarlar['kilit_sure']) > $tarih ) AND
($yonetim_denetim['giris_denemesi'] > 4) )
{
	header('Location: ../hata.php?hata=21');
	exit();
}




//	KULLANICI ADI VE ÞÝFRE UYUÞMUYORSA	//

elseif ( (!mysql_num_rows($sonuc)) OR ($yonetim_denetim['sifre'] != $karma))
{

	//	BAÞARISIZ GÝRÝÞLER BEÞE ULAÞTIÐINDA HESAP KÝLÝTLENÝYOR	//

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


//	HESAP ETKÝNLEÞTÝRÝLMEMÝÞSE	//

elseif ($yonetim_denetim['kul_etkin'] == 0)
{
	header('Location: ../hata.php?hata=23');
	exit();
}


//	HESAP ENGELLENMÝÞSE	//

elseif ($yonetim_denetim['engelle'] == 1)
{
	header('Location: ../hata.php?hata=24');
	exit();
}


//	YÖNETÝCÝ YETKÝSÝ YOKSA	//

elseif ($yonetim_denetim['yetki'] != 1)
{
	header('Location: ../hata.php?hata=144');
	exit();
}




//	SORUN YOK GÝRÝÞ YAPILIYOR	//

//	ZAMAN DEÐERÝ SHA1 ÝLE ÞÝFRELENEREK ÇEREZE YAZILIYOR //
//	BENÝ HATIRLA ÝÞARETLÝ ÝSE ÇEREZ GEÇERLÝLÝK SÜRESÝ EKLENÝYOR	//

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


	//	KULLANICI GÝRÝÞ YAPINCA AÇILAN MÝSAFÝR OTURUMU VE ÇEREZÝ SÝLÝNÝYOR	//

	if ( (isset($_COOKIE['misafir_kimlik'])) OR ($_COOKIE['misafir_kimlik'] != '') )
	{
		$strSQL = "DELETE FROM $tablo_oturumlar WHERE sid='$_COOKIE[misafir_kimlik]'";
		$sonuc = mysql_query($strSQL);
		setcookie('misafir_kimlik', '', 0, $ayarlar['f_dizin']);
	}


	//	YÖNETÝCÝ VE KULLANICI KÝMLÝK VERÝTABANINA YAZILIYOR //

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


        //      GÝRÝÞ YAP TIKLANMIÞSA   -   SONU    //





//	GÝRÝÞ YAPILMIÞSA YÖNETÝM ANA SAYFASINA YÖNLENDÝR	//

elseif ( (isset($_COOKIE['yonetim_kimlik'])) AND ($_COOKIE['yonetim_kimlik'] != '') ):

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';

header('Location: index.php');
exit();




// GÝRÝÞ YAPILMAMIÞSA GÝRÝÞ EKRANINI VER	//

else:
$sayfa_adi = 'Yönetim Giriþ';
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
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanlýþ">\';
else if ( !girdi_deger.match(desen) )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanlýþ">\';
	else document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="../temalar/'.$ayarlar['temadizini'].'/resimler/dogru.png" alt="doðru">\';}
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