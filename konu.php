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
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_SEO')) include 'seo.php';


if (isset($_GET['mesaj_no'])) $_GET['k'] = @zkTemizle($_GET['mesaj_no']);
elseif (isset($_GET['k'])) $_GET['k'] = @zkTemizle($_GET['k']);
else $_GET['k'] = 0;


if (is_numeric($_GET['k']) == false)
{
	header('Location: hata.php?hata=47');
	exit();
}


//	SAYFA DE�ERLER� YOKSA SIFIR YAPILIYOR

if (isset($_GET['sayfa'])) $_GET['ks'] = $_GET['sayfa'];
if (isset($_GET['fsayfa'])) $_GET['fs'] = $_GET['fsayfa'];

if (empty($_GET['ks'])) {$_GET['ks'] = 0; $baslik_ek = '';}
else
{
    $_GET['ks'] = @zkTemizle($_GET['ks']);
    $_GET['ks'] = @str_replace(array('-','x','.'), '', $_GET['ks']);
    if (is_numeric($_GET['ks']) == false) $_GET['ks'] = 0;
    if ($_GET['ks'] < 0) $_GET['ks'] = 0;
    $baslik_ek = ' : Sayfa '.(($_GET['ks']/$ayarlar['ksyfkota'])+1);
}


if (empty($_GET['fs'])) $_GET['fs'] = 0;
else
{
    $_GET['fs'] = @zkTemizle($_GET['fs']);
    $_GET['fs'] = @str_replace(array('-','x','.'), '', $_GET['fs']);
    if (is_numeric($_GET['fs']) == false) $_GET['fs'] = 0;
    if ($_GET['fs'] < 0) $_GET['fs'] = 0;
}


$zaman_asimi = $ayarlar['cevrimici'];
$tarih = time();


// MESAJ B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT
id,hangi_forumdan,yazan,mesaj_baslik,mesaj_icerik,tarih,yazan_ip,bbcode_kullan,degistirme_sayisi,degistiren,degistirme_tarihi,degistiren_ip,kilitli,son_mesaj_tarihi,goruntuleme,ust_konu,ifade
FROM $tablo_mesajlar WHERE id='$_GET[k]' AND silinmis='0' LIMIT 1";
$sonuc = mysql_query($strSQL);
$mesaj_satir = mysql_fetch_assoc($sonuc);


// KONU YOKSA HATA MESAJI, VARSA DEVAM //

if (empty($mesaj_satir))
{
	header('Location: hata.php?hata=47');
	exit();
}


// SEO ADRES�N�N DO�RULU�U KONTROL ED�L�YOR YANLI�SA DO�RU ADRESE Y�NLEND�R�L�YOR //

$dogru_adres = seoyap($mesaj_satir['mesaj_baslik']);

if ( (isset($_SERVER['REQUEST_URI'])) AND ($_SERVER['REQUEST_URI'] != '') AND (!@preg_match("/-$dogru_adres.html/i", $_SERVER['REQUEST_URI'])) AND (!@preg_match('/konu\.php\?/i', $_SERVER['REQUEST_URI'])) )
{
    $yonlendir = linkver('konu.php?k='.$mesaj_satir['id'], $mesaj_satir['mesaj_baslik']);
    header('Location:'.$yonlendir);
    exit();
}


// FORUM B�LG�LER� �EK�L�YOR //

$strSQL = "SELECT forum_baslik,okuma_izni,yazma_izni,alt_forum FROM $tablo_forumlar
			WHERE id='$mesaj_satir[hangi_forumdan]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$forum_satir = mysql_fetch_assoc($sonuc);



			//	KULLANICIYA G�RE FORUM G�STER�M� - BA�I		//



//	FORUM HERKESE KAPALIYSA	//

if ($forum_satir['okuma_izni'] == 5)
{
	// sadece y�neticiyse girebilir
	if ( (!isset($kullanici_kim['yetki']) ) OR ($kullanici_kim['yetki'] != 1) )
	{
		header('Location: hata.php?hata=164');
		exit();
	}
}


//	FORUM M�SAF�RLERE KAPALIYSA		//

if ($forum_satir['okuma_izni'] > 0)
{
	// �ye de�ilse - ziyaret�iyse
	if ( empty($kullanici_kim['id']) )
	{
		if (@preg_match('/cikiss=1/', $_SERVER['REQUEST_URI']))
		{
			header('Location: index.php');
			exit();
		}

		else
		{
			header('Location: hata.php?uyari=6&git='.$_SERVER['REQUEST_URI']);
			exit();
		}
	}
}


//	SADECE Y�NET�C�LER ���NSE	//

