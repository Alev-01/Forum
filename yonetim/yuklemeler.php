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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
$dosya_yolu = 'dosyalar/yuklemeler/';


// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];



if ((isset($_GET['ara'])) AND ($_GET['ara'] != ''))
{
	$_GET['ara'] = @zkTemizle($_GET['ara']);

	// �zel iletilerde aran�yor
	$strSQL = "SELECT id FROM $tablo_ozel_ileti WHERE ozel_icerik LIKE '%$_GET[ara]%' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$ozel = mysql_fetch_array($sonuc);

	if (isset($ozel['id'])) echo '<b>Var</b>';
	else echo 'Yok';
	exit();
}



//	DOSYA S�LME ��LEMLER� - BA�I	//

elseif ((isset($_GET['sil'])) AND ($_GET['sil'] != ''))
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	// Veri rakam de�ilse hata ver
	if (!is_numeric($_GET['sil']))
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	// site kurucusu de�ilse hata ver
	if ($kullanici_kim['id'] != 1)
	{
		header('Location: ../hata.php?hata=151');
		exit();
	}

	// dosyan�n bilgileri �ekiliyor
	$strSQL = "SELECT id,dosya FROM $tablo_yuklemeler WHERE id='$_GET[sil]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$dosya = mysql_fetch_array($sonuc);

	// dosya yoksa hata ver
	if (!isset($dosya['id']))
	{
		header('Location: ../hata.php?hata=206');
		exit();
	}

	// dosya sunucudan siliniyor
	@unlink('../'.$dosya_yolu.$dosya['dosya']);

	// dosya girdisi veritaban�ndan siliniyor
	$strSQL = "DELETE FROM $tablo_yuklemeler WHERE id='$_GET[sil]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: ../hata.php?bilgi=49');
	exit();
}

//	DOSYA S�LME ��LEMLER� - SONU	//



$sayfa_adi = 'Y�netim Dosya Y�klemeleri';
include 'yonetim_baslik.php';


