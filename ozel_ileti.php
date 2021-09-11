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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


// �zel ileti �zelli�i kapal�ysa
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


// DUYURU B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT * FROM $tablo_duyurular WHERE fno='ozel' ORDER BY id";
$duyuru_sonuc = mysql_query($strSQL) or die ('<h2>duyuru sorgu ba�ar�s�z</h2>');


// DUYURU VARSA D�NG�YE G�R�L�YOR //

if (mysql_num_rows($duyuru_sonuc)) 
{
	while ($duyurular = mysql_fetch_assoc($duyuru_sonuc))
	{
		$tekli2[] = array('{OZEL_DUYURU_BASLIK}' => $duyurular['duyuru_baslik'], '{OZEL_DUYURU_ICERIK}' => $duyurular['duyuru_icerik']);
	}
}





//  ENGELLEME ��LEMLER� - BA�I  //

if ( (isset($_POST['kip'])) AND ($_POST['kip'] == 'engel') ):

    $_POST['engel_tipi'] = @zkTemizle($_POST['engel_tipi']);
    if (!preg_match('/^[0-2]+$/', $_POST['engel_tipi'])) $_POST['engel_tipi'] = 0;
    $dogru_kuladlar = '';


    // Tip s�f�r de�ilse �ye adlar�n� denetle
    if ($_POST['engel_tipi'] != '0')
    {
        // de�i�kendeki veriler sat�r sat�r ayr�l�p dizi de�i�kene aktar�l�yor //
        $yasak_kulad_bosluk = explode("\r\n", $_POST['engellenenler']);

        // sat�r say�s� al�n�yor //
        $yasak_kulad_sayi = count($yasak_kulad_bosluk);

        // dizideki sat�rlar d�ng�ye sokuluyor //
        for ($d=0,$a=0; $d < $yasak_kulad_sayi; $d++)
        {
            $yasak_kulad_bosluk[$d] = @zkTemizle(trim($yasak_kulad_bosluk[$d]));

            // 3 karakterden k�sa ve ayn� olan isimler diziden at�l�yor	//
            if ( (strlen($yasak_kulad_bosluk[$d]) > 3) AND (!preg_match("/$yasak_kulad_bosluk[$d],/i", $dogru_kuladlar)) )
            {
                // kullan�c� ad� denetleniyor
                $strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$yasak_kulad_bosluk[$d]' AND yetki='0' AND id!=$kullanici_kim[id] LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
                $satir = mysql_fetch_array($sonuc);

                if (isset($satir['kullanici_adi'])) $dogru_kuladlar .= $satir['kullanici_adi'].',';
                $a++;
            }
        }
    }


    // kullan�c�n�n engelleme girdileri �ekiliyor
    $strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='$kullanici_kim[id]' LIMIT 1";
    $sonuc = mysql_query($strSQL);


    // kullan�c�ya ait girdi varsa
    if (mysql_num_rows($sonuc))
    {
        // tip s�f�r de�ilse yasaklar tablosuna girdi yap�l�yor
        if ($_POST['engel_tipi'] != '0')
            $strSQL = "UPDATE $tablo_yasaklar SET deger='$dogru_kuladlar', tip='$_POST[engel_tipi]' where etiket='$kullanici_kim[id]' LIMIT 1";

        // tip s�f�r ise yasaklar tablosundaki girdi siliniyor
        else $strSQL = "DELETE FROM $tablo_yasaklar WHERE etiket='$kullanici_kim[id]' LIMIT 1";

        $sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');
    }


    // kullan�c�ya ait girdi yoksa
    else
    {
        // tip s�f�r de�ilse yasaklar tablosuna girdi yap�l�yor
        if ($_POST['engel_tipi'] != '0')
        {
            $strSQL = "INSERT INTO $tablo_yasaklar (etiket, deger, tip)";
            $strSQL .= "VALUES ('$kullanici_kim[id]', '$dogru_kuladlar', '$_POST[engel_tipi]')";
            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        }
    }

    header('Location: hata.php?bilgi=46');
    exit();


//  ENGELLEME ��LEMLER� - SONU  //




//  �LET� S�LME ��LEMLER� - BA�I  //

