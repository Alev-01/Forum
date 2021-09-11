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


//	KULLANICI B�LG�LER� �EK�L�YOR	//

$_COOKIE['kullanici_kimlik'] =  @zkTemizle($_COOKIE['kullanici_kimlik']);


$strSQL = "SELECT id,son_ileti,son_giris FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$kullanici_kim = mysql_fetch_assoc($sonuc);

$arama_kota = 30;
$tarih = time();

if (empty($_GET['sayfa'])) $_GET['sayfa'] = 0;
else $_GET['sayfa'] = @zkTemizle($_GET['sayfa']);



//	SAYFA SE��M�NE G�RE SORGU HAZIRLANIYOR	//

if ((isset($_GET['kip'])) AND ($_GET['kip'] == 'bugun'))
{
	// bug�n�n ilk saati
	$bugun = mktime(0,0,0,date('m'),date('d'),date('Y'));

	$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE silinmis='0' AND son_mesaj_tarihi > '$bugun'") or die ('<h2>ARAMA SONU�LANAMADI</h2>');
	$satir_sayi = mysql_num_rows($result);

	$strSQL = "SELECT id,yazan,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik,yazan
	FROM $tablo_mesajlar WHERE silinmis='0' AND son_mesaj_tarihi > '$bugun'
	ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[sayfa],$arama_kota";

	$m_arama_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// Kipe g�re de�i�en veriler
	$sayfa_adi = 'Bug�n Yaz�lan iletiler';
	$kip_ek = 'kip=bugun&amp;';
	$sonuc_bilgi = 'Forumda bug�n b�rak�lan <b>'.$satir_sayi.'</b> ileti bulunmaktad�r.
<br>Hen�z okunmayanlar <b>kal�n</b> yaz�lm��t�r.';
}



elseif ((isset($_GET['kip'])) AND ($_GET['kip'] == 'cevapsiz'))
{
	$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE cevap_sayi='0' AND silinmis='0' AND kilitli='0'") or die ('<h2>ARAMA SONU�LANAMADI</h2>');
	$satir_sayi = mysql_num_rows($result);

	$strSQL = "SELECT id,yazan,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik,yazan
	FROM $tablo_mesajlar WHERE cevap_sayi='0' AND silinmis='0' AND kilitli='0'
	ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[sayfa],$arama_kota";

	$m_arama_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// Kipe g�re de�i�en veriler
	$sayfa_adi = 'Cevaps�z Konular';
	$kip_ek = 'kip=cevapsiz&amp;';
	$sonuc_bilgi = 'Forumda cevaps�z <b>'.$satir_sayi.'</b> konu bulunmaktad�r.
<br>Hen�z okunmayanlar <b>kal�n</b> yaz�lm��t�r.';
}



else
{
	$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE silinmis='0' AND son_mesaj_tarihi > '$kullanici_kim[son_giris]'") or die ('<h2>ARAMA SONU�LANAMADI</h2>');
	$satir_sayi = mysql_num_rows($result);

	$strSQL = "SELECT id,yazan,hangi_forumdan,son_mesaj_tarihi,cevap_sayi,goruntuleme,mesaj_baslik,yazan
	FROM $tablo_mesajlar WHERE silinmis='0' AND son_mesaj_tarihi > '$kullanici_kim[son_giris]'
	ORDER BY son_mesaj_tarihi DESC LIMIT $_GET[sayfa],$arama_kota";

	$m_arama_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// Kipe g�re de�i�en veriler
	$sayfa_adi = 'Yeni iletiler';
	$kip_ek = '';
	$sonuc_bilgi = 'Son geli�inizden sonra b�rak�lan <b>'.$satir_sayi.'</b> ileti bulunmaktad�r.
<br>Hen�z okunmayanlar <b>kal�n</b> yaz�lm��t�r.';
}



$toplam_sayfa = ($satir_sayi / $arama_kota);
settype($toplam_sayfa,'integer');
if (($satir_sayi % $arama_kota) != 0) $toplam_sayfa++;
if (empty($satir_sayi)) $satir_sayi = 0;



//	BA�LIK DAH�L ED�L�YOR	//

$sayfano = 34;
include 'baslik.php';




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/ymesaj.html');




        //      ARAMA SONU�LARI SIRALANIYOR BA�LANGI�       //



if ($satir_sayi <= 0):


//	veriler tema motoruna yollan�yor	//

if ((isset($_GET['kip'])) AND ($_GET['kip'] == 'bugun'))
	$yeni_mesaj_yok = 'Bug�n b�rak�lan ileti yok.';
elseif ((isset($_GET['kip'])) AND ($_GET['kip'] == 'cevapsiz'))
	$yeni_mesaj_yok = 'Cevaps�z konu yok.';
else $yeni_mesaj_yok = 'Son geli�inizden sonra b�rak�lan ileti yok.';

$sonuc_bilgi = '';

$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('{YENI_MESAJ_YOK}' => $yeni_mesaj_yok), true);



elseif ($satir_sayi > 0):

// ALT FORUM B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY dal_no, sira";
$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

while ($forum_satir = mysql_fetch_array($sonuc3))
	$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];

$yeni_mesaj_yok = '';



while ($m_arama_satir = mysql_fetch_assoc($m_arama_sonuc)):

$konu_baslik = '<a href="konu.php?k='.$m_arama_satir['id'].'">';



//  OKUNMAMI� MESAJLARI KALIN YAZDIR  //

if ($m_arama_satir['son_mesaj_tarihi'] < $kullanici_kim['son_giris'])
	$konu_baslik .= $m_arama_satir['mesaj_baslik'].'</a>';

