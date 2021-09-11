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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// site kurucusu de�ilse hata ver
if ($kullanici_kim['id'] != 1)
{
    header('Location: ../hata.php?hata=151');
    exit();
}


// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


//  BA�LI�I GER� Y�KLEME ��LEMLER�   //

if ( (isset($_GET['kurtark'])) AND ($_GET['kurtark'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['kurtark'])) $_GET['kurtark'] = @zkTemizle($_GET['kurtark']);


    if (is_numeric($_GET['kurtark']) == true)
    {
        //  OTURUM B�LG�S�NE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }

        // ba�l���n bilgileri �ekiliyor
        $strSQL = "SELECT hangi_forumdan,silinmis FROM $tablo_mesajlar WHERE id='$_GET[kurtark]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

        // ba�l�k yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=47');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);
        // ba�l�k zaten geri y�klenmi�se
        if ($fno['silinmis'] != 1)
        {
            header('Location: ../hata.php?hata=168');
            exit();
        }


        // ba�l���n silinen cevaplar� varsa d�ng�ye sokularak teker teker geri y�kleniyor
        $strSQL1 = "SELECT id FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[kurtark]' ORDER BY id DESC";
        $sonuc_konu = mysql_query($strSQL1) or die ('<h2>sorgu ba�ar�s�z</h2>');


        $toplam_cevap = 0;

        while ($cevaplari_yukle = mysql_fetch_assoc($sonuc_konu))
        {
            $strSQL2 = "UPDATE $tablo_cevaplar SET silinmis=0 WHERE id='$cevaplari_yukle[id]' LIMIT 1";
            $sonuc = mysql_query($strSQL2) or die ('<h2>sorgu ba�ar�s�z</h2>');


            // forumun cevap sayisi artt�r�l�yor
            $strSQL3 = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
            $sonuc3 = mysql_query($strSQL3) or die ('<h2>sorgu ba�ar�s�z</h2>');
            $toplam_cevap++;
        }


        //	ba�l���n son cevab� �ekiliyor
        $strSQL = "SELECT id,tarih,cevap_yazan FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[kurtark]' ORDER BY tarih DESC LIMIT 1";
        $sonuc = mysql_query($strSQL);
        $son_mesaj = mysql_fetch_assoc($sonuc);


        // cevab� yoksa
        if (empty($son_mesaj['tarih']))
            $strSQL = "UPDATE $tablo_mesajlar SET silinmis=0, cevap_sayi=0, son_mesaj_tarihi=tarih, son_cevap=0, son_cevap_yazan=NULL WHERE id='$_GET[kurtark]'";

        // cevab� varsa
        else $strSQL = "UPDATE $tablo_mesajlar SET silinmis=0, cevap_sayi='$toplam_cevap', son_mesaj_tarihi='$son_mesaj[tarih]', son_cevap='$son_mesaj[id]', son_cevap_yazan='$son_mesaj[cevap_yazan]' WHERE id='$_GET[kurtark]'";

        // konu geri y�kleniyor, son mesaj tarihi ve son cevap bilgileri g�ncelleniyor
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        //	forumun konu say�s� artt�r�l�yor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        header('Location: ../konu.php?k='.$_GET['kurtark']);
        exit();
    }

    else
    {
        header('Location: ../hata.php?hata=47');
        exit();
    }
}




//  CEVABI GER� Y�KLEME ��LEMLER�   //

