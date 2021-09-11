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


if (!defined('PHPKF_ICINDEN')) define('PHPKF_ICINDEN', true);
if (!defined('DOSYA_AYAR')) include 'ayar.php';
$sayfano = 9;


// �ye al�m� kapal�ysa
if ($ayarlar['uye_kayit'] != '1')
{
	header('Location: hata.php?uyari=9');
	exit();
}


//  KULLANICI ADI KONTROL� - BA�I  //

if ((isset($_GET['kosul'])) AND ($_GET['kosul'] == 'kadi')):


header('Content-type: text/html');
header("Content-type: text/html; charset=iso-8859-9");
header('Content-Language: tr');


if ((!isset($_GET['kadi'])) OR ($_GET['kadi'] == ''))
{
	echo 'Kullan�c� ad� girmediniz.';
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_������������.]+$/', $_GET['kadi']))
{
	echo 'Ge�ersiz karakterler var.';
	exit();
}

if (( strlen($_GET['kadi']) > 20) or ( strlen($_GET['kadi']) < 4))
{
	echo 'En az 4, en fazla 20 karakter olmal�d�r.';
	exit();
}



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


		if (preg_match("/$ysk_kuladd[$d]/i", $_GET['kadi']))
		{
			echo 'Bu kullan�c� ad� yasaklanm��t�r.';
			exit();
		}
	}
}


// KULLANICI ADININ DAHA �NCE ALINIP ALINMADI�I DENETLEN�YOR //

$strSQL = "SELECT kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$_GET[kadi]'";
$sonuc = mysql_query($strSQL);

if (mysql_num_rows($sonuc))
{
	echo 'Bu kullan�c� ad� kullan�lmaktad�r.';
	exit();
}


echo '<font color="green"><b>Uygun</b></font>';

//  KULLANICI ADI KONTROL� - SONU  //




//     NORMAL G�STER�M     //
//     NORMAL G�STER�M     //


else:


//	GE�ERS�Z B�R �EREZ VARSA �IKIS SAYFASINA Y�NLEND�R�L�YOR	//

if (isset($_COOKIE['kullanici_kimlik'])):
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';

if (empty($kullanici_kim['id'])):
setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
header('Location: '.$forum_index);
exit();


//	G�R�� YAPILMI�SA PROF�LE Y�NLEND�R	//

elseif (isset($kullanici_kim['id'])):
header('Location: profil.php');
exit();
endif;


else:
//	oturum a�l�yor	//
@session_start();




//  KAYIT KO�ULLARI - BA�I  //

if ( (empty($_GET['kosul'])) OR ((isset($_GET['kosul'])) AND ($_GET['kosul'] != 'kabul')) ):
$sayfa_adi = 'Forum �yelik Ko�ullar�';
include 'baslik.php';


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/kayit.html');
$ornek1->kosul('1', array('' => ''), true);
$ornek1->kosul('2', array('' => ''), false);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
exit();

//  KAYIT KO�ULLARI - SONU  //





elseif ( (isset($_GET['kosul'])) AND ($_GET['kosul'] == 'kabul') ):
$sayfa_adi = 'Kullan�c� Kay�t';
include 'baslik.php';




if (isset($_SESSION['kullanici_adi']))
	$kullanici_adi = $_SESSION['kullanici_adi'];

else $kullanici_adi = '';


if (isset($_SESSION['gercek_ad']))
	$ad_soyad = $_SESSION['gercek_ad'];

else $ad_soyad = '';


if (isset($_SESSION['posta']))
	$eposta = $_SESSION['posta'];

else $eposta = '';


$onay_id = session_id().'&amp;sayi='.sha1(microtime());


if (isset($_SESSION['dogum_gun']))
	$dogum_gunu = '<option value="'.$_SESSION['dogum_gun'].'">'.$_SESSION['dogum_gun'].'</option>';

else $dogum_gunu = '<option value="" selected="selected">-G�n-</option>';



$dogum_ayi = '<option value=""> &nbsp; - Ay -</option>
<option value="01"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 01))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Ocak</option>
<option value="02" ';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 02)) 
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>&#350;ubat</option>
<option value="03"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 03))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Mart</option>
<option value="04"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 04))
	$dogum_ayi .= 'selected="selected"';

$dogum_ayi .= '>Nisan</option>
<option value="05"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 05))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>May&#305;s</option>
<option value="06"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 06))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Haziran</option>
<option value="07"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 07))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Temmuz</option>
<option value="08"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == '08'))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>A&#287;ustos</option>
<option value="09"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == '09'))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Eyl�l</option>
<option value="10"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 10))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Ekim</option>
<option value="11"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 11))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Kas�m</option>
<option value="12"';

if (isset($_SESSION['dogum_ay']) and ($_SESSION['dogum_ay'] == 12))
	$dogum_ayi .= ' selected="selected"';

$dogum_ayi .= '>Aral&#305;k</option>';


if(isset($_SESSION['dogum_yil']))
	$dogum_yili = $_SESSION['dogum_yil'];

else $dogum_yili = '';



if(isset($_SESSION['sehir']))
	$sehir = '<option value="'.$_SESSION['sehir'].'">'.$_SESSION['sehir'].'</option>';

else $sehir = '<option value="">-- Se&#231;iniz --</option>';





