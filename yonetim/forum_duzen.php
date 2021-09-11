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


// KÝP VERÝSÝ YOKSA UYARI VER	//

if ( (empty($_GET['kip'])) )
{
	header('Location: ../hata.php?hata=138');
	exit();
}


@ini_set('magic_quotes_runtime', 0);


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// oturum kodu
$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

if ( (isset($_GET['dalno'])) AND (is_numeric($_GET['dalno']) == false) )
{
	header('Location: ../hata.php?hata=139');
	exit();
}


if ( (isset($_GET['fno'])) AND (is_numeric($_GET['fno']) == false) )
{
	header('Location: ../hata.php?hata=14');
	exit();
}


if (isset($_GET['dalno'])) $_GET['dalno'] = zkTemizle($_GET['dalno']);
else $_GET['dalno'] = 0;

if (isset($_GET['fno'])) $_GET['fno'] = zkTemizle($_GET['fno']);
else $_GET['fno'] = 0;



// SAYFA BAÞLIÐI //

$sayfa_adi = 'Yönetim Forum Düzenleme';

include 'yonetim_baslik.php';



echo '<form action="forum_duzen_yap.php?o='.$o.'" method="post" name="forum_duzen">
<input type="hidden" name="fno" value="'.$_GET['fno'].'">
<input type="hidden" name="kip" value="'.$_GET['kip'].'">';

?>

<table cellspacing="1" cellpadding="0" width="600" border="0" align="center" class="tablo_border">
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

<table cellspacing="5" width="100%" cellpadding="4" border="0" align="center" class="tablo_ici">
	<tr>
	<td height="20"></td>
	</tr>

	<tr>
	<td colspan="2" class="baslik">
- Forum Düzenleme -
	</td>
	</tr>


<?php

// FORUM DALI DÜZENLE	//

if($_GET['kip'] == 'dal_duzenle'):

$strSQL = "SELECT * FROM $tablo_dallar WHERE id='$_GET[dalno]'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

$forum_satir = mysql_fetch_array($sonuc);


echo '
	<tr>
	<td colspan="2" width="140" height="20"></td>
	</tr>

	<tr>
	<td class="liste-etiket" align="center">
Forum Dalý Baþlýðý :
	</td>
	<td class="liste-veri">

<input type="hidden" name="dalno" value="'.$_GET['dalno'].'">
<input type="text" name="forum_baslik" size="50" value="'
.@str_replace('&','&#38',$forum_satir['ana_forum_baslik']).
'" class="formlar">

	</td>
	</tr>
';


// FORUM DÜZENLE	//

elseif($_GET['kip'] == 'forum_duzenle'):

$strSQL = "SELECT * FROM $tablo_forumlar WHERE id='$_GET[fno]'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

$forum_satir = mysql_fetch_array($sonuc);

$ust_forumlar_formu = '<input type="hidden" name="dalno" value="'.$forum_satir['dal_no'].'">';




//	ALT FORUM FORMU HAZIRLANIYOR	//

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$sonuc_dal = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


if ($forum_satir['alt_forum'] == '0')
	$ust_forumlar_formu .= '<select name="alt_forum" class="formlar">
	<option value="ust" selected="selected">ÜST FORUM';

else
	$ust_forumlar_formu = '<select name="alt_forum" class="formlar">
	<option value="ust">ÜST FORUM';



while ($ust_dal_satir = mysql_fetch_assoc($sonuc_dal))
{
	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE dal_no='$ust_dal_satir[id]' AND alt_forum='0' ORDER BY sira";
	$ust_forum_sonuc = mysql_query($strSQL);


	$ust_forumlar_formu .= '
	<option value="">[ '.$ust_dal_satir['ana_forum_baslik'].' ]';


	while ($ust_forum_satir = mysql_fetch_assoc($ust_forum_sonuc))
	{
		if ($ust_forum_satir['id'] == $forum_satir['alt_forum'])
			$ust_forumlar_formu .= '
			<option value="'.$ust_forum_satir['id'].'" selected="selected"> &nbsp; - '.$ust_forum_satir['forum_baslik'];

		elseif ($ust_forum_satir['id'] != $_GET['fno'])
		$ust_forumlar_formu .= '
		<option value="'.$ust_forum_satir['id'].'"> &nbsp; - '.$ust_forum_satir['forum_baslik'];
	}
}

$ust_forumlar_formu .= '
</select>

';

?>
	<tr>
	<td colspan="2" align="left" class="liste-veri">
<br>
&nbsp; &nbsp; Buradan; forum baþlýðýný, açýklamasýný ve resmini deðiþtirebilirsiniz.
<br>
Alt forum seçeneði ile, istediðiniz forumun alt forumu yapabilir veya tekrar üst forum haline getirebilirsiniz.
<br>
<br>
	</td>
	</tr>


	<tr>
	<td class="liste-etiket" width="140" align="left">Forum Baþlýðý :</td>

	<td class="liste-veri" align="left">
<input type="text" name="forum_baslik" size="50" value="<?php echo @str_replace('&','&#38',$forum_satir['forum_baslik']) ?>" class="formlar">
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left">Forum Resmi :</td>
	<td class="liste-veri" align="left">
<input type="text" name="resim" size="50" value="<?php
if (isset($forum_satir['resim'])) echo $forum_satir['resim'] ?>" class="formlar">
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left">Alt Forum :</td>
	<td class="liste-veri" align="left">
<?php echo $ust_forumlar_formu; ?>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" valign="top" align="left">
<b>Açýklama :</b>
<br><br><br>
HTML <b>açýk</b>
<br>
BBCode <b>kapalý</b>
	</td>
	<td class="liste-veri" align="left">
<textarea cols="48" rows="8" name="forum_bilgi" class="formlar"><?php echo @str_replace('&','&#38',$forum_satir['forum_bilgi']) ?></textarea>
	</td>
	</tr>

<?php endif ?>

	<tr>
	<td colspan="2" align="center" class="liste-veri">
<br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<input name="mesaj_gonder" type="submit" value="Düzenle" class="dugme">
<br>
<br>
	</td>
	</tr>

	<tr>
	<td height="10"></td>
	</tr>

	</table>
</td></tr></table>
</td></tr>
<tr><td width="140" height="20"></td></tr></table>
</td></tr></table>
</form>

<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>