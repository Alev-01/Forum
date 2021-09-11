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


if ( ( isset($_POST['izindegistir']) ) AND ( $_POST['izindegistir'] == 'izindegistir' ) )
{
	$_POST['okuma_izni'] = zkTemizle($_POST['okuma_izni']);
	$_POST['yazma_izni'] = zkTemizle($_POST['yazma_izni']);
	$_POST['konu_acma_izni'] = zkTemizle($_POST['konu_acma_izni']);
	$_POST['fno'] = zkTemizle($_POST['fno']);


	// misafirlere a��ksa gizlenmesin
	if ($_POST['okuma_izni'] == '0') $_POST['gizle'] = 0;


	// okuma izni sadece y�neticiler i�inse ve di�er izinler de kapal� de�ilse, di�er izinleri sadece y�netici olarak de�i�tir
	if ($_POST['okuma_izni'] == '1')
	{
		if ($_POST['konu_acma_izni'] != '5') $_POST['konu_acma_izni'] = 1;
		if ($_POST['yazma_izni'] != '5') $_POST['yazma_izni'] = 1;
	}


	// okuma izni yard�mc�lar i�inse ve di�er izinler daha d���kse
	if ($_POST['okuma_izni'] == '2')
	{
		if (($_POST['konu_acma_izni'] == '0') OR ($_POST['konu_acma_izni'] == '3')) $_POST['konu_acma_izni'] = 2;
		if (($_POST['yazma_izni'] == '0') OR ($_POST['yazma_izni'] == '3')) $_POST['yazma_izni'] = 2;
	}


	// okuma izni �zel �yeler i�inse ve di�er izinler t�m �yeler ise
	if ($_POST['okuma_izni'] == '3')
	{
		if ($_POST['konu_acma_izni'] == '0') $_POST['konu_acma_izni'] = 3;
		if ($_POST['yazma_izni'] == '0') $_POST['yazma_izni'] = 3;
	}


	// okuma izni kapal� ise di�er izinleri de kapat
	if ($_POST['okuma_izni'] == '5')
	{
		$_POST['konu_acma_izni'] = 5;
		$_POST['yazma_izni'] = 5;
	}



	// FORUM �Z�N B�LG�LER� DE���T�R�L�YOR //

	$strSQL = "UPDATE $tablo_forumlar SET 
	okuma_izni='$_POST[okuma_izni]', yazma_izni='$_POST[yazma_izni]', konu_acma_izni='$_POST[konu_acma_izni]', gizle='$_POST[gizle]'
	WHERE id='$_POST[fno]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
}




elseif ( ( isset($_POST['izingoster']) ) AND ( $_POST['izingoster'] == 'izingoster' ) )
{
	if ( (!isset($_POST['forum_izin'])) OR (is_numeric($_POST['forum_izin']) == false) )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['forum_izin'] = zkTemizle($_POST['forum_izin']);


	// FORUM �Z�N B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT id,forum_baslik,okuma_izni,yazma_izni,konu_acma_izni,gizle FROM $tablo_forumlar
			WHERE id='$_POST[forum_izin]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$izinler_satir = mysql_fetch_array($sonuc);
}




$sayfa_adi = 'Y�netim Forum �zinleri';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';
?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Forum �zinleri -

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




		<!--	FORUM �Z�NLER� TABLOSU BA�I		-->


<table cellspacing="1" width="77%" cellpadding="0" border="0" align="right" class="tablo_border4">
	<tr>
	<td align="center" valign="top">


<form name="forum_izinleri" action="fizinler.php" method="post">
<input type="hidden" name="izingoster" value="izingoster">

<table cellspacing="0" width="100%" cellpadding="2" border="0" align="left" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Forum Se�imi -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<?php

if ( (!isset($_POST['izindegistir'])) AND (!isset($_POST['izingoster'])) )
{
echo '
    <br> &nbsp; &nbsp; &nbsp; �zinlerini g�r�nt�lemek ve/veya d�zenlemek istedi�iniz forumu a�a��dan se�ip
    <br>&nbsp;<b>�zinleri G�ster</b> d��mesini t�klay�n.

    <p> &nbsp; &nbsp; &nbsp; Forum b�l�m� yetkisi olarak <b>�zel �yeleri</b> se�ti�inizde, istedi�iniz kullan�c�ya ilgili forum b�l�m�ne eri�imi i�in okuma, yazma veya y�netme yetkisi verebilirsiniz.
    <br>Herhangi bir �yeye forum b�l�m�n� y�netme yetkisi verdi�inizde �ye o forum b�l�m�n�n yard�mc�s� olur, yetkisi de <b>B�l�m Yard�mc�s�</b> olur.

    <p> &nbsp; &nbsp; &nbsp; Forum b�l�m�n�n ayarlanm�� yetkiden daha d���k yetkili �yeler ilgili forumu y�netemez.
    Yani herhangi bir yetkisi y�neticiler olarak ayarlanm�� bir forum b�l�m� i�in, daha d���k yetkiye sahip bir �yeye y�netme yetkisi verilemez. Bu durum yard�mc� yetkisi verilmi� forum b�l�mleri i�in de ge�erlidir.

    <p> &nbsp; &nbsp; &nbsp; <b><u>B�l�m Yard�mc�s� Atama:</u></b>&nbsp; Herhangi bir �yeye b�l�m yard�mc�s� yetkisi ve/veya �zel yetkiler vermek i�in, <a href="kullanicilar.php">bu sayfadan</a>
    istedi�iniz �yenin kullan�c� ad�n� t�klay�n. A��lan, "Kullan�c� Profilini De�i�tir" sayfas�ndan <b>Di�er Yetkiler</b> ba�lant�s�n� t�klay�n. Yeni a��lan sayfadan yetki vermek istedi�iniz forumu se�erek kullan�c�ya istedi�iniz yetkiyi verebilirsiniz.
    <br>Y�netme yetkisi verdi�inizde �yenin yetkisi b�l�m yard�mc�s� olur.

    <p> &nbsp; &nbsp; &nbsp; <b><u>Forum Gizleme:</u></b>&nbsp; �stedi�iniz forum b�l�mlerini, 
ayarlanan okuma yetkisinden d���k �yelere gizleyebilirsiniz. Mesela bir forum b�l�m�n�n okuma yetkisini sadece y�neticiler olarak ayarlay�p gizledi�inizde, bu b�l�m ve konular� sadece y�neticiler taraf�ndan g�r�nt�lenecektir.';
}

