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
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// site kurucusu de�ilse hata ver
if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}

// eklenti yard�m konular�

$yardim_konulari = 'Ayr�nt�l� bilgi i�in a�a��daki konulara bak�n.<br><br><a href="http://www.phpkf.com/k1902-eklentiler-kurulum-kaldirma-ve-etkisizlestirme.html"><b>Eklenti Y�kleme, Kurulum, Kald�rma ve Etkisizle�tirme</b></a><br><br><a href="http://www.phpkf.com/k1904-eklenti-islemlerinde-olusabilecek-hatalar.html"><b>Olu�abilecek Hatalar</b></a>';



//  XML DOSYASI OKUMA FONKS�YONU    //

function xml_oku($dosya)
{
	global $ayarlar;
	global $tablo_oneki;
	global $forum_index;
	global $portal_index;

	$degistir = 0;
	$etkin_degistir = 0;
	$ekle = 0;
	$olustur = 0;
	$kur_veritabani = 0;
	$kaldir_veritabani = 0;
	$etkin_veritabani = 0;
	$etkisiz_veritabani = 0;

	$bul = array('{VT_ONEK}', '{FORUM_INDEX}', '{PORTAL_INDEX}');
	$cevir = array($tablo_oneki, $forum_index, $portal_index);

	$ebilgi = new XMLReader();
	$ebilgi->open($dosya, 'iso-8859-9');

	while ($ebilgi->read())
	{
		if ($ebilgi->nodeType == XMLReader::ELEMENT)
			$etiket = $ebilgi->name;

		elseif ( ($ebilgi->nodeType == XMLReader::TEXT) OR ($ebilgi->nodeType == XMLReader::CDATA) )
		{
			if ($etiket == 'degistirilecek_dosya')
			{
				$dizi[$etiket][$degistir] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$degistir++;
			}

			elseif ($etiket == 'etkin_degistirilecek_dosya')
			{
				$dizi[$etiket][$etkin_degistir] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$etkin_degistir++;
			}

			elseif ($etiket == 'eklenecek_dosya')
			{
				$dizi[$etiket][$ekle] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$ekle++;
			}

			elseif ($etiket == 'dizin_olustur')
			{
				$dizi[$etiket][$olustur] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$olustur++;
			}

			elseif ($etiket == 'kur_veritabani')
			{
				$dizi[$etiket][$kur_veritabani] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$kur_veritabani++;
			}

			elseif ($etiket == 'kaldir_veritabani')
			{
				$dizi[$etiket][$kaldir_veritabani] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$kaldir_veritabani++;
			}

			elseif ($etiket == 'etkin_veritabani')
			{
				$dizi[$etiket][$etkin_veritabani] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$etkin_veritabani++;
			}

			elseif ($etiket == 'etkisiz_veritabani')
			{
				$dizi[$etiket][$etkisiz_veritabani] = str_replace($bul, $cevir, mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8"));
				$etkisiz_veritabani++;
			}


			elseif ($etiket == 'kod_bul')
				$dizi[$etiket][$degistir-1][] = mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8");

			elseif ($etiket == 'kod_degistir')
				$dizi[$etiket][$degistir-1][] = mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8");

			elseif ($etiket == 'etkin_kod_bul')
				$dizi[$etiket][$etkin_degistir-1][] = mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8");

			elseif ($etiket == 'etkin_kod_degistir')
				$dizi[$etiket][$etkin_degistir-1][] = mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8");

			else $dizi[$etiket] = mb_convert_encoding($ebilgi->value, "ISO-8859-9", "UTF-8");
		}
	}
	$ebilgi->close();
	return($dizi);
}



//  D�Z�N-DOSYA SIRALAMA    //

function DizinDosya_Ac($ftp_baglanti, $dzn_chmod, $dsy_chmod, $ftp_kok, $yol)
{
	$cikis = '';
	$dizin = opendir('../../'.$yol);

	while ( gettype($bilgi = readdir($dizin)) != 'boolean' )
	{
		if ( (is_dir('../../'.$yol.'/'.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
		{
			$cikis .= "<br><br> &nbsp; <b>Dizin:</b>&nbsp; ";
			$cikis .= HakDegistir($ftp_baglanti, $dzn_chmod, $ftp_kok, $yol.'/'.$bilgi);
			$cikis .= DizinDosya_Ac($ftp_baglanti, $dzn_chmod, $dsy_chmod, $ftp_kok, $yol.'/'.$bilgi);
		}

		elseif ( ($bilgi != '.') AND ($bilgi != '..') )
		{
			$cikis .= '<br><br> &nbsp; <b>Dosya:</b> &nbsp; ';
			$cikis .= HakDegistir($ftp_baglanti, $dsy_chmod, $ftp_kok, $yol.'/'.$bilgi);
		}
	}
	closedir($dizin);
	return $cikis;
}



//  D�Z�N-DOSYA HAKLARINI DE���T�RME    //

function HakDegistir($ftp_baglanti, $chmod, $ftp_kok, $yol)
{
	if (ftp_site($ftp_baglanti, "CHMOD $chmod $ftp_kok/$yol") !== false)
		return "$yol<font color=\"#669900\"><b> &nbsp; De�i�tirildi</b></font>";

	else
	{
		if (chmod('../../'.$yol, '0777')) return "$yol<font color=\"#669900\"><b> &nbsp; De�i�tirildi</b></font>";
		else return "$yol<font color=\"#ff0000\"><b> &nbsp; De�i�tirilemedi</b></font>";
	}
	return true;
}




		//  phpkf_eklenti SINIF - BA�I    //

class phpkf_eklenti
{
	var $eklenti_ham;
	var $eklenti_cikis;
	var $hata;


	// dosya de�i�tirme i�in denetleniyor
	function dd_denetle($dosya)
	{
		if (!@is_file($dosya))
		{
			$this->hata = 'dosya yok';
			return false;
		}

		elseif (@touch($dosya))
		{
			if (!@is_writable($dosya))
			{
				$this->hata = 'yazma hakk� yok';
				return false;
			}
			else return true;
		}

		else
		{
			$this->hata = 'yazma hakk� yok';
			return false;
		}
	}


	// dosya kopyalama i�in denetleniyor
	function do_denetle($dosya, $dizin)
	{
		$dosyak = preg_replace('|(.*?)/([a-z0-9_\-.&]+?)$|si', '\\2', $dosya);
		$dosyak = '../eklentiler/'.$dizin.'/'.$dosyak;

		if (!@is_file($dosyak))
		{
			$this->hata = 'kopyalanacak dosya yok';
			return false;
		}

		elseif (!@touch($dosyak))
		{
			$this->hata = 'kopyalanacak dosyaya okuma hakk� yok';
			return false;
		}

		elseif (@is_file($dosya))
		{
			if (!@is_writable($dosya)) $this->hata = 'dosya var, �zerine yazma hakk� yok';
			else $this->hata = 'dosya var';
			return false;
		}

		elseif (@touch($dosya))
		{
			if (!@is_writable($dosya))
			{
				$this->hata = 'dizine yazma hakk� yok';
				return false;
			}

			else
			{
				@unlink($dosya);
				return true;
			}
		}

		else
		{
			$this->hata = 'dizine yazma hakk� yok';
			return false;
		}
	}


	// dosya kopyalama
	function dosya_kopyala($dosya, $dizin)
	{
		global $_SERVER;
		$dosyat = preg_replace('|(.*?)/([a-z0-9_\-.&]+?)$|si', '\\2', $dosya);
		$dosyak = '../eklentiler/'.$dizin.'/'.$dosyat;

		if (!@is_file($dosyak))
		{
			$this->hata = 'kopyalanacak dosya yok';
			return false;
		}

		elseif (@is_file($dosya))
		{
			if ($this->dosya_yedekle($dosya, $dizin));
			if (!@copy($dosyak, $dosya)) return false;
			if ($_SERVER['HTTP_HOST'] != 'localhost') @chmod($dosya, '0777');
			return true;
		}

		elseif (@copy($dosyak, $dosya))
		{
			if ($_SERVER['HTTP_HOST'] != 'localhost') @chmod($dosya, '0777');
			return true;
		}

		else
		{
			$this->hata = 'dizine yazma hakk� yok';
			return false;
		}
	}


	// dosya silme i�in denetleniyor
	function ds_denetle($dosya)
	{
		if (!@is_file($dosya))
		{
			$this->hata = 'dosya yok';
			return false;
		}

		elseif (!@touch($dosya))
		{
			$this->hata = 'silme hakk� yok';
			return false;
		}

		else return true;
	}


	// dosya siliyor
	function dosya_silme($dosya)
	{
		if (!@is_file($dosya))
		{
			$this->hata = 'dosya yok';
			return false;
		}

		elseif (@unlink($dosya)) return true;

		else
		{
			$this->hata = 'silme hakk� yok';
			return false;
		}
	}


	// dizin olu�turma i�in denetleniyor
	function dio_denetle($dizin)
	{
		if (@opendir($dizin))
		{
			$this->hata = 'dizin zaten var';
			return false;
		}

		elseif (@mkdir($dizin))
		{
			@rmdir($dizin);
			return true;
		}

		else
		{
			$this->hata = 'dizine yazma hakk� yok';
			return false;
		}
	}


	// dizin olu�turuluyor
	function dizin_olustur($dizin)
	{
		$eski_umask = umask(0);
		if (@opendir($dizin))
		{
			$this->hata = 'dizin zaten var';
			return false;
		}

		elseif (@mkdir($dizin)) return true;

		else
		{
			$this->hata = 'dizine yazma hakk� yok';
			return false;
		}
		umask($eski_umask);
	}


	// dizin silme i�in denetleniyor
	function dis_denetle($dizin)
	{
		if (!@is_dir($dizin))
		{
			$this->hata = 'dizin yok';
			return false;
		}

		elseif (!@fopen($dizin.'/yokla.txt', 'w'))
		{
			$this->hata = 'silme hakk� yok';
			return false;
		}

		else
		{
			@unlink($dizin.'/yokla.txt');
			return true;
		}
	}


	// dizin siliniyor
	function dizin_sil($dizin)
	{
		if (!@is_dir($dizin))
		{
			$this->hata = 'dizin yok';
			return false;
		}

		else
		{
			$dosyalar = @opendir($dizin);
			$dizin .= '/';
			while (@gettype($dosya = @readdir($dosyalar)) != 'boolean')
			{
				if ( (!@is_dir($dizin.$dosya)) AND ($dosya != '.') AND ($dosya != '..')) @unlink($dizin.$dosya);

				elseif (($dosya != '.') AND ($dosya != '..'))
				{
					$this->dizin_sil($dizin.$dosya.'/');
					@rmdir($dizin.$dosya);
				}
			}
			@closedir($dosyalar);

			if (@rmdir($dizin)) return true;
			else {$this->hata .= '<br>dizin yok'; return false;}
		}
	}


	// de�i�tirilecek dosya a��l�yor
	function dosya_ac($dosya)
	{
		if (!($dosya_ac = @fopen($dosya,'r')))
		{
			$this->hata = $dosya.' = <font color="#ff0000">dosya a��lam�yor</font>';
			return false;
		}

		else
		{
			$boyut = filesize($dosya);
			$dosya_metni = fread($dosya_ac,$boyut);
			fclose($dosya_ac);
			$this->eklenti_ham = $dosya_metni;

			return true;
		}
	}


	// dosyadaki bul kodlar� denetleniyor
	function bul_denetle($bul)
	{
		$t_bul = array('\\', "'", '$', '(', ')', '<', '>', '{', '}', '&', '[', ']', '|', '^', '?', '+', '*');
		$t_cevir = array('\\\\', "\'", '\$', '\(', '\)', '\<', '\>', '\{', '\}', '\&', '\[', '\]', '\|', '\^', '\?', '\+', '\*');
		$bul = @str_replace($t_bul, $t_cevir, $bul);
		$sayi = 1;


		foreach ($bul as $deger)
		{
			if (!preg_match('|'.$deger.'|si', $this->eklenti_ham))
			{
				$this->hata .= '<br><hr><b>'.$sayi.')</b><br><pre>'.htmlspecialchars(stripslashes($deger)).'</pre><hr>';
				$bulunamadi = 1;
				$sayi++;
			}
		}

		if (isset($bulunamadi)) return false;
		else return true;
	}


	// de�i�iklik yap�l�yor
	function degistir($bul,$cevir)
	{
		$t_bul = array('\\', "'", '$', '(', ')', '<', '>', '{', '}', '&', '[', ']', '|', '^', '?', '+', '*');
		$t_cevir = array('\\\\', "\'", '\$', '\(', '\)', '\<', '\>', '\{', '\}', '\&', '\[', '\]', '\|', '\^', '\?', '\+', '\*');
		$bul = @str_replace($t_bul, $t_cevir, $bul);
		$cevir = @str_replace('\\', '\\\\', $cevir);

		$sayi = 0;
		foreach ($bul as $deger)
		{
			$bul[$sayi] = '|'.$deger.'|si';
			$sayi++;
		}

		$this->eklenti_cikis = preg_replace($bul,$cevir,$this->eklenti_ham);
	}


	// de�i�tirilen dosya kaydediliyor
	function dosya_kaydet($dosya)
	{
		if (@touch($dosya))
		{
			if (@is_writable($dosya))
			{
				$dosya_kaydet = fopen($dosya, 'w');
				flock($dosya_kaydet, 2);
				fwrite($dosya_kaydet, $this->eklenti_cikis);
				flock($dosya_kaydet, 3);
				fclose($dosya_kaydet);

				return true;
			}

			else
			{
				$this->hata = 'yaz�lam�yor';
				return false;
			}
		}

		else
		{
			$this->hata = 'yaz�lam�yor';
			return false;
		}
	}


	// yedekleme (de�i�tirilen dosyay�)
	function dosya_yedekle($dosya, $dizin)
	{
		global $_SERVER;
		$dosyak = str_replace(array('../','/'), array('',' '), $dosya);
		$dosyak = '../eklentiler/'.$dizin.'/yedek/'.$dosyak;

		if (!@is_file($dosya))
		{
			$this->hata = 'yedeklenecek dosya yok';
			return false;
		}

		elseif (@copy($dosya, $dosyak))
		{
			if ($_SERVER['HTTP_HOST'] != 'localhost') @chmod($dosyak, '0777');
			return true;
		}

		else
		{
			$this->hata = 'yedek dizine yazma hakk� yok';
			return false;
		}
	}
}

		//  phpkf_eklenti SINIF - SONU    //





		//  KURULUM �NCES� DENET�M   //

if ( (isset($_GET['kur'])) AND ($_GET['kur'] != '') ):

$_GET['kur'] = zkTemizle(trim($_GET['kur']));


// dosya ad�nda sorun varsa
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_GET['kur']))
{
	header('Location: ../hata.php?hata=171');
	exit();
}


// eklentiler dizini yazma hakk� yoksa
if (!@fopen('../eklentiler/yokla.txt', 'w'))
{
	header('Location: ../hata.php?hata=172');
	exit();
}
else @unlink('../eklentiler/yokla.txt');


// eklenti dosyas� yoksa
if (!@is_file('../eklentiler/'.$_GET['kur'].'/eklenti_bilgi.xml'))
{
	header('Location: ../hata.php?hata=173');
	exit();
}


// eklenti dosyas� y�kleniyor
$edbilgi = xml_oku('../eklentiler/'.$_GET['kur'].'/eklenti_bilgi.xml');


// eklenti s�r�m� uyumsuzsa
if ($edbilgi['uyumlu_surum'] != $ayarlar['surum'])
{
	header('Location: ../hata.php?hata=185');
	exit();
}



// Eklenti bilgileri �ekiliyor
$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$_GET[kur]'";
$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$ekl_satir = mysql_fetch_assoc($ekl_sonuc);


// eklenti zaten kuruluysa, g�ncelleme de�ilse
if ( ($ekl_satir['kur'] == 1) AND (!isset($_GET['guncel'])) )
{
	header('Location: ../hata.php?hata=174');
	exit();
}


// eklenti portal i�inse ve portal kullan�lm�yorsa
if ( ($portal_kullan == 0) AND ($edbilgi['sistem'] != '1') )
{
	header('Location: ../hata.php?hata=189');
	exit();
}



// eklenti_bilgi.xml dosyas�
$dosya_xml = 'eklentiler/'.$_GET['kur'].'/eklenti_bilgi.xml';


if (!isset($edbilgi['tema_dizini'])) $edbilgi['tema_dizini'] = '5renkli';
$esayfa_aciklama = '';



// de�i�tirilecek dosyalar denetleniyor

$esayfa_aciklama .= '<b>De�i�tirilecek Dosyalar:</b> ';

if ( (isset($edbilgi['degistirilecek_dosya'])) AND (is_array($edbilgi['degistirilecek_dosya'])) )
{
	$sayi = 0;
	foreach($edbilgi['degistirilecek_dosya'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->dd_denetle('../'.$a))
			{
				$eklenti1->dosya_ac('../'.$a);

				if ( (!isset($edbilgi['kod_bul'][$sayi])) OR (!isset($edbilgi['kod_degistir'][$sayi])) )
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">de�i�iklik bilgileri yok</font>';
					$ed_hata2 = false;
				}

				elseif (!$eklenti1->bul_denetle($edbilgi['kod_bul'][$sayi]))
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">�u kod(lar) bulunam�yor: </font><br>'.$eklenti1->hata.'<br><br>';
					$ed_hata2 = false;
				}

				else
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';

					if ( (isset($ed_hata2)) AND ($ed_hata2 == false) ) $ed_hata2 = false;
					else $ed_hata2 = true;
				}
			}

			elseif ( ($eklenti1->hata == 'dosya yok') AND (preg_match('/temalar\//i', $a)) )
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

			else
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
				$ed_hata2 = false;
			}

			if ( ((isset($ed_hata)) AND ($ed_hata == true)) OR ($a != $dosya_xml) ) $ed_hata = true;
			else $ed_hata = false;

			unset($eklenti1);
			$sayi++;
		}
	}
}

