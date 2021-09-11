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


$sayfa_adi = 'Yönetim Veritabaný Yedekleme';
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


	// tablo týklamalarý
	if (islem == 'tek')
	{
		// týklanan iþaretli ise diðerlerine bakýlýyor
		if (isim.checked == true)
		{
			// tüm forum için
			for (i=3; i < 17; i++)
			{
				if (document.yedekle.elements[i].checked != true)
					forum_isaretli = false;
			}

			// tüm portal için
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

			// iþaretli ise malum seçenekler iþaretleniyor
			if (forum_isaretli == true) document.yedekle.elements[2].checked = true;
			if (portal_isaretli == true) document.yedekle.elements[17].checked = true;
			if ( (forum_isaretli == true) && (portal_isaretli == true) ) document.yedekle.elements[1].checked = true;
		}


		// týklanan iþaretli deðilse tüm tablolarýn iþaretini kaldýr
		else
		{
			if (isim.value < 16) document.yedekle.elements[2].checked = false;
			if (isim.value > 49) document.yedekle.elements[17].checked = false;
			document.yedekle.elements[1].checked = false;
		}
	}


	// tüm forum tablolarý týklanýnca
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


	// tüm portal tablolarý týklanýnca
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


	// tüm tablolar týklanýnca
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
VERÝTABANI YEDEKLEME
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">




<?php

//      GELÝÞMÝÞ KÝPÝ   -   BAÞI    //

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'gelismis') ):


//  PARÇA HESAPLAMA KISMI   -   BAÞI    //

if ( (isset($_GET['parca'])) AND ($_GET['parca'] == 'hesapla') ):


	//  HANGÝ TABLONUN YEDEKLENECEÐÝNE BAKILIYOR    // 


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


	//	TABLODAKÝ SATIR SAYISI ALINIYOR 	//

	$sorgu = mysql_query("SHOW TABLE STATUS LIKE '$yedeklenecek_tablo'")
		or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());
	$satir_sayisi = mysql_fetch_assoc($sorgu);


	$asama = $satir_sayisi['Rows'] / $_GET['adim'];
	settype($asama,'integer');
	if (($satir_sayisi['Rows'] % $_GET['adim']) != 0) $asama++;


echo '
<br>
<b>&nbsp; Seçilen tablodaki girdi sayýsý:</b> '.$satir_sayisi['Rows'].
'<br><b>&nbsp; Parça sayýsý:</b> '.$asama;


//  PARÇA HESAPLAMA KISMI   -   SONU    //

//  BÝRÝNCÝDEN SONRAKÝ AÞAMALAR -   BAÞI    //


elseif ( (isset($_GET['toplamp'])) AND ($_GET['toplamp'] != '') ):

$_GET['devam']+=$_GET['adim'];

$parca = ($_GET['devam'] / $_GET['adim'])+1;

$asama = $_GET['toplamp'];

echo '
<br>
<b>&nbsp; Yedekleme Aþamasý: <font color="red">'.$parca.' / '.$asama.'</font></b>';




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


// son aþamaya kadar bu kýsým

if ($asama >= ($parca+1))

echo '
<input class="dugme" type="submit" value="'.$parca.'. Parçayý indir">
</form>

<br><br><hr><br>
<b>'.$parca.'. parçayý indirdikten sonra Devamý týklayýn.</b>
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


// tek parça ise bu kýsým

elseif ($asama == 1)

echo '
<b>Tablo tek parçada yedeklenebiliyor, alttan indirebilirsiniz.</b>

<br><br><br>

<input class="dugme" type="submit" value="Tümünü indir">
</form>
<br>
<br>
</center>';


// son aþamada bu kýsým

else

echo '
<input class="dugme" type="submit" value="Son Parçayý indir">
</form>
<br>
<b>Son parçayý da indirdikten sonra seçtiðiniz tablonun<br>yedeði tamamlanmýþ olacak.</b>
<br><br><br><br>
Baþa dönmek için <a href="vt_yedek.php?kip=gelismis"><b>týklayýn</b></a>
<br><br>
</center>';


//  BÝRÝNCÝDEN SONRAKÝ AÞAMALAR -   SONU    //


else:

//  GELÝÞMÝÞ KÝP GÝRÝÞ SAYFASI -   BAÞI    //

?>