echo '<script type="text/javascript">
<!-- //
function GonderAl(adres,katman){
var katman1 = document.getElementById(katman);
var veri_yolla = \'name=value\';
if (document.all) var istek = new ActiveXObject("Microsoft.XMLHTTP");
else var istek = new XMLHttpRequest();
istek.open("GET", adres, true);

istek.onreadystatechange = function(){
if (istek.readyState == 4){
    if (istek.status == 200) katman1.innerHTML = istek.responseText;
    else katman1.innerHTML = \'<font color="#ff0000"><b>Ba�lant� Kurulamad� !</b></font>\';}};
istek.send(veri_yolla);}

function ara(katman,veri){
adres = \'yuklemeler.php?ara=\'+veri;
var katman1 = document.getElementById(katman);
katman1.innerHTML = \'<img src="../dosyalar/yukleniyor.gif" width="15" alt="Y�." title="Y�kleniyor...">\';
setTimeout("GonderAl(\'"+adres+"\',\'"+katman+"\')",1000);}
//  -->
</script>';

?>



<table cellspacing="1" cellpadding="0" width="830" border="0" align="center" class="tablo_border">
	<tbody>
	<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tbody>
	<tr>
	<td height="17"></td>
	</tr>

	<tr>
	<td align="center" valign="top">

<table cellspacing="1" cellpadding="0" width="96%" border="0" class="tablo_border3">
	<tbody>
	<tr>
	<td align="center" valign="top" class="tablo_ici">

<table cellspacing="0" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tbody>

	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Dosya Y�klemeleri -
	</td>
	</tr>

	<tr>
	<td height="20"></td>
	</tr>

	<tr>
	<td align="center">
<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td align="center" valign="top" width="148">


<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html';


$sira = 1;
$tboyut = 0;

$strSQL = "SELECT * FROM $tablo_yuklemeler ORDER BY ";

if ((isset($_GET['uye'])) AND ($_GET['uye'] == '1')) $strSQL .= "uye_adi ASC";
elseif ((isset($_GET['uye'])) AND ($_GET['uye'] == '0')) $strSQL .= "uye_adi DESC";
elseif ((isset($_GET['tarih'])) AND ($_GET['tarih'] == '1')) $strSQL .= "tarih DESC";
elseif ((isset($_GET['tarih'])) AND ($_GET['tarih'] == '0')) $strSQL .= "tarih ASC";
elseif ((isset($_GET['ip'])) AND ($_GET['ip'] == '1')) $strSQL .= "ip ASC";
elseif ((isset($_GET['ip'])) AND ($_GET['ip'] == '0')) $strSQL .= "ip DESC";
elseif ((isset($_GET['boyut'])) AND ($_GET['boyut'] == '1')) $strSQL .= "boyut ASC";
elseif ((isset($_GET['boyut'])) AND ($_GET['boyut'] == '0')) $strSQL .= "boyut DESC";
else $strSQL .= "id ASC";

$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



echo '
	</td>
	<td align="center" valign="top">

<table cellspacing="1" width="100%" cellpadding="5" border="0" align="center" class="tablo_border4">
	<tr class="forum_baslik">
	<td align="center" valign="top" colspan="2" height="25">';

if ((isset($_GET['uye']) AND ($_GET['uye'] == '1')))
	echo '<a href="yuklemeler.php?uye=0" style="color:#ffffff;text-decoration:none">�ye Ad�&nbsp;&#94;</a>';
elseif ((isset($_GET['uye'])) AND ($_GET['uye'] == '0'))
	echo '<a href="yuklemeler.php?uye=1" style="color:#ffffff;text-decoration:none">�ye Ad�&nbsp;<font style="position:relative;font-family:arial black;font-size:10px">V</font></a>';
else echo '<a href="yuklemeler.php?uye=1" style="color:#ffffff;text-decoration:none">�ye Ad�</a>';


echo '</td>
	<td align="center" width="120">';


if ((!isset($_GET['uye'])) AND (!isset($_GET['tarih'])) AND (!isset($_GET['ip'])) AND (!isset($_GET['boyut'])))
	echo '<a href="yuklemeler.php?tarih=1" style="color:#ffffff;text-decoration:none">Tarih&nbsp;&#94;</a>';
elseif ((isset($_GET['tarih'])) AND ($_GET['tarih'] == '1'))
	echo '<a href="yuklemeler.php?tarih=0" style="color:#ffffff;text-decoration:none">Tarih&nbsp;<font style="position:relative;font-family:arial black;font-size:10px">V</font></a>';
elseif ((isset($_GET['tarih']) AND ($_GET['tarih'] == '0')))
	echo '<a href="yuklemeler.php?tarih=1" style="color:#ffffff;text-decoration:none">Tarih&nbsp;&#94;</a>';
else echo '<a href="yuklemeler.php?tarih=0" style="color:#ffffff;text-decoration:none">Tarih</a>';


echo '</td>
	<td align="center" width="105">';


if ((isset($_GET['ip']) AND ($_GET['ip'] == '1')))
	echo '<a href="yuklemeler.php?ip=0" style="color:#ffffff;text-decoration:none">IP Adresi&nbsp;&#94;</a>';
elseif ((isset($_GET['ip'])) AND ($_GET['ip'] == '0'))
	echo '<a href="yuklemeler.php?ip=1" style="color:#ffffff;text-decoration:none">IP Adresi&nbsp;<font style="position:relative;font-family:arial black;font-size:10px">V</font></a>';
else echo '<a href="yuklemeler.php?ip=1" style="color:#ffffff;text-decoration:none">IP Adresi</a>';


echo '</td>
	<td align="center" width="60">';


if ((isset($_GET['boyut']) AND ($_GET['boyut'] == '1')))
	echo '<a href="yuklemeler.php?boyut=0" style="color:#ffffff;text-decoration:none">Boyut&nbsp;&#94;</a>';
elseif ((isset($_GET['boyut'])) AND ($_GET['boyut'] == '0'))
	echo '<a href="yuklemeler.php?boyut=1" style="color:#ffffff;text-decoration:none">Boyut&nbsp;<font style="position:relative;font-family:arial black;font-size:10px">V</font></a>';
else echo '<a href="yuklemeler.php?boyut=1" style="color:#ffffff;text-decoration:none">Boyut</a>';


echo '</td>
	<td align="center" width="25">Sil</td>
	<td align="center" width="25">Ara</td>
	<td align="center" width="35">�.Ara</td>
	<td align="center" width="25">A�</td>
	</tr>
';




while ($yukleme = mysql_fetch_array($sonuc2)):

echo '
	<tr class="liste-veri" bgcolor="#ffffff" onMouseOver="this.bgColor= \'#e0e0e0\'" onMouseOut="this.bgColor= \'#ffffff\'">
	<td width="20" align="left">
<b>'.$sira.')</b>
	</td>

	<td align="left" height="25">
	<a href="../profil.php?u='.$yukleme['uye_id'].'">'.$yukleme['uye_adi'].'</a>
	</td>

	<td align="center">
'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $yukleme['tarih']).'
	</td>

	<td align="center">
	<a href="ip_yonetimi.php?kip=1&amp;ip='.$yukleme['ip'].'">'.$yukleme['ip'].'</a>
	</td>

	<td align="right">'.NumaraBicim($yukleme['boyut']).' <b>kb.</b></td>

	<td align="center">
	<a href="yuklemeler.php?sil='.$yukleme['id'].'&amp;o='.$o.'" onclick="return window.confirm(\'Dosyay� ve girdiyi silmek istedi�inize emin misiniz ?\')">Sil</a>
	</td>

	<td align="center">
	<a href="../arama.php?a=1&amp;b=1&amp;forum=tum&amp;tarih=tum_zamanlar&amp;sozcuk_hepsi='.$yukleme['dosya'].'">Ara</a>
	</td>

	<td align="center">
	<div id="oara-'.$yukleme['id'].'">
	<a href="javascript:void(0);" onclick="ara(\'oara-'.$yukleme['id'].'\', \''.$yukleme['dosya'].'\')">�.Ara</a>
	</div>
	</td>

	<td align="center">
	<a href="../'.$dosya_yolu.$yukleme['dosya'].'" target="_blank">A�</a>
	</td>
	</tr>
';

$sira++;
$tboyut += $yukleme['boyut'];

endwhile;


echo '
	<tr class="tablo_ici">
	<td class="liste-veri" colspan="9">&nbsp;</td>
	</tr>

	<tr class="tablo_ici">
	<td align="center" colspan="9" class="liste-etiket">
	<b>Toplam dosya boyutu:&nbsp; '.NumaraBicim($tboyut).' kb.</b>
	</td>
	</tr>';

?>

</table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
<tr>
<td align="center" height="15"></td>
</tr>
</table>
</td></tr></table>
<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>