else
{
	$esayfa_aciklama .= ' Yok';
	$ed_hata = false;
	$ed_hata2 = true;
}



// olu�turulacak dizinler denetleniyor, g�ncelleme de�ilse

$esayfa_aciklama .= '<br><br><b>Olu�turulacak Dizinler:</b>';
$olusturulacak_dizinler = ',';

if ( (isset($edbilgi['dizin_olustur'])) AND (is_array($edbilgi['dizin_olustur'])) AND (!isset($_GET['guncel'])) )
{
	foreach($edbilgi['dizin_olustur'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->dio_denetle('../'.$a))
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';
				$olusturulacak_dizinler .= $a.',';

				if ( (isset($edi_hata2)) AND ($edi_hata2 == false) ) $edi_hata2 = false;
				else $edi_hata2 = true;
			}

			else
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
				$edi_hata2 = false;
				}

			$edi_hata = true;
			unset($eklenti1);
		}
	}
}


else
{
	$esayfa_aciklama .= ' Yok';
	$edi_hata = false;
	$edi_hata2 = true;
}



// kopyalanacak dosyalar denetleniyor, g�ncelleme de�ilse

$esayfa_aciklama .= '<br><br><b>Eklenecek Dosyalar:</b>';

if ( (isset($edbilgi['eklenecek_dosya'])) AND (is_array($edbilgi['eklenecek_dosya'])) )
{
	foreach($edbilgi['eklenecek_dosya'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->do_denetle('../'.$a, $_GET['kur']))
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';

				if ( (isset($eo_hata2)) AND ($eo_hata2 == false) ) $eo_hata2 = false;
				else $eo_hata2 = true;
			}

			// dizin yoksa
			elseif ($eklenti1->hata == 'dizine yazma hakk� yok')
			{
				$dosyak = preg_replace('|(.*?)/([a-z0-9_\-.&]+?)$|si', '\\1', $a);
				// olu�turulacaksa
				if (preg_match('/,'.$dosyak.',/i', $olusturulacak_dizinler))
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';
					$eo_hata2 = true;
				}
				// tema ise ge�iliyor
				elseif (preg_match('/temalar\//i', $a))
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';
					if ( (isset($eo_hata2)) AND ($eo_hata2 == false) ) $eo_hata2 = false;
					else $eo_hata2 = true;
				}

				else
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
					$eo_hata2 = false;
				}
			}

			else
			{
				if ($eklenti1->hata == 'dosya var')
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">dosya var, �zerine yaz�lacak</font>';
					$eo_hata2 = true;
				}

				else
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
					$eo_hata2 = false;
				}
			}

			$eo_hata = true;
			unset($eklenti1);
		}
	}
}

else
{
	$esayfa_aciklama .= ' Yok';
	$eo_hata = false;
	$eo_hata2 = true;
}



// veritaban� i�lemleri g�r�nt�leniyor, g�ncelleme de�ilse

$esayfa_aciklama .= '<br><br><b>Veritaban� ��lemleri:</b>';

