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


$resim_boyut = 563200;
$resim_genislik = 900;
$resim_yukseklik = 700;
$sure = 20;
$dosya_yolu = 'dosyalar/yuklemeler/';
$tarih = time();
$bicim_tarih = date('d.m.Y- H:i:s', $tarih);
$cikis1 = '<br><center><font color="#ff0000"><b>';
$cikis2 = '<p>&nbsp; <a href="dyukle.php">&laquo; &nbsp;geri</a></b></font></center>';
$cikis3 = '</b></font></center>';



if (!defined('DOSYA_AYAR')) include 'ayar.php';

if ($ayarlar['f_dizin'] == '/') $f_dizin = '';
else $f_dizin = $ayarlar['f_dizin'];

$adres = 'http://'.$ayarlar['alanadi'].$f_dizin.'/';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';


// �ye kontrol� yap�l�yor
if (!isset($kullanici_kim['id']))
{
	echo $cikis1.'Sadece �yeler y�kleme yap�labilir !<br><br></font><font color="#000000"><a href="giris.php" target="_blank">Giri�</a> &nbsp; - &nbsp; <a href="kayit.php" target="_blank">Kay�t</a>'.$cikis3;
	exit();
}



// Y�KLENEN DOSYA KONTROL ED�L�YOR  //

if ( (isset($_FILES['yukle']['tmp_name'])) AND ($_FILES['yukle']['tmp_name'] != '') ):

//  �ift t�klanma olas�l���na kar�� 1 saniye bekleniyor
sleep(1);

if (!defined('DOSYA_GERECLER')) include 'gerecler.php';

@session_start();

if (!isset($_SESSION['yukleme_zamani'])) $_SESSION['yukleme_zamani'] = 0;


// ardarda y�kleme yap�lmas� engelleniyor
if (($_SESSION['yukleme_zamani']+$sure) > $tarih)
{
	echo $cikis1.'Son y�klemenizin �zerinden '.$sure.' saniye ge�meden ba�ka y�kleme yapamazs�n�z !<br>Kalan s�re '.(($_SESSION['yukleme_zamani'] + $sure) - $tarih).' saniye.'.$cikis2;
	exit();
}



// dosya boyutuna bak�l�yor
if ($_FILES['yukle']['size'] > $resim_boyut)
{
	echo $cikis1.'Y�klemeye �al��t���n�z dosya '.($resim_boyut/1024).' kilobayt`dan b�y�k olamaz !'.$cikis2;
	exit();
}




list($genislik, $yukseklik, $tip) = @getimagesize($_FILES['yukle']['tmp_name']);
$uzanti = end(explode(".", strtolower($_FILES['yukle']['name'])));


// zip uzant�l� dosyalar

if ($uzanti == 'zip')
{
	$uzanti = '.zip';

	if (@extension_loaded('zip'))
	{
		$arsiv = new ZipArchive;
		$zip_dosya = $arsiv->open($_FILES['yukle']['tmp_name']);

		if ($zip_dosya !== true)
		{
			echo $cikis1.'Y�klemeye �al��t���n�z zip dosyas� bozuk !'.$cikis2;
			exit();
		}
	}

	else
	{
		echo $cikis1.'Sunucuda zip deste�i yok !'.$cikis2;
		exit();
	}
}


// gif uzant�l� dosyalar

elseif ((isset($tip)) AND ($tip == 1))
{
	$uzanti = '.gif';

	if (!@imagecreatefromgif($_FILES['yukle']['tmp_name']))
	{
		echo $cikis1.'Y�klemeye �al��t���n�z resim bozuk !'.$cikis2;
		exit();
	}
}


// jpg uzant�l� dosyalar

elseif ((isset($tip)) AND ($tip == 2))
{
	$uzanti = '.jpg';

	if (!@imagecreatefromjpeg($_FILES['yukle']['tmp_name']))
	{
		echo $cikis1.'Y�klemeye �al��t���n�z resim bozuk !'.$cikis2;
		exit();
	}
}


// png uzant�l� dosyalar

elseif ((isset($tip)) AND ($tip == 3))
{
	$uzanti = '.png';

	if (!@imagecreatefrompng($_FILES['yukle']['tmp_name']))
	{
		echo $cikis1.'Y�klemeye �al��t���n�z resim bozuk !'.$cikis2;
		exit();
	}
}


// kabul edilmeyen uzant�l� dosyalar

else
{
	echo $cikis1.'Sadece jpg, gif, png resimleri ve zip dosyalar� y�klenebilir ! <br>E�er dosyan�z do�ru tipte ise bozuk olabilir.'.$cikis2;
	exit();
}




// resim en ve boyuna bak�l�yor
if (($genislik > $resim_genislik) OR ($yukseklik > $resim_yukseklik))
{
	echo $cikis1.'Y�klemeye �al��t���n�z resmin boyutlar� '.$resim_genislik.'x'.$resim_yukseklik.'`den b�y�k olamaz !'.$cikis2;
	exit();
}


