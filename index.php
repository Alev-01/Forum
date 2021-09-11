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

if (!@is_file('ayar.php'))
{
	// ayar.php yok, kurulum yap�lmam��, kurulum sayfas�na y�nlendir.
	header('Location: kurulum/index.php');
	exit();
}


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';
$zaman_asimi = $ayarlar['cevrimici'];
$tarih = time();


if ($ayarlar['surum'] != '1.90')
{
	// g�ncelleme yap�lmam��sa kurulum sayfas�na y�nlendir.
	header('Location: kurulum/index.php');
	exit();
}




//  FORUM TEMASINI DE���T�R //

if ((isset($_GET['renk'])) AND ($_GET['renk'] != ''))
{
	switch($_GET['renk'])
	{
		case 'yesil';
		setcookie('forum_rengi', 'yesil', $tarih+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		header('Location: '.$forum_index);
		exit();
		break;

		case 'kirmizi';
		setcookie('forum_rengi', 'kirmizi', $tarih+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		header('Location: '.$forum_index);
		exit();
		break;

		case 'turuncu';
		setcookie('forum_rengi', 'turuncu', $tarih+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		header('Location: '.$forum_index);
		exit();
		break;

		case 'mavi';
		setcookie('forum_rengi', 'mavi', $tarih+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		header('Location: '.$forum_index);
		exit();
		break;

		default:
		setcookie('forum_rengi', 'siyah', $tarih+$ayarlar['k_cerez_zaman'], $ayarlar['f_dizin']);
		header('Location: '.$forum_index);
		exit();
	}
}




		//      FORUMLAR SIRALANIYOR - BA�I      //


//  FORUM DALLARININ B�LG�LER� �EK�L�YOR    //

$strSQL = "SELECT * FROM $tablo_dallar ORDER BY sira";
$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


$sayfano = 1;
$sayfa_adi = 'Forum Ana Sayfas�';
include 'baslik.php';

if (!defined('DOSYA_SEO')) include 'seo.php';



$guncel_saat = zonedate2($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $tarih);

if ($ayarlar['saat_dilimi'] >= 0) $guncel_saat .= '&nbsp; (GMT +'.$ayarlar['saat_dilimi'].' saat)';
else $guncel_saat .= '&nbsp; (GMT '.$ayarlar['saat_dilimi'].' saat)';



//  SON GEL�� TAR�H� �EREZDEN ALINIYOR  //

if (isset($kullanici_kim['son_giris']))
$guncel_saat .= '<br><b>Son Ziyaretiniz:</b>&nbsp; '
.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $kullanici_kim['son_giris'])
.'<br><a href="ymesaj.php">Yeni ve Cevaps�z iletiler</a>';



$guncel_ek = '';
$toplam_baslik = 0;
$toplam_mesaj = 0;
$dongu1 = 0;


//  FORUM DALLARI SIRALANIYOR   //

while ($dallar_satir = mysql_fetch_assoc($sonuc3)):

//	veriler tema motoruna yollan�yor	//
$tema_dis[] = array('{FORUM_DALI_BASLIK}' => $dallar_satir['ana_forum_baslik']);

// �st forumlar�n bilgileri �ekiliyor
$strSQL = "SELECT id,forum_baslik,forum_bilgi,okuma_izni,resim,konu_sayisi,cevap_sayisi,gizle
		FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
$sonuc4 = mysql_query($strSQL);


// FORUM DALINA A�T FORUM YOKSA	//

if (!mysql_num_rows($sonuc4))
{
	$tema_ic[$dongu1][] = array('{FORUM_KLASOR}' => $acik_forum.' alt="Herkese A��k Forum" title="Herkese A��k Forum"',
'{FORUM_OZEL_KLASOR}' =>  'src="temalar/'.$ayarlar['temadizini'].'/resimler/forum01.gif" width="0" height="0" alt="bo�"',
'{FORUM_BAGLANTI}' => '#',
'{FORUM_BASLIK}' => '</b></a><br><div align="center"><b>HEN�Z FORUM OLU�TURULMAMI�</b></div><a href="#"><b>',
'{FORUM_BILGI}' => '',
'{FORUM_YARDIMCILARI}' => '',
'{FORUM_GOR}' => '',
'{SONMESAJ_BASLIK}' => '',
'{FORUM_BASLIK_SAYISI}' => '',
'{FORUM_MESAJ_SAYISI}' => '',
'{ALT_FORUMLAR}' => '');
}

$forum_yardimcilari = '';




//	�ST FORUMLAR SIRALANIYOR    //

while ($forum_satir = mysql_fetch_assoc($sonuc4)):

// alt forumlar�n bilgileri �ekiliyor
$strSQL = "SELECT id,forum_baslik,konu_sayisi,cevap_sayisi,okuma_izni,gizle FROM $tablo_forumlar WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
$sonuc5 = mysql_query($strSQL);

// forum ba�l�klar� diziye aktar�l�yor
$tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];


$iceride_ek = '';
$alt_forumlar = '';
$alt_forum_sorgu = '';
$fkonu_sayisi = 0;
$fmesaj_sayisi = 0;


// alt forum varsa
if (mysql_num_rows($sonuc5))
{
	$alt_forumlar = '<br><br><font class="alt_forum">Alt Forumlar:</font>&nbsp; <br>
	<table cellspacing="0" cellpadding="4" border="0" align="left">
	<tr>';

	$alt_forum_sayi = 0;


	//	ALT FORUMLAR SIRALANIYOR    //

	while ($alt_forum_satir = mysql_fetch_assoc($sonuc5))
	{
		$iceride_ek .= "OR sayfano LIKE '%3,$alt_forum_satir[id]'";

		// Yetkiye g�re alt forum (ve konu) ba�l��� gizleme

		if (($alt_forum_satir['gizle'] == 1) AND ($alt_forum_satir['okuma_izni'] != 0))
		{
			if (isset($kullanici_kim['id']))
			{
				if (($alt_forum_satir['okuma_izni'] == 5) AND ($kullanici_kim['yetki'] != 1))
				{
					$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
					continue;
				}

				elseif (($alt_forum_satir['okuma_izni'] == 1) AND ($kullanici_kim['yetki'] != 1))
				{
					$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
					continue;
				}

				elseif (($alt_forum_satir['okuma_izni'] == 2) AND ($kullanici_kim['yetki'] == 0))
				{
					$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
					continue;
				}

				elseif (($alt_forum_satir['okuma_izni'] == 3) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2))
				{
					if ($kullanici_kim['yetki'] >= 0)
					{
						if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$alt_forum_satir[id]' AND okuma='1' OR";
						else $grupek = "grup='0' AND";

						$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$alt_forum_satir[id]' AND okuma='1'";
						$kul_izin = mysql_query($strSQL);
						if (!mysql_num_rows($kul_izin))
						{
							$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
							continue;
						}
					}
					else
					{
						$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
						continue;
					}
				}
			}

			else
			{
				$guncel_ek .= " AND hangi_forumdan!='$alt_forum_satir[id]' ";
				continue;
			}
		}



		// alt forumlar�n dizili� bi�imi, 2 sat�r dizilmesi i�in % 2 girin
		if ( ($alt_forum_sayi != 0) AND ($alt_forum_sayi % 1) == 0)
			$alt_forumlar .= '</tr><tr>';

		$alt_forumlar .= '<td align="left" class="liste-veri"><img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Alt Forum"> <a href="'.linkver('forum.php?f='.$alt_forum_satir['id'], $alt_forum_satir['forum_baslik']).'">'.$alt_forum_satir['forum_baslik'].'</a></td>';

		$alt_forum_sayi++;

		$toplam_baslik += $alt_forum_satir['konu_sayisi'];
		$toplam_mesaj += ($alt_forum_satir['cevap_sayisi'] + $alt_forum_satir['konu_sayisi']);
		$fkonu_sayisi += $alt_forum_satir['konu_sayisi'];
		$fmesaj_sayisi += ($alt_forum_satir['cevap_sayisi'] + $alt_forum_satir['konu_sayisi']);

		$alt_forum_sorgu .= "OR silinmis='0' AND hangi_forumdan='$alt_forum_satir[id]' ";

		$tumforum_satir[$alt_forum_satir['id']] = $alt_forum_satir['forum_baslik'];
	}

	$alt_forumlar .= '
	</tr>
	</table>';
}


// Yetkiye g�re �st forum (ve konu) ba�l��� gizleme

if (($forum_satir['gizle'] == 1) AND ($forum_satir['okuma_izni'] != 0))
{
	if (isset($kullanici_kim['id']))
	{
		if (($forum_satir['okuma_izni'] == 5) AND ($kullanici_kim['yetki'] != 1))
		{
			$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
			continue;
		}

		elseif (($forum_satir['okuma_izni'] == 1) AND ($kullanici_kim['yetki'] != 1))
		{
			$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
			continue;
		}

		elseif (($forum_satir['okuma_izni'] == 2) AND ($kullanici_kim['yetki'] == 0))
		{
			$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
			continue;
		}

		elseif (($forum_satir['okuma_izni'] == 3) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2))
		{
			if ($kullanici_kim['yetki'] >= 0)
			{
				if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$forum_satir[id]' AND okuma='1' OR";
				else $grupek = "grup='0' AND";

				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$forum_satir[id]' AND okuma='1'";
				$kul_izin = mysql_query($strSQL);
				if (!mysql_num_rows($kul_izin))
				{
					$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
					continue;
				}
			}
			else
			{
				$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
				continue;
			}
		}
	}

	else
	{
		$guncel_ek .= " AND hangi_forumdan!='$forum_satir[id]' ";
		continue;
	}
}