elseif ( (isset($_POST['secili_sil'])) AND ($_POST['secili_sil'] != '') ):

    // se�im yap�lmam��sa
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


        // yollad��� bir �zel ilet ise
        if (($ozel_ileti['kimden'] == $kullanici_kim['kullanici_adi']))
        {
            // ileti okunmadan siliniyorsa
            if ( (!$ozel_ileti['okunma_tarihi']) AND ($kullanici_kim['okunmamis_oi'] != '0') )
            {
                $strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi-1 WHERE id='$kullanici_kim[id]' LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
            }


            // kendine yollad��� bir �zel ileti ise ger�ekten sil
            if ($ozel_ileti['kimden'] == $ozel_ileti['kime']) $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // iletiyi alan ki�i de silmi�se ger�ekten sil
            elseif ($ozel_ileti['alan_kutu'] == '0') $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // sadece gonderen kutusunu s�f�rla
            else $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='0' WHERE id='$sec_ileti_sil' LIMIT 1";

            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        }


        // ald��� bir �zel ilet ise
        elseif (($ozel_ileti['kime'] == $kullanici_kim['kullanici_adi']))
        {
            // ileti okunmadan siliniyorsa
            if ( (!$ozel_ileti['okunma_tarihi']) AND ($kullanici_kim['okunmamis_oi'] != '0') )
            {
                $strSQL = "UPDATE $tablo_kullanicilar SET okunmamis_oi=okunmamis_oi-1 WHERE id='$kullanici_kim[id]' LIMIT 1";
                $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
            }


            // iletiyi g�nderen ki�i de silmi�se ger�ekten sil
            if ($ozel_ileti['gonderen_kutu'] == '0') $strSQL = "DELETE FROM $tablo_ozel_ileti WHERE id='$sec_ileti_sil' LIMIT 1";

            // sadece alan kutusunu s�f�rla
            else $strSQL = "UPDATE $tablo_ozel_ileti SET alan_kutu='0' WHERE id='$sec_ileti_sil' LIMIT 1";

            $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        }

        // silme yetkisi yoksa
        else
        {
            header('Location: hata.php?hata=69');
            exit();
        }
    }


    // gelinen sayfaya geri d�n�l�yor

    if ($_POST['git'] == 'ozel_ileti') $git = 'ozel_ileti.php';
    elseif ($_POST['git'] == 'ulasan') $git = 'ozel_ileti.php?kip=ulasan';
    elseif ($_POST['git'] == 'gonderilen') $git = 'ozel_ileti.php?kip=gonderilen';
    elseif ($_POST['git'] == 'kaydedilen') $git = 'ozel_ileti.php?kip=kaydedilen';
    else $git = 'ozel_ileti.php';

    header('Location: '.$git);
    exit();


//  �LET� S�LME ��LEMLER� - SONU  //




//  �LET� KAYDETME ��LEMLER� - BA�I //