if ($forum_satir['okuma_izni'] == 1)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
	{
		header('Location: hata.php?hata=15');
		exit();
	}
}


//	SADECE Y�NET�C�LER VE YARDIMCILAR ���NSE	//

elseif ($forum_satir['okuma_izni'] == 2)
{
	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
		AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
	{
		header('Location: hata.php?hata=16');
		exit();
	}
}


//	SADECE �ZEL �YELER ���NSE 	//

elseif ($forum_satir['okuma_izni'] == 3)
{
	//	Y�NET�C� DE��LSE YARDIMCILI�INA BAK	//

	if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2) )
	{
		if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$mesaj_satir[hangi_forumdan]' AND okuma='1' OR";
		else $grupek = "grup='0' AND";

		$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$mesaj_satir[hangi_forumdan]' AND okuma='1'";
		$kul_izin = mysql_query($strSQL);

		if ( !mysql_num_rows($kul_izin) )
		{
			header('Location: hata.php?hata=17');
			exit();
		}
	}
}


// b�l�m yard�mc�s� ise y�netme yetkisine bak�l�yor - sil, d�zenle, vs linkleri i�in
if ($kullanici_kim['yetki'] == 3)
{
	if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$mesaj_satir[hangi_forumdan]' AND yonetme='1' OR";
	else $grupek = "grup='0' AND";

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$mesaj_satir[hangi_forumdan]' AND yonetme='1'";
	$kul_izin = mysql_query($strSQL);

	if (mysql_num_rows($kul_izin)) $yrd_yetkisi = true;
	else $yrd_yetkisi = false;
}

			//	KULLANICIYA G�RE FORUM G�STER�M� - SONU			//




// MESAJ SAH�B�N�N PROF�L� �EK�L�YOR //

$strSQL = "SELECT
id,kullanici_adi,gercek_ad,resim,katilim_tarihi,mesaj_sayisi,sehir_goster,sehir,web,imza,yetki,son_hareket,gizli,engelle,hangi_sayfada,sayfano,ozel_ad 
FROM $tablo_kullanicilar WHERE kullanici_adi='$mesaj_satir[yazan]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$mesaj_sahibi = mysql_fetch_assoc($sonuc);


// G�R�NT�LEME SAYISINI ARTTIR //

$strSQL = "UPDATE $tablo_mesajlar SET goruntuleme=goruntuleme + 1
			WHERE id='$mesaj_satir[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


// CEVAP B�LG�LER� �EK�L�YOR

$strSQL = "SELECT
id,cevap_yazan,cevap_baslik,cevap_icerik,tarih,yazan_ip,bbcode_kullan,degistirme_sayisi,degistiren,degistirme_tarihi,degistiren_ip,ifade
FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$_GET[k]' ORDER BY tarih LIMIT $_GET[ks],$ayarlar[ksyfkota]";
$cevap = mysql_query($strSQL);


// CEVAPLARIN SATIR SAYISINA BAKILIYOR //

$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$_GET[k]'");
$satir_sayi = mysql_num_rows($result);


// OLU�TURULACAK SAYFA SAYISI BA�LANTISI //

$toplam_sayfa = ($satir_sayi / $ayarlar['ksyfkota']);
settype($toplam_sayfa,'integer');

if (($satir_sayi % $ayarlar['ksyfkota']) != 0)
$toplam_sayfa++;


//	BA�LI�IN �LET� NUMARASI //

$ileti_no = $_GET['ks'];





//  BA�LI�IN OKUNDU B�LG�S� �EREZE YAZDIRILIYOR    //

