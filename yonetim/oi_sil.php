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
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}


if ( ( isset($_POST['oi_sil']) ) AND ( $_POST['oi_sil'] == 'oi_sil' ) )
{
	if ( ($_POST['gunsayisi'] <= 0) OR ($_POST['gunsayisi'] > 999) )
	{
		header('Location: ../hata.php?hata=153');
		exit();
	}

	$tarih = time();
	$hesapla = ($tarih - ($_POST['gunsayisi'] * 86400));


	$strSQL = "DELETE FROM $tablo_ozel_ileti WHERE gonderme_tarihi < $hesapla AND alan_kutu!=4 AND gonderen_kutu!=4 AND okunma_tarihi IS NOT NULL";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	header('Location: ../hata.php?bilgi=47');
	exit();
}


elseif ( ( isset($_POST['oi_hesapla']) ) AND ( $_POST['oi_hesapla'] == 'oi_hesapla' ) )
{
	if ( ($_POST['gunsayisi'] <= 0) OR ($_POST['gunsayisi'] > 999) )
	{
		header('Location: ../hata.php?hata=153');
		exit();
	}


	$onayal = 'onayal';
	$tarih = time();
	$hesapla = ($tarih - ($_POST['gunsayisi'] * 86400));


	// S�L�NECEK �ZEL �LET� SAYISI ALINIYOR //

	$strSQL = "SELECT * FROM $tablo_ozel_ileti WHERE gonderme_tarihi < $hesapla AND alan_kutu!=4 AND gonderen_kutu!=4 AND okunma_tarihi IS NOT NULL";
	$eski_mesaj_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$oi_sayi = mysql_num_rows($eski_mesaj_sonuc);
}


$sayfa_adi = 'Y�netim �zel �leti Silme';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';
?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- �zel �leti Silme -

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




		<!--	FORUM �Z�NLER� TABLOSU BA�I		-->

<?php

//	BA�LIKLARI S�L TIKLANMAMI�SA	//

if (empty($onayal)):

?>

<form name="oi_sil" action="oi_sil.php" method="post">
<input type="hidden" name="oi_hesapla" value="oi_hesapla">

<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- �zel �leti Silme -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br> &nbsp; &nbsp; &nbsp; Bu sayfadan; belirtti�iniz g�nden eski, kaydedilen kutusuna ta��nmam�� �zel iletileri silebilirsiniz. G�n� girdikten sonra gelen sayfada, ka� �zel iletinin silinece�i belirtilir. �sterseniz silme i�leminden bu k�s�mda vazge�ebilirsiniz.

<br><br> &nbsp; &nbsp; &nbsp; �zel iletileri silmeden �nce <a href="duyurular.php?kip=yeni">buradan</a> sayfaya bir duyurusu ekleyerek, �yeleri silinmesini istemedikleri iletileri kaydetmeleri konusunda uyarabilirsiniz.

<br><br><br>

<center>

<br><br>


<input type="text" name="gunsayisi" size="4" class="formlar" maxlength="3">
<b>&nbsp; G�nden eski kaydedilmemi� �zel iletiler&nbsp;</b>
<br><br><br>
<input type="submit" value="Bul" class="dugme">
<br><br>
</center>
	</td>
	</tr>
</table>
</form>

<?php

//	BA�LIKLARI S�L TIKLANMI�SA	//

elseif (isset($onayal)):

if ($oi_sayi > 0)
{
	echo '
	<form name="oi_sil" action="oi_sil.php" method="post">
	<input type="hidden" name="oi_sil" value="oi_sil">
	<input type="hidden" name="gunsayisi" value="'.$_POST['gunsayisi'].'">';
}


echo '<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Silinecek �zel �leti Say�s� -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>';



if ($oi_sayi > 0)
{
	echo '
&nbsp; <b>'.$_POST['gunsayisi'].'</b> g�nden eski �zel ileti say�s�: <b>'.$oi_sayi.'</b>
	<br><br><br>
<p align="center">
<b>�zel iletileri silmek istedi�inize emin misiniz?</b>
<br><br><br>
<input type="submit" value="Evet Sil" class="dugme">
</p>';
}


else
{
	echo '<center>
	<br>
&nbsp; <b>Forumda '.$_POST['gunsayisi'].' g�nden eski silinecek �zel ileti yok.</b>
	<br><br><br>
	<center>
';
}


echo '

<br>
	</td>
	</tr>
</table>
</form>';


	//	FORM �Z�NLER� G�R�NT�LEN�YOR - B�T��	//

endif;

?>


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