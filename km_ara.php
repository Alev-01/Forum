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

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


//	KÝM DEÐÝÞKENÝ YOKSA UYARILIYOR	//

if ( (empty($_GET['kim'])) OR ($_GET['kim'] == '') )
{
	header('Location: hata.php?hata=45');
	exit();
}

if ( (empty($_GET['kip'])) OR ($_GET['kip'] == '') )
{
	header('Location: hata.php?hata=45');
	exit();
}



$arama_kota = 30;
$tarih = time();
@session_start();
$_GET['kim'] = zkTemizle(trim($_GET['kim']));

if (empty($_GET['sayfa'])) $_GET['sayfa'] = 0;
else $_GET['sayfa'] = @zkTemizle($_GET['sayfa']);




//	KULLANICI ADI DENETLENÝYOR  //

$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE kullanici_adi='$_GET[kim]' LIMIT 1") or die ('<h2>ARAMA SONUÇLANAMADI</h2>');

$satir_sayi = mysql_num_rows($result);

if (($satir_sayi <= 0))
{
	header('Location: hata.php?hata=46');
	exit();
}



//	ÝKÝ ÝLETÝ ARASI SÜRESÝ DOLMAMIÞSA UYARILIYOR	//	
//	oturum açlýyor, arama zamanýna bakýlýyor  //

if ($_GET['sayfa'] <= 0)
{
	if ( isset($_SESSION['arama_tarih']) AND (($_SESSION['arama_tarih']) > ($tarih - 20)) )
	{
		header('Location: hata.php?hata=1');
		exit();
	}
}




//  KULLANICININ AÇTIÐI KONULAR ARANIYOR    //

if ($_GET['kip'] == 'mesaj')
{
	//	SORGU SONUCUNDAKÝ TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE silinmis='0' AND yazan='$_GET[kim]'") or die ('<h2>ARAMA SONUÇLANAMADI</h2>');

	$satir_sayi = mysql_num_rows($result);

	if ($satir_sayi > 0)
	{
		//  FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//

		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY dal_no, sira";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		while ($forum_satir = mysql_fetch_array($sonuc))
			$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


		//  MESAJLAR TABLOSUNDA ARAMA YAPILIYOR //

		$strSQL = "SELECT id,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik
		FROM $tablo_mesajlar WHERE silinmis='0' AND yazan='$_GET[kim]'
		ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[sayfa],$arama_kota";

		$km_ara_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		//	ARAMA SONUÇ VERÝRSE SON ARAMA ZAMANI OTURUMA GÝRÝLÝYOR	//

		$_SESSION['arama_tarih'] = $tarih;
	}

	$sayfano = 37;
	$sayfa_adi = 'Üye Konu Arama: '.$_GET['kim'];
}





//  KULLANICININ YAZDIÐI CEVAPLAR ARANIYOR    //

elseif ($_GET['kip'] == 'cevap')
{
	//	SORGU SONUCUNDAKÝ TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND cevap_yazan='$_GET[kim]'") or die ('<h2>ARAMA SONUÇLANAMADI</h2>');

	$satir_sayi = mysql_num_rows($result);

	if ($satir_sayi > 0)
	{
		//  FORUM BÝLGÝLERÝ ÇEKÝLÝYOR	//

		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY dal_no, sira";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		while ($forum_satir = mysql_fetch_array($sonuc))
			$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


		//  CEVAPLAR TABLOSUNDA ARAMA YAPILIYOR //

		$strSQL = "SELECT id,hangi_forumdan,hangi_basliktan,tarih,cevap_baslik
		FROM $tablo_cevaplar WHERE silinmis='0' AND cevap_yazan='$_GET[kim]'
		ORDER BY tarih DESC LIMIT $_GET[sayfa],$arama_kota";

		$km_ara_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


		//	ARAMA SONUÇ VERÝRSE SON ARAMA ZAMANI OTURUMA GÝRÝLÝYOR	//

		$_SESSION['arama_tarih'] = $tarih;
	}

	$sayfano = 38;
	$sayfa_adi = 'Üye Cevap Arama: '.$_GET['kim'];
}



else
{
	header('Location: hata.php?hata=45');
	exit();
}



$toplam_sayfa = ($satir_sayi / $arama_kota);
settype($toplam_sayfa,'integer');
if (($satir_sayi % $arama_kota) != 0) $toplam_sayfa++;
if (empty($satir_sayi)) $satir_sayi = 0;


include 'baslik.php';


if ($satir_sayi <= 0):


if ($_GET['kip'] == 'cevap')
$sonuc_yok = '<b>'.$_GET['kim'].'</b> adlý kullanýcýnýn yazdýðý cevap bulunmamaktadýr !';

else $sonuc_yok = '<b>'.$_GET['kim'].'</b> adlý kullanýcýnýn açtýðý konu bulunmamaktadýr !';
$bulunan_sonuc = '';




		//      ARAMA SONUÇLARI SIRALANIYOR BAÞLANGIÇ       //




elseif ($satir_sayi > 0):

if ($_GET['kip'] == 'cevap')
$bulunan_sonuc = '<b>'.$_GET['kim'].'</b> adlý kullanýcýnýn yazdýðý &nbsp;<b>'.$satir_sayi.'</b>&nbsp; adet cevap bulundu.';

