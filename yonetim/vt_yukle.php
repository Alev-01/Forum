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
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}



		//	VERÝTABANI YEDEÐÝ YÜKLEME KISMI - BAÞI	//


//	DOSYA YÜKLEMEDE HATA OLURSA - DOSYA 2`MB. DAN BÜYÜKSE	//

if ( (isset($_FILES['vtyukle']['error'])) AND ($_FILES['vtyukle']['error'] != 0) )
{
	header('Location: ../hata.php?hata=156');
	exit();
}


if ( (isset($_FILES['vtyukle']['tmp_name'])) AND
		($_FILES['vtyukle']['tmp_name'] != '') ):


//	DOSYA 2`MB. DAN BÜYÜKSE	//

if ($_FILES['vtyukle']['size'] > 2097952):
	header('Location: ../hata.php?hata=157');
	exit();
endif;


$uzanti = end(explode(".", strtolower($_FILES['vtyukle']['name'])));


//	DOSYA SIKIÞTIRILMIÞ MI BAKILIYOR	//

if ($uzanti == 'gz'):

	if(extension_loaded('zlib'))
	{
		$gzipdosya01 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
		$gzipac01 = gzread( $gzipdosya01, 3921920 );
		gzclose($gzipdosya01);

		//	çift sýkýþtýrýlýmýþ olma olasýlýðýna karþý tekrar açýlýyor
		$yeni_gzipdosya = fopen($_FILES['vtyukle']['tmp_name'], 'w') or die ("Dosya açýlamýyor!");
		fwrite($yeni_gzipdosya, $gzipac01);
		fclose($yeni_gzipdosya);

		$gzipdosya02 = gzopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
		$gzipac02 = gzread( $gzipdosya02, 3921920 );
		gzclose($gzipdosya02);

		$ac = $gzipac02;
	}

	else
	{
		header('Location: ../hata.php?hata=158');
		exit();
	}



//	DOSYA .SQL UZANTILI DEÐÝLSE	//

elseif ($uzanti != 'sql'):

	header('Location: ../hata.php?hata=159');
	exit();


//	TEMP'TEKÝ DOSYANIN ÝÇÝNDEKÝLER DEÐÝÞKENE AKTARILIYOR	//

else:
$dosya = @fopen($_FILES['vtyukle']['tmp_name'], 'r') or die ("Dosya açýlamýyor!");
$boyut = @filesize($_FILES['vtyukle']['tmp_name']);
$ac = @fread( $dosya, $boyut );
endif;





// dosyadaki veriler satýr satýr dizi deðiþkene aktarýlýyor //
$toplam = explode(";\n\n", $ac);

// satýr sayýsý alýnýyor //
$toplam_sayi = count($toplam);

// dizideki satýrlar döngüye sokuluyor //
for ($satir=0;$satir<$toplam_sayi;$satir++)
{
	// 9 karakterden kýsa dizi elemanlarý diziden atýlýyor	//
	if (strlen($toplam[$satir]) > 9)
	{
		// yorumlar diziden atýlýyor //
		if (preg_match("/\n\n--/", $toplam[$satir]))
		{
			$yorum = explode("\n\n", $toplam[$satir]);
			$yorum_sayi = count($yorum);

			for ($satir2=0;$satir2<$yorum_sayi;$satir2++)
			{
				if ( (strlen($yorum[$satir2]) > 9) AND (!preg_match("/--/", $yorum[$satir2])) )
				// sorgu veritabanýna giriliyor //
				$strSQL = mysql_query($yorum[$satir2]) or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());
			}
		}

		else // sorgu veritabanýna giziliyor //
		$strSQL = mysql_query($toplam[$satir]) or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());
	}
}


//	VERÝTABANI YEDEÐÝ YÜKLENDÝ MESAJI	//

mysql_close($link);

setcookie('kullanici_kimlik', '', 0, $ayarlar['f_dizin']);
setcookie('yonetim_kimlik', '', 0, $ayarlar['f_dizin']);

header('Location: ../hata.php?bilgi=38');
exit();



		//	VERÝTABANI YEDEÐÝ YÜKLEME KISMI - SONU	//


		//	GÝRÝÞ SAYFASI KISMI - BAÞI	//

else:
$sayfa_adi = 'Yönetim Veritabaný Geri Yükleme';
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
VERÝTABANI YEDEÐÝ GERÝ YÜKLEME
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" >
<br>

Buradan sadece forum üzerinden aldýðýnýz yedekleri yükleyebilirsiniz!
<p>
&nbsp; Bir çok sunucuda kabul edilen en büyük dosya boyutu 2mb. olduðu için, yüklenebilecek en büyük yedek dosya boyutu 2mb.`dýr.
<br>Büyük veritabanlarý için yedekleri tablo tablo almayý deneyin. Tek bir tablo da 2mb.`dan büyükse <a href="vt_yedek.php?kip=gelismis">geliþmiþ yedeklemeyi</a> kullanýn.

<p>&nbsp; Gzip biçiminde sýkýþtýrýlmýþ dosyalar otomatik açýlýp yüklenir.

<p>&nbsp; Veritabaný geri yükleme iþlemi, dosya büyüklüðüne ve sunucu yoðunluðuna göre biraz uzun sürebilir. Dosya sunucuya ulaþtýktan sonra 30 saniye kadar daha sürebilir. Lütfen yükleme bitene kadar bekleyin.

<p>&nbsp;Bir çok sunucuda, kilitlenmeye sebep olmamasý için bir betiðin çalýþtýrýlabileceði süre 30 saniye ile sýnýrlanmýþtýr. Eðer <b>Fatal error: Maximum execution time of 30 seconds exceeded in...</b> þeklinde bir mesaj alýrsanýz, bu engele takýldýnýz anlamýna gelmektedir.

<p>&nbsp;2mb.`dan büyük veritabaný ve/veya tablo yedekleri için, barýndýrma hizmeti aldýðýnýz firmanýn size saðladýðý araçlarý kullanmanýzý öneririz. Ayrýca <a href="http://mysql.navicat.com/download.html">Navicat</a> veya <a href="http://www.mysqlfront.de/download.html">MySQL-Front</a> da kullanabilirsiniz.
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
<input class="dugme" type="submit" value="Geri Yükle">
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