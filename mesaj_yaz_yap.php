<?php
/*
 +-=====================================================================-+
 |                     php Kolay Forum (phpKF) v1.90                     |
 +-----------------------------------------------------------------------+
 |             Telif - Copyright (c) 2007 - 2012 Adem YILMAZ             |
 |               http://www.phpkf.com   -   phpkf@phpkf.com              |
 |               Tüm haklarý saklýdýr - All Rights Reserved              |
 +-----------------------------------------------------------------------+
 |  Bu betik üzerinde deðiþiklik yaparak/yapmayarak kullanabilirsiniz.   |
 |  Betiði daðýtma ve resmi sürüm çýkartma haklarý sadece yazara aittir. |
 |  Hiçbir þekilde para ile satýlamaz veya baþka bir yerde daðýtýlamaz.  |
 |  Betiðin (script) tamamý veya bir kýsmý, kaynak belirtilerek          |
 |  dahi olsa, baþka bir betikte kesinlikle kullanýlamaz.                |
 |  Kodlardaki ve sayfalarýn en altýndaki telif yazýlarý silinemez,      |
 |  deðiþtirilemez, veya bu telif ile çeliþen baþka bir telif eklenemez. |
 |                                                                       |
 |  Telif maddelerinin deðiþtirilme hakký saklýdýr.                      |
 |  Güncel ve tam telif maddeleri için www.phpkf.com`u ziyaret edin.     |
 |  Emeðe saygý göstererek bu kurallara uyunuz ve bu bölümü silmeyiniz.  |
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


	// FORUM BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT id,okuma_izni,yazma_izni,konu_acma_izni FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
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




		//	FORUM YETKÝLERÝ - BAÞI	//
		//	FORUM YETKÝLERÝ - BAÞI	//



// forum okumaya kapalýysa sadece yöneticiler girebilir
if ($forum_satir['okuma_izni'] == 5)
{
	if ( (!isset($kullanici_kim['yetki']) ) OR ($kullanici_kim['yetki'] != 1) )
	{
		header('Location: hata.php?hata=164');
		exit();
	}
}



	//	KULLANICIYA GÖRE CEVAP YAZMA - BAÞI		//

if ($_POST['kip'] == 'cevapla')
{
	// KONUNUN KÝLÝT DURUMUNA BAKILIYOR

	$strSQL = "SELECT kilitli FROM $tablo_mesajlar WHERE id='$_POST[mesaj_no]' AND silinmis='0' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$kilit_satir = mysql_fetch_array($sonuc);

	// konu yok uyarýsý
	if (!mysql_num_rows($sonuc))
	{
		header('Location: hata.php?hata=47');
		exit();
	}

	// konu kilitli uyarýsý
	elseif ($kilit_satir['kilitli'] == 1)
	{
		header('Location: hata.php?hata=57');
		exit();
	}



	//	OKUMA ÝZNÝ SADECE YÖNETÝCÝLER ÝÇÝNSE	//

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


	//	CEVAP YAZMA ÝZNÝ SADECE YÖNETÝCÝLER ÝÇÝNSE	//

	elseif ($forum_satir['yazma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=58');
			exit();
		}
	}


	//	CEVAP YAZMA ÝZNÝ SADECE YÖNETÝCÝLER VE YARDIMCILAR ÝÇÝNSE	//

	elseif ($forum_satir['yazma_izni'] == 2)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
			AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
		{
			header('Location: hata.php?hata=59');
			exit();
		}
	}


	//	CEVAP YAZMA ÝZNÝ SADECE ÖZEL ÜYELER ÝÇÝNSE 	//

	elseif ($forum_satir['yazma_izni'] == 3)
	{
		//	YÖNETÝCÝ DEÐÝLSE KOÞULLARA BAK	//

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

	//	KULLANICIYA GÖRE CEVAP YAZMA - SONU			//




	//	KULLANICIYA GÖRE KONU AÇMA - BAÞI		//

else
{
	//	OKUMA ÝZNÝ SADECE YÖNETÝCÝLER ÝÇÝNSE	//

	if ($forum_satir['okuma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=15');
			exit();
		}
	}


	//	KONU AÇMAYA KAPALIYSA 	//

	elseif ($forum_satir['konu_acma_izni'] == 5)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=192');
			exit();
		}
	}


	//	SADECE YÖNETÝCÝLER ÝÇÝNSE	//

	elseif ($forum_satir['konu_acma_izni'] == 1)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1) )
		{
			header('Location: hata.php?hata=165');
			exit();
		}
	}


	//	SADECE YÖNETÝCÝLER VE YARDIMCILAR ÝÇÝNSE	//

	elseif ($forum_satir['konu_acma_izni'] == 2)
	{
		if ( ( isset($kullanici_kim['yetki']) ) AND ($kullanici_kim['yetki'] != 1)
			AND ($kullanici_kim['yetki'] != 2) AND ($kullanici_kim['yetki'] != 3) )
		{
			header('Location: hata.php?hata=166');
			exit();
		}
	}


	//	SADECE ÖZEL ÜYELER ÝÇÝNSE 	//

	elseif ($forum_satir['konu_acma_izni'] == 3)
	{
		//	YÖNETÝCÝ DEÐÝLSE KOÞULLARA BAK	//

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

	//	KULLANICIYA GÖRE KONU AÇMA - SONU			//




		//	FORUM YETKÝLERÝ - SONU	//
		//	FORUM YETKÝLERÝ - SONU	//





	//	ÝKÝ ÝLETÝ ARASI SÜRESÝ DOLMAMIÞSA UYARILIYOR	//

	$tarih = time();
	
	if ( ($kullanici_kim['son_ileti']) > ($tarih - $ayarlar['ileti_sure']) )
	{
		header('Location: hata.php?hata=6');
		exit();
	}


    //  SANSÜRLENECEK SÖZCÜKLER ALINIYOR    //

    $strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='sozcukler' LIMIT 1";
    $yasak_sonuc = mysql_query($strSQL);
    $yasak_sozcukler = mysql_fetch_row($yasak_sonuc);
    $ysk_sozd = explode("\r\n", $yasak_sozcukler[0]);


    //  SANSÜR CÜMLESÝ ALINIYOR //

    $strSQL = "SELECT deger FROM $tablo_yasaklar WHERE etiket='cumle' LIMIT 1";
    $yasak_sonuc = mysql_query($strSQL);
    $yasak_cumle = mysql_fetch_row($yasak_sonuc);


    //  SANSÜR UYGULANIYOR  //

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



    //	ZARARLI KODLAR TEMÝZLENÝYOR	//

	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['mesaj_baslik'] = @ileti_yolla(stripslashes($_POST['mesaj_baslik']),1);
		$_POST['mesaj_icerik'] = @ileti_yolla(stripslashes($_POST['mesaj_icerik']),2);
	}

	//	magic_quotes_gpc kapalýysa	//
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




						//	YAZILAN YENÝ BAÞLIKSA	//



	if ($_POST['kip'] == 'yeni')
	{
		//		ÜST KONU BÝLGÝSÝ		//

		if (isset($_POST['ust_konu']))
		{
			if (($kullanici_kim['yetki'] == 1) OR ($kullanici_kim['yetki'] == 2)) $ust_konu = 1;

			elseif ($kullanici_kim['yetki'] == 3)
			{
				if ($kullanici_kim['grupid'] != '0') $grupek = "grup='$kullanici_kim[grupid]' AND fno='$_POST[fno]' AND yonetme='1' OR";
				else $grupek = "grup='0' AND";

				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE $grupek kulad='$kullanici_kim[kullanici_adi]' AND fno='$_POST[fno]' AND yonetme='1'";
				$kul_izin = mysql_query($strSQL);

				//	YÖNETME YETKÝSÝ VARSA	//
				if (mysql_num_rows($kul_izin)) $ust_konu = 1;
				else $ust_konu = 0;
			}
		}

		else $ust_konu = 0;


		//	ALANLAR BOÞ ÝSE VEYA 53 KARAKTERDEN UZUN ÝSE	//
		
		if (( strlen($_POST['mesaj_baslik']) >  53) OR ( strlen($_POST['mesaj_baslik']) <  3) OR ( strlen($_POST['mesaj_icerik']) <  3))
		{
			header('Location: hata.php?hata=53');
			exit();
		}

		else
		{
			//	YENÝ BAÞLIK VERÝTABANINA GÝRÝLÝYOR	//

			$strSQL = "INSERT INTO $tablo_mesajlar (tarih, mesaj_baslik, mesaj_icerik, yazan, hangi_forumdan, son_mesaj_tarihi,yazan_ip,bbcode_kullan,ust_konu,ifade)";
	
			$strSQL .= "VALUES ('$tarih','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$kullanici_kim[kullanici_adi]','$_POST[fno]','$tarih','$_SERVER[REMOTE_ADDR]','$bbcode_kullan','$ust_konu','$ifade_kullan')";

			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// veritabanýna yapýlan son kaydýn id`si alýnýyor //
			$ymesaj_no = mysql_insert_id();


			//	KULLANICININ MESAJ SAYISI ARTTIRILIYOR VE SON ÝLETÝ TARÝHÝ GÝRÝLÝYOR	//

			$strSQL = "UPDATE $tablo_kullanicilar SET mesaj_sayisi=mesaj_sayisi + 1, son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			//	FORUMUN KONU SAYISI ARTTIRILIYOR //

			$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi=konu_sayisi + 1 WHERE id='$_POST[fno]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			//	BAÞLIK GÖNDERÝLDÝ ÝLETÝSÝ	//

			header('Location: hata.php?bilgi=2&fno='.$_POST['fno'].'&mesaj_no='.$ymesaj_no);
		}
	}





							//	YAZILAN CEVAPSA	//



	elseif ($_POST['kip'] == 'cevapla')
	{
		//	ALANLAR BOÞ ÝSE VEYA 53 KARAKTERDEN UZUN ÝSE	//

		if (( strlen($_POST['mesaj_baslik']) >  53) or ( strlen($_POST['mesaj_icerik']) <  3))
		{
			header('Location: hata.php?hata=53');
			exit();
		}

		else
		{
			//	BAÞLIK GÝRÝLMEMÝÞSE Cvp: EKLE	//

			if ($_POST['mesaj_baslik'] == '')
			$_POST['mesaj_baslik'] = 'Cvp:';


			//	CEVAP VERÝTABANINA GÝRÝLÝYOR	//

			$strSQL = "INSERT INTO $tablo_cevaplar (tarih, cevap_baslik, cevap_icerik, cevap_yazan, hangi_basliktan, hangi_forumdan,yazan_ip,bbcode_kullan,ifade)";

			$strSQL .= "VALUES ('$tarih','$_POST[mesaj_baslik]','$_POST[mesaj_icerik]','$kullanici_kim[kullanici_adi]','$_POST[mesaj_no]','$_POST[fno]','$_SERVER[REMOTE_ADDR]','$bbcode_kullan','$ifade_kullan')";

			$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			$cevapno = mysql_insert_id();


			//	BAÞLIÐIN CEVAP SAYISI ARTTIRILIYOR, SON CEVAP NO, TARÝHÝ VE YAZAN GÝRÝLÝYOR		//

			$strSQL = "UPDATE $tablo_mesajlar SET cevap_sayi=cevap_sayi + 1, son_mesaj_tarihi='$tarih',son_cevap='$cevapno',son_cevap_yazan='$kullanici_kim[kullanici_adi]' WHERE id='$_POST[mesaj_no]' LIMIT 1";

			$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			//	KULLANICININ MESAJ SAYISI ARTTIRILIYOR VE SON ÝLETÝ TARÝHÝ GÝRÝLÝYOR	//

			$strSQL = "UPDATE $tablo_kullanicilar SET mesaj_sayisi=mesaj_sayisi + 1, son_ileti='$tarih' WHERE id='$kullanici_kim[id]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			//	FORUMUN CEVAP SAYISI ARTTIRILIYOR //

			$strSQL = "UPDATE $tablo_forumlar SET cevap_sayisi=cevap_sayisi + 1 WHERE id='$_POST[fno]' LIMIT 1";

			$sonuc1 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			//	CEVAP GÖNDERÝLDÝ ÝLETÝSÝ	//

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