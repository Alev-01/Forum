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



//  FORM DOLU ÝSE  //

if ((isset($_POST['form'])) AND ($_POST['form'] == 'dolu')):



if ((!isset($_POST['grup_adi'])) OR ($_POST['grup_adi'] == ''))
{
	header('Location: ../hata.php?hata=26');
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_ ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['grup_adi']))
{
	header('Location: ../Hata.php?hata=201');
	exit();
}

if ( ( strlen($_POST['grup_adi']) > 30) OR ( strlen($_POST['grup_adi']) < 4) )
{
	header('Location: ../hata.php?hata=202');
	exit();
}


//  veriler temizleniyor

if (isset($_POST['grup_adi'])) $_POST['grup_adi'] = zkTemizle($_POST['grup_adi']);
if (isset($_POST['ozel_ad'])) $_POST['ozel_ad'] = zkTemizle($_POST['ozel_ad']);
if (isset($_POST['grup_bilgi'])) $_POST['grup_bilgi'] = zkTemizle($_POST['grup_bilgi']);
if (isset($_POST['duzenle'])) $_POST['duzenle'] = zkTemizle($_POST['duzenle']);
if (isset($_POST['yetki'])) $_POST['yetki'] = zkTemizle($_POST['yetki']);


// grup gizleme
if (isset($_POST['grup_gizle'])) $grup_gizle = 1;
else $grup_gizle = 0;



//   YENÝ GRUP OLUÞTURMA   //

if ((isset($_POST['yeni_grup'])) AND ($_POST['yeni_grup'] == 'yeni_grup'))
{
	// grup adýnýn daha önce kullanýlýp kullanýlmadýðýna bakýlýyor
	$strSQL = "SELECT grup_adi FROM $tablo_gruplar WHERE grup_adi='$_POST[grup_adi]' LIMIT 1";
	$sonuc = mysql_query($strSQL);


	if (mysql_num_rows($sonuc))
	{
		header('Location: ../hata.php?hata=203');
		exit();
	}


	// yeni grup kaydý yapýlýyor
	$strSQL = "INSERT INTO $tablo_gruplar (grup_adi, sira, gizle, yetki, ozel_ad, uyeler, grup_bilgi)";
	$strSQL .= "VALUES ('$_POST[grup_adi]','$_POST[sira]', '$grup_gizle', '$_POST[yetki]', '$_POST[ozel_ad]', '', '$_POST[grup_bilgi]')";
	$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
}



//   GRUP DÜZENLEME   //

