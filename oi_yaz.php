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


// �zel ileti �zelli�i kapal� ise uyar� veriliyor
if ($ayarlar['o_ileti'] == 0)
{
	header('Location: hata.php?uyari=2');
	exit();
}


if (isset($_POST['mesaj_onizleme']))
{
	$sayfano = 22;
	$sayfa_adi = '�zel ileti �nizlemesi';
}
else
{
	$sayfano = 23;
	$sayfa_adi = '�zel ileti Yazma';
}


if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';



//  �YE ARAMA - BA�I  //

if (isset($_GET['uye_ara']))
{
	if (isset($_GET['kip']))
	{
		if ($_GET['kip'] == '1')
		{
			$formadi = 'form1';
			$formkime = 'ozel_kime';
		}
		elseif ($_GET['kip'] == '2')
		{
			$formadi = 'kul_izinleri';
			$formkime = 'kim';
		}
	}

	else
	{
		$formadi = 'form1';
		$formkime = 'ozel_kime';
		$_GET['kip'] = '1';
	}


	echo '<center><font style="font-family:verdana;font-weight:bold;font-size:18px;">- �YE ARAMA -</font><br><br>
	<form action="oi_yaz.php" method="get" name="ozel_uye">
	<input type="hidden" name="kip" value="'.$_GET['kip'].'">
	<b>�ye:&nbsp;</b> <input type="text" name="uye_ara" size="25" maxlength="20" value="'.$_GET['uye_ara'].'"> &nbsp; <input name="ara" type="submit" value="Ara"></center>';


	// bo� ise
	if ($_GET['uye_ara'] == '') echo '<center><br>Ba�ta joker olarak * kullanabilirsiniz. <br>Sona joker girmeye gerek yoktur, var kabul edilir.<br>Joker hari� en az 2, en �ok 20 karakter girebilirsiniz.<br><br><a href="javascript:window.close()">Kapat</a></center>';


	// 20 karakterden uzunsa
	elseif (strlen($_GET['uye_ara']) > 20)
	{
		echo '<center><br><font color="#ff0000"><b>20 karakterden fazla giremezsiniz !</b></font><br><br><a href="oi_yaz.php?uye_ara=&amp;kip=1">Geri</a></center>';
		exit();
	}


	// ge�ersiz karakterler varsa
	elseif (!preg_match('/^[A-Za-z0-9-_������������.*]+$/', $_GET['uye_ara']))
	{
		echo '<center><br><font color="#ff0000"><b>Ge�ersiz karakter !</b></font><br><br><a href="oi_yaz.php?uye_ara=&amp;kip=1">Geri</a></center>';
		exit();
	}


	// sorun yok ise aramaya ba�lan�yor
	else
	{
		if (strlen(@str_replace('*','',trim($_GET['uye_ara']))) < 2)
		{
			echo '<center><br><font color="#ff0000"><b>�ye arama i�in en az iki harf girmelisiniz !</b></font><br><br><a href="javascript:window.close()">Kapat</a></center>';
			exit();
		}


		// veri temizleniyor
		$_GET['uye_ara'] = @zkTemizle($_GET['uye_ara']);
		$_GET['uye_ara'] = @zkTemizle4($_GET['uye_ara']);
		$_GET['uye_ara'] = @str_replace('*','%',trim($_GET['uye_ara']));


		// �yeler aran�yor
		$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar WHERE engelle='0' AND kul_etkin='1' AND kullanici_adi LIKE '$_GET[uye_ara]%' LIMIT 0,20";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		if (!mysql_num_rows($sonuc))
		{
			echo '<center><br><b>Arad���n�z ko�ula uyan herhangi bir �ye bulunamad� !</b><br><br>
			<a href="javascript:window.close()">Kapat</a></center>';
		}


		else
		{
			echo '<p align="center"><b>�stedi�iniz �ye ad�n�n �zerine t�klay�n.</b><p>';

			$sayi = 0;
			while($uyeler = mysql_fetch_assoc($sonuc))
			{
				$sayi++;
				echo $sayi.')&nbsp; <a href="javascript:void(0);" onclick="opener.document.forms[\''.$formadi.'\'].'.$formkime.'.value=\''.$uyeler['kullanici_adi'].'\'; window.close()">'.$uyeler['kullanici_adi'].'</a><br>';
			}

			if ($sayi>19) echo '<br>�ok fazla sonu� bulundu, sadece 20 tanesi g�steriliyor. Arama s�zc���n� de�i�tirin veya arad���n�z �yeyi <a href="uyeler.php" target="_blank"><b>�yeler</b></a> sayfas�ndan bulun.';
		}
	}

	echo '<script type="text/javascript"><!-- //
	document.ozel_uye.uye_ara.focus();
	// --></script>';

	exit();
}

//  �YE ARAMA - SONU  //



include 'baslik.php';


if (isset($_GET['ozel_kime']))
{
	$ozel_kime = @zkTemizle($_GET['ozel_kime']);
	$ozel_kime = @zkTemizle4($ozel_kime);
}
if (isset($_POST['ozel_kime'])) $ozel_kime = @zkTemizle($_POST['ozel_kime']);



