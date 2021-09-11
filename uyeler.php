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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
if (!defined('DOSYA_SEO')) include 'seo.php';


//		GRUPLAR SIRALANIYOR		//
//		GRUPLAR SIRALANIYOR		//
//		GRUPLAR SIRALANIYOR		//

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == 'grup') ):

$sbaslik = 'FORUM YETK�L�LER�';
$gbaslik = '�YE GRUPLARI';
$sayfano = 42;
$sayfa_adi = 'Yetkililer ve Gruplar';
include 'baslik.php';



//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/uyeler.html');

// site kurucusu bilgileri �ekiliyor
$strSQL = "SELECT id,kullanici_adi,gercek_ad,resim,mesaj_sayisi,katilim_tarihi,sehir_goster,sehir,mesaj_sayisi FROM $tablo_kullanicilar WHERE id='1' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$kurucu = mysql_fetch_assoc($sonuc);

// forum y�neticileri bilgileri �ekiliyor
$strSQL = "SELECT id,kullanici_adi,resim,mesaj_sayisi,katilim_tarihi,sehir_goster,sehir,mesaj_sayisi FROM $tablo_kullanicilar WHERE yetki='1' AND id!='1' ORDER BY id";
$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

// forum yard�mc�lar� bilgileri �ekiliyor
$strSQL = "SELECT id,kullanici_adi,resim,mesaj_sayisi,katilim_tarihi,sehir_goster,sehir,mesaj_sayisi FROM $tablo_kullanicilar WHERE yetki='2' ORDER BY id";
$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

// b�l�m yard�mc�lar� bilgileri �ekiliyor
$strSQL = "SELECT id,kullanici_adi,resim,mesaj_sayisi,katilim_tarihi,sehir_goster,sehir,mesaj_sayisi FROM $tablo_kullanicilar WHERE yetki='3' ORDER BY id";
$sonuc4 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

// Gruplar�n bilgileri �ekiliyor
$strSQL = "SELECT * FROM $tablo_gruplar where gizle='0' ORDER BY sira";
$sonuc5 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');




//	S�TE KURUCUSU	//

$kurucubag = linkver('profil.php?u='.$kurucu['id'].'&kim='.$kurucu['kullanici_adi'],$kurucu['kullanici_adi']);

if ($kurucu['resim'] != '') $kurucu_resim = '<a href="'.$kurucubag.'"><img src="'.$kurucu['resim'].'" alt="Kullan�c� Resmi" border="0" width="65"></a>';
elseif ($ayarlar['kul_resim'] != '') $kurucu_resim = '<a href="'.$kurucubag.'"><img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi" border="0" width="65"></a>';
else $kurucu_resim = '';

$kurucu_bilgi = '<span style="float:left;width:100%;margin:1px;">&nbsp;
<b>�ye Ad�:</b> <a href="'.$kurucubag.'" style="text-decoration:none">'.$kurucu['kullanici_adi'].'</a>&nbsp; ('.$kurucu['gercek_ad'].')</span>
<br><span style="float:left;width:100%;margin:1px;">&nbsp;
<b>Kay�t Tarihi:</b> '.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, $kurucu['katilim_tarihi']).'</span>
<br><span style="float:left;width:100%;margin:1px;">&nbsp;
<b>�leti Say�s�: </b> '.$kurucu['mesaj_sayisi'].'</span>
<br><span style="float:left;width:100%;margin:1px;">&nbsp;
<b>�ehir:</b> '.$kurucu['sehir'].'</span>';



//	FORUM Y�NET�C�LER�	//

