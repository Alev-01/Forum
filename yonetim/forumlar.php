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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';

$sayfa_adi = 'Y�netim - Forum Y�netimi';
include 'yonetim_baslik.php';


// ANA FORUM DALI B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT * FROM $tablo_dallar ORDER BY sira";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

?>


<table cellspacing="1" cellpadding="0" width="95%" border="0" align="center" class="tablo_border">
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

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tbody>
	<tr>
	<td class="baslik">

		<!--	SAYFA BA�LIK		-->

	- Forum Y�netimi -
	</td>
	</tr>

	<tr>
	<td height="1" class="tablo_ici"></td>
	</tr>

	<tr>
	<td align="center" valign="top">



		<!--	FORUM DALLARI SIRALANIYOR BA�I		-->



<?php

while ($dallar_satir = mysql_fetch_assoc($sonuc2)):

echo '
<table cellspacing="1" width="98%" cellpadding="2" border="0" align="center" class="tablo_border4">
	<tr>
	<td height="30" class="forum_baslik" align="left" colspan="2">&nbsp;'
.$dallar_satir['ana_forum_baslik'].'
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="80">
<a href="forum_duzen.php?kip=dal_duzenle&amp;dalno='.$dallar_satir['id'].'"><font color="#ffffff" style="font-size: 11px">d�zenle</font></a>
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="70">
<a href="forum_sil.php?kip=dal_sil&amp;dalno='.$dallar_satir['id'].'"><font color="#ffffff" style="font-size: 11px">sil / ta��</font></a>
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="85">
<a href="forum_duzen_yap.php?kip=dal_yukari&amp;o='.$o.'&amp;sira='.$dallar_satir['sira'].'"><font color="#ffffff" style="font-size: 11px">yukar� al</font></a><br><a href="forum_duzen_yap.php?kip=dal_asagi&amp;o='.$o.'&amp;sira='.$dallar_satir['sira'].'"><font color="#ffffff" style="font-size: 11px">a�a�� al</font></a>
	</td>
	</tr>';



		//		FORUM DALLARI SIRALANIYOR SONU		//

		//		FORUMLAR SIRALANIYOR BA�I		//




//	�ST FORUM B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT * FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
$sonuc = mysql_query($strSQL);

$ust_forumlar_formu = '';


while ($forum_satir = mysql_fetch_assoc($sonuc)):


$ust_forumlar_formu .= '<option value="'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


echo '
	<tr class="tablo_ici">
	<td width="50" align="center" class="liste-veri">';
if (empty($forum_satir['resim']))
echo '<img border="0" src="../temalar/5renkli/resimler/forum01.gif" alt="Forum Simgesi">';
else echo '<img border="0" src="../'.$forum_satir['resim'].'" alt="Forum Simgesi">';
echo'</td>

	<td align="left" class="liste-veri">
<p><a href="../forum.php?f='.$forum_satir['id'].'">'.$forum_satir['forum_baslik'].'</a><br>'.$forum_satir['forum_bilgi'].'
	</td>

	<td align="center" width="80" class="liste-veri">
<a href="forum_duzen.php?kip=forum_duzenle&amp;fno='.$forum_satir['id'].'">d�zenle</a>
	</td>

	<td align="center" width="70" class="liste-veri">
<a href="forum_sil.php?kip=forum_sil&amp;fno='.$forum_satir['id'].'">sil / ta��</a>
	</td>

	<td align="center" width="85" class="liste-veri">