elseif (isset($_POST['secili_kaydet'])):

    // se�im yap�lmam��sa
    if (!isset($_POST['sec_ileti']))
    {
        header('Location: hata.php?hata=68');
        exit();
    }

    $result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4'");
    $num_rows = mysql_num_rows($result);


    // se�ilen iletiler kaydedilen kutusundaki bo�luktan fazla ise
    if (($num_rows + count($_POST['sec_ileti'])) > $ayarlar['kaydedilen_kutu_kota'])
    {
        header('Location: hata.php?hata=70');
        exit();
    }


    foreach ($_POST['sec_ileti'] as $sec_ileti_kaydet)
    {
        $sec_ileti_kaydet = zkTemizle($sec_ileti_kaydet);

        $strSQL = "SELECT kime,kimden,alan_kutu,gonderen_kutu FROM $tablo_ozel_ileti WHERE id='$sec_ileti_kaydet' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
        $ozel_ileti = mysql_fetch_array($sonuc);


        // yollad��� bir �zel ilet ise
        if (($ozel_ileti['kimden'] == $kullanici_kim['kullanici_adi']))
        {
            // kendine yollad��� bir �zel ileti ise ger�ekten sil
            if ($ozel_ileti['kimden'] == $ozel_ileti['kime'])
                $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='4',alan_kutu='4' WHERE id='$sec_ileti_kaydet' LIMIT 1";

            else $strSQL = "UPDATE $tablo_ozel_ileti SET gonderen_kutu='4' WHERE id='$sec_ileti_kaydet' LIMIT 1";
            $sonuc = mysql_query($strSQL);
        }

        // ald��� bir �zel ilet ise
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

//  �LET� KAYDETME ��LEMLER� - SONU //









// �ZEL �LET� KUTULARI G�R�T�LEN�YOR - BA�I //
// �ZEL �LET� KUTULARI G�R�T�LEN�YOR - BA�I //

elseif (isset($_GET['kip'])):



//  AYARLAR SAYFASI G�R�NT�LEN�YOR - BA�I  //

if ($_GET['kip'] == 'ayarlar')
{
$sayfano = 24;
$sayfa_adi = '�zel ileti Ayarlar�';
include 'baslik.php';


if ($kullanici_kim['okunmamis_oi']) $okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
else $okunmamis_oi = '';


// kullan�c�n�n engelleme girdileri �ekiliyor
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

	$euyari= '<br><br><br><p align="center"><font style="color: #FF6600; font-weight: bolder;">�nceki sayfadan t�klad���n�z " <u>'.$_GET['kim'].'</u> " �ye ad�<br>a�a��daki alana eklenmi�tir.<br><br>Uygulamak i�in " *Sadece alttakileri engelle "<br>se�ene�ini se�ip "De�i�tir" d��mesini t�klay�n.</font></p>';
}

else
{
	if ( (isset($satir['deger'])) AND ($satir['deger'] != '') ) $engellenenler = $satir['deger'];
	else $engellenenler = '';
	$euyari= '';
}


$form_bilgi2 = '<form name="engelle" action="ozel_ileti.php" method="post">
<input type="hidden" name="kip" value="engel">';


// tema s�n�f� �rne�i olu�turuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa ko�ul 8 alan� tekli d�ng�ye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


// tema uygulan�yor
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

//  AYARLAR SAYFASI G�R�NT�LEN�YOR - SONU  //





//  ULA�AN KUTUSU G�R�NT�LEN�YOR - BA�I  //

elseif ($_GET['kip'] == 'ulasan')
{
$sayfano = 25;
$sayfa_adi = '�zel iletiler Ula�an Kutusu';
include 'baslik.php';


//	 ULA�AN �LET�LER OKUNMA TAR�H SIRASINA G�RE �EK�L�YOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='2' ORDER BY okunma_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	ULA�AN �LET�LER�N SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='2'");
$num_rows = mysql_num_rows($result);


// tema s�n�f� �rne�i olu�turuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa ko�ul 8 alan� tekli d�ng�ye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL �LET� YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'Ula�an Kutusunda hi� iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL �LET� VARSA	//

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


		//	veriler tema motoruna yollan�yor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kime,
		'{OI_TARIH1}' => $oi_gonderme_tarih,
		'{OI_TARIH2}' => $oi_okunma_tarihi);
	}
}


//	DOLULUK ORANI Y�ZDES� HESAPLANIYOR	//

if ($num_rows != 0)
{
	$doluluk_orani = 100 / ($ayarlar['ulasan_kutu_kota'] / $num_rows);
	settype($doluluk_orani,'integer');
	if ($doluluk_orani > 100) $doluluk_orani = 100;
}

else $doluluk_orani = 1;


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="ulasan">';

$kutu_aciklama = 'Yollad���n�z iletiler g�nderilen taraf�ndan okundu�unda buraya ta��n�r.
<br>�letinin okunma tarihini yukar�da g�rebilirsiniz.';



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
'{ULASAN_KUTUSU}' => 'Ula�an Kutusu ',
'{GONDERILEN_KUTUSU}' => 'G�nderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php">',
'{GELEN_KUTUSU_BAG2}' => '</a>',
'{ULASAN_KUTUSU_BAG}' => '',
'{ULASAN_KUTUSU_BAG2}' => '',
'{GONDERILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=gonderilen">',
'{GONDERILEN_KUTUSU_BAG2}' => '</a>',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'G�nderme Tarihi',
'{KUTU_ACIKLAMA}' => $kutu_aciklama));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}

