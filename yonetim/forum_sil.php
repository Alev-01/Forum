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


//	ZARARLI KODLAR TEM�ZLEN�YOR	//

if (isset($_POST['fno'])) $_POST['fno'] = @zkTemizle($_POST['fno']);
if (isset($_POST['dalno'])) $_POST['dalno'] = @zkTemizle($_POST['dalno']);
if (isset($_POST['forumlar'])) $_POST['forumlar'] = @zkTemizle($_POST['forumlar']);
if (isset($_POST['dallar'])) $_POST['dallar'] = @zkTemizle($_POST['dallar']);
if (isset($_POST['dalatasi_no'])) $_POST['dalatasi_no'] = @zkTemizle($_POST['dalatasi_no']);



		//		FORUM DALI ��LEMLER�		//

if ( (isset($_POST['dalno'])) AND ($_POST['dalno'] != '') )
{
	// i�lem yap�lan dal�n, s�ra numaras� al�n�yor
	$strSQL = "SELECT id,sira FROM $tablo_dallar WHERE id='$_POST[dalno]'";
	$sonuc_silinen = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$silinen_dal = mysql_fetch_assoc($sonuc_silinen);


	// i�lem yap�lan dal�n, forumlar� al�n�yor
	$strSQL = "SELECT id FROM $tablo_forumlar WHERE dal_no='$_POST[dalno]'";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	if (!empty($_POST['sil']))
	{
		while ($fno = mysql_fetch_assoc($sonuc2))
		{
			$strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_forumdan='$fno[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			$strSQL = "DELETE FROM $tablo_mesajlar WHERE hangi_forumdan='$fno[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			$strSQL = "DELETE FROM $tablo_forumlar WHERE id='$fno[id]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}


		$strSQL = "DELETE FROM $tablo_dallar WHERE id='$_POST[dalno]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		// silinen dal�n alt�ndaki dallar�n s�ra say�lar� de�i�tiriliyor
		$strSQL = "SELECT id FROM $tablo_dallar WHERE sira > '$silinen_dal[sira]'";
		$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		while ($dal_sira = mysql_fetch_assoc($sonuc_sira))
		{
			$strSQL = "UPDATE $tablo_dallar SET sira=sira - 1 WHERE id='$dal_sira[id]' LIMIT 1";
			$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		}


		header('Location: ../hata.php?bilgi=27');
		exit();
	}


	elseif ( (!empty($_POST['tasi'])) AND (!empty($_POST['dallar'])) )
	{
		//	forum dal�n�n en alttaki forumunun sira numaras� al�n�yor
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dallar]' AND alt_forum='0' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		$enalt = mysql_fetch_assoc($sonuc);

		if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;


		while ($fno = mysql_fetch_assoc($sonuc2))
		{
			$enalt['sira']++;
			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dallar]', sira='$enalt[sira]' WHERE id='$fno[id]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
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



		//		FORUM ��LEMLER�		//

elseif ( (isset($_POST['fno'])) AND ($_POST['fno'] != '') )
{
	if (!empty($_POST['sil']))
	{
		//	silinen forumun, �st - alt forum durumu ve s�ra numaras� al�n�yor
		$strSQL = "SELECT id,dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc_silinen = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		$silinen_forum = mysql_fetch_assoc($sonuc_silinen);


		// silinen forumun, alt forumlar� varsa uyar� veriliyor
		$strSQL = "SELECT id FROM $tablo_forumlar WHERE alt_forum='$silinen_forum[id]' LIMIT 1";
		$sonuc = mysql_query($strSQL);

		if (mysql_num_rows($sonuc))
		{
			header('Location: ../hata.php?hata=39');
			exit();
		}



		// cevaplar�, konular� ve forum siliniyor
		$strSQL = "DELETE FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "DELETE FROM $tablo_mesajlar WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "DELETE FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



		// silinen �st forum ise
		if ($silinen_forum['alt_forum'] == '0')
		{
			// silinen forumun alt�ndaki �st forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$silinen_forum[dal_no]' AND alt_forum='0' AND sira > '$silinen_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}


		// silinen alt forum ise
		else
		{
			// silinen forumun alt�ndaki alt forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$silinen_forum[alt_forum]' AND sira > '$silinen_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}


		header('Location: ../hata.php?bilgi=29');
		exit();
	}




	elseif ( (!empty($_POST['tasi'])) AND (!empty($_POST['forumlar'])) )
	{
		$strSQL = "UPDATE $tablo_cevaplar SET hangi_forumdan='$_POST[forumlar]' WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_mesajlar SET hangi_forumdan='$_POST[forumlar]' WHERE hangi_forumdan='$_POST[fno]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		// ta��nan forumun konu say�s� hesaplan�yor
        $result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE hangi_forumdan='$_POST[forumlar]'");
        $konu_sayi = mysql_num_rows($result);


        // ta��nan forumun cevap say�s� hesaplan�yor
        $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$_POST[forumlar]'");
        $cevap_sayi = mysql_num_rows($result);


        // ta��nan forumun konu ve cevap say�s� giriliyor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi='$konu_sayi',cevap_sayisi='$cevap_sayi'
                    WHERE id='$_POST[forumlar]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


        // mesajlar� silinen forumun konu ve cevap say�s� s�f�rlan�yor
        $strSQL = "UPDATE $tablo_forumlar SET konu_sayisi='0',cevap_sayisi='0'
                    WHERE id='$_POST[fno]' LIMIT 1";
        $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		header('Location: ../hata.php?bilgi=30');
		exit();
	}




	elseif ( (!empty($_POST['dalatasi'])) AND (!empty($_POST['dalatasi_no'])) )
	{
		//	se�ilen forumun �st - alt forum durumana bak�l�yor
		$strSQL = "SELECT id,dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
		$sonuc_tasinan = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		$tasinan_forum = mysql_fetch_assoc($sonuc_tasinan);



		// se�ilen forumun �st forum ise
		if ($tasinan_forum['alt_forum'] == '0')
		{
			//	forum dal�n�n en alttaki forumunun sira numaras� al�n�yor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalatasi_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$enalt = mysql_fetch_assoc($sonuc);


			if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]', sira='$enalt[sira]', alt_forum=0 WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// ta��nan forumun alt�ndaki �st forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$tasinan_forum[dal_no]' AND alt_forum='0' AND sira > '$tasinan_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}


			// alt forumlar�na bak�l�yor
			$strSQL = "SELECT id FROM $tablo_forumlar WHERE alt_forum='$_POST[fno]'";
			$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// alt forumlar� varsa bunlar�nda dal numaralar� de�i�tirliyor
			while ($alt_forum = mysql_fetch_assoc($sonuc2))
			{
				$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]' WHERE id='$alt_forum[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}



		// se�ilen forumun alt forum ise, �st forum yaparak ta��n�yor
		else
		{
			//	forum dal�n�n en alttaki forumunun sira numaras� al�n�yor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalatasi_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$enalt = mysql_fetch_assoc($sonuc);


			if ( (!isset($enalt['sira'])) OR ($enalt['sira'] == '') OR ($enalt['sira'] == '0') ) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET dal_no='$_POST[dalatasi_no]', sira='$enalt[sira]', alt_forum=0 WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// ta��nan forumun alt�ndaki alt forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$tasinan_forum[alt_forum]' AND sira > '$tasinan_forum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
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



//	SAYFAYA DO�RUDAN ER���L�YOR �SE UYARILIYOR	//

if ( (empty($_GET['kip'])) )
{
	header('Location: ../hata.php?hata=138');
	exit();
}

$sayfa_adi = 'Y�netim Forum Silme / Ta��ma';
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
Forum Silme / Ta��ma
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
&nbsp; �nceki sayfadan se�ti�iniz forum dal� alt�ndaki forumlar�, buradan se�ti�iniz ba�ka bir forum dal�na ta��yabilir veya ta��madan silebilirsiniz.

<p>&nbsp; Ta��ma veya silme i�lemlerinde, forumdal� alt�ndaki; forumlar, alt forumlar, konular ve cevaplar� i�lem g�r�r.

<br><br><center><b>Yapaca��n�z i�lem i�in bir daha onay istenmeyecektir.
<br>L�tfen iyice emin olduktan sonra i�lem yap�n�z.</b><br><br><br>
<select name="dallar" class="formlar">
<option value="" selected="selected"> &nbsp; - Ta��yaca��n�z forum dal�n� se�iniz - &nbsp; ';


	//	FORUM DALLARI B�LG�LER� �EK�L�YOR	//

	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
		echo '<option value="'.$dallar_satir['id'].'">'.$dallar_satir['ana_forum_baslik'];
}


elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'forum_sil') )
{
	echo '<input name="fno" type="hidden" value="'.$_GET['fno'].'">
&nbsp; �nceki sayfadan se�ti�iniz forumu, buradan se�ece�iniz ba�ka bir forum dal� alt�na ta��yabilirsiniz.
<br><br>
<center><b>Yapaca��n�z i�lem i�in bir daha onay istenmeyecektir.
<br>L�tfen iyice emin olduktan sonra i�lem yap�n�z.</b>
<br><br><br>
Bu forum dal�na ta��: &nbsp;
<select name="dalatasi_no" class="formlar">
<option value="" selected="selected"> &nbsp; - Ta��yaca��n�z forum dal�n� se�iniz - &nbsp; ';


	//	FORUM DALLARI B�LG�LER� �EK�L�YOR	//

	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	while ($dallar_satir2 = mysql_fetch_array($dallar_sonuc))
		echo '<option value="'.$dallar_satir2['id'].'">'.$dallar_satir2['ana_forum_baslik'];


	echo '</select>
<p><input class="dugme" name="dalatasi" type="submit" value="Ta��">
<br><br><br>

<hr class="cizgi_renk">
<br>
&nbsp; �nceki sayfadan se�ti�iniz forum alt�ndaki ba�l�klar� ve cevaplar�n�, buradan se�ti�iniz ba�ka bir foruma ta��yabilir veya ta��madan silebilirsiniz.
<br><br>
<b>Yapaca��n�z i�lem i�in bir daha onay istenmeyecektir.
<br>L�tfen iyice emin olduktan sonra i�lem yap�n�z.</b>
<br><br>
<br><br><b>��eri�ini bu foruma ta��:</b>

<br><br>
<select name="forumlar" class="formlar" size="15">
<option value="" selected="selected"> &nbsp; - Ta��yaca��n�z forumu se�iniz - &nbsp; ';

$forum_secenek = '';


	$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
	$sonuc3 = mysql_query($strSQL);


	while ($dallar_satir = mysql_fetch_array($sonuc3))
	{
		$forum_secenek .= '<option value="">['.$dallar_satir['ana_forum_baslik'].']';


		//	FORUM B�LG�LER� �EK�L�YOR	//
		$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar where alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
		$forum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		while ($forum_satir = mysql_fetch_array($forum_sonuc))
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

}


?>

</select>
<br><br><br>
<input class="dugme" name="tasi" type="submit" value="Buraya Ta��">
&nbsp; &nbsp;
<input class="dugme" name="sil" type="submit" value="Ta��madan Sil">
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