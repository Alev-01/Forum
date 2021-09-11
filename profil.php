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
if (!defined('DOSYA_SEO')) include 'seo.php';

$zaman_asimi = $ayarlar['cevrimici'];
$tarih = time();



//	$U DE���KEN� VARSA BU KULLANICIYA A�T VER�LER� �EK 	//

if ( (isset($_GET['u'])) AND ($_GET['u'] != '') )
{
	if (is_numeric($_GET['u']) == false)
	{
		header('Location: hata.php?hata=46');
		exit();
	}

	$_GET['u'] = @zkTemizle($_GET['u']);

	$strSQL = "SELECT id,kullanici_adi,gercek_ad,dogum_tarihi_goster,dogum_tarihi,sehir_goster,sehir,posta_goster,posta,web,katilim_tarihi,mesaj_sayisi,resim,imza,yetki,engelle,gizli,son_hareket,kul_etkin,hangi_sayfada,sayfano,grupid,icq,msn,yahoo,aim,skype,ozel_ad
	FROM $tablo_kullanicilar WHERE id='$_GET[u]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
	$satir = mysql_fetch_array($sonuc);

	if (empty($satir['kullanici_adi']))
	{
		header('Location: hata.php?hata=46');
		exit();
	}


	$sayfano = '4,'.$satir['id'];
	$sayfa_adi = 'Profil G�r�nt�leme: '.$satir['kullanici_adi'];
	$sayfa_baslik2 = 'Kullan�c� Profili';
}



//	$KIM DE���KEN� VARSA BU KULLANICIYA A�T VER�LER� �EK 	//

elseif ( (isset($_GET['kim'])) AND ($_GET['kim'] != '') )
{
	if (( strlen($_GET['kim']) > 20))
	{
		header('Location: hata.php?hata=72');
		exit();
	}

	$_GET['kim'] = @zkTemizle($_GET['kim']);

	$strSQL = "SELECT id,kullanici_adi,gercek_ad,dogum_tarihi_goster,dogum_tarihi,sehir_goster,sehir,posta_goster,posta,web,katilim_tarihi,mesaj_sayisi,resim,imza,yetki,engelle,gizli,son_hareket,kul_etkin,hangi_sayfada,sayfano,grupid,icq,msn,yahoo,aim,skype,ozel_ad
	FROM $tablo_kullanicilar WHERE kullanici_adi='$_GET[kim]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
	$satir = mysql_fetch_array($sonuc);

	if (empty($satir['kullanici_adi']))
	{
		header('Location: hata.php?hata=46');
		exit();
	}


	$sayfano = '4,'.$satir['id'];
	$sayfa_adi = 'Profil G�r�nt�leme: '.$satir['kullanici_adi'];
	$sayfa_baslik2 = 'Kullan�c� Profili';
}



//	$U ve $K�M DE���KEN� YOKSA KULLANICININ KEND� PROF�L�N� �EK	//

else
{
	if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';

	$_COOKIE['kullanici_kimlik'] = @zkTemizle($_COOKIE['kullanici_kimlik']);

	$strSQL = "SELECT id,kullanici_adi,gercek_ad,dogum_tarihi_goster,dogum_tarihi,sehir_goster,sehir,posta_goster,posta,web,katilim_tarihi,mesaj_sayisi,resim,imza,yetki,engelle,gizli,son_hareket,kul_etkin,hangi_sayfada,sayfano,grupid,icq,msn,yahoo,aim,skype,ozel_ad,okunmamis_oi
	FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL);
	$satir = mysql_fetch_array($sonuc) or die ('<h2>Sorgu ba�ar�s�z</h2>');

	$sayfano = '4,'.$satir['id'];
	$sayfa_adi = 'Profil G�r�nt�le';
	$sayfa_baslik2 = 'Profilim';
}




// SEO ADRES�N�N DO�RULU�U KONTROL ED�L�YOR YANLI�SA DO�RU ADRESE Y�NLEND�R�L�YOR //

