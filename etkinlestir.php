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
if (!defined('DOSYA_AYAR')) include 'ayar.php';


//	GÝRÝÞ YAPILMIÞSA ANA SAYFAYA YÖNLENDÝR	//

if (isset($_COOKIE['kullanici_kimlik']))
{
	header('Location: index.php');
	exit();
}


//	ETKÝNLEÞTÝRME KODU TALEBÝ YAPILIYORSA	//

if (isset($_POST['kayit_yapildi_mi']) and ($_POST['kayit_yapildi_mi'] == 'etkinlestirme_talebi')):

if ( strlen($_POST['posta']) == ''):
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

if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

$_POST['posta'] = @zkTemizle($_POST['posta']);


//	E-POSTA ADRESÝNÝN DOÐRULUÐU KONTROL EDÝLÝYOR	//

$strSQL = "SELECT id,kullanici_adi,posta,gercek_ad,dogum_tarihi,sehir,kul_etkin_kod,kul_etkin
            FROM $tablo_kullanicilar WHERE posta='$_POST[posta]' LIMIT 1";
$etkin_sonuc = mysql_query($strSQL);

if (mysql_num_rows($etkin_sonuc)):
$etkin_satir = mysql_fetch_array($etkin_sonuc);


if ( $etkin_satir['kul_etkin'] == 1 )
{
	header('Location: hata.php?hata=12');
	exit();
}




//		postalar/etkinlestirme.txt DOSYASINDAKÝ YAZILAR ALINIYOR...		//
//		... BELÝRTÝLEN YERLERE YENÝ BÝLGÝLER GÝRÝLÝYOR		// 


if (!($dosya_ac = fopen('./postalar/etkinlestirme.txt','r'))) die ('Dosya Açýlamýyor');
$posta_metni = fread($dosya_ac,1024);
fclose($dosya_ac);

$bul = array('{forumadi}',
'{alanadi}',
'{f_dizin}',
'{kullanici_adi}',
'{posta}',
'{gercek_ad}',
'{dogum_tarihi}',
'{sehir}',
'{kulid}',
'{kul_etkin_kod}');

$cevir = array($ayarlar['anasyfbaslik'],
$ayarlar['alanadi'],
$ayarlar['f_dizin'],
$etkin_satir['kullanici_adi'],
$etkin_satir['posta'],
$etkin_satir['gercek_ad'],
$etkin_satir['dogum_tarihi'],
$etkin_satir['sehir'],
$etkin_satir['id'],
$etkin_satir['kul_etkin_kod']);

if ($cevir[2] == '/')
$cevir[2] = '';

$posta_metni = str_replace($bul,$cevir,$posta_metni);




//	ETKÝNLEÞTÝRME BÝLGÝLERÝ POSTALANIYOR		//

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
$mail->GonderilenAdres($etkin_satir['posta']);
$mail->YanitlamaAdres($ayarlar['y_posta']);
$mail->konu = $ayarlar['anasyfbaslik'].' - Etkinleþtirme Kodu';
$mail->icerik = $posta_metni;


if ($mail->Yolla())
{
// E-POSTA YOLLANDI, EKRAN ÇIKTISI VERÝLÝYOR //

	header('Location: hata.php?bilgi=14');
	exit();
}

else
{
	echo '<br><br><center><h3><font color="red">E-posta gönderilemedi !<p>Hata iletisi: ';
	echo $mail->hata_bilgi;
	echo '</p></font></h3></center>';
	exit();
}

//	GÝRÝLEN E-POSTA VERÝTABANINDA YOKSA 	//

else:
	header('Location: hata.php?hata=13');
	exit();
endif;





//	SAYFAYA ÝLK DEFA GÝRÝLÝYORSA BURADAN SONRASI GÖSTERÝLÝYOR	//

else :

$sayfano = 35;
$sayfa_adi = 'Etkinleþtirme Kodu Baþvurusu';
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
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/etkinlestir.html');

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>