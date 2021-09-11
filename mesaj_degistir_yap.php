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
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include 'kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';



if (isset($_POST['fsayfa'])) $_POST['fsayfa'] = zkTemizle($_POST['fsayfa']);
else $_POST['fsayfa'] = 0;


//  SANS�RLENECEK S�ZC�KLER ALINIYOR    //

$strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='sozcukler' LIMIT 1";
$yasak_sonuc = mysql_query($strSQL);
$yasak_sozcukler = mysql_fetch_row($yasak_sonuc);
$ysk_sozd = explode("\r\n", $yasak_sozcukler[0]);


//  SANS�R C�MLES� ALINIYOR //

$strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='cumle' LIMIT 1";
$yasak_sonuc = mysql_query($strSQL);
$yasak_cumle = mysql_fetch_row($yasak_sonuc);


//  SANS�R UYGULANIYOR  //

if ( ($ysk_sozd[0] != '') AND (!empty($_POST['mesaj_baslik'])) AND (!empty($_POST['mesaj_icerik'])) )
{
    if (function_exists('str_ireplace'))
    {
        $_POST['mesaj_baslik'] = str_ireplace($ysk_sozd, $yasak_cumle[0], $_POST['mesaj_baslik']);
        $_POST['mesaj_icerik'] = str_ireplace($ysk_sozd, $yasak_cumle[0], $_POST['mesaj_icerik']);
    }

    else
    {
        $_POST['mesaj_baslik'] = str_replace($ysk_sozd, $yasak_cumle[0], $_POST['mesaj_baslik']);
        $_POST['mesaj_icerik'] = str_replace($ysk_sozd, $yasak_cumle[0], $_POST['mesaj_icerik']);
    }
}


//	FORM DOLU MU? ZARARLI KODLAR TEM�ZLEN�YOR	//

if ( ( isset($_POST['mesaj_degisti_mi']) ) AND ($_POST['mesaj_degisti_mi'] == 'form_dolu') ):
$_POST['mesaj_no'] = @zkTemizle($_POST['mesaj_no']);
$_POST['cevap_no'] = @zkTemizle($_POST['cevap_no']);

//	magic_quotes_gpc a��ksa	//
if (get_magic_quotes_gpc(1))
{
	$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),1);
	$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),2);
}

//	magic_quotes_gpc kapal�ysa	//
else
{
	$_POST['mesaj_baslik'] = @ileti_yolla($_POST['mesaj_baslik'],1);
	$_POST['mesaj_icerik'] = @ileti_yolla($_POST['mesaj_icerik'],2);
}


// bbcode kullanma bilgisi
if (isset($_POST['bbcode_kullan'])) $bbcode_kullan = 1;
else $bbcode_kullan = 0;


// ifade kullanma bilgisi
if (isset($_POST['ifade'])) $ifade_kullan = 1;
else $ifade_kullan = 0;


// �st konu bilgisi
if (isset($_POST['ust_konu'])) $ust_konu_yap = 1;
else $ust_konu_yap = 0;


$ust_konu = '';

$tarih = time();


//	DE���T�R�LEN BA�LIK �SE	//
//	DE���T�R�LEN BA�LIK �SE	//



if ($_POST['kip'] == 'mesaj')
{
	//	ALANLAR BO� �SE VEYA 53 KARAKTERDEN UZUN �SE HATA MESAJI	//

	if (( strlen($_POST['mesaj_baslik']) > 53) or ( strlen($_POST['mesaj_baslik']) < 3) or ( strlen($_POST['mesaj_icerik']) < 3))
	{
		header('Location: hata.php?hata=53');
		exit();
	}


	//	KONUNUN B�LG�LER� �EK�L�YOR	//

	$strSQL = "SELECT hangi_forumdan,yazan,degistirme_sayisi,kilitli FROM $tablo_mesajlar WHERE id='$_POST[mesaj_no]' AND silinmis='0' LIMIT 1";
	$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// konu yoksa uyar� ver //
	if (!mysql_num_rows($sonuc3))
	{
		header('Location: hata.php?hata=47');
		exit();
	}


	$yetkili_mi = mysql_fetch_assoc($sonuc3);
	$fno = $yetkili_mi['hangi_forumdan'];


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$fno' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	�Z�NLERDEN B�R� SADECE Y�NET�C�LER ���NSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=52');
			exit();
		}
	}


	// konu kilitli ise de�i�tirilemez uyar�s� veriliyor //

	if ( ($yetkili_mi['kilitli'] == 1) AND (($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2)) )
	{
		header('Location: hata.php?hata=50');
		exit();
	}



