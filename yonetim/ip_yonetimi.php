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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';
if (!defined('DOSYA_SEO')) include '../seo.php';
include '../hangi_sayfada.php';


$adim = 30;

if (empty($_GET['sayfa'])) $sayfa = 0;

else
{
	$_GET['sayfa'] = @zkTemizle($_GET['sayfa']);
	$_GET['sayfa'] = @str_replace(array('-','x'), '', $_GET['sayfa']);
	if (is_numeric($_GET['sayfa']) == false) $_GET['sayfa'] = 0;
	if ($_GET['sayfa'] < 0) $_GET['sayfa'] = 0;
	$sayfa = $_GET['sayfa'];
}

// ip adresi kontrol ediliyor

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == '1') )
{
	if ( (!isset($_GET['ip'])) OR ($_GET['ip'] == '') OR (!preg_match('/^[0-9.]+$/', $_GET['ip'])) )
	{
		header('Location: ../hata.php?hata=190');
		exit();
	}

	$_GET['ip'] = zkTemizle3(zkTemizle4($_GET['ip']));
}


// kullan�c� ad� kontrol ediliyor

elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == '2') )
{
	if ( ( strlen($_GET['kim']) < 4) OR ( strlen($_GET['kim']) > 20))
	{
		header('Location: ../hata.php?hata=46');
		exit();
	}

	$_GET['kim'] = zkTemizle3(zkTemizle4(trim($_GET['kim'])));

	$strSQL = "SELECT id,kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$_GET[kim]' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$kullanici = mysql_fetch_array($sonuc);

	if (empty($kullanici))
	{
		header('Location: ../hata.php?hata=46');
		exit();
	}
}




$sayfa_adi = 'Y�netim IP Y�netimi';
include 'yonetim_baslik.php';
$sayfa_baslik = 'IP Y�netimi';


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/ip_yonetimi.html');






//  IP ADRES�NE G�RE ARAMA - BA�I    //

if ( (isset($_GET['kip'])) AND ($_GET['kip'] == '1') ):


$safya_kip = 'kip=1&amp;ip='.$_GET['ip'];


$strSQL = "SELECT id,yazan,degistiren,tarih,degistirme_tarihi,mesaj_baslik,hangi_forumdan as hangi,yazan_ip,degistiren_ip,tarih as tarih2,yazan AS rakam,id AS rakam2
FROM $tablo_mesajlar WHERE yazan_ip='$_GET[ip]'

UNION ALL SELECT id,yazan,degistiren,tarih,degistirme_tarihi,mesaj_baslik,hangi_forumdan as hangi,yazan_ip,degistiren_ip,degistirme_tarihi as tarih2,yazan AS rakam,yazan AS rakam2
FROM $tablo_mesajlar WHERE degistiren_ip='$_GET[ip]'

UNION ALL SELECT id,cevap_yazan as yazan,degistiren,tarih,degistirme_tarihi,cevap_baslik as mesaj_baslik,hangi_basliktan as hangi,yazan_ip,degistiren_ip,tarih as tarih2,id AS rakam,id AS rakam2
FROM $tablo_cevaplar WHERE yazan_ip='$_GET[ip]'

UNION ALL SELECT id,cevap_yazan as yazan,degistiren,tarih,degistirme_tarihi,cevap_baslik as mesaj_baslik,hangi_basliktan as hangi,yazan_ip,degistiren_ip,degistirme_tarihi as tarih2,id AS rakam,cevap_yazan AS rakam2
FROM $tablo_cevaplar WHERE degistiren_ip='$_GET[ip]'

ORDER BY tarih2 DESC";


$sonuc1 = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
$satir_sayi = mysql_num_rows($sonuc1);
$sonuc1 = @mysql_query("$strSQL LIMIT $sayfa,$adim") or die ('<h2>Sorgu ba�ar�s�z</h2>');

$toplam_sayfa = ($satir_sayi / $adim);
settype($toplam_sayfa,'integer');
if ( ($satir_sayi % $adim) != 0 ) $toplam_sayfa++;


$sayfa_aciklama = '<br><div align="center" class="liste-veri" style="text-align: left; width: 699px; height: 18px;">
<a href="ip_yonetimi.php" style="text-decoration: none;"><b>&laquo; &nbsp;ilk sayfas�na geri d�n</b></a></div>';
$sayfa_aciklama3 = '<br><div align="center" class="liste-veri" style="text-align: left; width: 100%; height: 18px;">
<a target="_blank" href="http://www.whois.sc/'.$_GET['ip'].'"><b>'.$_GET['ip'].'</b></a>&nbsp; ip adresi i�in <b>'.$satir_sayi.'</b> sonu� bulundu.</div>';