<p><a href="forum_duzen_yap.php?kip=forum_yukari&amp;dalno='.$forum_satir['dal_no'].'&amp;fno='.$forum_satir['id'].'&amp;ustforum=1&amp;o='.$o.'&amp;sira='.$forum_satir['sira'].'">yukar� al</a><p><a href="forum_duzen_yap.php?kip=forum_asagi&amp;dalno='.$forum_satir['dal_no'].'&amp;fno='.$forum_satir['id'].'&amp;ustforum=1&amp;o='.$o.'&amp;sira='.$forum_satir['sira'].'">a�a�� al</a>
	</td>
	</tr>';



	//	ALT FORUMLARINA BAKILIYOR

	$strSQL = "SELECT * FROM $tablo_forumlar WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
	$sonuca = mysql_query($strSQL);


	if (mysql_num_rows($sonuca))
	{
		echo '
		<tr>
		<td colspan="5" class="tablo_ici">

		<table cellspacing="3" width="100%" cellpadding="3" border="0" align="center" class="tablo_ici">';



		while ($alt_forum_satir = mysql_fetch_array($sonuca))
		{
			echo '<tr class="tablo_ici">
			<td width="75" align="right"><img border="0" src="../temalar/'.$ayarlar['temadizini'].'/resimler/alt_forum.png" alt="Alt Forumlar">&nbsp;</td>
			<td width="50" align="center" class="liste-veri">';
			if (empty($alt_forum_satir['resim']))
			echo '<img border="0" src="../temalar/5renkli/resimler/forum01.gif" alt="Alt Forum Simgesi">';
			else echo '<img border="0" src="../'.$alt_forum_satir['resim'].'" alt="alt forum">';
			echo'</td>

			<td align="left" class="liste-veri">
			<a href="../forum.php?f='.$alt_forum_satir['id'].'">'.$alt_forum_satir['forum_baslik'].'</a><br>'.$alt_forum_satir['forum_bilgi'].'
			</td>

			<td align="center" width="76" class="liste-veri">
			<p><a href="forum_duzen.php?kip=forum_duzenle&amp;fno='.$alt_forum_satir['id'].'">d�zenle</a>
			</td>

			<td align="center" width="68" class="liste-veri">
			<p><a href="forum_sil.php?kip=forum_sil&amp;fno='.$alt_forum_satir['id'].'">sil / ta��</a>
			</td>

			<td align="center" width="75" class="liste-veri">
			<p><a href="forum_duzen_yap.php?kip=forum_yukari&amp;dalno='.$alt_forum_satir['dal_no'].'&amp;fno='.$alt_forum_satir['id'].'&amp;altforum='.$forum_satir['id'].'&amp;o='.$o.'&amp;sira='.$alt_forum_satir['sira'].'"><i>yukar� al</i></a><p><a href="forum_duzen_yap.php?kip=forum_asagi&amp;dalno='.$alt_forum_satir['dal_no'].'&amp;fno='.$alt_forum_satir['id'].'&amp;altforum='.$forum_satir['id'].'&amp;o='.$o.'&amp;sira='.$alt_forum_satir['sira'].'"><i>a�a�� al</i></a>
			</td>
			</tr>';
		}


		echo '
		</table>
		</td>
		</tr>
		';

	}


endwhile;




echo '
	<tr class="tablo_ici">
	<td width="50">&nbsp;</td>
	<td class="liste-veri" height="80" align="left" valign="middle">

<form action="forum_duzen_yap.php?o='.$o.'" method="post" name="yeni_forum">
<input type="hidden" name="kip" value="yeni_forum">
<input type="hidden" name="sira" value="sonsira">
<input type="hidden" name="dalno" value="'.$dallar_satir['id'].'">

&nbsp; <b>Ba�l�k: &nbsp; &nbsp; &nbsp;&nbsp;</b>
<input class="formlar" type="text" name="forum_baslik" size="50" value="">

<br>

&nbsp; <b>A��klama:&nbsp;&nbsp;</b>
<input class="formlar" type="text" name="forum_bilgi"  size="50" value="">

<br>

&nbsp; <b>Alt Forum:</b>&nbsp;
<select name="alt_forum" class="formlar">
<option value="ust" selected="selected">�ST FORUM OLU�TUR
'.$ust_forumlar_formu.'
</select>
<br><br>

<input class="formlar" type="submit" value="Yeni forum olu�tur" name="yeni_forum">
</form>
	</td>
	<td colspan="3">&nbsp;</td>
	</tr>
</table><br>';

endwhile;


?>


		<!--	T�M FORUMLARIN SIRALANI�I SONU		-->



	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-veri" colspan="4" align="left">
<form action="forum_duzen_yap.php?o=<?php echo $o ?>" method="post" name="yeni_dal">
<input type="hidden" name="kip" value="yeni_dal">
&nbsp; <b>Yeni Forum Dal� Ad�: </b>
<br>
&nbsp; <input class="formlar" type="text" name="ana_forum_baslik" value="" size="60">
<br>
<br>
&nbsp; <input class="formlar" type="submit" value="Olu�tur" name="yeni_dal">
</form>
	</td>

	</tr>

</table>
</td></tr></table>
	</td>
	</tr>

	<tr>
	<td width="140" height="20">
	</td>
	</tr>

</table>
</td></tr></table>
<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>