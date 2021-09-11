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


// Seçilen dil çereze giriliyor

if ((isset($_GET['dil'])) AND ($_GET['dil'] != ''))
{
	// forum dizini alýnýyor
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


// dil dosyasý yükleniyor

if ((isset($_COOKIE['forum_dili'])) AND ($_COOKIE['forum_dili'] != ''))
{
	if ($_COOKIE['forum_dili'] == 'english') include 'dil_english.php';
	else include 'dil_turkce.php';
}
else include 'dil_turkce.php';



@ini_set('magic_quotes_runtime', 0);

		//	VERÝTABANI YEDEÐÝ YÜKLEME KISMI - BAÞI	//

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
	echo $hata_tablo1.'Hatalý Bilgi'.$hata_tablo2.'Veritabaný kullanýcý adý ve þifresi hariç tüm alanlarýn doldurulmasý zorunludur!'.$hata_tablo3;
	exit();
}


//	DOSYA YÜKLEMEDE HATA OLURSA - DOSYA 2`MB. DAN BÜYÜKSE	//

if ( (isset($_FILES['vtyukle']['error'])) AND ($_FILES['vtyukle']['error'] != 0) )
{
	echo '<h2>hata_iletisi= Dosya Yüklenemedi, Dosya adý alýnamadý !<p>Bunun nedeni dosyanýn 2mb.`dan büyük olmasý ya da<br />dosya adýnýn kabul edilmeyen karakterler içermesi olabilir. <p>Yedeði tablo tablo ayrý dosyalara bölmeyi deneyin veya dosya adýný deðiþtirmeyi deneyin.</h2>'.$_FILES['vtyukle']['tmp_name'].' - '.$_FILES['vtyukle']['error'];
	exit();
}


//	DOSYA 2`MB. DAN BÜYÜKSE	//
if ( (isset($_FILES['vtyukle']['tmp_name'])) AND ($_FILES['vtyukle']['tmp_name'] != '') )
{
	if ($_FILES['vtyukle']['size'] > 5242880)
	{
		echo '<h2>hata_iletisi= 5mb.`dan büyük yedek yükleyemezsiniz. <br />Yedeði tablo tablo ayrý dosyalara bölmeyi deneyin.</h2>';
		exit();
	}
}


$uzanti = end(explode(".", strtolower($_FILES['vtyukle']['name'])));


//	DOSYA SIKIÞTIRILMIÞ MI BAKILIYOR	//

if ($uzanti == 'gz'):

	if(extension_loaded('zlib'))
	{
		$gzipdosya01 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
		$gzipac01 = gzread( $gzipdosya01, 9921920 );
		gzclose($gzipdosya01);

		//	çift sýkýþtýrýlýmýþ olma olasýlýðýna karþý tekrar açýlýyor
		$yeni_gzipdosya = fopen($_FILES['vtyukle']['tmp_name'], 'w') or die ("Dosya açýlamýyor!");
		fwrite($yeni_gzipdosya, $gzipac01);
		fclose($yeni_gzipdosya);

		$gzipdosya02 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
		$gzipac02 = gzread( $gzipdosya02, 9921920 );
		gzclose($gzipdosya02);

		$ac = $gzipac02;
	}
	else echo '<h2>hata_iletisi= Sunucunuz sýkýþtýrýlmýþ dosya yüklemesini desteklemiyor!</h2>';


//	DOSYA .SQL UZANTILI DEÐÝLSE	//

elseif ($uzanti != 'sql'):
	echo '<h2>hata_iletisi= Sadece .sql ve .gz uzantýlý dosyalar yüklenebilir.</h2>';
	exit();


//	TEMP'TEKÝ DOSYANIN ÝÇÝNDEKÝLER DEÐÝÞKENE AKTARILIYOR	//

else:
$dosya = @fopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
$boyut = filesize($_FILES['vtyukle']['tmp_name']);
$ac = @fread( $dosya, $boyut );
endif;


$ayarlar['f_dizin'] = '/';
include '../gerecler.php';


//  veritabaný sunucu adresi
$vt_sunucu = $_POST['vt_sunucu'];
//  veritabaný ismi
$vt_adi = zkTemizle3($_POST['vt_adi']);
//  veritabaný kullanýcý adý
$vt_kullanici = zkTemizle3($_POST['vt_kullanici']);
//  veritabaný þifresi
$vt_sifre = zkTemizle3($_POST['vt_sifre']);



//	VERÝTABANI BAÐLANTISI KURULUYOR	//