if ( (isset($kullanici_kim['son_giris'])) AND ($mesaj_satir['son_mesaj_tarihi'] > $kullanici_kim['son_giris']) )
{
    if (isset($_COOKIE['kfk_okundu']))
    {
        $cerez_dizi = explode('_', $_COOKIE['kfk_okundu']);

        foreach ($cerez_dizi as $cerez_parcala)
        {
            $okunan_kno = substr($cerez_parcala, 11);
            $okunan_dizi[$okunan_kno] = substr($cerez_parcala, 0, 10);
        }

        if (empty($okunan_dizi[$mesaj_satir['id']]))
        {
            setcookie('kfk_okundu', $_COOKIE['kfk_okundu'].'_'.$tarih.'-'.$mesaj_satir['id'], $tarih +$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
        }

        elseif ($mesaj_satir['son_mesaj_tarihi'] > $okunan_dizi[$mesaj_satir['id']])
        {
            $cereze_yaz = '';

            foreach ($okunan_dizi as $ckno => $ctarih)
            {
                if ($ckno == $mesaj_satir['id']) $cereze_yaz .= '_'.$tarih.'-'.$ckno;

                else $cereze_yaz .= '_'.$ctarih.'-'.$ckno;
            }

            $cereze_yaz = substr($cereze_yaz, 1);
            setcookie('kfk_okundu', $cereze_yaz, $tarih +$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
        }
    }

    else 
    {
        setcookie('kfk_okundu', $tarih.'-'.$mesaj_satir['id'], $tarih +$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
    }
}



$sayfano = '2,'.$mesaj_satir['id'].',3,'.$mesaj_satir['hangi_forumdan'];
$sayfa_adi = $mesaj_satir['mesaj_baslik'].$baslik_ek;


include 'baslik.php';





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
	&nbsp;<a href="'.linkver('konu.php?k='.$_GET['k'], $mesaj_satir['mesaj_baslik']).'">&laquo;ilk</a>&nbsp;</td>
		
	<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">
	&nbsp;<a href="'.linkver('konu.php?k='.$_GET['k'].'&fs='.$_GET['fs'].'&ks='.($_GET['ks'] - $ayarlar['ksyfkota']), $mesaj_satir['mesaj_baslik']).'">&lt;</a>&nbsp;</td>';
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

			&nbsp;<a href="'.linkver('konu.php?k='.$_GET['k'].'&fs='.$_GET['fs'].'&ks='.($sayi * $ayarlar['ksyfkota']), $mesaj_satir['mesaj_baslik']).'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}

if ($_GET['ks'] < ($satir_sayi - $ayarlar['ksyfkota']))
{
	$sayfalama_cikis .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">
	&nbsp;<a href="'.linkver('konu.php?k='.$_GET['k'].'&fs='.$_GET['fs'].'&ks='.($_GET['ks'] + $ayarlar['ksyfkota']), $mesaj_satir['mesaj_baslik']).'">&gt;</a>&nbsp;</td>

	<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">
	&nbsp;<a href="'.linkver('konu.php?k='.$_GET['k'].'&fs='.$_GET['fs'].'&ks='.(($toplam_sayfa - 1) * $ayarlar['ksyfkota']), $mesaj_satir['mesaj_baslik']).'">son&raquo;</a>&nbsp;</td>';
}

$sayfalama_cikis .= '</tr></tbody></table>';
endif;


	//		SAYFA BA�LANTILARI OLU�TURULUYOR SONU	//






//	YEN� BA�LIK YEN� CEVAP			//


$baslik_cevap = '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=yeni"><img '.$yenibaslik_rengi.' alt="Yeni Ba�l�k" title="Yeni Konu A�mak i�in T�klay�n"></a> &nbsp;';



if ($mesaj_satir['kilitli'] == 1)
{
	$baslik_cevap .= '<img '.$kilitli_rengi.' alt="kilitli" title="Bu konu kilitlenmi�tir, cevap yaz�lamaz" style="cursor: help;">';
	$form_ksayfa = 0;
}

else
{
	if ($satir_sayi < $ayarlar['ksyfkota'])
	{
		$baslik_cevap .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;mesaj_no='.$_GET['k'].'&amp;kip=cevapla&amp;fsayfa='.$_GET['fs'].'"><img '.$cevapyaz_rengi.' alt="Cevap Yaz" title="Bu Konuya Cevap Yazmak i�in T�klay�n"></a>';
		$form_ksayfa = 0;
	}

	elseif ( ($satir_sayi % $ayarlar['ksyfkota']) == 0 )
	{
		$baslik_cevap .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;mesaj_no='.$_GET['k'].'&amp;kip=cevapla&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$satir_sayi.'"><img '.$cevapyaz_rengi.' alt="Cevap Yaz" title="Bu Konuya Cevap Yazmak i�in T�klay�n"></a>';
		$form_ksayfa = $satir_sayi; 
	}

	else
	{
		$y_sayi = $satir_sayi - ($satir_sayi % $ayarlar['ksyfkota']);
		$baslik_cevap .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;mesaj_no='.$_GET['k'].'&amp;kip=cevapla&amp;amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$y_sayi.'"><img '.$cevapyaz_rengi.' alt="Cevap Yaz" title="Bu Konuya Cevap Yazmak i�in T�klay�n"></a>';
		$form_ksayfa = $y_sayi;
	}
}




				//		BA�LIK TABLOSU BA�I		//


if ($_GET['ks'] < 1 ):


if ($mesaj_sahibi['engelle'] != 1)
    $konu_acan = '<a href="'.linkver('profil.php?u='.$mesaj_sahibi['id'].'&kim='.$mesaj_satir['yazan'],$mesaj_satir['yazan']).'">'.$mesaj_satir['yazan'].'</a>';

else $konu_acan = '<a href="'.linkver('profil.php?u='.$mesaj_sahibi['id'].'&kim='.$mesaj_satir['yazan'],$mesaj_satir['yazan']).'"><s>'.$mesaj_satir['yazan'].'</s></a>';


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

elseif ($mesaj_sahibi['yetki'] == 3)
	$konu_acan_yetkisi = '<font class="blm_yrd"><u>'.$ayarlar['blm_yrd'].'</u></font>';

else $konu_acan_yetkisi = '';


if ($mesaj_sahibi['resim'] != '') $konu_acan_resmi = '<img src="'.$mesaj_sahibi['resim'].'" alt="Kullan�c� Resmi">';
elseif ($ayarlar['kul_resim'] != '') $konu_acan_resmi = '<img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';
else $konu_acan_resmi = '';


if (!empty($mesaj_sahibi['katilim_tarihi']))
	$konu_acan_kayit = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $mesaj_sahibi['katilim_tarihi']);

else $konu_acan_kayit = '';


if (!empty($mesaj_sahibi['mesaj_sayisi']))
	$konu_acan_mesajsayi = $mesaj_sahibi['mesaj_sayisi'];

	else $konu_acan_mesajsayi = 0;


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


$konu_acan_eposta = '<a title="Forum �zerinden e-posta g�nder" href="eposta.php?kim='.$mesaj_sahibi['kullanici_adi'].'">E-Posta G�nder</a>';

if ($mesaj_sahibi['web'])
	$konu_acan_web = '<br><a href="'.$mesaj_sahibi['web'].'" target="_blank">Web Adresi</a>';

else $konu_acan_web = '';

$konu_acan_ozel = '<a href="oi_yaz.php?ozel_kime='.$mesaj_sahibi['kullanici_adi'].'">�zel ileti G�nder</a>';

$konu_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $mesaj_satir['tarih']);



		//	ALINTI S�L VE D�ZENLE OLU�TURULUYOR - BA�I	//


$konu_alinti_duzenle = '';

if ($satir_sayi < $ayarlar['ksyfkota'])
	$konu_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=mesaj">';

elseif (($satir_sayi % $ayarlar['ksyfkota']) == 0 )
	$konu_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=mesaj&amp;sayfa='.$satir_sayi.'">';

else
{
  $y_sayi = $satir_sayi - ($satir_sayi % $ayarlar['ksyfkota']);
	$konu_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=mesaj&amp;sayfa='.$y_sayi.'">';
}

$konu_alinti_duzenle .= '<img '.$simge_alinti.' alt="Al�nt� yaparak cevapla" title="Al�nt� yaparak cevapla"></a>&nbsp;&nbsp;';



			//	KULLANICIYA G�RE S�L VE D�ZENLE - BA�I		//



//	Y�NET�C� VE YARDIMCI �SE	//
if ( ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) ):

