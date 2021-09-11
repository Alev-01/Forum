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
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// site kurucusu de�ilse hata ver
if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}


if (isset($_GET['mesaj_no'])) $_GET['k'] = $_GET['mesaj_no'];
if (isset($_GET['k'])) $_GET['k'] = @zkTemizle($_GET['k']);


if (is_numeric($_GET['k']) == false)
{
	header('Location: ../hata.php?hata=47');
	exit();
}

$zaman_asimi = $ayarlar['cevrimici'];
$tarih = time();


// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


// MESAJ B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT
id,hangi_forumdan,yazan,mesaj_baslik,mesaj_icerik,tarih,yazan_ip,bbcode_kullan,degistirme_sayisi,degistiren,degistirme_tarihi,degistiren_ip,kilitli,son_mesaj_tarihi,goruntuleme,silinmis,ifade
FROM $tablo_mesajlar WHERE id='$_GET[k]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$mesaj_satir = mysql_fetch_assoc($sonuc);


// KONU YOKSA HATA MESAJI, VARSA DEVAM //

if ( (empty($mesaj_satir)) OR (empty($_GET['k'])) )
{
	header('Location: ../hata.php?hata=47');
	exit();
}



// FORUM B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT forum_baslik,alt_forum FROM $tablo_forumlar
			WHERE id='$mesaj_satir[hangi_forumdan]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$forum_satir = mysql_fetch_assoc($sonuc);



//	SAYFA DE�ERLER� YOKSA SIFIR YAPILIYOR		


if (isset($_GET['sayfa'])) $_GET['ks'] = $_GET['sayfa'];
if (isset($_GET['fsayfa'])) $_GET['fs'] = $_GET['fsayfa'];

if (empty($_GET['ks'])) $_GET['ks'] = 0;
else $_GET['ks'] = @zkTemizle($_GET['ks']);
if (is_numeric($_GET['ks']) == false) $_GET['ks'] = 0;

if (empty($_GET['fs'])) $_GET['fs'] = 0;
else $_GET['fs'] = @zkTemizle($_GET['fs']);
if (is_numeric($_GET['fs']) == false) $_GET['fs'] = 0;


// MESAJ SAH�B�N�N PROF�L� �EK�L�YOR //

$strSQL = "SELECT
id,kullanici_adi,gercek_ad,resim,katilim_tarihi,mesaj_sayisi,sehir_goster,sehir,web,imza,yetki,son_hareket,gizli,engelle,hangi_sayfada,sayfano,ozel_ad 
FROM $tablo_kullanicilar WHERE kullanici_adi='$mesaj_satir[yazan]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$mesaj_sahibi = mysql_fetch_assoc($sonuc);


// CEVAP B�LG�LER� �EK�L�YOR

$strSQL = "SELECT
id,cevap_yazan,cevap_baslik,cevap_icerik,tarih,yazan_ip,bbcode_kullan,degistirme_sayisi,degistiren,degistirme_tarihi,degistiren_ip,silinmis,ifade
FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[k]' ORDER BY tarih LIMIT $_GET[ks],$ayarlar[ksyfkota]";
$cevap = mysql_query($strSQL);


// CEVAPLARIN SATIR SAYISINA BAKILIYOR //

$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_basliktan='$_GET[k]'");
$satir_sayi = mysql_num_rows($result);


// OLU�TURULACAK SAYFA SAYISI BA�LANTISI //

$toplam_sayfa = ($satir_sayi / $ayarlar['ksyfkota']);
settype($toplam_sayfa,'integer');

if (($satir_sayi % $ayarlar['ksyfkota']) != 0)
$toplam_sayfa++;


//	BA�LI�IN �LET� NUMARASI //

$ileti_no = $_GET['ks'];



$sayfa_adi = 'Y�netim Silinen �letiler - '.$forum_satir['forum_baslik'].' - '.$mesaj_satir['mesaj_baslik'];


include 'yonetim_baslik.php';





	//		SAYFA BA�LANTILARI OLU�TURULUYOR BA�I	//

$sayfalama_cikis = '';

if ($satir_sayi > $ayarlar['ksyfkota']):
$sayfalama_cikis = '<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tbody>
	<tr>
	<td class="forum_baslik">
Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
	</td>';