if ( (isset($edbilgi['kur_veritabani'])) AND (is_array($edbilgi['kur_veritabani'])) AND (!isset($_GET['guncel'])) )
{
	$dongu = 1;
	foreach($edbilgi['kur_veritabani'] as $a)
	{
		if ($a != '')
		{
			$esayfa_aciklama .= '<br><b>Sorgu '.$dongu.' : </b> '.$a;
			$ev_hata = true;
			$dongu++;
		}
	}
}

else
{
	$esayfa_aciklama .= ' Yok';
	$ev_hata = false;
}



$esayfa_aciklama .= '<br><br><br><hr><br>';




// HERHANG� B�R HATA VARSA  //

if ( ((!$ed_hata) AND (!$eo_hata) AND (!$edi_hata) AND (!$ev_hata)) OR (!$ed_hata2) OR (!$edi_hata2) OR (!$eo_hata2) )
{
	$esayfa_aciklama .= '<font color="#ff0000"><b>Eklenti Kurulamaz !</b><br>';

	// hi�bir dosya, dizin veya veritaban� i�lemi yok
	if ( (!$ed_hata) AND (!$eo_hata) AND (!$edi_hata) AND (!$ev_hata) ) $esayfa_aciklama .= '<br>Hi�bir dosya, dizin veya veritaban� i�lemi yok.</font>';

	// dosya de�i�im hatalar�
	if (!$ed_hata2) $esayfa_aciklama .= '<br>Dosya de�i�im i�lemlerinde hata(lar) var.';

	// dizin olu�turma hatalar� 
	if (!$edi_hata2) $esayfa_aciklama .= '<br>Dizin olu�turma i�lemlerinde hata(lar) var.';

	// dosya kopyalama hatalar� 
	if (!$eo_hata2) $esayfa_aciklama .= '<br>Dosya kopyalama i�lemlerinde hata(lar) var.';

	$esayfa_aciklama .= '</font><br><br>'.$yardim_konulari;
}




//  H��B�R HATA YOKSA KURULUMA DEVAM    //

else
{
	if (isset($_GET['guncel']))
	{
		$guncelek = 'guncel=1&amp;';
		if (isset($_GET['vt'])) $guncelek .= 'vt=1&amp;';
		if (isset($_GET['dosya'])) $guncelek .= 'dosya=1&amp;';
		if (isset($_GET['dizin'])) $guncelek .= 'dizin=1&amp;';
	}

	else $guncelek = '';

	$esayfa_aciklama .= '<br><center><font color="#669900"><b>Eklentide herhangi bir sorunla kar��la��lmad�, kurulumu ba�latabilirsiniz.</b></font>

<br><br><br>

<form action="eklentiler.php?'.$guncelek.'kur='.$_GET['kur'].'" method="post" name="form1">
<input type="hidden" name="onay" value="onay">
<input type="submit" class="dugme" value="Kurulumu Ba�lat">
</form>
	</center>';
}




//  KURULUM YAPILIYOR   //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') ):

$esayfa_aciklama = '';

if ( ((!$ed_hata) AND (!$eo_hata) AND (!$edi_hata) AND (!$ev_hata)) OR (!$ed_hata2) OR (!$edi_hata2) OR (!$eo_hata2) )
	$esayfa_aciklama .= '<p align="center"><font color="#ff0000"><b>Eklentide hatalar var, kurulamaz !</b></font></p><br><br>'.$yardim_konulari;



// sorun yoksa kurulumu ger�ekle�tir
else
{
	$esayfa_aciklama = '';
	$vt_islem = 0;
	$dosya_islem = 0;
	$dizin_islem = 0;

	// veritaban� i�lemleri yap�l�yor, g�ncelleme de�ilse
	if ( (isset($edbilgi['kur_veritabani'])) AND (is_array($edbilgi['kur_veritabani'])) AND (!isset($_GET['guncel'])) )
	{
		foreach($edbilgi['kur_veritabani'] as $a)
		{
			if ($a != '')
			{
				$ev_sonuc = mysql_query($a);

				// sorguda hata varsa
				if (!$ev_sonuc)
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000"><b>hatal� sorgu<br><br>Sorgudan d�nen hata</b></font> = '.mysql_error().'<br>';
					$ev_hata = false; break;
				}

				// sorguda hata yoksa
				else
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>sorun yok</b></font><br>';
					if ($ev_hata != false) $ev_hata = true;
				}
			}
		}
		$vt_islem = 1;
	}

	else {$ev_hata = true; $vt_islem = 0;}



	// veritaban� i�lemlerinde hata yoksa di�er i�lemlere devam
	if ($ev_hata == true)
	{
		// dosya de�i�likleri yap�l�yor
		if ( (isset($edbilgi['degistirilecek_dosya'])) AND (is_array($edbilgi['degistirilecek_dosya'])) )
		{
			// yedek dizini olu�turuluyor, i�ine index.html kopyalan�yor
			$eski_umask = umask(0);
			@mkdir('../eklentiler/'.$_GET['kur'].'/yedek');
			@copy('../eklentiler/'.$_GET['kur'].'/index.html', '../eklentiler/'.$_GET['kur'].'/yedek/index.html');
			umask($eski_umask);
			$sayi = 0;

			foreach($edbilgi['degistirilecek_dosya'] as $a)
			{
				if ($a != '')
				{
					$eklenti1 = new phpkf_eklenti();
					$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

					if ($eklenti1->dosya_ac('../'.$a))
					{
						$eklenti1->degistir($edbilgi['kod_bul'][$sayi],$edbilgi['kod_degistir'][$sayi]);
						$esayfa_aciklama .= '<br>'.$a.' = ';

						if ($eklenti1->dosya_yedekle('../'.$a, $_GET['kur']))
							$esayfa_aciklama .= '<font color="#669900"><b>yedeklendi</b></font>';
						else $esayfa_aciklama .= '<font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';

						if ($eklenti1->dosya_kaydet('../'.$a))
							$esayfa_aciklama .= ', <font color="#669900"><b>de�i�tirildi</b></font>';
						else $esayfa_aciklama .= ', <font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
					}

					elseif ( (preg_match('/\>dosya a��lam�yor\</', $eklenti1->hata)) AND (preg_match('/temalar\//i', $a)) )
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

					else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
				}

				unset($eklenti1);
				$sayi++;
			}
		}


		// dizinler olu�turuluyor, g�ncelleme de�ilse
		if ( (isset($edbilgi['dizin_olustur'])) AND (is_array($edbilgi['dizin_olustur'])) AND (!isset($_GET['guncel'])) )
		{
			$sayi = 0;
			foreach($edbilgi['dizin_olustur'] as $a)
			{
				if ($a != '')
				{
					$eklenti1 = new phpkf_eklenti();
					$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

					if ($eklenti1->dizin_olustur('../'.$a))
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>dizin olu�turuldu</b></font>';

					else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
				}

				unset($eklenti1);
				$sayi++;
			}
			$dizin_islem = 1;
		}


		// dosyalar kopyalan�yor, g�ncelleme de�ilse
		if ( (isset($edbilgi['eklenecek_dosya'])) AND (is_array($edbilgi['eklenecek_dosya'])) )
		{
			$sayi = 0;
			foreach($edbilgi['eklenecek_dosya'] as $a)
			{
				if ($a != '')
				{
					$eklenti1 = new phpkf_eklenti();
					$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

					if ($eklenti1->dosya_kopyala('../'.$a, $_GET['kur']))
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>dosya kopyaland�</b></font>';

					// tema ise ge�iliyor
					elseif (preg_match('/temalar\//i', $a))
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

					else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
				}

				unset($eklenti1);
				$sayi++;
			}
			$dosya_islem = 1;
		}


		// Etkisizle�tirme deste�i
		if (isset($edbilgi['eklenti_etkin'])) $eetkin = 1;
		else $eetkin = 2;


		if (!isset($_GET['guncel']))
		{
			// Eklenti veritaban�na ekleniyor - ilk kurulum
			$strSQL = "INSERT INTO $tablo_eklentiler (ad,kur,etkin,vt,dosya,dizin,sistem,usurum,esurum)
			VALUES ('$_GET[kur]', '1', '$eetkin', '$vt_islem', $dosya_islem, $dizin_islem,'$edbilgi[sistem]', '$edbilgi[uyumlu_surum]', '$edbilgi[eklenti_surumu]')";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}

		else
		{
			if (isset($_GET['vt'])) $vt_islem = 1;
			if (isset($_GET['dosya'])) $dosya_islem = 1;
			if (isset($_GET['dizin'])) $dizin_islem = 1;

			// Eklenti veritaban�na ekleniyor - g�ncelleme
			$strSQL = "UPDATE $tablo_eklentiler SET etkin='$eetkin',vt='$vt_islem',dosya='$dosya_islem',dizin='$dizin_islem',sistem='$edbilgi[sistem]',usurum='$edbilgi[uyumlu_surum]',esurum='$edbilgi[eklenti_surumu]' WHERE ad='$_GET[kur]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}


		$esayfa_aciklama .= '<br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Kurulum ba�ar�yla tamamlanm��t�r.</b><br><br></font><b>Geri g�nmek i�in <a href="eklentiler.php">t�klay�n.</a></b>';
	}


	// veritaban� sorgu hatas�
	else $esayfa_aciklama .= '<br><br><br><br><font color="#ff0000"><b>Yukar�daki veritaban� i�leminde hata olu�tu. Eklenti kurulumu durduruldu.<br>Hatal� sorgudan �nce sorunsuz yap�lan sorgular <u>varsa</u> bunlar ger�ekle�ti, i�lem yar�m kalm�� olabilir.</b></font><br>';
}


endif; // kur onay kapat�l�yor





//  BA�LIK DOSYASI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';


//  TEMA UYGULANIYOR    //

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');



$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => 'Eklenti Kurulum i�in Denetleniyor',
'{SAYFA_KIP}' => '',
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));




		//  KALDIRMA �NCES� DENET�M   //

elseif ( (isset($_GET['kaldir'])) AND ($_GET['kaldir'] != '') ):

$_GET['kaldir'] = zkTemizle(trim($_GET['kaldir']));


// dosya ad�nda sorun varsa
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_GET['kaldir']))
{
	header('Location: ../hata.php?hata=171');
	exit();
}


// eklenti dosyas� yoksa
if (!@is_file('../eklentiler/'.$_GET['kaldir'].'/eklenti_bilgi.xml'))
{
	header('Location: ../hata.php?hata=173');
	exit();
}


