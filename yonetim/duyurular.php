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
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


//	DUYURU EKLENÝYOR	//

if ( (isset($_POST['duyuru'])) AND ($_POST['duyuru'] == 'ekle') )
{
	// OTURUM KODU ÝÞLEMLERÝ  //

	if (isset($_POST['o'])) $_POST['o'] = @zkTemizle($_POST['o']);
	else $_POST['o'] = '';

	$o = $satir['yonetim_kimlik'];
	$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_POST['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if ( (!isset($_POST['fno'])) OR  ($_POST['fno'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['fno'] = zkTemizle($_POST['fno']);


	// zararlý kodlar temizleniyor
	$bul = array('meta ', 'script ', 'script>');
	$cevir = array('');

	$_POST['mesaj_baslik'] = str_replace($bul, $cevir, $_POST['mesaj_baslik']);
	$_POST['mesaj_icerik'] = str_replace($bul, $cevir, $_POST['mesaj_icerik']);


	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['mesaj_baslik'] = @mysql_real_escape_string(stripslashes($_POST['mesaj_baslik']));
		$_POST['mesaj_icerik'] = @mysql_real_escape_string(stripslashes($_POST['mesaj_icerik']));
	}

	//	magic_quotes_gpc kapalýysa	//
	else
	{
		$_POST['mesaj_baslik'] = @mysql_real_escape_string($_POST['mesaj_baslik']);
		$_POST['mesaj_icerik'] = @mysql_real_escape_string($_POST['mesaj_icerik']);
	}

	$strSQL = "INSERT INTO $tablo_duyurular (fno,duyuru_baslik,duyuru_icerik)
				VALUES ('$_POST[fno]','$_POST[mesaj_baslik]', '$_POST[mesaj_icerik]')";


	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	header('Location: duyurular.php');
	exit();
}



//	SEÇÝLÝ DUYURU SÝLÝNÝYOR	//

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'sil') )
{
	// OTURUM KODU ÝÞLEMLERÝ  //

	if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
	else $_GET['o'] = '';

	$o = $satir['yonetim_kimlik'];
	$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	$_GET['dno'] = zkTemizle($_GET['dno']);

	$strSQL = "DELETE FROM $tablo_duyurular WHERE id='$_GET[dno]'";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	header('Location: duyurular.php');
	exit();
}



//	SEÇÝLÝ DUYURU DÜZENLENÝYOR	//

if ( (isset($_POST['duyuru'])) AND ($_POST['duyuru'] == 'duzenle') )
{
	// OTURUM KODU ÝÞLEMLERÝ  //

	if (isset($_POST['o'])) $_POST['o'] = @zkTemizle($_POST['o']);
	else $_POST['o'] = '';

	$o = $satir['yonetim_kimlik'];
	$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_POST['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	if ( (!isset($_POST['fno'])) OR  ($_POST['fno'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['fno'] = zkTemizle($_POST['fno']);

	$_POST['dno'] = zkTemizle($_POST['dno']);


	// zararlý kodlar temizleniyor
	$bul = array('meta ', 'script ', 'script>');
	$cevir = array('');

	$_POST['mesaj_baslik'] = str_replace($bul, $cevir, $_POST['mesaj_baslik']);
	$_POST['mesaj_icerik'] = str_replace($bul, $cevir, $_POST['mesaj_icerik']);


	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['mesaj_baslik'] = @mysql_real_escape_string(stripslashes($_POST['mesaj_baslik']));
		$_POST['mesaj_icerik'] = @mysql_real_escape_string(stripslashes($_POST['mesaj_icerik']));
	}

	//	magic_quotes_gpc kapalýysa	//
	else
	{
		$_POST['mesaj_baslik'] = @mysql_real_escape_string($_POST['mesaj_baslik']);
		$_POST['mesaj_icerik'] = @mysql_real_escape_string($_POST['mesaj_icerik']);
	}

	$strSQL = "UPDATE $tablo_duyurular SET fno='$_POST[fno]',duyuru_baslik='$_POST[mesaj_baslik]',duyuru_icerik='$_POST[mesaj_icerik]' WHERE id='$_POST[dno]'";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	header('Location: duyurular.php');
	exit();
}




$sayfa_adi = 'Yönetim Duyurular';
include 'yonetim_baslik.php';



//	tema dosyasý açýlýyor	//
function tema_dosyasi($dosya)
{
	if (!($dosya_ac = fopen($dosya,'r')))
		die ('<p><font color="red"><b>Tema Dosyasý Açýlamýyor '.$dosya.'</b></font></p>');

	$boyut = filesize($dosya);
	$dosya_metni = fread($dosya_ac,$boyut);
	fclose($dosya_ac);
	
	return $dosya_metni;
}


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/duyurular.html');

$yonetim_sol_menu = tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html');


