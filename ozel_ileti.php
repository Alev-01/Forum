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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


// özel ileti özelliði kapalýysa
if ($ayarlar['o_ileti'] == 0)
{
    header('Location: hata.php?uyari=2');
    exit();
}


$javascript_kodu2 = '<script type="text/javascript">
<!-- 
function secim(ne){
var neresi;for (i=0, tablono=1; i < document.secim_formu.length; i++,tablono++){
document.secim_formu.elements[i].checked = ne;
neresi = document.getElementById("secili"+tablono);
if ( (ne == false) && (neresi != null) )
neresi.style.backgroundColor = "#ffffff";
if ( (ne == true) && (neresi != null) )
neresi.style.backgroundColor = "#e0e0e0";}}

function secili_yap(tablono){
var neresi = document.getElementById("secili"+tablono);
if (document.secim_formu.elements[tablono].checked == false)
neresi.style.backgroundColor = "#ffffff";
else
neresi.style.backgroundColor = "#e0e0e0";}
//  -->
</script>';


// DUYURU BÝLGÝLERÝ ÇEKÝLÝYOR //

$strSQL = "SELECT * FROM $tablo_duyurular WHERE fno='ozel' ORDER BY id";
$duyuru_sonuc = mysql_query($strSQL) or die ('<h2>duyuru sorgu baþarýsýz</h2>');


// DUYURU VARSA DÖNGÜYE GÝRÝLÝYOR //