//	DE���T�RMEYE YETK�L� OLUP OLMADI�INA BAKILIYOR	- BA�I	//


    //	Y�NET�C� VE YARDICI �SE	//
    if ( ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) )
        $ust_konu = ",ust_konu='$ust_konu_yap'";

    //	B�L�M YARDIMCISI �SE	//
    if ($kullanici_kim['yetki'] == 3)
    {
        if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$fno' AND yonetme='1' OR";
        else $grupek = "grup='0' AND";

        //	KEND� YAZISI DE��LSE	//
        if ( ($yetkili_mi['yazan'] != $kullanici_kim['kullanici_adi']) )
        {
            $strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$fno' AND yonetme='1'";
            $kul_izin = mysql_query($strSQL);

            if ( !mysql_num_rows($kul_izin) )
            {
                header('Location: hata.php?hata=52');
                exit();
            }

            //  B�L�M YARDIMCISI �SE �ST KONU YAPAB�L�R  //
            else $ust_konu = ",ust_konu='$ust_konu_yap'";
        }

        //	KEND� YAZISI �SE -- B�L�M YARDIMCISI �SE �ST KONU YAPAB�L�R  //
        else
        {
            $strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$fno' AND yonetme='1'";
            $kul_izin = mysql_query($strSQL);
            if (mysql_num_rows($kul_izin))
            $ust_konu = ",ust_konu='$ust_konu_yap'";
        }
    }

    //	YAZAN, Y�NET�C� VEYA YARDIMCI �SE	//
    elseif ( ($yetkili_mi['yazan'] == $kullanici_kim['kullanici_adi']) OR ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) );

    //	H��B�R� DE��LSE	//
    else
    {
        header('Location: hata.php?hata=52');
        exit();
    }

//	DE���T�RMEYE YETK�L� OLUP OLMADI�INA BAKILIYOR	- SONU	//




//	BA�LIK DE���T�R�L�YOR	//

    $strSQL = "UPDATE $tablo_mesajlar SET degistirme_tarihi='$tarih',mesaj_baslik='$_POST[mesaj_baslik]',mesaj_icerik='$_POST[mesaj_icerik]',degistiren='$kullanici_kim[kullanici_adi]',degistirme_sayisi=degistirme_sayisi + 1,degistiren_ip='$_SERVER[REMOTE_ADDR]',bbcode_kullan='$bbcode_kullan',ifade='$ifade_kullan' $ust_konu WHERE id='$_POST[mesaj_no]' LIMIT 1";

    $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


//	BA�LIK DE���T�R�LD� �LET�S�	//

    header('Location: hata.php?bilgi=3&fno='.$fno.'&mesaj_no='.$_POST['mesaj_no'].'&fsayfa='.$_POST['fsayfa']);
    exit();
}




//	DE���T�R�LEN CEVAP �SE	//
//	DE���T�R�LEN CEVAP �SE	//