if ($_GET['ks'] != 0)
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">
	&nbsp;<a href="konu_silinmis.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;k='.$_GET['k'].'&amp;fs='.$_GET['fs'].'">&laquo;ilk</a>&nbsp;</td>
		
	<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">
	&nbsp;<a href="konu_silinmis.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;k='.$_GET['k'].'&amp;fs='.$_GET['fs'].'&amp;ks='.($_GET['ks'] - $ayarlar['ksyfkota']).'">&lt;</a>&nbsp;</td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['ks']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['ks'] / $ayarlar['ksyfkota']) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['ks'] + 8)) break;
		if (($sayi == 0) and ($_GET['ks'] == 0))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">
			&nbsp;<b>[1]</b>&nbsp;</td>';
		}
	
		elseif (($sayi + 1) == (($_GET['ks'] / $ayarlar['ksyfkota']) + 1))
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">
			&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}
	
		else
		{
			$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">

			&nbsp;<a href="konu_silinmis.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;k='.$_GET['k'].'&amp;fs='.$_GET['fs'].'&amp;ks='.($sayi * $ayarlar['ksyfkota']).'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}

if ($_GET['ks'] < ($satir_sayi - $ayarlar['ksyfkota']))
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">
	&nbsp;<a href="konu_silinmis.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;k='.$_GET['k'].'&amp;fs='.$_GET['fs'].'&amp;ks='.($_GET['ks'] + $ayarlar['ksyfkota']).'">&gt;</a>&nbsp;</td>

	<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">
	&nbsp;<a href="konu_silinmis.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;k='.$_GET['k'].'&amp;fs='.$_GET['fs'].'&amp;ks='.(($toplam_sayfa - 1) * $ayarlar['ksyfkota']).'">son&raquo;</a>&nbsp;</td>';
}

$sayfalama_cikis .= '</tr></tbody></table>';
endif;


	//		SAYFA BA�LANTILARI OLU�TURULUYOR SONU	//





				//		BA�LIK TABLOSU BA�I		//


if ($_GET['ks'] < 1 ):


if ($mesaj_sahibi['engelle'] != 1) $konu_acan = '<a href="../profil.php?u='.$mesaj_sahibi['id'].'">'.$mesaj_satir['yazan'].'</a>';

else $konu_acan = '<a href="../profil.php?u='.$mesaj_satir['id'].'"><s>'.$mesaj_satir['yazan'].'</s></a>';


if (!empty($mesaj_sahibi['gercek_ad']))
	$konu_acan_adi = $mesaj_sahibi['gercek_ad'];
else $konu_acan_adi = '';


if (!empty($mesaj_sahibi['ozel_ad']))
	$konu_acan_yetkisi = '<font class="ozel_ad"><u>'.$mesaj_sahibi['ozel_ad'].'</u></font>';

elseif ($mesaj_sahibi['id'] == 1) 
	$konu_acan_yetkisi = '<font class="kurucu"><u>'.$ayarlar['kurucu'].'</u></font>';

elseif ($mesaj_sahibi['yetki'] == 1)
	$konu_acan_yetkisi = '<font class="yonetici"><u>'.$ayarlar['yonetici'].'</u></font>';
	
elseif ($mesaj_sahibi['yetki'] == 2)
	$konu_acan_yetkisi = '<font class="yardimci"><u>'.$ayarlar['yardimci'].'</u></font>';

else $konu_acan_yetkisi = '';


if ($mesaj_sahibi['resim'] != '')
{
	if ( (preg_match('/^http:\/\//i', $mesaj_sahibi['resim'])) OR (preg_match('/^ftp:\/\//i', $mesaj_sahibi['resim'])))
		$konu_acan_resmi = '<img src="'.$mesaj_sahibi['resim'].'" alt="Kullan�c� Resmi">';

	else $konu_acan_resmi = '<img src="../'.$mesaj_sahibi['resim'].'" alt="Kullan�c� Resmi">';
}

elseif ($ayarlar['kul_resim'] != '')
{
	if ( (preg_match('/^http:\/\//i', $ayarlar['kul_resim'])) OR (preg_match('/^ftp:\/\//i', $ayarlar['kul_resim'])))
		$konu_acan_resmi = '<img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';

	else $konu_acan_resmi = '<img src="../'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';
}

