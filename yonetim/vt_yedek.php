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


$sayfa_adi = 'Y�netim Veritaban� Yedekleme';
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



<script type="text/javascript">
<!-- //
function secim(islem,isim)
{
	var forum_isaretli = true;
	var portal_isaretli = true;


	// tablo t�klamalar�
	if (islem == 'tek')
	{
		// t�klanan i�aretli ise di�erlerine bak�l�yor
		if (isim.checked == true)
		{
			// t�m forum i�in
			for (i=3; i < 17; i++)
			{
				if (document.yedekle.elements[i].checked != true)
					forum_isaretli = false;
			}

			// t�m portal i�in
			for (i=18; i < document.yedekle.length; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
				{
					if (document.yedekle.elements[i].checked != true)
					{
						//document.yedekle.elements[14].checked = false;
						portal_isaretli = false;
					}
				}
			}

			// i�aretli ise malum se�enekler i�aretleniyor
			if (forum_isaretli == true) document.yedekle.elements[2].checked = true;
			if (portal_isaretli == true) document.yedekle.elements[17].checked = true;
			if ( (forum_isaretli == true) && (portal_isaretli == true) ) document.yedekle.elements[1].checked = true;
		}


		// t�klanan i�aretli de�ilse t�m tablolar�n i�aretini kald�r
		else
		{
			if (isim.value < 16) document.yedekle.elements[2].checked = false;
			if (isim.value > 49) document.yedekle.elements[17].checked = false;
			document.yedekle.elements[1].checked = false;
		}
	}


	// t�m forum tablolar� t�klan�nca
	else if (islem == 'forum')
	{
		if (document.yedekle.elements[2].checked == true)
		{
			for (i=3; i < 17; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = true;
			}
		}

		else if (document.yedekle.elements[2].checked == false)
		{
			for (i=3; i < 17; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = false;
			}
		}
		secim('tek',isim);
	}


	// t�m portal tablolar� t�klan�nca
	else if (islem == 'portal')
	{
		if (document.yedekle.elements[17].checked == true)
		{
			for (i=18; i < document.yedekle.length; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = true;
			}
		}

		else if (document.yedekle.elements[17].checked == false)
		{
			for (i=18; i < document.yedekle.length; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = false;
			}
		}
		secim('tek',isim);
	}


	// t�m tablolar t�klan�nca
	else if (islem == 'hepsi')
	{
		if (document.yedekle.elements[1].checked == true)
		{
			for (i=2; i < document.yedekle.length; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = true;
			}
		}

		else if (document.yedekle.elements[1].checked == false)
		{
			for (i=2; i < document.yedekle.length; i++)
			{
				if (document.yedekle.elements[i].name != 'gzip')
					document.yedekle.elements[i].checked = false;
			}
		}
	}
}
//  -->
</script>





<table cellspacing="1" width="77%" cellpadding="5" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center">
VER�TABANI YEDEKLEME
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">




<?php

//      GEL��M�� K�P�   -   BA�I    //

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'gelismis') ):


//  PAR�A HESAPLAMA KISMI   -   BA�I    //

if ( (isset($_GET['parca'])) AND ($_GET['parca'] == 'hesapla') ):


	//  HANG� TABLONUN YEDEKLENECE��NE BAKILIYOR    // 


	if ($_GET['tablo'][0] == '2') $yedeklenecek_tablo = $tablo_ayarlar;
	elseif ($_GET['tablo'][0] == '3') $yedeklenecek_tablo = $tablo_cevaplar;
	elseif ($_GET['tablo'][0] == '4') $yedeklenecek_tablo = $tablo_dallar;
	elseif ($_GET['tablo'][0] == '5') $yedeklenecek_tablo = $tablo_duyurular;
	elseif ($_GET['tablo'][0] == '6') $yedeklenecek_tablo = $tablo_forumlar;
	elseif ($_GET['tablo'][0] == '7') $yedeklenecek_tablo = $tablo_kullanicilar;
	elseif ($_GET['tablo'][0] == '8') $yedeklenecek_tablo = $tablo_mesajlar;
	elseif ($_GET['tablo'][0] == '9') $yedeklenecek_tablo = $tablo_oturumlar;
	elseif ($_GET['tablo'][0] == '10') $yedeklenecek_tablo = $tablo_ozel_ileti;
	elseif ($_GET['tablo'][0] == '11') $yedeklenecek_tablo = $tablo_ozel_izinler;
	elseif ($_GET['tablo'][0] == '12') $yedeklenecek_tablo = $tablo_yasaklar;
	elseif ($_GET['tablo'][0] == '13') $yedeklenecek_tablo = $tablo_eklentiler;
	elseif ($_GET['tablo'][0] == '14') $yedeklenecek_tablo = $tablo_gruplar;
	elseif ($_GET['tablo'][0] == '15') $yedeklenecek_tablo = $tablo_yuklemeler;


	//	TABLODAK� SATIR SAYISI ALINIYOR 	//

	$sorgu = mysql_query("SHOW TABLE STATUS LIKE '$yedeklenecek_tablo'")
		or die ('<h2>Ba�ar�s�z Sorgu<br></h2>'.mysql_error());
	$satir_sayisi = mysql_fetch_assoc($sorgu);


	$asama = $satir_sayisi['Rows'] / $_GET['adim'];
	settype($asama,'integer');
	if (($satir_sayisi['Rows'] % $_GET['adim']) != 0) $asama++;