//  ULA�AN KUTUSU G�R�NT�LEN�YOR - SONU  //





//  G�NDER�LEN KUTUSU G�R�NT�LEN�YOR - BA�I  //

elseif ($_GET['kip'] == 'gonderilen')
{
$sayfano = 26;
$sayfa_adi = '�zel iletiler G�nderilen Kutusu';
include 'baslik.php';


//	G�NDER�LEN �LET�LER TAR�H SIRASINA G�RE �EK�L�YOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='3' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	G�NDER�LEN �LET�LER�N SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='3'");
$num_rows = mysql_num_rows($result);


// tema s�n�f� �rne�i olu�turuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa ko�ul 8 alan� tekli d�ng�ye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL �LET� YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'G�nderilen Kutusunda hi� iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL �LET� VARSA	//

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


		//	veriler tema motoruna yollan�yor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kime,
		'{OI_TARIH1}' => $oi_tarih);
	}
}


$form_bilgi = '<form name="secim_formu" action="ozel_ileti.php" method="post">
<input type="hidden" name="git" value="gonderilen">';

$kutu_aciklama = 'G�nderdi�iniz ki�i taraf�ndan hen�z okunmayan iletiler burada bulunur,
<br>g�nderilen taraf�ndan okunduklar�nda Ula�an Kutusuna ta��n�r.';



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
'{ULASAN_KUTUSU}' => 'Ula�an Kutusu ',
'{GONDERILEN_KUTUSU}' => 'G�nderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php">',
'{GELEN_KUTUSU_BAG2}' => '</a>',
'{ULASAN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=ulasan">',
'{ULASAN_KUTUSU_BAG2}' => '</a>',
'{GONDERILEN_KUTUSU_BAG}' => '',
'{GONDERILEN_KUTUSU_BAG2}' => '',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'G�nderme Tarihi',
'{KUTU_ACIKLAMA}' => $kutu_aciklama));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();
}

//  G�NDER�LEN KUTUSU G�R�NT�LEN�YOR - SONU  //





//  KAYDED�LEN KUTUSU G�R�NT�LEN�YOR - BA�I  //

elseif ($_GET['kip'] == 'kaydedilen')
{
$sayfano = 27;
$sayfa_adi = '�zel iletiler Kaydedilen Kutusu';
include 'baslik.php';


//	KAYDED�LEN �LET�LER TAR�H SIRASINA G�RE �EK�L�YOR	//

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//	KAYDED�LEN �LET�LER�N SAYISI ALINIYOR		//

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kimden='$kullanici_kim[kullanici_adi]' AND gonderen_kutu='4' OR kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='4'");
$num_rows = mysql_num_rows($result);


// tema s�n�f� �rne�i olu�turuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa ko�ul 8 alan� tekli d�ng�ye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//	OZEL �LET� YOKSA	//

if (!$num_rows)
{
	$ornek1->kosul('1', array('{KUTU_BOS}' => 'Kaydedilen Kutusunda hi� iletiniz yok.'), true);
	$ornek1->kosul('2', array('' => ''), false);
}


//	OZEL �LET� VARSA	//

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


		//	veriler tema motoruna yollan�yor	//
		$tekli1[] = array('{TABLO_NO}' => $tablono,
		'{OI_NO}' => $satir['id'],
		'{OZEL_ILET_BASLIK}' => $oi_baslik,
		'{OI_KIMDEN}' => $oi_kimden,
		'{OI_TARIH1}' => $oi_kime,
		'{OI_TARIH2}' => $oi_tarih);
	}
}


//	DOLULUK ORANI Y�ZDES� HESAPLANIYOR	//

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
$ornek1->kosul('3', array('{TARIH_ALAN2}' => 'G�nderme Tarihi'), true);
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
'{ULASAN_KUTUSU}' => 'Ula�an Kutusu ',
'{GONDERILEN_KUTUSU}' => 'G�nderilen Kutusu',
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

//  KAYDED�LEN KUTUSU G�R�NT�LEN�YOR - SONU  //





//  GELEN KUTUSU G�R�NT�LEN�YOR - BA�I  //

else:

