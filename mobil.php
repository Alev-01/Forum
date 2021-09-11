<?php
/*
 +-=====================================================================-+
 |                     php Kolay Forum (phpKF) v1.90                     |
 +-----------------------------------------------------------------------+
 |             Telif - Copyright (c) 2007 - 2012 Adem YILMAZ             |
 |               http://www.phpkf.com   -   phpkf@phpkf.com              |
 |               Tüm haklarý saklýdýr - All Rights Reserved              |
 |                     Mobil özelliði - Yücel KAHRAMAN                   |
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

@ini_set('magic_quotes_runtime', 0);

if (!@is_file('ayar.php'))
{
	// ayar.php yok, kurulum yapýlmamýþ, kurulum sayfasýna yönlendir.
	header('Location: kurulum/index.php');
	exit();
}


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
$sayfano = 41;
$sayfa_adi = 'Mobil Sürüm';
$tarih = time();
$cikis = '';




//		VERÝLER TEMÝZLENÝYOR		//


if (isset($_GET['ak']))
{
	$_GET['ak'] = @zkTemizle($_GET['ak']);
	$_GET['ak'] = @str_replace(array('-','x','.'), '', $_GET['ak']);
	if (!is_numeric($_GET['ak'])) $_GET['ak'] = 0;
	if ($_GET['ak'] < 0) $_GET['ak'] = 0;
}
else $_GET['ak'] = 0;


if (isset($_GET['aks']))
{
	$_GET['aks'] = @zkTemizle($_GET['aks']);
	$_GET['aks'] = @str_replace(array('-','x','.'), '', $_GET['aks']);
	if (!is_numeric($_GET['aks'])) $_GET['aks'] = 0;
	if ($_GET['aks'] < 0) $_GET['aks'] = 0;
}
else $_GET['aks'] = 0;


if (isset($_GET['af']))
{
	$_GET['af'] = @zkTemizle($_GET['af']);
	$_GET['af'] = @str_replace(array('-','x','.'), '', $_GET['af']);
	if (!is_numeric($_GET['af'])) $_GET['af'] = 0;
	if ($_GET['af'] < 0) $_GET['af'] = 0;
}
else $_GET['af'] = 0;


if (isset($_GET['afs']))
{
	$_GET['afs'] = @zkTemizle($_GET['afs']);
	$_GET['afs'] = @str_replace(array('-','x','.'), '', $_GET['afs']);
	if (!is_numeric($_GET['afs'])) $_GET['afs'] = 0;
	if ($_GET['afs'] < 0) $_GET['afs'] = 0;
}
else $_GET['afs'] = 0;



// oturum kodu
$o = $kullanici_kim['kullanici_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];



//  HTMLKodu FONKSÝYONU - BAÞI  //

function HTMLKodu($kisim, $baslik_ek = 'Mobil Sürüm')
{
global $ayarlar,$kullanici_kim,$forum_index,$o;

switch ($kisim)
{
	case 1:
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9">
<meta http-equiv="Content-Language" content="tr">
<meta content="minimum-scale=1.0, width=device-width, user-scalable=yes" name="viewport">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Style-Type" content="text/css">
<link href="temalar/5renkli/sablons.css" rel="stylesheet" type="text/css">
<style type="text/css">
A:link {color: #0f30F0; text-decoration: none}
A:active {color: #ff0000; text-decoration: none}
A:visited {color: #0063ce; text-decoration: none}
A:hover {color: #000000; text-decoration: underline}
BODY{
background: #ffffff;
margin-top: 0px;
margin-bottom: 0px;
font-family: helvetica;
font-size: 14px;}
</style>
<link rel="shortcut icon" href="temalar/5renkli/resimler/favicon.ico">
<title>'.$baslik_ek.' - '.$ayarlar['title'].'</title>
<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2013 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  Tüm haklarý saklýdýr - All Rights Reserved

function hepsiniSec(kodCizelgesi){if(document.selection){var secim=document.body.createTextRange();secim.moveToElementText(document.getElementById(kodCizelgesi));secim.select();}else if(window.getSelection){var secim=document.createRange();secim.selectNode(document.getElementById(kodCizelgesi));window.getSelection().addRange(secim);}else if(document.createRange && (document.getSelection || window.getSelection)){secim=document.createRange();secim.selectNodeContents(document.getElementById(kodCizelgesi));a=window.getSelection ? window.getSelection() : document.getSelection();a.removeAllRanges();a.addRange(secim);}}function ResimBuyut(resim,ratgele,en,boy,islem){var katman=document.getElementById(ratgele);if(islem=="buyut"){resim.width=en;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"kucult")};katman.style.width=(en-12)+"px";katman.innerHTML="Küçültmek için resmin üzerine týklayýn. Yeni pencerede açmak için buraya týklayýn."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-out";}else if(islem=="kucult"){resim.width=600;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"buyut")};katman.style.width="588px";katman.innerHTML="Büyütmek için resmin üzerine týklayýn. Yeni pencerede açmak için buraya týklayýn."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}else if(islem=="ac")window.open(resim,"_blank","scrollbars=yes,left=1,top=1,width="+(en+40)+",height="+(boy+30)+",resizable=yes");}function ResimBoyutlandir(resim){if(resim.width>"600"){var en=resim.width;var boy=resim.height;var adres=resim.src;var rastgele="resim_boyut_"+Math.random();oyazi=document.createTextNode("Büyütmek için resmin üzerine týklayýn. Yeni pencerede açmak için buraya týklayýn."+" ("+resim.width+"x"+resim.height+")");okatman=document.createElement("div");okatman.id=rastgele;okatman.className="resim_boyutlandir";okatman.align="left";okatman.title="Gerçek boyutunda görmek için resmin üzerine týklayýn!";okatman.style.cursor="pointer";okatman.onclick=function(){ResimBuyut(adres,rastgele,en,boy,"ac")};okatman.textNode=oyazi;okatman.appendChild(oyazi);resim.onclick=function(){ResimBuyut(resim,rastgele,en,boy,"buyut")};resim.width="600";resim.border="1";resim.title="Gerçek boyutunda görmek için resmin üzerine týklayýn!";resim.parentNode.insertBefore(okatman, resim);if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}}
function denetle(){var dogruMu = true;
if ((document.giris.kullanici_adi.value.length < 4) || (document.giris.sifre.value.length < 5)){ 
dogruMu = false; alert("Lütfen kullanýcý adý ve þifrenizi giriniz !");}
else; return dogruMu;}
function denetle2(){var dogruMu=true;if(document.form1.mesaj_icerik.value.length < 3){dogruMu=false;alert("YAZDIÐINIZ MESAJ 3 KARAKTERDEN UZUN OLMALIDIR !");}else;return dogruMu;}
//  -->
</script>
</head>
<body>
<br>
<table cellspacing="1" cellpadding="8" width="99%" border="0" align="center" bgcolor="#dddddd">
<tr><td align="left" bgcolor="#f9f9f9"><a href="mobil.php"><b>'.$ayarlar['title'].' Mobil Sürüm</b></a><font style="font-size:11px"><br><br>Tam sürüme geçmek için <a href="'.$forum_index.'">týklayýn.</a></font></td></tr>
</table>

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#ffffff">
<tr><td bgcolor="#ffffff" height="16"></td></tr>
</table>';


if (empty($kullanici_kim['id']))
{
	echo '<form name="giris" action="giris.php" method="post" onsubmit="return denetle()">
		<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">
	<input type="hidden" name="git" value="mobil.php">

	<table cellspacing="1" cellpadding="5" width="99%" border="0" align="center" bgcolor="#dddddd">
	<tr><td align="left" bgcolor="#f9f9f9"><b>Kullanýcý Adý:</b></td>
	<td align="left" bgcolor="#f9f9f9"><b>Þifre:</b></td></tr>

	<tr><td align="left" bgcolor="#f9f9f9" width="140">
	<input type="text" name="kullanici_adi" size="15" maxlength="20"></td>

	<td align="left" bgcolor="#f9f9f9">
	<input type="password" name="sifre" size="15" maxlength="20"></td></tr>

	<tr><td align="left" bgcolor="#f9f9f9">
	<label style="cursor: pointer;"><input type="checkbox" name="hatirla" style="position: relative; top: 2px;">Beni Hatýrla</label></td>
	<td align="left" bgcolor="#f9f9f9">
	<input type="submit" value="Giriþ">
	&nbsp; <a href="kayit.php">Üye Ol</a></td></tr>
	</table></form>';
}

else
{
	echo '<table cellspacing="1" cellpadding="4" width="99%" border="0" align="center" bgcolor="#dddddd">
	<tr><td align="left" bgcolor="#f9f9f9">&nbsp;Hoþ Geldiniz <b>'.$kullanici_kim['kullanici_adi'].'</b> <a href="cikis.php?o='.$o.'">[Çýkýþ]</a></td></tr>
	</table>';
}

echo '<table cellspacing="0" cellpadding="0" width="99%" border="0" align="center" bgcolor="#ffffff">

<tr><td align="left" bgcolor="#ffffff" height="5px">&nbsp;</td></tr>
</table>

<table cellspacing="1" cellpadding="0" width="99%" border="0" align="center" bgcolor="#dddddd">
<tr><td align="left">

<table cellspacing="1" cellpadding="7" width="100%" border="0" align="center" bgcolor="#dddddd">';
break;


case 2:
echo '</table></td></tr></table>
<table cellspacing="1" cellpadding="3" width="99%" border="0" align="center" bgcolor="#ffffff">
<tr><td align="left" bgcolor="#ffffff" height="12"></td></tr>
</table>

<table cellspacing="1" cellpadding="6" width="99%" border="0" align="center" bgcolor="#dddddd">
<tr><td align="center" bgcolor="#f9f9f9"><font color="#000000">'.base64_decode(SATIR1).'</td></tr>
</table><br></body></html>';
break;
}
}

//  HTMLKodu FONKSÝYONU - SONU  //





			//	KONU GÖSTERÝMÝ - BAÞI	//

if ($_GET['ak'] > 0)
{
$strSQL = "SELECT
id,tarih,hangi_forumdan,yazan,mesaj_baslik,mesaj_icerik,tarih,cevap_sayi,kilitli,bbcode_kullan,ifade
FROM $tablo_mesajlar WHERE id='$_GET[ak]' AND silinmis='0' LIMIT 1";
$sonuc = mysql_query($strSQL);
$mesaj_satir = mysql_fetch_assoc($sonuc);


$sayfa_adi = 'Mobil Sürüm Konu: '.$mesaj_satir['mesaj_baslik'];
if (!defined('DOSYA_BASLIK_KOD')) include 'baslik_kod.php';


// KONU YOKSA HATA MESAJI, VARSA DEVAM //

if (empty($mesaj_satir))
{
	echo HTMLKodu(1, 'Mobil Sürüm');
	echo '<tr>
	<td align="left" bgcolor="#eeeeee">Seçtiðiniz konu veritabanýnda bulunmamaktadýr ! </td>
	</tr>';
	echo HTMLKodu(2, '');
	exit();
}


// forum bilgileri çekiliyor
$strSQL = "SELECT id,forum_baslik,okuma_izni,konu_acma_izni,alt_forum FROM $tablo_forumlar WHERE id='$mesaj_satir[hangi_forumdan]' LIMIT 1";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$forum_satir2 = mysql_fetch_assoc($sonuc2);


if ($forum_satir2['alt_forum'] != '0')
{
	$alt_forum_baslik = '<a href="mobil.php?af='.$mesaj_satir['hangi_forumdan'].'"><b>'.$forum_satir2['forum_baslik'].'</b></a>';

	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$forum_satir2[alt_forum]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_satir3 = mysql_fetch_assoc($sonuc2);

	$ust_forum_baslik = '<a href="mobil.php?af='.$forum_satir3['id'].'"><b>'.$forum_satir3['forum_baslik'].'</b></a> &nbsp;&raquo;&nbsp; ';
}

else
{
	$ust_forum_baslik = '<a href="mobil.php?af='.$mesaj_satir['hangi_forumdan'].'"><b>'.$forum_satir2['forum_baslik'].'</b></a>';
	$alt_forum_baslik = '';
}



//	KULLANICIYA GÖRE FORUM GÖSTERÝMÝ - BAÞI	//


//	FORUM HERKESE KAPALIYSA	//

if ($forum_satir2['okuma_izni'] == 5)
{
	// sadece yöneticiyse girebilir
	if ( (!isset($kullanici_kim['yetki']) ) OR ($kullanici_kim['yetki'] != 1) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu forum kapatýlmýþtýr !</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	FORUM MÝSAFÝRLERE KAPALIYSA		//

if ($forum_satir2['okuma_izni'] > 0)
{
	// üye deðilse - ziyaretçiyse
	if (empty($kullanici_kim['id']))
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece üyeler girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE YÖNETÝCÝLER ÝÇÝNSE	//

if ($forum_satir2['okuma_izni'] == 1)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece yöneticiler girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE YÖNETÝCÝLER VE YARDIMCILAR ÝÇÝNSE	//

elseif ($forum_satir2['okuma_izni'] == 2)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
		AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece yöneticiler ve yardýmcýlar girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE ÖZEL ÜYELER ÝÇÝNSE 	//

elseif ($forum_satir2['okuma_izni'] == 3)
{
	//	YÖNETÝCÝ DEÐÝLSE YARDIMCILIÐINA BAK	//

	if (isset($kullanici_kim['yetki']))
	{
		if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2));

		elseif ($kullanici_kim['yetki'] == 3)
		{
			if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_GET[af]' AND okuma='1' OR";
			else $grupek = "grup='0' AND";

			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_GET[af]' AND okuma='1'";
			$kul_izin = mysql_query($strSQL);

			if ( !mysql_num_rows($kul_izin) )
			{
				echo HTMLKodu(1, 'Mobil Sürüm');
				echo '<tr>
				<td align="left" bgcolor="#eeeeee">Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler girebilir !</td>
				</tr>';
				echo HTMLKodu(2, '');
				exit();
			}
		}

		else
		{
			echo HTMLKodu(1, 'Mobil Sürüm');
			echo '<tr>
				<td align="left" bgcolor="#eeeeee">Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler girebilir !</td>
				</tr>';
				echo HTMLKodu(2, '');
			exit();
		}
	}
}

//	KULLANICIYA GÖRE FORUM GÖSTERÝMÝ - SONU	//






// OLUÞTURULACAK SAYFA SAYISI BAÐLANTISI //

$satir_sayi = $mesaj_satir['cevap_sayi'];

$sinir = 8;

$toplam_sayfa = ($satir_sayi / $sinir);
settype($toplam_sayfa,'integer');

if ( ($satir_sayi % $sinir) != 0 )
$toplam_sayfa++;



//	SAYFA BAÐLANTILARI OLUÞTURULUYOR BAÞI	//

$sayfalama_cikis ='';

if ($satir_sayi > $sinir):

$sayfalama_cikis .= '<table cellspacing="1" cellpadding="3" width="5%" border="0" align="right" bgcolor="#dddddd">
	<tr>';

if ($_GET['aks'] != 0)
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" title="ilk sayfa">
	<a href="mobil.php?ak='.$_GET['ak'].'"><b>&nbsp;&laquo;&nbsp;</b></a></td>

	<td bgcolor="#ffffff" title="önceki sayfa">
	<a href="mobil.php?ak='.$_GET['ak'].'&amp;aks='.($_GET['aks'] - $sinir).'"><b>&nbsp;&lt;&nbsp;</b></a></td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['aks']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['aks'] / $sinir) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['aks'] + 8))  break;
		if (($sayi == 0) AND ($_GET['aks'] == 0))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="Þu anki sayfa">
			&nbsp;<b>[1]</b>&nbsp;</td>';
		}

		elseif (($sayi + 1) == (($_GET['aks'] / $sinir) + 1))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="Þu anki sayfa">
			&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}

		else
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="'.($sayi + 1).'. sayfaya git">
			<a href="mobil.php?ak='.$_GET['ak'].'&amp;aks='.($sayi * $sinir).'"><b>&nbsp;'.($sayi + 1).'&nbsp;</b></a></td>';
		}
	}
}

if ($_GET['aks'] < ($satir_sayi - $sinir))
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfa">
	<a href="mobil.php?ak='.$_GET['ak'].'&amp;aks='.($_GET['aks'] + $sinir).'"><b>&nbsp;&gt;&nbsp;</b></a></td>

	<td bgcolor="#ffffff" class="liste-veri" title="son sayfa">
	<a href="mobil.php?ak='.$_GET['ak'].'&amp;aks='.(($toplam_sayfa - 1) * $sinir).'"><b>&nbsp;&raquo;&nbsp;</b></a></td>';
}

$sayfalama_cikis .= '</tr></table>';

endif;

// SAYFA BAÐLANTILARI OLUÞTURULUYOR - SONU //




if ($mesaj_satir['ifade'] == 1)
	$mesaj_satir['mesaj_icerik'] = ifadeler($mesaj_satir['mesaj_icerik']);

if ( ($mesaj_satir['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
	$konu_icerik = bbcode_acik($mesaj_satir['mesaj_icerik'],$mesaj_satir['id']);

else $konu_icerik = bbcode_kapali($mesaj_satir['mesaj_icerik']);

$cikis .= '<tr>
<td align="left" bgcolor="#eeeeee" height="40px" colspan="2"><a href="mobil.php"><b>'.$ayarlar['syfbaslik'].'</b></a> &nbsp;&raquo;&nbsp; '.$ust_forum_baslik.' '.$alt_forum_baslik.' &nbsp;&raquo;&nbsp; <a href="konu.php?k='.$mesaj_satir['id'].'"><b>'.$mesaj_satir['mesaj_baslik'].'</b></a></td>
</tr>

<tr>
<td align="left" colspan="2" bgcolor="#ffffff">
'.$sayfalama_cikis.'
</td>
</tr>';


//  konu sadece ilk sayfada gösteriliyor  //

if ($_GET['aks'] < 1 )
{
	$cikis .= '<tr><td align="left" bgcolor="#eeeeee" width="50%">
	<a href="profil.php?kim='.$mesaj_satir['yazan'].'"><b>'.$mesaj_satir['yazan'].'</b></a></td>
	<td align="right" bgcolor="#eeeeee" width="50%"><b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $mesaj_satir['tarih']).'</b></td></tr>
	<tr><td align="left" bgcolor="#f9f9f9" colspan="2"><br>'.$konu_icerik.'<br><br></td></tr>';
}



// CEVAP BÝLGÝLERÝ ÇEKÝLÝYOR

$strSQL = "SELECT
id,cevap_yazan,cevap_baslik,cevap_icerik,tarih,bbcode_kullan,ifade
FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$_GET[ak]' ORDER BY tarih LIMIT $_GET[aks],$sinir";
$cevap = mysql_query($strSQL);


if ( (!isset($_GET['aks'])) OR ($_GET['aks'] <= 0) ) $say = 1;
else $say = $_GET['aks']+1;


while ($cevap_satir = mysql_fetch_assoc($cevap))
{
	if ($cevap_satir['ifade'] == 1)
		$cevap_satir['cevap_icerik'] = ifadeler($cevap_satir['cevap_icerik']);

	if ( ($cevap_satir['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
		$cevap_icerik = bbcode_acik($cevap_satir['cevap_icerik'],$cevap_satir['id']);

	else $cevap_icerik = bbcode_kapali($cevap_satir['cevap_icerik']);

	$cikis .= '<tr>
		<td align="left" bgcolor="#eeeeee" width="50%">
		<a href="profil.php?kim='.$cevap_satir['cevap_yazan'].'"><b>'.$cevap_satir['cevap_yazan'].'</b></a>&nbsp;|&nbsp;<i>'.$cevap_satir['cevap_baslik'].'</i></td>
		<td align="right" bgcolor="#eeeeee" width="50%"><b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevap_satir['tarih']).'</b> &nbsp; Cevap: '.$say.'</td>
		</tr>
		<tr>
		<td align="left" bgcolor="#f9f9f9" colspan="2"><br>'.$cevap_icerik.'<br><br></td>
		</tr>';
	$say++;
}


// sayfalama linkleri alt taraf

$cikis .= '<tr>
<td align="left" colspan="2" bgcolor="#ffffff">
'.$sayfalama_cikis.'
</td>
</tr>';



//		HIZLI CEVAP		//

if (isset($kullanici_kim['id']))
{
	if ($mesaj_satir['kilitli'] == 1) $form_ksayfa = 0;
	else
	{
		if ($satir_sayi < $sinir) $form_ksayfa = 0;
		elseif ( ($satir_sayi % $sinir) == 0 ) $form_ksayfa = $satir_sayi; 
		else $form_ksayfa = $satir_sayi - ($satir_sayi % $sinir);
	}

	$cikis .= '<tr><td colspan="2" align="center" bgcolor="#eeeeee"><b>Hýzlý Cevap</b></td></tr>
	<tr>
	<td colspan="2" align="center" bgcolor="#f9f9f9">
	<a name="hcevap"></a>

	<form action="mesaj_yaz_yap.php" method="post" onsubmit="return denetle2()" name="form1">
	<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">
	<input type="hidden" name="kip" value="cevapla">
	<input type="hidden" name="mobil" value="mobil">
	<input type="hidden" name="mesaj_baslik" value="Cvp:">
	<input type="hidden" name="fno" value="'.$mesaj_satir['hangi_forumdan'].'">
	<input type="hidden" name="mesaj_no" value="'.$mesaj_satir['id'].'">
	<input type="hidden" name="fsayfa" value="0">
	<input type="hidden" name="sayfa" value="'.$form_ksayfa.'">

	<textarea cols="30" rows="7" name="mesaj_icerik" style="width: 99%;"></textarea>
	<br><br>
	<input name="mesaj_gonder" type="submit" value="G ö n d e r">
	</form>
	</td>
	</tr>';
}


	echo HTMLKodu(1, $mesaj_satir['mesaj_baslik']);
	echo $cikis;
	echo HTMLKodu(2, '');
	exit();
}


			//	KONU GÖSTERÝMÝ - SONU	//






			//	FORUM GÖSTERÝMÝ - BAÞI	//


$sinir = 50;


if ($_GET['af'] > 0)
{

if ($_GET['afs'] == 0) $afs = '';
else $afs = '&amp;afs='.$_GET['afs'];

$strSQL = "SELECT id,mesaj_baslik FROM $tablo_mesajlar WHERE silinmis='0' AND hangi_forumdan='$_GET[af]' ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[afs],$sinir";
$baslik_sirala = mysql_query($strSQL);

$strSQL = "SELECT id,forum_baslik,okuma_izni,konu_acma_izni,alt_forum,konu_sayisi FROM $tablo_forumlar WHERE id='$_GET[af]' LIMIT 1";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$forum_satir = mysql_fetch_assoc($sonuc2);


$sayfa_adi = 'Mobil Sürüm Bölüm: '.$forum_satir['forum_baslik'];
if (!defined('DOSYA_BASLIK_KOD')) include 'baslik_kod.php';


// FORUM YOKSA HATA MESAJI, VARSA DEVAM //

if (empty($forum_satir))
{
	echo HTMLKodu(1, 'Mobil Sürüm');
	echo '<tr>
	<td align="left" bgcolor="#eeeeee">Seçtiðiniz forum veritabanýnda bulunmamaktadýr ! </td>
	</tr>';
	echo HTMLKodu(2, '');
	exit();
}


// FORUM YOKSA HATA MESAJI, VARSA DEVAM //

if ($forum_satir['konu_sayisi'] == '0')
{
	echo HTMLKodu(1, 'Mobil Sürüm');
	echo '<tr>
	<td align="left" bgcolor="#eeeeee">Bu forumda henüz hiçbir yazý bulunmamaktadýr !</td>
	</tr>';
	echo HTMLKodu(2, '');
	exit();
}



if ($forum_satir['alt_forum'] != '0')
{
	$alt_forum_baslik = '<a href="mobil.php?af='.$forum_satir['id'].'">'.$forum_satir['forum_baslik'].'</a>';

	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$forum_satir[alt_forum]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_satir2 = mysql_fetch_assoc($sonuc2);

	$ust_forum_baslik = '<a href="mobil.php?af='.$forum_satir2['id'].'">'.$forum_satir2['forum_baslik'].'</a> &nbsp;&raquo;&nbsp; ';
}

else
{
	$ust_forum_baslik = '<a href="mobil.php?af='.$forum_satir['id'].'">'.$forum_satir['forum_baslik'].'</a>';
	$alt_forum_baslik = '';
}




//	KULLANICIYA GÖRE FORUM GÖSTERÝMÝ - BAÞI	//


//	FORUM HERKESE KAPALIYSA	//

if ($forum_satir['okuma_izni'] == 5)
{
	// sadece yöneticiyse girebilir
	if ( (!isset($kullanici_kim['yetki']) ) OR ($kullanici_kim['yetki'] != 1) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu forum kapatýlmýþtýr !</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	FORUM MÝSAFÝRLERE KAPALIYSA		//

if ($forum_satir['okuma_izni'] > 0)
{
	// üye deðilse - ziyaretçiyse
	if (empty($kullanici_kim['id']))
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece üyeler girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE YÖNETÝCÝLER ÝÇÝNSE	//

if ($forum_satir['okuma_izni'] == 1)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece yöneticiler girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE YÖNETÝCÝLER VE YARDIMCILAR ÝÇÝNSE	//

elseif ($forum_satir['okuma_izni'] == 2)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
		AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
	{
		echo HTMLKodu(1, 'Mobil Sürüm');
		echo '<tr>
		<td align="left" bgcolor="#eeeeee">Bu foruma sadece yöneticiler ve yardýmcýlar girebilir!</td>
		</tr>';
		echo HTMLKodu(2, '');
		exit();
	}
}


//	SADECE ÖZEL ÜYELER ÝÇÝNSE 	//

elseif ($forum_satir['okuma_izni'] == 3)
{
	//	YÖNETÝCÝ DEÐÝLSE YARDIMCILIÐINA BAK	//

	if (isset($kullanici_kim['yetki']))
	{
		if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2));

		elseif ($kullanici_kim['yetki'] == 3)
		{
			if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_GET[af]' AND okuma='1' OR";
			else $grupek = "grup='0' AND";

			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_GET[af]' AND okuma='1'";
			$kul_izin = mysql_query($strSQL);

			if ( !mysql_num_rows($kul_izin) )
			{
				echo HTMLKodu(1, 'Mobil Sürüm');
				echo '<tr>
				<td align="left" bgcolor="#eeeeee">Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler girebilir !</td>
				</tr>';
				echo HTMLKodu(2, '');
				exit();
			}
		}

		else
		{
			echo HTMLKodu(1, 'Mobil Sürüm');
			echo '<tr>
				<td align="left" bgcolor="#eeeeee">Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler girebilir !</td>
				</tr>';
				echo HTMLKodu(2, '');
			exit();
		}
	}
}

//	KULLANICIYA GÖRE FORUM GÖSTERÝMÝ - SONU	//




// OLUÞTURULACAK SAYFA SAYISI BAÐLANTISI //

$satir_sayi = $forum_satir['konu_sayisi'];

$toplam_sayfa = ($satir_sayi / $sinir);
settype($toplam_sayfa,'integer');

if ( ($satir_sayi % $sinir) != 0 )
$toplam_sayfa++;



//	SAYFA BAÐLANTILARI OLUÞTURULUYOR BAÞI	//

$sayfalama_cikis ='';

$cikis .= '<tr>
	<td align="left" bgcolor="#eeeeee" height="40px" colspan="2">
	<b><a href="mobil.php">'.$ayarlar['syfbaslik'].'</a> &nbsp;&raquo;&nbsp; '.$ust_forum_baslik.' '.$alt_forum_baslik.'</b></td>
	</tr>
	<tr><td align="left" colspan="2" bgcolor="#ffffff">';


if ($satir_sayi > $sinir):

$sayfalama_cikis .= '<table cellspacing="1" cellpadding="3" width="5%" border="0" align="right" bgcolor="#dddddd">
	<tr>';

if ($_GET['afs'] != 0)
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" title="ilk sayfa">
	<a href="mobil.php?af='.$_GET['af'].'"><b>&nbsp;&laquo;&nbsp;</b></a></td>

	<td bgcolor="#ffffff" title="önceki sayfa">
	<a href="mobil.php?af='.$_GET['af'].'&amp;afs='.($_GET['afs'] - $sinir).'"><b>&nbsp;&lt;&nbsp;</b></a></td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['afs']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['afs'] / $sinir) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['afs'] + 8))  break;
		if (($sayi == 0) AND ($_GET['afs'] == 0))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="Þu anki sayfa">
			&nbsp;<b>[1]</b>&nbsp;</td>';
		}

		elseif (($sayi + 1) == (($_GET['afs'] / $sinir) + 1))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="Þu anki sayfa">
			&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}

		else
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" title="'.($sayi + 1).'. sayfaya git">
			<a href="mobil.php?af='.$_GET['af'].'&amp;afs='.($sayi * $sinir).'"><b>&nbsp;'.($sayi + 1).'&nbsp;</b></a></td>';
		}
	}
}

if ($_GET['afs'] < ($satir_sayi - $sinir))
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfa">
	<a href="mobil.php?af='.$_GET['af'].'&amp;afs='.($_GET['afs'] + $sinir).'"><b>&nbsp;&gt;&nbsp;</b></a></td>

	<td bgcolor="#ffffff" class="liste-veri" title="son sayfa">
	<a href="mobil.php?af='.$_GET['af'].'&amp;afs='.(($toplam_sayfa - 1) * $sinir).'"><b>&nbsp;&raquo;&nbsp;</b></a></td>';
}

$cikis .= $sayfalama_cikis .= '</tr></table>';

endif;

$cikis .= '</td></tr>';

// SAYFA BAÐLANTILARI OLUÞTURULUYOR - SONU //



if ( (!isset($_GET['afs'])) OR ($_GET['afs'] <= 0) ) $say = 1;
else $say = $_GET['afs']+1;


while ($satir = mysql_fetch_assoc($baslik_sirala))
{
$cikis .= '
	<tr>
	<td align="left" bgcolor="#ffffff" width="25">'.$say.'.</td>
	<td align="left" bgcolor="#ffffff">
	<a href="mobil.php?ak='.$satir['id'].'"><span style="float:left; width:100%">'.$satir['mesaj_baslik'].'</span></a></td>
	</tr>';
	$say++;
}


$cikis .= '<tr><td align="left" colspan="2" bgcolor="#ffffff">'.$sayfalama_cikis.'</td></tr>';


echo HTMLKodu(1, $forum_satir['forum_baslik']);
echo $cikis;
echo HTMLKodu(2, '');
exit();
}

			//	FORUM GÖSTERÝMÝ - SONU	//




			//	ANA SAYFA GÖSTERÝMÝ - BAÞI	//


// Dallar Sýralanýyor

$strSQL = "SELECT * FROM $tablo_dallar ORDER BY sira";
$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_assoc($sonuc3))
{
	$cikis .= '<tr>
	<td align="left" bgcolor="#eeeeee"><b>'.$dallar_satir['ana_forum_baslik'].'</b></td>
	</tr>';


$strSQL = "SELECT id,forum_baslik,okuma_izni,gizle FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";

$sonuc4 = mysql_query($strSQL);


//	ÜST FORUMLAR SIRALANIYOR    //

while ($forum_satir = mysql_fetch_assoc($sonuc4))
{
	// alt forumlarýn bilgileri çekiliyor
	$strSQL = "SELECT id,forum_baslik,okuma_izni,gizle FROM $tablo_forumlar WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
	$sonuc5 = mysql_query($strSQL);

	$alt_forum_sorgu = '';
	$fkonu_sayisi = 0;
	$fmesaj_sayisi = 0;


	// Yetkiye göre üst forum (ve konu) baþlýðý gizleme

if (($forum_satir['gizle'] == 1) AND ($forum_satir['okuma_izni'] != 0))
{
	if (isset($kullanici_kim['id']))
	{
		if (($forum_satir['okuma_izni'] == 5) AND ($kullanici_kim['yetki'] != 1)) continue;

		elseif (($forum_satir['okuma_izni'] == 1) AND ($kullanici_kim['yetki'] != 1)) continue;

		elseif (($forum_satir['okuma_izni'] == 2) AND ($kullanici_kim['yetki'] == 0)) continue;

		elseif (($forum_satir['okuma_izni'] == 3) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2))
		{
			if ($kullanici_kim['yetki'] >= 0)
			{
				if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$forum_satir[id]' AND okuma='1' OR";
				else $grupek = "grup='0' AND";

				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE kulad='$kullanici_kim[kullanici_adi]' AND fno='$forum_satir[id]' AND okuma='1'";
				$kul_izin = mysql_query($strSQL);
				if (!mysql_num_rows($kul_izin)) continue;
			}
			else continue;
		}
	}

	else continue;
}



$cikis .= '<tr><td align="left" bgcolor="#ffffff"><a href="mobil.php?af='.$forum_satir['id'].'"><span style="float:left; width:100%">'.$forum_satir['forum_baslik'].'</span></a></td></tr>';


// alt forum varsa
if (mysql_num_rows($sonuc5))
{
	//	ALT FORUMLAR SIRALANIYOR    //
	while ($alt_forum_satir = mysql_fetch_assoc($sonuc5))
	{
		// Yetkiye göre alt forum (ve konu) baþlýðý gizleme
		if (($alt_forum_satir['gizle'] == 1) AND ($alt_forum_satir['okuma_izni'] != 0))
		{
			if (isset($kullanici_kim['id']))
			{
				if (($alt_forum_satir['okuma_izni'] == 5) AND ($kullanici_kim['yetki'] != 1)) continue;

				elseif (($alt_forum_satir['okuma_izni'] == 1) AND ($kullanici_kim['yetki'] != 1)) continue;

				elseif (($alt_forum_satir['okuma_izni'] == 2) AND ($kullanici_kim['yetki'] == 0)) continue;

				elseif (($alt_forum_satir['okuma_izni'] == 3) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2))
				{
					if ($kullanici_kim['yetki'] >= 0)
					{
						if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$alt_forum_satir[id]' AND okuma='1' OR";
						else $grupek = "grup='0' AND";

						$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE kulad='$kullanici_kim[kullanici_adi]' AND fno='$alt_forum_satir[id]' AND okuma='1'";
						$kul_izin = mysql_query($strSQL);
						if (!mysql_num_rows($kul_izin)) continue;
					}
					else continue;
				}
			}
			else continue;
		}

		$cikis .= '<tr><td align="left" bgcolor="#ffffff"><a href="mobil.php?af='.$alt_forum_satir['id'].'"><span style="float:left; width:100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$alt_forum_satir['forum_baslik'].'</span></a></td></tr>';

		$alt_forum_sorgu .= "OR silinmis='0' AND hangi_forumdan='$alt_forum_satir[id]' ";
	}
}
}
}


if (!defined('DOSYA_BASLIK_KOD')) include 'baslik_kod.php';

echo HTMLKodu(1, 'Mobil Sürüm');
echo $cikis;
echo HTMLKodu(2, '');
exit();

			//	ANA SAYFA GÖSTERÝMÝ - SONU	//

?>