else echo '<br><center><a href="fizinler.php"><b>- Yard�m G�ster -</b></a></center>';

?>
<br><br>

<center>
<b>Forum Se�:</b> &nbsp;
<br><br>

<?php


$forum_secenek = '<select name="forum_izin" class="formlar" size="15">';


// forum dal� adlar� �ekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlar� �ekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bak�l�yor
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
<input type="submit" value="�zinleri G�ster" class="dugme">
</center>
<br><br>

<?php if ( ( isset($_POST['izindegistir']) ) AND ( $_POST['izindegistir'] == 'izindegistir' ) )
echo '<p align="center"><b><font color="green">Forum izinleri de�i�tirilmi�tir.</b></p><br>'; ?>

	</td>
	</tr>
</table>
</form>

	</td>
	</tr>


<?php
//	FORUM �Z�NLER�N� G�STER TIKLANMI�SA	//

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
- Forum �zinleri -
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
T�m �yeler

<option value="3" <?php if ($izinler_satir['okuma_izni'] == 3) echo 'selected="selected"'; ?>>
�zel �yeler ve Y�neticiler

<option value="2" <?php if ($izinler_satir['okuma_izni'] == 2) echo 'selected="selected"'; ?>>
Yard�mc�lar ve Y�neticiler

<option value="1" <?php if ($izinler_satir['okuma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Y�neticiler

<option value="5" <?php if ($izinler_satir['okuma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapal�
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Konu A�ma:</td>
	<td class="liste-veri" align="left" valign="middle">
<select name="konu_acma_izni" class="formlar" size="5">
<option value="0" <?php if ($izinler_satir['konu_acma_izni'] == 0) echo 'selected="selected"'; ?>>
T�m �yeler

<option value="3" <?php if ($izinler_satir['konu_acma_izni'] == 3) echo 'selected="selected"'; ?>>
�zel �yeler ve Y�neticiler

<option value="2" <?php if ($izinler_satir['konu_acma_izni'] == 2) echo 'selected="selected"'; ?>>
Yard�mc�lar ve Y�neticiler

<option value="1" <?php if ($izinler_satir['konu_acma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Y�neticiler

<option value="5" <?php if ($izinler_satir['konu_acma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapal�
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Cevap Yazma:</td>
	<td class="liste-veri" align="left" valign="middle">
<select name="yazma_izni" class="formlar" size="5">
<option value="0" <?php if ($izinler_satir['yazma_izni'] == 0) echo 'selected="selected"'; ?>>
T�m �yeler

<option value="3" <?php if ($izinler_satir['yazma_izni'] == 3) echo 'selected="selected"'; ?>>
�zel �yeler ve Y�neticiler

<option value="2" <?php if ($izinler_satir['yazma_izni'] == 2) echo 'selected="selected"'; ?>>
Yard�mc�lar ve Y�neticiler

<option value="1" <?php if ($izinler_satir['yazma_izni'] == 1) echo 'selected="selected"'; ?>>
Sadece Y�neticiler

<option value="5" <?php if ($izinler_satir['yazma_izni'] == 5) echo 'selected="selected"'; ?>>
Kapal�
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
echo '>G�ster';

echo '<option value="1"';
if ($izinler_satir['gizle'] == 1) echo ' selected="selected"';
echo '>Gizle';
?>
</select>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Y�netme:</td>
	<td class="liste-veri" align="left" valign="top">
<?php
if ( ($izinler_satir['yazma_izni'] == 1) OR ($izinler_satir['konu_acma_izni'] == 1) OR ($izinler_satir['okuma_izni'] == 1) )
    echo 'Sadece Forum Y�neticileri';

else if ( ($izinler_satir['yazma_izni'] == 2) OR ($izinler_satir['konu_acma_izni'] == 2) OR ($izinler_satir['okuma_izni'] == 2) )
    echo 'Forum Y�neticileri ve Forum Yard�mc�lar�';

else if ( ($izinler_satir['yazma_izni'] == 3) OR ($izinler_satir['konu_acma_izni'] == 3) OR ($izinler_satir['okuma_izni'] == 3) )
    echo 'Forum y�neticileri, yard�mc�lar� ve b�l�m�n yard�mc�lar�
    <br><br><a href="kullanicilar.php">Bu B�l�me Yard�mc�lar Ata</a>';

elseif ( ($izinler_satir['yazma_izni'] == 5) OR ($izinler_satir['konu_acma_izni'] == 5) OR ($izinler_satir['okuma_izni'] == 5) )
    echo 'Sadece Forum Y�neticileri';

else echo 'Forum y�neticileri, yard�mc�lar� ve b�l�m�n yard�mc�lar�
    <br><br><a href="kullanicilar.php">Bu B�l�me Yard�mc�lar Ata</a>';
?>
	</td>
	</tr>
</table>

<br>
<input type="submit" value="�zinleri De�i�tir" class="dugme">

<br><br>
	</td>
	</tr>
</table>
</form>

	</td>
	</tr>

<?php
	//	FORM �Z�NLER� G�R�NT�LEN�YOR - B�T��	//

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