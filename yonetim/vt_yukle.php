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
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}



		//	VER�TABANI YEDE�� Y�KLEME KISMI - BA�I	//


//	DOSYA Y�KLEMEDE HATA OLURSA - DOSYA 2`MB. DAN B�Y�KSE	//

if ( (isset($_FILES['vtyukle']['error'])) AND ($_FILES['vtyukle']['error'] != 0) )
{
	header('Location: ../hata.php?hata=156');
	exit();
}


if ( (isset($_FILES['vtyukle']['tmp_name'])) AND
		($_FILES['vtyukle']['tmp_name'] != '') ):


//	DOSYA 2`MB. DAN B�Y�KSE	//

if ($_FILES['vtyukle']['size'] > 2097952):
	header('Location: ../hata.php?hata=157');
	exit();
endif;


$uzanti = end(explode(".", strtolower($_FILES['vtyukle']['name'])));


//	DOSYA SIKI�TIRILMI� MI BAKILIYOR	//

if ($uzanti == 'gz'):

	if(extension_loaded('zlib'))
	{
		$gzipdosya01 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
		$gzipac01 = gzread( $gzipdosya01, 3921920 );
		gzclose($gzipdosya01);

		//	�ift s�k��t�r�l�m�� olma olas�l���na kar�� tekrar a��l�yor
		$yeni_gzipdosya = fopen($_FILES['vtyukle']['tmp_name'], 'w') or die ("Dosya a��lam�yor!");
		fwrite($yeni_gzipdosya, $gzipac01);
		fclose($yeni_gzipdosya);

		$gzipdosya02 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
		$gzipac02 = gzread( $gzipdosya02, 3921920 );
		gzclose($gzipdosya02);

		$ac = $gzipac02;
	}

	else
	{
		header('Location: ../hata.php?hata=158');
		exit();
	}



//	DOSYA .SQL UZANTILI DE��LSE	//

elseif ($uzanti != 'sql'):

	header('Location: ../hata.php?hata=159');
	exit();


//	TEMP'TEK� DOSYANIN ���NDEK�LER DE���KENE AKTARILIYOR	//

else:
$dosya = @fopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya a��lam�yor!");
$boyut = @filesize($_FILES['vtyukle']['tmp_name']);
$ac = @fread( $dosya, $boyut );
endif;





// dosyadaki veriler sat�r sat�r dizi de�i�kene aktar�l�yor //
$toplam = explode(";\n\n", $ac);

// sat�r say�s� al�n�yor //
$toplam_sayi = count($toplam);

// dizideki sat�rlar d�ng�ye sokuluyor //
for ($satir=0;$satir<$toplam_sayi;$satir++)
{
	// 9 karakterden k�sa dizi elemanlar� diziden at�l�yor	//
	if (strlen($toplam[$satir]) > 9)
	{
		// yorumlar diziden at�l�yor //
		if (preg_match("/\n\n--/", $toplam[$satir]))
		{
			$yorum = explode("\n\n", $toplam[$satir]);
			$yorum_sayi = count($yorum);

			for ($satir2=0;$satir2<$yorum_sayi;$satir2++)
			{
				if ( (strlen($yorum[$satir2]) > 9) AND (!preg_match("/--/", $yorum[$satir2])) )
				// sorgu veritaban�na giriliyor //
				$strSQL = mysql_query($yorum[$satir2]) or die ('<h2>Ba�ar�s�z Sorgu<br></h2>'.mysql_error());
			}
		}

		else // sorgu veritaban�na giziliyor //
		$strSQL = mysql_query($toplam[$satir]) or die ('<h2>Ba�ar�s�z Sorgu<br></h2>'.mysql_error());
	}
}


//	VER�TABANI YEDE�� Y�KLEND� MESAJI	//

mysql_close($link);

setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);

header('Location: ../hata.php?bilgi=38');
exit();



		//	VER�TABANI YEDE�� Y�KLEME KISMI - SONU	//


		//	G�R�� SAYFASI KISMI - BA�I	//

else:
$sayfa_adi = 'Y�netim Veritaban� Geri Y�kleme';
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




<table cellspacing="1" width="77%" cellpadding="5" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
VER�TABANI YEDE�� GER� Y�KLEME
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" >
<br>

Buradan sadece forum �zerinden ald���n�z yedekleri y�kleyebilirsiniz!
<p>
&nbsp; Bir �ok sunucuda kabul edilen en b�y�k dosya boyutu 2mb. oldu�u i�in, y�klenebilecek en b�y�k yedek dosya boyutu 2mb.`d�r.
<br>B�y�k veritabanlar� i�in yedekleri tablo tablo almay� deneyin. Tek bir tablo da 2mb.`dan b�y�kse <a href="vt_yedek.php?kip=gelismis">geli�mi� yedeklemeyi</a> kullan�n.

<p>&nbsp; Gzip bi�iminde s�k��t�r�lm�� dosyalar otomatik a��l�p y�klenir.

<p>&nbsp; Veritaban� geri y�kleme i�lemi, dosya b�y�kl���ne ve sunucu yo�unlu�una g�re biraz uzun s�rebilir. Dosya sunucuya ula�t�ktan sonra 30 saniye kadar daha s�rebilir. L�tfen y�kleme bitene kadar bekleyin.

<p>&nbsp;Bir �ok sunucuda, kilitlenmeye sebep olmamas� i�in bir beti�in �al��t�r�labilece�i s�re 30 saniye ile s�n�rlanm��t�r. E�er <b>Fatal error: Maximum execution time of 30 seconds exceeded in...</b> �eklinde bir mesaj al�rsan�z, bu engele tak�ld�n�z anlam�na gelmektedir.

<p>&nbsp;2mb.`dan b�y�k veritaban� ve/veya tablo yedekleri i�in, bar�nd�rma hizmeti ald���n�z firman�n size sa�lad��� ara�lar� kullanman�z� �neririz. Ayr�ca <a href="http://mysql.navicat.com/download.html">Navicat</a> veya <a href="http://www.mysqlfront.de/download.html">MySQL-Front</a> da kullanabilirsiniz.
</p>
<br>
<br>
<center>
<form name="vtyukleme" action="vt_yukle.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="2621440">
<input class="formlar" name="vtyukle" type="file" size="30">
<br>
<br>
<br>
<input class="dugme" type="submit" value="Geri Y�kle">
</form>
</center>
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
endif;
?>