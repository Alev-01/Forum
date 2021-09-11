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


if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';



        //      E-POSTA YOLLA TIKLANMI�SA   -   BA�I    //


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


//	ZARARLI KODLAR TEM�ZLEN�YOR	//

$_COOKIE['kullanici_kimlik'] = zkTemizle($_COOKIE['kullanici_kimlik']);
$_POST['eposta_kime'] = zkTemizle(trim($_POST['eposta_kime']));

//	KULLANICININ B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT id,son_ileti,kullanici_adi,posta FROM $tablo_kullanicilar
			WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$kullanici_kim = mysql_fetch_assoc($sonuc);

//	magic_quotes_gpc a��ksa	//
if (get_magic_quotes_gpc(1))
{
	$_POST['eposta_baslik'] = stripslashes($_POST['eposta_baslik']);
	$_POST['eposta_icerik'] = stripslashes($_POST['eposta_icerik']);
}


//	�K� �LET� ARASI S�RES� DOLMAMI�SA UYARILIYOR	//

$tarih = time();

if (($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']))
{
	header('Location: hata.php?hata=6');
	exit();
}


//	G�NDER�LEN K���N�N B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT posta,kullanici_adi FROM $tablo_kullanicilar
			WHERE kullanici_adi='$_POST[eposta_kime]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$eposta_gonderilen = mysql_fetch_array($sonuc);

if (empty($eposta_gonderilen))
{
	header('Location: hata.php?hata=7');
	exit();
}


//		POSTALAR/OZEL_POSTA.TXT DOSYASINDAK� YAZILAR ALINIYOR...		//
//		... BEL�RT�LEN YERLERE YEN� B�LG�LER G�R�L�YOR		// 



if (!($dosya_ac = fopen('./postalar/ozel_posta.txt','r'))) die ('Dosya A��lam�yor');
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
$mail->konu = $ayarlar['title'].' - Kullan�c� E-Postas�';
$mail->icerik = $posta_metni;


//	 KULLANICI ALANINA SON �LET� TAR�H� G�R�L�YOR		//

if ($mail->Yolla())
{
	$strSQL = "UPDATE $tablo_kullanicilar SET son_ileti='$tarih'
	WHERE id='$kullanici_kim[id]' LIMIT 1";
	$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	header('Location: hata.php?bilgi=13');
	exit();
}

else
{
	echo '<br><br><center><h3><font color="red">E-posta g�nderilemedi !<p><u>Hata iletisi</u>: &nbsp; ';
	echo $mail->hata_bilgi;
	echo '</p></font></h3></center>';
	exit();
}

        //      E-POSTA YOLLA TIKLANMI�SA   -   SONU    //





else:


// �ye ad� yoksa
if ( (!isset($_GET['kim'])) OR ($_GET['kim'] == '') )
{
	header('Location: hata.php?hata=46');
	exit();
}


// �ye ad� bilgisi temizleniyor
$_GET['kim'] = @zkTemizle4(@zkTemizle(trim($_GET['kim'])));



// sayfa ba�l���
$sayfano = 12;
$sayfa_adi = 'E-Posta G�nder: '.$_GET['kim'];
include 'baslik.php';



$javascript_kodu = '<script type="text/javascript">
<!-- 
function denetle(){ 
var dogruMu = true;
if (document.eposta_form.eposta_kime.value.length < 4){ 
    dogruMu = false; 
    alert("E-postay� g�ndermek istedi�iniz ki�inin ad�n� yaz�n�z !");}
else if (document.eposta_form.eposta_baslik.value.length < 3){ 
    dogruMu = false; 
    alert("YAZDI�INIZ BA�LIK 3 KARAKTERDEN UZUN OLMALIDIR !");}
else if (document.eposta_form.eposta_icerik.value.length < 3){ 
   dogruMu = false; 
   alert("YAZDI�INIZ �LET� 3 KARAKTERDEN UZUN OLMALIDIR !");}
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