$dogru_adres = seoyap($satir['kullanici_adi']);

if ( (isset($_SERVER['REQUEST_URI'])) AND ($_SERVER['REQUEST_URI'] != '') AND (!@preg_match("/-$dogru_adres.html/i", $_SERVER['REQUEST_URI'])) AND (!@preg_match('/profil\.php/i', $_SERVER['REQUEST_URI'])) )
{
    $yonlendir = linkver('profil.php?u='.$satir['id'].'&kim='.$satir['kullanici_adi'],$satir['kullanici_adi']);
    header('Location:'.$yonlendir);
    exit();
}



include 'baslik.php';
include 'hangi_sayfada.php';


//	TEMA SINIFI �RNE�� OLU�TURULUYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/profil.html');



$blmyrd_yetki = '';

if ($satir['id'] == 1) $uye_yetki = '<font class="kurucu">'.$ayarlar['kurucu'].'</font>';

elseif ($satir['yetki'] == 1) $uye_yetki = '<font class="yonetici">'.$ayarlar['yonetici'].'</font>';

elseif ($satir['yetki'] == 2) $uye_yetki = '<font class="yardimci">'.$ayarlar['yardimci'].'</font>';

// b�l�m yard�mc�s�
elseif ($satir['yetki'] == 3)
{
	$uye_yetki = '<font class="blm_yrd">'.$ayarlar['blm_yrd'].'</font>';

	if ($satir['grupid'] != '0') $grupek = "grup='$satir[grupid]' AND yonetme='1' OR";
	else $grupek = "grup='0' AND";

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$satir[kullanici_adi]' AND yonetme='1' ORDER BY fno DESC";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	while ($ozelizinler_satir = mysql_fetch_array($sonuc2))
	{
		$strSQL3 = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE id='$ozelizinler_satir[fno]' LIMIT 1";
		$sonuc3 = mysql_query($strSQL3) or die ('<h2>sorgu ba�ar�s�z</h2>');
		$forum_satir = mysql_fetch_array($sonuc3);

		$blmyrd_yetki .= '<a href="'.linkver('forum.php?f='.$forum_satir['id'], $forum_satir['forum_baslik']).'">'.$forum_satir['forum_baslik'].'</a><br>';
	}
}

else $uye_yetki = '<font class="kullanici">'.$ayarlar['kullanici'].'</font>';



//	grup �yeli�i varsa grubun bilgileri �ekiliyor
if ($satir['grupid'] != 0)
{
	$strSQL = "SELECT grup_adi,gizle FROM $tablo_gruplar WHERE id='$satir[grupid]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
	$grup_satir = mysql_fetch_assoc($sonuc);

	// grup gizli de�ilse
	if ($grup_satir['gizle'] != '1') $ornek1->kosul('4', array('{UYE_GRUBU}' => '<a href="uyeler.php?kip=grup">'.$grup_satir['grup_adi'].'</a>'), true);

	else $ornek1->kosul('4', array('' => ''), false);
}

else $ornek1->kosul('4', array('' => ''), false);



if ($satir['dogum_tarihi_goster'] == 1) $uye_dogum = $satir['dogum_tarihi'];
else $uye_dogum = '<b>Gizli</b>';



if ($satir['sehir_goster'] == 1) $uye_sehir = $satir['sehir'];
else $uye_sehir = '<b>Gizli</b>';



if ($satir['posta_goster'] == 1) $uye_eposta = '<a title="Forum �zerinden e-posta g�nder" href="eposta.php?kim='.$satir['kullanici_adi'].'">'.$satir['posta'].'</a>';
else $uye_eposta = '<a title="Forum �zerinden e-posta g�nder" href="eposta.php?kim='.$satir['kullanici_adi'].'">E-Posta G�nder</a>';



$uye_oi = '<a href="oi_yaz.php?ozel_kime='.$satir['kullanici_adi'].'">�zel ileti G�nder</a>';



