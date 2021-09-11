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


if ( ( isset($_POST['izindegistir']) ) AND ( $_POST['izindegistir'] == 'izindegistir' ) )
{
	$_POST['okuma_izni'] = zkTemizle($_POST['okuma_izni']);
	$_POST['yazma_izni'] = zkTemizle($_POST['yazma_izni']);
	$_POST['konu_acma_izni'] = zkTemizle($_POST['konu_acma_izni']);
	$_POST['fno'] = zkTemizle($_POST['fno']);


	// misafirlere açýksa gizlenmesin
	if ($_POST['okuma_izni'] == '0') $_POST['gizle'] = 0;


	// okuma izni sadece yöneticiler içinse ve diðer izinler de kapalý deðilse, diðer izinleri sadece yönetici olarak deðiþtir
	if ($_POST['okuma_izni'] == '1')
	{
		if ($_POST['konu_acma_izni'] != '5') $_POST['konu_acma_izni'] = 1;
		if ($_POST['yazma_izni'] != '5') $_POST['yazma_izni'] = 1;
	}


	// okuma izni yardýmcýlar içinse ve diðer izinler daha düþükse
	if ($_POST['okuma_izni'] == '2')
	{
		if (($_POST['konu_acma_izni'] == '0') OR ($_POST['konu_acma_izni'] == '3')) $_POST['konu_acma_izni'] = 2;
		if (($_POST['yazma_izni'] == '0') OR ($_POST['yazma_izni'] == '3')) $_POST['yazma_izni'] = 2;
	}


	// okuma izni özel üyeler içinse ve diðer izinler tüm üyeler ise
	if ($_POST['okuma_izni'] == '3')
	{
		if ($_POST['konu_acma_izni'] == '0') $_POST['konu_acma_izni'] = 3;
		if ($_POST['yazma_izni'] == '0') $_POST['yazma_izni'] = 3;
	}


	// okuma izni kapalý ise diðer izinleri de kapat
	if ($_POST['okuma_izni'] == '5')
	{
		$_POST['konu_acma_izni'] = 5;
		$_POST['yazma_izni'] = 5;
	}



	// FORUM ÝZÝN BÝLGÝLERÝ DEÐÝÞTÝRÝLÝYOR //

	$strSQL = "UPDATE $tablo_forumlar SET 
	okuma_izni='$_POST[okuma_izni]', yazma_izni='$_POST[yazma_izni]', konu_acma_izni='$_POST[konu_acma_izni]', gizle='$_POST[gizle]'
	WHERE id='$_POST[fno]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
}




elseif ( ( isset($_POST['izingoster']) ) AND ( $_POST['izingoster'] == 'izingoster' ) )
{
	if ( (!isset($_POST['forum_izin'])) OR (is_numeric($_POST['forum_izin']) == false) )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['forum_izin'] = zkTemizle($_POST['forum_izin']);


	// FORUM ÝZÝN BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT id,forum_baslik,okuma_izni,yazma_izni,konu_acma_izni,gizle FROM $tablo_forumlar
			WHERE id='$_POST[forum_izin]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$izinler_satir = mysql_fetch_array($sonuc);
}




$sayfa_adi = 'Yönetim Forum Ýzinleri';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';
?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Forum Ýzinleri -

	</td>
	</tr>
	
	<tr>
	<td height="20"></td>
	</tr>
	
	<tr>
	<td align="center">
<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td align="center" valign="top">



<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html' ?>




		<!--	FORUM ÝZÝNLERÝ TABLOSU BAÞI		-->


<table cellspacing="1" width="77%" cellpadding="0" border="0" align="right" class="tablo_border4">
	<tr>
	<td align="center" valign="top">


<form name="forum_izinleri" action="fizinler.php" method="post">
<input type="hidden" name="izingoster" value="izingoster">

<table cellspacing="0" width="100%" cellpadding="2" border="0" align="left" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Forum Seçimi -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<?php