elseif ((isset($_POST['duzenle'])) AND ($_POST['duzenle'] != ''))
{
	// grubun eski yetkisi "bölüm yardýmcýlýðý" ise ve deðiþtirilmiþse uyarý ver
	if ( ($_POST['eski_yetki'] == '3') AND ($_POST['yetki'] != '3') )
	{
		header('Location: ../hata.php?hata=205');
		exit();
	}

	// grubun eski ve yeni yetkileri "yetkisiz" deðilse, yeni yetkiyi grup üyelerine uygula
	elseif ($_POST['yetki'] != '-1')
	{
		$strSQL = "UPDATE $tablo_kullanicilar SET yetki='$_POST[yetki]' WHERE grupid='$_POST[duzenle]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}

	// gruba özel ad eklenmiþse veya silinmiþse grup üyelerine uygulanýyor
	if ( ($_POST['ozel_ad'] != '') OR (($_POST['eski_ozel_ad'] != '') AND ($_POST['ozel_ad'] == '')) )
	{
		$strSQL = "UPDATE $tablo_kullanicilar SET ozel_ad='$_POST[ozel_ad]' WHERE grupid='$_POST[duzenle]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}

	// sýra deðiþtirilmiþse diðer gruba uygulanýyor
	if ($_POST['eski_sira'] != $_POST['sira'])
	{
		$strSQL = "UPDATE $tablo_gruplar SET sira='$_POST[eski_sira]' WHERE sira='$_POST[sira]' LIMIT 1";
		$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}

	// grup bilgileri düzenleniyor
	$strSQL = "UPDATE $tablo_gruplar SET grup_adi='$_POST[grup_adi]', sira='$_POST[sira]', gizle='$grup_gizle', yetki='$_POST[yetki]', ozel_ad='$_POST[ozel_ad]', grup_bilgi='$_POST[grup_bilgi]' WHERE id='$_POST[duzenle]' LIMIT 1";
	$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
}


header('Location: gruplar.php');
exit();





//   GRUP SÝLME ÝÞLEMLERÝ   //

elseif ((isset($_GET['sil'])) AND ($_GET['sil'] != '')):


// oturum kodu iþlemleri

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


// oturum bilgilerine bakýlýyor
if ($_GET['o'] != $o)
{
	header('Location: ../hata.php?hata=45');
	exit();
}


$_GET['sil'] = zkTemizle($_GET['sil']);


// grup siliniyor
$strSQL = "DELETE FROM $tablo_gruplar WHERE id='$_GET[sil]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


// grubun özel izinleri siliniyor
$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE grup='$_GET[sil]'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


// grup üyelikleri iptal ediliyor
$strSQL = "UPDATE $tablo_kullanicilar SET grupid='0' WHERE grupid='$_GET[sil]'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

header('Location: gruplar.php');
exit();




endif;



// Düzenleme týklanmýþsa

if ((isset($_GET['duzenle'])) AND ($_GET['duzenle'] != ''))
{
	if (isset($_GET['duzenle'])) $_GET['duzenle'] = zkTemizle($_GET['duzenle']);

	$strSQL = "SELECT * FROM $tablo_gruplar WHERE id='$_GET[duzenle]' LIMIT 1";
	$sonuc_duzenle = mysql_query($strSQL);
	$satir_duzenle = mysql_fetch_assoc($sonuc_duzenle);

	// seçili grup yoksa
	if (!isset($satir_duzenle['id']))
	{
		header('Location: gruplar.php');
		exit();
	}
}




//  SAYFA NORMAL GÖSTERÝM  //

$sayfa_adi = 'Yönetim Üye Gruplar';
include 'yonetim_baslik.php';


// oturum kodu oluþturuluyor
$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


// Gruplarýn bilgileri çekiliyor
$strSQL = "SELECT * FROM $tablo_gruplar ORDER BY sira";
$sonuc_grup = mysql_query($strSQL);

?>

<script type="text/javascript">
<!-- //
function silme_onay(){
	var onay1 = confirm("Bu grubu silmek istediðinize emin misiniz?");
	if (onay1){
		var onay2 = confirm("Gerçekten silmek istediðinize emin misiniz?");
		if (onay2) return true;
		else return false;
	}
	else return false;
}
// -->
</script>


<table cellspacing="1" cellpadding="0" width="760" border="0" align="center" class="tablo_border">
	<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tr>
	<td height="17"></td>
	</tr>

	<tr>
	<td align="center" valign="top">

<table cellspacing="1" cellpadding="0" width="96%" border="0" class="tablo_border3">
	<tr>
	<td align="center" valign="top" class="tablo_ici">

<table cellspacing="0" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Üye Gruplarý -

	</td>
	</tr>

	<tr>
	<td height="20"></td>
	</tr>

	<tr>
	<td align="center">
<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td>



<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html' ?>


<table cellspacing="0" width="77%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td>

<table cellspacing="1" width="99%" cellpadding="5" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="3" align="center" valign="middle" height="23">
Gruplar
	</td>
	</tr>

	<tr class="liste-veri">
	<td align="left" valign="middle" height="50" colspan="3" class="tablo_ici">
<br>
&nbsp; &nbsp; Grup oluþturma, düzenleme ve görüntüleme iþlemlerini bu sayfadan yapabilirsiniz.
<br><br>
Bir gruba yetki verdiðinizde gruptaki tüm üyelerin yetkileri deðiþir. Grup yetkisi "Yok" olarak ayarlandýðýnda grup üyelerinin yetkilerinde herhangi bir deðiþiklik olmaz.

<br><br>
Bir gruba özel ad verdiðinizde gruptaki tüm üyelerin özel adlarý deðiþir, boþ býraktýðýnýzda  herhangi bir deðiþiklik olmaz.
<br>
<br>
	</td>
	</tr>

<?php

$tgrup = 0;

if (!mysql_num_rows($sonuc_grup))
{
	echo '
	<tr class="liste-veri">
	<td align="center" valign="middle" height="50" colspan="3" class="tablo_ici">
	<b>Henüz Hiçbir Grup Oluþturulmamýþ</b><br>
	</td>
	</tr>';
}


else
{
	echo '<tr class="liste-etiket">
	<td align="center" class="tablo_ici" width="43%">Grup Adý</td>
	<td align="center" class="tablo_ici" width="43%">Grup Üyeleri</td>
	<td align="center" class="tablo_ici" width="14%">Ýþlem</td></tr>';


	while ($satir_grup = mysql_fetch_assoc($sonuc_grup))
	{
		echo '
		<tr class="tablo_ici" >
		<td align="left" valign="top" height="30" class="liste-etiket">
		<b>'.$satir_grup['grup_adi'].'</b><font style="font-size: 11px; font-weight: normal">';
		if ($satir_grup['gizle'] == '1') echo '&nbsp; <i>(gizli)</i>';
		echo '<br><br>
		<a href="kul_izinler.php?grup='.$satir_grup['id'].'">Özel yetki ver</a>
		</font></td>
		<td align="left" class="liste-veri">';

		$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar WHERE grupid='$satir_grup[id]'";
		$sonuc_grup2 = mysql_query($strSQL);
		$sayi = 1;

		if (mysql_num_rows($sonuc_grup2))
		{
			while ($satir_grup2 = mysql_fetch_assoc($sonuc_grup2))
			{
				echo '<b>'.$sayi.')</b>&nbsp; <a href="kullanici_degistir.php?u='.$satir_grup2['id'].'" title="Üye profilini deðiþtir">'.$satir_grup2['kullanici_adi'].'</a><br>';
				$sayi++;
			}
		}

		else echo '<b>Yok</b><br><br><a href="kullanicilar.php">Üye Ekle</a>';


		echo '<td align="center" class="tablo_ici">
		<a href="gruplar.php?duzenle='.$satir_grup['id'].'#duzenle" title="Grubu Düzenle"><img '.$simge_degistir.' alt="düzenle"></a> &nbsp;
		<a href="gruplar.php?sil='.$satir_grup['id'].'&amp;o='.$o.'" title="Grubu Sil" onclick="return silme_onay()"><img '.$simge_sil.' alt="sil"></a>
		</td></tr>';

		$tgrup++;
	}
}

?>

</table>

</td>
</tr>

<tr>
<td height="35"><a name="duzenle"></a>&nbsp;</td>
</tr>

<tr>
<td>

<form action="gruplar.php" method="post" name="form1">
<input type="hidden" name="form" value="dolu">

<?php
echo '<input type="hidden" name="sira" value="'.($tgrup+1).'">';

if ((isset($_GET['duzenle'])) AND ($_GET['duzenle'] != ''))
echo '<input type="hidden" name="duzenle" value='.$satir_duzenle['id'].'">';

else echo '<input type="hidden" name="yeni_grup" value="yeni_grup">';
?>

<table cellspacing="1" width="99%" cellpadding="5" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
<?php
if ((isset($_GET['duzenle'])) AND ($_GET['duzenle'] != '')) echo 'Grup Düzenleme';
else echo 'Yeni Grup Oluþtur';
?>
	</td>
	</tr>


	<tr class="tablo_ici">
	<td colspan="2" class="liste-veri" align="left" valign="middle" height="55">
Yeni grup oluþturma ve düzenleme iþlemlerini bu bölümden yapabilirsiniz.<br><br>
<font size="1">
<i>Tüm alanlarýn doldurulmasý zorunludur!</i>
</font>
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" width="45%" height="40" class="tablo_ici">
Grup Adý:
	</td>

	<td align="left" width="55%" height="40"  class="tablo_ici">
<input type="text" class="formlar" name="grup_adi" size="37" maxlength="30" value="<?php
if (isset($satir_duzenle['grup_adi'])) echo $satir_duzenle['grup_adi'];
?>">
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" valign="top" height="40" class="tablo_ici">
Grup Açýklamasý:<br>
<font size="1" style="font-weight: normal">
Açýklama en fazla 250 karakter olabilir.<br>
(Sadece düz metin)
</font>
<br><br><br><br><br><br>
<div id="bilgi_uzunluk" style="font-weight: normal">Eklenebilir Karakter: </div>
	</td>

	<td align="left" class="tablo_ici">
<textarea name="grup_bilgi" rows="10" cols="30" class="formlar" style="width: 85%; height:130px" onkeyup="BilgiUzunluk()"><?php
if (isset($satir_duzenle['grup_bilgi'])) echo $satir_duzenle['grup_bilgi'];
?></textarea>


<script type="text/javascript">
<!-- //
function BilgiUzunluk()
{
	var div_katman = document.getElementById('bilgi_uzunluk');
	div_katman.innerHTML = 'Eklenebilir Karakter: ' + (250-document.form1.grup_bilgi.value.length);

	if (document.form1.grup_bilgi.value.length > 250)
	{
		alert('En fazla 250 karakter girebilirsiniz.');
		document.form1.grup_bilgi.value = document.form1.grup_bilgi.value.substr(0,250);
		div_katman.innerHTML = 'Eklenebilir Karakter: 0';
	}
	return true;
}
BilgiUzunluk();
//  -->
</script>

	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" width="45%" height="40" class="tablo_ici">
Grup Özel Adý:
	</td>

	<td align="left" width="55%" height="40"  class="tablo_ici">
<input type="text" class="formlar" name="ozel_ad" size="37" maxlength="30" value="<?php
if (isset($satir_duzenle['ozel_ad'])) echo $satir_duzenle['ozel_ad'];
?>">
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Grup Yetkisi:
	</td>

	<td align="left" class="tablo_ici">
<?php

if (isset($satir_duzenle['yetki']))
{
	echo '<input type="hidden" name="eski_yetki" value="'.$satir_duzenle['yetki'].'">
<input type="hidden" name="eski_ozel_ad" value="'.$satir_duzenle['ozel_ad'].'">
<select class="formlar" name="yetki">
	<option value="-1"';
	if ($satir_duzenle['yetki'] == '-1') echo ' selected="selected"';
	echo '>Yok</option>';

	echo '<option value="0"';
	if ($satir_duzenle['yetki'] == 0) echo ' selected="selected"';
	echo '>Kayýtlý Kullanýcý</option>';

	if ($satir_duzenle['yetki'] == 3) echo '<option value="3" selected="selected">Bölüm Yardýmcýsý</option>';

	echo '<option value="2"';
	if ($satir_duzenle['yetki'] == 2) echo ' selected="selected"';
	echo '>Forum Yardýmcýsý</option>';

	echo '<option value="1"';
	if ($satir_duzenle['yetki'] == 1) echo ' selected="selected"';
	echo '>Forum Yöneticisi</option></select> &nbsp;&nbsp;
	<font style="font-size: 11px; font-weight: normal"><a href="kul_izinler.php?grup='.$satir_duzenle['id'].'">Özel yetki ver</a></font>';
}

else
{
	echo '<select class="formlar" name="yetki">
	<option value="-1" selected="selected">Yok</option>
	<option value="0">Kayýtlý Kullanýcý</option>
	<option value="2">Forum Yardýmcýsý</option>
	<option value="1">Forum Yöneticisi</option>
	</select>';
}

?>
	</td>
	</tr>



<?php

if (isset($satir_duzenle['sira']))
{
	echo '<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Grup Sýrasý:
	</td>

	<td align="left" class="tablo_ici">
<input type="hidden" name="eski_sira" value="'.$satir_duzenle['sira'].'">
<select class="formlar" name="sira">';

	for($i=1; $i<=$tgrup; $i++)
	{
		echo '<option value="'.$i.'"';
		if ($satir_duzenle['sira'] == $i) echo ' selected="selected"';
		echo '>&nbsp;'.$i.'&nbsp;</option>';
	}


	echo '>Yok</option>
	</select>
	</td>
	</tr>';
}

?>



	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Grup Durumu:
	</td>

	<td align="left" class="tablo_ici">
<label style="cursor: pointer;">
<?php

if ((isset($satir_duzenle['gizle'])) AND ($satir_duzenle['gizle'] == 1)) echo '<input type="checkbox" name="grup_gizle" checked="checked">';
else echo '<input type="checkbox" name="grup_gizle">';

?>
<font style="font-size: 11px; font-weight: normal; position: relative; top:-2px;">Grubu gizle</font></label>
	</td>
	</tr>


	<tr class="tablo_ici">
	<td colspan="2" class="liste-veri" align="center" valign="middle" height="50">
<?php
if ((isset($_GET['duzenle'])) AND ($_GET['duzenle'] != '')) echo '<input class="dugme" type="submit" value="Grubu Düzenle">';
else echo '<input class="dugme" type="submit" value="Grup Oluþtur">';
?>
	</td>
	</tr>

</table>
</form>
</td></tr></table>
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