if (mysql_num_rows($sonuc2))
{
	while ($yonetici = mysql_fetch_assoc($sonuc2))
	{
		$yonetbag = linkver('profil.php?u='.$yonetici['id'].'&kim='.$yonetici['kullanici_adi'],$yonetici['kullanici_adi']);

		if ($yonetici['resim'] != '') $yonetici_resim = '<a href="'.$yonetbag.'"><img src="'.$yonetici['resim'].'" alt="Kullan�c� Resmi" border="0" width="45"></a>';
		elseif ($ayarlar['kul_resim'] != '') $yonetici_resim = '<a href="'.$yonetbag.'"><img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi" border="0" width="45"></a>';
		else $yonetici_resim = '';

		$yonetici_bilgi = '<span style="float:left;width:100%;margin:1px;">
<b>�ye Ad�:</b> <a href="'.$yonetbag.'" style="text-decoration:none">'.$yonetici['kullanici_adi'].'</a></span>
<br><span style="float:left;width:100%;margin:1px;">
<b>Kay�t Tarihi:</b> '.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, $yonetici['katilim_tarihi']).'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�leti Say�s�: </b> '.$yonetici['mesaj_sayisi'].'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�ehir:</b> '.$yonetici['sehir'].'</span>';


		$tekli2[] = array('{YONETICI_RESIM}' => $yonetici_resim,
		'{YONETICI_BILGI}' => $yonetici_bilgi);
	}
}

else
{
	$tekli2[] = array('{YONETICI_RESIM}' => '',
	'{YONETICI_BILGI}' => '&nbsp;'.$ayarlar['yonetici'].' Yok');
}



//	FORUM YARDIMCILARI	//

if (mysql_num_rows($sonuc3))
{
	while ($yardimci = mysql_fetch_assoc($sonuc3))
	{
		$yardimbag = linkver('profil.php?u='.$yardimci['id'].'&kim='.$yardimci['kullanici_adi'],$yardimci['kullanici_adi']);

		if ($yardimci['resim'] != '') $yardimci_resim = '<a href="'.$yardimbag.'"><img src="'.$yardimci['resim'].'" alt="Kullan�c� Resmi" border="0" width="45"></a>';
		elseif ($ayarlar['kul_resim'] != '') $yardimci_resim = '<a href="'.$yardimbag.'"><img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi" border="0" width="45"></a>';
		else $yardimci_resim = '';

		$yardimci_bilgi = '<span style="float:left;width:100%;margin:1px;">
<b>�ye Ad�:</b> <a href="'.$yardimbag.'" style="text-decoration:none">'.$yardimci['kullanici_adi'].'</a></span>
<br><span style="float:left;width:100%;margin:1px;">
<b>Kay�t Tarihi:</b> '.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, $yardimci['katilim_tarihi']).'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�leti Say�s�: </b> '.$yardimci['mesaj_sayisi'].'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�ehir:</b> '.$yardimci['sehir'].'</span>';


		$tekli3[] = array('{YARDIMCI_RESIM}' => $yardimci_resim,
		'{YARDIMCI_BILGI}' => $yardimci_bilgi);
	}
}

else
{
	$tekli3[] = array('{YARDIMCI_RESIM}' => '',
	'{YARDIMCI_BILGI}' =>  '&nbsp;'.$ayarlar['yardimci'].' Yok');
}



//	B�L�M YARDIMCILARI	//

if (mysql_num_rows($sonuc4))
{
	while ($byardimci = mysql_fetch_assoc($sonuc4))
	{
		$byardimbag = linkver('profil.php?u='.$byardimci['id'].'&kim='.$byardimci['kullanici_adi'],$byardimci['kullanici_adi']);

		if ($byardimci['resim'] != '') $blm_yrd_resim = '<a href="'.$byardimbag.'"><img src="'.$byardimci['resim'].'" alt="Kullan�c� Resmi" border="0" width="45"></a>';
		elseif ($ayarlar['kul_resim'] != '') $blm_yrd_resim = '<a href="'.$byardimbag.'"><img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi" border="0" width="45"></a>';
		else $blm_yrd_resim = '';

		$blm_yrd_bilgi = '<span style="float:left;width:100%;margin:1px;">
<b>�ye Ad�:</b> <a href="'.$byardimbag.'" style="text-decoration:none">'.$byardimci['kullanici_adi'].'</a></span>
<br><span style="float:left;width:100%;margin:1px;">
<b>Kay�t Tarihi:</b> '.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, $byardimci['katilim_tarihi']).'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�leti Say�s�: </b> '.$byardimci['mesaj_sayisi'].'</span>
<br><span style="float:left;width:100%;margin:1px;">
<b>�ehir:</b> '.$byardimci['sehir'].'</span>';


		$tekli4[] = array('{BLM_YRD_RESIM}' => $blm_yrd_resim,
		'{BLM_YRD_BILGI}' => $blm_yrd_bilgi);
	}
}