if ($satir['web']) $uye_web = '<a href="'.$satir['web'].'" target="_blank">'.$satir['web'].'</a>';
else $uye_web = '';



$uye_katilim = zonedate('d-m-Y', $ayarlar['saat_dilimi'], false, $satir['katilim_tarihi']);



if ( (isset($_GET['kim'])) OR (isset($_GET['u'])) )
{
	$konu_goster = '<a href="km_ara.php?kip=mesaj&amp;kim='.$satir['kullanici_adi'].'">A�t��� Konular� G�ster</a>';
	$cevap_goster = '<a href="km_ara.php?kip=cevap&amp;kim='.$satir['kullanici_adi'].'">Yazd��� Cevaplar� G�ster</a>';
	$mesaj_ara = '<a href="arama.php?a=1&amp;b=1&amp;forum=tum&amp;yazar_ara='.$satir['kullanici_adi'].'">T�m yazd�klar�nda Arama Yap</a>';

    $ornek1->kosul('2', array('' => ''), false);
}


else
{
	$konu_goster = '<a href="km_ara.php?kip=mesaj&amp;kim='.$satir['kullanici_adi'].'">A�t���m Konular� G�ster</a>';
	$cevap_goster = '<a href="km_ara.php?kip=cevap&amp;kim='.$satir['kullanici_adi'].'">Yazd���m Cevaplar� G�ster</a>';
	$mesaj_ara = '<a href="arama.php?a=1&amp;b=1&amp;forum=tum&amp;yazar_ara='.$satir['kullanici_adi'].'">T�m yazd�klar�mda Arama Yap</a>';

    // okunmam�� �zel iletisi varsa
    if ($ayarlar['o_ileti'] == 1)
    {
        if ($kullanici_kim['okunmamis_oi'])
            $okunmamis_oi = ' ('.$kullanici_kim['okunmamis_oi'].')';
        else $okunmamis_oi = '';
    }

    else $okunmamis_oi = '';

    $ornek1->kosul('2', array('{OKUNMAMIS_OI}' => $okunmamis_oi), true);
}



if ($satir['kul_etkin'] != 1)
$uye_durum = '<font color="#FF0000">hesap etkinle�tirilmemi�</font>';


elseif ($satir['engelle'] == 1)
$uye_durum = '<font color="#FF0000">�ye uzakla�t�r�lm��</font>';


elseif ($satir['gizli'] == 1)
{
    $uye_durum = '<font color="#FF0000">Gizli</font>';

    if  ( (isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1)
    AND ( ($satir['son_hareket'] + $zaman_asimi) > $tarih )
    AND ($satir['sayfano'] != '-1') )
    $uye_durum .= ' &nbsp; (Forumda)';

    elseif ( (isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1) )
    $uye_durum .= ' &nbsp; (Forumda De�il)';
}


elseif ( (($satir['son_hareket'] + $zaman_asimi) > $tarih) AND ($satir['sayfano'] != '-1') )
$uye_durum = '<font color="#339900">Forumda</font>';


else $uye_durum = '<font color="#FF0000">Forumda De�il</font>';




if ( (isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1)
    AND ($satir['gizli'] == 1) AND ($satir['son_hareket'] != 0) )
$uye_giris = zonedate('d-m-Y- H:i', $ayarlar['saat_dilimi'], false, $satir['son_hareket']);


elseif ($satir['gizli'] == 1) $uye_giris = '<b>Gizli</b>';


elseif ($satir['son_hareket'] != 0)
$uye_giris = zonedate('d-m-Y- H:i', $ayarlar['saat_dilimi'], false, $satir['son_hareket']);

else $uye_giris = '';




if ((isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1))
{
    $uye_sayfa = HangiSayfada($satir['sayfano'], $satir['hangi_sayfada']);
}


elseif ($satir['gizli'] == 1) $uye_sayfa = '<b>Gizli</b>';