if (mysql_num_rows($duyuru_sonuc)) 
{
	while ($duyurular = mysql_fetch_assoc($duyuru_sonuc))
	{
		$tekli2[] = array('{OZEL_DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{OZEL_DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);
	}
}





//  ENGELLEME ÝÞLEMLERÝ - BAÞI  //

if ( (isset($_POST['kip'])) AND ($_POST['kip'] == 'engel') ):

    $_POST['engel_tipi'] = @zkTemizle($_POST['engel_tipi']);
    if (!preg_match('/^[0-2]+$/', $_POST['engel_tipi'])) $_POST['engel_tipi'] = 0;
    $dogru_kuladlar = '';


    // Tip sýfýr deðilse üye adlarýný denetle
    if ($_POST['engel_tipi'] != '0')
    {
        // deðiþkendeki veriler satýr satýr ayrýlýp dizi deðiþkene aktarýlýyor //
        $yasak_kulad_bosluk = explode("\r\n", $_POST['engellenenler']);

        // satýr sayýsý alýnýyor //
        $yasak_kulad_sayi = count($yasak_kulad_bosluk);

        // dizideki satýrlar döngüye sokuluyor //
        for ($d=0,$a=0; $d < $yasak_kulad_sayi; $d++)
        {
            $yasak_kulad_bosluk[$d] = @zkTemizle(trim($yasak_kulad_bosluk[$d]));

            // 3 karakterden kýsa ve ayný olan isimler diziden atýlýyor	//
            if ( (strlen($yasak_kulad_bosluk[$d]) > 3) AND (!preg_match("/$yasak_kulad_bosluk[$d],/i", $dogru_kuladlar)) )
            {
                // kullanýcý adý denetleniyor
                $strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$yasak_kulad_bosluk[$d]' AND yetki='0' AND id!=$kullanici_kim[id] LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
                $satir = mysql_fetch_array($sonuc);

                if (isset($satir['kullanici_adi'])) $dogru_kuladlar .= $satir['kullanici_adi'].',';
                $a++;
            }
        }
    }


    // kullanýcýnýn engelleme girdileri çekiliyor
    $strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='$kullanici_kim[id]' LIMIT 1";
    $sonuc = mysql_query($strSQL);


    // kullanýcýya ait girdi varsa
    if (mysql_num_rows($sonuc))
    {
        // tip sýfýr deðilse yasaklar tablosuna girdi yapýlýyor
        if ($_POST['engel_tipi'] != '0')
            $strSQL = "UPDATE $tablo_yasaklar SET deger='$dogru_kuladlar', tip='$_POST[engel_tipi]' where etiket='$kullanici_kim[id]' LIMIT 1";

        // tip sýfýr ise yasaklar tablosundaki girdi siliniyor
        else $strSQL = "DELETE FROM $tablo_yasaklar WHERE etiket='$kullanici_kim[id]' LIMIT 1";

        $sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');
    }


    // kullanýcýya ait girdi yoksa
    else
    {
        // tip sýfýr deðilse yasaklar tablosuna girdi yapýlýyor
        if ($_POST['engel_tipi'] != '0')
        {
            $strSQL = "INSERT INTO $tablo_yasaklar (etiket, deger, tip)";
            $strSQL .= "VALUES ('$kullanici_kim[id]', '$dogru_kuladlar', '$_POST[engel_tipi]')";
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        }
    }

    header('Location: hata.php?bilgi=46');
    exit();


//  ENGELLEME ÝÞLEMLERÝ - SONU  //




//  ÝLETÝ SÝLME ÝÞLEMLERÝ - BAÞI  //

elseif ( (isset($_POST['secili_sil'])) AND ($_POST['secili_sil'] != '') ):

    // seçim yapýlmamýþsa
    if (!isset($_POST['sec_ileti']))
    {
        header('Location: hata.php?hata=68');
        exit();
    }


    foreach ($_POST['sec_ileti'] as $sec_ileti_sil)
    {
        $sec_ileti_sil = zkTemizle($sec_ileti_sil);

        $strSQL = "SELECT okunma_tarihi,kimden,kime,alan_kutu,gonderen_kutu FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";
        $sonuc = mysql_query($strSQL);
        $ozel_ileti = mysql_fetch_array($sonuc);


        // yolladýðý bir özel ilet ise
        if (($ozel_ileti['kimden'] == $kullanici_kim['kullanici_adi']))
        {
            // ileti okunmadan siliniyorsa
            if ( (!$ozel_ileti['okunma_tarihi']) AND ($kullanici_kim['okunmamis_oi'] != '0') )
            {
                $strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi-1 WHERE id='$kullanici_kim[id]' LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
            }


            // kendine yolladýðý bir özel ileti ise gerçekten sil
            if ($ozel_ileti['kimden'] == $ozel_ileti['kime']) $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // iletiyi alan kiþi de silmiþse gerçekten sil
            elseif ($ozel_ileti['alan_kutu'] == '0') $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // sadece gonderen kutusunu sýfýrla
            else $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='0' WHERE id='$sec_ileti_sil' LIMIT 1";

            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        }


        // aldýðý bir özel ilet ise
        elseif (($ozel_ileti['kime'] == $kullanici_kim['kullanici_adi']))
        {
            // ileti okunmadan siliniyorsa
            if ( (!$ozel_ileti['okunma_tarihi']) AND ($kullanici_kim['okunmamis_oi'] != '0') )
            {
                $strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi-1 WHERE id='$kullanici_kim[id]' LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
            }


            // iletiyi gönderen kiþi de silmiþse gerçekten sil
            if ($ozel_ileti['gonderen_kutu'] == '0') $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // sadece alan kutusunu sýfýrla
            else $strSQL = "UPDATE $tablo_ozel_ileti SET alan_kutu='0' WHERE id='$sec_ileti_sil' LIMIT 1";

            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        }

        // silme yetkisi yoksa
        else
        {
            header('Location: hata.php?hata=69');
            exit();
        }
    }


    // gelinen sayfaya geri dönülüyor

    if ($_POST['git'] == 'ozel_ileti') $git = 'ozel_ileti.php';
    elseif ($_POST['git'] == 'ulasan') $git = 'ozel_ileti.php?kip=ulasan';
    elseif ($_POST['git'] == 'gonderilen') $git = 'ozel_ileti.php?kip=gonderilen';
    elseif ($_POST['git'] == 'kaydedilen') $git = 'ozel_ileti.php?kip=kaydedilen';
    else $git = 'ozel_ileti.php';

    header('Location: '.$git);
    exit();


//  ÝLETÝ SÝLME ÝÞLEMLERÝ - SONU  //




//  ÝLETÝ KAYDETME ÝÞLEMLERÝ - BAÞI //

elseif (isset($_POST['secili_kaydet'])):

    // seçim yapýlmamýþsa
    if (!isset($_POST['sec_ileti']))
    {
        header('Location: hata.php?hata=68');
        exit();
    }

    $result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4'");
    $num_rows = mysql_num_rows($result);


    // seçilen iletiler kaydedilen kutusundaki boþluktan fazla ise
    if (($num_rows + count($_POST['sec_ileti'])) > $ayarlar['kaydedilen_kutu_kota'])
    {
        header('Location: hata.php?hata=70');
        exit();
    }


    foreach ($_POST['sec_ileti'] as $sec_ileti_kaydet)
    {
        $sec_ileti_kaydet = zkTemizle($sec_ileti_kaydet);

        $strSQL = "SELECT kime,kimden,alan_kutu,gonderen_kutu FROM $tablo_ozel_ileti WHERE id='$sec_ileti_kaydet' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        $ozel_ileti = mysql_fetch_array($sonuc);


        // yolladýðý bir özel ilet ise
        if (($ozel_ileti['kimden'] == $kullanici_kim['kullanici_adi']))
        {
            // kendine yolladýðý bir özel ileti ise gerçekten sil
            if ($ozel_ileti['kimden'] == $ozel_ileti['kime'])
                $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='4',alan_kutu='4' WHERE id='$sec_ileti_kaydet' LIMIT 1";

            else $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='4' WHERE id='$sec_ileti_kaydet' LIMIT 1";
            $sonuc = mysql_query($strSQL);
        }

        // aldýðý bir özel ilet ise
        elseif (($ozel_ileti['kime'] == $kullanici_kim['kullanici_adi']))
        {
            $strSQL = "UPDATE $tablo_ozel_ileti SET alan_kutu='4' WHERE id='$sec_ileti_kaydet' LIMIT 1";
            $sonuc = mysql_query($strSQL);
        }

        // kaydetme yetkisi yoksa
        else
        {
            header('Location: hata.php?hata=71');
            exit();
        }
    }


    header('Location: ozel_ileti.php?kip=kaydedilen');
    exit();

//  ÝLETÝ KAYDETME ÝÞLEMLERÝ - SONU //









// ÖZEL ÝLETÝ KUTULARI GÖRÜTÜLENÝYOR - BAÞI //
// ÖZEL ÝLETÝ KUTULARI GÖRÜTÜLENÝYOR - BAÞI //

elseif (isset($_GET['kip'])):



//  AYARLAR SAYFASI GÖRÜNTÜLENÝYOR - BAÞI  //

if ($_GET['kip'] == 'ayarlar')
{
$sayfano = 24;
$sayfa_adi = 'Özel ileti Ayarlarý';
include 'baslik.php';


if ($kullanici_kim['okunmamis_oi']) $okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
else $okunmamis_oi = '';


// kullanýcýnýn engelleme girdileri çekiliyor
$strSQL = "SELECT * FROM $tablo_yasaklar WHERE etiket='$kullanici_kim[id]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$satir = mysql_fetch_array($sonuc);


// engelleme tipi belirleniyor
if (isset($satir['tip']))
{
	$tip_hickimse = '';

	if ($satir['tip'] == '1') $tip_herkes = 'checked="checked"';
	else $tip_herkes = '';

	if ($satir['tip'] == '2') $tip_sadece = 'checked="checked"';
	else $tip_sadece = '';
}

else
{
	$tip_hickimse = 'checked="checked"';
	$tip_herkes = '';
	$tip_sadece = '';
}


$satir['deger'] = @str_replace(',', "\r\n", $satir['deger']);
$satir['deger'] = @preg_replace('|\r\n$|si','',$satir['deger']);


if ( (isset($_GET['kim'])) AND ($_GET['kim'] != '') )
{
	$_GET['kim'] = @zkTemizle($_GET['kim']);
	$_GET['kim'] = @zkTemizle4($_GET['kim']);

	if ( (isset($satir['deger'])) AND ($satir['deger'] != '') ) $engellenenler = $satir['deger']."\r\n".$_GET['kim'];
	else $engellenenler = $_GET['kim'];

	$euyari= '<br><br><br><p align="center"><font style="color: #FF6600; font-weight: bolder;">Önceki sayfadan týkladýðýnýz " <u>'.$_GET['kim'].'</u> " üye adý<br>aþaðýdaki alana eklenmiþtir.<br><br>Uygulamak için " *Sadece alttakileri engelle "<br>seçeneðini seçip "Deðiþtir" düðmesini týklayýn.</font></p>';
}

else
{
	if ( (isset($satir['deger'])) AND ($satir['deger'] != '') ) $engellenenler = $satir['deger'];
	else $engellenenler = '';
	$euyari= '';
}


$form_bilgi2 = '<form name="engelle" action="ozel_ileti.php" method="post">
<input type="hidden" name="kip" value="engel">';


// tema sýnýfý örneði oluþturuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa koþul 8 alaný tekli döngüye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


// tema uygulanýyor
$ornek1->kosul('5', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('6', array('' => ''), true);

$dongusuz = array('{FORM_BILGI2}' => $form_bilgi2,
'{OKUNMAMIS_OI}' => $okunmamis_oi,
'{EUYARI}' => $euyari,
'{TIP_HICKIMSE}' => $tip_hickimse,
'{TIP_HERKES}' => $tip_herkes,
'{TIP_SADECE}' => $tip_sadece,
'{ENGELLENENLER}' => $engellenenler);

$ornek1->dongusuz($dongusuz);
if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}

//  AYARLAR SAYFASI GÖRÜNTÜLENÝYOR - SONU  //





//  ULAÞAN KUTUSU GÖRÜNTÜLENÝYOR - BAÞI  //

elseif ($_GET['kip'] == 'ulasan')
{
$sayfano = 25;
$sayfa_adi = 'Özel iletiler Ulaþan Kutusu';
include 'baslik.php';


//	 ULAÞAN ÝLETÝLER OKUNMA TARÝH SIRASINA GÖRE ÇEKÝLÝYOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='2' ORDER BY okunma_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	ULAÞAN ÝLETÝLERÝN SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='2'");
$num_rows = mysql_num_rows($result);


// tema sýnýfý örneði oluþturuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa koþul 8 alaný tekli döngüye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL ÝLETÝ YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'Ulaþan Kutusunda hiç iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL ÝLETÝ VARSA	//

else
{
	$tablono = 0;

	$ornek1->kosul('2', array('' => ''), true);
	$ornek1->kosul('1', array('' => ''), false);


	while ($satir = mysql_fetch_array($sonuc))
	{
		$tablono++;
		$oi_baslik = '<a href="oi_oku.php?oino='.$satir['id'].'">'.$satir['ozel_baslik'].'</a>';
		$oi_kime = '<a href="profil.php?kim='.$satir['kime'].'">'.$satir['kime'].'</a>';
		$oi_gonderme_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['gonderme_tarihi']);
		$oi_okunma_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['okunma_tarihi']);


		//	veriler tema motoruna yollanýyor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kime,
		'{OI_TARIH1}' => $oi_gonderme_tarih,
		'{OI_TARIH2}' => $oi_okunma_tarihi);
	}
}