if ( (!isset($_POST['izindegistir'])) AND (!isset($_POST['izingoster'])) )
{
echo '
    <br> &nbsp; &nbsp; &nbsp; Ýzinlerini görüntülemek ve/veya düzenlemek istediðiniz forumu aþaðýdan seçip
    <br>&nbsp;<b>Ýzinleri Göster</b> düðmesini týklayýn.

    <p> &nbsp; &nbsp; &nbsp; Forum bölümü yetkisi olarak <b>Özel Üyeleri</b> seçtiðinizde, istediðiniz kullanýcýya ilgili forum bölümüne eriþimi için okuma, yazma veya yönetme yetkisi verebilirsiniz.
    <br>Herhangi bir üyeye forum bölümünü yönetme yetkisi verdiðinizde üye o forum bölümünün yardýmcýsý olur, yetkisi de <b>Bölüm Yardýmcýsý</b> olur.

    <p> &nbsp; &nbsp; &nbsp; Forum bölümünün ayarlanmýþ yetkiden daha düþük yetkili üyeler ilgili forumu yönetemez.
    Yani herhangi bir yetkisi yöneticiler olarak ayarlanmýþ bir forum bölümü için, daha düþük yetkiye sahip bir üyeye yönetme yetkisi verilemez. Bu durum yardýmcý yetkisi verilmiþ forum bölümleri için de geçerlidir.

    <p> &nbsp; &nbsp; &nbsp; <b><u>Bölüm Yardýmcýsý Atama:</u></b>&nbsp; Herhangi bir üyeye bölüm yardýmcýsý yetkisi ve/veya özel yetkiler vermek için, <a href="kullanicilar.php">bu sayfadan</a>
    istediðiniz üyenin kullanýcý adýný týklayýn. Açýlan, "Kullanýcý Profilini Deðiþtir" sayfasýndan <b>Diðer Yetkiler</b> baðlantýsýný týklayýn. Yeni açýlan sayfadan yetki vermek istediðiniz forumu seçerek kullanýcýya istediðiniz yetkiyi verebilirsiniz.
    <br>Yönetme yetkisi verdiðinizde üyenin yetkisi bölüm yardýmcýsý olur.

    <p> &nbsp; &nbsp; &nbsp; <b><u>Forum Gizleme:</u></b>&nbsp; Ýstediðiniz forum bölümlerini, 
ayarlanan okuma yetkisinden düþük üyelere gizleyebilirsiniz. Mesela bir forum bölümünün okuma yetkisini sadece yöneticiler olarak ayarlayýp gizlediðinizde, bu bölüm ve konularý sadece yöneticiler tarafýndan görüntülenecektir.';
}

else echo '<br><center><a href="fizinler.php"><b>- Yardým Göster -</b></a></center>';

?>
<br><br>

<center>
<b>Forum Seç:</b> &nbsp;
<br><br>

<?php


$forum_secenek = '<select name="forum_izin" class="formlar" size="15">';


