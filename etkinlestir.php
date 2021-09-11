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
if (!defined('DOSYA_AYAR')) include 'ayar.php';


//	G�R�� YAPILMI�SA ANA SAYFAYA Y�NLEND�R	//

if (isset($_COOKIE['kullanici_kimlik']))
{
	header('Location: index.php');
	exit();
}


//	ETK�NLE�T�RME KODU TALEB� YAPILIYORSA	//

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





//	FORM DO�RU DOLDURULDUYSA ��LEMLERE DEVAM	//

if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

$_POST['posta'] = @zkTemizle($_POST['posta']);


//	E-POSTA ADRES�N�N DO�RULU�U KONTROL ED�L�YOR	//

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




//		postalar/etkinlestirme.txt DOSYASINDAK� YAZILAR ALINIYOR...		//
//		... BEL�RT�LEN YERLERE YEN� B�LG�LER G�R�L�YOR		// 


if (!($dosya_ac = fopen('./postalar/etkinlestirme.txt','r'))) die ('Dosya A��lam�yor');
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




//	ETK�NLE�T�RME B�LG�LER� POSTALANIYOR		//

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
$mail->konu = $ayarlar['anasyfbaslik'].' - Etkinle�tirme Kodu';
$mail->icerik = $posta_metni;


if ($mail->Yolla())
{
// E-POSTA YOLLANDI, EKRAN �IKTISI VER�L�YOR //

	header('Location: hata.php?bilgi=14');
	exit();
}

else
{
	echo '<br><br><center><h3><font color="red">E-posta g�nderilemedi !<p>Hata iletisi: ';
	echo $mail->hata_bilgi;
	echo '</p></font></h3></center>';
	exit();
}

//	G�R�LEN E-POSTA VER�TABANINDA YOKSA 	//

else:
	header('Location: hata.php?hata=13');
	exit();
endif;





//	SAYFAYA �LK DEFA G�R�L�YORSA BURADAN SONRASI G�STER�L�YOR	//

else :

$sayfano = 35;
$sayfa_adi = 'Etkinle�tirme Kodu Ba�vurusu';
include 'baslik.php';



$javascript_kodu = '<script type="text/javascript">
<!--
function denetle()
{ 
	var dogruMu = true;
	if (document.giris.posta.value.length < 4)
	{ 
		dogruMu = false; 
		alert("L�tfen E-Posta adresinizi giriniz !");
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