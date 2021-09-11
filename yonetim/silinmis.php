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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// site kurucusu deðilse hata ver
if ($kullanici_kim['id'] != 1)
{
    header('Location: ../hata.php?hata=151');
    exit();
}


// OTURUM KODU ÝÞLEMLERÝ  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


//  BAÞLIÐI GERÝ YÜKLEME ÝÞLEMLERÝ   //

if ( (isset($_GET['kurtark'])) AND ($_GET['kurtark'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['kurtark'])) $_GET['kurtark'] = @zkTemizle($_GET['kurtark']);


    if (is_numeric($_GET['kurtark']) == true)
    {
        //  OTURUM BÝLGÝSÝNE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }

        // baþlýðýn bilgileri çekiliyor
        $strSQL = "SELECT hangi_forumdan,silinmis FROM $tablo_mesajlar WHERE id='$_GET[kurtark]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

        // baþlýk yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=47');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);
        // baþlýk zaten geri yüklenmiþse
        if ($fno['silinmis'] != 1)
        {
            header('Location: ../hata.php?hata=168');
            exit();
        }


        // baþlýðýn silinen cevaplarý varsa döngüye sokularak teker teker geri yükleniyor
        $strSQL1 = "SELECT id FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[kurtark]' ORDER BY id DESC";
        $sonuc_konu = mysql_query($strSQL1) or die ('<h2>sorgu baþarýsýz</h2>');


        $toplam_cevap = 0;

        while ($cevaplari_yukle = mysql_fetch_assoc($sonuc_konu))
        {
            $strSQL2 = "UPDATE $tablo_cevaplar SET silinmis=0 WHERE id='$cevaplari_yukle[id]' LIMIT 1";
            $sonuc = mysql_query($strSQL2) or die ('<h2>sorgu baþarýsýz</h2>');


            // forumun cevap sayisi arttýrýlýyor
            $strSQL3 = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
            $sonuc3 = mysql_query($strSQL3) or die ('<h2>sorgu baþarýsýz</h2>');
            $toplam_cevap++;
        }


        //	baþlýðýn son cevabý çekiliyor
        $strSQL = "SELECT id,tarih,cevap_yazan FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[kurtark]' ORDER BY tarih DESC LIMIT 1";
        $sonuc = mysql_query($strSQL);
        $son_mesaj = mysql_fetch_assoc($sonuc);


        // cevabý yoksa
        if (empty($son_mesaj['tarih']))
            $strSQL = "UPDATE $tablo_mesajlar SET silinmis=0, cevap_sayi=0, son_mesaj_tarihi=tarih, son_cevap=0, son_cevap_yazan=NULL WHERE id='$_GET[kurtark]'";

        // cevabý varsa
        else $strSQL = "UPDATE $tablo_mesajlar SET silinmis=0, cevap_sayi='$toplam_cevap', son_mesaj_tarihi='$son_mesaj[tarih]', son_cevap='$son_mesaj[id]', son_cevap_yazan='$son_mesaj[cevap_yazan]' WHERE id='$_GET[kurtark]'";

        // konu geri yükleniyor, son mesaj tarihi ve son cevap bilgileri güncelleniyor
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        //	forumun konu sayýsý arttýrýlýyor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        header('Location: ../konu.php?k='.$_GET['kurtark']);
        exit();
    }

    else
    {
        header('Location: ../hata.php?hata=47');
        exit();
    }
}




//  CEVABI GERÝ YÜKLEME ÝÞLEMLERÝ   //