elseif (isset($_COOKIE['kfk_okundu']))
{
	$cerez_dizi = explode('_', $_COOKIE['kfk_okundu']);

	foreach ($cerez_dizi as $cerez_parcala)
	{
		$okunan_kno = substr($cerez_parcala, 11);
		$okunan_dizi[$okunan_kno] = substr($cerez_parcala, 0, 10);
	}

	if ( (empty($okunan_dizi[$m_arama_satir['id']])) OR ($m_arama_satir['son_mesaj_tarihi'] > $okunan_dizi[$m_arama_satir['id']]) )
		$konu_baslik .= '<b>'.$m_arama_satir['mesaj_baslik'].'</b></a>';

	else $konu_baslik .= $m_arama_satir['mesaj_baslik'].'</a>';
}

else $konu_baslik .= '<b>'.$m_arama_satir['mesaj_baslik'].'</b></a>';



$forum_baslik = '<a href="forum.php?f='.$m_arama_satir['hangi_forumdan'].'">'.$tumforum_satir[$m_arama_satir['hangi_forumdan']].'</a>';

$yazan = '<a href="profil.php?kim='.$m_arama_satir['yazan'].'">'.$m_arama_satir['yazan'].'</a>';

$sonmesaj_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $m_arama_satir['son_mesaj_tarihi']);

$sonmesaj_baglanti = '';



//	CEVAP YOKSA MESAJ TAR�H�N� YAZ	//

if ($m_arama_satir['cevap_sayi'] == 0)
$sonmesaj_baglanti .= '<a href="profil.php?kim='.$m_arama_satir['yazan'].'">'.$m_arama_satir['yazan'].'</a>&nbsp;<a href="konu.php?k='.$m_arama_satir['id'].'" style="text-decoration: none">&nbsp;<img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';


//	CEVAP VARSA SON MESAJ B�LG�LER� �EK�L�YOR	//

else
{
	$strSQL = "SELECT id,cevap_yazan FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$m_arama_satir[id]' ORDER BY tarih DESC LIMIT 1";
	$sonuc2 = mysql_query($strSQL);
	$son_mesaj = mysql_fetch_assoc($sonuc2);
	$sonmesaj_baglanti .= '<a href="profil.php?kim='.$son_mesaj['cevap_yazan'].'">'.$son_mesaj['cevap_yazan'].'</a>';


	//  BA�LIK �OK SAYFALI �SE SON SAYFAYA G�T  //

	if ($m_arama_satir['cevap_sayi'] > $ayarlar['ksyfkota'])
	{
		$sayfaya_git = (($m_arama_satir['cevap_sayi']-1) / $ayarlar['ksyfkota']);
		settype($sayfaya_git,'integer');
		$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

		$sonmesaj_baglanti .= '&nbsp;<a href="konu.php?k='.$m_arama_satir['id'].'&amp;ks='.$sayfaya_git.'#c'.$son_mesaj['id'].'" style="text-decoration: none">';
	}

	else $sonmesaj_baglanti .= '&nbsp;<a href="konu.php?k='.$m_arama_satir['id'].'#c'.$son_mesaj['id'].'" style="text-decoration: none">';

	$sonmesaj_baglanti .= '&nbsp;<img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';
}




//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{SONUC_SAYISI}' => $satir_sayi,
'{KONU_BASLIK}' => $konu_baslik,
'{FORUM_BASLIK}' => $forum_baslik,
'{YAZAN}' => $yazan,
'{CEVAP}' => NumaraBicim($m_arama_satir['cevap_sayi']),
'{GOSTERIM}' => NumaraBicim($m_arama_satir['goruntuleme']),
'{SONMESAJ_TARIH}' => $sonmesaj_tarih,
'{SONMESAJ_BAGLANTI}' => $sonmesaj_baglanti);



endwhile;






				//	SAYFALAMA BA�LANGI�	//


$sayfalama = '';

if ($satir_sayi > $arama_kota):
$sayfalama .= '<p>
<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tbody>
	<tr>
	<td class="forum_baslik">
Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
	</td>
	';


if ($_GET['sayfa'] != 0)
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
	$sayfalama .= '&nbsp;<a href="ymesaj.php?'.$kip_ek.'sayfa=0">&laquo;ilk</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="ymesaj.php?'.$kip_ek.'sayfa='.($_GET['sayfa'] - $arama_kota).'">&lt;</a>&nbsp;</td>';
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
			$sayfalama .= '&nbsp;<a href="ymesaj.php?'.$kip_ek.'sayfa='.($sayi * $arama_kota).'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}


if ($_GET['sayfa'] < ($satir_sayi - $arama_kota))
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="ymesaj.php?'.$kip_ek.'sayfa='.($_GET['sayfa'] + $arama_kota).'">&gt;</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	$sayfalama .= '&nbsp;<a href="ymesaj.php?'.$kip_ek.'sayfa='.(($toplam_sayfa - 1) * $arama_kota).'">son&raquo;</a>&nbsp;</td>';
}

$sayfalama .= '</tr>
	</tbody>
</table>
';

endif;




				//	SAYFALAMA B�T��		//



//	veriler tema motoruna yollan�yor	//

$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('{SAYFALAMA}' => $sayfalama), true);

$ornek1->tekli_dongu('1',$tekli1);


endif;



//	TEMA UYGULANIYOR	//

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => $sayfa_adi,
'{SONUC_BILGI}' => $sonuc_bilgi));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>