elseif ( (isset($_GET['kurtarc'])) AND ($_GET['kurtarc'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['kurtarc'])) $_GET['kurtarc'] = @zkTemizle($_GET['kurtarc']);


    if (is_numeric($_GET['kurtarc']) == true)
    {
        //  OTURUM B�LG�S�NE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // cevab�n bilgileri �ekiliyor
        $strSQL = "SELECT hangi_forumdan,silinmis,hangi_basliktan FROM $tablo_cevaplar WHERE id='$_GET[kurtarc]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // cevap yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=55');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);
        // cevap zaten geri y�klenmi�se
        if ($fno['silinmis'] != 1)
        {
            header('Location: ../hata.php?hata=169');
            exit();
        }


        // cevap geri y�kleniyor
        $strSQL = "UPDATE $tablo_cevaplar SET silinmis=0 WHERE id='$_GET[kurtarc]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        //	forumun cevap say�s� artt�r�l�yor
        $strSQL = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        //	ba�l���n son cevab� �ekiliyor
        $strSQL = "SELECT id,tarih,cevap_yazan FROM $tablo_cevaplar WHERE silinmis=0 AND hangi_basliktan='$fno[hangi_basliktan]' ORDER BY tarih DESC LIMIT 1";
        $sonuc = mysql_query($strSQL);
        $son_mesaj = mysql_fetch_assoc($sonuc);


        //	ba�ka cevab� yoksa, ba�l�k tarihi son mesaj tarihi olarak giriliyor, cevap_sayi ve son_cevap s�f�r yap�l�yor, son_cevap_yazan siliniyor
        if (empty($son_mesaj['tarih']))
        {
            $strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=0, son_mesaj_tarihi=tarih, son_cevap=0, son_cevap_yazan=NULL WHERE id='$fno[hangi_basliktan]' LIMIT 1";
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        }

        //	cevap varsa, tarihi son mesaj tarihi olarak giriliyor, cevap say�s� bir artt�r�l�yor, cevap no ve cevap yazan giriliyor
        else
        {
            $strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=cevap_sayi + 1, son_mesaj_tarihi='$son_mesaj[tarih]', son_cevap='$son_mesaj[id]', son_cevap_yazan='$son_mesaj[cevap_yazan]' WHERE id='$fno[hangi_basliktan]' LIMIT 1";	
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        }

        if (is_numeric($_GET['ks']) == false) $_GET['ks'] = 0;

        header('Location: ../konu.php?k='.$fno['hangi_basliktan'].'&amp;ks='.$_GET['ks'].'#c'.$_GET['kurtarc']);
        exit();
    }

    else
    {
        header('Location: ../hata.php?hata=55');
        exit();
    }
}




//  BA�LIK KALICI S�LME ��LMELER�   //

elseif ( (isset($_GET['silk'])) AND ($_GET['silk'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['silk'])) $_GET['silk'] = @zkTemizle($_GET['silk']);


    if (is_numeric($_GET['silk']) == true)
    {
        //  OTURUM B�LG�S�NE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // ba�l���n bilgileri �ekiliyor
        $strSQL = "SELECT hangi_forumdan FROM $tablo_mesajlar WHERE id='$_GET[silk]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // ba�l�k yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=47');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);


        // ba�l�k siliniyor
        $strSQL = "DELETE FROM $tablo_mesajlar WHERE id='$_GET[silk]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        // ba�l���n cevaplar� varsa siliniyor
        $strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[silk]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

        header('Location: ../hata.php?bilgi=6&fno='.$fno['hangi_forumdan'].'&fsayfa=0');
        exit();
	}


    else
    {
        header('Location: ../hata.php?hata=47');
        exit();
    }
}




//  CEVAP KALICI S�LME ��LMELER�   //

elseif ( (isset($_GET['silc'])) AND ($_GET['silc'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['silc'])) $_GET['silc'] = @zkTemizle($_GET['silc']);


    if (is_numeric($_GET['silc']) == true)
    {
        //  OTURUM B�LG�S�NE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // cevab�n bilgileri �ekiliyor
        $strSQL = "SELECT hangi_basliktan FROM $tablo_cevaplar WHERE id='$_GET[silc]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // cevap yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=55');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);


        // ba�l�k siliniyor
        $strSQL = "DELETE FROM $tablo_cevaplar WHERE id='$_GET[silc]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        header('Location: ../hata.php?bilgi=8&mesaj_no='.$fno['hangi_basliktan'].'&fsayfa=0&sayfa=0');
        exit();
	}


    else
    {
        header('Location: ../hata.php?hata=55');
        exit();
    }
}






//  SAYFA NORMAL G�STER�M BA�I  //

$sayfa_adi = 'Y�netim Silinen �letiler';
include 'yonetim_baslik.php';



//  FORUM BA�LIKLARI �EK�L�YOR //

$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar";
$sonuc4 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

while ($forum_satir = mysql_fetch_assoc($sonuc4))
    $tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


//  S�L�NM�� KONULAR �EK�L�YOR   //

$strSQL = "SELECT id,yazan,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik,yazan,son_cevap_yazan FROM $tablo_mesajlar WHERE silinmis='1' ORDER BY son_mesaj_tarihi";
$sonuc10 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


//  S�L�NM�� CEVAPLAR �EK�L�YOR   //

$strSQL = "SELECT id,tarih,cevap_baslik,cevap_yazan,hangi_basliktan,hangi_forumdan FROM $tablo_cevaplar WHERE silinmis='1' ORDER BY tarih";
$sonuc11 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