//	DOLULUK ORANI YÜZDESÝ HESAPLANIYOR	//

if ($num_rows != 0)
{
	$doluluk_orani = 100 / ($ayarlar['ulasan_kutu_kota'] / $num_rows);
	settype($doluluk_orani,'integer');
	if ($doluluk_orani > 100) $doluluk_orani = 100;
}

else $doluluk_orani = 1;


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="ulasan">';

$kutu_aciklama = 'Yolladýðýnýz iletiler gönderilen tarafýndan okunduðunda buraya taþýnýr.
<br>Ýletinin okunma tarihini yukarýda görebilirsiniz.';



//	TEMA UYGULANIYOR	//

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), true);
$ornek1->kosul('3', array('{TARIH_ALAN2}' => 'Okunma Tarihi'), true);
$ornek1->kosul('4', array('' => ''), true);
$ornek1->kosul('7', array('' => ''), true);

if (isset($tekli1)) $ornek1->tekli_dongu('1',$tekli1);

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu2,
'{KUTU_KOTA}' => $ayarlar['ulasan_kutu_kota'],
'{DOLULUK}' => $num_rows,
'{OZEL_ILETI_GONDER}' => $oi_rengi,
'{DOLULUK_ORANI}' => $doluluk_orani,
'{FORM_BILGI}' => $form_bilgi,
'{HUCRE_SAYISI}' => '5',
'{HUCRE_DEGER}' => '275',
'{KIMDEN_KIME}' => 'Kime',
'{GELEN_KUTUSU}' => 'Gelen Kutusu',
'{ULASAN_KUTUSU}' => 'Ulaþan Kutusu ',
'{GONDERILEN_KUTUSU}' => 'Gönderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php">',
'{GELEN_KUTUSU_BAG2}' => '</a>',
'{ULASAN_KUTUSU_BAG}' => '',
'{ULASAN_KUTUSU_BAG2}' => '',
'{GONDERILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=gonderilen">',
'{GONDERILEN_KUTUSU_BAG2}' => '</a>',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'Gönderme Tarihi',
'{KUTU_ACIKLAMA}' => $kutu_aciklama));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}