// eklenti dosyas� y�kleniyor
$edbilgi = xml_oku('../eklentiler/'.$_GET['kaldir'].'/eklenti_bilgi.xml');



// Eklenti bilgileri �ekiliyor
$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$_GET[kaldir]'";
$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$ekl_satir = mysql_fetch_assoc($ekl_sonuc);


// eklenti zaten kald�r�lm��sa
if (!isset($ekl_satir['kur']))
{
	header('Location: ../hata.php?hata=182');
	exit();
}


// eklenti_bilgi.xml dosyas�
$dosya_xml = 'eklentiler/'.$_GET['kaldir'].'/eklenti_bilgi.xml';


if (!isset($edbilgi['tema_dizini'])) $edbilgi['tema_dizini'] = '5renkli';
$esayfa_aciklama = '';



// de�i�tirilecek dosyalar denetleniyor

$esayfa_aciklama .= '<b>De�i�tirilecek Dosyalar:</b> ';

if ( (isset($edbilgi['degistirilecek_dosya'])) AND (is_array($edbilgi['degistirilecek_dosya'])) )
{
	$sayi = 0;
	foreach($edbilgi['degistirilecek_dosya'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->dd_denetle('../'.$a))
			{
				$eklenti1->dosya_ac('../'.$a);

				if (!$eklenti1->bul_denetle($edbilgi['kod_degistir'][$sayi]))
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">�u kod(lar) bulunam�yor: </font><br>'.$eklenti1->hata.'<br><br>';
					$ed_hata2 = false;
				}

				else
				{
					$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';

					if ( (isset($ed_hata2)) AND ($ed_hata2 == false) ) $ed_hata2 = false;
					else $ed_hata2 = true;
				}
			}

			elseif ( ($eklenti1->hata == 'dosya yok') AND (preg_match('/temalar\//i', $a)) )
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

			else
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
				$ed_hata2 = false;
			}

			if ( ((isset($ed_hata)) AND ($ed_hata == true)) OR ($a != $dosya_xml) ) $ed_hata = true;
			else $ed_hata = false;

			unset($eklenti1);
			$sayi++;
		}
	}
}

else
{
	$esayfa_aciklama .= ' Yok';
	$ed_hata = false;
	$ed_hata2 = true;
}



// silinecek dosyalar denetleniyor

$esayfa_aciklama .= '<br><br><b>Silinecek Dosyalar:</b>';

if ( (isset($edbilgi['eklenecek_dosya'])) AND (is_array($edbilgi['eklenecek_dosya'])) )
{
	foreach($edbilgi['eklenecek_dosya'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->ds_denetle('../'.$a, $_GET['kaldir']))
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';

				if ( (isset($es_hata2)) AND ($es_hata2 == false) ) $es_hata2 = false;
				else $es_hata2 = true;
			}

			// tema ise ge�iliyor
			elseif (preg_match('/temalar\//i', $a))
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';
				if ( (isset($es_hata2)) AND ($es_hata2 == false) ) $es_hata2 = false;
				else $es_hata2 = true;
			}

			else
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
				$es_hata2 = false;
			}

			$es_hata = true;
			unset($eklenti1);
		}
	}
}

else
{
    $esayfa_aciklama .= ' Yok';
    $es_hata = false;
    $es_hata2 = true;
}



// silinecek dizinler denetleniyor

$esayfa_aciklama .= '<br><br><b>Silinecek Dizinler:</b>';

if ( (isset($edbilgi['dizin_olustur'])) AND (is_array($edbilgi['dizin_olustur'])) )
{
	foreach($edbilgi['dizin_olustur'] as $a)
	{
		if ($a != '')
		{
			$eklenti1 = new phpkf_eklenti();

			$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

			if ($eklenti1->dis_denetle('../'.$a))
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900">sorun yok</font>';

				if ( (isset($dis_hata2)) AND ($dis_hata2 == false) ) $dis_hata2 = false;
				else $dis_hata2 = true;
			}

			else
			{
				$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
				$dis_hata2 = false;
			}

			$dis_hata = true;
			unset($eklenti1);
		}
	}
}


else
{
	$esayfa_aciklama .= ' Yok';
	$dis_hata = false;
	$dis_hata2 = true;
}




// veritaban� i�lemleri g�r�nt�leniyor

$esayfa_aciklama .= '<br><br><b>Veritaban� ��lemleri:</b>';

if ( (isset($edbilgi['kaldir_veritabani'])) AND (is_array($edbilgi['kaldir_veritabani'])) )
{
	$dongu = 1;
	foreach($edbilgi['kaldir_veritabani'] as $a)
	{
		if ($a != '')
		{
			$esayfa_aciklama .= '<br><b>Sorgu '.$dongu.' : </b> '.$a;
			$ev_hata = true;
			$dongu++;
		}
	}
}

else
{
	$esayfa_aciklama .= ' Yok';
	$ev_hata = false;
}



$esayfa_aciklama .= '<br><br><br><hr><br>';


// HERHANG� B�R HATA VARSA  //

if ( ((!$ed_hata) AND (!$es_hata) AND (!$dis_hata) AND (!$ev_hata)) OR (!$ed_hata2) OR (!$dis_hata2) OR (!$es_hata2) )
{
	$esayfa_aciklama .= '<font color="#ff0000"><b>Eklenti Kald�r�lamaz !</b><br>';

	// hi�bir dosya, dizin veya veritaban� i�lemi yok
	if ( (!$ed_hata) AND (!$es_hata) AND (!$dis_hata) AND (!$ev_hata) ) $esayfa_aciklama .= '<br>Hi�bir dosya, dizin veya veritaban� i�lemi yok.</font>';

	// dosya de�i�im hatalar�
	if (!$ed_hata2) $esayfa_aciklama .= '<br>Dosya de�i�im i�lemlerinde hata(lar) var.';

	// dizin silme hatalar� 
	if (!$dis_hata2) $esayfa_aciklama .= '<br>Dizin silme i�lemlerinde hata(lar) var.';

	// dosya silme hatalar� 
	if (!$es_hata2) $esayfa_aciklama .= '<br>Dosya silme i�lemlerinde hata(lar) var.';

	$esayfa_aciklama .= '</font><br><br>'.$yardim_konulari;
}




//  H��B�R HATA YOKSA KALDIRMA ��LEM�NE DEVAM    //

else
{
	$esayfa_aciklama .= '<br><center><font color="#669900"><b>Eklentide herhangi bir sorunla kar��la��lmad�, kald�rma i�lemini ba�latabilirsiniz.</b></font>

<br><br><br>

<form action="eklentiler.php?kaldir='.$_GET['kaldir'].'" method="post" name="form1">
<input type="hidden" name="onay" value="onay">
<input type="submit" class="dugme" value="Kald�r">
</form>
	</center>';
}




//  KALDIRMA ��LEM� YAPILIYOR   //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') )
{
	$esayfa_aciklama = '';

	if ( ((!$ed_hata) AND (!$es_hata) AND (!$ev_hata)) OR (!$ed_hata2) OR (!$es_hata2) )
		$esayfa_aciklama .= '<p align="center"><font color="#ff0000"><b>Eklentide hatalar var, kald�r�lamaz !</b></font></p><br>'.$yardim_konulari;


	// sorun yoksa kald�rma i�lemlerini ger�ekle�tir
	else
	{
		$esayfa_aciklama = '';

		// veritaban� i�lemleri yap�l�yor
		if ( (isset($edbilgi['kaldir_veritabani'])) AND (is_array($edbilgi['kaldir_veritabani'])) )
		{
			foreach($edbilgi['kaldir_veritabani'] as $a)
			{
				if ($a != '')
				{
					$ev_sonuc = mysql_query($a);

					// sorguda hata varsa
					if (!$ev_sonuc)
					{
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000"><b>hatal� sorgu<br><br>Sorgudan d�nen hata</b></font> = '.mysql_error().'<br>';
						$ev_hata = false; break;
					}

					// sorguda hata yoksa
					else
					{
						$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>sorun yok</b></font><br>';
						if ($ev_hata != false) $ev_hata = true;
					}
				}
			}
		}

		else $ev_hata = true;



		// veritaban� i�lemlerinde hata yoksa di�er i�lemlere devam
		if ($ev_hata != false)
		{
			// dosya de�i�likleri geri al�n�yor
			if ( (isset($edbilgi['degistirilecek_dosya'])) AND (is_array($edbilgi['degistirilecek_dosya'])) )
			{
				$sayi = 0;
				foreach($edbilgi['degistirilecek_dosya'] as $a)
				{
					if ($a != '')
					{
						$eklenti1 = new phpkf_eklenti();
						$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

						if ($eklenti1->dosya_ac('../'.$a))
						{
							$eklenti1->degistir($edbilgi['kod_degistir'][$sayi],$edbilgi['kod_bul'][$sayi]);

							if ($eklenti1->dosya_kaydet('../'.$a))
								$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>de�i�tirildi</b></font>';

							else $esayfa_aciklama .= '<br>'.$a.' = <font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
						}

						elseif ( (preg_match('/\>dosya a��lam�yor\</', $eklenti1->hata)) AND (preg_match('/temalar\//i', $a)) )
							$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

						else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
					}

					unset($eklenti1);
					$sayi++;
				}
			}


			// dosyalar siliniyor
			if ( (isset($edbilgi['eklenecek_dosya'])) AND (is_array($edbilgi['eklenecek_dosya'])) )
			{
				$sayi = 0;
				foreach($edbilgi['eklenecek_dosya'] as $a)
				{
					if ($a != '')
					{
						$eklenti1 = new phpkf_eklenti();
						$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

						if ($eklenti1->dosya_silme('../'.$a))
							$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>dosya silindi</b></font>';

						elseif ( ($eklenti1->hata == 'dosya yok') AND (preg_match('/temalar\//i', $a)) )
							$esayfa_aciklama .= '<br>'.$a.' = <font color="#ff8800">ge�iliyor...</font>';

						else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
					}

					unset($eklenti1);
					$sayi++;
				}
			}


			// dizinler siliniyor
			if ( (isset($edbilgi['dizin_olustur'])) AND (is_array($edbilgi['dizin_olustur'])) )
			{
				$sayi = 0;
				foreach($edbilgi['dizin_olustur'] as $a)
				{
					if ($a != '')
					{
						$eklenti1 = new phpkf_eklenti();
						$a = str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));

						if ($eklenti1->dizin_sil('../'.$a))
							$esayfa_aciklama .= '<br>'.$a.' = <font color="#669900"><b>dizin silindi</b></font>';

						else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font>';
					}

					unset($eklenti1);
					$sayi++;
				}
			}


			// Eklenti veritaban�ndan siliniyor
			$strSQL = "DELETE FROM $tablo_eklentiler WHERE ad='$_GET[kaldir]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			$esayfa_aciklama .= '<br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Kald�rma i�lemi ba�ar�yla tamamlanm��t�r.</b><br><br></font><b>Geri g�nmek i�in <a href="eklentiler.php">t�klay�n.</a></b>';
		}


		// veritaban� sorgu hatas�
		else $esayfa_aciklama .= '<br><br><br><br><font color="#ff0000"><b>Yukar�daki veritaban� i�leminde hata olu�tu. Eklenti kald�rma i�lemi durduruldu.<br>Hatal� sorgudan �nce sorunsuz yap�lan sorgular <u>varsa</u> bunlar ger�ekle�ti, i�lem yar�m kalm�� olabilir.</b></font><br>';
	}
}