<p align="center">
<b>2mb.`dan büyük veritabaný yedekleri için bu sayfayý kullanýn.</b>
</p><br>

&nbsp; &nbsp; Bu sayfa; toplam boyutu 2-3 mb. geçen veritabaný yedekleme ve yükleme iþlemleri için hazýrlanmýþtýr. Yine tablolarý buradan tek tek yedekleyeceksiniz, ama çok büyük tablolar parçalara ayrýlarak uygun boyutlara getirilecek.

<p>&nbsp; &nbsp; Varsayýlan parçalama boyutu 1000 girdi þeklindedir. Örneðin, forumunuzda 3 bin konu bulunduðunu varsayalým. Bu durumda mesajlar tablonuzda 3 bin girdi var demektir ve varsayýlan 1000 adýmý kullandýðýnýzda bu mesajlar tablosu 3 yedek dosyasý halinde size sunulacaktýr.


<p>&nbsp; &nbsp; Forumdaki yazýlarýn büyüklüðüne, sunucunuzun hýzýna ve yükleme yaptýðýnýz andaki yoðunluða baðlý olarak 3-5 bin girdi adýmý bile kullanýlabilir (toplam girdiye <a href="vt_yonetim.php">buradan bakýn</a>). Önemli olan gzip sýkýþtýrlýmýþ yedek dosyasýnýn boyutudur. &nbsp; 700kb. dan büyük dosyalarýn yüklenmesi iþlemleri yarým kalabilir.

<p>&nbsp; &nbsp; En uygun ayarý dosya boyutuna bakarak kendiniz bulabilirsiniz, ama 1000 girdilik adýmlar kullanmanýzý öneririz. Ayrýca gzip sýkýþtýrmayý açmayý da unutmayýn.


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

<b>Gzip Sýkýþtýr: </b> &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="0">&nbsp;Hayýr</label>
&nbsp; &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="1" checked="checked">&nbsp;Evet</label>

<br><br><br>

<?php

echo '
<input class="formlar" type="hidden" name="devam" value="0">

<b>Girdi Adýmý:&nbsp;</b>
<input class="formlar" type="text" name="adim" size="8" value="1000" maxlength="4">&nbsp; 

<br><br><br>

<input class="dugme" type="submit" value="parça sayýsýný hesapla">

<br><br>
	</td>
	</tr>
</table>

</form>
';





//  GELÝÞMÝÞ KÝP GÝRÝÞ SAYFASI -   SONU    //

endif; // devam etmeyi kapat

//  BÝRÝNCÝ AÞAMA -   SONU    //

//      GELÝÞMÝÞ KÝPÝ   -   SONU    //

//      NORMAL KÝP   -   BAÞI    //


else:

?>



<br>
&nbsp;Buradan veritabanýnýndaki, forum ile ilgili tüm tablolarý yedekleyebilirsiniz. Ayrýca sunucunuz destekliyorsa (çoðunlukla destekler) dosyayý Gzip biçiminde sýkýþtýrabilirsiniz.
<br>
<br>
&nbsp; Ýsterseniz tablolarý tek tek de yedekleyebilirsiniz. Bu veritabanýnýz 2mb. büyük ise çok iþinize yarar. Çünkü sunucularýn kabul ettiði en büyük dosya boyutu genellikle 2mb.`dýr.
<br>
<br>
&nbsp; 2mb.`dan büyük veritabaný ve/veya tablo yedekleri için <a href="vt_yedek.php?kip=gelismis">geliþmiþ yedeklemeyi</a> kullanýn.
<br>
<br>
<p align="center"><a href="vt_yedek.php?kip=gelismis">- Geliþmiþ Yedekleme -</a></p>

<br>
<br>

<form name="yedekle" action="vt_yedek_yap.php" method="post">
<input name="yedekle" type="hidden" value="yedek_al">


<table cellspacing="3" width="450" cellpadding="3" border="0" align="center">
	<tr>
	<td class="liste-veri" align="center" valign="top" colspan="3">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[0]" value="0" checked="checked" onclick="javascript:secim('hepsi',this.value)">&nbsp;Tüm tablolarý yedekle&nbsp;</label>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top" width="197">
<label style="cursor: pointer;">
<input type="checkbox" name="tablo[1]" value="1" onclick="javascript:secim('forum',this)">&nbsp;Tüm forum tablolarý</label>
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
<input type="checkbox" name="tablo[50]" value="50" onclick="javascript:secim('portal',this)">&nbsp;Tüm portal tablolarý</label>
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

<b>Gzip Sýkýþtýr: </b> &nbsp;
<label style="cursor: pointer;"><input type="radio" name="gzip" value="0">&nbsp;Hayýr</label>
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
        //      NORMAL KÝP   -   SONU    //
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