//  KONULAR SIRALANIYOR //

if (mysql_num_rows($sonuc10))
{
    $sira = 1;

    while ($konular = mysql_fetch_assoc($sonuc10))
    {
        $konu_baslik = '<a href="konu_silinmis.php?k='.$konular['id'].'">'.$konular['mesaj_baslik'].'</a>';
        $forum_baslik = '<a href="../forum.php?f='.$konular['hangi_forumdan'].'">'.$tumforum_satir[$konular['hangi_forumdan']].'</a>';
        $konu_acan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';


        // cevap varsa
        if ($konular['cevap_sayi'] == 0)
        $konu_sonyazan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';

        // cevap yoksa
        else $konu_sonyazan = '<a href="../profil.php?kim='.$konular['son_cevap_yazan'].'">'.$konular['son_cevap_yazan'].'</a>';

        $konu_sontarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['son_mesaj_tarihi']);


        //	veriler tema motoruna yollan�yor	//

        $tekli1[] = array('{SIRA}' => $sira,
        '{KONU_BASLIK}' => $konu_baslik,
        '{FORUM_BASLIK}' => $forum_baslik,
        '{KONU_ACAN}' => $konu_acan,
        '{CEVAP_SAYI}' => $konular['cevap_sayi'],
        '{GOSTERIM}' => $konular['goruntuleme'],
        '{SON_YAZAN}' => $konu_sonyazan,
        '{TARIH}' => $konu_sontarih);


        $sira++;
    }
}




//  CEVAPLAR SIRALANIYOR //

if (mysql_num_rows($sonuc11))
{
    $sira = 1;

    while ($cevaplar = mysql_fetch_assoc($sonuc11))
    {
        // cevab�n konusunun bilgileri �ekiliyor
        $strSQL = "SELECT id,cevap_sayi,goruntuleme,mesaj_baslik,silinmis FROM $tablo_mesajlar WHERE id='$cevaplar[hangi_basliktan]'";
        $sonuc12 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        $konular = mysql_fetch_assoc($sonuc12);


        // cevab�n konusu silinmi�se cevap b�l�m�nde g�sterme
        if ($konular['silinmis'] == 1) continue;


        // cevab�n ka��nc� s�rada oldu�u hesaplan�yor
        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$cevaplar[hangi_basliktan]' AND id < $cevaplar[id]") or die ('<h2>sorgu ba�ar�s�z</h2>');
        $cavabin_sirasi = mysql_num_rows($result);

        $sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
        settype($sayfaya_git,'integer');
        $sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

        if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
        else $sayfaya_git = '';


        // ba�lant�lar olu�turuluyor
        $cevap_baslik = '<a href="konu_silinmis.php?k='.$konular['id'].$sayfaya_git.'#c'.$cevaplar['id'].'">'.$konular['mesaj_baslik'].' &raquo; '.$cevaplar['cevap_baslik'].'</a>';
        $forum_baslik = '<a href="../forum.php?f='.$cevaplar['hangi_forumdan'].'">'.$tumforum_satir[$cevaplar['hangi_forumdan']].'</a>';
        $cevap_yazan = '<a href="../profil.php?kim='.$cevaplar['cevap_yazan'].'">'.$cevaplar['cevap_yazan'].'</a>';

        $cevap_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevaplar['tarih']);


        //	veriler tema motoruna yollan�yor	//

        $tekli2[] = array('{SIRA}' => $sira,
        '{CEVAP_BASLIK}' => $cevap_baslik,
        '{FORUM_BASLIK}' => $forum_baslik,
        '{CEVAP_YAZAN}' => $cevap_yazan,
        '{CEVAP_SAYI}' => $konular['cevap_sayi'],
        '{GOSTERIM}' => $konular['goruntuleme'],
        '{TARIH}' => $cevap_tarihi);


        $sira++;
    }
}




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/silinmis.html');



if (isset($tekli1))
{
	$ornek1->tekli_dongu('1',$tekli1);
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), true);
}

else
{
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('' => ''), true);
}



if (isset($tekli2))
{
	$ornek1->tekli_dongu('2',$tekli2);
	$ornek1->kosul('3', array('' => ''), false);
	$ornek1->kosul('4', array('' => ''), true);
}

else
{
	$ornek1->kosul('4', array('' => ''), false);
	$ornek1->kosul('3', array('' => ''), true);
}

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Silinen �letiler'));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>