else $konu_acan_resmi = '';


if (!empty($mesaj_sahibi['katilim_tarihi']))
	$konu_acan_kayit = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $mesaj_sahibi['katilim_tarihi']);

else $konu_acan_kayit = '';


if (!empty($mesaj_sahibi['mesaj_sayisi']))
	$konu_acan_mesajsayi = $mesaj_sahibi['mesaj_sayisi'];

	else $konu_acan_mesajsayi = '';


if ($mesaj_sahibi['sehir_goster'] == 1)
	$konu_acan_sehir = $mesaj_sahibi['sehir'];

else $konu_acan_sehir = 'Gizli';


if (empty($mesaj_sahibi['gercek_ad']))
	$konu_acan_durum = '<font color="#FF0000">�ye silinmi�</font>';

elseif ($mesaj_sahibi['engelle'] == 1)
	$konu_acan_durum = '<font color="#FF0000">�ye uzakla�t�r�lm��</font>';

elseif ($mesaj_sahibi['gizli'] == 1)
	$konu_acan_durum = '<font color="#FF0000">Gizli</font>';

elseif ( (($mesaj_sahibi['son_hareket'] + $zaman_asimi) > $tarih ) AND
		($mesaj_sahibi['sayfano'] != '-1') )
	$konu_acan_durum = '<font color="#339900">Forumda</font>';

else $konu_acan_durum = '<font color="#FF0000">Forumda De�il</font>';


$konu_acan_eposta = '<a title="Forum �zerinden e-posta g�nder" href="../eposta.php?kim='.$mesaj_sahibi['kullanici_adi'].'">E-Posta G�nder</a>';

if ($mesaj_sahibi['web'])
	$konu_acan_web = '<br><a target="_blank" href="'.$mesaj_sahibi['web'].'">Web Adresi</a>';

else $konu_acan_web = '';

$konu_acan_ozel = '<a href="../oi_yaz.php?ozel_kime='.$mesaj_sahibi['kullanici_adi'].'">�zel ileti G�nder</a>';

$konu_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $mesaj_satir['tarih']);



//	S�L VE GER� Y�KLE OLU�TURULUYOR	//

$konu_alinti_duzenle = '';

//  silinmi� iletiyi kurtarma
if ($mesaj_satir['silinmis'] == 1)
$konu_alinti_duzenle .= '<a href="silinmis.php?kurtark='.$mesaj_satir['id'].'&amp;o='.$o.'"><img '.$simge_gerial.' alt="Bu konuyu cevaplar�yla beraber geri y�kle" title="Bu konuyu cevaplar�yla beraber geri y�kle"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="silinmis.php?silk='.$mesaj_satir['id'].'&amp;o='.$o.'"><img '.$simge_sil.' alt="Bu konuyu ve cevaplar�n� kal�c� olarak sil" title="Bu konuyu ve cevaplar�n� kal�c� olarak sil" onclick="return window.confirm(\'Bu konuyu ve cevaplar�n� kal�c� olarak silmek istedi�inize emin misiniz?\')"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="ip_yonetimi.php?kip=1&amp;ip='.$mesaj_satir['yazan_ip'].'"><img '.$simge_ip.' alt="Bu konuyu a�an�n ip adresi" title="Bu konuyu a�an�n ip adresi"></a>&nbsp;&nbsp;';



	//	BA�LIK ��ER��� YAZDIRILIYOR	//
	//	VARSA �MZA VE DE���T�RME B�LG�LER� YAZDIRILIYOR	//


if ($mesaj_satir['ifade'] == 1)
	$mesaj_satir['mesaj_icerik'] = ifadeler($mesaj_satir['mesaj_icerik']);