$link = mysql_connect($vt_sunucu,$vt_kullanici,$vt_sifre);

$veri_tabani = mysql_select_db($vt_adi,$link);

if ( (!$link) OR (!$veri_tabani) )
{
	$hata = mysql_error();

	if ( (preg_match("|Can\'t connect to MySQL server|si", $hata)) OR
			(preg_match("|Unknown MySQL server|si", $hata)) )
		echo $hata_tablo1.'Veritabaný sunucusu ile baðlantý kurulamýyor !'.$hata_tablo2.'Girdiðiniz veritabaný adresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayrýntýsý: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Access denied for user|si", $hata))
		echo $hata_tablo1.'Veritabaný sunucusu ile baðlantý kurulamýyor !'.$hata_tablo2.'Girdiðiniz veritabaný kullanýcý adý ve þifresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayrýntýsý: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Unknown database|si", $hata))
		echo $hata_tablo1.'Veritabaný açýlamýyor !'.$hata_tablo2.'Veritabaný adýný doðru yazdýðýnýzdan emin olun.<br><br>
<b>Hata ayrýntýsý: </b>'.$hata.$hata_tablo3;

	else echo $hata_tablo1.'Veritabaný ile baðlantý kurulamýyor !'.$hata_tablo2.'Veritabaný sunucu adresi, kullanýcý adý ve þifre bilgilerinizi tekrar girin.<br><br>
<b>Hata ayrýntýsý: </b>'.$hata.$hata_tablo3;

	die();
}




// dosyadaki veriler satýr satýr dizi deðiþkene aktarýlýyor //
$toplam = explode(";\n\n", $ac);

// satýr sayýsý alýnýyor //
$toplam_sayi = count($toplam);

// dizideki satýrlar döngüye sokuluyor //
for ($satir=0;$satir<$toplam_sayi;$satir++)
{
	// 9 karakterden kýsa dizi elemanlarý diziden atýlýyor	//
	if (strlen($toplam[$satir]) > 9)
	{
		// yorumlar diziden atýlýyor //
		if (preg_match("/\n\n--/", $toplam[$satir]))
		{
			$yorum = explode("\n\n", $toplam[$satir]);
			$yorum_sayi = count($yorum);

			for ($satir2=0;$satir2<$yorum_sayi;$satir2++)
			{
				if ( (strlen($yorum[$satir2]) > 9) AND (!preg_match("/--/", $yorum[$satir2])) )
				// sorgu veritabanýna giriliyor //
				$strSQL = mysql_query($yorum[$satir2]) or die ($hata_tablo1.'Sorgu Baþarýsýz'.$hata_tablo2.mysql_error().$hata_tablo3);
			}
		}

		else // sorgu veritabanýna giziliyor //
		$strSQL = mysql_query($toplam[$satir]) or die ($hata_tablo1.'Sorgu Baþarýsýz'.$hata_tablo2.mysql_error().$hata_tablo3);
	}
}


//	VERÝTABANI YEDEÐÝ YÜKLENDÝ MESAJI	//

mysql_close($link);
@setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
@setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);
echo $hata_tablo1.'- Yükleme Baþarýlý -'.$hata_tablo2.'<br /><center><b>Veritabaný yedeðiniz baþarýyla geri yüklenmiþtir.</b></center>'.$hata_tablo3;
exit();

		//	VERÝTABANI YEDEÐÝ YÜKLEME KISMI - SONU	//







else:

//  SAYFA BAÞI   //

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
<option value="turkce" selected="selected">&nbsp;Türkçe (Turkish)&nbsp; </option>
<option value="english" ';

if ((isset($_COOKIE['forum_dili'])) AND ($_COOKIE['forum_dili'] != '') AND ($_COOKIE['forum_dili'] == 'english') ) echo 'selected="selected"';

echo '>&nbsp;English (Ýngilizce)&nbsp; </option>
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
<input class="dugme" type="submit" value="Yedeði Yükle" />
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
<br /><b>Forum Yazýlýmý:</b> &nbsp; <a href="http://www.phpkf.com" target="_blank" style="text-decoration:none; color:#000000">php Kolay Forum (phpKF)</a>
&nbsp;&copy;&nbsp; 2007 - 2012 &nbsp; <a href="http://www.phpkf.com/phpkf_ekibi.php" target="_blank" style="text-decoration:none;color:#000000">phpKF Ekibi</a></div>
</td></tr></tbody></table>
<br /><br />
</div>
</div>
</body>
</html>';

endif;

?>