$strSQL = "SELECT id,kullanici_adi,katilim_tarihi,son_hareket,hangi_sayfada,sayfano FROM $tablo_kullanicilar WHERE kul_ip='$_GET[ip]' ORDER BY son_hareket DESC LIMIT 50";
$sonuc3 = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');

$strSQL = "SELECT giris,son_hareket,hangi_sayfada,sayfano FROM $tablo_oturumlar WHERE kul_ip='$_GET[ip]' ORDER BY son_hareket DESC LIMIT 50";
$sonuc4 = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');



//  KULLANICILAR //

if (mysql_num_rows($sonuc3))
{
	$sira = 1;

	while ($uye = mysql_fetch_assoc($sonuc3))
	{
		$uye_adi = '<a href="../profil.php?u='.$uye['id'].'">'.$uye['kullanici_adi'].'</a>';

		$kayit = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $uye['katilim_tarihi']);
		$son_giris = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $uye['son_hareket']);
		$hsayfa = HangiSayfada($uye['sayfano'], $uye['hangi_sayfada']);
		$hsayfa = str_replace('<a href="', '<a href="../', $hsayfa);


		//	veriler tema motoruna yollan�yor	//

		$tekli2[] = array('{SIRA}' => $sira,
		'{UYE_ADI}' => $uye_adi,
		'{HANGI_SAYFADA}' => $hsayfa,
		'{KAYIT}' => $kayit,
		'{SON_GIRIS}' => $son_giris);

		$sira++;
	}
}


//  M�SAF�RLER //

if (mysql_num_rows($sonuc4))
{
	if (!isset($sira)) $sira = 1;

	while ($uye = mysql_fetch_assoc($sonuc4))
	{
		$uye_adi = 'Misafir';

		$kayit = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $uye['giris']);
		$son_giris = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $uye['son_hareket']);
		$hsayfa = HangiSayfada($uye['sayfano'], $uye['hangi_sayfada']);
		$hsayfa = str_replace('<a href="', '<a href="../', $hsayfa);


		//	veriler tema motoruna yollan�yor	//

		$tekli2[] = array('{SIRA}' => $sira,
		'{UYE_ADI}' => $uye_adi,
		'{HANGI_SAYFADA}' => $hsayfa,
		'{KAYIT}' => $kayit,
		'{SON_GIRIS}' => $son_giris);

		$sira++;
	}
}



//  YAPILAN ��LMELER SIRALANIYOR //

if (mysql_num_rows($sonuc1))
{
	$sira = $sayfa+1;

	while ($konular = mysql_fetch_assoc($sonuc1))
	{
		// bulunan cevap ise
		if (is_numeric($konular['rakam']))
		{
			// cevab�n konusunun bilgileri �ekiliyor
			$strSQL = "SELECT id,mesaj_baslik FROM $tablo_mesajlar WHERE id='$konular[hangi]'";
			$sonuc12 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$konular2 = mysql_fetch_assoc($sonuc12);


			// cevab�n ka��nc� s�rada oldu�u hesaplan�yor
			$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$konular[hangi]' AND id < $konular[id]") or die ('<h2>sorgu ba�ar�s�z</h2>');
			$cavabin_sirasi = mysql_num_rows($result);

			$sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
			settype($sayfaya_git,'integer');
			$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

			if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
			else $sayfaya_git = '';


			// ba�lant�lar olu�turuluyor
			$konu_baslik = '<a href="../konu.php?k='.$konular2['id'].$sayfaya_git.'#c'.$konular['id'].'">'.$konular2['mesaj_baslik'].' &raquo; '.$konular['mesaj_baslik'].'</a>';
			$yazan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';

			$cevap_tarihi = '<center>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']).'</center>';


			if (is_numeric($konular['rakam2']))
			{
				$islem = 'Cevap Yazma';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']);
				$ip_adresi = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';
			}

			else
			{
				$islem = 'Cevap De�i�tirme';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['degistirme_tarihi']);
				$ip_adresi = '<a href="../profil.php?kim='.$konular['degistiren'].'">'.$konular['degistiren'].'</a>';
			}
		}



		// bulunan konu ise
		else
		{
			$konu_baslik = '<a href="../konu.php?k='.$konular['id'].'">'.$konular['mesaj_baslik'].'</a>';
			$yazan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';


			if (is_numeric($konular['rakam2']))
			{
				$islem = 'Konu A�ma';
				$tarih =  zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']);
				$ip_adresi = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';
			}

			else
			{
				$islem = 'Konu De�i�tirme';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['degistirme_tarihi']);
				$ip_adresi = '<a href="../profil.php?kim='.$konular['degistiren'].'">'.$konular['degistiren'].'</a>';
			}
		}


		//	veriler tema motoruna yollan�yor	//

		$tekli1[] = array('{SIRA}' => $sira,
		'{KONU_BASLIK}' => $konu_baslik,
		'{YAZAN}' => $yazan,
		'{IP_DEGISTIREN2}' => $ip_adresi,
		'{ISLEM}' => $islem,
		'{TARIH}' => $tarih);

		$sira++;
	}
}