else $bulunan_sonuc = '<b>'.$_GET['kim'].'</b> adlý kullanýcýnýn açtýðý &nbsp;<b>'.$satir_sayi.'</b>&nbsp; adet konu bulundu.';



//	ARAMA SONUÇLARI SIRALANIYOR	//

while ($km_ara_satir = mysql_fetch_assoc($km_ara_sonuc)):

if ($_GET['kip'] == 'cevap')
{
	// cevabýn baðlý olduðu konunun bilgileri çekiliyor
	$strSQL = "SELECT mesaj_baslik,cevap_sayi,goruntuleme,son_mesaj_tarihi FROM $tablo_mesajlar WHERE id='$km_ara_satir[hangi_basliktan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$konu_satir = mysql_fetch_assoc($sonuc);


	// cevabýn kaçýncý sýrada olduðu hesaplanýyor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$km_ara_satir[hangi_basliktan]' AND id < $km_ara_satir[id]") or die ('<h2>ARAMA SONUÇLANAMADI</h2>');
	$cavabin_sirasi = mysql_num_rows($result);

	$sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
	settype($sayfaya_git,'integer');
	$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

	if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
	else $sayfaya_git = '';


	$km_ara_satir['cevap_sayi'] = $konu_satir['cevap_sayi'];
	$km_ara_satir['goruntuleme'] = $konu_satir['goruntuleme'];

	$baslik_baglanti = '<a href="konu.php?k='.$km_ara_satir['hangi_basliktan'].$sayfaya_git.'#c'.$km_ara_satir['id'].'">'.$konu_satir['mesaj_baslik'].' &raquo; '.$km_ara_satir['cevap_baslik'].'</a>';

	$sonu_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $km_ara_satir['tarih']);
}


else
{
	$baslik_baglanti = '<a href="konu.php?k='.$km_ara_satir['id'].'">'.$km_ara_satir['mesaj_baslik'].'</a>';

	$sonu_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $km_ara_satir['son_mesaj_tarihi']);
}

$forum_baglanti = '<a href="forum.php?f='.$km_ara_satir['hangi_forumdan'].'">'.$tumforum_satir[$km_ara_satir['hangi_forumdan']].'</a>';



//	veriler tema motoruna yollanýyor	//

$tekli1[] = array('{BASLIK_BAGLANTI}' => $baslik_baglanti,
'{FORUM_BAGLANTI}' => $forum_baglanti,
'{YAZAN}' => $_GET['kim'],
'{CEVAP_SAYI}' => $km_ara_satir['cevap_sayi'],
'{GORUNTULEME}' => $km_ara_satir['goruntuleme'],
'{TARIH}' => $sonu_tarih);



endwhile;




// 	SAYFALAR BAÞLANGIÇ 	//

$sayfalama = '';

if ($satir_sayi > $arama_kota): 

$sayfalama = '<p>
<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tbody>
	<tr>
	<td class="forum_baslik">
Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
	</td>';


if ($_GET['sayfa'] != 0)
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
	$sayfalama .= '&nbsp;<a href="km_ara.php?kip='.$_GET['kip'].'&amp;kim='.$_GET['kim'].'&amp;sayfa=0">&laquo;ilk</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="önceki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="km_ara.php?kip='.$_GET['kip'].'&amp;kim='.$_GET['kim'].'&amp;sayfa='.($_GET['sayfa'] - $arama_kota).'">&lt;</a>&nbsp;</td>';
}


for ($sayi=0,$sayfa_sinir=$_GET['sayfa']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['sayfa'] / $arama_kota) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['sayfa'] + 8)) break;
		if (($sayi == 0) and ($_GET['sayfa'] == 0))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="Þu an bulunduðunuz sayfa">';
			$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
		}
	
		elseif (($sayi + 1) == (($_GET['sayfa'] / $arama_kota) + 1))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="Þu an bulunduðunuz sayfa">';
			$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}
	
		else
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaralý sayfaya git">';
			$sayfalama .= '&nbsp;<a href="km_ara.php?kip='.$_GET['kip'].'&amp;kim='.$_GET['kim'].'&amp;sayfa='.($sayi * $arama_kota).'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}


if ($_GET['sayfa'] < ($satir_sayi - $arama_kota))
{	
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="km_ara.php?kip='.$_GET['kip'].'&amp;kim='.$_GET['kim'].'&amp;sayfa='.($_GET['sayfa'] + $arama_kota).'">&gt;</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	$sayfalama .= '&nbsp;<a href="km_ara.php?kip='.$_GET['kip'].'&amp;kim='.$_GET['kim'].'&amp;sayfa='.(($toplam_sayfa - 1) * $arama_kota).'">son&raquo;</a>&nbsp;</td>';

}
$sayfalama .= '</tr>
	</tbody>
</table>';


// 	SAYFALAR BÝTÝÞ 	//


endif;
endif;




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/km_ara.html');


if (isset($tekli1))
{
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('{BULUNAN_SONUC}' => $bulunan_sonuc,
		'{SAYFALAMA}' => $sayfalama), true);
	$ornek1->tekli_dongu('1',$tekli1);
}

else
{
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('{SONUC_YOK}' => $sonuc_yok), true);
}


$ornek1->dongusuz(array('{BULUNAN_SONUC}' => $bulunan_sonuc));
if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>