$sayfano = 28;
$sayfa_adi = '�zel iletiler Gelen Kutusu';
include 'baslik.php';


// tema s�n�f� �rne�i olu�turuluyor
$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ozel_ileti.html');


// duyuru varsa ko�ul 8 alan� tekli d�ng�ye sokuluyor
if (isset($tekli2))
{
	$ornek1->kosul('8', array('' => ''), true);
	$ornek1->tekli_dongu('2',$tekli2);
	unset($tekli2);
}
else $ornek1->kosul('8', array('' => ''), false);


//  �ZEL �LET�LER TAR�H SIRASINA G�RE �EK�L�YOR //

$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='1' ORDER BY gonderme_tarihi DESC";
$sonuc = mysql_query($strSQL);


//  �ZEL �LET�LER�N SAYISI ALINIYOR //

$result = mysql_query("SELECT id FROM $tablo_ozel_ileti WHERE kime='$kullanici_kim[kullanici_adi]' AND alan_kutu='1'");
$num_rows = mysql_num_rows($result);


//  OZEL �LET� YOKSA    //

if (!$num_rows)
{
    $ornek1->kosul('1', array('{KUTU_BOS}' => 'Gelen Kutusunda hi� iletiniz yok.'), true);
    $ornek1->kosul('2', array('' => ''), false);
}


//  OZEL �LET� VARSA    //

else
{
    $tablono = 0;

    $ornek1->kosul('2', array('' => ''), true);
    $ornek1->kosul('1', array('' => ''), false);


    while ($satir = mysql_fetch_array($sonuc))
    {
        $tablono++;
        $oi_baslik = '<a href="oi_oku.php?oino='.$satir['id'].'">';


        //  OKUNMAMI� �LET�LER KALIN YAZILIYOR  //

        if (!$satir['okunma_tarihi']) $oi_baslik .= '<b>'.$satir['ozel_baslik'].'</b></a>';
        else $oi_baslik .= $satir['ozel_baslik'].'</a>';


        $oi_kimden = '<a href="profil.php?kim='.$satir['kimden'].'">'.$satir['kimden'].'</a>';

        $oi_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $satir['gonderme_tarihi']);


        //  veriler tema motoruna yollan�yor    //
        $tekli1[] = array('{TABLO_NO}' => $tablono,
        '{OI_NO}' => $satir['id'],
        '{OZEL_ILET_BASLIK}' => $oi_baslik,
        '{OI_KIMDEN}' => $oi_kimden,
        '{OI_TARIH1}' => $oi_tarih);
    }
}




//  DOLULUK ORANI Y�ZDES� HESAPLANIYOR  //

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
'{ULASAN_KUTUSU}' => 'Ula�an Kutusu ',
'{GONDERILEN_KUTUSU}' => 'G�nderilen Kutusu',
'{KAYDEDILEN_KUTUSU}' => 'Kaydedilen Kutusu',
'{GELEN_KUTUSU_BAG}' => '',
'{GELEN_KUTUSU_BAG2}' => '',
'{ULASAN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=ulasan">',
'{ULASAN_KUTUSU_BAG2}' => '</a>',
'{GONDERILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=gonderilen">',
'{GONDERILEN_KUTUSU_BAG2}' => '</a>',
'{KAYDEDILEN_KUTUSU_BAG}' => '<a href="ozel_ileti.php?kip=kaydedilen">',
'{KAYDEDILEN_KUTUSU_BAG2}' => '</a>',
'{TARIH_ALAN1}' => 'G�nderme Tarihi',
'{KUTU_ACIKLAMA}' => ''));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);


// Gelen kutusu dolu uyar�s�

if ($ayarlar['gelen_kutu_kota'] <= $num_rows)
{
echo '<script type="text/javascript">
<!-- 
alert(\'Gelen Kutusu Tam Dolu !\\nTekrar �zel ileti alabilmek i�in gelen kutusunu bo�alt�n.\')
//  -->
</script>';
}


//  NORMAL SAYFA G�R�NT�LEN�YOR - SONU  //



// �ZEL �LET� KUTULARI G�R�T�LEN�YOR - SONU //
// �ZEL �LET� KUTULARI G�R�T�LEN�YOR - SONU //

endif;
?>