$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('3', array('{KAYIT_IP}' => 'Kay�t Tarihi', '{YAZAN_IP}' => 'Yazan', '{IP_DEGISTIREN}' => '��lem Yapan','{UYE_MISAFIR}' => '�yeler ve Misafirler', '{KONULAR_CEVAPLAR}' => 'Yaz�lan veya De�i�tirilen Son 50 Konu ve Cevap', '{SAYFA_ACIKLAMA3}' => $sayfa_aciklama3), true);


if (isset($tekli1))
{
	$ornek1->tekli_dongu('1',$tekli1);
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), true);
}
else
{
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('' => ''), true);
}


if (isset($tekli2))
{
	$ornek1->tekli_dongu('2',$tekli2);
	$ornek1->kosul('5', array('' => ''), false);
	$ornek1->kosul('6', array('' => ''), true);
}
else
{
	$ornek1->kosul('6', array('' => ''), false);
	$ornek1->kosul('5', array('' => ''), true);
}



//  IP ADRES�NE G�RE ARAMA - SONU    //








//  KULLANICI ADINA G�RE ARAMA - BA�I   //

elseif ( (isset($_GET['kip'])) AND ($_GET['kip'] == '2') ):


$safya_kip = 'kip=2&amp;kim='.$_GET['kim'];

$strSQL = "SELECT id,yazan,degistiren,tarih,degistirme_tarihi,mesaj_baslik,hangi_forumdan as hangi,yazan_ip,degistiren_ip,tarih as tarih2,yazan AS rakam,id AS rakam2
FROM $tablo_mesajlar WHERE yazan='$kullanici[kullanici_adi]'

UNION ALL SELECT id,yazan,degistiren,tarih,degistirme_tarihi,mesaj_baslik,hangi_forumdan as hangi,yazan_ip,degistiren_ip,degistirme_tarihi as tarih2,yazan AS rakam,yazan AS rakam2
FROM $tablo_mesajlar WHERE degistiren='$kullanici[kullanici_adi]'

UNION ALL SELECT id,cevap_yazan as yazan,degistiren,tarih,degistirme_tarihi,cevap_baslik as mesaj_baslik,hangi_basliktan as hangi,yazan_ip,degistiren_ip,tarih as tarih2,id AS rakam,id AS rakam2
FROM $tablo_cevaplar WHERE cevap_yazan='$kullanici[kullanici_adi]'

UNION ALL SELECT id,cevap_yazan as yazan,degistiren,tarih,degistirme_tarihi,cevap_baslik as mesaj_baslik,hangi_basliktan as hangi,yazan_ip,degistiren_ip,degistirme_tarihi as tarih2,id AS rakam,cevap_yazan AS rakam2
FROM $tablo_cevaplar WHERE degistiren='$kullanici[kullanici_adi]'

ORDER BY tarih2 DESC";


$sonuc1 = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
$satir_sayi = mysql_num_rows($sonuc1);
$sonuc1 = @mysql_query("$strSQL LIMIT $sayfa,$adim") or die ('<h2>Sorgu ba�ar�s�z</h2>');

$toplam_sayfa = ($satir_sayi / $adim);
settype($toplam_sayfa,'integer');
if ( ($satir_sayi % $adim) != 0 ) $toplam_sayfa++;


$sayfa_aciklama = '<br><div align="center" class="liste-veri" style="text-align: left; width: 699px; height: 18px;">
<a href="ip_yonetimi.php" style="text-decoration: none"><b>&laquo; &nbsp;ilk sayfas�na geri d�n</b></a></div>';