//  BA�LIK DOSYASI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';


//  TEMA UYGULANIYOR    //

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');



$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => 'Eklenti Kald�rma i�in Denetleniyor',
'{SAYFA_KIP}' => '',
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));






		//  S�LME �NCES� DENET�M   //

elseif ( (isset($_GET['sil'])) AND ($_GET['sil'] != '') ):

$_GET['sil'] = zkTemizle(trim($_GET['sil']));


// dosya ad�nda sorun varsa
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_GET['sil']))
{
	header('Location: ../hata.php?hata=171');
	exit();
}


// eklenti dosyas� varsa
if (@is_file('../eklentiler/'.$_GET['sil'].'/eklenti_bilgi.xml'))
{
	// eklenti dosyas� y�kleniyor
	$edbilgi = xml_oku('../eklentiler/'.$_GET['sil'].'/eklenti_bilgi.xml');

	// Eklenti bilgileri �ekiliyor
	$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$_GET[sil]'";
	$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$ekl_satir = mysql_fetch_assoc($ekl_sonuc);

	// eklenti kald�r�l mam��sa "�nce kald�r�n" uyar�s� ver
	if ($ekl_satir['kur'] == 1)
	{
		header('Location: ../hata.php?hata=187');
		exit();
	}
}

$esayfa_aciklama = '';

$eklenti1 = new phpkf_eklenti();


// silinecek dizin denetleniyor

if ($eklenti1->dis_denetle('../eklentiler/'.$_GET['sil']))
{
	$esayfa_aciklama .= '<br>../eklentiler/'.$_GET['sil'].' = <font color="#669900">sorun yok</font>';
	$dis_hata2 = true;
}

else
{
	$esayfa_aciklama .= '<br>../eklentiler/'.$_GET['sil'].' = <font color="#ff0000">'.$eklenti1->hata.'</font>';
	$dis_hata2 = false;
}

$esayfa_aciklama .= '<br><br><br><hr><br>';


// HERHANG� B�R HATA VARSA  //

if (!$dis_hata2) $esayfa_aciklama .= '<font color="#ff0000"><b>Eklenti Silinemez !</b><br><br>Dosya silme i�leminde hata var.</font>';




//  H��B�R HATA YOKSA S�LME ��LEM�NE DEVAM    //

else
{
	$esayfa_aciklama .= '<br><center><font color="#669900"><b>Eklentide herhangi bir sorunla kar��la��lmad�, silme i�lemini ba�latabilirsiniz.</b></font>

<br><br><br>

<form action="eklentiler.php?sil='.$_GET['sil'].'" method="post" name="form1">
<input type="hidden" name="onay" value="onay">
<input type="submit" class="dugme" value="Sil">
</form>
	</center>';
}




//  S�LME ��LEM� YAPILIYOR   //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') )
{
	$esayfa_aciklama = '';

	if (!$dis_hata2) $esayfa_aciklama .= '<font color="#ff0000"><b>Eklenti Silinemez !</b><br><br>Dosya silme i�leminde hata var.</font>';


	// sorun yoksa silme i�lemini ger�ekle�tir
	else
	{
		$esayfa_aciklama = '';

		// eklenti dizin ve dosyalar� siliniyor
		if ($eklenti1->dizin_sil('../eklentiler/'.$_GET['sil']))
			$esayfa_aciklama .= '<br>'.$_GET['sil'].' = <font color="#669900"><b>dizin silindi</b></font><br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Silme i�lemi ba�ar�yla tamamlanm��t�r.</b><br><br></font><b>Geri g�nmek i�in <a href="eklentiler.php">t�klay�n.</a></b>';

		else $esayfa_aciklama .= '<br><font color="#ff0000"><b>'.$eklenti1->hata.'</b></font><br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Silme i�lemi yukar�daki hata(lar) nedeniyle tamamlanamad�.</b></font><br>';
	}
}




//  BA�LIK DOSYASI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';


//  TEMA UYGULANIYOR    //

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');



$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => 'Eklenti Silme i�in Denetleniyor',
'{SAYFA_KIP}' => '',
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));





		//  ETK�NLE�T�RME �NCES� DENET�M   //

elseif ( (isset($_GET['etkin'])) AND ($_GET['etkin'] != '') ):

$_GET['etkin'] = zkTemizle(trim($_GET['etkin']));


// dosya ad�nda sorun varsa
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_GET['etkin']))
{
	header('Location: ../hata.php?hata=171');
	exit();
}


// eklenti dosyas� yoksa
if (!@is_file('../eklentiler/'.$_GET['etkin'].'/eklenti_bilgi.xml'))
{
	header('Location: ../hata.php?hata=173');
	exit();
}


// eklenti dosyas� y�kleniyor
$edbilgi = xml_oku('../eklentiler/'.$_GET['etkin'].'/eklenti_bilgi.xml');


// Eklenti bilgileri �ekiliyor
$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$_GET[etkin]'";
$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$ekl_satir = mysql_fetch_assoc($ekl_sonuc);


// eklenti etkisizle�tirmeyi desteklemiyorsa
if ((!isset($ekl_satir['etkin'])) OR ($ekl_satir['etkin'] == 2))
{
	header('Location: ../hata.php?hata=200');
	exit();
}


// eklenti zaten etkinse
if ($ekl_satir['etkin'] == 1)
{
	header('Location: ../hata.php?hata=183');
	exit();
}


$esayfa_aciklama = '<br><center><font color="#669900"><b>Etkinle�tirme i�lemini ba�latmak i�in t�klay�n.</b></font>
<br><br><br>
<form action="eklentiler.php?etkin='.$_GET['etkin'].'" method="post" name="form1">
<input type="hidden" name="onay" value="onay">
<input type="submit" class="dugme" value="Etkinle�tir">
</form>
</center>';



//  ETK�NLE�T�RME ��LEM� YAPILIYOR   //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') )
{
	// Eklenti Veritaban�nda etkinle�tiriliyor

	$strSQL = "UPDATE $tablo_eklentiler SET etkin='1' WHERE ad='$_GET[etkin]'";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	$esayfa_aciklama = '<br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Etkinle�tirme i�lemi ba�ar�yla tamamlanm��t�r.</b><br><br></font><b>Geri g�nmek i�in <a href="eklentiler.php">t�klay�n.</a></b>';
}




//  BA�LIK DOSYASI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';


//  TEMA UYGULANIYOR    //

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');



$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => 'Etkinle�tirme i�in Denetleniyor',
'{SAYFA_KIP}' => '',
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));






		//  ETK�S�ZLE�T�RME �NCES� DENET�M   //

elseif ( (isset($_GET['etkisiz'])) AND ($_GET['etkisiz'] != '') ):
$_GET['etkisiz'] = zkTemizle(trim($_GET['etkisiz']));


// dosya ad�nda sorun varsa
if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_GET['etkisiz']))
{
	header('Location: ../hata.php?hata=171');
	exit();
}


// eklenti dosyas� yoksa
if (!@is_file('../eklentiler/'.$_GET['etkisiz'].'/eklenti_bilgi.xml'))
{
	header('Location: ../hata.php?hata=173');
	exit();
}


// eklenti dosyas� y�kleniyor
$edbilgi = xml_oku('../eklentiler/'.$_GET['etkisiz'].'/eklenti_bilgi.xml');


// Eklenti bilgileri �ekiliyor
$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$_GET[etkisiz]'";
$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
$ekl_satir = mysql_fetch_assoc($ekl_sonuc);


// eklenti zaten etkisizse
if ((!isset($ekl_satir['etkin'])) OR ($ekl_satir['etkin'] == 0))
{
	header('Location: ../hata.php?hata=184');
	exit();
}


// eklenti etkisizle�tirmeyi desteklemiyorsa
if ($ekl_satir['etkin'] == 2)
{
	header('Location: ../hata.php?hata=200');
	exit();
}



$esayfa_aciklama = '<br><center><font color="#669900"><b>Etkisizle�tirme i�lemini ba�latmak i�in t�klay�n.</b></font>
<br><br><br>
<form action="eklentiler.php?etkisiz='.$_GET['etkisiz'].'" method="post" name="form1">
<input type="hidden" name="onay" value="onay">
<input type="submit" class="dugme" value="Etkisizle�tir">
</form>
</center>';



//  ETK�S�ZLE�T�RME ��LEM� YAPILIYOR   //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') )
{
	$esayfa_aciklama = '';

	// Eklenti Veritaban�nda etkisizle�tiriliyor
	$strSQL = "UPDATE $tablo_eklentiler SET etkin='0' WHERE ad='$_GET[etkisiz]'";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	$esayfa_aciklama = '<br><br><br><br><p align="center"><font style="font-size: 17px;"><b>Etkisizle�tirme i�lemi ba�ar�yla tamamlanm��t�r.</b><br><br></font><b>Geri g�nmek i�in <a href="eklentiler.php">t�klay�n.</a></b>';
}



//  BA�LIK DOSYASI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';


//  TEMA UYGULANIYOR    //

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');



$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => 'Etkisizle�tirme i�in Denetleniyor',
'{SAYFA_KIP}' => '',
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));






		//  SAYFA NORMAL G�STER�M   //

else:
//  BA�LIK VE TEMA DOSYALARI Y�KLEN�YOR  //

$sayfa_adi = 'Y�netim Eklentiler';
include 'yonetim_baslik.php';

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/eklentiler.html');


// eklentiler dizinine yazma hakk�na bak�l�yor

$eyhakki = '<b> &nbsp; Eklentiler Dizinine Yazma Hakk�:&nbsp;</b>';

