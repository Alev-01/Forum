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


// Se�ilen dil �ereze giriliyor

if ((isset($_GET['dil'])) AND ($_GET['dil'] != ''))
{
	// forum dizini al�n�yor
	$forum_dizin = dirname(dirname($_SERVER['PHP_SELF'] ));
	if ($forum_dizin == '\\') $forum_dizin = '/'; 

	if ($_GET['dil'] == 'english')
	{
		@setcookie('forum_dili', 'english', time()+604800, $forum_dizin);
		header('Location: yukleme.php');
		exit();
	}
	else
	{
		@setcookie('forum_dili', '', 0, $forum_dizin);
		header('Location: yukleme.php');
		exit();
	}
}


// dil dosyas� y�kleniyor

if ((isset($_COOKIE['forum_dili'])) AND ($_COOKIE['forum_dili'] != ''))
{
	if ($_COOKIE['forum_dili'] == 'english') include 'dil_english.php';
	else include 'dil_turkce.php';
}
else include 'dil_turkce.php';



@ini_set('magic_quotes_runtime', 0);

		//	VER�TABANI YEDE�� Y�KLEME KISMI - BA�I	//

if ( (isset($_POST['vt_yukleme'])) AND ($_POST['vt_yukleme'] == 'vt_yukleme') ):

//  HATA TABLOSU    //

$hata_tablo1 = '<br><br><br><table border="0" cellspacing="1" cellpadding="7" width="530" bgcolor="#999999" align="center">
<tr><td bgcolor="#eeeeee" align="center"><font color="#ff0000"><b>';

$hata_tablo2 = '</b></font></td></tr>
<tr><td bgcolor="#fafafa">
<table border="0" cellspacing="1" cellpadding="7" width="100%" bgcolor="#999999" align="center"><tr><td bgcolor="#eeeeee" align="left"><br>';

$hata_tablo3 = '<br><br></td></tr></table>';



if ( (empty($_POST['vt_sunucu'])) OR (empty($_POST['vt_adi'])) )
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Veritaban� kullan�c� ad� ve �ifresi hari� t�m alanlar�n doldurulmas� zorunludur!'.$hata_tablo3;
	exit();
}


//	DOSYA Y�KLEMEDE HATA OLURSA - DOSYA 2`MB. DAN B�Y�KSE	//

if ( (isset($_FILES['vtyukle']['error'])) AND ($_FILES['vtyukle']['error'] != 0) )
{
	echo '<h2>hata_iletisi= Dosya Y�klenemedi, Dosya ad� al�namad� !<p>Bunun nedeni dosyan�n 2mb.`dan b�y�k olmas� ya da<br />dosya ad�n�n kabul edilmeyen karakterler i�ermesi olabilir. <p>Yede�i tablo tablo ayr� dosyalara b�lmeyi deneyin veya dosya ad�n� de�i�tirmeyi deneyin.</h2>'.$_FILES['vtyukle']['tmp_name'].' - '.$_FILES['vtyukle']['error'];
	exit();
}


//	DOSYA 2`MB. DAN B�Y�KSE	//
if ( (isset($_FILES['vtyukle']['tmp_name'])) AND ($_FILES['vtyukle']['tmp_name'] != '') )
{
	if ($_FILES['vtyukle']['size'] > 5242880)
	{
		echo '<h2>hata_iletisi= 5mb.`dan b�y�k yedek y�kleyemezsiniz. <br />Yede�i tablo tablo ayr� dosyalara b�lmeyi deneyin.</h2>';
		exit();
	}
}


$uzanti = end(explode(".", strtolower($_FILES['vtyukle']['name'])));


//	DOSYA SIKI�TIRILMI� MI BAKILIYOR	//

if ($uzanti == 'gz'):

	if(extension_loaded('zlib'))
	{
		$gzipdosya01 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
		$gzipac01 = gzread( $gzipdosya01, 9921920 );
		gzclose($gzipdosya01);

		//	�ift s�k��t�r�l�m�� olma olas�l���na kar�� tekrar a��l�yor
		$yeni_gzipdosya = fopen($_FILES['vtyukle']['tmp_name'], 'w') or die ("Dosya a��lam�yor!");
		fwrite($yeni_gzipdosya, $gzipac01);
		fclose($yeni_gzipdosya);

		$gzipdosya02 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
		$gzipac02 = gzread( $gzipdosya02, 9921920 );
		gzclose($gzipdosya02);

		$ac = $gzipac02;
	}
	else echo '<h2>hata_iletisi= Sunucunuz s�k��t�r�lm�� dosya y�klemesini desteklemiyor!</h2>';