else
{
    if (@preg_match('/^Y�netim/', $satir['hangi_sayfada']))
        $uye_sayfa = 'Y�netim Sayfalar�';

    else $uye_sayfa = HangiSayfada($satir['sayfano'], $satir['hangi_sayfada']);
}




if ($satir['icq'] != '') $uye_icq = '<a href="http://wwp.icq.com/scripts/search.dll?to='.$satir['icq'].'">'.$satir['icq'].'</a>';
else $uye_icq = '';



if ($satir['aim'] != '') $uye_aim = '<a href="aim:goim?screenname='.$satir['aim'].'&amp;message=Merhaba+Burdam&#305;s&#305;n&#305;z?">'.$satir['aim'].'</a>';
else $uye_aim = '';



if ($satir['msn'] != '')
$uye_msn = '<a href="http://members.msn.com/'.$satir['msn'].'" target="_blank">'.$satir['msn'].'</a>';
else $uye_msn = '';



if ($satir['yahoo'] != '') $uye_yahoo = '<a href="http://members.yahoo.com/interests?.oc=t&amp;.kw='.$satir['yahoo'].'&amp;.sb=1">'.$satir['yahoo'].'</a>';
else $uye_yahoo = '';



if ($satir['skype'] != '') $uye_skype = '<a href="skype:'.$satir['skype'].'?userinfo">'.$satir['skype'].'</a>';
else $uye_skype = '';



if ($satir['resim'] != '') $uye_resim = '<img src="'.$satir['resim'].'" alt="Kullan�c� Resmi">';
elseif ($ayarlar['kul_resim'] != '') $uye_resim = '<img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi">';
else $uye_resim = '';



if ( (isset($satir['imza'])) AND ($satir['imza'] != '') )
{
	if ($ayarlar['bbcode'] == 1) $uye_imza = bbcode_acik(ifadeler($satir['imza']),0);
	else $uye_imza = bbcode_kapali(ifadeler($satir['imza']));
}


else $uye_imza = '';





//	TEMA UYGULANIYOR	//

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => $sayfa_baslik2,
'{UYE_ADI}' => $satir['kullanici_adi'],
'{UYE_YETKI}' => $uye_yetki,
'{UYE_GERCEK_AD}' => $satir['gercek_ad'],
'{UYE_DOGUM}' => $uye_dogum,
'{UYE_SEHIR}' => $uye_sehir,
'{UYE_EPOSTA}' => $uye_eposta,
'{UYE_OI}' => $uye_oi,
'{UYE_WEB}' => $uye_web,
'{UYE_KATILIM}' => $uye_katilim,
'{UYE_MESAJ_SAYISI}' => NumaraBicim($satir['mesaj_sayisi']),
'{KONU_GOSTER}' => $konu_goster,
'{CEVAP_GOSTER}' => $cevap_goster,
'{MESAJ_ARA}' => $mesaj_ara,
'{UYE_DURUM}' => $uye_durum,
'{UYE_GIRIS}' => $uye_giris,
'{SON_SAYFA}' => $uye_sayfa,
'{UYE_ICQ}' => $uye_icq,
'{UYE_AIM}' => $uye_aim,
'{UYE_MSN}' => $uye_msn,
'{UYE_YAHOO}' => $uye_yahoo,
'{UYE_SKYPE}' => $uye_skype,
'{UYE_RESIM}' => $uye_resim,
'{UYE_IMZA}' => $uye_imza));


if ( (isset($satir['ozel_ad']))  AND ($satir['ozel_ad'] != '') )
	$ornek1->kosul('1', array('{OZEL_AD}' => $satir['ozel_ad']), true);

else $ornek1->kosul('1', array('' => ''), false);


if ($blmyrd_yetki != '')
	$ornek1->kosul('3', array('{BLMYRD_YETKI}' => $blmyrd_yetki), true);

else $ornek1->kosul('3', array('' => ''), false);


if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>