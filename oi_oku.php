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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';


// �zel ileti �zelli�i kapal�ysa
if ($ayarlar['o_ileti'] == 0)
{
    header('Location: hata.php?uyari=2');
    exit();
}


if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


$_GET['oino'] = zkTemizle($_GET['oino']);


// �zel iletinin �ekiliyor
$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE id='$_GET[oino]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$ozel_ileti = mysql_fetch_array($sonuc);

if (($ozel_ileti['kime'] != $kullanici_kim['kullanici_adi']) AND ($ozel_ileti['kimden'] != $kullanici_kim['kullanici_adi']))
{
    header('Location: hata.php?hata=62');
    exit();
}


// sadece gelen iletinin okunma tarihi yoksa okundu bilgisi giriliyor
if ((!$ozel_ileti['okunma_tarihi']) AND ($ozel_ileti['kime'] == $kullanici_kim['kullanici_adi']))
{
    $tarih = time();

    $strSQL = "UPDATE $tablo_ozel_ileti SET okunma_tarihi='$tarih' WHERE id='$_GET[oino]' LIMIT 1";
    $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


    // g�nderen iletiyi silmi�se ula�an kutusuna ta��
    if ($ozel_ileti['gonderen_kutu'] != '0')
    {
        // g�nderenin ula�an kutusunun doluluk oran�na bak�l�yor
        $result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$ozel_ileti[kimden]' AND gonderen_kutu='2'");
        $num_rows = mysql_num_rows($result);

        if (($num_rows + 1) > $ayarlar['ulasan_kutu_kota'])
        {
            $strSQL = "SELECT id FROM $tablo_ozel_ileti WHERE kimden='$ozel_ileti[kimden]' AND gonderen_kutu='2' ORDER BY okunma_tarihi LIMIT 1";
            $sonuc = mysql_query($strSQL);
            $satir = mysql_fetch_array($sonuc);

            // kutudaki en eski ileti siliior
            $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$satir[id]' LIMIT 1";
            $sonuc = mysql_query($strSQL);
        }

        $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='2',okunma_tarihi='$tarih' WHERE id='$ozel_ileti[id]' LIMIT 1";
    }

    else $strSQL = "UPDATE $tablo_ozel_ileti SET okunma_tarihi='$tarih' WHERE id='$ozel_ileti[id]' LIMIT 1";

    $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


    // okunmam�� �zel ileti say�s� s�f�r de�ilse say�y� eksilt
    if ($kullanici_kim['okunmamis_oi'] != 0)
    {
        $strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi-1 WHERE id='$kullanici_kim[id]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
    }
}


$sayfano = 21;
$sayfa_adi = '�zel ileti Okuma';
include 'kullanici_kimlik.php';
include 'baslik.php';


// g�nderenin yetkisine bak�l�yor
$strSQL = "SELECT id,yetki FROM $tablo_kullanicilar WHERE kullanici_adi='$ozel_ileti[kimden]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$kimden_yetki = mysql_fetch_assoc($sonuc);


$oi_kimden = '<a href="profil.php?kim='.$ozel_ileti['kimden'].'" title="Kullan�c� profilini g�r�nt�le">'.$ozel_ileti['kimden'].'</a>';


// g�nderen y�netici, yard�mc� veya kendisi de�ilse
if ( ($kimden_yetki['yetki'] == 0) AND ($ozel_ileti['kimden'] != $kullanici_kim['kullanici_adi']) )
    $oi_kimden .= '&nbsp; &nbsp; &nbsp; <a href="ozel_ileti.php?kip=ayarlar&amp;kim='.$ozel_ileti['kimden'].'">[ Bu ki�iyi engelle ]</a>';


$oi_kime = '<a href="profil.php?kim='.$ozel_ileti['kime'].'" title="Kullan�c� profilini g�r�nt�le">'.$ozel_ileti['kime'].'</a>';


$oi_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $ozel_ileti['gonderme_tarihi']);



if ($ozel_ileti['ifade'] == 1)
    $ozel_ileti['ozel_icerik'] = ifadeler($ozel_ileti['ozel_icerik']);

if (($ozel_ileti['bbcode_kullan'] == 1) and ($ayarlar['bbcode'] == 1))
    $oi_icerik = bbcode_acik($ozel_ileti['ozel_icerik'],1);

else $oi_icerik = bbcode_kapali($ozel_ileti['ozel_icerik']);



$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2012 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  T�m haklar� sakl�d�r - All Rights Reserved

function hepsiniSec(kodCizelgesi){if(document.selection){var secim=document.body.createTextRange();secim.moveToElementText(document.getElementById(kodCizelgesi));secim.select();}else if(window.getSelection){var secim=document.createRange();secim.selectNode(document.getElementById(kodCizelgesi));window.getSelection().addRange(secim);}else if(document.createRange && (document.getSelection || window.getSelection)){secim=document.createRange();secim.selectNodeContents(document.getElementById(kodCizelgesi));a=window.getSelection ? window.getSelection() : document.getSelection();a.removeAllRanges();a.addRange(secim);}}function ResimBuyut(resim,ratgele,en,boy,islem){var katman=document.getElementById(ratgele);if(islem=="buyut"){resim.width=en;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"kucult")};katman.style.width=(en-12)+"px";katman.innerHTML="K���ltmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-out";}else if(islem=="kucult"){resim.width=600;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"buyut")};katman.style.width="588px";katman.innerHTML="B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}else if(islem=="ac")window.open(resim,"_blank","scrollbars=yes,left=1,top=1,width="+(en+40)+",height="+(boy+30)+",resizable=yes");}function ResimBoyutlandir(resim){if(resim.width>"600"){var en=resim.width;var boy=resim.height;var adres=resim.src;var rastgele="resim_boyut_"+Math.random();oyazi=document.createTextNode("B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+resim.width+"x"+resim.height+")");okatman=document.createElement("div");okatman.id=rastgele;okatman.className="resim_boyutlandir";okatman.align="left";okatman.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";okatman.style.cursor="pointer";okatman.onclick=function(){ResimBuyut(adres,rastgele,en,boy,"ac")};okatman.textNode=oyazi;okatman.appendChild(oyazi);resim.onclick=function(){ResimBuyut(resim,rastgele,en,boy,"buyut")};resim.width="600";resim.border="1";resim.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";resim.parentNode.insertBefore(okatman, resim);if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}}
//  --></script>';



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/oi_oku.html');


$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu,
'{OI_NO}' => $_GET['oino'],
'{OZEL_ILETI_GONDER}' => $oi_rengi,
'{OI_KIMDEN}' => $oi_kimden,
'{OI_KIME}' => $oi_kime,
'{OZEL_ILET_BASLIK}' => $ozel_ileti['ozel_baslik'],
'{OI_TARIH}' => $oi_tarih,
'{OI_ICERIK}' => $oi_icerik));


if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>