//	DOSYA .SQL UZANTILI DE��LSE	//

elseif ($uzanti != 'sql'):
	echo '<h2>hata_iletisi= Sadece .sql ve .gz uzant�l� dosyalar y�klenebilir.</h2>';
	exit();


//	TEMP'TEK� DOSYANIN ���NDEK�LER DE���KENE AKTARILIYOR	//

else:
$dosya = @fopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
$boyut = filesize($_FILES['vtyukle']['tmp_name']);
$ac = @fread( $dosya, $boyut );
endif;


$ayarlar['f_dizin'] = '/';
include '../gerecler.php';


//  veritaban� sunucu adresi
$vt_sunucu = $_POST['vt_sunucu'];
//  veritaban� ismi
$vt_adi = zkTemizle3($_POST['vt_adi']);
//  veritaban� kullan�c� ad�
$vt_kullanici = zkTemizle3($_POST['vt_kullanici']);
//  veritaban� �ifresi
$vt_sifre = zkTemizle3($_POST['vt_sifre']);



//	VER�TABANI BA�LANTISI KURULUYOR	//

$link = mysql_connect($vt_sunucu,$vt_kullanici,$vt_sifre);

$veri_tabani = mysql_select_db($vt_adi,$link);

if ( (!$link) OR (!$veri_tabani) )
{
	$hata = mysql_error();

	if ( (preg_match("|Can\'t connect to MySQL server|si", $hata)) OR
			(preg_match("|Unknown MySQL server|si", $hata)) )
		echo $hata_tablo1.'Veritaban� sunucusu ile ba�lant� kurulam�yor !'.$hata_tablo2.'Girdi�iniz veritaban� adresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Access denied for user|si", $hata))
		echo $hata_tablo1.'Veritaban� sunucusu ile ba�lant� kurulam�yor !'.$hata_tablo2.'Girdi�iniz veritaban� kullan�c� ad� ve �ifresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Unknown database|si", $hata))
		echo $hata_tablo1.'Veritaban� a��lam�yor !'.$hata_tablo2.'Veritaban� ad�n� do�ru yazd���n�zdan emin olun.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	else echo $hata_tablo1.'Veritaban� ile ba�lant� kurulam�yor !'.$hata_tablo2.'Veritaban� sunucu adresi, kullan�c� ad� ve �ifre bilgilerinizi tekrar girin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	die();
}




// dosyadaki veriler sat�r sat�r dizi de�i�kene aktar�l�yor //
$toplam = explode(";\n\n", $ac);

// sat�r say�s� al�n�yor //
$toplam_sayi = count($toplam);

// dizideki sat�rlar d�ng�ye sokuluyor //
for ($satir=0;$satir<$toplam_sayi;$satir++)
{
	// 9 karakterden k�sa dizi elemanlar� diziden at�l�yor	//
	if (strlen($toplam[$satir]) > 9)
	{
		// yorumlar diziden at�l�yor //
		if (preg_match("/\n\n--/", $toplam[$satir]))
		{
			$yorum = explode("\n\n", $toplam[$satir]);
			$yorum_sayi = count($yorum);

			for ($satir2=0;$satir2<$yorum_sayi;$satir2++)
			{
				if ( (strlen($yorum[$satir2]) > 9) AND (!preg_match("/--/", $yorum[$satir2])) )
				// sorgu veritaban�na giriliyor //
				$strSQL = mysql_query($yorum[$satir2]) or die ($hata_tablo1.'Sorgu Ba�ar�s�z'.$hata_tablo2.mysql_error().$hata_tablo3);
			}
		}

		else // sorgu veritaban�na giziliyor //
		$strSQL = mysql_query($toplam[$satir]) or die ($hata_tablo1.'Sorgu Ba�ar�s�z'.$hata_tablo2.mysql_error().$hata_tablo3);
	}
}


//	VER�TABANI YEDE�� Y�KLEND� MESAJI	//

mysql_close($link);
@setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
@setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
echo $hata_tablo1.'- Y�kleme Ba�ar�l� -'.$hata_tablo2.'<br /><center><b>Veritaban� yede�iniz ba�ar�yla geri y�klenmi�tir.</b></center>'.$hata_tablo3;
exit();

		//	VER�TABANI YEDE�� Y�KLEME KISMI - SONU	//







