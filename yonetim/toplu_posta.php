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

$sayfa_adi = 'Y�netim Toplu E-Posta G�nderimi';

include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';

?>

	<tr>
	<td class="baslik" align="center" valign="bottom">

<script type="text/javascript">
<!-- //
function denetle()
{
	var dogruMu = true;
	if (document.eposta_form.adim.value.length < 1)
	{
		dogruMu = false; 
		alert("G�NDER�M ADIMI KISMI BO� BIRAKILAMAZ !");
	}

	else if (document.eposta_form.eposta_baslik.value.length < 3)
	{
		dogruMu = false; 
		alert("YAZDI�INIZ BA�LIK 3 KARAKTERDEN UZUN OLMALIDIR !");
	}

	else if (document.eposta_form.eposta_icerik.value.length < 3)
	{
		dogruMu = false; 
		alert("YAZDI�INIZ �LET� 3 KARAKTERDEN UZUN OLMALIDIR !");
	}
	else;
	return dogruMu;
}
//  -->
</script>

<br>
Toplu E-Posta G�nderimi
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



<?php

include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html';



		//  TOPLU E-POSTA G�NDER TIKLANMI�SA    -   BA�I    //


if ( (isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') ):


echo '
<table cellspacing="1" width="77%" cellpadding="4" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center" colspan="2">
Toplu E-Posta G�nderimi
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" colspan="2">
';



if (($_POST['eposta_baslik']=='') or ( strlen($_POST['eposta_baslik']) < 3)
	OR ( strlen($_POST['eposta_baslik']) > 60) or ($_POST['eposta_icerik']=='')
	OR ( strlen($_POST['eposta_icerik']) < 3)):

	echo '<center><br><br><font color="red"><b>
		E-posta ba�l��� en az 3, en fazla 60 karakterden olu�mal�d�r.
		<br><br>E-posta i�eri�i en az 3 karakterden olu�mal�d�r.</b></font><br><br><br>
		<b>L�tfen <a href="toplu_posta.php">geri d�n�p</a> tekrar deneyin.</b><br><br></center>';


else:


//	magic_quotes_gpc a��ksa	//

if (get_magic_quotes_gpc(1))
{
	$_POST['eposta_baslik'] = stripslashes($_POST['eposta_baslik']);
	$_POST['eposta_icerik'] = stripslashes($_POST['eposta_icerik']);
}


//  SE��LEN ALANA G�RE SORGU YAPILIYOR  //

if ( (isset($_POST['kimlere'])) AND ($_POST['kimlere'] != '') )
{
	if ($_POST['kimlere'] == 'tum') $eposta_kimlere = "";
	elseif ($_POST['kimlere'] == 'e_haric') $eposta_kimlere = "WHERE engelle='0'";
	elseif ($_POST['kimlere'] == 'ee_haric') $eposta_kimlere = "WHERE engelle='0' AND kul_etkin='1'";
	elseif ($_POST['kimlere'] == 'yonetici') $eposta_kimlere = "WHERE yetki='1'";
	elseif ($_POST['kimlere'] == 'yardimci') $eposta_kimlere = "WHERE yetki='2'";
	elseif ($_POST['kimlere'] == 'engellenmis') $eposta_kimlere = "WHERE engelle='1' AND kul_etkin='1'";
	elseif ($_POST['kimlere'] == 'etkisiz') $eposta_kimlere = "WHERE kul_etkin='0'";


	//	G�NDER�LECEK �YELER�N SAYISI ALINIYOR	//
	
	$strSQL = "SELECT posta FROM $tablo_kullanicilar $eposta_kimlere";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$satir_sayi = mysql_num_rows($sonuc);


	//  G�NDERECEK K�MSE YOKSA HATA VER�L�YOR   //

	if (empty($satir_sayi))
	{
		echo '<center><br><br><font color="red"><b>
			Se�mi� oldu�unuz grupta hi�bir �ye bulunmamaktad�r !
			</b></font><br><br></center>';
	}


	//  E-POSTA G�NDERME KISMI - BA�I   //

	else
	{
		if ( (isset($_POST['adim'])) AND (is_numeric($_POST['adim']) == false) ) $_POST['adim'] = 50;
		if ( (isset($_POST['adim'])) AND ($_POST['adim'] != '') ) $adim = $_POST['adim'];

		if ( (isset($_POST['devam'])) AND (is_numeric($_POST['devam']) == false) ) $devam = 0;
		if ( (!isset($_POST['devam'])) OR ($_POST['devam'] == '') ) $devam = 0;
		else $devam = $_POST['devam'];


		if ($satir_sayi >= $devam)
		{
			//	G�NDER�LECEK E-POSTA ADRESLER� �EK�L�YOR	//

			$strSQL = "SELECT kullanici_adi,posta FROM $tablo_kullanicilar $eposta_kimlere ORDER BY id LIMIT $devam,$adim";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//		POSTA YOLLANIYOR		//

			require('../eposta_sinif.php');

			while ($eposta_gonderilen = mysql_fetch_assoc($sonuc))
			{
				$posta_bul = array('{uye_adi}', '{uye_posta}');
				$posta_cevir = array($eposta_gonderilen['kullanici_adi'], $eposta_gonderilen['posta']);


				$mail = new eposta_yolla();

				if ($ayarlar['eposta_yontem'] == 'mail') $mail->MailKullan();
				elseif ($ayarlar['eposta_yontem'] == 'smtp') $mail->SMTPKullan();


				$mail->sunucu = $ayarlar['smtp_sunucu'];
				if ($ayarlar['smtp_kd'] == 'true') $mail->smtp_dogrulama = true;
				else $mail->smtp_dogrulama = false;
				$mail->kullanici_adi = $ayarlar['smtp_kullanici'];
				$mail->sifre = $ayarlar['smtp_sifre'];


				$mail->gonderen = $ayarlar['y_posta'];
				$mail->gonderen_adi = $ayarlar['title'];
				$mail->GonderilenAdres($eposta_gonderilen['posta']);
				$mail->YanitlamaAdres($ayarlar['y_posta']);

				$mail->konu = str_replace($posta_bul, $posta_cevir,$_POST['eposta_baslik']);
				$mail->icerik = str_replace($posta_bul, $posta_cevir, $_POST['eposta_icerik']);


				if (!$mail->Yolla())
				{
					echo '<br><br><center><font color="red"><b>E-posta g�nderilemedi !<br><br>Hata iletisi: ';
					echo $mail->hata_bilgi;
					echo '</b></font></center><br>';
					exit();
				}
				usleep(30000);
			}



			$kacdakac = ($devam / $adim) + 1;

			$asama = $satir_sayi / $adim;
			settype($asama,'integer');
			if (($satir_sayi % $adim) != 0) $asama++;



			if ($satir_sayi <= ($devam + $adim))
			{
				echo '<br><br><center><b>
				E-POSTALARINIZ YOLLANMI�TIR...
				</b></center><br><br>';
			}


			else
			{
				echo '

					<form action="toplu_posta.php" method="post" name="eposta_form2">
					<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">
					<input type="hidden" name="adim" value="'.$adim.'">
					<input type="hidden" name="devam" value="'.($devam + $adim).'">
					<input type="hidden" name="kimlere" value="'.$_POST['kimlere'].'">
					<input type="hidden" name="eposta_baslik" value="'.$_POST['eposta_baslik'].'">
					<input type="hidden" name="eposta_icerik" value="'.$_POST['eposta_icerik'].'">

					<br><br>

					<p><b>G�nderilecek toplam e-posta say�s�: </b>'.$satir_sayi.'
					<p><b>G�nderim Ad�m�: </b>'.$adim.'
					<p><b>G�nderim A�amas�: <font color="red">
					'.$kacdakac.' / '.$asama.'</font></b>
					<br><br><br>
					<center>3 saniye bekleyin ya da "Devam &gt;&gt;" d��mesini t�klay�n.
					<br><br><br>
					<input class="dugme" type="submit" value="Devam &gt;&gt;">
					</center></form><br>
					<script type="text/javascript">
					<!-- //
						setTimeout("document.eposta_form2.submit()",3000);
					//-->
					</script>';
			}
		}

		else
		{
			echo '<br><br><center><b>
			E-POSTALARINIZ YOLLANMI�TIR...
			</b></center><br><br>';
		}
	}


	//  E-POSTA G�NDERME KISMI - SONU   //


}


//  SE��M ALANINDA HATA VARSA  //

else
{
	echo '<center><br><br><font color="red"><b>
	Se�mi� oldu�unuz grupta hi�bir �ye bulunmamaktad�r !
	</b></font><br><br></center>';
}




endif; // form dolu - bo�


echo '
	</td>
	</tr>
</table>
';



		//  TOPLU E-POSTA G�NDER TIKLANMI�SA    -   SONU    //



else:

?>

<form action="toplu_posta.php" method="post" onsubmit="return denetle()" name="eposta_form">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">

<table cellspacing="1" width="77%" cellpadding="4" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center" colspan="2">
Toplu E-Posta G�nderimi
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" colspan="2">

<br>
 &nbsp; &nbsp; Bu sayfadan �yelerinize toplu E-Posta g�nderebilirsiniz.
Sadece y�neticilere, sadece forum yard�mc�lar�na ya da t�m �yelere ayr� ayr� da yollayabilirsiniz.

<br><br><br>
Varsay�lan "G�nderim Ad�m�" 50 dir. Bu �zellik, �ok fazla �yeniz varsa sunucuya y�k yapmadan ad�m ad�m e-posta yollamaya yaramaktad�r.
<p>�sterseniz bir seferde yollanan e-posta say�s�n� artt�rabilirsiniz, ama fazla y�kseltmeniz �nerilmez.
<br><br>

E-Posta ba�l�k ve i�erik k�s�mlar�nda; g�nderilen �yenin ad� i�in {uye_adi}, e-posta adresi i�in de {uye_posta} kullanarak e-postalar� �zelle�tirebilirsiniz.
<br><br>

	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-etiket" valign="middle" height="40" width="170">
G�nderilecek Ki�iler :
	</td>

	<td align="left" class="liste-veri">
<select class="formlar" name="kimlere">
<option value="tum">T�m �yeler</option>
<option value="e_haric">Engellenmi� Olanlar Hari� T�m �yeler</option>
<option value="ee_haric">Engellenmi� ve Etkin Olmayanlar Hari� T�m �yeler</option>
<option value="yonetici">Sadece Forum Y�neticileri</option>
<option value="yardimci">Sadece Forum Yard�mc�lar�</option>
<option value="engellenmis">Sadece Engellenmi� �yeler</option>
<option value="etkisiz">Sadece Etkin Olmayan �yeler</option>
</select>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-etiket" valign="middle" height="40">
G�nderim Ad�m� :
	</td>

	<td align="left" class="liste-veri">
<input class="formlar" type="text" name="adim" size="4" maxlength="3" value="50">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-etiket" valign="middle" height="40">
E-Posta Ba�l��� :
	</td>

	<td align="left" class="liste-veri">
<input class="formlar" type="text" name="eposta_baslik" size="53" maxlength="60" value="">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-etiket" valign="top" rowspan="2">
<br>
E-Posta ��eri�i :
<br><br>
<div style="font-weight: normal">
<font size="1">
&nbsp;HTML <b>kapal�</b><br>
&nbsp;BBCode <b>kapal�</b>
<br><br><br>
&nbsp;�ye Ad�: {uye_adi}<br>
&nbsp;�ye E-Posta: {uye_posta}
<br><br><br>
&nbsp;(Sadece d�z metin)
</font>
</div>
	</td>

	<td align="left" class="liste-veri">
<br>
<textarea class="formlar" cols="50" rows="15" name="eposta_icerik">
</textarea>
<br><br>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="center" class="liste-veri" height="40" valign="middle">
<input class="dugme" name="mesaj_gonder" type="submit" value="E-Postalar� G�nder">
 &nbsp; 
<input class="dugme" type="reset" value="Temizle">
	</td>
	</tr>
</table>
</form>


<?php endif; // e-posta g�nder t�klanm�� - t�klanmam�� ?>



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