elseif ( (isset($_GET['kurtarc'])) AND ($_GET['kurtarc'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['kurtarc'])) $_GET['kurtarc'] = @zkTemizle($_GET['kurtarc']);


    if (is_numeric($_GET['kurtarc']) == true)
    {
        //  OTURUM BÝLGÝSÝNE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // cevabýn bilgileri çekiliyor
        $strSQL = "SELECT hangi_forumdan,silinmis,hangi_basliktan FROM $tablo_cevaplar WHERE id='$_GET[kurtarc]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // cevap yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=55');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);
        // cevap zaten geri yüklenmiþse
        if ($fno['silinmis'] != 1)
        {
            header('Location: ../hata.php?hata=169');
            exit();
        }


        // cevap geri yükleniyor
        $strSQL = "UPDATE $tablo_cevaplar SET silinmis=0 WHERE id='$_GET[kurtarc]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        //	forumun cevap sayýsý arttýrýlýyor
        $strSQL = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$fno[hangi_forumdan]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        //	baþlýðýn son cevabý çekiliyor
        $strSQL = "SELECT id,tarih,cevap_yazan FROM $tablo_cevaplar WHERE silinmis=0 AND hangi_basliktan='$fno[hangi_basliktan]' ORDER BY tarih DESC LIMIT 1";
        $sonuc = mysql_query($strSQL);
        $son_mesaj = mysql_fetch_assoc($sonuc);


        //	baþka cevabý yoksa, baþlýk tarihi son mesaj tarihi olarak giriliyor, cevap_sayi ve son_cevap sýfýr yapýlýyor, son_cevap_yazan siliniyor
        if (empty($son_mesaj['tarih']))
        {
            $strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=0, son_mesaj_tarihi=tarih, son_cevap=0, son_cevap_yazan=NULL WHERE id='$fno[hangi_basliktan]' LIMIT 1";
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        }

        //	cevap varsa, tarihi son mesaj tarihi olarak giriliyor, cevap sayýsý bir arttýrýlýyor, cevap no ve cevap yazan giriliyor
        else
        {
            $strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=cevap_sayi + 1, son_mesaj_tarihi='$son_mesaj[tarih]', son_cevap='$son_mesaj[id]', son_cevap_yazan='$son_mesaj[cevap_yazan]' WHERE id='$fno[hangi_basliktan]' LIMIT 1";	
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
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




//  BAÞLIK KALICI SÝLME ÝÞLMELERÝ   //

elseif ( (isset($_GET['silk'])) AND ($_GET['silk'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['silk'])) $_GET['silk'] = @zkTemizle($_GET['silk']);


    if (is_numeric($_GET['silk']) == true)
    {
        //  OTURUM BÝLGÝSÝNE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // baþlýðýn bilgileri çekiliyor
        $strSQL = "SELECT hangi_forumdan FROM $tablo_mesajlar WHERE id='$_GET[silk]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // baþlýk yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=47');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);


        // baþlýk siliniyor
        $strSQL = "DELETE FROM $tablo_mesajlar WHERE id='$_GET[silk]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        // baþlýðýn cevaplarý varsa siliniyor
        $strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[silk]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

        header('Location: ../hata.php?bilgi=6&fno='.$fno['hangi_forumdan'].'&fsayfa=0');
        exit();
	}


    else
    {
        header('Location: ../hata.php?hata=47');
        exit();
    }
}




//  CEVAP KALICI SÝLME ÝÞLMELERÝ   //

elseif ( (isset($_GET['silc'])) AND ($_GET['silc'] != '') )
{
    if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
    if (isset($_GET['silc'])) $_GET['silc'] = @zkTemizle($_GET['silc']);


    if (is_numeric($_GET['silc']) == true)
    {
        //  OTURUM BÝLGÝSÝNE BAKILIYOR  //
        if ($_GET['o'] != $o)
        {
            header('Location: ../hata.php?hata=45');
            exit();
        }


        // cevabýn bilgileri çekiliyor
        $strSQL = "SELECT hangi_basliktan FROM $tablo_cevaplar WHERE id='$_GET[silc]' LIMIT 1";
        $sonuc = mysql_query($strSQL);

        // cevap yoksa
        if (!mysql_num_rows($sonuc))
        {
            header('Location: ../hata.php?hata=55');
            exit();
        }

        $fno = mysql_fetch_assoc($sonuc);


        // baþlýk siliniyor
        $strSQL = "DELETE FROM $tablo_cevaplar WHERE id='$_GET[silc]'";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        header('Location: ../hata.php?bilgi=8&mesaj_no='.$fno['hangi_basliktan'].'&fsayfa=0&sayfa=0');
        exit();
	}


    else
    {
        header('Location: ../hata.php?hata=55');
        exit();
    }
}






//  SAYFA NORMAL GÖSTERÝM BAÞI  //

$sayfa_adi = 'Yönetim Silinen Ýletiler';
include 'yonetim_baslik.php';



//  FORUM BAÞLIKLARI ÇEKÝLÝYOR //

$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar";
$sonuc4 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

while ($forum_satir = mysql_fetch_assoc($sonuc4))
    $tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


//  SÝLÝNMÝÞ KONULAR ÇEKÝLÝYOR   //

$strSQL = "SELECT id,yazan,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik,yazan,son_cevap_yazan FROM $tablo_mesajlar WHERE silinmis='1' ORDER BY son_mesaj_tarihi";
$sonuc10 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


//  SÝLÝNMÝÞ CEVAPLAR ÇEKÝLÝYOR   //

$strSQL = "SELECT id,tarih,cevap_baslik,cevap_yazan,hangi_basliktan,hangi_forumdan FROM $tablo_cevaplar WHERE silinmis='1' ORDER BY tarih";
$sonuc11 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');



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


        //	veriler tema motoruna yollanýyor	//

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
        // cevabýn konusunun bilgileri çekiliyor
        $strSQL = "SELECT id,cevap_sayi,goruntuleme,mesaj_baslik,silinmis FROM $tablo_mesajlar WHERE id='$cevaplar[hangi_basliktan]'";
        $sonuc12 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        $konular = mysql_fetch_assoc($sonuc12);


        // cevabýn konusu silinmiþse cevap bölümünde gösterme
        if ($konular['silinmis'] == 1) continue;


        // cevabýn kaçýncý sýrada olduðu hesaplanýyor
        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$cevaplar[hangi_basliktan]' AND id < $cevaplar[id]") or die ('<h2>sorgu baþarýsýz</h2>');
        $cavabin_sirasi = mysql_num_rows($result);

        $sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
        settype($sayfaya_git,'integer');
        $sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

        if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
        else $sayfaya_git = '';


        // baðlantýlar oluþturuluyor
        $cevap_baslik = '<a href="konu_silinmis.php?k='.$konular['id'].$sayfaya_git.'#c'.$cevaplar['id'].'">'.$konular['mesaj_baslik'].' &raquo; '.$cevaplar['cevap_baslik'].'</a>';
        $forum_baslik = '<a href="../forum.php?f='.$cevaplar['hangi_forumdan'].'">'.$tumforum_satir[$cevaplar['hangi_forumdan']].'</a>';
        $cevap_yazan = '<a href="../profil.php?kim='.$cevaplar['cevap_yazan'].'">'.$cevaplar['cevap_yazan'].'</a>';

        $cevap_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevaplar['tarih']);


        //	veriler tema motoruna yollanýyor	//

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

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Silinen Ýletiler'));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>