if ( ($mesaj_satir['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
	$konu_icerik = bbcode_acik($mesaj_satir['mesaj_icerik'],$mesaj_satir['id']);

else $konu_icerik = bbcode_kapali($mesaj_satir['mesaj_icerik']);


$konu_acan_imza = '';

if ( (isset($mesaj_sahibi['imza'])) AND ($mesaj_sahibi['imza'] != '') )
{
	if ($ayarlar['bbcode'] == 1) $konu_acan_imza .= bbcode_acik(ifadeler($mesaj_sahibi['imza']),0);
	else $konu_acan_imza .= bbcode_kapali(ifadeler($mesaj_sahibi['imza']));
}


		//	�LET� DE���T�R�LME B�LG�LER�		//

$konu_degisme = '';

if ($mesaj_satir['degistirme_sayisi'] != 0):
	$konu_degisme .= '<br>__________________<p><font size="1"><i> Bu ileti en son <b>'.$mesaj_satir['degistiren'].'</b>
taraf�ndan <b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $mesaj_satir['degistirme_tarihi']).'</b> tarihinde, toplamda '.$mesaj_satir['degistirme_sayisi'].' kez de�i�tirilmi�tir.</i></font>&nbsp;<a href="ip_yonetimi.php?kip=1&amp;ip='.$mesaj_satir['degistiren_ip'].'"><img '.$simge_ip.' alt="Bu konuyu de�i�tirenin ip adresi" title="Bu konuyu de�i�tirenin ip adresi"></a>';
endif;


//	veriler tema motoruna yollan�yor	//

$kosul1 = array('{KONU_ANAME}' => '<a name="c0"></a>',
'{KONU_BASLIK2}' => $mesaj_satir['mesaj_baslik'],
'{GOSTERIM}' => NumaraBicim($mesaj_satir['goruntuleme']),
'{KONU_ACAN}' => $konu_acan,
'{KONU_ACAN_ADI}' => $konu_acan_adi,
'{KONU_ACAN_YETKISI}' => $konu_acan_yetkisi,
'{KONU_ACAN_RESMI}' => $konu_acan_resmi,
'{KONU_ACAN_KAYIT}' => $konu_acan_kayit,
'{KONU_ACAN_MESAJSAYI}' => NumaraBicim($konu_acan_mesajsayi),
'{KONU_ACAN_SEHIR}' => $konu_acan_sehir,
'{KONU_ACAN_DURUM}' => $konu_acan_durum,
'{KONU_ACAN_EPOSTA}' => $konu_acan_eposta,
'{KONU_ACAN_WEB}' => $konu_acan_web,
'{KONU_ACAN_OZEL}' => $konu_acan_ozel,
'{KONU_TARIHI}' => $konu_tarihi,
'{KONU_ALINTI_DUZENLE}' => $konu_alinti_duzenle,
'{KONU_ICERIK}' => $konu_icerik,
'{KONU_ACAN_IMZA}' => $konu_acan_imza,
'{KONU_DEGISTIRME}' => $konu_degisme);


endif;




						//	BA�LIK TABLOSU SONU	//



						//	CEVAPLAR SIRALANIYOR	//



//	SADECE BA�LI�IN CEVAPLARI VARSA WHILE D�NG�S�NE G�R�L�YOR	//
if (isset($satir_sayi)):
while ($cevap_satir = mysql_fetch_assoc($cevap)):

$strSQL = "SELECT id,kullanici_adi,gercek_ad,resim,katilim_tarihi,mesaj_sayisi,sehir_goster,sehir,web,imza,yetki,son_hareket,gizli,engelle,hangi_sayfada,sayfano,ozel_ad 
FROM $tablo_kullanicilar WHERE kullanici_adi='$cevap_satir[cevap_yazan]' LIMIT 1";
$sonuc = mysql_query($strSQL);
$cevap_sahibi = mysql_fetch_assoc($sonuc);



		//	CEVAP TABLOLARI	BA�I	//


$cevap_aname = '<a name="c'.$cevap_satir['id'].'"></a>';

$ileti_no++;


if ($cevap_sahibi['engelle'] != 1) $cevap_yazan = '<a href="../profil.php?u='.$cevap_sahibi['id'].'">'.$cevap_satir['cevap_yazan'].'</a>';

else $cevap_yazan = '<a href="../profil.php?u='.$cevap_sahibi['id'].'"><s>'.$cevap_satir['cevap_yazan'].'</s></a>';


if (!empty($cevap_sahibi['gercek_ad']))
	$cevap_yazan_adi = $cevap_sahibi['gercek_ad'];

else $cevap_yazan_adi = '';


if (!empty($cevap_sahibi['ozel_ad']))
	$cevap_yazan_yetkisi = '<font class="ozel_ad"><u>'.$cevap_sahibi['ozel_ad'].'</u></font>';

elseif ($cevap_sahibi['id'] == 1)
	$cevap_yazan_yetkisi = '<font class="kurucu"><u>'.$ayarlar['kurucu'].'</u></font>';

elseif ($cevap_sahibi['yetki'] == 1)
	$cevap_yazan_yetkisi = '<font class="yonetici"><u>'.$ayarlar['yonetici'].'</u></font>';

elseif ($cevap_sahibi['yetki'] == 2)
	$cevap_yazan_yetkisi = '<font class="yardimci"><u>'.$ayarlar['yardimci'].'</u></font>';

else $cevap_yazan_yetkisi = '';


if ($cevap_sahibi['resim'] != '')
{
	if ( (preg_match('/^http:\/\//i', $cevap_sahibi['resim'])) OR (preg_match('/^ftp:\/\//i', $cevap_sahibi['resim'])))
		$cevap_yazan_resmi = '<img src="'.$cevap_sahibi['resim'].'" alt="Kullan�c� Resmi">';

	else $cevap_yazan_resmi = '<img src="../'.$cevap_sahibi['resim'].'" alt="Kullan�c� Resmi">';
}

elseif ($ayarlar['kul_resim'] != '')
{
	if ( (preg_match('/^http:\/\//i', $ayarlar['kul_resim'])) OR (preg_match('/^ftp:\/\//i', $ayarlar['kul_resim'])))
		$cevap_yazan_resmi = '<img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';

	else $cevap_yazan_resmi = '<img src="../'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';
}

else $cevap_yazan_resmi = '';


if (!empty($cevap_sahibi['katilim_tarihi']))
	$cevap_yazan_kayit = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $cevap_sahibi['katilim_tarihi']);

else $cevap_yazan_kayit ='';


if (!empty($cevap_sahibi['mesaj_sayisi']))
	$cevap_yazan_mesajsayi = $cevap_sahibi['mesaj_sayisi'];

else $cevap_yazan_mesajsayi = '';


if ($cevap_sahibi['sehir_goster'] == 1)
	$cevap_yazan_sehir = $cevap_sahibi['sehir'];

else $cevap_yazan_sehir = 'Gizli';


if (empty($cevap_sahibi['gercek_ad']))
	$cevap_yazan_durum = '<font color="#FF0000">�ye silinmi�</font>';

elseif ($cevap_sahibi['engelle'] == 1)
	$cevap_yazan_durum = '<font color="#FF0000">�ye uzakla�t�r�lm��</font>';

elseif ($cevap_sahibi['gizli'] == 1)
	$cevap_yazan_durum = '<font color="#FF0000">Gizli</font>';

elseif ( (($cevap_sahibi['son_hareket'] + $zaman_asimi) > $tarih ) AND
		($cevap_sahibi['sayfano'] != '-1') )
	$cevap_yazan_durum = '<font color="#339900">Forumda</font>';

else $cevap_yazan_durum = '<font color="#FF0000">Forumda De�il</font>';


$cevap_yazan_eposta = '<a title="Forum �zerinden e-posta g�nder" href="../eposta.php?kim='.$cevap_sahibi['kullanici_adi'].'">E-Posta G�nder</a>';


if ($cevap_sahibi['web'])
	$cevap_yazan_web = '<br><a target="_blank" href="'.$cevap_sahibi['web'].'">Web Adresi</a>';

else $cevap_yazan_web = '';


$cevap_yazan_ozel = '<a href="../oi_yaz.php?ozel_kime='.$cevap_sahibi['kullanici_adi'].'">�zel ileti G�nder</a>';

$cevap_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevap_satir['tarih']);