unset($yardimcilar);

// forum yard�mlar�n�n bilgileri �ekiliyor
$strSQL = "SELECT kulid,kulad,grup FROM $tablo_ozel_izinler WHERE fno='$forum_satir[id]' AND yonetme='1' ORDER BY kulad";
$ysonuc = mysql_query($strSQL);

while ($yardimci = mysql_fetch_assoc($ysonuc))
{
	if ($yardimci['grup'] == '0')
	{
		if (empty($yardimcilar)) $yardimcilar = '<a href="'.linkver('profil.php?u='.$yardimci['kulid'].'&kim='.$yardimci['kulad'],$yardimci['kulad']).'">'.$yardimci['kulad'].'</a>';
		else $yardimcilar .= ', <a href="'.linkver('profil.php?u='.$yardimci['kulid'].'&kim='.$yardimci['kulad'],$yardimci['kulad']).'">'.$yardimci['kulad'].'</a>';
	}

	else
	{
		if (empty($yardimcilar)) $yardimcilar = '<a href="uyeler.php?kip=grup">'.$yardimci['kulad'].'</a>';
		else $yardimcilar .= ', <a href="<a href="uyeler.php?kip=grup">'.$yardimci['kulad'].'</a>';
	}
}



// forum klas�rleri
$forum_klasor = '';