else
{
	$tekli4[] = array('{BLM_YRD_RESIM}' => '',
	'{BLM_YRD_BILGI}' => '&nbsp;'.$ayarlar['blm_yrd'].' Yok');
}





//	GRUP �YELER�	//

$tablosayi = 0;

// GRUPLAR SIRALANIYOR

if (mysql_num_rows($sonuc5)):


// t�m forumlar�n bilgileri �ekiliyor
$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar ORDER BY id";
$sonuc7 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

// forumlar�n ba�l�klar� diziye aktar�l�yor
while ($forumlar = mysql_fetch_assoc($sonuc7))
$tumforumlar[$forumlar['id']] = $forumlar['forum_baslik'];


while ($gruplar = mysql_fetch_assoc($sonuc5))
{
	if ($gruplar['ozel_ad'] != '') $gozel_ad = $gruplar['ozel_ad'];
	else $gozel_ad = 'Yok';

	if ($gruplar['yetki'] == '-1') $gyetki = 'Yok';
	elseif ($gruplar['yetki'] == 0) $gyetki = $ayarlar['kullanici'];
	elseif ($gruplar['yetki'] == 2) $gyetki = $ayarlar['yardimci'];
	elseif ($gruplar['yetki'] == 1) $gyetki = $ayarlar['yonetici'];
	elseif ($gruplar['yetki'] == 3)
	{
		$gyetki = $ayarlar['blm_yrd'].'<br><span style="float:left;width:100%;margin:1px;"><b>Yekili Oldu�u B�l�mler:</b></span>';

		// grubun yetkisine �ekiliyor
		$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$gruplar[id]' AND yonetme='1' ORDER BY fno";
		$sonuc8 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		// yetkili oldu�u forumlar s�ralan�yor
		while ($oforumlar = mysql_fetch_assoc($sonuc8))
			$gyetki .= '<br><span style="float:left;width:100%;margin:1px;">
			<a href="'.linkver('forum.php?f='.$oforumlar['fno'], $tumforumlar[$oforumlar['fno']]).'">'.$tumforumlar[$oforumlar['fno']].'</a></span>';
	}


	// grup �yeleri s�ralan�yor
	$guyed = explode(',', $gruplar['uyeler']);


	if (count($guyed) > 1)
	{
		foreach ($guyed as $guye)
		{
			if ($guye == '') continue;
			$strSQL = "SELECT kulid FROM $tablo_ozel_izinler WHERE kulid='$guye' AND yonetme='1'";
			$yardimcilik = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			// grup �yelerinin bilgileri �ekiliyor
			$strSQL = "SELECT id,kullanici_adi,resim,mesaj_sayisi,katilim_tarihi,sehir_goster,sehir,mesaj_sayisi FROM $tablo_kullanicilar WHERE id='$guye' LIMIT 1";
			$sonuc6 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$guye = mysql_fetch_assoc($sonuc6);


			$gbag = linkver('profil.php?u='.$guye['id'].'&kim='.$guye['kullanici_adi'],$guye['kullanici_adi']);

			if ($guye['resim'] != '') $grup_resim = '<a href="'.$gbag.'"><img src="'.$guye['resim'].'" alt="Kullan�c� Resmi" border="0" width="45"></a>';
			elseif ($ayarlar['kul_resim'] != '') $grup_resim = '<a href="'.$gbag.'"><img src="'.$ayarlar['kul_resim'].'" alt="Varsay�lan Kullan�c� Resmi" border="0" width="45"></a>';
			else $grup_resim = '';

			$grup_uye = '<span style="float:left;width:100%;margin:1px;"></span><span style="float:left;width:100%;margin:1px;">
			<b>�ye Ad�:</b> <a href="'.$gbag.'" style="text-decoration:none">'.$guye['kullanici_adi'].'</a></span>
			<br><span style="float:left;width:100%;margin:1px;">
			<b>Kay�t Tarihi:</b> '.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, $guye['katilim_tarihi']).'</span>
			<br><span style="float:left;width:100%;margin:1px;">
			<b>�leti Say�s�: </b> '.$guye['mesaj_sayisi'].'</span>
			<br><span style="float:left;width:100%;margin:1px;">
			<b>�ehir:</b> '.$guye['sehir'].'</span>';

			$tema_ic[$tablosayi][] = array(	'{GRUP_RESIM}' => $grup_resim,
			'{GRUP_UYE}' => $grup_uye);
		}
	}

	else
	{
		$tema_ic[$tablosayi][] = array('{GRUP_RESIM}' => '',
		'{GRUP_UYE}' => '&nbsp;Grupta Hi�bir �ye Yok');
	}


	$grup_bilgi = '<span style="float:left;width:100%;margin:1px;"></span>
	<span style="float:left;width:100%;margin:1px;">
	<b>�ye Say�s�: </b>'.(count($guyed)-1).'</span>
	<br><span style="float:left;width:100%;margin:1px;">
	<b>A��klama:</b> '.$gruplar['grup_bilgi'].'</span>
	<br><span style="float:left;width:100%;margin:1px;">
	<b>�zel Ad:</b> '.$gozel_ad.'</span>
	<br><span style="float:left;width:100%;margin:1px;">
	<b>Yetki:</b> '.$gyetki.'</span>
	<span style="float:left;width:100%;margin:1px;"></span>';



	if ( ($tablosayi != 0) AND ($tablosayi % 3) == 0)
	$asagiat = '<div style="float:left; width:100%; height:1px;"></div>';
	else $asagiat = '';

	$tema_dis[] = array('{GRUP_ADI}' => $gruplar['grup_adi'],
	'{GRUP_BILGI}' => $grup_bilgi,
	'{ASAGI_AT}' => $asagiat);
	$tablosayi++;
}

