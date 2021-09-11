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


// RESMÝ DUYURU - BAÞI //

if ( (isset($_GET['duyuru'])) AND ($_GET['duyuru'] == 'forum' ) OR (isset($_GET['fsurum'])) AND ($_GET['fsurum'] != '' ) )
{
	header('Content-type: text/html');
	header("Content-type: text/html; charset=iso-8859-9");
	header('Content-Language: tr');

	if (isset($_GET['duyuru'])) $ek = 'duyuru=forum';
	elseif (isset($_GET['fsurum'])) $ek = 'fsurum='.$_GET['fsurum'];

	if (!isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = 'http://'.$ayarlar['alanadi'].$ayarlar['f_dizin'].'/yonetim/index.php';

$out = "GET /resmi_duyuru.php?$ek HTTP/1.1,
Accept: text/html
Accept-Encoding: iso-8859-9
Host: www.phpkf.com
Referer: $_SERVER[HTTP_REFERER]
User-Agent: phpKF Resmi Duyuru ve Surum Denetleme
Connection: close

";


	// fsockopen fonksiyonu engellenmiþse
	if (!function_exists('fsockopen'))
	{
		echo '<font color="#ff0000"><b>fsockopen() fonksiyonu engellenmiþ !</b></font>';

		if ( (isset($_GET['duyuru'])) AND ($_GET['duyuru'] == 'forum' ) )

		echo '<br><br><b>Sunucu ayarlarýnýz www.phpkf.com adresine baðlanmayý desteklemiyor veya izin vermiyor !</b><br><br>Duyurular aþaðýda çevreve (iframe) içinde açýlacaktýr.<br><br>
		<hr width="100%" style="color: #000000"><br>
		<iframe src="http://www.phpkf.com/resmi_duyuru.php?duyuru=forum" name="duyuru" height="250" width="530" frameborder="0">Tarayýcýnýz iframe özelliðini desteklemiyor veya kapalý. Baþka bir tarayýcýda deneyin veya kullandýðýnýz tarayýcýnýn iframe özelliðini açýn.</iframe>';

		exit();
	}



	$cikis = '';
	$adres = 'www.phpkf.com';
	$baglanti = @fsockopen($adres, 80, $hatano, $hata, 10);

	if(!$baglanti)
	{
		echo '<font color="#ff0000"><b>Baðlantý Kurulamadý !</b></font><br>';
		echo "$hata ($hatano)<br><br>";
		exit();
	}

	@fputs($baglanti, $out);
	$satir = @fgets($baglanti);

	if (@substr_count($satir, "200 OK") > 0)
	{
		$baslik = false;
		while(!@feof($baglanti))
		{
			$satir = @fgets($baglanti);
			if ($satir == "\r\n") $baslik = true;
			if ($baslik) $cikis .= $satir;
		}
	}

	else $cikis .= '<font color="#ff0000"><b>Baðlantý Kurulamadý !</b></font><br>';

	@fclose($baglanti);

	echo $cikis;
	exit();
}

// RESMÝ DUYURU - SONU //



$sayfa_adi = 'Yönetim Ana Sayfasý';
include 'yonetim_baslik.php';


//	tema dosyasý açýlýyor	//
function tema_dosyasi($dosya)
{
	if (!($dosya_ac = @fopen($dosya,'r')))
		die ('<p><font color="red"><b>Tema Dosyasý Açýlamýyor '.$dosya.'</b></font></p>');

	$boyut = @filesize($dosya);
	$dosya_metni = @fread($dosya_ac,$boyut);
	@fclose($dosya_ac);

	return $dosya_metni;
}


// RESIM DÝZÝNÝ BOYUTU ÖLÇÜLÜYOR //

function dizinboyut($dizinadi)
{
	if ((!is_dir($dizinadi)) OR (!is_readable($dizinadi)))
		return false;

	$dizinadi_dizi[] = $dizinadi;
	$boyut = 0;

	do
	{
		$dizinadi = array_shift($dizinadi_dizi);
		$klasor = opendir($dizinadi);

		while (false !== ($dosya = readdir($klasor)))
		{
			if ( ($dosya != '.') AND ($dosya != '..') AND (is_readable($dizinadi . DIRECTORY_SEPARATOR . $dosya)) )
			{
				if (is_dir($dizinadi . DIRECTORY_SEPARATOR . $dosya))
					$dizinadi_dizi[] = $dizinadi . DIRECTORY_SEPARATOR . $dosya;

				$boyut += filesize($dizinadi . DIRECTORY_SEPARATOR . $dosya);
			}
		}
		closedir($klasor);
	}
	while (count($dizinadi_dizi) > 0);

	return $boyut;
}



//	GD KUTUPHANESÝ BÝLGÝSÝ	//

$gd_bilgisi = '';

if (@extension_loaded('gd'))
{
	$gd_bilgi = @gd_info();

	$gd_bilgisi .= 'GD Bilgisi: '.$gd_bilgi['GD Version'];

	if ($gd_bilgi['PNG Support'] == true)
		$gd_bilgisi .= '<br>PNG Bilgisi: destekleniyor';

	else $gd_bilgisi .= '<br><font color="#ff0000">Sunucunuz PNG desteklemiyor, onay kodu çalýþmaz !<br>"Genel Ayarlar" sayfasýndan kapatabilirsiniz.</font></font>';
}

else $gd_bilgisi .= '<font color="#ff0000">Sunucunuz GD desteklemiyor, onay kodu çalýþmaz !<br>"Genel Ayarlar" sayfasýndan kapatabilirsiniz.</font><br>';



//  GZÝP DOSYA SIKIÞTIRMA DESTEÐÝ   //

if (@extension_loaded('zlib'))
	$gzip = '<font color="#007900">Var</font>';

else $gzip = '<font color="#ff0000"><b>Yok</b></font>';



//  SUNUCUNUN register_globals AYARINA BAKILIYOR //

if(@ini_get('register_globals'))
{
	$register_globals = '<font color="#ff0000">
Sunucunuzun register_globals ayarý açýk durumda !
<br>Sitenizin güvenliði için bu ayarý kapatmanýz önerilir.
<br><br>Kapatmak için <a href="http://www.phpkf.com/k820-registerglobals-evrensel-kayit-ozelligini-kapatma.html" target="_blank">bu sayfaya bakýn.</a>
</font>';
}

else $register_globals = '<font color="#007900">Kapalý</font>';



//  SUNUCUNUN safe_mode AYARINA BAKILIYOR //

if(@ini_get('safe_mode')) $safe_mode = '<font color="#007900">Açýk</font>';

else $safe_mode = 'Kapalý';




//	VERÝTABANI BOYUTU HESAPLANIYOR - BAÞI	//

$strSQL = "SHOW TABLE STATUS LIKE '%'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

$toplam_boyut = 0;

while ($tablo_bilgileri = mysql_fetch_array($sonuc))
$toplam_boyut += ($tablo_bilgileri['Data_length'] + $tablo_bilgileri['Index_length']);

$vt_boyutu = sprintf("%.2f" , ($toplam_boyut / 1024));
$vt_boyutu .= ' kb.';




$acilis_tarihi = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $forum_acilis);