if (@fopen('../eklentiler/yokla.txt', 'w'))
{
	@unlink('../eklentiler/yokla.txt');
	$eyhakki .= '<font color="#008800"><b>Var</b></font>';
}

else $eyhakki .= '<font color="#ff0000"><b>Yok !</b></font>
<br> &nbsp; Eklenti y�kleme ve kurulumu i�in /eklentiler dizinine yazma hakk� olmal�d�r. 
<a href="eklentiler.php?kip=ayarlar">Ayarlar</a> sayfas�na FTP bilgilerini girerek i�lemin otomatik yap�lmas�n� sa�layabilir veya FTP program�n�zla yazma hakk� verebilirsiniz. (chmod 777)<br>';


// sunucu XMLReader deste�ine bak�l�yor

$xmldestek = '<b> &nbsp; Sunucu XMLReader Deste�i:&nbsp;</b>';

if (@extension_loaded('xmlreader')) $xmldestek .= '<font color="#008800"><b>Var</b></font>';
else $xmldestek .= '<font color="#ff0000"><b>Desteklenmiyor !</b></font>
<br> &nbsp; Eklentilerle ilgili hi�bir i�lem yapamazs�n�z. XMLReader deste�i i�in bar�nd�rma hizmeti ald���n�z �irket ile g�r���n.<br>';


// sunucu zip deste�ine bak�l�yor

$zipdestek = '<b> &nbsp; Sunucu Zip Deste�i:&nbsp;</b>';

if (@extension_loaded('zip')) $zipdestek .= '<font color="#008800"><b>Var</b></font>';
else $zipdestek .= '<font color="#ff0000"><b>Desteklenmiyor !</b></font>
<br> &nbsp; Eklenti y�klemek i�in sunucuda zip deste�i olmal�d�r. Y�klemek istedi�iniz eklentileri a��p (zipten ��kart�p), <br>FTP program�yla /eklentiler dizinine kendiniz kopayalabilirsiniz.';


// sunucu safe_mode ayar�na bak�l�yor

$safe_mode = '<b> &nbsp; Safe Mode:&nbsp;</b>';
if(@ini_get('safe_mode')) $safe_mode .= '<font color="#ff0000"><b>A��k !</b></font>
 &nbsp; &nbsp; Safe Mode`un a��k olmas� forum �zerinden eklenti y�klemenize engel olabilir.';
else $safe_mode .= '<font color="#008800"><b>Kapal�</b></font>';






//  EKLENT� Y�KLEME SAYFASI //

if ( (isset($_GET['kip'])) AND ($_GET['kip'] != '') ):

if ($_GET['kip'] == 'yukle'):
$sayfa_baslik2 = 'Eklenti Y�kleme';
$sayfa_kip = '<a href="eklentiler.php">Y�kl� Eklentiler</a> &nbsp; | &nbsp; Eklenti Y�kleme &nbsp; | &nbsp; <a href="eklentiler.php?kip=ayarlar">Ayarlar</a>';



//  Y�KLEME ��LEMLER� //
if ( (isset($_POST['yukleme'])) AND ($_POST['yukleme'] == 'yapildi') )
{
	if ( (isset($_FILES['eklenti_yukle']['error'])) AND ($_FILES['eklenti_yukle']['error'] != 0) )
		$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>Dosya y�klenemedi, dosya ad� al�namad� !</b></font><br><br><b>Nedeni dosyan�n 2mb.`dan b�y�k olmas� ya da<br>dosya ad�n�n kabul edilemeyen karakterler i�ermesi olabilir.</b></p>';

	elseif ( (isset($_FILES['eklenti_yukle']['tmp_name'])) AND ($_FILES['eklenti_yukle']['tmp_name'] != '') )
	{
		if ($_FILES['eklenti_yukle']['size'] > 2097952)
			$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>2mb.`dan b�y�k eklentiler buradan y�klenemez !</b></font><br><br><b>Bu eklentiyi a��p (zipten ��kart�p) FTP program�yla /eklentiler dizinine kendiniz y�kleyin.</b></p>';

		elseif ($_FILES['eklenti_yukle']['type'] != 'application/zip')
			$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>Sadece .zip uzant�l� (zip olarak s�k��t�r�lm��) eklentiler y�kleneblir !</b></font></p>';

		elseif (!@extension_loaded('zip'))
			$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>Sunucunuz zip dosyalar�n� a�may� desteklemiyor !</b></font><br><br><b>Bu eklentiyi a��p (zipten ��kart�p) FTP program�yla /eklentiler dizinine kendiniz y�kleyin.</b></p>';

		else
		{
			$arsiv = new ZipArchive;
			$zip_dosya = $arsiv->open($_FILES['eklenti_yukle']['tmp_name']);

			if ($zip_dosya === true)
			{
				$eski_umask = umask(0);
				ob_start();
				ob_implicit_flush(0);
				$arsiv->extractTo('../eklentiler/');
				$zip_hata = ob_get_contents();
				ob_end_clean();
				$arsiv->close();
				umask($eski_umask);
				$dosyaya_git = substr($_FILES['eklenti_yukle']['name'], 0, -4);
				$xml_dosya = '../eklentiler/'.$dosyaya_git.'/eklenti_bilgi.xml';


				if ($zip_hata == '')
				{
					// eklenti_bilgi.xml dosyas�n�n varl��� kontrol ediliyor
					if (!@is_file($xml_dosya))
						$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>Y�kleme tamamland� fakat eklentinin eklenti_bilgi.xml dosyas� bulunam�yor.<br>Bu eklentiyi a��p (zipten ��kart�p) FTP program�yla /eklentiler dizinine kendiniz y�kleyin.<br><br><br>Y�kl� eklentileri g�rmek i�in <a href="eklentiler.php">t�klay�n.</a></b></font>';


					else
					{
						// eklenti_bilgi.xml dosyas�n�n sat�r sonu kodu kontrol ediliyor
						$xml_ac = fopen($xml_dosya,'r');
						$xml_metni = fread($xml_ac,100);
						fclose($xml_ac);

						if (!preg_match('|\<\?xml version="1.0" encoding="iso-8859-9"\?\>\r\n\<phpKF_Eklenti\>|si', $xml_metni))
							$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>Y�kleme tamamland� fakat eklentinin eklenti_bilgi.xml dosyas�n�n sat�r sonu kodu sorunlu y�klendi.<br>Bu eklentiyi a��p (zipten ��kart�p) FTP program�yla /eklentiler dizinine kendiniz y�kleyin.<br><br><br>Y�kl� eklentileri g�rmek i�in <a href="eklentiler.php">t�klay�n.</a></b></font>';

						else $esayfa_aciklama = '<center><br><br><b>Y�kleme Tamamland� !</b><br><br>Y�kl� eklentileri g�rmek i�in <a href="eklentiler.php#'.$dosyaya_git.'">t�klay�n.</a></center>';
					}
				}


				else
				{
					$esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>ZiP DOSYASI �IKARTILAMIYOR !</b></font><br><br>Sunucu bu dizine dosya kopyalanmas�na izin vermiyor.';
					if(@ini_get('safe_mode')) $esayfa_aciklama .= ' Nedeni SAFE MODE`un a��k olmas� olabilir.';
					$esayfa_aciklama .= '<br><br><br><br><b>Hata ��kt�s�:</b><br>'.$zip_hata.'</p>';
				}
			}

			else $esayfa_aciklama = '<br><p align="center"><font color="#ff0000"><b>ZiP DOSYASI A�ILAMIYOR !</b></font><br><br><b>Hata Kodu: '.$zip_dosya.'</b></p>';
		}
	}
}


else $esayfa_aciklama = 'Bu sayfadan eklentileri sunucuya y�kledikten sonra <a href="eklentiler.php">Y�kl� Eklentiler</a> sayfas�ndan kurulum yap�n.
<br>Eklenti edinmek i�in <a href="http://www.phpkf.com/eklentiler.php" target="_blank">www.phpKF.com</a> eklentiler sayfas�n� ziyaret edin.

