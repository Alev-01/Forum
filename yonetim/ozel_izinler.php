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

if (empty($_GET['sayfa'])) $_GET['sayfa'] = 0;
else 	$_GET['sayfa'] = @zkTemizle($_GET['sayfa']);

if (empty($_GET['kul_ara'])) $_GET['kul_ara'] = '%';
else
{
	$_GET['kul_ara'] = @zkTemizle(trim($_GET['kul_ara']));
	$_GET['kul_ara'] = @str_replace('*','%',$_GET['kul_ara']);
}


$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY id";
$forumlar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

//	forum adlar� al�n�yor
while ($forumlar_satir = mysql_fetch_assoc($forumlar_sonuc))
{
	$fid = $forumlar_satir['id'];
	$forum_baslik[$fid] = $forumlar_satir['forum_baslik'];
}



//	SORGU SONUCUNDAK� TOPLAM SONU� SAYISI ALINIYOR	//

$result = mysql_query("SELECT kulid FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%'");
$satir_sayi = mysql_num_rows($result);

$ozelizinler_kota = 30;

$toplam_sayfa = ($satir_sayi / $ozelizinler_kota);
settype($toplam_sayfa,'integer');

if (($satir_sayi % $ozelizinler_kota) != 0) $toplam_sayfa++;



//	�ZEL �Z�NL� KULLANICILARIN B�LG�LER� �EK�L�YOR	//

if ((isset($_GET['sirala'])) AND ($_GET['sirala'] == 'fnoters'))
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY fno DESC LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}

elseif ((isset($_GET['sirala'])) AND ($_GET['sirala'] == 'ad_AdanZye'))
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY kulad LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}

elseif ((isset($_GET['sirala'])) AND ($_GET['sirala'] == 'ad_ZdenAya'))
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY kulad DESC LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}

elseif ((isset($_GET['sirala'])) AND ($_GET['sirala'] == 'izinegore'))
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY yonetme DESC, fno LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}

elseif ((isset($_GET['sirala'])) AND ($_GET['sirala'] == 'grup'))
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY grup DESC, fno LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}

else
{
	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE kulad LIKE '$_GET[kul_ara]%' ORDER BY fno LIMIT $_GET[sayfa],$ozelizinler_kota";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$_GET['sirala'] = '';
}

$sayfa_adi = 'Y�netim �zel izinli �ye ve Gruplar';
include 'yonetim_baslik.php';

?>


<table cellspacing="1" cellpadding="0" width="760" border="0" align="center" class="tablo_border">
	<tbody>
	<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tbody>
	<tr>
	<td align="center" valign="top">


<table cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
	<td align="center" class="liste-veri" height="25">
<a href="kullanicilar.php">Etkin �yeler</a>
&nbsp; | &nbsp;
<a href="kullanicilar.php?kip=etkisiz">Etkin Olmayanlar</a>
&nbsp; | &nbsp;
<a href="kullanicilar.php?kip=engelli">Engellenenler</a>
&nbsp; | &nbsp;
<a href="ozel_izinler.php">�zel �zinliler</a>
&nbsp; | &nbsp;
<a href="kul_izinler.php">�ye �zinleri</a>
&nbsp; | &nbsp;
<a href="gruplar.php">Gruplar</a>
&nbsp; | &nbsp;
<a href="yeni_uye.php">Yeni �ye Ekle</a>
	</td>
	</tr>
</table>


<table cellspacing="1" cellpadding="0" width="96%" border="0" class="tablo_border3">
	<tbody>
	<tr>
	<td align="center" valign="top" class="tablo_ici">

<table cellspacing="0" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tbody>
	<tr>
	<td class="baslik">
	<br>
- �zel izinli �ye ve Gruplar -
	</td>
	</tr>

	<tr>
	<td align="center">

<form action="ozel_izinler.php" name="kul_ara" method="get">

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">

	<tr>
	<td class="liste-veri" valign="bottom" height="35" align="left">
<input class="formlar" type="text" name="kul_ara" size="20" maxlength="20" value="<?php
echo str_replace('%','*',$_GET['kul_ara'])
?>">
&nbsp;<input type="submit" value="Ara" class="dugme">
	</td>
	<td class="liste-veri" valign="bottom" align="right">
<select name="sirala" class="formlar">
<option value="1">Forum S�ras�na g�re

<option value="fnoters" <?php if ( (isset($_GET['sirala'])) AND ($_GET['sirala'] == 'fnoters') ) echo 'selected="selected"' ?>>
Forum S�ras�na g�re tersten

<option value="ad_AdanZye" <?php if ( (isset($_GET['sirala'])) AND ($_GET['sirala'] == 'ad_AdanZye') ) echo 'selected="selected"' ?>>
Kullan�c� ad�na g�re A'dan Z'ye

<option value="ad_ZdenAya" <?php if ( (isset($_GET['sirala'])) AND ($_GET['sirala'] == 'ad_ZdenAya') ) echo 'selected="selected"' ?>>
Kullan�c� ad�na g�re Z'den A'ya

<option value="izinegore" <?php if ( (isset($_GET['sirala'])) AND ($_GET['sirala'] == 'izinegore') ) echo 'selected="selected"' ?>>
Yetkisine g�re(Yard�mc�lar �nde)

<option value="grup" <?php if ( (isset($_GET['sirala'])) AND ($_GET['sirala'] == 'grup') ) echo 'selected="selected"' ?>>
Gruplar �nde