$javascript_kodu = '<script type="text/javascript" src="../dosyalar/betik_zengin.php"></script>
<script type="text/javascript">
<!-- //
function denetle2(){ 
var dogruMu = true;
if (document.form1.mesaj_baslik.value.length < 3){ 
    dogruMu = false; 
    alert("YAZDIÐINIZ BAÞLIK 3 KARAKTERDEN UZUN OLMALIDIR !");
}
else if (document.form1.mesaj_icerik.value.length < 3){ 
    dogruMu = false; 
    alert("YAZDIÐINIZ ÝLETÝ 3 KARAKTERDEN UZUN OLMALIDIR !");
}
else;
return dogruMu;
}
//  -->
</script>
<script type="text/javascript" src="../dosyalar/betik_mesaj.js"></script>
<script type="text/javascript" src="../dosyalar/betik_duyuru.js"></script>';


$javascript_kodu2 = '<script type="text/javascript">
<!-- //
yolla2(\'mesaj_icerik\',\'mesaj_icerik_div\',\'cevirme\');
var alan1 = document.getElementById(\'mesaj_icerik_div\');
alan1.designMode="On";
alan1.contentEditable="true";
alan1.indicateeditable="true";
alan1.useCSS="false";
alan1.useHeader="false";
var alan2=document.getElementById(\'mesaj_icerik\');
alan2.focus();
var zengin=cerez_oku("zengin");
if (zengin==1)duzenleyici_degis(\'cevirme\');
var zenginboyut=cerez_oku("zenginboyut");
if (zenginboyut==0);
else if(zenginboyut>0){for(i=0;zenginboyut>i;i++)alan_buyut("buyut",true);}
else if(zenginboyut<0){for(i=0;zenginboyut<i;i--)alan_buyut("kucult",true);}
//  -->
</script>';


// OTURUM KODU ÝÞLEMLERÝ  //

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];




			// DUYURU DÜZENLEME BAÐLANTISI TIKLANMIÞSA - BAÞI   //



if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'duzenle') ):

$_GET['dno'] = @zkTemizle($_GET['dno']);


// DUYURUNUN BÝLGÝLERÝ ÇEKÝLÝYOR //

$strSQL = "SELECT * FROM $tablo_duyurular where id='$_GET[dno]' LIMIT 1";
$duzenle_sonuc = mysql_query($strSQL) or die ('<h2>duyuru sorgu baþarýsýz</h2>');
$duyuru_duzenle = mysql_fetch_assoc($duzenle_sonuc);


$sayfa_baslik = 'Duyuru Düzenleme';
$duyuru_baslik = @str_replace('&','&#38',$duyuru_duzenle['duyuru_baslik']);
$duyuru_icerik = @str_replace('&','&#38',$duyuru_duzenle['duyuru_icerik']);


$forum_secenek = '
<select name="fno" class="formlar">';

if ($duyuru_duzenle['fno'] == 'tum') $forum_secenek .= '<option value="tum" selected="selected"> &nbsp; - TÜM SAYFALAR -';
else $forum_secenek .= '<option value="tum"> &nbsp; - TÜM SAYFALAR -';

if ($portal_kullan == 1) 
{
    if ($duyuru_duzenle['fno'] == 'por') $forum_secenek .= '<option value="por" selected="selected"> &nbsp; - PORTAL ANA SAYFASI -';
    else $forum_secenek .= '<option value="por"> &nbsp; - PORTAL ANA SAYFASI -';
}

if ($duyuru_duzenle['fno'] == 'ozel') $forum_secenek .= '<option value="ozel" selected="selected"> &nbsp; - ÖZEL ÝLETÝ SAYFALARI -';
else $forum_secenek .= '<option value="ozel"> &nbsp; - ÖZEL ÝLETÝ SAYFALARI -';

if ($duyuru_duzenle['fno'] == 'mis') $forum_secenek .= '<option value="mis" selected="selected"> &nbsp; - MÝSAFÝRLER -';
else $forum_secenek .= '<option value="mis"> &nbsp; - MÝSAFÝRLER -';

if ($duyuru_duzenle['fno'] == 'uye') $forum_secenek .= '<option value="uye" selected="selected"> &nbsp; - TÜM ÜYELER -';
else $forum_secenek .= '<option value="uye"> &nbsp; - TÜM ÜYELER -';

if ($duyuru_duzenle['fno'] == 'byar') $forum_secenek .= '<option value="byar" selected="selected"> &nbsp; - BÖLÜM YARDIMCILARI VE ÖZEL ÜYELER -';
else $forum_secenek .= '<option value="yar"> &nbsp; - BÖLÜM YARDIMCILARI VE ÖZEL ÜYELER -';

if ($duyuru_duzenle['fno'] == 'fyar') $forum_secenek .= '<option value="fyar" selected="selected"> &nbsp; - FORUM YARDIMCILARI -';
else $forum_secenek .= '<option value="yar"> &nbsp; - FORUM YARDIMCILARI -';

if ($duyuru_duzenle['fno'] == 'yon') $forum_secenek .= '<option value="yon" selected="selected"> &nbsp; - YÖNETÝCÝLER -';
else $forum_secenek .= '<option value="yon"> &nbsp; - YÖNETÝCÝLER -';


// forum dalý adlarý çekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar
					WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
		{
			if ($duyuru_duzenle['fno'] == $forum_satir['id']) $forum_secenek .= '
			<option value="'.$forum_satir['id'].'" selected="selected"> &nbsp; - '.$forum_satir['forum_baslik'];

			else $forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];
		}


		else
		{
			if ($duyuru_duzenle['fno'] == $forum_satir['id']) $forum_secenek .= '
			<option value="'.$forum_satir['id'].'" selected="selected"> &nbsp; - '.$forum_satir['forum_baslik'];

			else $forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


			while ($alt_forum_satir = mysql_fetch_array($sonuca))
			{
				if ($duyuru_duzenle['fno'] == $alt_forum_satir['id']) $forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'" selected="selected"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];

				else $forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
			}
		}
	}
}

