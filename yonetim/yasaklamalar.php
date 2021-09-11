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


//      FORM DOLDURULDUYSA      //

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


// zararlý kodlar temizleniyor
if (isset($_POST['kulad'])) $kulad = trim($_POST['kulad']);
if (isset($_POST['adsoyad'])) $adsoyad = trim($_POST['adsoyad']);
if (isset($_POST['posta'])) $posta = trim($_POST['posta']);
if (isset($_POST['sozcukler'])) $sozcukler = trim($_POST['sozcukler']);
if (isset($_POST['cumle'])) $cumle = trim($_POST['cumle']);
if (isset($_POST['yasak_ip'])) $yasak_ip = trim($_POST['yasak_ip']);



// kullanýcý adlarý için, 3 karakterden az sözcükler atýlýyor
$yasak_bosluk = explode("\r\n", $kulad);
$kulad = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	$yasak_bosluk[$d] = trim($yasak_bosluk[$d]);

	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if ($kulad != '') $kulad .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $kulad .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}


// ad soyad için, 3 karakterden az sözcükler atýlýyor
$yasak_bosluk = explode("\r\n", $adsoyad);
$adsoyad = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if ($adsoyad != '') $adsoyad .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $adsoyad .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}


// e-posta için, 3 karakterden az sözcükler atýlýyor
$yasak_bosluk = explode("\r\n", $posta);
$posta = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	$yasak_bosluk[$d] = trim($yasak_bosluk[$d]);

	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if ($posta != '') $posta .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $posta .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}


// sözcükler için, 3 karakterden az sözcükler atýlýyor
$yasak_bosluk = explode("\r\n", $sozcukler);
$sozcukler = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if ($sozcukler != '') $sozcukler .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $sozcukler .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}


// cümle için, 3 karakterden az sözcükler atýlýyor
$yasak_bosluk = explode("\r\n", $cumle);
$cumle = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if ($cumle != '') $cumle .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $cumle .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}


// ip adresi için, 3 karakterden az adresler atýlýyor
$yasak_bosluk = explode("\r\n", $yasak_ip);
$yasak_ip = '';
$yasak_sayi = count($yasak_bosluk);

for ($d=0,$a=0; $d < $yasak_sayi; $d++)
{
	if (strlen($yasak_bosluk[$d]) >= 3)
	{
		if (!preg_match('/^[0-9 .]+$/', $yasak_bosluk[$d])) continue;
		$yasak_bosluk[$d] = trim($yasak_bosluk[$d]);
		if ($yasak_ip != '') $yasak_ip .= "\r\n".@zkTemizle($yasak_bosluk[$d]);
		else $yasak_ip .= @zkTemizle($yasak_bosluk[$d]);
		$a++;
	}
}




//  BÝLGÝLER VERÝTABANINA GÝRÝLÝYOR  //

$strSQL = "UPDATE $tablo_yasaklar SET deger='$kulad' where etiket='kulad' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$adsoyad' where etiket='adsoyad' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$posta' where etiket='posta' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$sozcukler' where etiket='sozcukler' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$cumle' where etiket='cumle' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$yasak_ip' where etiket='yasak_ip' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


// güncellendi iletisi

header('Location: ../hata.php?bilgi=39');
exit();







//      SAYFA NORMAL GÖSTERÝM  //

else:

$sayfa_adi = 'Yönetim Yasaklamalar Sayfasý';

include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';


//	YASAK KULLANICI ADLARI ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='kulad' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_kulad = mysql_fetch_row($yasak_sonuc);


//	YASAK POSTA ADRESLERÝ ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='posta' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_posta = mysql_fetch_row($yasak_sonuc);


//	YASAK AD SOYADLAR ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='adsoyad' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_adsoyad = mysql_fetch_row($yasak_sonuc);


//	SANSÜRLENECEK SÖZCÜKLER ADRESLERÝ ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='sozcukler' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_sozcukler = mysql_fetch_row($yasak_sonuc);


//	SANSÜR CÜMLESÝ ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='cumle' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_cumle = mysql_fetch_row($yasak_sonuc);


//	YASAKLI IP ADRESLERÝ ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='yasak_ip' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_ip = mysql_fetch_row($yasak_sonuc);
?>


	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- Yasaklamalar -

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




		<!--		YASAK KULLANICI ADLARI			-->


<form name="yasak" action="yasaklamalar.php" method="post">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">

<table cellspacing="1" width="77%" cellpadding="4" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Kullanýcý Adý Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br>
Buraya girdiðiniz kullanýcý adlarýyla kayýt yapýlmasýný önleyebilirsiniz.
<br>Girdiðiniz her ismi satýr atlayarak birbirinden ayýrýnýz.
<br>Þununla baþlayan veya biten anlamýnda joker olarak yýldýz ( * ) kullanabilirsiniz.
<br>Girilen isimler jokerle beraber en az 3 karakter olmalýdýr.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>Örnek:</b>
<br>
<br>Ahmet
<br>Mehmet
<br>*Veli
<br>Veli*
<br>*Veli*
	</td>
	<td align="right" valign="middle">