$konu_alinti_duzenle .= '<a href="mesaj_sil.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_sil.' alt="Bu konuyu sil" title="Bu konuyu sil"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="baslik_tasi.php?kip=tasi&amp;mesaj_no='.$mesaj_satir['id'].'"><img '.$simge_tasi.' alt="Bu konuyu ta��" title="Bu konuyu ta��"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_degistir.' alt="Bu konuyu de�i�tir" title="Bu konuyu de�i�tir"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir_yap.php?kip=kilitle&amp;mesaj_no='.$mesaj_satir['id'].'">';

if ($mesaj_satir['kilitli'] == 1)
$konu_alinti_duzenle .= '<img '.$simge_kilitle.' alt="Bu konunun kilitini a�" title="Bu konunun kilitini a�"></a>&nbsp;&nbsp;';

else $konu_alinti_duzenle .= '<img '.$simge_kilitle.' alt="Bu konuyu kilitle" title="Bu konuyu kilitle"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir_yap.php?kip=ustkonu&amp;mesaj_no='.$mesaj_satir['id'].'">';

if ($mesaj_satir['ust_konu'] == 1)
$konu_alinti_duzenle .= '<img '.$simge_ust.' alt="Alt konu yap" title="Alt konu yap"></a>&nbsp;&nbsp;';

else $konu_alinti_duzenle .= '<img '.$simge_ust.' alt="�st konu yap" title="�st konu yap"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="yonetim/ip_yonetimi.php?kip=1&amp;ip='.$mesaj_satir['yazan_ip'].'"><img  '.$simge_ip.' alt="Bu konuyu a�an�n ip adresi" title="Bu konuyu a�an�n ip adresi"></a>&nbsp;&nbsp;';


//	B�L�M YARDIMCI �SE	//
elseif ($kullanici_kim['yetki'] == 3):

