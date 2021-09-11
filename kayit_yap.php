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


@session_start();
if (!defined('DOSYA_AYAR')) include 'ayar.php';


// üye alýmý kapalýysa
if ($ayarlar['uye_kayit'] != 1)
{
	header('Location: hata.php?uyari=9');
	exit();
}


//  kayýt deneme sayýsý her denemede arttýrýlýyor   //
if (empty($_SESSION['kayit_deneme'])) $_SESSION['kayit_deneme'] = 1;
else $_SESSION['kayit_deneme']++;


//  kayýt denemesi beþe ulaþtýðýnda hata iletisi veriliyor  //
if ($_SESSION['kayit_deneme'] > 500)
{
    header('Location: hata.php?hata=25');
    exit();
}


//  BÝLGÝLERÝ TEKRAR GÝRMEYE GEREK KALMAMASI ÝÇÝN OTURUMA KAYDEDÝLÝYOR  //

$_SESSION['kullanici_adi'] = $_POST['kullanici_adi'];
$_SESSION['gercek_ad'] = $_POST['gercek_ad'];
$_SESSION['posta'] = $_POST['posta'];
$_SESSION['dogum_gun'] = $_POST['dogum_gun'];
$_SESSION['dogum_ay'] = $_POST['dogum_ay'];
$_SESSION['dogum_yil'] = $_POST['dogum_yil'];
$_SESSION['sehir'] = $_POST['sehir'];


// kayýt sorusu özelliði açýk ise
if ($ayarlar['kayit_soru'] == '1') $_SESSION['kayit_cevabi'] = $_POST['kayit_cevabi'];

// onay kodu kapalý ise
if ($ayarlar['onay_kodu'] != '1')
{
    $_POST['onay_kodu'] = 'kapali';
    $_SESSION['onay_kodu'] = 'kapali';
}



// KAYIT ALANINDA EKSÝK VARSA UYARILIYOR    //

if ( (!$_POST['kullanici_adi']) or (!$_POST['sifre']) or (!$_POST['posta']) or (!$_POST['gercek_ad']) or (!$_POST['dogum_gun']) or (!$_POST['dogum_ay']) or (!$_POST['dogum_yil']) or (!$_POST['sehir']) ):

header('Location: hata.php?hata=26');
exit();





//  GÖRSEL ONAY KODU DOÐRU ÝSE DEVAM    //

elseif (strtolower($_POST['onay_kodu']) == strtolower($_SESSION['onay_kodu'])):

// KAYIT BÝLGÝLERÝNÝN DOÐRULUÐU DENETLENÝYOR //