//	�ZEL �LET� YANITLA TIKLANMI�SA �LET�N�N B�LG�LER� �EK�L�YOR	//

if (!empty($_POST['ozel_yanitla']))
{
	$_POST['oino'] = @zkTemizle($_POST['oino']);

	$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE id='$_POST[oino]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$ozel_ileti = mysql_fetch_array($sonuc);

//	�ZEL �LET�Y� G�NDEREN VEYA ALAN DE��LSE DE���KENLER SIFIRLANIYOR	//

	if (($ozel_ileti['kime'] != $kullanici_kim['kullanici_adi']) AND ($ozel_ileti['kimden'] != $kullanici_kim['kullanici_adi']))
	{
		unset($ozel_yanitla);
		unset($ozel_ileti);
	}
}





//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/mesaj_yaz.html');





			//		�N�ZLEME TABLOSU BA�I		//


if ( isset($_POST['mesaj_onizleme']) ):

	if ( empty($_POST['mesaj_icerik']) ):
		$javascript_kapali = '<center><br><b><font size="3" color="red">�nizleme �zelli�i i�in tarayc�n�z�n java �zelli�inin a��k olmas� gereklidir.</b></center><br>';




	else:

$javascript_kapali = '';


//	ZARARLI KODLAR TEM�ZLEN�YOR	//
//	magic_quotes_gpc a��ksa	//

if (get_magic_quotes_gpc(1))
{
	$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),3);
	$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),4);
}


//	magic_quotes_gpc kapal�ysa	//
else
{
	$_POST['mesaj_baslik'] = @ileti_yolla($_POST['mesaj_baslik'],3);
	$_POST['mesaj_icerik'] = @ileti_yolla($_POST['mesaj_icerik'],4);
}



if (isset($ozel_kime))
$onizleme_kime = $ozel_kime;

else $onizleme_kime = '';


$onizleme_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, time());



$onizleme_mesaj = $_POST['mesaj_icerik'];

if ($_POST['ifade'] == 1) $onizleme_mesaj = ifadeler($onizleme_mesaj);