$sayfa_aciklama3 = '<br><div align="center" class="liste-veri" style="text-align: left; width: 100%; height: 18px;"><b>'.
$kullanici['kullanici_adi'].'</b> adl� �ye i�in <b>'.$satir_sayi.'</b> sonu� bulundu.</div>';


$strSQL = "SELECT id,son_hareket,hangi_sayfada,kul_ip,sayfano FROM $tablo_kullanicilar WHERE kullanici_adi='$kullanici[kullanici_adi]' LIMIT 1";
$sonuc3 = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');



//  KULLANICININ B�LG�LER� //

if (mysql_num_rows($sonuc3))
{
	$sira = 1;
	$uye = mysql_fetch_assoc($sonuc3);

	$uye_adi = '<a href="../profil.php?u='.$uye['id'].'">'.$kullanici['kullanici_adi'].'</a>';
	$kayit = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$uye['kul_ip'].'">'.$uye['kul_ip'].'</a>';
	$son_giris = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $uye['son_hareket']);
	$hsayfa = HangiSayfada($uye['sayfano'], $uye['hangi_sayfada']);
	$hsayfa = str_replace('<a href="', '<a href="../', $hsayfa);


	//	veriler tema motoruna yollan�yor	//

	$tekli2[] = array('{SIRA}' => $sira,
	'{UYE_ADI}' => $uye_adi,
	'{HANGI_SAYFADA}' => $hsayfa,
	'{KAYIT}' => $kayit,
	'{SON_GIRIS}' => $son_giris);
}



//  YAPILAN ��LEMLER SIRALANIYOR //

if (mysql_num_rows($sonuc1))
{
	$sira = $sayfa+1;

	while ($konular = mysql_fetch_assoc($sonuc1))
	{
		// bulunan cevap ise
		if (is_numeric($konular['rakam']))
		{
			// cevab�n konusunun bilgileri �ekiliyor
			$strSQL = "SELECT id,mesaj_baslik FROM $tablo_mesajlar WHERE id='$konular[hangi]'";
			$sonuc12 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$konular2 = mysql_fetch_assoc($sonuc12);


			// cevab�n ka��nc� s�rada oldu�u hesaplan�yor
			$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$konular[hangi]' AND id < $konular[id]") or die ('<h2>sorgu ba�ar�s�z</h2>');
			$cavabin_sirasi = mysql_num_rows($result);

			$sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
			settype($sayfaya_git,'integer');
			$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

			if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
			else $sayfaya_git = '';


			// ba�lant�lar olu�turuluyor
			$konu_baslik = '<a href="../konu.php?k='.$konular2['id'].$sayfaya_git.'#c'.$konular['id'].'">'.$konular2['mesaj_baslik'].' &raquo; '.$konular['mesaj_baslik'].'</a>';
			$yazan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';

			$cevap_tarihi = '<center>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']).'</center>';


			if (is_numeric($konular['rakam2']))
			{
				$islem = 'Cevap Yazma';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']);
				$ip_adresi = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$konular['yazan_ip'].'">'.$konular['yazan_ip'].'</a>';
			}

			else
			{
				$islem = 'Cevap De�i�tirme';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['degistirme_tarihi']);
				$ip_adresi = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$konular['degistiren_ip'].'">'.$konular['degistiren_ip'].'</a>';
			}
		}



		// bulunan konu ise
		else
		{
			$konu_baslik = '<a href="../konu.php?k='.$konular['id'].'">'.$konular['mesaj_baslik'].'</a>';
			$yazan = '<a href="../profil.php?kim='.$konular['yazan'].'">'.$konular['yazan'].'</a>';


			if (is_numeric($konular['rakam2']))
			{
				$islem = 'Konu A�ma';
				$tarih =  zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['tarih']);
				$ip_adresi = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$konular['yazan_ip'].'">'.$konular['yazan_ip'].'</a>';
			}

			else
			{
				$islem = 'Konu De�i�tirme';
				$tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $konular['degistirme_tarihi']);
				$ip_adresi = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$konular['degistiren_ip'].'">'.$konular['degistiren_ip'].'</a>';
			}
		}


		//	veriler tema motoruna yollan�yor	//

		$tekli1[] = array('{SIRA}' => $sira,
		'{KONU_BASLIK}' => $konu_baslik,
		'{YAZAN}' => $yazan,
		'{IP_DEGISTIREN2}' => $ip_adresi,
		'{ISLEM}' => $islem,
		'{TARIH}' => $tarih);

		$sira++;
	}
}