elseif ($_POST['kip'] == 'cevap')
{
	//	ALANLAR BO� �SE VEYA 53 KARAKTERDEN UZUN �SE HATA MESAJI	//

	if (( strlen($_POST['mesaj_baslik']) > 53) or ( strlen($_POST['mesaj_icerik']) < 3))
	{
		header('Location: hata.php?hata=53');
		exit();
	}


	//	CEVAP B�LG�LER� VER�TABANINDAN �EK�L�YOR	//

	$strSQL = "SELECT cevap_yazan,degistirme_sayisi,hangi_forumdan,hangi_basliktan FROM $tablo_cevaplar
			WHERE id='$_POST[cevap_no]' AND silinmis='0' LIMIT 1";
	$sonuc4 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	// cevap yoksa uyar� ver //
	if (!mysql_num_rows($sonuc4))
	{
		header('Location: hata.php?hata=55');
		exit();
	}


	$yetkili_mi = mysql_fetch_assoc($sonuc4);
	$fno = $yetkili_mi['hangi_forumdan'];


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$fno' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	�Z�NLERDEN B�R� SADECE Y�NET�C�LER ���NSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=52');
			exit();
		}
	}


	// konu kilitli ise de�i�tirilemez uyar�s� veriliyor //

	$strSQL = "SELECT kilitli FROM $tablo_mesajlar WHERE id='$yetkili_mi[hangi_basliktan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$konu_kilitlimi = mysql_fetch_assoc($sonuc);

	if ( ($konu_kilitlimi['kilitli'] == 1) AND (($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2)) )
	{
		header('Location: hata.php?hata=51');
		exit();
	}



//	DE���T�RMEYE YETK�L� OLUP OLMADI�INA BAKILIYOR	- BA�I	//

    //	B�L�M YARDIMCISI �SE	//
    if ($kullanici_kim['yetki'] == 3)
    {
        //	KEND� YAZISI DE��LSE	//
        if ( ($yetkili_mi['cevap_yazan'] != $kullanici_kim['kullanici_adi']) )
        {
             if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$fno' AND yonetme='1' OR";
             else $grupek = "grup='0' AND";

            $strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$fno' AND yonetme='1'";
            $kul_izin = mysql_query($strSQL);

            if ( !mysql_num_rows($kul_izin) )
            {
                header('Location: hata.php?hata=52');
                exit();
            }
        }
    }

    //	YAZAN, Y�NET�C� VEYA YARDIMCI �SE	//
    elseif ( ($yetkili_mi['cevap_yazan'] == $kullanici_kim['kullanici_adi']) OR ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) );

    //	H��B�R� DE��LSE	//
    else
    {
        header('Location: hata.php?hata=52');
        exit();
    }

//	DE���T�RMEYE YETK�L� OLUP OLMADI�INA BAKILIYOR	- SONU	//




    if ($_POST['mesaj_baslik']=='') $_POST['mesaj_baslik'] = 'Cvp:';


    //		CEVAP DE���T�R�LiYOR		//

    $strSQL = "UPDATE $tablo_cevaplar SET degistirme_tarihi='$tarih',cevap_baslik='$_POST[mesaj_baslik]',cevap_icerik='$_POST[mesaj_icerik]',degistiren='$kullanici_kim[kullanici_adi]',degistirme_sayisi=degistirme_sayisi + 1,degistiren_ip='$_SERVER[REMOTE_ADDR]',bbcode_kullan='$bbcode_kullan',ifade='$ifade_kullan' WHERE id='$_POST[cevap_no]' LIMIT 1";

    $sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


    //	CEVAP DE���T�R�LD� �LET�S�	//

    header('Location: hata.php?bilgi=4&fno='.$fno.'&mesaj_no='.$_POST['mesaj_no'].'&fsayfa='.$_POST['fsayfa'].'&sayfa='.$_POST['sayfa'].'&cevapno='.$_POST['cevap_no']);
    exit();
}

endif;



//		KONU K�L�TLEME ��LEMLER� 		//


