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


if ( ( isset($_POST['basliklari_sil']) ) AND ( $_POST['basliklari_sil'] == 'basliklari_sil' ) )
{
	$_POST['fno'] = @zkTemizle($_POST['fno']);

	$tarih = time();
	$hesapla = ($tarih - ($_POST['gunsayisi'] * 86400));

	if ($_POST['fno'] != 'tumu' )
	$hangi_forumdan = "hangi_forumdan='$_POST[fno]' AND";
	else $hangi_forumdan = '';


	// 		MESAJLAR S�L�N�YOR		 //

	$strSQL = "SELECT id FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$mesaj_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	$strSQL = "DELETE FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// 		CEVAPLAR S�L�N�YOR		 //

	while ($eski_mesaj = mysql_fetch_assoc($mesaj_sonuc))
	{
		$strSQL = "DELETE FROM $tablo_cevaplar
				WHERE $hangi_forumdan hangi_basliktan='$eski_mesaj[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}


    // FORUM B�LG�LER� �EK�L�YOR	//

    $strSQL = "SELECT id FROM $tablo_forumlar";
    $sonuc = mysql_query($strSQL);


    while ($forum_satir = mysql_fetch_assoc($sonuc))
    {

        //	FORUMDAK� BA�LIKLARIN SAYISI ALINIYOR	//

        $result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE hangi_forumdan='$forum_satir[id]'");
        $konu_sayi = mysql_num_rows($result);


        //	FORUMDAK� T�M MESAJLARIN SAYISI ALINIYOR	//

        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_satir[id]'");
        $cevap_sayi = mysql_num_rows($result);


        //  KONU VE CEVAP SAYISI YEN� ALANLARA G�R�L�YOR    //

        $strSQL = "UPDATE `$tablo_forumlar` SET konu_sayisi='$konu_sayi', cevap_sayisi='$cevap_sayi'
                    WHERE id='$forum_satir[id]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');
	}


	header('Location: ../hata.php?bilgi=36');
	exit();
}


elseif ( ( isset($_POST['forum_goster']) ) AND ( $_POST['forum_goster'] == 'forum_goster' ) )
{
	if ( empty($_POST['fno']) OR ($_POST['fno'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	if ( ($_POST['gunsayisi'] <= 0) OR ($_POST['gunsayisi'] > 999) )
	{
		header('Location: ../hata.php?hata=153');
		exit();
	}

	$_POST['fno'] = @zkTemizle($_POST['fno']);

	$onayal = 'onayal';
	$tarih = time();
	$hesapla = ($tarih - ($_POST['gunsayisi'] * 86400));

	if ($_POST['fno'] != 'tumu' )
	$hangi_forumdan = "hangi_forumdan='$_POST[fno]' AND";
	else $hangi_forumdan = '';


	// 	S�L�NECEK MESAJ SAYISI ALINIYOR	 //

	$strSQL = "SELECT id FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$eski_mesaj_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$mesaj_sayi = mysql_num_rows($eski_mesaj_sonuc);


	// 	S�L�NECEK MESAJ SAYISI ALINIYOR	 //

	$toplam_cevap_sayi = 0;
	while ($eski_mesaj = mysql_fetch_assoc($eski_mesaj_sonuc))
	{
		$strSQL = "SELECT id FROM $tablo_cevaplar 
				WHERE $hangi_forumdan hangi_basliktan='$eski_mesaj[id]'";
		$eski_cevap_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$cevap_sayi = mysql_num_rows($eski_cevap_sonuc);
		$toplam_cevap_sayi += $cevap_sayi;
	}
}


$sayfa_adi = 'Y�netim Forum Temizleme';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';
?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Forum Temizleme -

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

<form name="temizleme" action="temizleme.php" method="post">
<input type="hidden" name="forum_goster" value="forum_goster">

<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Forum Se�imi -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br> &nbsp; &nbsp; &nbsp; Bu sayfadan; belirtti�iniz g�n say�s� i�erisinde cevap yaz�lmayan ba�l�klar� ve cevaplar�n� silebilirsiniz. Forumu se�ip, g�n girdikten sonra gelen sayfada, ka� ba�l���n ve bunlara ba�l� ka� cevab�n silinece�i belirtilir. �sterseniz silme i�leminden bu k�s�mda vazge�ebilirsiniz.

<br><br><br>

<center>
<b>Forum Se�:</b>

<br><br>

<?php


$forum_secenek = '<select name="fno" class="formlar" size="15">
<option value="tumu">&nbsp; - T�M FORUMLAR -';


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
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


		else
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


			while ($alt_forum_satir = mysql_fetch_array($sonuca))
				$forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
		}
	}
}


echo $forum_secenek;


?>
</select>

<br><br><br><br>

<input type="text" name="gunsayisi" size="4" class="formlar" maxlength="3">
<b>&nbsp; G�nd�r cevap yaz�lmayan &nbsp;</b>
<br><br><br>
<input type="submit" value="Ba�l�klar� Bul" class="dugme">
<br><br>
</center>
	</td>
	</tr>
</table>
</form>

<?php

//	BA�LIKLARI S�L TIKLANMI�SA	//

elseif (isset($onayal)):

?>

<form name="temizleme" action="temizleme.php" method="post">
<input type="hidden" name="basliklari_sil" value="basliklari_sil">
<input type="hidden" name="fno" value="<?php if (isset($_POST['fno'])) echo $_POST['fno'] ?>">
<input type="hidden" name="gunsayisi" value="<?php if (isset($_POST['gunsayisi'])) echo $_POST['gunsayisi'] ?>">

<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Silinecek Ba�l�k ve Cevap Say�s� -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>

<?php

echo ' &nbsp; Se�mi� oldu�unuz forumda son <b>'.$_POST['gunsayisi'].'</b> g�nd�r cevap yaz�lmam��;
<br><br> &nbsp; Ba�l�k say�s�: <b>'.$mesaj_sayi.'</b>
<br> &nbsp; Ba�l�klara ba�l� cevap say�s�: <b>'.$toplam_cevap_sayi.'</b>';

?>

<br><br><br>
<p align="center">
<b>Ba�l�k ve cevaplar�n� silmek istedi�inize emin misiniz?</b>
<br><br><br>
<input type="submit" value="Evet Sil" class="dugme">

</p><br>
	</td>
	</tr>
</table>
</form>
<?php

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