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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';

$sayfa_adi = 'Yönetim - Forum Yönetimi';
include 'yonetim_baslik.php';


// ANA FORUM DALI BÝLGÝLERÝ ÇEKÝLÝYOR //

$strSQL = "SELECT * FROM $tablo_dallar ORDER BY sira";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

// OTURUM KODU ÝÞLEMLERÝ  //

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

		<!--	SAYFA BAÞLIK		-->

	- Forum Yönetimi -
	</td>
	</tr>

	<tr>
	<td height="1" class="tablo_ici"></td>
	</tr>

	<tr>
	<td align="center" valign="top">



		<!--	FORUM DALLARI SIRALANIYOR BAÞI		-->



<?php

while ($dallar_satir = mysql_fetch_assoc($sonuc2)):

echo '
<table cellspacing="1" width="98%" cellpadding="2" border="0" align="center" class="tablo_border4">
	<tr>
	<td height="30" class="forum_baslik" align="left" colspan="2">&nbsp;'
.$dallar_satir['ana_forum_baslik'].'
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="80">
<a href="forum_duzen.php?kip=dal_duzenle&amp;dalno='.$dallar_satir['id'].'"><font color="#ffffff" style="font-size: 11px">düzenle</font></a>
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="70">
<a href="forum_sil.php?kip=dal_sil&amp;dalno='.$dallar_satir['id'].'"><font color="#ffffff" style="font-size: 11px">sil / taþý</font></a>
	</td>

	<td class="forum_baslik" align="center" bgcolor="#0099ff" width="85">
<a href="forum_duzen_yap.php?kip=dal_yukari&amp;o='.$o.'&amp;sira='.$dallar_satir['sira'].'"><font color="#ffffff" style="font-size: 11px">yukarý al</font></a><br><a href="forum_duzen_yap.php?kip=dal_asagi&amp;o='.$o.'&amp;sira='.$dallar_satir['sira'].'"><font color="#ffffff" style="font-size: 11px">aþaðý al</font></a>
	</td>
	</tr>';



		//		FORUM DALLARI SIRALANIYOR SONU		//

		//		FORUMLAR SIRALANIYOR BAÞI		//




//	ÜST FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//

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
<a href="forum_duzen.php?kip=forum_duzenle&amp;fno='.$forum_satir['id'].'">düzenle</a>
	</td>

	<td align="center" width="70" class="liste-veri">
<a href="forum_sil.php?kip=forum_sil&amp;fno='.$forum_satir['id'].'">sil / taþý</a>
	</td>

	<td align="center" width="85" class="liste-veri">
<p><a href="forum_duzen_yap.php?kip=forum_yukari&amp;dalno='.$forum_satir['dal_no'].'&amp;fno='.$forum_satir['id'].'&amp;ustforum=1&amp;o='.$o.'&amp;sira='.$forum_satir['sira'].'">yukarý al</a><p><a href="forum_duzen_yap.php?kip=forum_asagi&amp;dalno='.$forum_satir['dal_no'].'&amp;fno='.$forum_satir['id'].'&amp;ustforum=1&amp;o='.$o.'&amp;sira='.$forum_satir['sira'].'">aþaðý al</a>
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
			<p><a href="forum_duzen.php?kip=forum_duzenle&amp;fno='.$alt_forum_satir['id'].'">düzenle</a>
			</td>

			<td align="center" width="68" class="liste-veri">
			<p><a href="forum_sil.php?kip=forum_sil&amp;fno='.$alt_forum_satir['id'].'">sil / taþý</a>
			</td>

			<td align="center" width="75" class="liste-veri">
			<p><a href="forum_duzen_yap.php?kip=forum_yukari&amp;dalno='.$alt_forum_satir['dal_no'].'&amp;fno='.$alt_forum_satir['id'].'&amp;altforum='.$forum_satir['id'].'&amp;o='.$o.'&amp;sira='.$alt_forum_satir['sira'].'"><i>yukarý al</i></a><p><a href="forum_duzen_yap.php?kip=forum_asagi&amp;dalno='.$alt_forum_satir['dal_no'].'&amp;fno='.$alt_forum_satir['id'].'&amp;altforum='.$forum_satir['id'].'&amp;o='.$o.'&amp;sira='.$alt_forum_satir['sira'].'"><i>aþaðý al</i></a>
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

&nbsp; <b>Baþlýk: &nbsp; &nbsp; &nbsp;&nbsp;</b>
<input class="formlar" type="text" name="forum_baslik" size="50" value="">

<br>

&nbsp; <b>Açýklama:&nbsp;&nbsp;</b>
<input class="formlar" type="text" name="forum_bilgi"  size="50" value="">

<br>

&nbsp; <b>Alt Forum:</b>&nbsp;
<select name="alt_forum" class="formlar">
<option value="ust" selected="selected">ÜST FORUM OLUÞTUR
'.$ust_forumlar_formu.'
</select>
<br><br>

<input class="formlar" type="submit" value="Yeni forum oluþtur" name="yeni_forum">
</form>
	</td>
	<td colspan="3">&nbsp;</td>
	</tr>
</table><br>';

endwhile;


?>


		<!--	TÜM FORUMLARIN SIRALANIÞI SONU		-->



	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-veri" colspan="4" align="left">
<form action="forum_duzen_yap.php?o=<?php echo $o ?>" method="post" name="yeni_dal">
<input type="hidden" name="kip" value="yeni_dal">
&nbsp; <b>Yeni Forum Dalý Adý: </b>
<br>
&nbsp; <input class="formlar" type="text" name="ana_forum_baslik" value="" size="60">
<br>
<br>
&nbsp; <input class="formlar" type="submit" value="Oluþtur" name="yeni_dal">
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