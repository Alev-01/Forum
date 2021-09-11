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


//	GÝRÝÞ YAPILMIÞSA ANA SAYFAYA YÖNLENDÝR	//

if (isset($_COOKIE['kullanici_kimlik']))
{
	header('Location: index.php');
	exit();
}




//	YENÝ ÞÝFRE TALEBÝ YAPILIYORSA	//

if (isset($_POST['kayit_yapildi_mi']) and ($_POST['kayit_yapildi_mi'] == 'sifre_talebi')):

if ( strlen($_POST['posta']) ==  ''):
	header('Location: hata.php?hata=8');
	exit();
endif;

if ( strlen($_POST['posta']) > 70):
	header('Location: hata.php?hata=40');
	exit();
endif;

if (!preg_match('/^([~&+.0-9a-z_-]+)@(([~&+0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $_POST['posta'])):
	header('Location: hata.php?hata=10');
	exit();
endif;



//	FORM DOÐRU DOLDURULDUYSA ÝÞLEMLERE DEVAM	//


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


$_POST['posta'] = @zkTemizle($_POST['posta']);


//	E-POSTA ADRESÝNÝN DOÐRULUÐU KONTROL EDÝLÝYOR	//

$strSQL = "SELECT id,posta,kullanici_adi,kul_etkin,engelle FROM $tablo_kullanicilar
		WHERE posta='$_POST[posta]' LIMIT 1";
$sonuc = mysql_query($strSQL);


// girilen e-posta doðruysa

if (mysql_num_rows($sonuc)):
$yeni_sifre = mysql_fetch_array($sonuc);


// hesap etkinleþtirilmemiþse uyarýlýyor

if ($yeni_sifre['kul_etkin'] != 1)
{
    header('Location: hata.php?hata=23');
    exit();
}


// hesap engellenmiþse uyarýlýyor

if ($yeni_sifre['engelle'] == 1)
{
    header('Location: hata.php?hata=24');
    exit();
}


//	YENÝ ÞÝFRE OLUÞTURULUP VERÝTABANINA GÝRÝLÝYOR	//

$rastgele = rand(1111111,9999999);

$strSQL = "UPDATE $tablo_kullanicilar SET yeni_sifre='$rastgele'
		WHERE posta='$_POST[posta]' LIMIT 1";
$sonuc = mysql_query($strSQL);



//		POSTALAR/YENI_SIFRE.TXT DOSYASINDAKÝ YAZILAR ALINIYOR...		//
//		... BELÝRTÝLEN YERLERE YENÝ BÝLGÝLER GÝRÝLÝYOR		// 



if (!($dosya_ac = fopen('./postalar/yeni_sifre.txt','r'))) die ('Dosya Açýlamýyor');
$posta_metni = fread($dosya_ac,1024);
fclose($dosya_ac);

$bul = array('{forumadi}',
'{alanadi}',
'{f_dizin}',
'{kullanici_adi}',
'{kulid}',
'{yeni_sifre}');

$cevir = array($ayarlar['anasyfbaslik'],
$ayarlar['alanadi'],
$ayarlar['f_dizin'],
$yeni_sifre['kullanici_adi'],
$yeni_sifre['id'],
$rastgele);

if ($cevir[2] == '/')
$cevir[2] = '';

$posta_metni = str_replace($bul,$cevir,$posta_metni);


//	YENÝ ÞÝFRE TALEBÝ BÝLGÝLERÝ POSTALANIYOR		//

require('eposta_sinif.php');
$mail = new eposta_yolla();


if ($ayarlar['eposta_yontem'] == 'mail') $mail->MailKullan();
elseif ($ayarlar['eposta_yontem'] == 'smtp') $mail->SMTPKullan();


$mail->sunucu = $ayarlar['smtp_sunucu'];
if ($ayarlar['smtp_kd'] == 'true') $mail->smtp_dogrulama = true;
else $mail->smtp_dogrulama = false;
$mail->kullanici_adi = $ayarlar['smtp_kullanici'];
$mail->sifre = $ayarlar['smtp_sifre'];

$mail->gonderen = $ayarlar['y_posta'];
$mail->gonderen_adi = $ayarlar['anasyfbaslik'];
$mail->GonderilenAdres($yeni_sifre['posta']);
$mail->YanitlamaAdres($ayarlar['y_posta']);

$mail->konu = $ayarlar['anasyfbaslik'].' - Yeni Þifre Baþvurusu';
$mail->icerik = $posta_metni;


if ($mail->Yolla())
{
    // YENÝ ÞÝFRE TALEBÝ TAMAMLANDI, EKRAN ÇIKTISI VERÝLÝYOR //

	header('Location: hata.php?bilgi=20');
	exit();
}

else
{
	echo '<br><br><center><h3><font color="red">E-posta gönderilemedi !<p><u>Hata iletisi</u>: &nbsp; ';
	echo $mail->hata_bilgi;
	echo '</p></font></h3></center>';
	exit();
}

//	GÝRÝLEN E-POSTA VERÝTABANINDA YOKSA 	//

else:
	header('Location: hata.php?hata=13');
	exit();
endif;










//	YENÝ ÞÝFRE OLUÞTUR DÜÐMESÝ TIKLANMIÞSA	//


elseif (isset($_POST['kayit_yapildi_mi']) AND ($_POST['kayit_yapildi_mi'] == 'sifre_olustur')):

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


$_POST['kulid'] = zkTemizle($_POST['kulid']);
$_POST['ys'] = zkTemizle($_POST['ys']);


//	KULID VE YENÝ ÞÝFRENÝN DOÐRULUÐU KONTROL EDÝLÝYOR	//	

if ( (strlen($_POST['ys']) !=  7) OR (is_numeric($_POST['ys']) == false) OR (is_numeric($_POST['kulid']) == false) ):
	header('Location: hata.php?hata=96');
	exit();
endif;


$strSQL = "SELECT id FROM $tablo_kullanicilar
		WHERE id='$_POST[kulid]' AND yeni_sifre='$_POST[ys]' LIMIT 1";
$sonuc = mysql_query($strSQL);

if (!mysql_num_rows($sonuc)):
	header('Location: hata.php?hata=96');
	exit();
endif;


//	FORM BÝLGÝLERÝ DENETLENÝYOR	//

if (( strlen($_POST['y_sifre1']) >  20) OR ( strlen($_POST['y_sifre1']) <  5)):
	header('Location: hata.php?hata=20');
	exit();
endif;

if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_POST['y_sifre1'])):
	header('Location: hata.php?hata=34');
	exit();
