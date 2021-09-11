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


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

if (isset($_POST['fno'])) $_POST['fno'] = @zkTemizle($_POST['fno']);
if (isset($_POST['dalno'])) $_POST['dalno'] = @zkTemizle($_POST['dalno']);
if (isset($_POST['forumlar'])) $_POST['forumlar'] = @zkTemizle($_POST['forumlar']);
if (isset($_POST['dallar'])) $_POST['dallar'] = @zkTemizle($_POST['dallar']);
if (isset($_POST['dalatasi_no'])) $_POST['dalatasi_no'] = @zkTemizle($_POST['dalatasi_no']);



		//		FORUM DALI ÝÞLEMLERÝ		//

if ( (isset($_POST['dalno'])) AND ($_POST['dalno'] != '') )
{
	// iþlem yapýlan dalýn, sýra numarasý alýnýyor
	$strSQL = "SELECT id,sira FROM $tablo_dallar WHERE id='$_POST[dalno]'";
	$sonuc_silinen = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$silinen_dal = mysql_fetch_assoc($sonuc_silinen);


	// iþlem yapýlan dalýn, forumlarý alýnýyor
	$strSQL = "SELECT id FROM $tablo_forumlar WHERE dal_no='$_POST[dalno]'";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	if (!empty($_POST['sil']))
	{
		while ($fno = mysql_fetch_assoc($sonuc2))
		{
			$strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_forumdan='$fno[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			$strSQL = "DELETE FROM $tablo_mesajlar WHERE hangi_forumdan='$fno[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			$strSQL = "DELETE FROM $tablo_forumlar WHERE id='$fno[id]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}


		$strSQL = "DELETE FROM $tablo_dallar WHERE id='$_POST[dalno]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		// silinen dalýn altýndaki dallarýn sýra sayýlarý deðiþtiriliyor
		$strSQL = "SELECT id FROM $tablo_dallar WHERE sira > '$silinen_dal[sira]'";
		$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		while ($dal_sira = mysql_fetch_assoc($sonuc_sira))
		{
			$strSQL = "UPDATE $tablo_dallar SET sira=sira - 1 WHERE id='$dal_sira[id]' LIMIT 1";
			$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		}


		header('Location: ../hata.php?bilgi=27');
		exit();
	}


	elseif ( (!empty($_POST['tasi'])) AND (!empty($_POST['dallar'])) )
	{
		//	forum dalýnýn en alttaki forumunun sira numarasý alýnýyor
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dallar]' AND alt_forum='0' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		$enalt = mysql_fetch_assoc($sonuc);

		if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;


		while ($fno = mysql_fetch_assoc($sonuc2))
		{
			$enalt['sira']++;
			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dallar]', sira='$enalt[sira]' WHERE id='$fno[id]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}

		header('Location: ../hata.php?bilgi=28');
		exit();
	}


	else
	{
		header('Location: ../hata.php?hata=142');
		exit();
	}
}



		//		FORUM ÝÞLEMLERÝ		//