if ( (isset($yrd_yetkisi)) AND ($yrd_yetkisi == true) ):


$konu_alinti_duzenle .= '<a href="mesaj_sil.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_sil.' alt="Bu konuyu sil" title="Bu konuyu sil"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="baslik_tasi.php?kip=tasi&amp;mesaj_no='.$mesaj_satir['id'].'"><img '.$simge_tasi.' alt="Bu konuyu ta��" title="Bu konuyu ta��"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_degistir.' alt="Bu konuyu de�i�tir" title="Bu konuyu de�i�tir"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir_yap.php?kip=kilitle&amp;mesaj_no='.$mesaj_satir['id'].'">';

if ($mesaj_satir['kilitli'] == 1)
$konu_alinti_duzenle .= '<img '.$simge_kilitle.' alt="Bu konunun kilitini a�" title="Bu konunun kilitini a�"></a>&nbsp;&nbsp;';

else $konu_alinti_duzenle .= '<img '.$simge_kilitle.' alt="Bu konuyu kilitle" title="Bu konuyu kilitle"></a>&nbsp;&nbsp;';

$konu_alinti_duzenle .= '<a href="mesaj_degistir_yap.php?kip=ustkonu&amp;mesaj_no='.$mesaj_satir['id'].'">';

if ($mesaj_satir['ust_konu'] == 1)
$konu_alinti_duzenle .= '<img '.$simge_ust.' alt="Alt konu yap" title="Alt konu yap"></a>&nbsp;&nbsp;';

else $konu_alinti_duzenle .= '<img '.$simge_ust.' alt="�st konu yap" title="�st konu yap"></a>&nbsp;&nbsp;';



//	BU FORUMUN YARDIMCISI OLMADI�I HALDE �LET�Y� YAZANSA	//

elseif ($kullanici_kim['kullanici_adi'] == $mesaj_satir['yazan']):
	$konu_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_degistir.' alt="Bu konuyu de�i�tir" title="Bu konuyu de�i�tir"></a>&nbsp;&nbsp;';
endif;


//	�LET�Y� YAZAN K���YSE	//

elseif ($kullanici_kim['kullanici_adi'] == $mesaj_satir['yazan']):
	$konu_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=mesaj&amp;mesaj_no='.$mesaj_satir['id'].'&amp;fsayfa='.$_GET['fs'].'"><img '.$simge_degistir.' alt="Bu konuyu de�i�tir" title="Bu konuyu de�i�tir"></a>&nbsp;&nbsp;';
endif;



			//	KULLANICIYA G�RE S�L VE D�ZENLE - SONU			//




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
taraf�ndan <b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $mesaj_satir['degistirme_tarihi']).'</b> tarihinde, toplamda '.$mesaj_satir['degistirme_sayisi'].' kez de�i�tirilmi�tir.</i></font>';

if ($kullanici_kim['yetki'] == 1):
	$konu_degisme .= '&nbsp;<a href="yonetim/ip_yonetimi.php?kip=1&amp;ip='.$mesaj_satir['degistiren_ip'].'"><img  '.$simge_ip.' alt="Bu konuyu de�i�tirenin ip adresi" title="Bu konuyu de�i�tirenin ip adresi"></a>';

endif;
endif;


//	veriler tema motoruna yollan�yor	//

$kosul1 = array('{KONU_ANAME}' => '<a name="c0"></a>',
'{KONU_BASLIK2}' => $mesaj_satir['mesaj_baslik'],
'{GOSTERIM}' => NumaraBicim(($mesaj_satir['goruntuleme']+1)),
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


if ($cevap_sahibi['engelle'] != 1)
    $cevap_yazan = '<a href="'.linkver('profil.php?u='.$cevap_sahibi['id'].'&kim='.$cevap_satir['cevap_yazan'],$cevap_satir['cevap_yazan']).'">'.$cevap_satir['cevap_yazan'].'</a>';

else $cevap_yazan = '<a href="'.linkver('profil.php?u='.$cevap_sahibi['id'].'&kim='.$cevap_satir['cevap_yazan'],$cevap_satir['cevap_yazan']).'"><s>'.$cevap_satir['cevap_yazan'].'</s></a>';



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

elseif ($cevap_sahibi['yetki'] == 3)
	$cevap_yazan_yetkisi = '<font class="blm_yrd"><u>'.$ayarlar['blm_yrd'].'</u></font>';

else $cevap_yazan_yetkisi = '';


if ($cevap_sahibi['resim'] != '')
	$cevap_yazan_resmi = '<img src="'.$cevap_sahibi['resim'].'" alt="Kullan�c� Resmi">';
elseif ($ayarlar['kul_resim'] != '')
	$cevap_yazan_resmi = '<img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';
else $cevap_yazan_resmi = '';


if (!empty($cevap_sahibi['katilim_tarihi']))
	$cevap_yazan_kayit = zonedate('d.m.Y', $ayarlar['saat_dilimi'], false, $cevap_sahibi['katilim_tarihi']);

else $cevap_yazan_kayit ='';


if (!empty($cevap_sahibi['mesaj_sayisi']))
	$cevap_yazan_mesajsayi = $cevap_sahibi['mesaj_sayisi'];

else $cevap_yazan_mesajsayi = 0;


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


$cevap_yazan_eposta = '<a title="Forum �zerinden e-posta g�nder" href="eposta.php?kim='.$cevap_sahibi['kullanici_adi'].'">E-Posta G�nder</a>';


if ($cevap_sahibi['web'])
	$cevap_yazan_web = '<br><a href="'.$cevap_sahibi['web'].'" target="_blank">Web Adresi</a>';

else $cevap_yazan_web = '';


$cevap_yazan_ozel = '<a href="oi_yaz.php?ozel_kime='.$cevap_sahibi['kullanici_adi'].'">�zel ileti G�nder</a>';

$cevap_tarihi = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevap_satir['tarih']);



		//	ALINTI S�L VE D�ZENLE OLU�TURULUYOR - BA�I	//