echo '
<br>
<b>&nbsp; Se�ilen tablodaki girdi say�s�:</b> '.$satir_sayisi['Rows'].
'<br><b>&nbsp; Par�a say�s�:</b> '.$asama;


//  PAR�A HESAPLAMA KISMI   -   SONU    //

//  B�R�NC�DEN SONRAK� A�AMALAR -   BA�I    //


elseif ( (isset($_GET['toplamp'])) AND ($_GET['toplamp'] != '') ):

$_GET['devam']+=$_GET['adim'];

$parca = ($_GET['devam'] / $_GET['adim'])+1;

$asama = $_GET['toplamp'];

echo '
<br>
<b>&nbsp; Yedekleme A�amas�: <font color="red">'.$parca.' / '.$asama.'</font></b>';




else:

endif;



if ( (isset($_GET['yedekle'])) AND ($_GET['yedekle'] == 'yedek_al') ):

$parca = ($_GET['devam'] / $_GET['adim'])+1;

echo'
<center>
<br><br><br>
<form name="yedekle" action="vt_yedek_yap.php" method="post">
<input name="toplamp" type="hidden" value="'.$asama.'">
<input name="kip" type="hidden" value="gelismis">
<input name="yedekle" type="hidden" value="yedek_al">
<input name="tablo[]" type="hidden" value="'.$_GET['tablo'][0].'">
<input name="devam" type="hidden" value="'.$_GET['devam'].'">
<input name="adim" type="hidden" value="'.$_GET['adim'].'">
<input name="gzip" type="hidden" value="'.$_GET['gzip'].'">';


// son a�amaya kadar bu k�s�m

if ($asama >= ($parca+1))

echo '
<input class="dugme" type="submit" value="'.$parca.'. Par�ay� indir">
</form>

<br><br><hr><br>
<b>'.$parca.'. par�ay� indirdikten sonra Devam� t�klay�n.</b>
<br><br><br>

<form name="yedekle2" action="vt_yedek.php" method="get">
<input name="toplamp" type="hidden" value="'.$asama.'">
<input name="kip" type="hidden" value="gelismis">
<input name="yedekle" type="hidden" value="yedek_al">
<input name="tablo[]" type="hidden" value="'.$_GET['tablo'][0].'">
<input name="devam" type="hidden" value="'.$_GET['devam'].'">
<input name="adim" type="hidden" value="'.$_GET['adim'].'">
<input name="gzip" type="hidden" value="'.$_GET['gzip'].'">
<input class="dugme" type="submit" value="Devam &gt;&gt;">
</form>
</center>
';


// tek par�a ise bu k�s�m

elseif ($asama == 1)

echo '
<b>Tablo tek par�ada yedeklenebiliyor, alttan indirebilirsiniz.</b>

<br><br><br>

<input class="dugme" type="submit" value="T�m�n� indir">
</form>
<br>
<br>
</center>';


// son a�amada bu k�s�m

else

echo '
<input class="dugme" type="submit" value="Son Par�ay� indir">
</form>
<br>
<b>Son par�ay� da indirdikten sonra se�ti�iniz tablonun<br>yede�i tamamlanm�� olacak.</b>
<br><br><br><br>
Ba�a d�nmek i�in <a href="vt_yedek.php?kip=gelismis"><b>t�klay�n</b></a>
<br><br>
</center>';


//  B�R�NC�DEN SONRAK� A�AMALAR -   SONU    //


else:

//  GEL��M�� K�P G�R�� SAYFASI -   BA�I    //

?>