$cevap_alinti_duzenle = '';


//	S�L VE GER� Y�KLE OLU�TURULUYOR	//

if ( ($mesaj_satir['silinmis'] != 1) AND ($cevap_satir['silinmis'] == 1) )
$cevap_alinti_duzenle .= '<a href="silinmis.php?kurtarc='.$cevap_satir['id'].'&amp;ks='.$_GET['ks'].'&amp;o='.$o.'"><img '.$simge_gerial.' alt="Bu cevab� geri y�kle" title="Bu cevab� geri y�kle"></a>&nbsp;&nbsp;<a href="silinmis.php?silc='.$cevap_satir['id'].'&amp;o='.$o.'"><img '.$simge_sil.' alt="Bu cevab� kal�c� olarak sil" title="Bu cevab� kal�c� olarak sil" onclick="return window.confirm(\'Bu cevab� kal�c� olarak silmek istedi�inize emin misiniz?\')"></a>&nbsp;&nbsp;';

$cevap_alinti_duzenle .= '<a href="ip_yonetimi.php?kip=1&amp;ip='.$cevap_satir['yazan_ip'].'"><img '.$simge_ip.' alt="Bu cevab� yazan�n ip adresi" title="Bu cevab� yazan�n ip adresi"></a>&nbsp;&nbsp;';



	//	CEVAPLARIN ��ER��� YAZDIRILIYOR	//
	//	VARSA �MZA VE DE���T�RME B�LG�LER� YAZDIRILIYOR	//