$forum_secenek .= '</select>';



$dongusuz = array('{SAYFA_BASLIK}' => $sayfa_baslik,
                '{FORUM_SECENEK}' => $forum_secenek,
                '{DNO}' => $_GET['dno'],
                '{KIP}' => 'duzenle"><input type="hidden" name="o" value="'.$o,
                '{FORM_GONDER}' => 'Düzenle',
                '{DUYURU_BASLIK}' => $duyuru_baslik,
                '{DUYURU_ICERIK}' => $duyuru_icerik,
                '{JAVASCRIPT_KODU}' => $javascript_kodu,
                '{JAVASCRIPT_KODU2}' => $javascript_kodu2);


$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('1', $dongusuz, true);





			// DUYURU DÜZENLEME BAÐLANTISI TIKLANMIÞSA - SONU   //


elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'yeni') ):


$sayfa_baslik = 'Duyuru Ekleme';


$forum_secenek = '
<select name="fno" class="formlar">
<option value="tum"> &nbsp; - TÜM FORUM SAYFALARI -';

if ($portal_kullan == 1) $forum_secenek .= '<option value="por"> &nbsp; - PORTAL ANA SAYFASI -';

$forum_secenek .= '<option value="ozel"> &nbsp; - ÖZEL ÝLETÝ SAYFALARI -
<option value="mis"> &nbsp; - MÝSAFÝRLER -
<option value="uye"> &nbsp; - TÜM ÜYELER -
<option value="byar"> &nbsp; - BÖLÜM YARDIMCILARI VE ÖZEL ÜYELER -
<option value="fyar"> &nbsp; - FORUM YARDIMCILARI -
<option value="yon"> &nbsp; - YÖNETÝCÝLER -
<option value=""> &nbsp; --------------------------------------------------';



// forum dalý adlarý çekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar
					WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


		else
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


			while ($alt_forum_satir = mysql_fetch_array($sonuca))
				$forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
		}
	}
}

$forum_secenek .= '</select>';



$dongusuz = array('{SAYFA_BASLIK}' => $sayfa_baslik,
                '{FORUM_SECENEK}' => $forum_secenek,
                '{DNO}' => '',
                '{KIP}' => 'ekle"><input type="hidden" name="o" value="'.$o,
                '{FORM_GONDER}' => 'G ö n d e r',
                '{DUYURU_BASLIK}' => '',
                '{DUYURU_ICERIK}' => '',
                '{JAVASCRIPT_KODU}' => $javascript_kodu,
                '{JAVASCRIPT_KODU2}' => $javascript_kodu2);


$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('1', $dongusuz, true);





            //      GÝRÝÞ SAYFASI - DUYURULAR SIRALANIYOR       //

else:

// OTURUM KODU ÝÞLEMLERÝ  //

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


//	FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//

$strSQL = "SELECT id,forum_baslik,okuma_izni FROM $tablo_forumlar ORDER BY dal_no, sira";
$sonuc = mysql_query($strSQL);

while ($forum_satir = mysql_fetch_array($sonuc))
{
	$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];
}



// DUYURU SIRALAMA ÞEKLÝ    //