<br><br><ul><li>Sadece 2mb.`dan k���k ve .zip �eklinde ar�ivlenmi� eklentiler y�klenebilir.
<li>Eklenti y�kleme i�in sunucunuzda zip a�ma �zelli�i olmal�d�r.
<li>Eklentilerin y�klenece�i /eklentiler dizinine yazma hakk� olmal�d�r.
<li>Sorunlu eklentiler y�klerken de�il kurulum yaparken hata verir.</ul>


<br>'.$eyhakki.'<br>'.$zipdestek.'<br>'.$safe_mode.'<br><br><br><br><br>
<center>

<script type="text/javascript">
<!-- //
function denetle(){
	var dogruMu = true;
	if (document.eklenti_yukleme.eklenti_yukle.value.length < 4){
		dogruMu = false; 
		alert("Dosya se�meyi unuttunuz !");}
	else;
	return dogruMu;}
//  -->
</script>

<form name="eklenti_yukleme" action="eklentiler.php?kip=yukle" method="post" enctype="multipart/form-data" onsubmit="return denetle()">
<input type="hidden" name="yukleme" value="yapildi">
<input type="hidden" name="MAX_FILE_SIZE" value="2621440">
<b>Dosya Se�: &nbsp;</b><input class="formlar" name="eklenti_yukle" type="file" size="30">
<br><br><br>
&nbsp; &nbsp; &nbsp; <input class="dugme" type="submit" value="Eklenti Y�kle">
</form></center>';


$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);






//  EKLENT� AYARLARI - BA�I //

elseif ($_GET['kip'] == 'ayarlar'):

$sayfa_baslik2 = 'Eklenti Ayarlar�';
$sayfa_kip = '<a href="eklentiler.php">Y�kl� Eklentiler</a> &nbsp; | &nbsp; <a href="eklentiler.php?kip=yukle">Eklenti Y�kleme</a> &nbsp; | &nbsp; Ayarlar';

$esayfa_aciklama = '<font class="liste-etiket">Dosya-Dizin �zinleri De�i�tiriliyor</font><br><br><br>';



// ayarlar yapl�yor //

if ( (isset($_POST['onay'])) AND ($_POST['onay'] != '') ):

$ftp_sunucu = $_POST['ftp_sunucu'];
$ftp_kullanici = $_POST['ftp_kullanici'];
$ftp_sifre = $_POST['ftp_sifre'];
$ftp_kok = $_POST['ftp_kok'];
$f_dizin = substr($_POST['f_dizin'], 1);
$dzn_chmod = $_POST['dzn_chmod'];
$dsy_chmod = $_POST['dsy_chmod'];



// ftp ba�lant�s� kuruluyor

$ftp_baglanti = ftp_connect($ftp_sunucu);
$ftp_sonuc = ftp_login($ftp_baglanti, $ftp_kullanici, $ftp_sifre);

if ((!$ftp_baglanti) OR (!$ftp_sonuc))
	die('<br><h3>FTP ba�lant�s� kurulamad� !</h3>');

$esayfa_aciklama = DizinDosya_Ac($ftp_baglanti, $dzn_chmod, $dsy_chmod, $ftp_kok, $f_dizin);

ftp_close($ftp_baglanti);



// Ayarlar sayfas� giri�

else:

$esayfa_aciklama = '<br> &nbsp; &nbsp; A�a��daki alanlara FTP bilgilerinizi girerek, dosya ve dizin haklar�n� de�i�tirebilirsiniz. Girdi�iniz FTP kullan�c�s�n�n, dizin haklar�n� de�i�tirme yetkisi olmas� gereklidir.
<br><br> &nbsp; &nbsp; Sahipli�i, girdi�iniz FTP kullan�c�s�ndan farkl� olan dosyalar�n haklar� de�i�tirilemez. Bu sorun baz� �cretsiz sunucularda forum �zerinden y�klenen dosyalarda ��kmaktad�r.
<br>&nbsp;Bu sorunu d�zeltmek i�in t�m dosyalar�n sahipli�ini almal�s�n�z, bunun i�in cPanel ve benzeri panellerde "Fix File Ownership", "Reset Owner" ve "Fix File Permissions" gibi ara�lar vard�r.
<br><br>&nbsp;Dosya ve dizin haklar�n� de�i�tirme i�lemi sadece Linux sunucularda �al���r.
<br><br><br><br>


<script type="text/javascript">
<!-- //
function denetle(){
	var dogruMu = true;
	for (var i=0; i<8; i++){
	if (document.eklenti_ayarlari.elements[i].value == \'\'){ 
		dogruMu = false; 
		alert(\'T�M ALANLARIN DOLDURULMASI ZORUNLUDUR !\');
		break;}}
	return dogruMu;}
//  -->
</script>


<form name="eklenti_ayarlari" action="eklentiler.php?kip=ayarlar" method="post" onsubmit="return denetle()">
<input type="hidden" name="onay" value="onay">

<table cellspacing="1" cellpadding="10" width="430" border="0" align="center" class="tablo_border4">
	<tr class="tablo_ici">
	<td align="left" valign="top" colspan="2" height="55" class="liste-veri">
<center><b>FTP B�LG�LER�N�Z</b></center>
<br><font size="1"><i>T�m alanlar�n doldurulmas� zorunludur!</i></font>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="middle" class="liste-veri" width="170" height="50">
<b>FTP Sunucu Adresi:</b>
<br><font size="1" style="font-weight: normal">
<i>Yanl�� ise de�i�tirin.</i></font>
	</td>
	<td align="left" valign="middle">
<input class="formlar" type="text" name="ftp_sunucu" value="';


if (preg_match('/^www./i', $_SERVER['HTTP_HOST'])) 
	$esayfa_aciklama .= 'ftp.'.str_replace('www.', '', $_SERVER['HTTP_HOST']);
else $esayfa_aciklama .= 'ftp.'.$_SERVER['HTTP_HOST'];


$esayfa_aciklama .= '" size="30" maxlength="100">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="top" class="liste-veri">
<b>FTP Kullan�c� Ad�:</b>
<br><font size="1" style="font-weight: normal">
<i>Kullan�c� ad� kaydedilmez.</i></font>
	</td>
	<td align="left" valign="top">
<input class="formlar" type="text" name="ftp_kullanici" size="30" maxlength="100">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="top" class="liste-veri">
<b>FTP �ifresi:</b>
<br><font size="1" style="font-weight: normal">
<i>�ifre kaydedilmez.</i></font>
	</td>
	<td align="left" valign="top">
<input class="formlar" type="password" name="ftp_sifre" size="30" maxlength="100">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="middle" class="liste-veri" height="50">
<b>FTP K�k Dizini:</b>
<br><font size="1" style="font-weight: normal">
<i>Yanl�� ise de�i�tirin.</i></font>
	</td>
	<td align="left" valign="middle">
	<input class="formlar" type="text" name="ftp_kok" value="public_html" size="30" maxlength="100">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="middle" class="liste-veri" height="50">
<b>Forum Dizini:</b>
<br><font size="1" style="font-weight: normal">
<i>Yanl�� ise de�i�tirin.</i></font>
	</td>
	<td align="left" valign="middle">
	<input class="formlar" type="text" name="f_dizin" value="'.$ayarlar['f_dizin'].'" size="30" maxlength="100">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="middle" class="liste-veri" height="50">
<b>Dizin i�in chmod:</b>
<br><font size="1" style="font-weight: normal">
<i>Bilmiyorsan�z dokunmay�n.</i></font>
	</td>
	<td align="left" valign="middle">
	<input class="formlar" type="text" name="dzn_chmod" value="0777" size="30" maxlength="4">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" valign="middle" class="liste-veri" height="50">
<b>Dosya i�in chmod:</b>
<br><font size="1" style="font-weight: normal">
<i>Bilmiyorsan�z dokunmay�n.</i></font>
	</td>
	<td align="left" valign="middle">
	<input class="formlar" type="text" name="dsy_chmod" value="0777" size="30" maxlength="4">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="center" valign="middle" colspan="2" height="55">
<input class="dugme" type="submit" value="�zinleri De�i�tir">
	</td>
	</tr>
</table>
</form>';

endif;


$ornek1->kosul('2', array('' => ''), false);
$ornek1->kosul('1', array('' => ''), false);


endif;







//  Y�KL� EKLENT�LER -   BA�I    //

else:

$sayfa_baslik2 = 'Y�kl� Eklentiler';
$sayfa_kip = 'Y�kl� Eklentiler &nbsp; | &nbsp; <a href="eklentiler.php?kip=yukle">Eklenti Y�kleme</a> &nbsp; | &nbsp; <a href="eklentiler.php?kip=ayarlar">Ayarlar</a>';


$esayfa_aciklama = ' &nbsp; Bu sayfada <b>/eklentiler</b> dizinine y�klenilen eklentiler g�r�nt�lenmektedir.
<br> &nbsp; Sol taraftan durum bilgilerini g�rebilir; yine ayn� yerdeki kur, g�ncelle, kald�r, etkinle�tir ve etkisizle�tir yaz�lar�n� t�klayarak eklentileri y�netebilirsiniz. Eklenti edinmek i�in <a href="http://www.phpkf.com/eklentiler.php" target="_blank">www.phpKF.com</a> eklentiler sayfas�n� ziyaret edin.
<br><br><br>'.$eyhakki.'<br>'.$xmldestek.'<br>'.$zipdestek.'<br>'.$safe_mode;


$yedizin_adi = '../eklentiler/';    // eklentiler dizini
$yedizin = @opendir($yedizin_adi);  // dizini a��yoruz


//  D�Z�NDEK� EKLENT�LER D�NG�YE SOKULARAK G�R�NT�LEN�YOR   //

while ( @gettype($bilgi = @readdir($yedizin)) != 'boolean' )
{
	if ( (@is_dir($yedizin_adi.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
	{
		$guncelek = '';

		if (@is_file($yedizin_adi.$bilgi.'/eklenti_bilgi.xml'))
			$edbilgi = xml_oku($yedizin_adi.$bilgi.'/eklenti_bilgi.xml');

		else { $edbilgi = array(); $eklenti_resim = ''; $eklenti_adi = ''; $ebilgiler2 = ''; $ebilgiler3 = ''; }


		//  EKLENT�DE SORUN VARSA //

		if (!isset($edbilgi['eklenti_adi']))
		{
			$ekle_kaldir = '<br><br><br><a href="eklentiler.php?sil='.$bilgi.'" title="Bu Eklentiyi Sil">- Sil -</a>';
			$ebilgiler = '<font color="#ff0000"><b>Eklenti dizini:</b> '.$bilgi.'
			<br><br>Bu eklentide sorunlar var.<br>Y�klenirken sorun olmu� olablir, kontrol edip tekrar y�kleyin.</font><br>';
			$edbilgi = array(); $eklenti_resim = ''; $eklenti_adi = ''; $ebilgiler2 = $yardim_konulari; $ebilgiler3 = '';
		}


		elseif ( (!isset($edbilgi['eklenecek_dosya'])) AND (!isset($edbilgi['degistirilecek_dosya'])) AND (!isset($edbilgi['kur_veritabani'])) )
		{
			$ekle_kaldir = '<br><br><a href="eklentiler.php?sil='.$bilgi.'" title="Bu Eklentiyi Sil">- Sil -</a>';
			$ebilgiler = '<font color="#ff0000"><b>Eklenti dizini:</b> '.$bilgi.'
			<br><br>Bu eklentide hi�bir dosya, dizin veya veritaban� i�lemi yok.</font><br>';
			$edbilgi = array(); $eklenti_resim = ''; $eklenti_adi = ''; $ebilgiler2 = $yardim_konulari; $ebilgiler3 = '';
		}


		elseif ( (isset($edbilgi['degistirilecek_dosya'])) AND ((!isset($edbilgi['kod_bul'])) OR (!isset($edbilgi['kod_degistir']))) )
		{
			$ekle_kaldir = '<br><br><a href="eklentiler.php?sil='.$bilgi.'" title="Bu Eklentiyi Sil">- Sil -</a>';
			$ebilgiler = '<font color="#ff0000"><b>Eklenti dizini:</b> '.$bilgi.'
			<br><br>Bu eklentide bul ve de�i�tir bilgileri yok.</font><br>';
			$edbilgi = array(); $eklenti_resim = ''; $eklenti_adi = ''; $ebilgiler2 = $yardim_konulari; $ebilgiler3 = '';
		}


		//  EKLENT�DE SORUN YOKSA   //

		else
		{
			// Eklenti bilgileri �ekiliyor
			$strSQL = "SELECT * FROM $tablo_eklentiler where ad='$bilgi'";
			$ekl_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$ekl_satir = mysql_fetch_assoc($ekl_sonuc);

			$eklenti_resim = '<a href="../eklentiler/'.$bilgi.'/onizlemeb.png" target="_blank"><img src="../eklentiler/'.$bilgi.'/onizlemek.jpg" alt="eklenti g�r�n�m" border="0" width="100" height="100"></a>';


			if ($ekl_satir['etkin'] == 1)
				$eklenti_etkin = '<br><br><br><hr><br><font color="#007900">Etkin</font><br><br>
				<a href="eklentiler.php?etkisiz='.$bilgi.'" title="Bu Eklentiyi Etkisizle�tir">Etkisizle�tir</a>';

			elseif ($ekl_satir['etkin'] == 0)
				$eklenti_etkin = '<br><br><br><hr><br><font color="#ff0000">Etkisiz</font><br><br>
				<a href="eklentiler.php?etkin='.$bilgi.'" title="Bu Eklentiyi Etkinle�tir">Etkinle�tir</a>';

			else $eklenti_etkin = '';


			$eklenti_adi = '- '.$edbilgi['eklenti_adi'].' -';
			$ebilgiler = '<b>Yap�mc�s�:</b> <a href="'.@zkTemizle($edbilgi['eklenti_adres']).'" target="_blank">'.@zkTemizle($edbilgi['eklenti_yapimcisi']).'</a>';
			$ebilgiler .= '<br><b>Eklenti S�r�m�:</b> '.$edbilgi['eklenti_surumu'];
			$ebilgiler .= '<br><b>Eklenti Tarihi:</b> '.$edbilgi['eklenti_tarihi'];


			if ($edbilgi['uyumlu_surum'] == $ayarlar['surum']) $ebilgiler .= '<br><b>Uyumlu S�r�m:</b> '.$edbilgi['uyumlu_surum'];
			else $ebilgiler .= '<br><b>Uyumlu S�r�m:</b> '.$edbilgi['uyumlu_surum'].' - <font color="#ff0000"><i>( uyumsuz )</i></font>';


			if ($edbilgi['sistem'] == '1') $ebilgiler .= '<br><b>Sistem:</b> Sadece forum i�in';
			elseif ($edbilgi['sistem'] == '2') $ebilgiler .= '<br><b>Sistem:</b> Sadece portal i�in';
			elseif ($edbilgi['sistem'] == '3') $ebilgiler .= '<br><b>Sistem:</b> Forum ve portal i�in';
			else $ebilgiler .= '<br><b>Sistem:</b> Hatal� veri';


			if ($edbilgi['tip'] == '1') $ebilgiler .= '<br><b>Eklenti Tipi:</b> De�i�iklik (Mod)';
			elseif ($edbilgi['tip'] == '2') $ebilgiler .= '<br><b>Eklenti Tipi:</b> Geli�kin Eklenti';
			else $ebilgiler .= '<br><b>Eklenti Tipi:</b> Hatal� veri<br>';

			if ((isset($edbilgi['eklenti_etkin'])) AND ($edbilgi['eklenti_etkin'] == '1'))
				$ebilgiler .= '<br><b>Etkisizle�tirme:</b> Destekliyor';


			if ( (isset($edbilgi['kur_veritabani'])) AND (is_array($edbilgi['kur_veritabani'])) ){
				$ebilgiler .= '<br><b>Veritaban� ��lemi:</b> Var';
				$guncelek .= '&amp;vt=1';}


			if (isset($edbilgi['tema_adi'])) $ebilgiler .= '<br><b>Tema De�i�ikli�i:</b> '.@zkTemizle($edbilgi['tema_adi']);
			else $ebilgiler .= '<br><b>Tema De�i�ikli�i:</b> Yok';
			if (!isset($edbilgi['tema_dizini'])) $edbilgi['tema_dizini'] = '5renkli';


			$ebilgiler2 = '';

			if ( (isset($edbilgi['degistirilecek_dosya'])) AND (is_array($edbilgi['degistirilecek_dosya'])) )
			{
				$ebilgiler2 .= '<b>De�i�tirilecek Dosyalar:</b><br>';
				$dongu = 0;
				foreach($edbilgi['degistirilecek_dosya'] as $a)
				{
					if ($a != '')
					{
						if ($dongu != 0) $ebilgiler2 .= '<br>';
						$ebilgiler2 .= str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));
						$dongu++;
					}
				}
				$ebilgiler2 .= '<br><br>';
			}


			if ( (isset($edbilgi['eklenecek_dosya'])) AND (is_array($edbilgi['eklenecek_dosya'])) )
			{
				$ebilgiler2 .= '<b>Eklenecek Dosyalar:</b><br>';
				$dongu = 0;
				foreach($edbilgi['eklenecek_dosya'] as $a)
				{
					if ($a != '')
					{
						if ($dongu != 0) $ebilgiler2 .= ' - ';
						$ebilgiler2 .= str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));
						$dongu++;
					}
				}
				$ebilgiler2 .= '<br><br>';
				$guncelek .= '&amp;dosya=1';
			}


			if ( (isset($edbilgi['dizin_olustur'])) AND (is_array($edbilgi['dizin_olustur'])) )
			{
				$ebilgiler2 .= '<b>Olu�turulacak Dizinler:</b><br>';
				$dongu = 0;
				foreach($edbilgi['dizin_olustur'] as $a)
				{
					if ($a != '')
					{
						if ($dongu != 0) $ebilgiler2 .= ' - ';
						$ebilgiler2 .= str_replace('{TEMA_DIZINI}', $edbilgi['tema_dizini'], @zkTemizle($a));
						$dongu++;
					}
				}
				$guncelek .= '&amp;dizin=1';
			}


			$ebilgiler3 = '<b><a href="javascript:void(0);" title="A��klama alan�n� geni�letmek i�in t�klay�n">A��klama:</a></b>&nbsp; '.$edbilgi['aciklama'].'';


			$ekle_kaldir = '<a name="'.$bilgi.'"></a>';


			// eklenti veritaban�na eklenmi� - kurulum yap�lm��
			if ($ekl_satir['kur'] == 1)
			{
				if ($ayarlar['surum'] < $edbilgi['uyumlu_surum'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklentinin sistem ile ayn� olan s�r�m�n� indirin !<b>';

				elseif ($ekl_satir['usurum'] < $edbilgi['uyumlu_surum'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br>Eklentinin phpKF '.$ekl_satir['usurum'].' s�r�m� kurulu,<br>g�ncelleme gerekli !
					<b><br><br><a href="eklentiler.php?guncel=1'.$guncelek.'&amp;kur='.$bilgi.'" title="Bu Eklentiyi G�ncelle">- G�ncelle -</a>';

				elseif ($ekl_satir['esurum'] < $edbilgi['eklenti_surumu'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklentinin '.$ekl_satir['esurum'].' s�r�m� kurulu,<br>g�ncelleme gerekli !
					<b><br><br><a href="eklentiler.php?guncel=1'.$guncelek.'&amp;kur='.$bilgi.'" title="Bu Eklentiyi G�ncelle">- G�ncelle -</a>';

				elseif ($ekl_satir['esurum'] > $edbilgi['eklenti_surumu'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklenti, kurulu olan s�r�mden daha eski !<b>';

				elseif ($ekl_satir['usurum'] > $edbilgi['uyumlu_surum'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklentinin sistem ile ayn� olan s�r�m�n� indirin !<b>';

				elseif (($ekl_satir['usurum'] == $ayarlar['surum']) AND ($ekl_satir['usurum'] == $edbilgi['uyumlu_surum']))
					$ekle_kaldir .= '<font color="#007900">Kurulu<br><br><a href="eklentiler.php?kaldir='.$bilgi.'" title="Bu Eklentiyi Kald�r">- Kald�r -</a></font>'.$eklenti_etkin;

				elseif ($ekl_satir['usurum'] != $ayarlar['surum'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklentinin sistem ile ayn� olan s�r�m�n� indirin !<b>';

				if (($ekl_satir['usurum'] == $ayarlar['surum']) AND ($ekl_satir['usurum'] == $edbilgi['uyumlu_surum']) AND ($ekl_satir['esurum'] == $edbilgi['eklenti_surumu']));
				else $ekle_kaldir .= '<br><br><br><a href="eklentiler.php?sil='.$bilgi.'" title="Bu Eklentiyi Sil">- Sil -</a></b></font>';
			}

			// eklenti veritaban�na eklen memi� - kurulum yap�lmam��
			else
			{
				if ($edbilgi['uyumlu_surum'] != $ayarlar['surum'])
					$ekle_kaldir .= '<font color="#ff0000" style="font-weight: normal"><b>UYARI:</b><br><br>Eklentinin sistem ile ayn� olan s�r�m�n� indirin !</font>';

				else $ekle_kaldir .= '<font color="#ff0000">Kurulu de�il</font><br><br><a href="eklentiler.php?kur='.$bilgi.'" title="Bu Eklentiyi Kur">- Kur -</a>';

				$ekle_kaldir .= '<br><br><br><a href="eklentiler.php?sil='.$bilgi.'" title="Bu Eklentiyi Sil"><b>- Sil -</b></a>';
			}
		}


		//  veriler tema motoruna yollan�yor    //

		$tekli1[] = array('{EKLENTI_RESIM}' => $eklenti_resim,
		'{EKLE_KALDIR}' => $ekle_kaldir,
		'{EKLENTI_ADI}' => $eklenti_adi,
		'{EKLENTI_ACIKLAMA1}' => $ebilgiler,
		'{EKLENTI_ACIKLAMA2}' => $ebilgiler2,
		'{EKLENTI_ACIKLAMA3}' => $ebilgiler3);
	}


	// de�i�kenler siliniyor
	unset($edbilgi);
	unset($eklenti_resim);
	unset($ebilgiler);
	unset($ebilgiler2);
}

@closedir($yedizin);    // dizini kapat�yoruz



if (isset($tekli1))
{
	$ornek1->tekli_dongu('1',$tekli1);
	$ornek1->kosul('1', array('' => ''), false);
	$ornek1->kosul('2', array('' => ''), true);
}

else
{
	$ornek1->kosul('2', array('' => ''), false);
	$ornek1->kosul('1', array('{EKLENTI_YOK}' => '<br>Y�kl� Eklenti Yok<br><br><span style="font-weight:normal">Eklenti edinmek i�in <a href="http://www.phpkf.com/eklentiler.php" target="_blank">www.phpKF.com</a> eklentiler sayfas�n� ziyaret edin.</span><br><br>'), true);
}


endif; // kip kapat�l�yor


//  Y�KL� EKLENT�LER -   SONU    //




//  TEMA UYGULANIYOR    //

$ornek1->dongusuz(array('{SAYFA_BASLIK}' => 'Eklenti Y�netimi',
'{SAYFA_BASLIK2}' => $sayfa_baslik2,
'{SAYFA_KIP}' => $sayfa_kip,
'{SAYFA_ACIKLAMA}' => $esayfa_aciklama));



endif; // i�lemler kapat�l�yor

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>