elseif ( (isset($_POST['fno'])) AND ($_POST['fno'] != '') )
{
	if (!empty($_POST['sil']))
	{
		//	silinen forumun, üst - alt forum durumu ve sýra numarasý alýnýyor
		$strSQL = "SELECT id,dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc_silinen = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		$silinen_forum = mysql_fetch_assoc($sonuc_silinen);


		// silinen forumun, alt forumlarý varsa uyarý veriliyor
		$strSQL = "SELECT id FROM $tablo_forumlar WHERE alt_forum='$silinen_forum[id]' LIMIT 1";
		$sonuc = mysql_query($strSQL);

		if (mysql_num_rows($sonuc))
		{
			header('Location: ../hata.php?hata=39');
			exit();
		}



		// cevaplarý, konularý ve forum siliniyor
		$strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "DELETE FROM $tablo_mesajlar WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "DELETE FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');



		// silinen üst forum ise
		if ($silinen_forum['alt_forum'] == '0')
		{
			// silinen forumun altýndaki üst forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$silinen_forum[dal_no]' AND alt_forum='0' AND sira > '$silinen_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}


		// silinen alt forum ise
		else
		{
			// silinen forumun altýndaki alt forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$silinen_forum[alt_forum]' AND sira > '$silinen_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}


		header('Location: ../hata.php?bilgi=29');
		exit();
	}




	elseif ( (!empty($_POST['tasi'])) AND (!empty($_POST['forumlar'])) )
	{
		$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[forumlar]' WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[forumlar]' WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		// taþýnan forumun konu sayýsý hesaplanýyor
        $result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE hangi_forumdan='$_POST[forumlar]'");
        $konu_sayi = mysql_num_rows($result);


        // taþýnan forumun cevap sayýsý hesaplanýyor
        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[forumlar]'");
        $cevap_sayi = mysql_num_rows($result);


        // taþýnan forumun konu ve cevap sayýsý giriliyor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi='$konu_sayi',cevap_sayisi='$cevap_sayi'
                    WHERE id='$_POST[forumlar]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


        // mesajlarý silinen forumun konu ve cevap sayýsý sýfýrlanýyor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi='0',cevap_sayisi='0'
                    WHERE id='$_POST[fno]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		header('Location: ../hata.php?bilgi=30');
		exit();
	}




	elseif ( (!empty($_POST['dalatasi'])) AND (!empty($_POST['dalatasi_no'])) )
	{
		//	seçilen forumun üst - alt forum durumana bakýlýyor
		$strSQL = "SELECT id,dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc_tasinan = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		$tasinan_forum = mysql_fetch_assoc($sonuc_tasinan);



		// seçilen forumun üst forum ise
		if ($tasinan_forum['alt_forum'] == '0')
		{
			//	forum dalýnýn en alttaki forumunun sira numarasý alýnýyor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalatasi_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$enalt = mysql_fetch_assoc($sonuc);


			if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]', sira='$enalt[sira]', alt_forum=0 WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// taþýnan forumun altýndaki üst forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$tasinan_forum[dal_no]' AND alt_forum='0' AND sira > '$tasinan_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}


			// alt forumlarýna bakýlýyor
			$strSQL = "SELECT id FROM $tablo_forumlar WHERE alt_forum='$_POST[fno]'";
			$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// alt forumlarý varsa bunlarýnda dal numaralarý deðiþtirliyor
			while ($alt_forum = mysql_fetch_assoc($sonuc2))
			{
				$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]' WHERE id='$alt_forum[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}



		// seçilen forumun alt forum ise, üst forum yaparak taþýnýyor
		else
		{
			//	forum dalýnýn en alttaki forumunun sira numarasý alýnýyor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalatasi_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$enalt = mysql_fetch_assoc($sonuc);


			if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]', sira='$enalt[sira]', alt_forum=0 WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// taþýnan forumun altýndaki alt forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$tasinan_forum[alt_forum]' AND sira > '$tasinan_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}


		header('Location: ../hata.php?bilgi=31');
		exit();
	}



	else
	{
		header('Location: ../hata.php?hata=143');
		exit();
	}
}



//	SAYFAYA DOÐRUDAN ERÝÞÝLÝYOR ÝSE UYARILIYOR	//

if ( (empty($_GET['kip'])) )
{
	header('Location: ../hata.php?hata=138');
	exit();
}

$sayfa_adi = 'Yönetim Forum Silme / Taþýma';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';
?>
	<tr>
	<td height="15"></td>
	</tr>
	
	<tr>
	<td align="center">

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td>



<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html' ?>





<table cellspacing="1" width="77%" cellpadding="4" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Forum Silme / Taþýma
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
<form action="forum_sil.php" name="forumdalsilme" method="post">

<?php
if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'dal_sil') )
{
	echo '<input name="dalno" type="hidden" value="'.$_GET['dalno'].'">
&nbsp; Önceki sayfadan seçtiðiniz forum dalý altýndaki forumlarý, buradan seçtiðiniz baþka bir forum dalýna taþýyabilir veya taþýmadan silebilirsiniz.

<p>&nbsp; Taþýma veya silme iþlemlerinde, forumdalý altýndaki; forumlar, alt forumlar, konular ve cevaplarý iþlem görür.

<br><br><center><b>Yapacaðýnýz iþlem için bir daha onay istenmeyecektir.
<br>Lütfen iyice emin olduktan sonra iþlem yapýnýz.</b><br><br><br>
<select name="dallar" class="formlar">
<option value="" selected="selected"> &nbsp; - Taþýyacaðýnýz forum dalýný seçiniz - &nbsp; ';


	//	FORUM DALLARI BÝLGÝLERÝ ÇEKÝLÝYOR	//

	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
		echo '<option value="'.$dallar_satir['id'].'">'.$dallar_satir['ana_forum_baslik'];
}


elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'forum_sil') )
{
	echo '<input name="fno" type="hidden" value="'.$_GET['fno'].'">
&nbsp; Önceki sayfadan seçtiðiniz forumu, buradan seçeceðiniz baþka bir forum dalý altýna taþýyabilirsiniz.
<br><br>
<center><b>Yapacaðýnýz iþlem için bir daha onay istenmeyecektir.
<br>Lütfen iyice emin olduktan sonra iþlem yapýnýz.</b>
<br><br><br>
Bu forum dalýna taþý: &nbsp;
<select name="dalatasi_no" class="formlar">
<option value="" selected="selected"> &nbsp; - Taþýyacaðýnýz forum dalýný seçiniz - &nbsp; ';


	//	FORUM DALLARI BÝLGÝLERÝ ÇEKÝLÝYOR	//

	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	while ($dallar_satir2 = mysql_fetch_array($dallar_sonuc))
		echo '<option value="'.$dallar_satir2['id'].'">'.$dallar_satir2['ana_forum_baslik'];


	echo '</select>
<p><input class="dugme" name="dalatasi" type="submit" value="Taþý">
<br><br><br>

<hr class="cizgi_renk">
<br>
&nbsp; Önceki sayfadan seçtiðiniz forum altýndaki baþlýklarý ve cevaplarýný, buradan seçtiðiniz baþka bir foruma taþýyabilir veya taþýmadan silebilirsiniz.
<br><br>
<b>Yapacaðýnýz iþlem için bir daha onay istenmeyecektir.
<br>Lütfen iyice emin olduktan sonra iþlem yapýnýz.</b>
<br><br>
<br><br><b>Ýçeriðini bu foruma taþý:</b>

<br><br>
<select name="forumlar" class="formlar" size="15">
<option value="" selected="selected"> &nbsp; - Taþýyacaðýnýz forumu seçiniz - &nbsp; ';

$forum_secenek = '';


	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$sonuc3 = mysql_query($strSQL);


	while ($dallar_satir = mysql_fetch_array($sonuc3))
	{
		$forum_secenek .= '<option value="">['.$dallar_satir['ana_forum_baslik'].']';


		//	FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//
		$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar where alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
		$forum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		while ($forum_satir = mysql_fetch_array($forum_sonuc))
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

}


?>

</select>
<br><br><br>
<input class="dugme" name="tasi" type="submit" value="Buraya Taþý">
&nbsp; &nbsp;
<input class="dugme" name="sil" type="submit" value="Taþýmadan Sil">
</center>
</form>
<br>
	</td>
	</tr>

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