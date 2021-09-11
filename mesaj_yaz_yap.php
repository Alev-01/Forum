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


//	FORM DOLU MU ?	//

if ( (isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu') )
{
	if (is_numeric($_POST['fno']) == false)
	{
		header('Location: hata.php?hata=14');
		exit();
	}

	else $_POST['fno'] = zkTemizle2($_POST['fno']);


	// FORUM B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_satir = mysql_fetch_array($sonuc);

	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=14');
		exit();
	}


	if (is_numeric($_POST['mesaj_no']) == false)
	{
		header('Location: hata.php?hata=47');
		exit();
	}

	else $_POST['mesaj_no'] = zkTemizle($_POST['mesaj_no']);




		//	FORUM YETK�LER� - BA�I	//
		//	FORUM YETK�LER� - BA�I	//



// forum okumaya kapal�ysa sadece y�neticiler girebilir
if ($forum_satir['okuma_izni'] == 5)
{
	if ( (!isset($kullanici_kim['yetki']) ) OR ($kullanici_kim['yetki'] != 1) )
	{
		header('Location: hata.php?hata=164');
		exit();
	}
}



	//	KULLANICIYA G�RE CEVAP YAZMA - BA�I		//

if ($_POST['kip'] == 'cevapla')
{
	// KONUNUN K�L�T DURUMUNA BAKILIYOR

	$strSQL = "SELECT kilitli FROM $tablo_mesajlar WHERE id='$_POST[mesaj_no]' AND silinmis='0' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$kilit_satir = mysql_fetch_array($sonuc);

	// konu yok uyar�s�
	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=47');
		exit();
	}

	// konu kilitli uyar�s�
	elseif ($kilit_satir['kilitli'] == 1)
	{
		header('Location: hata.php?hata=57');
		exit();
	}



	//	OKUMA �ZN� SADECE Y�NET�C�LER ���NSE	//

	if ($forum_satir['okuma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=15');
			exit();
		}
	}


	//	CEVAP YAZMAYA KAPALIYSA	//

	elseif ($forum_satir['yazma_izni'] == 5)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=193');
			exit();
		}
	}


	//	CEVAP YAZMA �ZN� SADECE Y�NET�C�LER ���NSE	//

	elseif ($forum_satir['yazma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=58');
			exit();
		}
	}


	//	CEVAP YAZMA �ZN� SADECE Y�NET�C�LER VE YARDIMCILAR ���NSE	//

	elseif ($forum_satir['yazma_izni'] == 2)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
			AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
		{
			header('Location: hata.php?hata=59');
			exit();
		}
	}


	//	CEVAP YAZMA �ZN� SADECE �ZEL �YELER ���NSE 	//

	elseif ($forum_satir['yazma_izni'] == 3)
	{
		//	Y�NET�C� DE��LSE KO�ULLARA BAK	//

		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2) )
		{
			if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_POST[fno]' AND yazma='1' OR";
			else $grupek = "grup='0' AND";

			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_POST[fno]' AND yazma='1'";
			$kul_izin = mysql_query($strSQL);

			if ( !mysql_num_rows($kul_izin) )
			{
				header('Location: hata.php?hata=60');
				exit();
			}
		}
	}
}

	//	KULLANICIYA G�RE CEVAP YAZMA - SONU			//




	//	KULLANICIYA G�RE KONU A�MA - BA�I		//