$grup_yok = '';



else:
	$ornek1->kosul('5', array(''=>''), false);

	$grup_yok = '<div style="float:left; width:100%; height:30px;"></div><div align="center" style="float:left; width:100%;">Forumda Hi�bir Grup Yok</div>';

	$tema_dis[] = array('{GRUP_ADI}' => '',
	'{GRUP_BILGI}' => '',
	'{ASAGI_AT}' => '');

	$tema_ic[0][] = array('{GRUP_RESIM}' => '',
	'{GRUP_UYE}' => '');

endif;



		//	veriler tema motoruna yollan�yor	//

$kosul4 = array('{KURUCU_BASLIK}' => $ayarlar['kurucu'],
'{YONETICI_BASLIK}' => $ayarlar['yonetici'],
'{YARDIMCI_BASLIK}' => $ayarlar['yardimci'],
'{BLM_YRD_BASLIK}' => $ayarlar['blm_yrd'],
'{KURUCU_RESIM}' => $kurucu_resim,
'{KURUCU_BILGI}' => $kurucu_bilgi,
'{GRUP_BASLIK}' => $gbaslik,
'{GRUP_YOK}' => $grup_yok);

$siralama_secenek ='';
$sayfalama = '';
$satir_sayi = 0;

$ornek1->kosul('1', array(''=>''), false);
$ornek1->kosul('2', array(''=>''), false);
$ornek1->kosul('3', array(''=>''), false);
$ornek1->kosul('4', $kosul4, true);

if ( (isset($tema_dis)) AND (isset($tema_ic)) )
	$ornek1->icice_dongu('1', $tema_dis, $tema_ic);

$ornek1->tekli_dongu('2',$tekli2);
$ornek1->tekli_dongu('3',$tekli3);
$ornek1->tekli_dongu('4',$tekli4);






//		�YELER SIRALANIYOR		//
//		�YELER SIRALANIYOR		//
//		�YELER SIRALANIYOR		//


else:

//	DE�ERLER YOKSA SIFIRLANIYOR

$uyeler_kota = 30;

if (empty($_GET['sayfa'])) {$_GET['sayfa'] = 0; $baslik_ek = '';}
else
{
    $_GET['sayfa'] = @zkTemizle($_GET['sayfa']);
    $_GET['sayfa'] = @str_replace(array('-','x'), '', $_GET['sayfa']);
    if (is_numeric($_GET['sayfa']) == false) $_GET['sayfa'] = 0;
    if ($_GET['sayfa'] < 0) $_GET['sayfa'] = 0;
    $baslik_ek = ' - Sayfa '.(($_GET['sayfa']/$uyeler_kota)+1);
}