if (!preg_match('/^[A-Za-z0-9-_ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['kullanici_adi']))
{
    header('Location: hata.php?hata=27');
    exit();
}
if (( strlen($_POST['kullanici_adi']) > 20) or ( strlen($_POST['kullanici_adi']) < 4))
{
    header('Location: hata.php?hata=28');
    exit();
}

if (!preg_match('/^[A-Za-z0-9-_ ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['gercek_ad']))
{
    header('Location: hata.php?hata=31');
    exit();
}
if ( ( strlen($_POST['gercek_ad']) > 30) or ( strlen($_POST['gercek_ad']) < 4) )
{
    header('Location: hata.php?hata=32');
    exit();
}

if ($_POST['sifre'] != $_POST['sifre2'])
{
    header('Location: hata.php?hata=33');
    exit();
}
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_POST['sifre']))
{
    header('Location: hata.php?hata=34');
    exit();
}
if (( strlen($_POST['sifre']) > 20) or ( strlen($_POST['sifre']) < 5))
{
    header('Location: hata.php?hata=35');
    exit();
}

if ((!preg_match('/^[A-Za-zðÐüÜÞþÝýÖöÇç]+$/', $_POST['sehir'])) or ( strlen($_POST['sehir']) >  15))
{
    header('Location: hata.php?hata=36');
    exit();
}

if ((!preg_match('/^[0-9]+$/', $_POST['dogum_gun'])) or ( strlen($_POST['dogum_gun']) != 2))
{
    header('Location: hata.php?hata=37');
    exit();
}
if ((!preg_match('/^[0-9]+$/', $_POST['dogum_ay'])) or ( strlen($_POST['dogum_ay']) != 2))
{
    header('Location: hata.php?hata=37');
    exit();
}
if ((!preg_match('/^[0-9]+$/', $_POST['dogum_yil'])) or ( strlen($_POST['dogum_yil']) != 4))
{
    header('Location: hata.php?hata=38');
    exit();
}

if ( strlen($_POST['posta']) > 70)
{
    header('Location: hata.php?hata=40');
    exit();
}

if (!preg_match('/^([~&+.0-9a-z_-]+)@(([~&+0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $_POST['posta']))
{
    header('Location: hata.php?hata=10');
    exit();
}

if ($ayarlar['kayit_soru'] == 1)
{
    if (strtolower($_POST['kayit_cevabi']) != strtolower($ayarlar['kayit_cevabi']))
    {
        header('Location: hata.php?hata=41');
        exit();
    }
}





        //      YASAKLAR - BAÞI     //


//  YASAK KULLANICI ADLARI ALINIYOR //

$strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='kulad' LIMIT 1";
$yasak_sonuc = mysql_query($strSQL);
$yasak_kulad = mysql_fetch_row($yasak_sonuc);
$ysk_kuladd = explode("\r\n", $yasak_kulad[0]);


//  KULLANICI ADI YASAKLARLARI    //

if ($ysk_kuladd[0] != '')
{
    $dongu_sayi = count($ysk_kuladd);
    for ($d=0; $d < $dongu_sayi; $d++)
    {
        if ( (!preg_match('/^\*/', $ysk_kuladd[$d])) AND (!preg_match('/\*$/', $ysk_kuladd[$d])) )
            $ysk_kuladd[$d] = '^'.$ysk_kuladd[$d].'$';

        elseif (!preg_match('/^\*/', $ysk_kuladd[$d])) $ysk_kuladd[$d] = '^'.$ysk_kuladd[$d];

        elseif (!preg_match('/\*$/', $ysk_kuladd[$d])) $ysk_kuladd[$d] .= '$';

        $ysk_kuladd[$d] = str_replace('*', '', $ysk_kuladd[$d]);


        if (preg_match("/$ysk_kuladd[$d]/i", $_POST['kullanici_adi']))
        {
            header('Location: hata.php?hata=29');
            exit();
        }
    }
}




//  YASAK POSTA ADRESLERÝ ALINIYOR  //

$strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='posta' LIMIT 1";
$yasak_sonuc = mysql_query($strSQL);
$yasak_posta = mysql_fetch_row($yasak_sonuc);
$ysk_postad = explode("\r\n", $yasak_posta[0]);


//  E-POSTA ADRESÝ YASAKLARI    //

if ($ysk_postad[0] != '')
{
    $dongu_sayi = count($ysk_postad);
    for ($i=0; $i<$dongu_sayi; $i++)
    {
        if ( (!preg_match('/^\*/', $ysk_postad[$i])) AND (!preg_match('/\*$/', $ysk_postad[$i])) )
        $ysk_postad[$i] = '^'.$ysk_postad[$i].'$';

        elseif (!preg_match('/^\*/', $ysk_postad[$i])) $ysk_postad[$i] = '^'.$ysk_postad[$i];

        elseif (!preg_match('/\*$/', $ysk_postad[$i])) $ysk_postad[$i] .= '$';

        $ysk_postad[$i] = str_replace('*', '', $ysk_postad[$i]);


        if (preg_match("/$ysk_postad[$i]/i", $_POST['posta']))
        {
            header('Location: hata.php?hata=30');
            exit();
        }
    }
}




//  YASAK AD SOYADLAR ALINIYOR  //

$strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='adsoyad' LIMIT 1";
$yasak_sonuc = mysql_query($strSQL);
$yasak_adsoyad = mysql_fetch_row($yasak_sonuc);
$ysk_adsoyadd = explode("\r\n", $yasak_adsoyad[0]);


// AD SOYADIN YASAKLAR LÝSTESÝNDE OLUP OLMADIÐINA BAKILIYOR //

if ($ysk_adsoyadd[0] != '')
{
    $dongu_sayi = count($ysk_adsoyadd);
    for ($i=0; $i<$dongu_sayi; $i++)
    {
        if (preg_match("/$ysk_adsoyadd[$i]/i", $_POST['gercek_ad']))
        {
            header('Location: hata.php?hata=186');
            exit();
        }
    }
}


        //      YASAKLAR - SONU     //






if ($_POST['kayit_yapildi_mi'] == 'form_dolu')
{
    $tarih = time();


    // KULLANICI ADININ DAHA ÖNCE ALINIP ALINMADIÐI DENETLENÝYOR //

    $strSQL = "SELECT kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]'";
    $sonuc = mysql_query($strSQL);


    // E-POSTA ÝLE DAHA ÖNCE KAYIT YAPILIP YAPILMADIÐI DENETLENÝYOR // 

    $strSQL = "SELECT posta FROM $tablo_kullanicilar WHERE posta='$_POST[posta]'";
    $sonuc2 = mysql_query($strSQL);

    if (mysql_num_rows($sonuc))
    {
        header('Location: hata.php?hata=42');
        exit();
    }

    elseif (mysql_num_rows($sonuc2))
    {
        header('Location: hata.php?hata=43');
        exit();
    }

    else
    {
        if (isset($_POST['eposta_gizle'])) $posta_goster = 0;
        else $posta_goster = 1;

        $_POST['posta'] = mysql_real_escape_string($_POST['posta']);

        $dogum_tarihi = $_POST['dogum_gun'].'-'.$_POST['dogum_ay'].'-'.$_POST['dogum_yil'];


        // anahtar deðeri þifreyle karýþtýrýlarak sha1 ile kodlanýyor
        $karma = sha1(($anahtar.$_POST['sifre']));


        //  HESAP ETKÝNLEÞTÝRME ÖZELLÝÐÝNE GÖRE GEREKLÝ ÝÞLEMLER YAPILIYOR  //

        if ($ayarlar['hesap_etkin'] == 0)
        {
            $strSQL = "INSERT INTO $tablo_kullanicilar (kullanici_adi, sifre, posta, posta_goster, gercek_ad, dogum_tarihi, katilim_tarihi, sehir, kul_etkin, son_giris, son_hareket, kul_ip, sayfano, hangi_sayfada)";

            $strSQL .= "VALUES ('$_POST[kullanici_adi]','$karma','$_POST[posta]','$posta_goster','$_POST[gercek_ad]','$dogum_tarihi','$tarih','$_POST[sehir]','1','$tarih','$tarih','$_SERVER[REMOTE_ADDR]','-1','Kullanýcý çýkýþ yaptý')";

            $sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
            $kul_etkin_kod = 0;
        }

        else
        {
            //  HESAP ETKÝNLEÞTÝRME KODU OLUÞTURULUYOR  //
            $kul_etkin_kod = sha1(microtime());
            $kul_etkin_kod = substr($kul_etkin_kod,9,10);

            $strSQL = "INSERT INTO $tablo_kullanicilar (kullanici_adi, sifre, posta, posta_goster, gercek_ad, dogum_tarihi, katilim_tarihi, sehir, kul_etkin_kod, son_giris, son_hareket, kul_ip, sayfano, hangi_sayfada)";

            $strSQL .= "VALUES ('$_POST[kullanici_adi]','$karma','$_POST[posta]','$posta_goster','$_POST[gercek_ad]','$dogum_tarihi','$tarih','$_POST[sehir]','$kul_etkin_kod','$tarih','$tarih','$_SERVER[REMOTE_ADDR]','-1','Kullanýcý çýkýþ yaptý')";

            $sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        }

        $kulid = mysql_insert_id();



        //  POSTALAR/KAYIT.TXT DOSYASINDAKÝ YAZILAR ALINIYOR... //
        //  ... BELÝRTÝLEN YERLERE YENÝ BÝLGÝLER GÝRÝLÝYOR  // 


        if ($ayarlar['hesap_etkin'] == 0) {
        $dosya = './postalar/kayit0.txt';
        $posta_baslik = $ayarlar['anasyfbaslik'].' Forumlarýna Hoþ Geldiniz';}

        elseif ($ayarlar['hesap_etkin'] == 1) {
        $dosya = './postalar/kayit1.txt';
        $posta_baslik = $ayarlar['anasyfbaslik'].' Forumlarýna Hoþ Geldiniz';}

        else {$dosya = './postalar/kayit2.txt';
        $posta_baslik = $ayarlar['anasyfbaslik'].' Forumlarý Yeni Kullanýcý Kaydý';}


        if (!($dosya_ac = fopen($dosya,'r'))) die ('Dosya Açýlamýyor');
        $posta_metni = fread($dosya_ac,3072);
        fclose($dosya_ac);

        $bul = array('{forumadi}',
        '{alanadi}',
        '{f_dizin}',
        '{kullanici_adi}',
        '{sifre}',
        '{posta}',
        '{gercek_ad}',
        '{dogum_tarihi}',
        '{sehir}',
        '{kulid}',
        '{kul_etkin_kod}');

        $cevir = array($ayarlar['anasyfbaslik'],
        $ayarlar['alanadi'],
        $ayarlar['f_dizin'],
        $_POST['kullanici_adi'],
        $_POST['sifre'],
        $_POST['posta'],
        $_POST['gercek_ad'],
        $dogum_tarihi,
        $_POST['sehir'],
        $kulid,
        $kul_etkin_kod);

        if ($cevir[2] == '/')
        $cevir[2] = '';

        $posta_metni = str_replace($bul,$cevir,$posta_metni);


        //  HESAP BÝLGÝLERÝ VE HESAP ETKÝNLEÞTÝRME KODU POSTALANIYOR    //

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

        if ($ayarlar['hesap_etkin'] == 2)
            $mail->GonderilenAdres($ayarlar['y_posta']);
        else $mail->GonderilenAdres($_POST['posta']);

        $mail->YanitlamaAdres($ayarlar['y_posta']);
        $mail->konu = $posta_baslik;
        $mail->icerik = $posta_metni;


        if ($mail->Yolla())
        {
            // KAYIT ÝÞLEMÝ TAMAMLANDI, EKRAN ÇIKTISI VERÝLÝYOR //

            if ($ayarlar['hesap_etkin'] == 0)
            {
                header('Location: hata.php?bilgi=15');
                exit();
            }

            elseif ($ayarlar['hesap_etkin'] == 1)
            {
                header('Location: hata.php?bilgi=16');
                exit();
            }

            else
            {
                header('Location: hata.php?bilgi=17');
                exit();
            }
        }

        else
        {
            // E-POSTA GÖNDERÝLEMEDÝ //

            if ($ayarlar['hesap_etkin'] == 0)
            {
                header('Location: hata.php?hata=198');
                exit();
            }

            elseif ($ayarlar['hesap_etkin'] == 1)
            {
                header('Location: hata.php?hata=11');
                exit();
            }

            else
            {
                header('Location: hata.php?hata=199');
                exit();
            }
        }
    }
}


$gec = '';


elseif (strtolower($_POST['onay_kodu']) != strtolower($_SESSION['onay_kodu'])):

header('Location: hata.php?hata=44');
exit();


endif;

?>