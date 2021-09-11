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

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


//	K�M DE���KEN� YOKSA UYARILIYOR	//

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




//	KULLANICI ADI DENETLEN�YOR  //

$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE kullanici_adi='$_GET[kim]' LIMIT 1") or die ('<h2>ARAMA SONU�LANAMADI</h2>');

$satir_sayi = mysql_num_rows($result);

if (($satir_sayi <= 0))
{
	header('Location: hata.php?hata=46');
	exit();
}



//	�K� �LET� ARASI S�RES� DOLMAMI�SA UYARILIYOR	//	
//	oturum a�l�yor, arama zaman�na bak�l�yor  //

if ($_GET['sayfa'] <= 0)
{
	if ( isset($_SESSION['arama_tarih']) AND (($_SESSION['arama_tarih']) > ($tarih - 20)) )
	{
		header('Location: hata.php?hata=1');
		exit();
	}
}




//  KULLANICININ A�TI�I KONULAR ARANIYOR    //

if ($_GET['kip'] == 'mesaj')
{
	//	SORGU SONUCUNDAK� TOPLAM SONU� SAYISI ALINIYOR	//

	$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE silinmis='0' AND yazan='$_GET[kim]'") or die ('<h2>ARAMA SONU�LANAMADI</h2>');

	$satir_sayi = mysql_num_rows($result);

	if ($satir_sayi > 0)
	{
		//  FORUM B�LG�LER� �EK�L�YOR	//

		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY dal_no, sira";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		while ($forum_satir = mysql_fetch_array($sonuc))
			$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


		//  MESAJLAR TABLOSUNDA ARAMA YAPILIYOR //

		$strSQL = "SELECT id,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik
		FROM $tablo_mesajlar WHERE silinmis='0' AND yazan='$_GET[kim]'
		ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[sayfa],$arama_kota";

		$km_ara_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		//	ARAMA SONU� VER�RSE SON ARAMA ZAMANI OTURUMA G�R�L�YOR	//

		$_SESSION['arama_tarih'] = $tarih;
	}

	$sayfano = 37;
	$sayfa_adi = '�ye Konu Arama: '.$_GET['kim'];
}





//  KULLANICININ YAZDI�I CEVAPLAR ARANIYOR    //

elseif ($_GET['kip'] == 'cevap')
{
	//	SORGU SONUCUNDAK� TOPLAM SONU� SAYISI ALINIYOR	//

	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND cevap_yazan='$_GET[kim]'") or die ('<h2>ARAMA SONU�LANAMADI</h2>');

	$satir_sayi = mysql_num_rows($result);

	if ($satir_sayi > 0)
	{
		//  FORUM B�LG�LER� �EK�L�YOR	//

		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY dal_no, sira";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		while ($forum_satir = mysql_fetch_array($sonuc))
			$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


		//  CEVAPLAR TABLOSUNDA ARAMA YAPILIYOR //

		$strSQL = "SELECT id,hangi_forumdan,hangi_basliktan,tarih,cevap_baslik
		FROM $tablo_cevaplar WHERE silinmis='0' AND cevap_yazan='$_GET[kim]'
		ORDER BY tarih DESC LIMIT $_GET[sayfa],$arama_kota";

		$km_ara_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


		//	ARAMA SONU� VER�RSE SON ARAMA ZAMANI OTURUMA G�R�L�YOR	//

		$_SESSION['arama_tarih'] = $tarih;
	}

	$sayfano = 38;
	$sayfa_adi = '�ye Cevap Arama: '.$_GET['kim'];
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
$sonuc_yok = '<b>'.$_GET['kim'].'</b> adl� kullan�c�n�n yazd��� cevap bulunmamaktad�r !';

else $sonuc_yok = '<b>'.$_GET['kim'].'</b> adl� kullan�c�n�n a�t��� konu bulunmamaktad�r !';
$bulunan_sonuc = '';




		//      ARAMA SONU�LARI SIRALANIYOR BA�LANGI�       //




elseif ($satir_sayi > 0):

if ($_GET['kip'] == 'cevap')
$bulunan_sonuc = '<b>'.$_GET['kim'].'</b> adl� kullan�c�n�n yazd��� &nbsp;<b>'.$satir_sayi.'</b>&nbsp; adet cevap bulundu.';

else $bulunan_sonuc = '<b>'.$_GET['kim'].'</b> adl� kullan�c�n�n a�t��� &nbsp;<b>'.$satir_sayi.'</b>&nbsp; adet konu bulundu.';



//	ARAMA SONU�LARI SIRALANIYOR	//

while ($km_ara_satir = mysql_fetch_assoc($km_ara_sonuc)):

if ($_GET['kip'] == 'cevap')
{
	// cevab�n ba�l� oldu�u konunun bilgileri �ekiliyor
	$strSQL = "SELECT mesaj_baslik,cevap_sayi,goruntuleme,son_mesaj_tarihi FROM $tablo_mesajlar WHERE id='$km_ara_satir[hangi_basliktan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$konu_satir = mysql_fetch_assoc($sonuc);


	// cevab�n ka��nc� s�rada oldu�u hesaplan�yor
	$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$km_ara_satir[hangi_basliktan]' AND id < $km_ara_satir[id]") or die ('<h2>ARAMA SONU�LANAMADI</h2>');
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



//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{BASLIK_BAGLANTI}' => $baslik_baglanti,
'{FORUM_BAGLANTI}' => $forum_baglanti,
'{YAZAN}' => $_GET['kim'],
'{CEVAP_SAYI}' => $km_ara_satir['cevap_sayi'],
'{GORUNTULEME}' => $km_ara_satir['goruntuleme'],
'{TARIH}' => $sonu_tarih);



endwhile;




// 	SAYFALAR BA�LANGI� 	//

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

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
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
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
		}
	
		elseif (($sayi + 1) == (($_GET['sayfa'] / $arama_kota) + 1))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}
	
		else
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">';
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


// 	SAYFALAR B�T�� 	//


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