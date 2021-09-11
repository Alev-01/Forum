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


if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';



        //      E-POSTA YOLLA TIKLANMIÞSA   -   BAÞI    //


if ( (isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') ):


if (($_POST['eposta_kime']=='') or ( strlen($_POST['eposta_kime']) < 4))
{
	header('Location: hata.php?hata=4');
	exit();
}

if (($_POST['eposta_baslik']=='') or ( strlen($_POST['eposta_baslik']) < 3) or ( strlen($_POST['eposta_baslik']) > 60) or ($_POST['eposta_icerik']=='') or  ( strlen($_POST['eposta_icerik']) < 3))
{
	header('Location: hata.php?hata=5');
	exit();
}

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

$_COOKIE['kullanici_kimlik'] = zkTemizle($_COOKIE['kullanici_kimlik']);
$_POST['eposta_kime'] = zkTemizle(trim($_POST['eposta_kime']));

//	KULLANICININ BÝLGÝLERÝ ÇEKÝLÝYOR	//

$strSQL = "SELECT id,son_ileti,kullanici_adi,posta FROM $tablo_kullanicilar
			WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$kullanici_kim = mysql_fetch_assoc($sonuc);

//	magic_quotes_gpc açýksa	//
if (get_magic_quotes_gpc(1))
{
	$_POST['eposta_baslik'] = stripslashes($_POST['eposta_baslik']);
	$_POST['eposta_icerik'] = stripslashes($_POST['eposta_icerik']);
}


//	ÝKÝ ÝLETÝ ARASI SÜRESÝ DOLMAMIÞSA UYARILIYOR	//

$tarih = time();

if (($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']))
{
	header('Location: hata.php?hata=6');
	exit();
}


//	GÖNDERÝLEN KÝÞÝNÝN BÝLGÝLERÝ ÇEKÝLÝYOR	//

$strSQL = "SELECT posta,kullanici_adi FROM $tablo_kullanicilar
			WHERE kullanici_adi='$_POST[eposta_kime]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$eposta_gonderilen = mysql_fetch_array($sonuc);

if (empty($eposta_gonderilen))
{
	header('Location: hata.php?hata=7');
	exit();
}


//		POSTALAR/OZEL_POSTA.TXT DOSYASINDAKÝ YAZILAR ALINIYOR...		//
//		... BELÝRTÝLEN YERLERE YENÝ BÝLGÝLER GÝRÝLÝYOR		// 



if (!($dosya_ac = fopen('./postalar/ozel_posta.txt','r'))) die ('Dosya Açýlamýyor');
$posta_metni = fread($dosya_ac,1024);
fclose($dosya_ac);

$bul = array('{forumadi}',
'{kullanici_adi}',
'{eposta_baslik}',
'{eposta_icerik}');

$cevir = array($ayarlar['title'],
$kullanici_kim['kullanici_adi'],
$_POST['eposta_baslik'],
$_POST['eposta_icerik']);

$posta_metni = str_replace($bul,$cevir,$posta_metni);



//		POSTA YOLLANIYOR		//

require('eposta_sinif.php');
$mail = new eposta_yolla();


if ($ayarlar['eposta_yontem'] == 'mail') $mail->MailKullan();
elseif ($ayarlar['eposta_yontem'] == 'smtp') $mail->SMTPKullan();


$mail->sunucu = $ayarlar['smtp_sunucu'];
if ($ayarlar['smtp_kd'] == 'true') $mail->smtp_dogrulama = true;
else $mail->smtp_dogrulama = false;
$mail->kullanici_adi = $ayarlar['smtp_kullanici'];
$mail->sifre = $ayarlar['smtp_sifre'];

$mail->gonderen = $kullanici_kim['posta'];
$mail->gonderen_adi = $kullanici_kim['kullanici_adi'];
$mail->GonderilenAdres($eposta_gonderilen['posta']);

if (!empty($_POST['eposta_kopya'])) $mail->DigerAdres($kullanici_kim['posta']);

$mail->YanitlamaAdres($kullanici_kim['posta']);
$mail->konu = $ayarlar['title'].' - Kullanýcý E-Postasý';
$mail->icerik = $posta_metni;


//	 KULLANICI ALANINA SON ÝLETÝ TARÝHÝ GÝRÝLÝYOR		//

if ($mail->Yolla())
{
	$strSQL = "UPDATE $tablo_kullanicilar SET son_ileti='$tarih'
	WHERE id='$kullanici_kim[id]' LIMIT 1";
	$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	header('Location: hata.php?bilgi=13');
	exit();
}

else
{
	echo '<br><br><center><h3><font color="red">E-posta gönderilemedi !<p><u>Hata iletisi</u>: &nbsp; ';
	echo $mail->hata_bilgi;
	echo '</p></font></h3></center>';
	exit();
}

        //      E-POSTA YOLLA TIKLANMIÞSA   -   SONU    //





else:


// üye adý yoksa
if ( (!isset($_GET['kim'])) OR ($_GET['kim'] == '') )
{
	header('Location: hata.php?hata=46');
	exit();
}


// üye adý bilgisi temizleniyor
$_GET['kim'] = @zkTemizle4(@zkTemizle(trim($_GET['kim'])));



// sayfa baþlýðý
$sayfano = 12;
$sayfa_adi = 'E-Posta Gönder: '.$_GET['kim'];
include 'baslik.php';



$javascript_kodu = '<script type="text/javascript">
<!-- 
function denetle(){ 
var dogruMu = true;
if (document.eposta_form.eposta_kime.value.length < 4){ 
    dogruMu = false; 
    alert("E-postayý göndermek istediðiniz kiþinin adýný yazýnýz !");}
else if (document.eposta_form.eposta_baslik.value.length < 3){ 
    dogruMu = false; 
    alert("YAZDIÐINIZ BAÞLIK 3 KARAKTERDEN UZUN OLMALIDIR !");}
else if (document.eposta_form.eposta_icerik.value.length < 3){ 
   dogruMu = false; 
   alert("YAZDIÐINIZ ÝLETÝ 3 KARAKTERDEN UZUN OLMALIDIR !");}
else;
return dogruMu;}
//  -->
</script>';




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/eposta.html');

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu,
'{EPOSTA_KIME}' => $_GET['kim']));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;

?>