if ($forum_satir['okuma_izni'] == 0) $forum_klasor .= $acik_forum.' alt="Herkese A��k Forum" title="Herkese A��k Forum"';
elseif ($forum_satir['okuma_izni'] == 1) $forum_klasor .= $yonetici_forum.' alt="Sadece Y�neticilere A��k Forum" title="Sadece Y�neticilere A��k Forum"';
elseif ($forum_satir['okuma_izni'] == 2) $forum_klasor .= $yardimci_forum.' alt="Sadece Y�neticilere ve Yard�mc�lara A��k Forum" title="Sadece Y�neticilere ve Yard�mc�lara A��k Forum"';
elseif ($forum_satir['okuma_izni'] == 3) $forum_klasor .= $ozel_forum.' alt="Sadece �zel Yetkilere Sahip �yelere A��k Forum" title="Sadece �zel Yetkilere Sahip �yelere A��k Forum"';
elseif ($forum_satir['okuma_izni'] == 4) $forum_klasor .= $uyeler_forum.' alt="Sadece �yelere A��k Forum" title="Sadece �yelere A��k Forum"';
elseif ($forum_satir['okuma_izni'] == 5) $forum_klasor .= $kapali_forum.' alt="Kapal� Forum" title="Kapal� Forum"';


if (empty($forum_satir['resim']))
$forum_ozel_klasor = 'src="temalar/'.$ayarlar['temadizini'].'/resimler/forum01.gif" alt="Forum �zel Klas�r"';
else $forum_ozel_klasor = 'src="'.$forum_satir['resim'].'" alt="Forum �zel Klas�r"';