else
{
	//	OKUMA �ZN� SADECE Y�NET�C�LER ���NSE	//

	if ($forum_satir['okuma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=15');
			exit();
		}
	}


	//	KONU A�MAYA KAPALIYSA 	//

	elseif ($forum_satir['konu_acma_izni'] == 5)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=192');
			exit();
		}
	}


	//	SADECE Y�NET�C�LER ���NSE	//

	elseif ($forum_satir['konu_acma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=165');
			exit();
		}
	}


	//	SADECE Y�NET�C�LER VE YARDIMCILAR ���NSE	//

	elseif ($forum_satir['konu_acma_izni'] == 2)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
			AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
		{
			header('Location: hata.php?hata=166');
			exit();
		}
	}


	//	SADECE �ZEL �YELER ���NSE 	//

	elseif ($forum_satir['konu_acma_izni'] == 3)
	{
		//	Y�NET�C� DE��LSE KO�ULLARA BAK	//

		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) AND ($kullanici_kim['yetki'] != 2) )
		{
			if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_POST[fno]' AND konu_acma='1' OR";
			else $grupek = "grup='0' AND";

			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_POST[fno]' AND konu_acma='1'";
			$kul_izin = mysql_query($strSQL);

			if ( !mysql_num_rows($kul_izin) )
			{
				header('Location: hata.php?hata=167');
				exit();
			}
		}
	}
}

	//	KULLANICIYA G�RE KONU A�MA - SONU			//




		//	FORUM YETK�LER� - SONU	//
		//	FORUM YETK�LER� - SONU	//





	//	�K� �LET� ARASI S�RES� DOLMAMI�SA UYARILIYOR	//

	$tarih = time();
	
	if ( ($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']) )
	{
		header('Location: hata.php?hata=6');
		exit();
	}


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

    if ($ysk_sozd[0] != '')
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



    //	ZARARLI KODLAR TEM�ZLEN�YOR	//

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




						//	YAZILAN YEN� BA�LIKSA	//



	if ($_POST['kip'] == 'yeni')
	{
		//		�ST KONU B�LG�S�		//

		if (isset($_POST['ust_konu']))
		{
			if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2)) $ust_konu = 1;

			elseif ($kullanici_kim['yetki'] == 3)
			{
				if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_POST[fno]' AND yonetme='1' OR";
				else $grupek = "grup='0' AND";

				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_POST[fno]' AND yonetme='1'";
				$kul_izin = mysql_query($strSQL);

				//	Y�NETME YETK�S� VARSA	//
				if (mysql_num_rows($kul_izin)) $ust_konu = 1;
				else $ust_konu = 0;
			}
		}

		else $ust_konu = 0;


		//	ALANLAR BO� �SE VEYA 53 KARAKTERDEN UZUN �SE	//
		
		if (( strlen($_POST['mesaj_baslik']) >  53) OR ( strlen($_POST['mesaj_baslik']) <  3) OR ( strlen($_POST['mesaj_icerik']) <  3))
		{
			header('Location: hata.php?hata=53');
			exit();
		}

		else
		{
			//	YEN� BA�LIK VER�TABANINA G�R�L�YOR	//

			$strSQL = "INSERT INTO $tablo_mesajlar (tarih, mesaj_baslik, mesaj_icerik, yazan, hangi_forumdan, son_mesaj_tarihi,yazan_ip,bbcode_kullan,ust_konu,ifade)";
	
			$strSQL .= "VALUES ('$tarih','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$kullanici_kim[kullanici_adi]','$_POST[fno]','$tarih','$_SERVER[REMOTE_ADDR]','$bbcode_kullan','$ust_konu','$ifade_kullan')";

			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// veritaban�na yap�lan son kayd�n id`si al�n�yor //
			$ymesaj_no = mysql_insert_id();


			//	KULLANICININ MESAJ SAYISI ARTTIRILIYOR VE SON �LET� TAR�H� G�R�L�YOR	//

			$strSQL = "UPDATE $tablo_kullanicilar SET mesaj_sayisi=mesaj_sayisi + 1, son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//	FORUMUN KONU SAYISI ARTTIRILIYOR //

			$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1 WHERE id='$_POST[fno]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//	BA�LIK G�NDER�LD� �LET�S�	//

			header('Location: hata.php?bilgi=2&fno='.$_POST['fno'].'&mesaj_no='.$ymesaj_no);
		}
	}





							//	YAZILAN CEVAPSA	//



	elseif ($_POST['kip'] == 'cevapla')
	{
		//	ALANLAR BO� �SE VEYA 53 KARAKTERDEN UZUN �SE	//

		if (( strlen($_POST['mesaj_baslik']) >  53) or ( strlen($_POST['mesaj_icerik']) <  3))
		{
			header('Location: hata.php?hata=53');
			exit();
		}

		else
		{
			//	BA�LIK G�R�LMEM��SE Cvp: EKLE	//

			if ($_POST['mesaj_baslik'] == '')
			$_POST['mesaj_baslik'] = 'Cvp:';


			//	CEVAP VER�TABANINA G�R�L�YOR	//

			$strSQL = "INSERT INTO $tablo_cevaplar (tarih, cevap_baslik, cevap_icerik, cevap_yazan, hangi_basliktan, hangi_forumdan,yazan_ip,bbcode_kullan,ifade)";

			$strSQL .= "VALUES ('$tarih','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$kullanici_kim[kullanici_adi]','$_POST[mesaj_no]','$_POST[fno]','$_SERVER[REMOTE_ADDR]','$bbcode_kullan','$ifade_kullan')";

			$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			$cevapno = mysql_insert_id();


			//	BA�LI�IN CEVAP SAYISI ARTTIRILIYOR, SON CEVAP NO, TAR�H� VE YAZAN G�R�L�YOR		//

			$strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=cevap_sayi + 1, son_mesaj_tarihi='$tarih',son_cevap='$cevapno',son_cevap_yazan='$kullanici_kim[kullanici_adi]' WHERE id='$_POST[mesaj_no]' LIMIT 1";

			$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//	KULLANICININ MESAJ SAYISI ARTTIRILIYOR VE SON �LET� TAR�H� G�R�L�YOR	//

			$strSQL = "UPDATE $tablo_kullanicilar SET mesaj_sayisi=mesaj_sayisi + 1, son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//	FORUMUN CEVAP SAYISI ARTTIRILIYOR //

			$strSQL = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$_POST[fno]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			//	CEVAP G�NDER�LD� �LET�S�	//

			if ((isset($_POST['mobil'])) AND ($_POST['mobil'] == 'mobil')) $yonlendir = 'mobil.php?ak='.$_POST['mesaj_no'].'&aks='.$_POST['sayfa'].'.#hcevap';

			else $yonlendir = 'hata.php?bilgi=1&fno='.$_POST['fno'].'&mesaj_no='.$_POST['mesaj_no'].'&sayfa='.$_POST['sayfa'].'&cevapno='.$cevapno;

			header('Location: '.$yonlendir);
			exit();
		}
	}
}


else
{
    header('Location: hata.php?hata=53');
    exit();
}
?>