else:

//  SAYFA BA�I   //

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1254" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9" />
<meta http-equiv="Content-Language" content="tr" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link href="../temalar/5renkli/sablons.css" rel="stylesheet" type="text/css" />
<title>'.$dil_kurulum[73].'</title>
</head>
<body bgcolor="#ffffff">
<div align="center" class="sayfa_kenarlik1">
<div align="center" class="sayfa_kenarlik2">


<table cellspacing="0" cellpadding="0" width="770" border="0" align="center" bgcolor="#d0d0d0">
<tbody>
	<tr>
	<td class="liste-veri" bgcolor="#ffffff" colspan="5" height="50" valign="middle">

<form name="form-dil" action="yukleme.php" method="get">
<select class="formlar" name="dil">
<option value="turkce" selected="selected">&nbsp;T�rk�e (Turkish)&nbsp; </option>
<option value="english" ';

if ((isset($_COOKIE['forum_dili'])) AND ($_COOKIE['forum_dili'] != '') AND ($_COOKIE['forum_dili'] == 'english') ) echo 'selected="selected"';

echo '>&nbsp;English (�ngilizce)&nbsp; </option>
</select>&nbsp; <input class="formlar" type="submit" value="'.$dil_kurulum[52].'" />
</form>


<script type="text/javascript">
//<![CDATA[
<!-- 
function denetle()
{ 
	var dogruMu = true;
	for (var i=0; i<6; i++)
	{
		if ( (i==3) || (i==4) )
		{
			continue;
		}
		else if (document.vtyukleme.elements[i].value=="")
		{
			dogruMu = false; 
			alert("'.$dil_kurulum[54].'");
			break;
		}
	}
	return dogruMu;
}
//  -->
//]]>
</script>
	<td>
	<tr>

	<tr class="liste-veri">
	<td width="85" height="27" align="center" valign="middle" bgcolor="#f8f8f8" style="border: 1px solid #e8e8e8;" onmouseover="this.bgColor= \'#eeeeee\'" onmouseout="this.bgColor= \'#f8f8f8\'">
<a href="index.php"><b>'.$dil_kurulum[69].'</b></a>
	</td>

	<td width="95" align="center" valign="middle" bgcolor="#f8f8f8" style="border: 1px solid #e8e8e8;" onmouseover="this.bgColor= \'#eeeeee\'" onmouseout="this.bgColor= \'#f8f8f8\'">
<a href="guncelle.php"><b>'.$dil_kurulum[70].'</b></a>
	</td>

	<td width="85" align="center" valign="middle" bgcolor="#f8f8f8" style="border-top: 1px solid #d0d0d0; border-left: 1px solid #d0d0d0; border-right: 1px solid #d0d0d0;">
<b>'.$dil_kurulum[71].'</b>
	</td>

	<td width="80" align="center" valign="middle" bgcolor="#f8f8f8" style="border: 1px solid #e8e8e8;" onmouseover="this.bgColor= \'#eeeeee\'" onmouseout="this.bgColor= \'#f8f8f8\'">
<a href="sil.php"><b>'.$dil_kurulum[72].'</b></a>
	</td>

	<td bgcolor="#ffffff">&nbsp;</td>
	</tr>
</tbody>
</table>



<table cellspacing="1" cellpadding="0" width="770" border="0" align="center" bgcolor="#d0d0d0">
	<tr>
	<td align="center">

<form name="vtyukleme" action="yukleme.php" method="post" enctype="multipart/form-data" onsubmit="return denetle()">
<input type="hidden" name="vt_yukleme" value="vt_yukleme" />


<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tr>
	<td align="center" valign="top" height="17"></td>
	</tr>
	
	<tr>
	<td align="center" valign="top">

<table cellspacing="1" cellpadding="0" width="96%" border="0"  class="tablo_border3">
	<tr>
	<td>

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" bgcolor="#ffffff">
	<tr>
	<td class="baslik" colspan="2" align="center" height="45">
'.$dil_kurulum[73].'
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left">
<br />
&nbsp; '.$dil_kurulum[79].'<br /><br /><br />';