if ($cevap_satir['ifade'] == 1)
    $cevap_satir['cevap_icerik'] = ifadeler($cevap_satir['cevap_icerik']);

if ( ($cevap_satir['bbcode_kullan'] == 1) AND ($ayarlar['bbcode'] == 1) )
	$cevap_icerik = bbcode_acik($cevap_satir['cevap_icerik'],$cevap_satir['id']);

else $cevap_icerik = bbcode_kapali($cevap_satir['cevap_icerik']);

if ( (isset($cevap_sahibi['imza'])) and ($cevap_sahibi['imza']!='') )
{
	if ($ayarlar['bbcode'] == 1) $cevap_yazan_imza = bbcode_acik(ifadeler($cevap_sahibi['imza']),1);
	else $cevap_yazan_imza = bbcode_kapali(ifadeler($cevap_sahibi['imza']));
}

else $cevap_yazan_imza = '';




		//		�LET� DE���T�R�LME B�LG�LER�	//

$cevap_degisme = '';

if ($cevap_satir['degistirme_sayisi'] != 0):
	$cevap_degisme .= '<br>__________________<p><font size="1"><i> Bu ileti en son <b>'.$cevap_satir['degistiren'].'</b>
taraf�ndan <b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevap_satir['degistirme_tarihi']).'</b> tarihinde, toplamda '.$cevap_satir['degistirme_sayisi'].' kez de�i�tirilmi�tir.</i></font>&nbsp;<a href="ip_yonetimi.php?kip=1&amp;ip='.$cevap_satir['degistiren_ip'].'"><img '.$simge_ip.' alt="Bu konuyu de�i�tirenin ip adresi" title="Bu konuyu de�i�tirenin ip adresi"></a>';
endif;


//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{CEVAP_ANAME}' => $cevap_aname,
'{CEVAP_BASLIK}' => $cevap_satir['cevap_baslik'],
'{ILETI_NO}' => 'Cevap: '.$ileti_no,
'{CEVAP_YAZAN}' => $cevap_yazan,
'{CEVAP_YAZAN_ADI}' => $cevap_yazan_adi,
'{CEVAP_YAZAN_YETKISI}' => $cevap_yazan_yetkisi,
'{CEVAP_YAZAN_RESMI}' => $cevap_yazan_resmi,
'{CEVAP_YAZAN_KAYIT}' => $cevap_yazan_kayit,
'{CEVAP_YAZAN_MESAJSAYI}' => NumaraBicim($cevap_yazan_mesajsayi),
'{CEVAP_YAZAN_SEHIR}' => $cevap_yazan_sehir,
'{CEVAP_YAZAN_DURUM}' => $cevap_yazan_durum,
'{CEVAP_YAZAN_EPOSTA}' => $cevap_yazan_eposta,
'{CEVAP_YAZAN_WEB}' => $cevap_yazan_web,
'{CEVAP_YAZAN_OZEL}' => $cevap_yazan_ozel,
'{CEVAP_TARIHI}' => $cevap_tarihi,
'{CEVAP_ALINTI_DUZENLE}' => $cevap_alinti_duzenle,
'{CEVAP_ICERIK}' => $cevap_icerik,
'{CEVAP_YAZAN_IMZA}' => $cevap_yazan_imza,
'{CEVAP_DEGISTIRME}' => $cevap_degisme);


endwhile;
endif;



				//		CEVAP TABLOLARI	SONU		//