<textarea name="kulad" class="formlar" cols="30" rows="8">
<?php echo $yasak_kulad[0] ?>
</textarea>
	</td>
	</tr>
</table>
<br>
	</td>
	</tr>


		<!--		YASAK E-POSTA ADRESLERÝ			-->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
E-Posta Adresi Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
Buraya girdiðiniz e-posta adresleriyle kayýt yapýlmasýný önleyebilirsiniz.
<br>Girdiðiniz her e-posta adresini satýr atlayarak birbirinden ayýrýnýz.
<br>Bir alanadýndan gelen tüm adresleri yasaklamak için joker olarak yýldýz ( * ) kullanabilirsiniz.
<br>Girilen adresler jokerle beraber en az 3 karakter olmalýdýr.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>Örnek:</b>
<br>
<br>ahmet@yahoo.com
<br>mehmet@hotmail.com
<br>*@spam.com
	</td>
	<td align="right" valign="middle">
<textarea name="posta" class="formlar" cols="30" rows="8">
<?php echo $yasak_posta[0] ?>
</textarea>
	</td>
	</tr>
</table>
<br>
	</td>
	</tr>


		<!--		YASAK AD SOYADLAR       -->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Ad Soyad - Lâkap Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br>
Buraya girdiðiniz sözcüklerin ad soyad - lâkap alanýnda yazýlmasýný engelleyebilirsiniz.
<br>Girdiðiniz her ismi satýr atlayarak birbirinden ayýrýnýz.
<br>Yazdýðýnýz isimlerim baþýna sonuna joker yýldýz ( * ) konulmuþ olarak varsayýlýr, ayrýca girmeyin.
<br>Girilen isimler jokerle beraber en az 3 karakter olmalýdýr.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>Örnek:</b>
<br>
<br>Ahmet
<br>Mehmet
<br>Veli
	</td>
	<td align="right" valign="middle">
<textarea name="adsoyad" class="formlar" cols="30" rows="8">
<?php echo $yasak_adsoyad[0] ?>
</textarea>
	</td>
	</tr>
</table>
<br>
	</td>
	</tr>


		<!--		SANSÜRLENECEK SÖZCÜKLER			-->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Sansürlenecek Sözcükler
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
Buraya girdiðiniz sözcüklerin, belirlediðiniz yasak cümlesi ile deðiþtirilmesini saðlayabilirsiniz.
<br>Girdiðiniz her sözcüðü satýr atlayarak birbirinden ayýrýnýz.
<br>Yazdýðýnýz sözcüklerin baþýna sonuna joker yýldýz ( * ) konulmuþ olarak varsayýlýr, ayrýca girmeyin.
<br>Girilen sözcükler en az 3 karakter olmalýdýr.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="430" class="tablo_ici">
	<tr class="liste-veri">
	<td align="center" valign="top">
<br>
<b>Örnek:</b>
<br>
<br>küfür
<br>hack
<br>crack
	</td>
	<td align="right" valign="middle">
<textarea name="sozcukler" class="formlar" cols="30" rows="8">
<?php echo $yasak_sozcukler[0] ?>
</textarea>
	</td>
	</tr>

	<tr class="liste-veri">
	<td align="left" valign="top">
<br><br>
<b>Sansürlenen sözcüklerin<br>yerine yazýlacak cümle:</b>
<br><i>BBCode kullanabilirsiniz.
<br>Boþ býrakabilirsiniz.</i>
	</td>
	<td align="right" valign="middel">
<br><br>
<input name="cumle" class="formlar" value="<?php echo $yasak_cumle[0] ?>" size="33">
	</td>
	</tr>

</table>
<br>
	</td>
	</tr>


		<!--		YASAKLI IPLER			-->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Yasaklanan ip Adresleri
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
Buraya yazdýðýnýz ip adresleri ile sitenize girilmesini yasaklayabilirsiniz.
<br>Girdiðiniz her ip adresini satýr atlayarak birbirinden ayýrýnýz.
<br>Girdiðiniz ip adresi bir üyeye aitse hesabýný da engelleyin.
<br>Girilen geçersiz karakterli satýrlar otomatik silinir.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="430" class="tablo_ici">
	<tr class="liste-veri">
	<td align="center" valign="top">
<br>
<b>Örnek:</b>
<br>
<br>192.168.1.1
<br>127.0.0.1
	</td>
	<td align="right" valign="middle">
<textarea name="yasak_ip" class="formlar" cols="30" rows="8">
<?php echo $yasak_ip[0] ?>
</textarea>
	</td>
	</tr>

</table>
<br>
	</td>
	</tr>



	<tr class="tablo_ici">
	<td align="center" class="liste-veri">

<br>
<input class="dugme" type="submit" value="Gönder">
&nbsp; &nbsp;
<input class="dugme" type="reset" value="Temizle">
<br><br>
	</td>
	</tr>
</table>

</form>

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
endif;
?>