</select>
&nbsp;<input type="submit" value="�yeleri s�rala" class="dugme">
	</td>
	</tr>
	
	<tr>
	<td colspan="2">

<table cellspacing="1" width="100%" cellpadding="5" border="0" align="center" class="tablo_border4">
	<tr class="forum_baslik">
	<td align="center" width="30">&nbsp;</td>
	<td align="center">�ye - Grup Ad�</td>
	<td align="center" width="220">Forum Ad�</td>
	<td align="center" width="45">Okuma</td>
	<td align="center" width="45">Konu</td>
	<td align="center" width="45">Cevap</td>
	<td align="center" width="50">Y�netme</td>
	</tr>

<?php

if ($satir_sayi == 0):

echo '<tr class="liste-etiket" bgcolor="'.$yazi_tabani1.'">
	<td colspan="9" align="center" height="70" valign="center">
Arad���n�z ko�ula uyan �zel izinli �ye yok !
	</td>
	</tr>';

endif;


while ($ozelizinler_satir = mysql_fetch_array($sonuc2)):

	echo '<tr class="liste-veri" bgcolor="'.$yazi_tabani1.'" onMouseOver="this.bgColor= \''.$yazi_tabani2.'\'" onMouseOut="this.bgColor= \''.$yazi_tabani1.'\'">';


	if ($ozelizinler_satir['grup'] == 0)
		echo '<td align="center">
		<a href="kullanici_degistir.php?u='.$ozelizinler_satir['kulid'].'" title="Kullan�c� profilini de�i�tir"><img alt="de�i�tir" '.$simge_degistir.'></a>
		</td>

		<td align="left" title="Kullan�c� yetkilerini de�i�tir"><b>�ye:</b>
&nbsp;&nbsp;<a href="kul_izinler.php?kim='.$ozelizinler_satir['kulad'].'">'.$ozelizinler_satir['kulad'].'</a>';


	else echo '<td align="center" width="30">
	<a href="gruplar.php?duzenle='.$ozelizinler_satir['grup'].'#duzenle" title="Grubu D�zenle"><img alt="de�i�tir" '.$simge_degistir.'></a>
	</td>

	<td align="left" title="Grup yetkilerini de�i�tir"><b>Grup:</b>
&nbsp;<a href="kul_izinler.php?grup='.$ozelizinler_satir['grup'].'">'.$ozelizinler_satir['kulad'].'</a>';

?>
	</td>
	<td align="left">
<?php echo '<a href="../forum.php?f='.$ozelizinler_satir['fno'].'">'.$forum_baslik[$ozelizinler_satir['fno']].'</a>' ?>
	</td>
	<td align="center">
<?php if ($ozelizinler_satir['okuma'] == 1) echo 'var'; else echo '<b>yok</b>'; ?>
	</td>
	<td align="center">
<?php if ($ozelizinler_satir['konu_acma'] == 1) echo 'var'; else echo '<b>yok</b>'; ?>
	</td>
	<td align="center">
<?php if ($ozelizinler_satir['yazma'] == 1) echo 'var'; else echo '<b>yok</b>'; ?>
	</td>
	<td align="center">
<?php if ($ozelizinler_satir['yonetme'] == 1) echo 'var'; else echo '<b>yok</b>'; ?>
	</td>
	</tr>

<?php endwhile; ?>

</table>
<br>

					<!--	SAYFALAR BA�LANGI�		-->

<span id="sayfalama">
<?php if ($satir_sayi > $ozelizinler_kota): ?>
<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tr>
	<td class="forum_baslik">
Toplam <?php echo $toplam_sayfa; ?> Sayfa:&nbsp;
	</td>
<?php
if ($_GET['sayfa'] != 0)
{
	echo '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
	echo '&nbsp;<a href="ozel_izinler.php?sayfa=0&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&laquo;ilk</a>&nbsp;</td>';
	
	echo '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
	echo '&nbsp;<a href="ozel_izinler.php?sayfa='.($_GET['sayfa'] - $ozelizinler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&lt;</a>&nbsp;</td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['sayfa']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['sayfa'] / $ozelizinler_kota) - 3))	{	}
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['sayfa'] + 8)) {break;}
		if (($sayi == 0) and ($_GET['sayfa'] == 0))
		{
			echo '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			echo '&nbsp;<b>[1]</b>&nbsp;</td>';
		}

		elseif (($sayi + 1) == (($_GET['sayfa'] / $ozelizinler_kota) + 1))
		{
			echo '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			echo '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}

		else
		{
			echo '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">';

			echo '&nbsp;<a href="ozel_izinler.php?sayfa='.($sayi * $ozelizinler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}
if ($_GET['sayfa'] < ($satir_sayi - $ozelizinler_kota))
{
	echo '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	echo '&nbsp;<a href="ozel_izinler.php?sayfa='.($_GET['sayfa'] + $ozelizinler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&gt;</a>&nbsp;</td>';

	echo '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	echo '&nbsp;<a href="ozel_izinler.php?sayfa='.(($toplam_sayfa - 1) * $ozelizinler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">son&raquo;</a>&nbsp;</td>';
}

echo '</tr>
</table>';

endif;
?>
</span>
					<!--	SAYFALAR B�T��		-->


<div class="liste-veri" align="left"><font size="1">
Arad���n�z ko�ula uyan �zel izinli �ye ve grup say�s�: <b><?php echo $satir_sayi ?></b>
<br>Y�netme yetkisi verilen �ye o b�l�m�n yard�mc�s� olur.
</font></div>


</td></tr></table>
</form>
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