//  ULAÞAN KUTUSU GÖRÜNTÜLENÝYOR - SONU  //





//  GÖNDERÝLEN KUTUSU GÖRÜNTÜLENÝYOR - BAÞI  //

elseif ($_GET['kip'] == 'gonderilen')
{
$sayfano = 26;
$sayfa_adi = 'Özel iletiler Gönderilen Kutusu';
include 'baslik.php';


//	GÖNDERÝLEN ÝLETÝLER TARÝH SIRASINA GÖRE ÇEKÝLÝYOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='3' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	GÖNDERÝLEN ÝLETÝLERÝN SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='3'");
$num_rows = mysql_num_rows($result);


// tema sýnýfý örneði oluþturuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa koþul 8 alaný tekli döngüye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL ÝLETÝ YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'Gönderilen Kutusunda hiç iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL ÝLETÝ VARSA	//

else
{
	$tablono = 0;

	$ornek1->kosul('2', array('' => ''), true);
	$ornek1->kosul('1', array('' => ''), false);


	while ($satir = mysql_fetch_array($sonuc))
	{
		$tablono++;
		$oi_baslik = '<a href="oi_oku.php?oino='.$satir['id'].'">'.$satir['ozel_baslik'].'</a>';
		$oi_kime = '<a href="profil.php?kim='.$satir['kime'].'">'.$satir['kime'].'</a>';
		$oi_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['gonderme_tarihi']);


		//	veriler tema motoruna yollanýyor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kime,
		'{OI_TARIH1}' => $oi_tarih);
	}
}


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="gonderilen">';