$forum_baglanti = linkver('forum.php?f='.$forum_satir['id'], $forum_satir['forum_baslik']);


//	B�L�M YARDIMCISI(LARI) VARSA SIRALANIYOR	//

if (isset($yardimcilar))
{
	if (preg_match('/,/', $yardimcilar)) $forum_yardimcilari = '<br><b><i>B�l�m yard�mc�lar�:</i></b> '.$yardimcilar;
	else $forum_yardimcilari = '<br><b><i>B�l�m yard�mc�s�:</i></b> '.$yardimcilar;
}




//  EN YEN� BA�LI�IN B�LG�LER� �EK�L�YOR  //

$strSQL = "SELECT id,son_mesaj_tarihi,mesaj_baslik,yazan,cevap_sayi,son_cevap,son_cevap_yazan FROM $tablo_mesajlar WHERE silinmis='0' AND hangi_forumdan='$forum_satir[id]' $alt_forum_sorgu ORDER BY son_mesaj_tarihi DESC LIMIT 1";
$sonuc2 = mysql_query($strSQL);
$son_mesaj = mysql_fetch_assoc($sonuc2);


//  FORUMDA H�� BA�LIK YOKSA  //

if (!isset($son_mesaj['id'])):

$sonmesaj_baslik = 'Hen�z yaz�<br>bulunmamaktad�r';


//      CEVAP YOKSA     //

elseif ($son_mesaj['cevap_sayi'] == 0):

$sonmesaj_baslik = '';


//  son konunun ba�l��� yazd�r�l�yor, uzunsa k�salt�l�yor  //

if ((strlen($son_mesaj['mesaj_baslik']) > 26))
{
	$kisa_baslik = (substr($son_mesaj['mesaj_baslik'], 0, 26).'...');
	$sonmesaj_baslik .= '<a title="'.$son_mesaj['mesaj_baslik'].'" href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik']).'"><b>'.$kisa_baslik.'</b></a>';
}

else $sonmesaj_baslik .= '<a title="'.$son_mesaj['mesaj_baslik'].'" href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik']).'"><b>'.$son_mesaj['mesaj_baslik'].'</b></a>';

$sonmesaj_baslik .= '<br><b>Yazan: </b>
<a href="'.linkver('profil.php?kim='.$son_mesaj['yazan'],$son_mesaj['yazan']).'" title="Kullan�c� Profilini G�r�nt�le">'.$son_mesaj['yazan'].'</a>';


$sonmesaj_baslik .= '<p align="right"><b>Tarih: </b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $son_mesaj['son_mesaj_tarihi']);

$sonmesaj_baslik .= '&nbsp;<a href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik']).'" style="text-decoration: none">&nbsp;<img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';




//      CEVAP VARSA     //

else:
$sonmesaj_baslik = '';


//  son konunun ba�l��� yazd�r�l�yor, uzunsa k�salt�l�yor  //

if ((strlen($son_mesaj['mesaj_baslik']) > 26))
{
	$kisa_baslik = (substr($son_mesaj['mesaj_baslik'], 0, 26).'...');
	$sonmesaj_baslik .= '<a title="'.$son_mesaj['mesaj_baslik'].'" href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik']).'"><b>'.$kisa_baslik.'</b></a>';
}

