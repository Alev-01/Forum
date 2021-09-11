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


	// 		MESAJLAR SÝLÝNÝYOR		 //

	$strSQL = "SELECT id FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$mesaj_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "DELETE FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	// 		CEVAPLAR SÝLÝNÝYOR		 //

	while ($eski_mesaj = mysql_fetch_assoc($mesaj_sonuc))
	{
		$strSQL = "DELETE FROM $tablo_cevaplar
				WHERE $hangi_forumdan hangi_basliktan='$eski_mesaj[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}


    // FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//

    $strSQL = "SELECT id FROM $tablo_forumlar";
    $sonuc = mysql_query($strSQL);


    while ($forum_satir = mysql_fetch_assoc($sonuc))
    {

        //	FORUMDAKÝ BAÞLIKLARIN SAYISI ALINIYOR	//

        $result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE hangi_forumdan='$forum_satir[id]'");
        $konu_sayi = mysql_num_rows($result);


        //	FORUMDAKÝ TÜM MESAJLARIN SAYISI ALINIYOR	//

        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_satir[id]'");
        $cevap_sayi = mysql_num_rows($result);


        //  KONU VE CEVAP SAYISI YENÝ ALANLARA GÝRÝLÝYOR    //

        $strSQL = "UPDATE `$tablo_forumlar` SET konu_sayisi='$konu_sayi', cevap_sayisi='$cevap_sayi'
                    WHERE id='$forum_satir[id]' LIMIT 1";
        $sonuc2 = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');
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


	// 	SÝLÝNECEK MESAJ SAYISI ALINIYOR	 //

	$strSQL = "SELECT id FROM $tablo_mesajlar
			WHERE $hangi_forumdan son_mesaj_tarihi < $hesapla";
	$eski_mesaj_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$mesaj_sayi = mysql_num_rows($eski_mesaj_sonuc);


	// 	SÝLÝNECEK MESAJ SAYISI ALINIYOR	 //

	$toplam_cevap_sayi = 0;
	while ($eski_mesaj = mysql_fetch_assoc($eski_mesaj_sonuc))
	{
		$strSQL = "SELECT id FROM $tablo_cevaplar 
				WHERE $hangi_forumdan hangi_basliktan='$eski_mesaj[id]'";
		$eski_cevap_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$cevap_sayi = mysql_num_rows($eski_cevap_sonuc);
		$toplam_cevap_sayi += $cevap_sayi;
	}
}


$sayfa_adi = 'Yönetim Forum Temizleme';
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




		<!--	FORUM ÝZÝNLERÝ TABLOSU BAÞI		-->

<?php

//	BAÞLIKLARI SÝL TIKLANMAMIÞSA	//

if (empty($onayal)):

?>

<form name="temizleme" action="temizleme.php" method="post">
<input type="hidden" name="forum_goster" value="forum_goster">

<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Forum Seçimi -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br> &nbsp; &nbsp; &nbsp; Bu sayfadan; belirttiðiniz gün sayýsý içerisinde cevap yazýlmayan baþlýklarý ve cevaplarýný silebilirsiniz. Forumu seçip, gün girdikten sonra gelen sayfada, kaç baþlýðýn ve bunlara baðlý kaç cevabýn silineceði belirtilir. Ýsterseniz silme iþleminden bu kýsýmda vazgeçebilirsiniz.

<br><br><br>

<center>
<b>Forum Seç:</b>

<br><br>

<?php


$forum_secenek = '<select name="fno" class="formlar" size="15">
<option value="tumu">&nbsp; - TÜM FORUMLAR -';


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
<b>&nbsp; Gündür cevap yazýlmayan &nbsp;</b>
<br><br><br>
<input type="submit" value="Baþlýklarý Bul" class="dugme">
<br><br>
</center>
	</td>
	</tr>
</table>
</form>

<?php

//	BAÞLIKLARI SÝL TIKLANMIÞSA	//

elseif (isset($onayal)):

?>

<form name="temizleme" action="temizleme.php" method="post">
<input type="hidden" name="basliklari_sil" value="basliklari_sil">
<input type="hidden" name="fno" value="<?php if (isset($_POST['fno'])) echo $_POST['fno'] ?>">
<input type="hidden" name="gunsayisi" value="<?php if (isset($_POST['gunsayisi'])) echo $_POST['gunsayisi'] ?>">

<table cellspacing="1" width="77%" cellpadding="2" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
- Silinecek Baþlýk ve Cevap Sayýsý -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>

<?php

echo ' &nbsp; Seçmiþ olduðunuz forumda son <b>'.$_POST['gunsayisi'].'</b> gündür cevap yazýlmamýþ;
<br><br> &nbsp; Baþlýk sayýsý: <b>'.$mesaj_sayi.'</b>
<br> &nbsp; Baþlýklara baðlý cevap sayýsý: <b>'.$toplam_cevap_sayi.'</b>';

?>

<br><br><br>
<p align="center">
<b>Baþlýk ve cevaplarýný silmek istediðinize emin misiniz?</b>
<br><br><br>
<input type="submit" value="Evet Sil" class="dugme">

</p><br>
	</td>
	</tr>
</table>
</form>
<?php

	//	FORM ÝZÝNLERÝ GÖRÜNTÜLENÝYOR - BÝTÝÞ	//
	
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