endif;

if ($_POST['y_sifre1'] != $_POST['y_sifre2']):
	header('Location: hata.php?hata=33');
	exit();
endif;


//	FORM DOÐRU DOLDURULDUYSA ÝÞLEMLERE DEVAM	//
//	YENÝ ÞÝFRE ANAHTAR DEÐÝÞKENÝNDE KULUNAN DEÐER KARIÞTIRILIP SHA1 ÝLE ÞÝFRELENÝYOR	//


$_POST['y_sifre1'] = @zkTemizle($_POST['y_sifre1']);

$karma = sha1(($anahtar.$_POST['y_sifre1']));

$strSQL = "UPDATE $tablo_kullanicilar SET sifre='$karma', yeni_sifre='0'
			WHERE id='$_POST[kulid]' LIMIT 1";
$sonuc = mysql_query($strSQL);

header('Location: hata.php?bilgi=21');
exit();












else :


if ( (isset($_GET['kulid'])) AND (isset($_GET['ys'])) AND ($_GET['ys'] == 'iptal')  )
{
    if (!defined('DOSYA_AYAR')) include 'ayar.php';
    if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


	if (is_numeric($_GET['kulid']) == false)
	{
		header('Location: hata.php?hata=49');
		exit();
	}


    $_GET['kulid'] = @zkTemizle($_GET['kulid']);


	$strSQL = "UPDATE $tablo_kullanicilar SET yeni_sifre='0' WHERE id='$_GET[kulid]' LIMIT 1";
	$sonuc = mysql_query($strSQL);

	header('Location: hata.php?bilgi=22');
	exit();
}



//	KULID VE YENÝ ÞÝFRENÝN DOÐRULUÐU KONTROL EDÝLÝYOR - SAYAYA GÝRÝÞ SIRASINDA	//

if ( (isset($_GET['kulid'])) AND (isset($_GET['ys'])) )
{
    if (!defined('DOSYA_AYAR')) include 'ayar.php';
    if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
    @session_start();


	//	yeni þifre deneme sayýsý her defasýnda arttýrýlýyor	//

    if (empty($_SESSION['yenisifre_deneme'])) 
    $_SESSION['yenisifre_deneme'] = 1;
    else
    $_SESSION['yenisifre_deneme']++;

    $_GET['kulid'] = @zkTemizle($_GET['kulid']);
    $_GET['ys'] = @zkTemizle($_GET['ys']);


    //  bilgiler hatalýysa  //

	if ( (strlen($_GET['ys']) !=  7) OR (is_numeric($_GET['ys']) == false) OR (is_numeric($_GET['kulid']) == false) )
	{
		header('Location: hata.php?hata=96');
		exit();
	}


    //	kayýt denemesi beþe ulaþtýðýnda hata iletisi veriliyor	//

    if ($_SESSION['yenisifre_deneme'] > 5)
    {
        $strSQL = "UPDATE $tablo_kullanicilar SET yeni_sifre='0' WHERE id='$_GET[kulid]' LIMIT 1";
        $sonuc = mysql_query($strSQL);
 

        header('Location: hata.php?hata=97');
        exit();
    }


	$strSQL = "SELECT id FROM $tablo_kullanicilar
		WHERE id='$_GET[kulid]' AND yeni_sifre='$_GET[ys]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=96');
		exit();
	}
}





//	SAYFAYA ÝLK DEFA GÝRÝLÝYORSA BURADAN SONRASI GÖSTERÝLÝYOR	//

$sayfano = 33;
$sayfa_adi = 'Yeni Þifre Baþvurusu';
include 'baslik.php';



$javascript_kodu = '<script type="text/javascript">
<!--
function denetle()
{ 
	var dogruMu = true;
	if (document.giris.posta.value.length < 4)
	{ 
		dogruMu = false; 
		alert("Lütfen E-Posta adresinizi giriniz !");
	}
	else;
	return dogruMu;
}
//  -->
</script>';



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/yeni_sifre.html');

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu));



//		YENÝ ÞÝFRE OLUÞTURMA EKRANI 	//

if ( (isset($_GET['kulid'])) AND (isset($_GET['ys'])) ):


if ( isset($_GET['kulid']) )
	$form_kulid = $_GET['kulid'];

else $form_kulid = '';


if ( isset($_GET['ys']) )
	$form_ys = $_GET['ys'];

else $form_ys = '';



$ornek1->kosul('2', array('' => ''), false);

$ornek1->kosul('1', array('{FORM_KULID}' => $form_kulid,
'{FORM_YS}' => $form_ys), true);






//		YENÝ ÞÝFRE TALEBÝ EKRANI 	//

else:

$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('' => ''), true);


endif;
if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>