$kullanici_cikis = '&nbsp; | &nbsp; <a href="../cikis.php?o='.$o.'" onclick="return window.confirm(\'��k�� yapmak istedi�inize emin misiniz?\')">��k�� [ '.$kullanici_kim['kullanici_adi'].' ]</a>';


// �st forum - alt forum ba�l���
if ($forum_satir['alt_forum'] != '0')
{
	$alt_forum_baslik = '<a href="../forum.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;fs='.$_GET['fs'].'">'.$forum_satir['forum_baslik'].'</a><br>';

	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$forum_satir[alt_forum]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc2);

	$ust_forum_baslik = '<a href="../forum.php?f='.$forum_satir['id'].'">'.$forum_satir['forum_baslik'].'</a> &nbsp;&raquo;&nbsp; ';
}

else
{
	$ust_forum_baslik = '<a href="../forum.php?f='.$mesaj_satir['hangi_forumdan'].'&amp;fs='.$_GET['fs'].'">'.$forum_satir['forum_baslik'].'</a>';
	$alt_forum_baslik = '<br>';
}



$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2012 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  T�m haklar� sakl�d�r - All Rights Reserved

function hepsiniSec(kodCizelgesi){if(document.selection){var secim=document.body.createTextRange();secim.moveToElementText(document.getElementById(kodCizelgesi));secim.select();}else if(window.getSelection){var secim=document.createRange();secim.selectNode(document.getElementById(kodCizelgesi));window.getSelection().addRange(secim);}else if(document.createRange && (document.getSelection || window.getSelection)){secim=document.createRange();secim.selectNodeContents(document.getElementById(kodCizelgesi));a=window.getSelection ? window.getSelection() : document.getSelection();a.removeAllRanges();a.addRange(secim);}}function ResimBuyut(resim,ratgele,en,boy,islem){var katman=document.getElementById(ratgele);if(islem=="buyut"){resim.width=en;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"kucult")};katman.style.width=(en-12)+"px";katman.innerHTML="K���ltmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-out";}else if(islem=="kucult"){resim.width=600;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"buyut")};katman.style.width="588px";katman.innerHTML="B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}else if(islem=="ac")window.open(resim,"_blank","scrollbars=yes,left=1,top=1,width="+(en+40)+",height="+(boy+30)+",resizable=yes");}function ResimBoyutlandir(resim){if(resim.width>"600"){var en=resim.width;var boy=resim.height;var adres=resim.src;var rastgele="resim_boyut_"+Math.random();oyazi=document.createTextNode("B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+resim.width+"x"+resim.height+")");okatman=document.createElement("div");okatman.id=rastgele;okatman.className="resim_boyutlandir";okatman.align="left";okatman.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";okatman.style.cursor="pointer";okatman.onclick=function(){ResimBuyut(adres,rastgele,en,boy,"ac")};okatman.textNode=oyazi;okatman.appendChild(oyazi);resim.onclick=function(){ResimBuyut(resim,rastgele,en,boy,"buyut")};resim.width="600";resim.border="1";resim.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";resim.parentNode.insertBefore(okatman, resim);if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}}
//  --></script>';




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/konu_silinmis.html');



$dongusuz = array('{FORUM_ANASAYFA}' => '<a href="../'.$forum_index.'">Forum Ana Sayfas�</a>',
'{FORUM_BASLIK}' => $ust_forum_baslik,
'{KONU_BASLIK}' => $mesaj_satir['mesaj_baslik'],
'{SAYFALAMA}' => $sayfalama_cikis,
'{KULLANICI_CIKIS}' => $kullanici_cikis,
'{ALT_FORUM_BASLIK}' => $alt_forum_baslik,
'{JAVASCRIPT_KODU}' => $javascript_kodu);

$ornek1->dongusuz($dongusuz);


//	sadece birinci sayfada ko�ul 1 alan�n� g�ster

if (isset($kosul1))
	$ornek1->kosul('1', $kosul1, true);

else	$ornek1->kosul('1', array('' => ''), false);


//	cevap varsa ko�ul 2 alal�n� g�ster

if (isset($tekli1))
{
	$ornek1->kosul('2', array('' => ''), true);
	$ornek1->tekli_dongu('1',$tekli1);
}

else	$ornek1->kosul('2', array('' => ''), false);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>