if (!ini_get('file_uploads')) echo '&nbsp; <b>'.$dil_kurulum[74].'</b><p>';
echo '&nbsp; <b>'.$dil_kurulum[75].': </b>'.ini_get('upload_max_filesize').
'<br />&nbsp; <b>'.$dil_kurulum[76].':</b> '.ini_get('post_max_size').
'<br />&nbsp; <b>'.$dil_kurulum[77].':</b> '.ini_get('max_input_time').$dil_kurulum[83].'
<br />&nbsp; <b>'.$dil_kurulum[78].':</b> '.ini_get('max_execution_time').$dil_kurulum[83];


echo '<br /><br /><br />
<font size="1">
<i>&nbsp;&nbsp; &nbsp; '.$dil_kurulum[56].'</i>
</font>
	</td>
	</tr>

	<tr>
	<td>

<table cellspacing="1" width="96%" cellpadding="5" border="0" align="center" bgcolor="#d0d0d0">

	<tr>
	<td colspan="2" class="forum_baslik" align="center" style="height: 14px;">
'.$dil_kurulum[57].'
	</td>
	</tr>


	<tr class="liste-etiket" bgcolor="#ffffff">
	<td align="left">
<br />'.$dil_kurulum[58].'<br /><br />
	</td>

	<td align="left">
<input class="formlar" type="text" name="vt_sunucu" size="40" maxlength="100" value="localhost" />
	</td>
	</tr>


	<tr class="liste-etiket" bgcolor="#ffffff">
	<td align="left">
<br />'.$dil_kurulum[59].'<br />
<font size="1" style="font-weight: normal">
'.$dil_kurulum[80].'
</font><br /><br />
	</td>

	<td align="left">
<input class="formlar" type="text" name="vt_adi" size="40" maxlength="100" />
	</td>
	</tr>


	<tr class="liste-etiket" bgcolor="#ffffff">
	<td align="left">
<br />'.$dil_kurulum[61].'<br />
<font size="1" style="font-weight: normal">
'.$dil_kurulum[62].'
</font><br /><br />
	</td>

	<td align="left">
<input class="formlar" type="text" name="vt_kullanici" size="40" maxlength="100" />
	</td>
	</tr>


	<tr class="liste-etiket" bgcolor="#ffffff">
	<td align="left">
<br />'.$dil_kurulum[63].'<br />
<font size="1" style="font-weight: normal">
'.$dil_kurulum[64].'
</font><br /><br />
	</td>

	<td align="left">
<input class="formlar" type="password" name="vt_sifre" size="40" maxlength="100" />
	</td>
	</tr>


	<tr class="liste-etiket" bgcolor="#ffffff">
	<td align="left">
<br />'.$dil_kurulum[81].'<br />
<font size="1" style="font-weight: normal">
'.$dil_kurulum[82].'
</font><br /><br />
	</td>

	<td align="left">
<input name="vtyukle" type="file" size="30" />
	</td>
	</tr>
</table>

<script type="text/javascript">
//<![CDATA[
<!-- //
document.vtyukleme.vt_kullanici.setAttribute("autocomplete","off");
document.vtyukleme.vt_sifre.setAttribute("autocomplete","off");
//  -->
//]]>
</script>

	</td>
	</tr>


	<tr>
	<td class="liste-etiket" bgcolor="#ffffff" align="center" valign="middle" height="50">
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
<input class="dugme" type="submit" value="Yede�i Y�kle" />
	</td>
	</tr>


</table>
</td></tr></table>
</td></tr>
	<tr>
	<td height="17" ></td>
	</tr>
</table>
</form>
</td></tr></table>

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
	<tbody>
	<tr>
	<td height="25"></td>
	</tr>

	<tr>
	<td align="center" valign="bottom" class="liste-veri" bgcolor="#ffffff">
<div style="background:#ffffff; font-family: Tahoma, helvetica; font-size:11px; color:#000000; position:relative; z-index:1001; text-align:center; float:left; width:100%; height:35px;">
<br /><b>Forum Yaz�l�m�:</b> &nbsp; <a href="http://www.phpkf.com" target="_blank" style="text-decoration:none; color:#000000">php Kolay Forum (phpKF)</a>
&nbsp;&copy;&nbsp; 2007 - 2012 &nbsp; <a href="http://www.phpkf.com/phpkf_ekibi.php" target="_blank" style="text-decoration:none;color:#000000">phpKF Ekibi</a></div>
</td></tr></tbody></table>
<br /><br />
</div>
</div>
</body>
</html>';

endif;

?>