else $sonmesaj_baslik .= '<a title="'.$son_mesaj['mesaj_baslik'].'" href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik']).'"><b>'.$son_mesaj['mesaj_baslik'].'</b></a>';

$sonmesaj_baslik .= '<br><b>Yazan: </b>
<a href="'.linkver('profil.php?kim='.$son_mesaj['son_cevap_yazan'],$son_mesaj['son_cevap_yazan']).'" title="Kullan�c� Profilini G�r�nt�le">'.$son_mesaj['son_cevap_yazan'].'</a>';


$sonmesaj_baslik .= '<p align="right"><b>Tarih: </b>'.zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $son_mesaj['son_mesaj_tarihi']);


//  BA�LIK �OK SAYFALI �SE SON SAYFAYA G�T  //

if ($son_mesaj['cevap_sayi'] > $ayarlar['ksyfkota'])
{
	$sayfaya_git = (($son_mesaj['cevap_sayi']-1) / $ayarlar['ksyfkota']);
	settype($sayfaya_git,'integer');
	$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

	$sonmesaj_baslik .= '&nbsp;<a href="'.linkver('konu.php?k='.$son_mesaj['id'].'&ks='.$sayfaya_git, $son_mesaj['mesaj_baslik'], '#c'.$son_mesaj['son_cevap']).'" style="text-decoration: none">&nbsp;';
}

else $sonmesaj_baslik .= '&nbsp;<a href="'.linkver('konu.php?k='.$son_mesaj['id'], $son_mesaj['mesaj_baslik'], '#c'.$son_mesaj['son_cevap']).'" style="text-decoration: none">&nbsp;';

$sonmesaj_baslik .= '<img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';


endif;



//  BU FORUMUN VE T�M FORUMLARIN KONU VE MESAJ SAYILARI HESAPLANIYOR    //

$toplam_baslik += $forum_satir['konu_sayisi'];
$toplam_mesaj += ($forum_satir['cevap_sayisi'] + $forum_satir['konu_sayisi']);
$fkonu_sayisi += $forum_satir['konu_sayisi'];
$fmesaj_sayisi += ($forum_satir['cevap_sayisi'] + $forum_satir['konu_sayisi']);



// FORUMU G�R�NT�LEYENLER�N SAYILARI ALINIYOR  //

if ($ayarlar['bolum_kisi'] == 1)
{
	$sonuc = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE (sayfano LIKE '%3,$forum_satir[id]' $iceride_ek ) AND (son_hareket + $zaman_asimi) > $tarih AND sayfano!='-1'");
	$gor_usayi = mysql_num_rows($sonuc);

	$sonuc = mysql_query("SELECT sid FROM $tablo_oturumlar WHERE (sayfano LIKE '%3,$forum_satir[id]' $iceride_ek ) AND (son_hareket + $zaman_asimi) > $tarih");
	$gor_msayi = mysql_num_rows($sonuc);

	$gor_sayi = $gor_usayi + $gor_msayi;

	if ($gor_sayi > 0) $forum_gor = '('.$gor_sayi.' ki�i i�eride)';
	else $forum_gor = '';
}

else $forum_gor = '';




//	veriler tema motoruna yollan�yor	//

$tema_ic[$dongu1][] = array('{FORUM_KLASOR}' => $forum_klasor,
'{FORUM_OZEL_KLASOR}' => $forum_ozel_klasor,
'{FORUM_BAGLANTI}' => $forum_baglanti,
'{FORUM_BASLIK}' => $forum_satir['forum_baslik'],
'{FORUM_GOR}' => $forum_gor,
'{FORUM_BILGI}' => $forum_satir['forum_bilgi'],
'{FORUM_YARDIMCILARI}' => $forum_yardimcilari,
'{SONMESAJ_BASLIK}' => $sonmesaj_baslik,
'{FORUM_BASLIK_SAYISI}' => NumaraBicim($fkonu_sayisi),
'{FORUM_MESAJ_SAYISI}' => NumaraBicim($fmesaj_sayisi),
'{ALT_FORUMLAR}' => $alt_forumlar);