$cevap_alinti_duzenle = '';

if ($satir_sayi < $ayarlar['ksyfkota'])
	$cevap_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=cevap">';

elseif ( ($satir_sayi % $ayarlar['ksyfkota']) == 0 )
	$cevap_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=cevap&amp;sayfa='.$satir_sayi.'">';

else
{
	$y_sayi = $satir_sayi - ($satir_sayi % $ayarlar['ksyfkota']);
	$cevap_alinti_duzenle .= '<a href="mesaj_yaz.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevapla&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;alinti=cevap&amp;sayfa='.$y_sayi.'">';
}

$cevap_alinti_duzenle .= '<img '.$simge_alinti.' alt="Al�nt� yaparak cevapla" title="Al�nt� yaparak cevapla"></a>&nbsp;&nbsp;';



			//	KULLANICIYA G�RE S�L VE D�ZENLE - BA�I		//


//	Y�NET�C� VE YARDIMCI �SE	//

if ( ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) ):

$cevap_alinti_duzenle .= '<a href="mesaj_sil.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_sil.'  alt="Bu cevab� sil" title="Bu cevab� sil"></a>&nbsp;&nbsp;';

$cevap_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_degistir.' alt="Bu cevab� de�i�tir" title="Bu cevab� de�i�tir"></a>&nbsp;&nbsp;';

$cevap_alinti_duzenle .= '<a href="yonetim/ip_yonetimi.php?kip=1&amp;ip='.$cevap_satir['yazan_ip'].'"><img  '.$simge_ip.' alt="Bu cevab� yazan�n ip adresi" title="Bu cevab� yazan�n ip adresi"></a>&nbsp;&nbsp;';


//	B�L�M YARDIMCI �SE	//

elseif ($kullanici_kim['yetki'] == 3):

if ( (isset($yrd_yetkisi)) AND ($yrd_yetkisi == true) ):

$cevap_alinti_duzenle .= '<a href="mesaj_sil.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_sil.' alt="Bu cevab� sil" title="Bu cevab� sil"></a>&nbsp;&nbsp;';

$cevap_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_degistir.' alt="Bu cevab� de�i�tir" title="Bu cevab� de�i�tir"></a>&nbsp;&nbsp;';


//	BU FORUMUN YARDIMCISI OLMADI�I HALDE �LET�Y� YAZANSA	//

elseif ($kullanici_kim['kullanici_adi'] == $cevap_satir['cevap_yazan']):
	$cevap_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_degistir.' alt="Bu cevab� de�i�tir" title="Bu cevab� de�i�tir"></a>&nbsp;&nbsp;';
endif;


//	�LET�Y� YAZAN K���YSE	//

elseif ($kullanici_kim['kullanici_adi'] == $cevap_satir['cevap_yazan']):
	$cevap_alinti_duzenle .= '<a href="mesaj_degistir.php?fno='.$mesaj_satir['hangi_forumdan'].'&amp;kip=cevap&amp;mesaj_no='.$mesaj_satir['id'].'&amp;cevap_no='.$cevap_satir['id'].'&amp;fsayfa='.$_GET['fs'].'&amp;sayfa='.$_GET['ks'].'"><img '.$simge_degistir.' alt="Bu cevab� de�i�tir" title="Bu cevab� de�i�tir"></a>&nbsp;&nbsp;';