// dosya ad� olu�turuluyor
$dosya_adi = $kullanici_kim['id'].'-'.$tarih.rand(1111, 9999).$uzanti;
$dosya_yolu2 = $dosya_yolu.$dosya_adi;


// dosya ta��n�yor
if (!@move_uploaded_file($_FILES["yukle"]["tmp_name"],$dosya_yolu2))
{
	echo $cikis1.'Dosya y�klenemedi, dizine yazma hakk� yok !<br><br>Y�neticiyseniz FTP program�n�zdan '.$dosya_yolu.'<br>dizinine yazma hakk� vermeyi (chmod 777) deneyin.'.$cikis2;
	exit();
}



$dosya_adresi = $adres.$dosya_yolu2;

$_SESSION['yukleme_zamani'] = $tarih;

$_SERVER['REMOTE_ADDR'] = @zkTemizle($_SERVER['REMOTE_ADDR']);

$boyut = $_FILES['yukle']['size'];
settype($boyut,'integer');
$boyut = ($boyut / 1024);
settype($boyut,'integer');
$boyut++;



// dosya_yukleme tablosuna giriliyor

$satir = "INSERT INTO $tablo_yuklemeler (tarih,boyut,ip,uye_id,uye_adi,dosya) VALUES ('$tarih', '$boyut', '$_SERVER[REMOTE_ADDR]', '$kullanici_kim[id]', '$kullanici_kim[kullanici_adi]', '$dosya_adi')";
$sonuc = mysql_query($satir) or die ('<h2>sorgu ba�ar�s�z</h2>');



echo "<html>
<head>
</head>
<body>
<script type=\"text/javascript\">
<!--
function add_code()
{
	var text = '$dosya_adresi';
	dosya_adi = '$dosya_adi';
	var div_katman = opener.document.getElementById('mesaj_icerik_div');


	if (div_katman.style.display == 'inline')
	{
		div_katman.focus();
		if (dosya_adi.match(/.jpg/gim)) veri = '<img src=\"' + text + '\">';
		else if (dosya_adi.match(/.gif/gim)) veri = '<img src=\"' + text + '\">';
		else if (dosya_adi.match(/.png/gim)) veri = '<img src=\"' + text + '\">';
		else veri = '<a href=\"' + text + '\"><b>' + dosya_adi + '</b></a>';

		if (document.all)
		{
			var imlec = opener.document.selection.createRange();
			var metin = opener.document.selection.createRange().text;

			metin = metin + veri;
			imlec.pasteHTML(metin);
			imlec.collapse(false);
			imlec.select();
		}

		else
		{
			opener.document.execCommand('insertHTML', false, veri);
			div_katman.focus();
		}
	}


	else
	{
		if (dosya_adi.match(/.jpg/gim)) veri = '[img]' + text + '[/img]';
		else if (dosya_adi.match(/.gif/gim)) veri = '[img]' + text + '[/img]';
		else if (dosya_adi.match(/.png/gim)) veri = '[img]' + text + '[/img]';
		else veri = '[url=' + text + '][b]' + dosya_adi + '[/b][/url]';

		opener.document.forms['form1'].mesaj_icerik.value += veri;
		opener.focus();
	}

	window.close();
}
add_code();
//-->
</script>
</body>
</html>";






else:

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9">
<meta http-equiv="Content-Language" content="tr">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<title>Dosya Y�kleme</title>
<script type="text/javascript">
<!-- //
function denetle(){
var dogruMu = true;
if (document.yukleme.yukle.value.length < 4){
dogruMu = false; 
alert("Dosya se�meyi unuttunuz !");}
else;
return dogruMu;}
function kilitle(){
var dogruMu = false;
if (document.yukleme.yukle.value.length > 4){
document.yukleme.yolla.value=\'Y�kleniyor...\';
document.yukleme.yolla.disabled=\'disabled\';
document.yukleme.submit();
dogruMu = true;}
var dogruMu;}
//  -->
</script>
</head>
<body>
<div align="center" style="width:350px;">
<font style="font-family:verdana;font-weight:bold;font-size:18px;">- Dosya Y�kleme -</font>
<br><br><p align="left"><font style="font-family:verdana;font-size:11px;">
<br><b>Kabul edilen dosya tipleri:</b>&nbsp; zip, jpg, gif, png
<br>
<b>Azami dosya boyutu:</b>&nbsp; '.($resim_boyut/1024).' kb.
<br>
<b>Azami resim b�y�kl���:</b>&nbsp; '.$resim_genislik.' x '.$resim_yukseklik.'
</font></p><br>
<form name="yukleme" action="dyukle.php" method="post" enctype="multipart/form-data" onsubmit="return denetle()">
<input type="hidden" name="MAX_FILE_SIZE" value="8388608">
<input name="yukle" type="file" size="30">
<br><br>
<input type="submit" name="yolla" value="Y�kle" onclick="return kilitle()">
</form>
</div>
</body>
</html>';
endif;

?>