$sbaslik = '�YELER';
$sayfano = 7;
$sayfa_adi = '�yeler'.$baslik_ek;


if (empty($_GET['sirala'])) $_GET['sirala'] = 1;
else $_GET['sirala'] = @zkTemizle4(@zkTemizle($_GET['sirala']));


if (empty($_GET['kul_ara'])) $_GET['kul_ara'] = '%';
else
{
	$_GET['kul_ara'] = @zkTemizle4(@zkTemizle($_GET['kul_ara']));
	$_GET['kul_ara'] = @str_replace('*','%',trim($_GET['kul_ara']));
}


if (( strlen($_GET['kul_ara']) >  20))
{
	header('Location: hata.php?hata=19');
	exit();
}


include 'baslik.php';


//	SORGU SONUCUNDAK� TOPLAM SONU� SAYISI ALINIYOR	//


$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE kullanici_adi LIKE '$_GET[kul_ara]%'");
$satir_sayi = mysql_num_rows($result);

$toplam_sayfa = ($satir_sayi / $uyeler_kota);
settype($toplam_sayfa,'integer');

if ( ($satir_sayi % $uyeler_kota) != 0 ) $toplam_sayfa++;



//	�YELER�N B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT id,kullanici_adi,mesaj_sayisi,katilim_tarihi,yetki,sehir_goster,sehir,engelle FROM $tablo_kullanicilar WHERE kullanici_adi LIKE '$_GET[kul_ara]%' ORDER BY ";

if ($_GET['sirala'] == 'mesaj_0dan9a') $strSQL .= "mesaj_sayisi LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'mesaj_9dan0a') $strSQL .= "mesaj_sayisi DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'katilim_9dan0a') $strSQL .= "id DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'ad_AdanZye') $strSQL .= "kullanici_adi LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'ad_ZdenAya') $strSQL .= "kullanici_adi DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'yetki') $strSQL .= "yetki=0, yetki=3, yetki=2, yetki=1, id LIMIT $_GET[sayfa],$uyeler_kota";
else $strSQL .= "id LIMIT $_GET[sayfa],$uyeler_kota";

$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');



// SIRALAMA SE�ENEKLER� //

$siralama_secenek = '<option value="1">Kat�l�m tarihine g�re
<option value="katilim_9dan0a" ';