<p align="center">
<b>2mb.`dan b�y�k veritaban� yedekleri i�in bu sayfay� kullan�n.</b>
</p><br>

&nbsp; &nbsp; Bu sayfa; toplam boyutu 2-3 mb. ge�en veritaban� yedekleme ve y�kleme i�lemleri i�in haz�rlanm��t�r. Yine tablolar� buradan tek tek yedekleyeceksiniz, ama �ok b�y�k tablolar par�alara ayr�larak uygun boyutlara getirilecek.

<p>&nbsp; &nbsp; Varsay�lan par�alama boyutu 1000 girdi �eklindedir. �rne�in, forumunuzda 3 bin konu bulundu�unu varsayal�m. Bu durumda mesajlar tablonuzda 3 bin girdi var demektir ve varsay�lan 1000 ad�m� kulland���n�zda bu mesajlar tablosu 3 yedek dosyas� halinde size sunulacakt�r.


<p>&nbsp; &nbsp; Forumdaki yaz�lar�n b�y�kl���ne, sunucunuzun h�z�na ve y�kleme yapt���n�z andaki yo�unlu�a ba�l� olarak 3-5 bin girdi ad�m� bile kullan�labilir (toplam girdiye <a href="vt_yonetim.php">buradan bak�n</a>). �nemli olan gzip s�k��t�rl�m�� yedek dosyas�n�n boyutudur. &nbsp; 700kb. dan b�y�k dosyalar�n y�klenmesi i�lemleri yar�m kalabilir.

<p>&nbsp; &nbsp; En uygun ayar� dosya boyutuna bakarak kendiniz bulabilirsiniz, ama 1000 girdilik ad�mlar kullanman�z� �neririz. Ayr�ca gzip s�k��t�rmay� a�may� da unutmay�n.


<br><br><br><br>



<form name="yedekle" action="vt_yedek.php" method="get">
<input name="kip" type="hidden" value="gelismis">
<input name="yedekle" type="hidden" value="yedek_al">
<input name="parca" type="hidden" value="hesapla">


<table cellspacing="2" width="90%" cellpadding="0" border="0" align="center">
	<tr>
	<td class="liste-veri" align="left" valign="top">


&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="2">&nbsp;Sadece ayarlar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="3" checked="checked">&nbsp;Sadece cevaplar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="4">&nbsp;Sadece dallar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="5">&nbsp;Sadece duyurular tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="13">&nbsp;Sadece eklentiler tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="6">&nbsp;Sadece forumlar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="14">&nbsp;Sadece gruplar tablosu</label>


	</td>
	<td class="liste-veri" align="left" valign="top">


&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="7">&nbsp;Sadece kullanicilar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="8">&nbsp;Sadece mesajlar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="9">&nbsp;Sadece oturumlar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="10">&nbsp;Sadece ozel_ileti tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="11">&nbsp;Sadece ozel_izinler tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="12">&nbsp;Sadece yasaklar tablosu</label>
<p>
&nbsp; &nbsp; <label style="cursor: pointer;">
<input type="radio" name="tablo[]" value="15">&nbsp;Sadece yuklemeler tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="center" valign="top" colspan="2">

<br><br>

<b>Gzip S�k��t�r: </b> &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="0">&nbsp;Hay�r</label>
&nbsp; &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="1" checked="checked">&nbsp;Evet</label>

<br><br><br>

<?php

echo '
<input class="formlar" type="hidden" name="devam" value="0">

<b>Girdi Ad�m�:&nbsp;</b>
<input class="formlar" type="text" name="adim" size="8" value="1000" maxlength="4">&nbsp; 

<br><br><br>

<input class="dugme" type="submit" value="par�a say�s�n� hesapla">

<br><br>
	</td>
	</tr>
</table>

</form>
';





//  GEL��M�� K�P G�R�� SAYFASI -   SONU    //

endif; // devam etmeyi kapat

//  B�R�NC� A�AMA -   SONU    //

//      GEL��M�� K�P�   -   SONU    //

//      NORMAL K�P   -   BA�I    //


else:

?>



<br>
&nbsp;Buradan veritaban�n�ndaki, forum ile ilgili t�m tablolar� yedekleyebilirsiniz. Ayr�ca sunucunuz destekliyorsa (�o�unlukla destekler) dosyay� Gzip bi�iminde s�k��t�rabilirsiniz.
<br>
<br>
&nbsp; �sterseniz tablolar� tek tek de yedekleyebilirsiniz. Bu veritaban�n�z 2mb. b�y�k ise �ok i�inize yarar. ��nk� sunucular�n kabul etti�i en b�y�k dosya boyutu genellikle 2mb.`d�r.
<br>
<br>
&nbsp; 2mb.`dan b�y�k veritaban� ve/veya tablo yedekleri i�in <a href="vt_yedek.php?kip=gelismis">geli�mi� yedeklemeyi</a> kullan�n.
<br>
<br>
<p align="center"><a href="vt_yedek.php?kip=gelismis">- Geli�mi� Yedekleme -</a></p>

<br>
<br>

<form name="yedekle" action="vt_yedek_yap.php" method="post">
<input name="yedekle" type="hidden" value="yedek_al">


<table cellspacing="3" width="450" cellpadding="3" border="0" align="center">
	<tr>
	<td class="liste-veri" align="center" valign="top" colspan="3">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[0]" value="0" checked="checked" onclick="javascript:secim('hepsi',this.value)">&nbsp;T�m tablolar� yedekle&nbsp;</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top" width="197">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[1]" value="1" onclick="javascript:secim('forum',this)">&nbsp;T�m forum tablolar�</label>
	</td>

	<td rowspan="7" width="20">&nbsp;</td>

	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[2]" value="2" onclick="javascript:secim('tek',this)">&nbsp;Sadece ayarlar tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[3]" value="3" onclick="javascript:secim('tek',this)">&nbsp;Sadece cevaplar tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[4]" value="4" onclick="javascript:secim('tek',this)">&nbsp;Sadece dallar tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[5]" value="5" onclick="javascript:secim('tek',this)">&nbsp;Sadece duyurular tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[13]" value="13" onclick="javascript:secim('tek',this)">&nbsp;Sadece eklentiler tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[6]" value="6" onclick="javascript:secim('tek',this)">&nbsp;Sadece forumlar tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[14]" value="14" onclick="javascript:secim('tek',this)">&nbsp;Sadece gruplar tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[7]" value="7" onclick="javascript:secim('tek',this)">&nbsp;Sadece kullanicilar tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[8]" value="8" onclick="javascript:secim('tek',this)">&nbsp;Sadece mesajlar tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[9]" value="9" onclick="javascript:secim('tek',this)">&nbsp;Sadece oturumlar tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[10]" value="10" onclick="javascript:secim('tek',this)">&nbsp;Sadece ozel_ileti tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[11]" value="11" onclick="javascript:secim('tek',this)">&nbsp;Sadece ozel_izinler tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[12]" value="12" onclick="javascript:secim('tek',this)">&nbsp;Sadece yasaklar tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="center" valign="top" colspan="3">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[15]" value="15" onclick="javascript:secim('tek',this)">&nbsp;Sadece yuklemeler tablosu</label>
	</td>
	</tr>
</table>



<?php if ($portal_kullan == 1): ?>

<br>
<table cellspacing="3" width="450" cellpadding="3" border="0" align="center">
	<tr>
	<td class="liste-veri" align="left" valign="top" width="197">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[50]" value="50" onclick="javascript:secim('portal',this)">&nbsp;T�m portal tablolar�</label>
	</td>

	<td rowspan="8" width="20">&nbsp;</td>

	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[51]" value="51" onclick="javascript:secim('tek',this)">&nbsp;Sadece anketsecenek tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[52]" value="52" onclick="javascript:secim('tek',this)">&nbsp;Sadece anketsoru tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[53]" value="53" onclick="javascript:secim('tek',this)">&nbsp;Sadece anketyorum tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[54]" value="54" onclick="javascript:secim('tek',this)">&nbsp;Sadece portal_ayarlar tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[55]" value="55" onclick="javascript:secim('tek',this)">&nbsp;Sadece blok tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[56]" value="56" onclick="javascript:secim('tek',this)">&nbsp;Sadece galeri tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[57]" value="57" onclick="javascript:secim('tek',this)">&nbsp;Sadece galeridal tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[59]" value="59" onclick="javascript:secim('tek',this)">&nbsp;Sadece haberler tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[60]" value="60" onclick="javascript:secim('tek',this)">&nbsp;Sadece haberyorum tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[61]" value="61" onclick="javascript:secim('tek',this)">&nbsp;Sadece indir tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[62]" value="62" onclick="javascript:secim('tek',this)">&nbsp;Sadece indirkategori tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[63]" value="63" onclick="javascript:secim('tek',this)">&nbsp;Sadece indiryorum tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[64]" value="64" onclick="javascript:secim('tek',this)">&nbsp;Sadece sayfa tablosu</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[65]" value="65" onclick="javascript:secim('tek',this)">&nbsp;Sadece siteekle tablosu</label>
	</td>
	<td class="liste-veri" align="left" valign="top">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[66]" value="66" onclick="javascript:secim('tek',this)">&nbsp;Sadece siteekledal tablosu</label>
	</td>
	</tr>
</table>

<?php endif; ?>


<script type="text/javascript">
<!-- //
secim('hepsi',document.yedekle.elements[2]);
//  -->
</script>


<table cellspacing="5" width="440" cellpadding="5" border="0" align="center">
	<tr>
	<td class="liste-veri" align="center" valign="top" colspan="3">

<br><br>

<b>Gzip S�k��t�r: </b> &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="0">&nbsp;Hay�r</label>
&nbsp; &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="1" checked="checked">&nbsp;Evet</label>

<br><br><br>

<input class="dugme" type="submit" name="gonder" value="Yedekle">

	</td>
	</tr>
</table>
<br>
</form>


<?php
        //      NORMAL K�P   -   SONU    //
endif;
?>



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