if (isset($_GET['sirala']))
{
    if ($_GET['sirala'] == 'f1')
    {
        $duyuru_sirala = 'fno';
        $siralama_secenek = '<option value="f2">Forum sýrasýna göre tersten
<option value="f1" selected="selected">Forum sýrasýna göre
<option value="d2">Oluþturulma sýrasýna göre tersten
<option value="d1">Oluþturulma sýrasýna göre';
    }

    elseif ($_GET['sirala'] == 'f2')
    {
        $duyuru_sirala = 'fno DESC';
        $siralama_secenek = '<option value="f2" selected="selected">Forum sýrasýna göre tersten
<option value="f1">Forum sýrasýna göre
<option value="d2">Oluþturulma sýrasýna göre tersten
<option value="d1">Oluþturulma sýrasýna göre';
    }

    elseif ($_GET['sirala'] == 'd1')
    {
        $duyuru_sirala = 'id';
        $siralama_secenek = '<option value="f2">Forum sýrasýna göre tersten
<option value="f1">Forum sýrasýna göre
<option value="d2">Oluþturulma sýrasýna göre tersten
<option value="d1" selected="selected">Oluþturulma sýrasýna göre';
    }

    elseif ($_GET['sirala'] == 'd2')
    {
        $duyuru_sirala = 'id DESC';
        $siralama_secenek = '<option value="f2">Forum sýrasýna göre tersten
<option value="f1">Forum sýrasýna göre
<option value="d2" selected="selected">Oluþturulma sýrasýna göre tersten
<option value="d1">Oluþturulma sýrasýna göre';
    }
}

else
{
    $duyuru_sirala = 'fno DESC';
    $siralama_secenek = '<option value="f2" selected="selected">Forum sýrasýna göre tersten
<option value="f1">Forum sýrasýna göre
<option value="d2">Oluþturulma sýrasýna göre tersten
<option value="d1">Oluþturulma sýrasýna göre';
}



// DUYURU BÝLGÝLERÝ ÇEKÝLÝYOR //

$strSQL = "SELECT * FROM $tablo_duyurular ORDER BY $duyuru_sirala";
$duyuru_sonuc = mysql_query($strSQL) or die ('<h2>duyuru sorgu baþarýsýz</h2>');


// DUYURU VARSA DÖNGÜYE GÝRÝLÝYOR //

if (mysql_num_rows($duyuru_sonuc)) 
{
    while ($duyurular = mysql_fetch_assoc($duyuru_sonuc))
    {
        if ($duyurular['fno'] == 'tum') $forum_baslik = '"Ana Duyuru"';
        elseif ($duyurular['fno'] == 'por') $forum_baslik = '"Portal Duyurusu"';
        elseif ($duyurular['fno'] == 'ozel') $forum_baslik = '"Özel Ýleti Duyurusu"';
        elseif ($duyurular['fno'] == 'mis') $forum_baslik = '"Misafirler Duyurusu"';
        elseif ($duyurular['fno'] == 'uye') $forum_baslik = '"Tüm Üyeler Duyurusu"';
        elseif ($duyurular['fno'] == 'byar') $forum_baslik = '"Bölüm Yardýmcýlarý ve Özel Üyeler Duyurusu"';
        elseif ($duyurular['fno'] == 'fyar') $forum_baslik = '"Forum Yardýmcýlarý Duyurusu"';
        elseif ($duyurular['fno'] == 'yon') $forum_baslik = '"Yöneticiler Duyurusu"';
        else $forum_baslik = $tumforum_satir[$duyurular['fno']].' Duyurusu';


        $tekli1[] = array('{FORUM_BASLIK}' => $forum_baslik,
                            '{DUYURU_BASLIK}' => $duyurular['duyuru_baslik'],
                            '{DUYURU_ICERIK}' => $duyurular['duyuru_icerik'],
                            '{DNO}' => $duyurular['id'],
                            '{O}' => $o);
    }


    $dongusuz = array('{SAYFA_BASLIK}' => 'Varolan Duyurular',
                        '{KIP}' => 'duzenle',
                        '{SIMGE_DEGISTIR}' => $simge_degistir,
                        '{SIMGE_SIL}' => $simge_sil,
                        '{YENI_DUYURU_EKLE}' => 'duyurular.php?kip=yeni',
                        '{SIRALAMA_SECENEK}' => $siralama_secenek);


    $ornek1->kosul('1', array('' => ''), false);
    $ornek1->kosul('3', array('' => ''), false);
    $ornek1->kosul('2', $dongusuz, true);

    $ornek1->tekli_dongu('1',$tekli1);
}



else
{
    $ornek1->kosul('1', array('' => ''), false);
    $ornek1->kosul('2', array('' => ''), false);
    $ornek1->kosul('3', array('{DUYURU_YOK}' => 'Henüz Duyuru Yok !',
                                '{YENI_DUYURU_EKLE}' => 'duyurular.php?kip=yeni'), true);
}


endif;





//	veriler tema motoruna yollanýyor	//

$dongusuz = array('{YONETIM_SOL_MENU}' => $yonetim_sol_menu);

$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>