$kutu_aciklama = 'Gönderdiðiniz kiþi tarafýndan henüz okunmayan iletiler burada bulunur,
<br>gönderilen tarafýndan okunduklarýnda Ulaþan Kutusuna taþýnýr.';



//	TEMA UYGULANIYOR	//

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), true);
$ornek1->kosul('7', array('' => ''), true);

if (isset($tekli1)) $ornek1->tekli_dongu('1',$tekli1);

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu2,
'{KUTU_KOTA}' => '&#8734;',
'{DOLULUK}' => $num_rows,
'{OZEL_ILETI_GONDER}' => $oi_rengi,
'{DOLULUK_ORANI}' => '0',
'{FORM_BILGI}' => $form_bilgi,
'{HUCRE_SAYISI}' => '4',
'{HUCRE_DEGER}' => '395',
'{KIMDEN_KIME}' => 'Kime',
'{GELEN_KUTUSU}' => 'Gelen Kutusu',
'{ULASAN_KUTUSU}' => 'Ulaþan Kutusu ',
'{GONDERILEN_KUTUSU}' => 'Gönderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php">',
'{GELEN_KUTUSU_BAG2}' => '</a>',
'{ULASAN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=ulasan">',
'{ULASAN_KUTUSU_BAG2}' => '</a>',
'{GONDERILEN_KUTUSU_BAG}' => '',
'{GONDERILEN_KUTUSU_BAG2}' => '',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'Gönderme Tarihi',
'{KUTU_ACIKLAMA}' => $kutu_aciklama));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}

//  GÖNDERÝLEN KUTUSU GÖRÜNTÜLENÝYOR - SONU  //





//  KAYDEDÝLEN KUTUSU GÖRÜNTÜLENÝYOR - BAÞI  //