endif;



			//	KULLANICIYA G�RE S�L VE D�ZENLE - SONU			//




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
taraf�ndan <b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $cevap_satir['degistirme_tarihi']).'</b> tarihinde, toplamda '.$cevap_satir['degistirme_sayisi'].' kez de�i�tirilmi�tir.</i></font>';

if ($kullanici_kim['yetki'] == 1):
	$cevap_degisme .= '&nbsp;<a href="yonetim/ip_yonetimi.php?kip=1&amp;ip='.$cevap_satir['degistiren_ip'].'"><img '.$simge_ip.' alt="Bu cevab� de�i�tirenin ip adresi" title="Bu cevab� de�i�tirenin ip adresi"></a>';

endif;
endif;



$cevap_bag = '<a href="'.linkver('konu.php?k='.$mesaj_satir['id'].'&ks='.$_GET['ks'], $mesaj_satir['mesaj_baslik'], '#c'.$cevap_satir['id']).'" style="color: #ffffff; text-decoration: none;" title="Cevap ba�lant�s�">Cevap: '.$ileti_no.'</a>';



//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{CEVAP_ANAME}' => $cevap_aname,
'{CEVAP_BASLIK}' => $cevap_satir['cevap_baslik'],
'{ILETI_NO}' => $cevap_bag,
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




if (isset($kullanici_kim['id']))
	$kullanici_cikis = '&nbsp; | &nbsp; <a href="cikis.php?o='.$o.'" onclick="return window.confirm(\'��k�� yapmak istedi�inize emin misiniz?\')">��k�� [ '.$kullanici_kim['kullanici_adi'].' ]</a>';

else $kullanici_cikis = '';



//  KONUYU G�R�NT�LEYENLER

if ($ayarlar['konu_kisi'] == 1)
{
	$gor_usayi = 0;
	$gor_usayi2 = 0;
	$gor_uyeler = '';

	$sonuc = mysql_query("SELECT sid FROM $tablo_oturumlar WHERE (sayfano LIKE '2,$mesaj_satir[id],%') AND (son_hareket + $zaman_asimi) > $tarih");
	$gor_msayi = mysql_num_rows($sonuc);


	$sonuc = mysql_query("SELECT id,kullanici_adi,gizli FROM $tablo_kullanicilar WHERE (sayfano LIKE '2,$mesaj_satir[id],%') AND (son_hareket + $zaman_asimi) > $tarih AND sayfano!='-1'");

	while ($gor_uye = mysql_fetch_assoc($sonuc))
	{
		if ($gor_uye['gizli'] == 0)
		{
			$gor_uyeler .= '<a href="'.linkver('profil.php?u='.$gor_uye['id'].'&kim='.$gor_uye['kullanici_adi'],$gor_uye['kullanici_adi']).'">'.$gor_uye['kullanici_adi'].'</a>, ';
			$gor_usayi++;
		}

		else
		{
			if ((isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1))
				$gor_uyeler .= '<a href="'.linkver('profil.php?u='.$gor_uye['id'].'&kim='.$gor_uye['kullanici_adi'],$gor_uye['kullanici_adi']).'"><i>'.$gor_uye['kullanici_adi'].'</i></a>, ';
			$gor_usayi2++;
		}
	}

	if ($gor_uyeler == '') $gor_uyeler = 'Bu konuyu g�r�nt�leyen �ye yok.';

	$gor_kisi = 'Bu konuyu '.($gor_msayi + $gor_usayi + $gor_usayi2).' ki�i g�r�nt�l�yor:&nbsp; '.$gor_msayi.' Misafir, '.($gor_usayi + $gor_usayi2).' �ye';
	if ($gor_usayi2 != 0) $gor_kisi .= ' ('.$gor_usayi2.' tanesi gizli)';
}

else {$gor_kisi = ''; $gor_uyeler = '';}




// �st forum - alt forum ba�l���
if ($forum_satir['alt_forum'] != '0')
{
	$alt_forum_baslik = '<a href="'.linkver('forum.php?f='.$mesaj_satir['hangi_forumdan'].'&fs='.$_GET['fs'], $forum_satir['forum_baslik']).'">'.$forum_satir['forum_baslik'].'</a><br>';

	$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$forum_satir[alt_forum]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc2);

	$ust_forum_baslik = '<a href="'.linkver('forum.php?f='.$forum_satir['id'], $forum_satir['forum_baslik']).'">'.$forum_satir['forum_baslik'].'</a> &nbsp;&raquo;&nbsp; ';	
}

else
{
	$ust_forum_baslik = '<a href="'.linkver('forum.php?f='.$mesaj_satir['hangi_forumdan'].'&fs='.$_GET['fs'], $forum_satir['forum_baslik']).'">'.$forum_satir['forum_baslik'].'</a>';
	$alt_forum_baslik = '<br>';
}