if ($_GET['sirala'] == 'katilim_9dan0a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kat�l�m tarihine g�re tersten

<option value="ad_AdanZye" ';
if ($_GET['sirala'] == 'ad_AdanZye') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kullan�c� ad�na g�re A\'dan Z\'ye

<option value="ad_ZdenAya" ';
if ($_GET['sirala'] == 'ad_ZdenAya') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kullan�c� ad�na g�re Z\'den A\'ya

<option value="mesaj_9dan0a" ';
if ($_GET['sirala'] == 'mesaj_9dan0a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>�leti say�s�na g�re

<option value="mesaj_0dan9a" ';
if ($_GET['sirala'] == 'mesaj_0dan9a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>�leti say�s�na g�re tersten

<option value="yetki" ';
if ($_GET['sirala'] == 'yetki') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Yetkisine g�re(Y�neticiler �nde)';




while ($uyeler_satir = mysql_fetch_assoc($sonuc)):



if ($uyeler_satir['id'] == 1)
	$uye_yetki = '<font class="kurucu">'.$ayarlar['kurucu'].'</font>';

elseif ($uyeler_satir['yetki'] == 1)
	$uye_yetki = '<font class="yonetici">'.$ayarlar['yonetici'].'</font>';

elseif ($uyeler_satir['yetki'] == 2)
	$uye_yetki = '<font class="yardimci">'.$ayarlar['yardimci'].'</font>';

elseif ($uyeler_satir['yetki'] == 3)
	$uye_yetki = '<font class="blm_yrd">'.$ayarlar['blm_yrd'].'</font>';

else $uye_yetki = '';



if($uyeler_satir['sehir_goster'] == 1)
	$uye_sehir = $uyeler_satir['sehir'];

else $uye_sehir = 'G�ZL�';



if ($uyeler_satir['engelle'] != 1)
    $uye_adi = '&nbsp;<a href="'.linkver('profil.php?u='.$uyeler_satir['id'].'&kim='.$uyeler_satir['kullanici_adi'],$uyeler_satir['kullanici_adi']).'">'.$uyeler_satir['kullanici_adi'].'</a>';

else $uye_adi = '&nbsp;<a href="'.linkver('profil.php?u='.$uyeler_satir['id'].'&kim='.$uyeler_satir['kullanici_adi'],$uyeler_satir['kullanici_adi']).'"><s>'.$uyeler_satir['kullanici_adi'].'</s></a>';



$uye_katilim = zonedate('d-m-Y', $ayarlar['saat_dilimi'], false, $uyeler_satir['katilim_tarihi']);

$uye_eposta = '<a href="eposta.php?kim='.$uyeler_satir['kullanici_adi'].'">E-Posta</a>';

$uye_ileti = '<a href="oi_yaz.php?ozel_kime='.$uyeler_satir['kullanici_adi'].'">ileti</a>';



//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{UYE_ADI}' => $uye_adi,
'{UYE_YETKISI}' => $uye_yetki,
'{UYE_MESAJ}' => NumaraBicim($uyeler_satir['mesaj_sayisi']),
'{UYE_KATILIM}' => $uye_katilim,
'{UYE_SEHIR}' => $uye_sehir,
'{UYE_EPOSTA}' => $uye_eposta,
'{UYE_OZEL}' => $uye_ileti);


endwhile;



//  SAYFALAMA   //

$sayfalama = '';

if ($satir_sayi > $uyeler_kota):

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
	$sayfalama .= '&nbsp;<a href="uyeler.php?sayfa=0&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&laquo;ilk</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="uyeler.php?sayfa='.($_GET['sayfa'] - $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&lt;</a>&nbsp;</td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['sayfa']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['sayfa'] / $uyeler_kota) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['sayfa'] + 8)) break;
		if (($sayi == 0) and ($_GET['sayfa'] == 0))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
		}

		elseif (($sayi + 1) == (($_GET['sayfa'] / $uyeler_kota) + 1))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}

		else
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">';

			$sayfalama .= '&nbsp;<a href="uyeler.php?sayfa='.($sayi * $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}
if ($_GET['sayfa'] < ($satir_sayi - $uyeler_kota))
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="uyeler.php?sayfa='.($_GET['sayfa'] + $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&gt;</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	$sayfalama .= '&nbsp;<a href="uyeler.php?sayfa='.(($toplam_sayfa - 1) * $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">son&raquo;</a>&nbsp;</td>';
}

$sayfalama .= '</tr>
	</tbody>
</table>';

endif;


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/uyeler.html');


if (isset($tekli1))
{
	$ornek1->kosul('2', array(''=>''), false);
	$ornek1->kosul('1', array(''=>''), true);

	$ornek1->tekli_dongu('1',$tekli1);
}

else
{
	$tekli1[] = array('{UYE_ADI}' => '',
	'{UYE_YETKISI}' => '',
	'{UYE_MESAJ}' => '',
	'{UYE_KATILIM}' => '',
	'{UYE_SEHIR}' => '',
	'{UYE_EPOSTA}' => '',
	'{UYE_OZEL}' => '');

	$ornek1->tekli_dongu('1',$tekli1);

	$ornek1->kosul('2', array('{SONUC_YOK}'=>'Arad���n�z ko�ula uyan �ye yok !'), true);
	$ornek1->kosul('1', array(''=>''), false);
}

$ornek1->kosul('4', array(''=>''), false);


endif;








$ornek1->dongusuz(array('{KULLANICI_ARA}' => @str_replace('%','*',$_GET['kul_ara']),
'{SAYFA_BASLIK}' => $sbaslik,
'{SIRALAMA_SECENEK}' => $siralama_secenek,
'{SAYFALAMA}' => $sayfalama,
'{UYE_SAYISI}' => NumaraBicim($satir_sayi)));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>