$forum_yardimcilari = '';


endwhile;

// forum dal�ndaki t�m forumlar gizliyse, forum dal�n� da gizle
if (!@is_array($tema_ic[$dongu1]))
{
	unset($tema_dis[$dongu1]);
	continue;
}
$dongu1++;

endwhile;


		//      FORUMLAR SIRALANIYOR - SONU      //






        //      G�NCEL KONULAR SIRALANIYOR - BA�I      //


if ($ayarlar['sonkonular'] == 1):


//  G�NCEL KONULARIN B�LG�LER� �EK�L�YOR  //

$strSQL = "SELECT id,son_mesaj_tarihi,yazan,hangi_forumdan,cevap_sayi,goruntuleme,mesaj_baslik,yazan,son_cevap,son_cevap_yazan FROM $tablo_mesajlar WHERE silinmis='0' $guncel_ek ORDER BY son_mesaj_tarihi DESC LIMIT $ayarlar[kacsonkonu]";
$sonuc10 = mysql_query($strSQL);


$satir_renklendir = 1;

while ($son10 = mysql_fetch_assoc($sonuc10))
{
	if (($satir_renklendir % 2)) $satir_renk = 'satir_renk1';
	else $satir_renk = 'satir_renk2';
	$satir_renklendir++;


	$son10konu_baslik = '<a href="'.linkver('konu.php?k='.$son10['id'],$son10['mesaj_baslik']).'">'.$son10['mesaj_baslik'].'</a>';

	$son10konu_forum_baslik = '<a href="'.linkver('forum.php?f='.$son10['hangi_forumdan'],$tumforum_satir[$son10['hangi_forumdan']]).'">'.$tumforum_satir[$son10['hangi_forumdan']].'</a>';

	$son10konu_acan = '<a href="'.linkver('profil.php?kim='.$son10['yazan'],$son10['yazan']).'">'.$son10['yazan'].'</a>';


	//      CEVAP YOKSA     //

	if ($son10['cevap_sayi'] == 0)
		$son10konu_sonyazan = '<a href="'.linkver('profil.php?kim='.$son10['yazan'],$son10['yazan']).'">'.$son10['yazan'].'</a>&nbsp;<a href="'.linkver('konu.php?k='.$son10['id'], $son10['mesaj_baslik']).'" style="text-decoration: none"><img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';


	//      CEVAP VARSA     //

	else
	{
		$son10konu_sonyazan = '<a href="'.linkver('profil.php?kim='.$son10['son_cevap_yazan'],$son10['son_cevap_yazan']).'">'.$son10['son_cevap_yazan'].'</a>';


		//  BA�LIK �OK SAYFALI �SE SON SAYFAYA G�T  //

		if ($son10['cevap_sayi'] > $ayarlar['ksyfkota'])
		{
			$sayfaya_git = (($son10['cevap_sayi']-1) / $ayarlar['ksyfkota']);
			settype($sayfaya_git,'integer');
			$sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

			$son10konu_sonyazan .= '&nbsp;<a href="'.linkver('konu.php?k='.$son10['id'].'&ks='.$sayfaya_git, $son10['mesaj_baslik'], '#c'.$son10['son_cevap']).'" style="text-decoration: none">';
		}

		else $son10konu_sonyazan .= '&nbsp;<a href="'.linkver('konu.php?k='.$son10['id'], $son10['mesaj_baslik'], '#c'.$son10['son_cevap']).'" style="text-decoration: none">';


		$son10konu_sonyazan .= '<img src="'.$sonileti_rengi.'" border="0" width="13" height="9" alt="Son iletiye git" title="Son iletiye git">&nbsp;</a>';
	}

	$son10konu_sontarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $son10['son_mesaj_tarihi']);


	//	veriler tema motoruna yollan�yor	//

	$tekli[] = array('{SATIR_RENK}' => $satir_renk,
'{SON10KONU_BASLIK}' => $son10konu_baslik,
'{SON10KONU_FORUMBASLIK}' => $son10konu_forum_baslik,
'{SON10KONU_ACAN}' => $son10konu_acan,
'{SON10KONU_CEVAPSAYI}' => NumaraBicim($son10['cevap_sayi']),
'{SON10KONU_GORSAYISI}' => NumaraBicim($son10['goruntuleme']),
'{SON10KONU_SONYAZAN}' => $son10konu_sonyazan,
'{SON10KONU_SONTARIH}' => $son10konu_sontarih);
}