if ($_GET['kip'] == 'kilitle')
{
	if (!isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = $forum_index;
	$_GET['mesaj_no'] = zkTemizle($_GET['mesaj_no']);

	$strSQL = "SELECT kilitli,hangi_forumdan FROM $tablo_mesajlar
				WHERE id='$_GET[mesaj_no]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$kilit_satir = mysql_fetch_assoc($sonuc);

	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=47');
		exit();
	}


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$kilit_satir[hangi_forumdan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	�Z�NLERDEN B�R� SADECE Y�NET�C�LER ���NSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=54');
			exit();
		}
	}



	//	Y�NET�C� VE YARDIMCI �SE	//
	if ( ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) )
	{
		if ($kilit_satir['kilitli'] == 1)
		{
			$strSQL = "UPDATE $tablo_mesajlar SET kilitli='0'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}

		else
		{
			$strSQL = "UPDATE $tablo_mesajlar SET kilitli='1'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}

		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();
	}


	//	B�L�M YARDIMCISI �SE	//
	elseif ($kullanici_kim['yetki'] == 3)
	{
		if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$kilit_satir[hangi_forumdan]' AND yonetme='1' OR";
		else $grupek = "grup='0' AND";

		$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$kilit_satir[hangi_forumdan]' AND yonetme='1'";
		$kul_izin = mysql_query($strSQL);

		if (mysql_num_rows($kul_izin))
		{
			if ($kilit_satir['kilitli'] == 1)
			{
				$strSQL = "UPDATE $tablo_mesajlar SET kilitli='0'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}

			else
			{
				$strSQL = "UPDATE $tablo_mesajlar SET kilitli='1'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}

			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		}

		//	BU FORUMU Y�NETME YETK�S� YOKSA	//
		else
		{
			header('Location: hata.php?hata=54');
			exit();
		}
	}

	//		YETK�S� YOKSA		//
	else
	{
		header('Location: hata.php?hata=54');
		exit();
	}
}




//		�ST KONU YAPMA ��LEMLER� 		//


if ($_GET['kip'] == 'ustkonu')
{
	if (!isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = $forum_index;
	$_GET['mesaj_no'] = zkTemizle($_GET['mesaj_no']);


	$strSQL = "SELECT ust_konu,hangi_forumdan FROM $tablo_mesajlar
				WHERE id='$_GET[mesaj_no]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$ustkonu_satir = mysql_fetch_assoc($sonuc);


	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=47');
		exit();
	}


	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni,forum_baslik,alt_forum FROM $tablo_forumlar WHERE id='$ustkonu_satir[hangi_forumdan]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_assoc($sonuc);


	//	�Z�NLERDEN B�R� SADECE Y�NET�C�LER ���NSE VEYA KAPALIYSA	//

	if ( ($forum_satir['okuma_izni'] == 1) OR ($forum_satir['konu_acma_izni'] == 1) OR ($forum_satir['yazma_izni'] == 1) OR ($forum_satir['okuma_izni'] == 5) OR ($forum_satir['konu_acma_izni'] == 5) OR ($forum_satir['yazma_izni'] == 5) )
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=170');
			exit();
		}
	}



	//	Y�NET�C� VEYA YARDIMCI �SE	//
	if ( ($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2) )
	{
		if ($ustkonu_satir['ust_konu'] == 1)
		{
			$strSQL = "UPDATE $tablo_mesajlar SET ust_konu='0'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}

		else
		{
			$strSQL = "UPDATE $tablo_mesajlar SET ust_konu='1'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}

		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();
	}


	//	B�L�M YARDIMCISI �SE	//
	elseif ($kullanici_kim['yetki'] == 3)
	{
		if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$ustkonu_satir[hangi_forumdan]' AND yonetme='1' OR";
		else $grupek = "grup='0' AND";

		$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$ustkonu_satir[hangi_forumdan]' AND yonetme='1'";
		$kul_izin = mysql_query($strSQL);

		if (mysql_num_rows($kul_izin))
		{
			if ($ustkonu_satir['ust_konu'] == 1)
			{
				$strSQL = "UPDATE $tablo_mesajlar SET ust_konu='0'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}

			else
			{
				$strSQL = "UPDATE $tablo_mesajlar SET ust_konu='1'
						WHERE id='$_GET[mesaj_no]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}

			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		}

		//	BU FORUMU Y�NETME YETK�S� YOKSA	//
		else
		{
			header('Location: hata.php?hata=170');
			exit();
		}
	}

	//		YETK�S� YOKSA		//
	else
	{
		header('Location: hata.php?hata=170');
		exit();
	}
}

?>