$phpkf_surum = $ayarlar['surum']. ' &nbsp; ';

$surum_denetle = '<a href="javascript:void(0);" onclick="yenile(\'katman_surum2\', \''.$ayarlar['surum'].'\')">Sürümü Denetle</a>';


if (@PHP_OS) $sunucu_is = @PHP_OS;
elseif (isset($_ENV['TERM'])) $sunucu_is = $_ENV['TERM'];
elseif (isset($_ENV['OS'])) $sunucu_is = $_ENV['OS'];


$resim_boyutu = sprintf("%.2f" , ((dizinboyut('../dosyalar/resimler/yuklenen/')) / 1024));
$resim_boyutu .= ' kb.';

$eklentiler_boyutu = sprintf("%.2f" , ((dizinboyut('../eklentiler/')) / 1024));
$eklentiler_boyutu .= ' kb.';




// RESMÝ DUYURU EKRANI - BAÞI //

$phpkf_duyuru = '<br>
<noscript><br><font color="#ff0000">
<b>Tarayýcýnýz javascript desteklemiyor veya kapalý !
<br>Bu özellik için javascript desteði gereklidir !</b><br>
</font></noscript>


<div id="katman_duyuru1" style="float:left; border:0px solid #000000">

<script type="text/javascript">
<!-- //
document.write(\'<b>&nbsp; Güncel duyurularý almak için <a href="javascript:void(0);" onclick="duyuru(\\\'katman_duyuru1\\\')">týklayýn.</a></b>\');
//  -->
</script>

</div>
';



$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2011 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  Tüm haklarý saklýdýr - All Rights Reserved

function GonderAl(adres,katman){
var katman1 = document.getElementById(katman);
var veri_yolla = \'name=value\';
if (document.all) var istek = new ActiveXObject("Microsoft.XMLHTTP");
else var istek = new XMLHttpRequest();
istek.open("GET", adres, true);

istek.onreadystatechange = function(){
if (istek.readyState == 4){
    if (istek.status == 200) katman1.innerHTML = istek.responseText;
    else katman1.innerHTML = \'<font color="#ff0000"><b>Baðlantý Kurulamadý !</b></font>\';}};
istek.send(veri_yolla);}

function yenile(katman,veri){
adres = \'index.php?fsurum=\'+veri;
var katman1 = document.getElementById(katman);
katman1.innerHTML = \'<img src="../dosyalar/yukleniyor.gif" width="18" height="18" alt="Yü." title="Yükleniyor...">\';
setTimeout("GonderAl(\'"+adres+"\',\'"+katman+"\')",1000);}

function duyuru(katman){
adres = \'index.php?duyuru=forum\';
var katman1 = document.getElementById(katman);
katman1.innerHTML = \'<img src="../dosyalar/yukleniyor.gif" width="18" height="18" alt="Yü." title="Yükleniyor...">\';
setTimeout("GonderAl(\'"+adres+"\',\'"+katman+"\')",1000);}';

$tarih = time();

if (($ayarlar['duyuru_tarihi']+259200) < $tarih)
{
	$strSQL = "UPDATE $tablo_ayarlar SET deger='$tarih' WHERE etiket='duyuru_tarihi' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$javascript_kodu .= 'duyuru(\'katman_duyuru1\');
	yenile(\'katman_surum2\', \''.$ayarlar['surum'].'\');';
}

$javascript_kodu .= '
// -->
</script>';

// RESMÝ DUYURU EKRANI - SONU //




//	TEMA UYGULANIYOR	//

$yonetim_sol_menu = tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html');


//	veriler tema motoruna yollanýyor	//

$dongusuz = array('{YONETIM_SOL_MENU}' => $yonetim_sol_menu,
'{ACILIS_TARIHI}' => $acilis_tarihi,
'{PHPKF_SURUM}' => $phpkf_surum,
'{MYSQL_SURUM}' => @mysql_get_server_info(),
'{PHP_SURUM}' => @phpversion(),
'{ZEND_SURUM}' => @zend_version(),
'{GD_BILGI}' => $gd_bilgisi,
'{GZIP}' => $gzip,
'{REGISTER_GLOBALS}' => $register_globals,
'{SAFE_MODE}' => $safe_mode,
'{SUNUCU_IS}' => $sunucu_is,
'{SUNUCU_BILGI}' => $_SERVER['SERVER_SOFTWARE'],
'{RESIM_BOYUTU}' => $resim_boyutu,
'{EKLENTILER_BOYUTU}' => $eklentiler_boyutu,
'{VT_BOYUTU}' => $vt_boyutu,
'{SURUM_DENETLE}' => $surum_denetle,
'{JAVASCRIPT_KODU}' => $javascript_kodu,
'{PHPKF_DUYURU}' => $phpkf_duyuru);


$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/index.html');

$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>