//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/kayit.html');


// kay�t sorusu �zelli�i a��k ise

if ($ayarlar['kayit_soru'] == 1)
{
	if (isset($_SESSION['kayit_cevabi']))
		$kayit_cevabi = $_SESSION['kayit_cevabi'];

	else $kayit_cevabi = '';

	$ornek1->kosul('3', array('{KAYIT_SORUSU}' => $ayarlar['kayit_sorusu'],
								'{KAYIT_CEVABI}' => $kayit_cevabi), true);

	$form_alan_sayi = 12;
}

else 
{
	$ornek1->kosul('3', array('' => ''), false);
	$form_alan_sayi = 11;
}


// onay kodu a��k ise

if ($ayarlar['onay_kodu'] == '1')
{
	$ornek1->kosul('4', array('{ONAY_ID}' => $onay_id), true);
	$form_alan_sayi++;
}

else $ornek1->kosul('4', array('' => ''), false);



//  session dizisi siliniyor
$_SESSION = 0;


$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2013 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  T�m haklar� sakl�d�r - All Rights Reserved

function denetle(){
var dogruMu = true;
for (var i=0; i<'.$form_alan_sayi.'; i++){
	if (document.form1.elements[i].value == \'\'){
		dogruMu = false; 
		alert(\'T�M ALANLARIN DOLDURULMASI ZORUNLUDUR !\');
		break;}}
if (document.form1.sifre.value != document.form1.sifre2.value){
	dogruMu = false;
	alert(\'YAZDI�INIZ ��FRELER UYU�MUYOR !\');}
return dogruMu;}
function dogrula(girdi_ad, girdi_deger){
var alan = girdi_ad + \'-alan\';
if (girdi_ad == \'kullanici_adi\'){
	var kucuk = 4;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_������������.]+$/;
	var katman = document.getElementById("kullanici_adi-alan2");
	katman.innerHTML = \'<a href="javascript:void(0);" onclick="KAdi()"><b>Kontrol Et</b></a>\';}
else if (girdi_ad == \'gercek_ad\'){
	var kucuk = 4;
	var buyuk = 30;
	var desen = /^[A-Za-z0-9-_ ������������.]+$/;}
else if (girdi_ad == \'posta\'){
	var kucuk = 4;
	var buyuk = 70;
	var desen = /^([-!#\$%&*+./0-9=?A-Z^_`a-z{|}~])+\@(([-!#\$%&*+/0-9=?A-Z^_`a-z{|}~])+\.)+([a-zA-Z0-9]{2,4})+$/;}
else if (girdi_ad == \'sifre\'){
	var kucuk = 5;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_.&]+$/;}
else if (girdi_ad == \'sifre2\'){
	var kucuk = 5;
	var buyuk = 20;
	var desen = /^[A-Za-z0-9-_.&]+$/;}
else if (girdi_ad == \'onay_kodu\'){
	var kucuk = 6;
	var buyuk = 6;
	var desen = /^[A-Za-z0-9]+$/;}
else if (girdi_ad == \'dogum_yil\'){
	var kucuk = 4;
	var buyuk = 4;
	var desen = /^[0-9]+$/;}
if ( girdi_deger.length < kucuk || girdi_deger.length > buyuk )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
else if ( !girdi_deger.match(desen) )
	document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/yanlis.png" alt="yanl��">\';
else document.getElementById(alan).innerHTML=\'<img width="17" height="17" src="temalar/'.$ayarlar['temadizini'].'/resimler/dogru.png" alt="do�ru">\';}
function GonderAl(adres,katman){
var katman1 = document.getElementById(katman);
var veri_yolla = "name=value";
if (document.all) var istek = new ActiveXObject("Microsoft.XMLHTTP");
else var istek = new XMLHttpRequest();
istek.open("GET", adres, true);
istek.onreadystatechange = function(){
if (istek.readyState == 4){
	if (istek.status == 200) katman1.innerHTML = istek.responseText;
	else katman1.innerHTML = "<b>Ba�lant� Kurulamad� !</b>";}};
istek.send(veri_yolla);}
function KAdi(){
var veri = document.form1.kullanici_adi.value;
if(veri != \'\'){
var adres = "kayit.php?kosul=kadi&kadi="+veri;
var katman = "kullanici_adi-alan2";
var katman1 = document.getElementById(katman);
katman1.innerHTML = \'<img src="dosyalar/yukleniyor.gif" width="18" height="18" alt="Y�." title="Y�kleniyor...">\';
setTimeout("GonderAl(\'"+adres+"\',\'"+katman+"\')",1000);}}
//  -->
</script>';




$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('{JAVASCRIPT_KODU}' => $javascript_kodu), true);


$ornek1->dongusuz(array('{SESSION_ID}' => session_id(),
'{KULLANICI_ADI}' => $kullanici_adi,
'{AD_SOYAD}' => $ad_soyad,
'{EPOSTA}' => $eposta,
'{DOGUM_GUNU}' => $dogum_gunu,
'{DOGUM_YILI}' => $dogum_yili,
'{DOGUM_AYI}' => $dogum_ayi,
'{SEHIR}' => $sehir,
'{PHP_SESSID}' => $_COOKIE['PHPSESSID']));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);
endif;
endif;
endif;

?>