endif;

		//      G�NCEL KONULAR SIRALANIYOR - BA�I      //





		//	FORUM B�LG�LER� - BA�I	//


//	TOPLAM �YE SAYISI ALINIYOR	//

$uyeler = mysql_query("SELECT id FROM $tablo_kullanicilar");
$uye_sayisi = mysql_num_rows($uyeler);


//	SON KAYDOLAN �YEN�N ADI ALINIYOR	//

$son_uye = mysql_query("SELECT id,kullanici_adi FROM $tablo_kullanicilar ORDER BY id DESC LIMIT 1");
$sonuye_adi = mysql_fetch_assoc($son_uye);


//	�EVR�M��� KULLANICI SAYISI ALINIYOR	//

$result = mysql_query("SELECT kullanici_adi,yetki FROM $tablo_kullanicilar WHERE (son_hareket + $zaman_asimi) > $tarih AND gizli='0' AND sayfano!='-1' ORDER BY son_hareket DESC");
$kullanici_sayi = mysql_num_rows($result);


//	�EVR�M��� KULLANICI B�LG�LER� �EK�L�YOR	//


$strSQL = "SELECT id,kullanici_adi,yetki FROM $tablo_kullanicilar
		WHERE (son_hareket + $zaman_asimi) > $tarih
		AND gizli='0' AND sayfano!='-1' ORDER BY son_hareket DESC LIMIT 0,20";

$cevirim_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


//	G�ZL� �EVR�M��� KULLANICI SAYISI ALINIYOR	//

$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE (son_hareket + $zaman_asimi) > $tarih AND gizli='1' AND sayfano!='-1'");
$gizli_sayi = mysql_num_rows($result);


//	�EVR�M��� M�SAF�RLER�N B�LG�LER� �EK�L�YOR	//

$result = mysql_query("SELECT giris FROM $tablo_oturumlar WHERE (son_hareket + $zaman_asimi) > $tarih");
$misafir_sayi = mysql_num_rows($result);


$toplam_sayi = ($kullanici_sayi + $gizli_sayi + $misafir_sayi);




//	FORUM B�LG�LER� YAZDIRILIYOR	//

$yeni_uye = '<a href="'.linkver('profil.php?u='.$sonuye_adi['id'].'&kim='.$sonuye_adi['kullanici_adi'],$sonuye_adi['kullanici_adi']).'">'.$sonuye_adi['kullanici_adi'].'</a>';


$cevrimici_isimler = '';

//	�EVR�M��� KULLANICILAR SIRALANIYOR	//

while ($cevirimici = mysql_fetch_assoc($cevirim_sonuc))
{
	$cevrimici_isimler .= '<a href="'.linkver('profil.php?u='.$cevirimici['id'].'&kim='.$cevirimici['kullanici_adi'],$cevirimici['kullanici_adi']).'">';

	if ($cevirimici['id'] == 1)
	$cevrimici_isimler .= '<font class="kurucu">'.$cevirimici['kullanici_adi'].'</font></a>, ';

	elseif ($cevirimici['yetki'] == 1)
	$cevrimici_isimler .= '<font class="yonetici">'.$cevirimici['kullanici_adi'].'</font></a>, ';

	elseif ($cevirimici['yetki'] == 2)
	$cevrimici_isimler .= '<font class="yardimci">'.$cevirimici['kullanici_adi'].'</font></a>, ';

	elseif ($cevirimici['yetki'] == 3)
	$cevrimici_isimler .= '<font class="blm_yrd">'.$cevirimici['kullanici_adi'].'</font></a>, ';

	else $cevrimici_isimler .= $cevirimici['kullanici_adi'].'</a>, ';
}