elseif ($_GET['kip'] == 'kaydedilen')
{
$sayfano = 27;
$sayfa_adi = 'Özel iletiler Kaydedilen Kutusu';
include 'baslik.php';


//	KAYDEDÝLEN ÝLETÝLER TARÝH SIRASINA GÖRE ÇEKÝLÝYOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	KAYDEDÝLEN ÝLETÝLERÝN SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4'");
$num_rows = mysql_num_rows($result);


// tema sýnýfý örneði oluþturuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa koþul 8 alaný tekli döngüye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL ÝLETÝ YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'Kaydedilen Kutusunda hiç iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL ÝLETÝ VARSA	//

else
{
	$tablono = 0;

	$ornek1->kosul('2', array('' => ''), true);
	$ornek1->kosul('1', array('' => ''), false);


	while ($satir = mysql_fetch_array($sonuc))
	{
		$tablono++;
		$oi_baslik = '<a href="oi_oku.php?oino='.$satir['id'].'">'.$satir['ozel_baslik'].'</a>';
		$oi_kimden = '<a href="profil.php?kim='.$satir['kimden'].'">'.$satir['kimden'].'</a>';
		$oi_kime = '<a href="profil.php?kim='.$satir['kime'].'">'.$satir['kime'].'</a>';
		$oi_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['gonderme_tarihi']);


		//	veriler tema motoruna yollanýyor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kimden,
		'{OI_TARIH1}' => $oi_kime,
		'{OI_TARIH2}' => $oi_tarih);
	}
}


//	DOLULUK ORANI YÜZDESÝ HESAPLANIYOR	//

if ($num_rows != 0)
{
	$doluluk_orani = 100 / ($ayarlar['kaydedilen_kutu_kota'] / $num_rows);
	settype($doluluk_orani,'integer');
	if ($doluluk_orani > 100) $doluluk_orani = 100;
}

else $doluluk_orani = 1;


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="kaydedilen">';



//	TEMA UYGULANIYOR	//

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), true);
$ornek1->kosul('3', array('{TARIH_ALAN2}' => 'Gönderme Tarihi'), true);
$ornek1->kosul('4', array('' => ''), true);
$ornek1->kosul('7', array('' => ''), false);

if (isset($tekli1)) $ornek1->tekli_dongu('1',$tekli1);

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu2,
'{KUTU_KOTA}' => $ayarlar['kaydedilen_kutu_kota'],
'{DOLULUK}' => $num_rows,
'{OZEL_ILETI_GONDER}' => $oi_rengi,
'{DOLULUK_ORANI}' => $doluluk_orani,
'{FORM_BILGI}' => $form_bilgi,
'{HUCRE_SAYISI}' => '5',
'{HUCRE_DEGER}' => '275',
'{KIMDEN_KIME}' => 'Kimden',
'{GELEN_KUTUSU}' => 'Gelen Kutusu',
'{ULASAN_KUTUSU}' => 'Ulaþan Kutusu ',
'{GONDERILEN_KUTUSU}' => 'Gönderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php">',
'{GELEN_KUTUSU_BAG2}' => '</a>',
'{ULASAN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=ulasan">',
'{ULASAN_KUTUSU_BAG2}' => '</a>',
'{GONDERILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=gonderilen">',
'{GONDERILEN_KUTUSU_BAG2}' => '</a>',
'{KAYDEDILEN_KUTUSU_BAG}' => '',
'{KAYDEDILEN_KUTUSU_BAG2}' => '',
'{TARIH_ALAN1}' => 'Kime',
'{KUTU_ACIKLAMA}' => ''));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}
$gec = '';

//  KAYDEDÝLEN KUTUSU GÖRÜNTÜLENÝYOR - SONU  //





//  GELEN KUTUSU GÖRÜNTÜLENÝYOR - BAÞI  //

else:

$sayfano = 28;
$sayfa_adi = 'Özel iletiler Gelen Kutusu';
include 'baslik.php';


// tema sýnýfý örneði oluþturuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa koþul 8 alaný tekli döngüye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//  ÖZEL ÝLETÝLER TARÝH SIRASINA GÖRE ÇEKÝLÝYOR //

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='1' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//  ÖZEL ÝLETÝLERÝN SAYISI ALINIYOR //

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='1'");
$num_rows = mysql_num_rows($result);