$javascript_kodu = '<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2013 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  T�m haklar� sakl�d�r - All Rights Reserved

function hepsiniSec(kodCizelgesi){if(document.selection){var secim=document.body.createTextRange();secim.moveToElementText(document.getElementById(kodCizelgesi));secim.select();}else if(window.getSelection){var secim=document.createRange();secim.selectNode(document.getElementById(kodCizelgesi));window.getSelection().addRange(secim);}else if(document.createRange && (document.getSelection || window.getSelection)){secim=document.createRange();secim.selectNodeContents(document.getElementById(kodCizelgesi));a=window.getSelection ? window.getSelection() : document.getSelection();a.removeAllRanges();a.addRange(secim);}}function ResimBuyut(resim,ratgele,en,boy,islem){var katman=document.getElementById(ratgele);if(islem=="buyut"){resim.width=en;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"kucult")};katman.style.width=(en-12)+"px";katman.innerHTML="K���ltmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-out";}else if(islem=="kucult"){resim.width=600;resim.onclick=function(){ResimBuyut(resim,ratgele,en,boy,"buyut")};katman.style.width="588px";katman.innerHTML="B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+en+"x"+boy+")";if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}else if(islem=="ac")window.open(resim,"_blank","scrollbars=yes,left=1,top=1,width="+(en+40)+",height="+(boy+30)+",resizable=yes");}function ResimBoyutlandir(resim){if(resim.width>"600"){var en=resim.width;var boy=resim.height;var adres=resim.src;var rastgele="resim_boyut_"+Math.random();oyazi=document.createTextNode("B�y�tmek i�in resmin �zerine t�klay�n. Yeni pencerede a�mak i�in buraya t�klay�n."+" ("+resim.width+"x"+resim.height+")");okatman=document.createElement("div");okatman.id=rastgele;okatman.className="resim_boyutlandir";okatman.align="left";okatman.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";okatman.style.cursor="pointer";okatman.onclick=function(){ResimBuyut(adres,rastgele,en,boy,"ac")};okatman.textNode=oyazi;okatman.appendChild(oyazi);resim.onclick=function(){ResimBuyut(resim,rastgele,en,boy,"buyut")};resim.width="600";resim.border="1";resim.title="Ger�ek boyutunda g�rmek i�in resmin �zerine t�klay�n!";resim.parentNode.insertBefore(okatman, resim);if(document.all)resim.style.cursor="pointer";else resim.style.cursor="-moz-zoom-in";}}
//  --></script>';

$javascript_kodu2 = '<script type="text/javascript"><!-- //
function denetle(){var dogruMu=true;if(document.form1.mesaj_icerik.value.length < 3){dogruMu=false;alert("YAZDI�INIZ MESAJ 3 KARAKTERDEN UZUN OLMALIDIR !");}else;return dogruMu;}function onizle(){if(denetle()){document.form1.action=\'mesaj_yaz.php#onizleme\';if(document.form1.bbcode_kullan.checked==true)document.form1.bbcode_kullan.value=1;if(document.form1.ifade.checked==true) document.form1.ifade.value=1;document.form1.submit();}}
//  --></script>';



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/konu.html');


$dongusuz = array('{FORUM_ANASAYFA}' => '<a href="'.$forum_index.'">Forum Ana Sayfas�</a>',
'{FORUM_BASLIK}' => $ust_forum_baslik,
'{KONU_BASLIK}' => $mesaj_satir['mesaj_baslik'],
'{SAYFALAMA}' => $sayfalama_cikis,
'{BASLIK_CEVAP}' => $baslik_cevap,
'{KULLANICI_CIKIS}' => $kullanici_cikis,
'{ALT_FORUM_BASLIK}' => $alt_forum_baslik,
'{GOR_KISI}' => $gor_kisi,
'{GOR_UYELER}' => $gor_uyeler,
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


// sadece �yelere h�zl� cevap yazma formunu g�ster

if (isset($kullanici_kim['id'])) 
	$ornek1->kosul('3', array('{FORM_FNO}' => $mesaj_satir['hangi_forumdan'],
							'{FORM_MESAJNO}' => $_GET['k'],
							'{FORM_FSAYFA}' => $_GET['fs'],
							'{FORM_KSAYFA}' => $form_ksayfa,
							'{JAVASCRIPT_KODU2}' => $javascript_kodu2), true);

else	$ornek1->kosul('3', array('' => ''), false);

if ($ayarlar['konu_kisi'] != 1) $ornek1->kosul('4', array(''=>''), false);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>