$ornek1->kosul('4', array('' => ''), false);
$ornek1->kosul('3', array('{KAYIT_IP}' => 'IP Adresi', '{IP_DEGISTIREN}' => 'IP Adresi', '{UYE_MISAFIR}' => '�yenin Bilgileri', '{KONULAR_CEVAPLAR}' => '�yenin Yapt��� Son i�lemler', '{SAYFA_ACIKLAMA3}' => $sayfa_aciklama3), true);


if (isset($tekli1))
{
	$ornek1->tekli_dongu('1',$tekli1);
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), true);
}
else
{
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('' => ''), true);
}


if (isset($tekli2))
{
	$ornek1->tekli_dongu('2',$tekli2);
	$ornek1->kosul('5', array('' => ''), false);
	$ornek1->kosul('6', array('' => ''), true);
}
else
{
	$ornek1->kosul('6', array('' => ''), false);
	$ornek1->kosul('5', array('' => ''), true);
}



//  KULLANICI ADINA G�RE ARAMA - SONU   //








//  NORMAL G�STER�M - BA�I   //
else:

$sayfa_aciklama = '';

$sayfa_aciklama2 = '<br> &nbsp; Ip adresini, <b>IP Sorgulama</b> alan�na girerek o ip adresi ile yap�lan t�m i�lemleri g�r�nt�leyebilirsiniz.

<br> &nbsp; �ye ad�n�, <b>�ye Sorgulama</b> alan�na girerek o �yenin yapt��� t�m i�lemleri g�r�nt�leyebilirsiniz.

<br><br> &nbsp; Ayr�ca, konu ve �evrimi�i sayfalar�ndaki ip adreslerini t�klayarak da bu �zellikleri kullanabilirsiniz.<br>';


$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('3', array('' => ''), false);
$ornek1->kosul('5', array('' => ''), false);
$ornek1->kosul('6', array('' => ''), false);
$ornek1->kosul('4', array('{IP_ADRESI}' => $_SERVER['REMOTE_ADDR'], '{UYE_ADI}' => $kullanici_kim['kullanici_adi'],'{SAYFA_ACIKLAMA2}' => $sayfa_aciklama2), true);

//  NORMAL G�STER�M - SONU   //

endif;






//  SAYFALAMA   //

$sayfalama = '';

if (isset($safya_kip))
{
	if ($satir_sayi > $adim):

	$sayfalama .= '<p>
	<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
		<tbody>
		<tr>
		<td class="forum_baslik">
	Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
		</td>
	';

	if ($sayfa != 0)
	{
		$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
		$sayfalama .= '&nbsp;<a href="ip_yonetimi.php?'.$safya_kip.'">&laquo;ilk</a>&nbsp;</td>';

		$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
		$sayfalama .= '&nbsp;<a href="ip_yonetimi.php?'.$safya_kip.'&amp;sayfa='.($sayfa - $adim).'">&lt;</a>&nbsp;</td>';
	}

	for ($sayi=0,$sayfa_sinir=$sayfa; $sayi < $toplam_sayfa; $sayi++)
	{
		if ($sayi < (($sayfa / $adim) - 3));
		else
		{
			$sayfa_sinir++;
			if ($sayfa_sinir >= ($sayfa + 8)) break;
			if (($sayi == 0) and ($sayfa == 0))
			{
				$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
				$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
			}

			elseif (($sayi + 1) == (($sayfa / $adim) + 1))
			{
				$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
				$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
			}

			else
			{
				$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">';

				$sayfalama .= '&nbsp;<a href="ip_yonetimi.php?'.$safya_kip.'&amp;sayfa='.($sayi * $adim).'">'.($sayi + 1).'</a>&nbsp;</td>';
			}
		}
	}

	if ($sayfa < ($satir_sayi - $adim))
	{
		$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
		$sayfalama .= '&nbsp;<a href="ip_yonetimi.php?'.$safya_kip.'&amp;sayfa='.($sayfa + $adim).'">&gt;</a>&nbsp;</td>';

		$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
		$sayfalama .= '&nbsp;<a href="ip_yonetimi.php?'.$safya_kip.'&amp;sayfa='.(($toplam_sayfa - 1) * $adim).'">son&raquo;</a>&nbsp;</td>';
	}

	$sayfalama .= '</tr>
		</tbody>
	</table>';

	endif;
}



$dongusuz = array('{SAYFA_BASLIK}' => $sayfa_baslik,
'{SAYFA_ACIKLAMA}' => $sayfa_aciklama,
 '{SAYFALAMA}' => $sayfalama);


$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>