//  OZEL ÝLETÝ YOKSA    //

if (!$num_rows)
{
    $ornek1->kosul('1', array('{KUTU_BOS}' => 'Gelen Kutusunda hiç iletiniz yok.'), true);
    $ornek1->kosul('2', array('' => ''), false);
}


//  OZEL ÝLETÝ VARSA    //

else
{
    $tablono = 0;

    $ornek1->kosul('2', array('' => ''), true);
    $ornek1->kosul('1', array('' => ''), false);


    while ($satir = mysql_fetch_array($sonuc))
    {
        $tablono++;
        $oi_baslik = '<a href="oi_oku.php?oino='.$satir['id'].'">';


        //  OKUNMAMIÞ ÝLETÝLER KALIN YAZILIYOR  //

        if (!$satir['okunma_tarihi']) $oi_baslik .= '<b>'.$satir['ozel_baslik'].'</b></a>';
        else $oi_baslik .= $satir['ozel_baslik'].'</a>';


        $oi_kimden = '<a href="profil.php?kim='.$satir['kimden'].'">'.$satir['kimden'].'</a>';

        $oi_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['gonderme_tarihi']);


        //  veriler tema motoruna yollanýyor    //
        $tekli1[] = array('{TABLO_NO}' => $tablono,
        '{OI_NO}' => $satir['id'],
        '{OZEL_ILET_BASLIK}' => $oi_baslik,
        '{OI_KIMDEN}' => $oi_kimden,
        '{OI_TARIH1}' => $oi_tarih);
    }
}




//  DOLULUK ORANI YÜZDESÝ HESAPLANIYOR  //

if ($num_rows != 0)
{
	$doluluk_orani = 100 / ($ayarlar['gelen_kutu_kota'] / $num_rows);
	settype($doluluk_orani,'integer');
	if ($doluluk_orani > 100) $doluluk_orani = 100;
}

else $doluluk_orani = 1;


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="ozel_ileti">';



//  TEMA UYGULANIYOR    //

$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), true);
$ornek1->kosul('7', array('' => ''), true);

if (isset($tekli1)) $ornek1->tekli_dongu('1',$tekli1);

$ornek1->dongusuz(array('{JAVASCRIPT_KODU}' => $javascript_kodu2,
'{KUTU_KOTA}' => $ayarlar['gelen_kutu_kota'],
'{DOLULUK}' => $num_rows,
'{OZEL_ILETI_GONDER}' => $oi_rengi,
'{DOLULUK_ORANI}' => $doluluk_orani,
'{FORM_BILGI}' => $form_bilgi,
'{HUCRE_SAYISI}' => '4',
'{HUCRE_DEGER}' => '395',
'{KIMDEN_KIME}' => 'Kimden',
'{GELEN_KUTUSU}' => 'Gelen Kutusu',
'{ULASAN_KUTUSU}' => 'Ulaþan Kutusu ',
'{GONDERILEN_KUTUSU}' => 'Gönderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '',
'{GELEN_KUTUSU_BAG2}' => '',
'{ULASAN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=ulasan">',
'{ULASAN_KUTUSU_BAG2}' => '</a>',
'{GONDERILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=gonderilen">',
'{GONDERILEN_KUTUSU_BAG2}' => '</a>',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'Gönderme Tarihi',
'{KUTU_ACIKLAMA}' => ''));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);


// Gelen kutusu dolu uyarýsý

if ($ayarlar['gelen_kutu_kota'] <= $num_rows)
{
echo '<script type="text/javascript">
<!-- 
alert(\'Gelen Kutusu Tam Dolu !\\nTekrar özel ileti alabilmek için gelen kutusunu boþaltýn.\')
//  -->
</script>';
}


//  NORMAL SAYFA GÖRÜNTÜLENÝYOR - SONU  //



// ÖZEL ÝLETÝ KUTULARI GÖRÜTÜLENÝYOR - SONU //
// ÖZEL ÝLETÝ KUTULARI GÖRÜTÜLENÝYOR - SONU //

endif;
?>