if ($kullanici_sayi == 0) $cevrimici_isimler .= ' Yok';
elseif ($kullanici_sayi > 20) $cevrimici_isimler .= ' <a href="cevrimici.php">...... devam�</a>';

$cevrimici_zaman = ($zaman_asimi / 60);



$javascript_kodu = '<script type="text/javascript">
<!-- //
function denetle()
{
	var dogruMu = true;
	if ((document.giris.kullanici_adi.value.length < 4) || (document.giris.sifre.value.length < 5))
	{
		dogruMu = false; 
		alert("L�tfen kullan�c� ad� ve �ifrenizi giriniz !");
	}
	return dogruMu;
}
// -->
</script>';


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/index.html');


// giri� yap�lmam��sa giri� formu g�ster

if (isset($kullanici_kim['id']))
{
	$ornek1->kosul('1', array('' => ''), false);
	$kullanici_adi = $kullanici_kim['kullanici_adi'];
}
else $kullanici_adi = '';


//	son kunular a��k - kapal�

if (isset($tekli)) $ornek1->tekli_dongu('1',$tekli);

else $ornek1->kosul('2', array('' => ''), false);


// forum dal� var - yok

if ((isset($tema_dis)) AND (isset($tema_ic)))
{
	$ornek1->kosul('5', array('' => ''), false);
	$ornek1->icice_dongu('1', $tema_dis, $tema_ic);
}

else
{
	$ornek1->kosul('3', array('' => ''), false);
	$ornek1->kosul('4', array('' => ''), false);
	$ornek1->kosul('5', array('{FORUM_DALI_YOK}' => 'HEN�Z FORUM DALI OLU�TURULMAMI�'), true);
}


//	veriler tema motoruna yollan�yor	//

$dongusuz = array('{SONKONU_SAYISI}' => $ayarlar['kacsonkonu'],
'{TOPLAM_BASLIK_SAYI}' => NumaraBicim($toplam_baslik),
'{TOPLAM_MESAJ_SAYI}' => NumaraBicim($toplam_mesaj),
'{TOPLAM_UYE_SAYI}' => NumaraBicim($uye_sayisi),
'{YENI_UYE}' => $yeni_uye,
'{KULLANICI_ADI}' => $kullanici_adi,
'{CEVRIMICI_TOPLAM}' => $toplam_sayi,
'{CEVRIMCI_UYE}' => $kullanici_sayi,
'{CEVRIMCI_GIZLI}' => $gizli_sayi,
'{CEVRIMCI_MISAFIR}' => $misafir_sayi,
'{AYARLAR_KURUCU}' => $ayarlar['kurucu'],
'{AYARLAR_YONETICI}' => $ayarlar['yonetici'],
'{AYARLAR_YARDIMCI}' => $ayarlar['yardimci'],
'{AYARLAR_BLM_YRD}' => $ayarlar['blm_yrd'],
'{CEVRIMCI_ISIMLER}' => $cevrimici_isimler,
'{CEVRIMICI_ZAMAN}' => $cevrimici_zaman,
'{ANASAYFA_BASLIK}' => $ayarlar['anasyfbaslik'],
'{GUNCEL_ZAMAN}' => $guncel_saat,
'{ACIK_FORUM}' => $acik_forum,
'{UYELER_FORUM}' => $uyeler_forum,
'{OZEL_FORUM}' => $ozel_forum,
'{YARDIMCI_FORUM}' => $yardimci_forum,
'{YONETICI_FORUM}' => $yonetici_forum,
'{KAPALI_FORUM}' => $kapali_forum,
'{ACIK_KONU}' => $acik_konu,
'{FORUMBILGI_RESIM}' => $forumbilgileri_resim,
'{CEVRIMICI_RESIM}' => $cevrimici_resim,
'{FORUM_INDEX}' => $forum_index,
'{JAVASCRIPT_KODU}' => $javascript_kodu);

$ornek1->dongusuz($dongusuz);


if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>