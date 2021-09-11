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


//      FORM DOLDURULDUYSA      //

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


// zararl� kodlar temizleniyor
if (isset($_POST['kulad'])) $kulad = trim($_POST['kulad']);
if (isset($_POST['adsoyad'])) $adsoyad = trim($_POST['adsoyad']);
if (isset($_POST['posta'])) $posta = trim($_POST['posta']);
if (isset($_POST['sozcukler'])) $sozcukler = trim($_POST['sozcukler']);
if (isset($_POST['cumle'])) $cumle = trim($_POST['cumle']);
if (isset($_POST['yasak_ip'])) $yasak_ip = trim($_POST['yasak_ip']);



// kullan�c� adlar� i�in, 3 karakterden az s�zc�kler at�l�yor
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


// ad soyad i�in, 3 karakterden az s�zc�kler at�l�yor
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


// e-posta i�in, 3 karakterden az s�zc�kler at�l�yor
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


// s�zc�kler i�in, 3 karakterden az s�zc�kler at�l�yor
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


// c�mle i�in, 3 karakterden az s�zc�kler at�l�yor
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


// ip adresi i�in, 3 karakterden az adresler at�l�yor
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




//  B�LG�LER VER�TABANINA G�R�L�YOR  //

$strSQL = "UPDATE $tablo_yasaklar SET deger='$kulad' where etiket='kulad' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$adsoyad' where etiket='adsoyad' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$posta' where etiket='posta' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$sozcukler' where etiket='sozcukler' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$cumle' where etiket='cumle' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');

$strSQL = "UPDATE $tablo_yasaklar SET deger='$yasak_ip' where etiket='yasak_ip' LIMIT 1";
$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z</h2>');


// g�ncellendi iletisi

header('Location: ../hata.php?bilgi=39');
exit();







//      SAYFA NORMAL G�STER�M  //

else:

$sayfa_adi = 'Y�netim Yasaklamalar Sayfas�';

include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';


//	YASAK KULLANICI ADLARI ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='kulad' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_kulad = mysql_fetch_row($yasak_sonuc);


//	YASAK POSTA ADRESLER� ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='posta' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_posta = mysql_fetch_row($yasak_sonuc);


//	YASAK AD SOYADLAR ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='adsoyad' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_adsoyad = mysql_fetch_row($yasak_sonuc);


//	SANS�RLENECEK S�ZC�KLER ADRESLER� ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='sozcukler' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_sozcukler = mysql_fetch_row($yasak_sonuc);


//	SANS�R C�MLES� ALINIYOR	//

$sorgu = "SELECT deger FROM $tablo_yasaklar WHERE etiket='cumle' LIMIT 1";
$yasak_sonuc = mysql_query($sorgu);
$yasak_cumle = mysql_fetch_row($yasak_sonuc);


//	YASAKLI IP ADRESLER� ALINIYOR	//

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
Kullan�c� Ad� Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br>
Buraya girdi�iniz kullan�c� adlar�yla kay�t yap�lmas�n� �nleyebilirsiniz.
<br>Girdi�iniz her ismi sat�r atlayarak birbirinden ay�r�n�z.
<br>�ununla ba�layan veya biten anlam�nda joker olarak y�ld�z ( * ) kullanabilirsiniz.
<br>Girilen isimler jokerle beraber en az 3 karakter olmal�d�r.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>�rnek:</b>
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


		<!--		YASAK E-POSTA ADRESLER�			-->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
E-Posta Adresi Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
Buraya girdi�iniz e-posta adresleriyle kay�t yap�lmas�n� �nleyebilirsiniz.
<br>Girdi�iniz her e-posta adresini sat�r atlayarak birbirinden ay�r�n�z.
<br>Bir alanad�ndan gelen t�m adresleri yasaklamak i�in joker olarak y�ld�z ( * ) kullanabilirsiniz.
<br>Girilen adresler jokerle beraber en az 3 karakter olmal�d�r.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>�rnek:</b>
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
Ad Soyad - L�kap Yasaklama
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<br>
Buraya girdi�iniz s�zc�klerin ad soyad - l�kap alan�nda yaz�lmas�n� engelleyebilirsiniz.
<br>Girdi�iniz her ismi sat�r atlayarak birbirinden ay�r�n�z.
<br>Yazd���n�z isimlerim ba��na sonuna joker y�ld�z ( * ) konulmu� olarak varsay�l�r, ayr�ca girmeyin.
<br>Girilen isimler jokerle beraber en az 3 karakter olmal�d�r.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="370" class="tablo_ici">
	<tr class="liste-veri">
	<td align="left" valign="top">
<br>
<b>�rnek:</b>
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


		<!--		SANS�RLENECEK S�ZC�KLER			-->


	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
Sans�rlenecek S�zc�kler
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">
<br>
Buraya girdi�iniz s�zc�klerin, belirledi�iniz yasak c�mlesi ile de�i�tirilmesini sa�layabilirsiniz.
<br>Girdi�iniz her s�zc��� sat�r atlayarak birbirinden ay�r�n�z.
<br>Yazd���n�z s�zc�klerin ba��na sonuna joker y�ld�z ( * ) konulmu� olarak varsay�l�r, ayr�ca girmeyin.
<br>Girilen s�zc�kler en az 3 karakter olmal�d�r.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="430" class="tablo_ici">
	<tr class="liste-veri">
	<td align="center" valign="top">
<br>
<b>�rnek:</b>
<br>
<br>k�f�r
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
<b>Sans�rlenen s�zc�klerin<br>yerine yaz�lacak c�mle:</b>
<br><i>BBCode kullanabilirsiniz.
<br>Bo� b�rakabilirsiniz.</i>
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
Buraya yazd���n�z ip adresleri ile sitenize girilmesini yasaklayabilirsiniz.
<br>Girdi�iniz her ip adresini sat�r atlayarak birbirinden ay�r�n�z.
<br>Girdi�iniz ip adresi bir �yeye aitse hesab�n� da engelleyin.
<br>Girilen ge�ersiz karakterli sat�rlar otomatik silinir.
<br><br><br>

<table border="0" align="center" cellspacing="0" cellpadding="0" width="430" class="tablo_ici">
	<tr class="liste-veri">
	<td align="center" valign="top">
<br>
<b>�rnek:</b>
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
<input class="dugme" type="submit" value="G�nder">
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