if ( ($_POST['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
$onizleme_icerik = bbcode_acik($onizleme_mesaj,1);

else $onizleme_icerik = bbcode_kapali($onizleme_mesaj);



//	veriler tema motoruna yollan�yor	//

$ornek1->kosul('3', array('{ONIZLEME_KIMDEN}' => $kullanici_kim['kullanici_adi'],
'{ONIZLEME_KIME}' => $onizleme_kime,
'{ONIZLEME_BASLIK}' => $_POST['mesaj_baslik'],
'{ONIZLEME_TARIH}' => $onizleme_tarih,
'{ONIZLEME_ICERIK}' => $onizleme_icerik), true);


endif;

else: $ornek1->kosul('3', array('' => ''), false);

endif;



						//	�N�ZLEME TABLOSU SONU	//





if (!empty($_POST['ozel_yanitla']))
{
	if (isset($_POST['ozel_kime'])) $oi_kime = @zkTemizle($_POST['ozel_kime']);
	else $oi_kime = $ozel_ileti['kimden'];
}

elseif (isset($ozel_kime))
	$oi_kime = $ozel_kime;

else $oi_kime = '';



if (isset($_POST['mesaj_baslik']))
	$form_baslik = $_POST['mesaj_baslik'];

elseif (!empty($_POST['ozel_yanitla']))
	$form_baslik = 'Cvp: '.$ozel_ileti['ozel_baslik'];

else $form_baslik = '';



$form_icerik = '';

if (!empty($_POST['mesaj_icerik']))
	$form_icerik .= $_POST['mesaj_icerik'];


elseif (!empty($_POST['ozel_yanitla']))
$form_icerik .= '[quote="'.$ozel_ileti['kimden'].'"]
'.$ozel_ileti['ozel_icerik'].'
[/quote]

';




//  BBCODE A�MA - KAPATMA    //

$form_ozellik = '';

if ($ayarlar['bbcode'] == 1)
{
	$form_ozellik .= '<label style="cursor: pointer;"><input type="checkbox" name="bbcode_kullan"';

	if (!empty($_POST['ozel_yanitla']))
		$form_ozellik .= 'checked="checked"';

	elseif ( (isset($_POST['bbcode_kullan'])) AND ($_POST['bbcode_kullan'] == 1) )
		$form_ozellik .= 'checked="checked"';

	$form_ozellik .= '>Bu iletide BBCode kullan</label>';
}

// bbcode kapal� ise
else $form_ozellik .= '<input type="hidden" name="bbcode_kullan">&nbsp;BBCode Kapal�';


//  �FADE A�MA - KAPATMA    //

$form_ozellik .= '<br><label style="cursor: pointer;"><input type="checkbox" name="ifade" ';

if ( (isset($_POST['ifade'])) AND ($_POST['ifade'] == 0) )
    $form_ozellik .= '';

else $form_ozellik .= 'checked="checked"';

$form_ozellik .= '>Bu iletide ifade kullan</label>';




if (isset($_POST['oino']))
	$oi_no = $_POST['oino'];

else $oi_no = '';



if (!empty($_POST['ozel_yanitla']))
	$form_kime = $ozel_ileti['kimden'];

elseif (isset($ozel_kime))
	$form_kime = $ozel_kime;

else $form_kime = '';



if (!empty($_POST['ozel_yanitla']))
	$form_yanitla = $_POST['ozel_yanitla'];

else $form_yanitla = '';





$form_bilgi1 = '<form action="oi_yaz_yap.php" method="post" onsubmit="return yolla(\'mesaj_icerik_div\',\'mesaj_icerik\',\'yolla\',\'cevir\'), denetle_ozel()" name="form1">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">';


$form_bilgi2 = '<form action="oi_yaz.php#onizleme" method="post" name="form2" onsubmit="return yolla(\'mesaj_icerik_div\',\'mesaj_icerik\',\'yolla\',\'cevir\'), onizle_ozel(), denetle_ozel()">
<input type="hidden" name="oino" value="'.$oi_no.'">
<input type="hidden" name="ozel_kime" value="'.$oi_kime.'">
<input type="hidden" name="ozel_yanitla" value="'.$form_yanitla.'">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">
<input type="hidden" name="bbcode_kullan" value="">
<input type="hidden" name="ifade" value="">
<input type="hidden" name="mesaj_baslik" value="">
<input type="hidden" name="mesaj_icerik" value="">';

$javascript_kodu = '<script type="text/javascript" src="dosyalar/betik_zengin.php"></script>
<script type="text/javascript" src="dosyalar/betik_mesaj.js"></script>
<script type="text/javascript">
<!-- //
function denetle_ozel(){ 
	var dogruMu = true;
	if (document.form1.ozel_kime.value.length < 4){
		dogruMu = false; 
		alert("�zel iletiyi g�ndermek istedi�iniz ki�inin ad�n� yaz�n�z !");}
	else if (document.form1.mesaj_baslik.value.length < 3){
		dogruMu = false; 
		alert("iletiye ba�l�k yazmay� unuttunuz !");}
	else if (document.form1.mesaj_icerik.value.length < 3){
		dogruMu = false; 
		alert("ileti yazmay� unuttunuz !");}
	else;
	return dogruMu;}
function onizle_ozel(){
	document.form2.ozel_kime.value = document.form1.ozel_kime.value;
	document.form2.mesaj_baslik.value = document.form1.mesaj_baslik.value;
	document.form2.mesaj_icerik.value = document.form1.mesaj_icerik.value;
	if (document.form1.bbcode_kullan){
		if (document.form1.bbcode_kullan.checked == true)
			document.form2.bbcode_kullan.value = 1;
		else document.form2.bbcode_kullan.value = 0;}
	if (document.form1.ifade){
		if (document.form1.ifade.checked == true)
			document.form2.ifade.value = 1;
		else document.form2.ifade.value = 0;}}
function uye_ara(){
	var uye = document.form1.ozel_kime.value;
	window.open("oi_yaz.php?kip=1&uye_ara="+uye, "_uyeara", "resizable=yes,width=390,height=350,scrollbars=yes");}
//  -->
</script>';


$javascript_kodu2 = '<script type="text/javascript">
<!-- //
yolla2(\'mesaj_icerik\',\'mesaj_icerik_div\',\'cevir\');
var alan1 = document.getElementById(\'mesaj_icerik_div\');
alan1.designMode="On";
alan1.contentEditable = "true";
alan1.indicateeditable="true";
alan1.useCSS="false";
alan1.styleWithCSS = "false";
alan1.useHeader="false";
var alan2 = document.getElementById(\'mesaj_icerik\');
alan2.focus();
var zengin=cerez_oku("zengin");
if (zengin==1)duzenleyici_degis();
var zenginboyut=cerez_oku("zenginboyut");
if (zenginboyut==0);
else if(zenginboyut>0){for(i=0;zenginboyut>i;i++)alan_buyut("buyut",true);}
else if(zenginboyut<0){for(i=0;zenginboyut<i;i--)alan_buyut("kucult",true);}
//  -->
</script>';


if ($form_icerik == '') $form_icerik = '';
if (!isset($javascript_kapali)) $javascript_kapali = '';



//	TEMA UYGULANIYOR	//

$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), true);


$dongusuz = array('{JAVASCRIPT_KAPALI}' => $javascript_kapali,
'{SAYFA_KIP}' => '�zel �leti Yaz',
'{OI_KIME}' => $oi_kime,
'{FORM_BASLIK}' => $form_baslik,
'{FORM_ICERIK}' => $form_icerik,
'{FORM_OZELLIK}' => $form_ozellik,
'{FORM_BILGI1}' => $form_bilgi1,
'{FORM_BILGI2}' => $form_bilgi2,
'{IFADELER}' => ifade_olustur('5'),
'{JAVASCRIPT_KODU}' => $javascript_kodu,
'{JAVASCRIPT_KODU2}' => $javascript_kodu2);


$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>