// forum dalý adlarý çekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar
					WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"';

			if ( ( isset($_POST['forum_izin']) ) AND ($_POST['forum_izin'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			else $forum_secenek .= '>';

			$forum_secenek .= ' &nbsp; - '.$forum_satir['forum_baslik'];
		}


		else
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"';

			if ( ( isset($_POST['forum_izin']) ) AND ($_POST['forum_izin'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			else $forum_secenek .= '>';

			$forum_secenek .= ' &nbsp; - '.$forum_satir['forum_baslik'];


			while ($alt_forum_satir = mysql_fetch_array($sonuca))
			{
			
				$forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'"';

				if ( ( isset($_POST['forum_izin']) ) AND ($_POST['forum_izin'] == $alt_forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
				elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $alt_forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
				else $forum_secenek .= '>';

				$forum_secenek .= ' &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
			}
		}
	}
}


echo $forum_secenek.'</select>';


?>


<br><br><br>
<input type="submit" value="Ýzinleri Göster" class="dugme">
</center>
<br><br>

<?php if ( ( isset($_POST['izindegistir']) ) AND ( $_POST['izindegistir'] == 'izindegistir' ) )
echo '<p align="center"><b><font color="green">Forum izinleri deðiþtirilmiþtir.</b></p><br>'; ?>

	</td>
	</tr>
</table>
</form>

	</td>
	</tr>


<?php
//	FORUM ÝZÝNLERÝNÝ GÖSTER TIKLANMIÞSA	//

if ( isset($izinler_satir) ):
?>

	<tr>
	<td align="center" valign="top">

<form name="forum_izinleri" action="fizinler.php" method="post">
<input type="hidden" name="izindegistir" value="izindegistir">
<input type="hidden" name="fno" value="<?php echo $izinler_satir['id'] ?>">

<table cellspacing="0" width="100%" cellpadding="2" border="0" align="left">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Forum Ýzinleri -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="center" class="liste-etiket">
<br>


..:::&nbsp; <?php echo $izinler_satir['forum_baslik'] ?> &nbsp;:::..


<br><br>


<table cellspacing="1" cellpadding="2" width="92%" border="0" align="center" class="tablo_ici">
	<tr>
	<td class="liste-etiket" align="left" valign="top" width="110">Okuma:</td>

	<td class="liste-veri" align="left" valign="middle">
<select name="okuma_izni" class="formlar" size="6">
<option value="0" <?php if ($izinler_satir['okuma_izni'] == 0) echo 'selected="selected"'; ?>>
Herkes

<option value="4" <?php if ($izinler_satir['okuma_izni'] == 4) echo 'selected="selected"'; ?>>
Tüm Üyeler

<option value="3" <?php if ($izinler_satir['okuma_izni'] == 3) echo 'selected="selected"'; ?>>
Özel Üyeler ve Yöneticiler

<option value="2" <?php if ($izinler_satir['okuma_izni'] == 2) echo 'selected="selected"'; ?>>
Yardýmcýlar ve Yöneticiler

<option value="1" <?php if ($izinler_satir['okuma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Yöneticiler

<option value="5" <?php if ($izinler_satir['okuma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapalý
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Konu Açma:</td>
	<td class="liste-veri" align="left" valign="middle">
<select name="konu_acma_izni" class="formlar" size="5">
<option value="0" <?php if ($izinler_satir['konu_acma_izni'] == 0) echo 'selected="selected"'; ?>>
Tüm Üyeler

<option value="3" <?php if ($izinler_satir['konu_acma_izni'] == 3) echo 'selected="selected"'; ?>>
Özel Üyeler ve Yöneticiler

<option value="2" <?php if ($izinler_satir['konu_acma_izni'] == 2) echo 'selected="selected"'; ?>>
Yardýmcýlar ve Yöneticiler

<option value="1" <?php if ($izinler_satir['konu_acma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Yöneticiler

<option value="5" <?php if ($izinler_satir['konu_acma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapalý
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Cevap Yazma:</td>
	<td class="liste-veri" align="left" valign="middle">
<select name="yazma_izni" class="formlar" size="5">
<option value="0" <?php if ($izinler_satir['yazma_izni'] == 0) echo 'selected="selected"'; ?>>
Tüm Üyeler

<option value="3" <?php if ($izinler_satir['yazma_izni'] == 3) echo 'selected="selected"'; ?>>
Özel Üyeler ve Yöneticiler

<option value="2" <?php if ($izinler_satir['yazma_izni'] == 2) echo 'selected="selected"'; ?>>
Yardýmcýlar ve Yöneticiler

<option value="1" <?php if ($izinler_satir['yazma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Yöneticiler

<option value="5" <?php if ($izinler_satir['yazma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapalý
</select>
<br><br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Gizleme:</td>
	<td class="liste-veri" align="left" valign="bottom">
<select name="gizle" class="formlar">
<?php
echo '<option value="0"';
if ($izinler_satir['gizle'] == 0) echo ' selected="selected"';
echo '>Göster';

echo '<option value="1"';
if ($izinler_satir['gizle'] == 1) echo ' selected="selected"';
echo '>Gizle';
?>
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Yönetme:</td>
	<td class="liste-veri" align="left" valign="top">
<?php
if ( ($izinler_satir['yazma_izni'] == 1) OR ($izinler_satir['konu_acma_izni'] == 1) OR ($izinler_satir['okuma_izni'] == 1) )
    echo 'Sadece Forum Yöneticileri';

else if ( ($izinler_satir['yazma_izni'] == 2) OR ($izinler_satir['konu_acma_izni'] == 2) OR ($izinler_satir['okuma_izni'] == 2) )
    echo 'Forum Yöneticileri ve Forum Yardýmcýlarý';

else if ( ($izinler_satir['yazma_izni'] == 3) OR ($izinler_satir['konu_acma_izni'] == 3) OR ($izinler_satir['okuma_izni'] == 3) )
    echo 'Forum yöneticileri, yardýmcýlarý ve bölümün yardýmcýlarý
    <br><br><a href="kullanicilar.php">Bu Bölüme Yardýmcýlar Ata</a>';

elseif ( ($izinler_satir['yazma_izni'] == 5) OR ($izinler_satir['konu_acma_izni'] == 5) OR ($izinler_satir['okuma_izni'] == 5) )
    echo 'Sadece Forum Yöneticileri';

else echo 'Forum yöneticileri, yardýmcýlarý ve bölümün yardýmcýlarý
    <br><br><a href="kullanicilar.php">Bu Bölüme Yardýmcýlar Ata</a>';
?>
	</td>
	</tr>
</table>

<br>
<input type="submit" value="Ýzinleri Deðiþtir" class="dugme">

<br><br>
	</td>
	</tr>
</table>
</form>

	</td>
	</tr>

<?php
	//	FORM ÝZÝNLERÝ GÖRÜNTÜLENÝYOR - BÝTÝÞ	//

endif;
?>


</table>
</table>
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