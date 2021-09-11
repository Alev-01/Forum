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
		header('Location: sil.php');
		exit();
	}
	else
	{
		@setcookie('forum_dili', '', 0, $forum_dizin);
		header('Location: sil.php');
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



//	VERÝTABANINDAKÝ FORUM TABLOLARI SÝLÝNÝYOR	//

if ( (isset($_POST['kurulum'])) AND ($_POST['kurulum'] = 'forum_sil') )
{
	//  HATA TABLOSU    //

	$hata_tablo1 = '<br><br><br><table border="0" cellspacing="1" cellpadding="7" width="530" bgcolor="#999999" align="center">
	<tr><td bgcolor="#eeeeee" align="center"><font color="#ff0000"><b>';

	$hata_tablo2 = '</b></font></td></tr>
	<tr><td bgcolor="#fafafa">
	<table border="0" cellspacing="1" cellpadding="7" width="100%" bgcolor="#999999" align="center"><tr><td bgcolor="#eeeeee" align="left"><br>';

	$hata_tablo3 = '<br><br></td></tr></table>';



	if ( (empty($_POST['vt_sunucu'])) OR (empty($_POST['vt_adi'])) OR (empty($_POST['tablo_onek'])) )
	{
		echo $hata_tablo1.'Hatalý Bilgi'.$hata_tablo2.'Veritabaný kullanýcý adý ve þifresi hariç tüm alanlarýn doldurulmasý zorunludur!'.$hata_tablo3;
		exit();
	}

	if (!preg_match('/^[a-zA-Z]\w{0,10}+$/', $_POST['tablo_onek']))
	{
		echo $hata_tablo1.'Hatalý Bilgi'.$hata_tablo2.'Veritabaný tablo öneki sadece harf ile baþlamalý ve 10 karakterden uzun olmamalýdýr.'.$hata_tablo3;
		exit();
	}



	//	VERÝTABANI BAÐLANTISI KURULUYOR	//

	$link = @mysql_connect($_POST['vt_sunucu'],$_POST['vt_kullanici'],$_POST['vt_sifre']);

	$veri_tabani = @mysql_select_db($_POST['vt_adi'],$link);

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



	$strSQL2 = "SHOW TABLE STATUS LIKE '$_POST[tablo_onek]%'";
	$sonuc2 = mysql_query($strSQL2);

	while ($silinecek = mysql_fetch_assoc($sonuc2))
	{
		$strSQL = "DROP TABLE $silinecek[Name]";
		$sonuc = mysql_query($strSQL);
	}


	echo $hata_tablo1.'- Silme Ýþlemi Baþarýlý -'.$hata_tablo2.'<br /><center><b>VERÝTABANINDAKÝ FORUM TABLOLARI SÝLÝNMÝÞTÝR.</b></center>'.$hata_tablo3;
	exit();
}


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
<title>'.$dil_kurulum[53].'</title>
</head>
<body bgcolor="#ffffff">
<div align="center" class="sayfa_kenarlik1">
<div align="center" class="sayfa_kenarlik2">


<table cellspacing="0" cellpadding="0" width="770" border="0" align="center" bgcolor="#d0d0d0">
<tbody>
	<tr>
	<td class="liste-veri" bgcolor="#ffffff" colspan="5" height="50" valign="middle">

<form name="form-dil" action="sil.php" method="get">
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
	for (var i=0; i<5; i++)
	{
		if ( (i==3) || (i==4) )
		{
			continue;
		}
		else if (document.silme_formu.elements[i].value=="")
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

	<td width="85" align="center" valign="middle" bgcolor="#f8f8f8" style="border: 1px solid #e8e8e8;" onmouseover="this.bgColor= \'#eeeeee\'" onmouseout="this.bgColor= \'#f8f8f8\'">
<a href="yukleme.php"><b>'.$dil_kurulum[71].'</b></a>
	</td>

	<td width="80" align="center" valign="middle" bgcolor="#f8f8f8" style="border-top: 1px solid #d0d0d0; border-left: 1px solid #d0d0d0; border-right: 1px solid #d0d0d0;">
<b>'.$dil_kurulum[72].'</b>
	</td>

	<td bgcolor="#ffffff">&nbsp;</td>
	</tr>
</tbody>
</table>



<table cellspacing="1" cellpadding="0" width="770" border="0" align="center" bgcolor="#d0d0d0">
	<tr>
	<td align="center">

<form action="sil.php" method="post" onsubmit="return denetle()" name="silme_formu">
<input type="hidden" name="kurulum" value="forumu_sil" />

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
'.$dil_kurulum[53].'
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left">
<br />
'.$dil_kurulum[55].'


<br /><br /><br />
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
'.$dil_kurulum[60].'
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
<br />'.$dil_kurulum[65].'<br />
<font size="1" style="font-weight: normal">
'.$dil_kurulum[66].'
</font><br /><br />
	</td>

	<td align="left">
<input class="formlar" type="text" name="tablo_onek" size="40" maxlength="20" value="phpkf_" />
	</td>
	</tr>
</table>

<script type="text/javascript">
//<![CDATA[
<!-- //
document.silme_formu.vt_kullanici.setAttribute("autocomplete","off");
document.silme_formu.vt_sifre.setAttribute("autocomplete","off");
//  -->
//]]>
</script>

	</td>
	</tr>


	<tr>
	<td class="liste-etiket" bgcolor="#ffffff" align="center" valign="middle" height="50">
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
<input class="dugme" type="submit" value="'.$dil_kurulum[67].'" />
	</td>
	</tr>


</table>
</td></tr></table>

	</td>
	</tr>

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

?>