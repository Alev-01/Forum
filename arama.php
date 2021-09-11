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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';


// arama sonuç renklendirme
function SonucRenklendir($metin, $sozcuk)
{
    $donen = @str_ireplace($sozcuk,'<b style="background: #ffff00; color: #000000;">'.$sozcuk.'</b>',$metin);
    return $donen;
}


// arama sonuç satýr atlama ve ifade ekleme
function SonucDuzenle($metin)
{
    $metin = @str_replace("\n", '<br>', substr($metin, 0, 500)).'....';
    return $metin;
}




$arama_kota = 20;

if (empty($_GET['sayfa'])) $_GET['sayfa'] = 0;
else $_GET['sayfa'] = @zkTemizle($_GET['sayfa']);

if ( (empty($_GET['sozcuk_aynen'])) AND (empty($_GET['sozcuk_hepsi'])) AND (empty($_GET['sozcuk_herhangi'])) AND (isset($_GET['yazar_ara'])) AND ((strlen($_GET['yazar_ara']) >=  4)) ) {$_GET['sozcuk_aynen'] = '%%%';}
elseif (empty($_GET['sozcuk_aynen'])) $_GET['sozcuk_aynen'] = '%';
else
{
	$_GET['sozcuk_aynen'] = @zkTemizle($_GET['sozcuk_aynen']);
	$_GET['sozcuk_aynen'] = @str_replace('*','%',$_GET['sozcuk_aynen']);
}

if (empty($_GET['sozcuk_hepsi'])) $_GET['sozcuk_hepsi'] = '%';
else
{
	$_GET['sozcuk_hepsi'] = @zkTemizle($_GET['sozcuk_hepsi']);
	$_GET['sozcuk_hepsi'] = @str_replace('*','%',$_GET['sozcuk_hepsi']);
}

if (empty($_GET['sozcuk_herhangi'])) $_GET['sozcuk_herhangi'] = '%';
else
{
	$_GET['sozcuk_herhangi'] = @zkTemizle($_GET['sozcuk_herhangi']);
	$_GET['sozcuk_herhangi'] = @str_replace('*','%',$_GET['sozcuk_herhangi']);
}

if (empty($_GET['sozcuk_haric'])) $_GET['sozcuk_haric'] = '%';
else
{
	$_GET['sozcuk_haric'] = @zkTemizle($_GET['sozcuk_haric']);
	$_GET['sozcuk_haric'] = @str_replace('*','%',$_GET['sozcuk_haric']);
}

if (empty($_GET['forum'])) $_GET['forum'] = 1;
else $_GET['forum'] = @zkTemizle($_GET['forum']);

if (empty($_GET['yazar_ara'])) $_GET['yazar_ara'] = '%';

if ((isset($_GET['yazar_ara'])) AND (strlen($_GET['yazar_ara']) >=  4))
{
	$_GET['yazar_ara'] = @zkTemizle($_GET['yazar_ara']);
	$_GET['yazar_ara'] = @str_replace('*','%',$_GET['yazar_ara']);
	$myazan_ara =  " $tablo_mesajlar.yazan LIKE '$_GET[yazar_ara]' AND ";
	$cyazan_ara =  " $tablo_cevaplar.cevap_yazan LIKE '$_GET[yazar_ara]' AND ";
}
else
{
	$myazan_ara = '';
	$cyazan_ara = '';
}









//	ARAMA YAPILMIÞSA ÇALIÞTIRILACAK KODLAR - BAÞI	//

if ( !empty($_GET['b']) ):




//	ÝKÝ ÝLETÝ ARASI SÜRESÝ DOLMAMIÞSA UYARILIYOR	//	
//	oturum açlýyor, arama zamanýna bakýlýyor  //

@session_start();
$tarih = time();

if ( ($_GET['sayfa'] <= 0) AND (isset($_GET['a'])) )
{
    if ( isset($_SESSION['arama_tarih']) AND ($_SESSION['arama_tarih'] > ($tarih - 20)) )
    {
		header('Location: hata.php?hata=1');
		exit();
    }
}




//  SEÇÝLÝ TARÝH HESAPLANIYOR   //

if ( (isset($_GET['tarih'])) AND ($_GET['tarih'] != '') )
{
    switch($_GET['tarih'])
    {
        case 'tum_zamanlar';
        $msecili_tarih = '';
        $csecili_tarih = '';
        break;

        case '1gun';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 86400);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 86400);
        break;

        case '3gun';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 259200);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 259200);
        break;

        case '1hafta';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 604800);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 604800);
        break;

        case '2hafta';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 1296000);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 1296000);
        break;

        case '1ay';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 2592000);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 2592000);
        break;

        case '3ay';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 7776000);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 7776000);
        break;

        case '6ay';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 15552000);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 15552000);
        break;

        case '1sene';
        $msecili_tarih = "AND $tablo_mesajlar.tarih > ".($tarih - 31536000);
        $csecili_tarih = "AND $tablo_cevaplar.tarih > ".($tarih - 31536000);
        break;

        default:
        $msecili_tarih = '';
        $csecili_tarih = '';
    }
}


else
{
    $msecili_tarih = '';
    $csecili_tarih = '';
}










//		HANGÝ ALANDA KAÇ KELÝME OLDUÐUNA BAKILARAK...	//
//		...	SORGUSUNUN WHERE KISMI HAZIRLANIYOR					//



if ($_GET['forum'] == 'tum')
{
	$mhangi = " $tablo_mesajlar.silinmis='0' $msecili_tarih AND ";
	$changi = " $tablo_cevaplar.silinmis='0' $csecili_tarih AND ";
}

elseif ($_GET['forum'][0] == 'f')
{
	$fno = substr($_GET['forum'],1);

	$mhangi = "$tablo_mesajlar.silinmis='0' AND $tablo_mesajlar.hangi_forumdan='$fno' $msecili_tarih AND ";
	$changi = "$tablo_cevaplar.silinmis='0' AND $tablo_cevaplar.hangi_forumdan='$fno' $csecili_tarih AND ";
}

else
{
	$mhangi = "$tablo_mesajlar.silinmis='0' AND $tablo_mesajlar.hangi_forumdan=satir[id] $msecili_tarih AND ";
	$changi = "$tablo_cevaplar.silinmis='0' AND $tablo_cevaplar.hangi_forumdan=satir[id] $csecili_tarih AND ";
}


if ((isset($_GET['sozcuk_haric'])) AND (strlen($_GET['sozcuk_haric']) >=  3))
{
	$harama_dizisi = explode(' ', $_GET['sozcuk_haric']);
	$ad_boyut = count($harama_dizisi);

	if ($ad_boyut == 1)
	{
		$haric_mesaj_baslik = "AND $tablo_mesajlar.mesaj_baslik NOT LIKE '%$_GET[sozcuk_haric]%' ";
		$haric_mesaj_icerik = "AND $tablo_mesajlar.mesaj_icerik NOT LIKE '%$_GET[sozcuk_haric]%' ";

		$haric_cevap_baslik = "AND $tablo_cevaplar.cevap_baslik NOT LIKE '%$_GET[sozcuk_haric]%' ";
		$haric_cevap_icerik = "AND $tablo_cevaplar.cevap_icerik NOT LIKE '%$_GET[sozcuk_haric]%' ";
	}

	else
	{
		$haric_mesaj_baslik = '';
		$haric_mesaj_icerik = '';

		$haric_cevap_baslik = '';
		$haric_cevap_icerik = '';

		for ($i=1,$d=0; $d < $ad_boyut; $i++,$d++)
		{
			if ($harama_dizisi[$d] != '')
			{
				$haric_mesaj_baslik .= "AND $tablo_mesajlar.mesaj_baslik NOT LIKE '%$harama_dizisi[$d]%' ";
				$haric_mesaj_icerik .= "AND $tablo_mesajlar.mesaj_icerik NOT LIKE '%$harama_dizisi[$d]%' ";

				$haric_cevap_baslik .= "AND $tablo_cevaplar.cevap_baslik NOT LIKE '%$harama_dizisi[$d]%' ";
				$haric_cevap_icerik .= "AND $tablo_cevaplar.cevap_icerik NOT LIKE '%$harama_dizisi[$d]%' ";
			}
		}
	}
}

else
{
	$haric_mesaj_baslik = '';
	$haric_mesaj_icerik = '';
	$haric_cevap_baslik = '';
	$haric_cevap_icerik = '';
}


if ((isset($_GET['sozcuk_hepsi'])) AND (strlen($_GET['sozcuk_hepsi']) >=  3))
{
	$arama_dizisi_bosluk = explode(' ', $_GET['sozcuk_hepsi']);
	$ad_boyut_bosluk = count($arama_dizisi_bosluk);


    //	BOÞ DÝZÝLER ATILIYOR	//
	
	for ($d=0,$a=0; $d < $ad_boyut_bosluk; $d++)
	{
		if ($arama_dizisi_bosluk[$d] != '')
		{
			$arama_dizisi[$a] = $arama_dizisi_bosluk[$d];
			$a++;
		}
	}

	$ad_boyut = count($arama_dizisi);

	if ($ad_boyut == 1)
	{
		$hepsi_mesaj = "$mhangi $myazan_ara $tablo_mesajlar.mesaj_baslik LIKE '%$_GET[sozcuk_hepsi]%' $haric_mesaj_baslik OR ";
		$hepsi_mesaj .= "$mhangi $myazan_ara $tablo_mesajlar.mesaj_icerik LIKE '%$_GET[sozcuk_hepsi]%' $haric_mesaj_icerik ";

		$hepsi_cevap = "$changi $cyazan_ara $tablo_cevaplar.cevap_baslik LIKE '%$_GET[sozcuk_hepsi]%' $haric_cevap_baslik OR ";
		$hepsi_cevap .= "$changi $cyazan_ara $tablo_cevaplar.cevap_icerik LIKE '%$_GET[sozcuk_hepsi]%' $haric_cevap_icerik ";
	}

	else
	{
		for ($i=1,$d=0; $d < $ad_boyut; $i++,$d++)
		{
			if (empty($hepsi_mesaj))
			{
				$hepsi_mesaj = $mhangi.$myazan_ara;
				$hepsi_cevap = $changi.$cyazan_ara;
			}

			if (($d + 1) == $ad_boyut)
			{
				$hepsi_mesaj .= "$tablo_mesajlar.mesaj_icerik LIKE '%$arama_dizisi[$d]%' $haric_mesaj_icerik ";
				$hepsi_cevap .= "$tablo_cevaplar.cevap_icerik LIKE '%$arama_dizisi[$d]%' $haric_cevap_icerik ";
				break;
			}

			else
			{
				$hepsi_mesaj .= "$tablo_mesajlar.mesaj_icerik LIKE '%$arama_dizisi[$d]%' AND ";
				$hepsi_cevap .= "$tablo_cevaplar.cevap_icerik LIKE '%$arama_dizisi[$d]%' AND ";
			}
			
		}
	}
}

if ((isset($_GET['sozcuk_aynen'])) AND (strlen($_GET['sozcuk_aynen']) >=  3))
{
	$aynen_mesaj = "$mhangi $myazan_ara $tablo_mesajlar.mesaj_baslik LIKE '%$_GET[sozcuk_aynen]%' $haric_mesaj_baslik OR ";
	$aynen_mesaj .= "$mhangi $myazan_ara $tablo_mesajlar.mesaj_icerik LIKE '%$_GET[sozcuk_aynen]%' $haric_mesaj_icerik ";

	$aynen_cevap = "$changi $cyazan_ara $tablo_cevaplar.cevap_baslik LIKE '%$_GET[sozcuk_aynen]%' $haric_cevap_baslik OR ";
	$aynen_cevap .= "$changi $cyazan_ara $tablo_cevaplar.cevap_icerik LIKE '%$_GET[sozcuk_aynen]%' $haric_cevap_icerik ";
}

if ((isset($_GET['sozcuk_herhangi'])) AND (strlen($_GET['sozcuk_herhangi']) >=  3))
{
	$arama_dizisi_bosluk = explode(' ', $_GET['sozcuk_herhangi']);
	$ad_boyut_bosluk = count($arama_dizisi_bosluk);


    //	BOÞ DÝZÝLER ATILIYOR	//

	for ($d=0,$a=0; $d < $ad_boyut_bosluk; $d++)
	{
		if ($arama_dizisi_bosluk[$d] != '')
		{
			$arama_dizisi2[$a] = $arama_dizisi_bosluk[$d];
			$a++;
		}
	}

	$ad_boyut2 = count($arama_dizisi2);
	if ($ad_boyut2 == 1)
	{
		$herhangi_mesaj = "$mhangi $myazan_ara $tablo_mesajlar.mesaj_baslik LIKE '%$_GET[sozcuk_herhangi]%' $haric_mesaj_baslik OR ";
		$herhangi_mesaj .= "$mhangi $myazan_ara $tablo_mesajlar.mesaj_icerik LIKE '%$_GET[sozcuk_herhangi]%' $haric_mesaj_icerik ";

		$herhangi_cevap = "$changi $cyazan_ara $tablo_cevaplar.cevap_baslik LIKE '%$_GET[sozcuk_herhangi]%' $haric_cevap_baslik OR ";
		$herhangi_cevap .= "$changi $cyazan_ara $tablo_cevaplar.cevap_icerik LIKE '%$_GET[sozcuk_herhangi]%' $haric_cevap_icerik ";
	}

	else
	{
		for ($i=1,$d=0; $d < $ad_boyut2; $i++,$d++)
		{
			if (empty($herhangi_mesaj))
			{
				$herhangi_mesaj = "$mhangi $myazan_ara $tablo_mesajlar.mesaj_baslik LIKE '%$arama_dizisi2[$d]%' $haric_mesaj_baslik OR ";
				$herhangi_mesaj .= "$mhangi $myazan_ara $tablo_mesajlar.mesaj_icerik LIKE '%$arama_dizisi2[$d]%' $haric_mesaj_icerik ";
			}

			if (empty($herhangi_cevap))
			{
				$herhangi_cevap = "$changi $cyazan_ara $tablo_cevaplar.cevap_baslik LIKE '%$arama_dizisi2[$d]%' $haric_cevap_baslik OR ";
				$herhangi_cevap .= "$changi $cyazan_ara $tablo_cevaplar.cevap_icerik LIKE '%$arama_dizisi2[$d]%' $haric_cevap_icerik ";
			}

			else
			{
				$herhangi_mesaj .= "OR $mhangi $myazan_ara $tablo_mesajlar.mesaj_baslik LIKE '%$arama_dizisi2[$d]%' $haric_mesaj_baslik OR ";
				$herhangi_mesaj .= "$mhangi $myazan_ara $tablo_mesajlar.mesaj_icerik LIKE '%$arama_dizisi2[$d]%' $haric_mesaj_icerik ";

				$herhangi_cevap .= "OR $changi $cyazan_ara $tablo_cevaplar.cevap_baslik LIKE '%$arama_dizisi2[$d]%' $haric_cevap_baslik OR ";
				$herhangi_cevap .= "$changi $cyazan_ara $tablo_cevaplar.cevap_icerik LIKE '%$arama_dizisi2[$d]%' $haric_cevap_icerik ";
			}
		}
	}
}



//		HANGÝ ALANLARIN DOLU OLDUÐUNA GÖRE...		//
//		... WHERE SORGUSU HAZIRLANIYOR 				//



if ( (strlen($_GET['sozcuk_hepsi']) >=  3) AND (strlen($_GET['sozcuk_aynen']) >=  3) AND (strlen($_GET['sozcuk_herhangi']) >=  3) )
{
	$aranan_mesaj_tumu = $hepsi_mesaj.' AND '.$aynen_mesaj.' AND '.$herhangi_mesaj;
	$aranan_cevap_tumu = $hepsi_cevap.' AND '.$aynen_cevap.' AND '.$herhangi_cevap;
}

if ( (strlen($_GET['sozcuk_hepsi']) >=  3) AND (strlen($_GET['sozcuk_aynen']) >=  3) AND (strlen($_GET['sozcuk_herhangi']) <  3) )
{
	$aranan_mesaj_tumu = $hepsi_mesaj.' AND '.$aynen_mesaj;
	$aranan_cevap_tumu = $hepsi_cevap.' AND '.$aynen_cevap;
}

if ( (strlen($_GET['sozcuk_hepsi']) >=  3) AND (strlen($_GET['sozcuk_herhangi']) >=  3) AND (strlen($_GET['sozcuk_aynen']) <  3) )
{
	$aranan_mesaj_tumu = $hepsi_mesaj.' AND '.$herhangi_mesaj;
	$aranan_cevap_tumu = $hepsi_cevap.' AND '.$herhangi_cevap;
}

if ( (strlen($_GET['sozcuk_aynen']) >=  3) AND (strlen($_GET['sozcuk_herhangi']) >=  3) AND (strlen($_GET['sozcuk_hepsi']) <  3) )
{
	$aranan_mesaj_tumu = $aynen_mesaj.' AND '.$herhangi_mesaj;
	$aranan_cevap_tumu = $aynen_cevap.' AND '.$herhangi_cevap;
}

if ( (strlen($_GET['sozcuk_aynen']) >=  3) AND (strlen($_GET['sozcuk_herhangi']) <  3) AND (strlen($_GET['sozcuk_hepsi']) <  3) )
{
	$aranan_mesaj_tumu = $aynen_mesaj;
	$aranan_cevap_tumu = $aynen_cevap;
}

if ( (strlen($_GET['sozcuk_aynen']) <  3) AND (strlen($_GET['sozcuk_herhangi']) >=  3) AND (strlen($_GET['sozcuk_hepsi']) <  3) )
{
	$aranan_mesaj_tumu = $herhangi_mesaj;
	$aranan_cevap_tumu = $herhangi_cevap;
}

if ( (strlen($_GET['sozcuk_aynen']) <  3) AND (strlen($_GET['sozcuk_herhangi']) <  3) AND (strlen($_GET['sozcuk_hepsi']) >=  3) )
{
	$aranan_mesaj_tumu = $hepsi_mesaj;
	$aranan_cevap_tumu = $hepsi_cevap;
}



// 		TÜM ALANLAR BOÞ BIRAKILMIÞSA KULLANICI UYARILIYOR	//



if ( (empty($aranan_mesaj_tumu)) AND (empty($myazan_ara)) )
{
	header('Location: hata.php?hata=2');
	exit();
}















//		TÜM FORUMLARDA ARAMA YAPIYORSA		//


if ($_GET['forum'] == 'tum')
{
	//	TÜM FORUMLAR - SORGU SONUCUNDAKÝ TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $aranan_mesaj_tumu 
	GROUP BY $tablo_mesajlar.id


	UNION SELECT $tablo_mesajlar.id, $tablo_cevaplar.id AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $aranan_cevap_tumu 
	GROUP BY $tablo_mesajlar.id") or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	
	$satir_sayi = mysql_num_rows($result);


    //	TÜM FORUMLAR - ARAMA YAPILIYOR VE SONUC BÝLGÝLERÝ ÇEKÝLÝYOR	//


	if ($satir_sayi > 0)
	{
		$strSQL = "SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_mesajlar.yazan AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $aranan_mesaj_tumu

		GROUP BY $tablo_mesajlar.id


		UNION SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_cevaplar.id AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $aranan_cevap_tumu

		GROUP BY $tablo_mesajlar.id

		ORDER BY id DESC LIMIT $_GET[sayfa],$arama_kota";

		$m_arama_sonuc = mysql_query($strSQL) or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	}
}





//		FORUM DALINDA ARAMA YAPIYORSA		//


elseif ($_GET['forum'][0] == 'd')
{
	$dno = substr($_GET['forum'],1);

    // FORUM DALINA BAÐLI FORUMLAR BULUNUYOR	//

	$strSQL = "SELECT id FROM $tablo_forumlar WHERE dal_no='$dno'";
	$sonuc = mysql_query($strSQL);
	while($satir = mysql_fetch_array($sonuc))
	{
		if (empty($m_where_bilgi))
		{
			$yaranan_mesaj_tumu = str_replace('satir[id]',"$satir[id]",$aranan_mesaj_tumu);
			$m_where_bilgi = "$yaranan_mesaj_tumu";

			$yaranan_cevap_tumu = str_replace('satir[id]',"$satir[id]",$aranan_cevap_tumu);
			$c_where_bilgi = "$yaranan_cevap_tumu";
		}
		else
		{
			$yaranan_mesaj_tumu = str_replace('satir[id]',"$satir[id]",$aranan_mesaj_tumu);
			$m_where_bilgi .= " OR $yaranan_mesaj_tumu";

			$yaranan_cevap_tumu = str_replace('satir[id]',"$satir[id]",$aranan_cevap_tumu);
			$c_where_bilgi .= " OR $yaranan_cevap_tumu";
		}
	}

    //	FORUM DALI - SORGUDAN DÖNEN TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan AS rakam
	
	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $m_where_bilgi
	GROUP BY $tablo_mesajlar.id


	UNION SELECT $tablo_mesajlar.id, $tablo_cevaplar.id AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $c_where_bilgi

	GROUP BY $tablo_mesajlar.id") or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	$satir_sayi = mysql_num_rows($result);


    //	FORUM DALI - ARAMA YAPILIYOR VE SONUC BÝLGÝLERÝ ÇEKÝLÝYOR	//


	if ($satir_sayi > 0)
	{
		$strSQL = "SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_mesajlar.yazan AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $m_where_bilgi
		GROUP BY $tablo_mesajlar.id


		UNION SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_cevaplar.id AS rakam
		
		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $c_where_bilgi

		GROUP BY $tablo_mesajlar.id
		ORDER BY id DESC LIMIT $_GET[sayfa],$arama_kota";
		$m_arama_sonuc = mysql_query($strSQL) or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	}
}





//		ÜST FORUMDA ARAMA YAPIYORSA		//


elseif ($_GET['forum'][0] == 'u')
{
	$uno = substr($_GET['forum'],1);

    // ALT FORUMLAR BULUNUYOR	//

	$strSQL = "SELECT id FROM $tablo_forumlar WHERE id='$uno' OR alt_forum='$uno'";
	$sonuc = mysql_query($strSQL);
	while($satir = mysql_fetch_array($sonuc))
	{
		if (empty($m_where_bilgi))
		{
			$yaranan_mesaj_tumu = str_replace('satir[id]',"$satir[id]",$aranan_mesaj_tumu);
			$m_where_bilgi = "$yaranan_mesaj_tumu";

			$yaranan_cevap_tumu = str_replace('satir[id]',"$satir[id]",$aranan_cevap_tumu);
			$c_where_bilgi = "$yaranan_cevap_tumu";
		}
		else
		{
			$yaranan_mesaj_tumu = str_replace('satir[id]',"$satir[id]",$aranan_mesaj_tumu);
			$m_where_bilgi .= " OR $yaranan_mesaj_tumu";

			$yaranan_cevap_tumu = str_replace('satir[id]',"$satir[id]",$aranan_cevap_tumu);
			$c_where_bilgi .= " OR $yaranan_cevap_tumu";
		}
	}

    //	ÜST FORUM - SORGUDAN DÖNEN TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan AS rakam
	
	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $m_where_bilgi
	GROUP BY $tablo_mesajlar.id


	UNION SELECT $tablo_mesajlar.id, $tablo_cevaplar.id AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $c_where_bilgi

	GROUP BY $tablo_mesajlar.id") or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	$satir_sayi = mysql_num_rows($result);


    //	ÜST FORUM - ARAMA YAPILIYOR VE SONUC BÝLGÝLERÝ ÇEKÝLÝYOR	//


	if ($satir_sayi > 0)
	{
		$strSQL = "SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_mesajlar.yazan AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $m_where_bilgi
		GROUP BY $tablo_mesajlar.id


		UNION SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_cevaplar.id AS rakam
		
		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan
		
		WHERE $c_where_bilgi

		GROUP BY $tablo_mesajlar.id
		ORDER BY id DESC LIMIT $_GET[sayfa],$arama_kota";
		$m_arama_sonuc = mysql_query($strSQL) or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	}
}





//		ALT FORUMDA ARAMA YAPILIYORSA		//


elseif ($_GET['forum'][0] == 'f')
{
    //	ALT FORUM - SORGU SONUCUNDAKÝ TOPLAM SONUÇ SAYISI ALINIYOR	//

	$result = mysql_query("SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $aranan_mesaj_tumu
	GROUP BY $tablo_mesajlar.id


	UNION SELECT $tablo_mesajlar.id, $tablo_cevaplar.id AS rakam

	FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
	ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

	WHERE $aranan_cevap_tumu
	GROUP BY $tablo_mesajlar.id") or die('<h2>ARAMA SONUÇLANAMADI</h2>');

	$satir_sayi = mysql_num_rows($result);


    //	ALT FORUM - ARAMA YAPILIYOR VE SONUC BÝLGÝLERÝ ÇEKÝLÝYOR	//


	if ($satir_sayi > 0)
	{
		$strSQL = "SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_mesajlar.yazan AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $aranan_mesaj_tumu
		GROUP BY $tablo_mesajlar.id


		UNION SELECT $tablo_mesajlar.id, $tablo_mesajlar.yazan, $tablo_mesajlar.hangi_forumdan, $tablo_mesajlar.tarih, $tablo_mesajlar.cevap_sayi, $tablo_mesajlar.goruntuleme, $tablo_cevaplar.cevap_yazan, $tablo_cevaplar.hangi_forumdan, $tablo_mesajlar.mesaj_baslik, $tablo_mesajlar.mesaj_icerik, $tablo_cevaplar.cevap_baslik, $tablo_cevaplar.cevap_icerik, $tablo_cevaplar.hangi_basliktan, $tablo_cevaplar.tarih cevap_tarih, $tablo_cevaplar.id AS cevap_id, $tablo_cevaplar.id AS rakam

		FROM $tablo_mesajlar LEFT OUTER JOIN $tablo_cevaplar
		ON $tablo_mesajlar.id = $tablo_cevaplar.hangi_basliktan

		WHERE $aranan_cevap_tumu
		GROUP BY $tablo_mesajlar.id

		ORDER BY id DESC LIMIT $_GET[sayfa],$arama_kota";

		$m_arama_sonuc = mysql_query($strSQL) or die('<h2>ARAMA SONUÇLANAMADI</h2>');
	}
}

if (empty($satir_sayi)) $satir_sayi = 0;



			//	ARAMA SONUÇ VERÝRSE SON ARAMA ZAMANI OTURUMA GÝRÝLÝYOR	//

if ( $satir_sayi > 0 ) $_SESSION['arama_tarih'] = $tarih;

	$toplam_sayfa = ($satir_sayi / $arama_kota);
	settype($toplam_sayfa,'integer');

	if (($satir_sayi % $arama_kota) != 0) $toplam_sayfa++;



endif;

//	ARAMA YAPILMIÞSA ÇALIÞTIRILACAK KODLAR - SONU	//









//	FORM SELECT ÝÇÝN ANA FORUMLARIN BÝLGÝLERÝ ÇEKÝLÝYOR	//

if (empty($satir_sayi)) $satir_sayi = 0;

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die('<h2>ARAMA SONUÇLANAMADI</h2>');

$sayfano = 10;
$sayfa_adi = 'Konu ve Ýçerik Arama';
include 'baslik.php';



		//		ARAMA AÇILIÞI SAYFASI - BAÞI 	//


if ($satir_sayi <= 0):


if ( isset($_GET['a']) )
	$bulunamadi = '<p align="center"><b> &nbsp; &nbsp; &nbsp; Aradýðýnýz koþula uyan hiçbir sonuç bulunamadý.</b><p>';

else $bulunamadi = '';




$bul = array('%', '"');
$cevir = array('*', '&#34;');


if ($_GET['sozcuk_herhangi'] != '%')
	$sozcuk_herhangi = @str_replace($bul,$cevir,$_GET['sozcuk_herhangi']);

else $sozcuk_herhangi = '';


if ($_GET['sozcuk_hepsi'] != '%')
	$sozcuk_hepsi = @str_replace($bul,$cevir,$_GET['sozcuk_hepsi']);

else $sozcuk_hepsi = '';


if ( ($_GET['sozcuk_aynen'] != '%')  AND ($_GET['sozcuk_aynen'] != '%%%') )
	$sozcuk_aynen = @str_replace($bul,$cevir,$_GET['sozcuk_aynen']);

else $sozcuk_aynen = '';


if ($_GET['sozcuk_haric'] != '%')
	$sozcuk_haric = @str_replace($bul,$cevir,$_GET['sozcuk_haric']);

else $sozcuk_haric = '';


if ($_GET['yazar_ara'] != '%') 
	$yazar_ara = @str_replace($bul,$cevir,$_GET['yazar_ara']);

else $yazar_ara = '';




$arama_secenek = '';


// forum dalý adlarý çekiliyor

while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$arama_secenek .= '
	<option value="d'.$dallar_satir['id'].'">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar
				WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar
					WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
			$arama_secenek .= '
			<option value="f'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];


		else
		{
			$arama_secenek .= '
			<option value="u'.$forum_satir['id'].'"> &nbsp; - '.$forum_satir['forum_baslik'];

			while ($alt_forum_satir = mysql_fetch_array($sonuca))
				$arama_secenek .= '
				<option value="f'.$alt_forum_satir['id'].'"> &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
		}
	}
}



//	veriler tema motoruna yollanýyor	//

$dongusuz = array('{BULUNAMADI}' => $bulunamadi,
'{SOZCUK_HEPSI}' => $sozcuk_hepsi,
'{SOZCUK_HERHANGI}' => $sozcuk_herhangi,
'{SOZCUK_AYNEN}' => $sozcuk_aynen,
'{SOZCUK_HARIC}' => $sozcuk_haric,
'{YAZAR_ARA}' => $yazar_ara,
'{ARAMA_SECENEK}' => $arama_secenek);


//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/arama.html');

$ornek1->kosul('1', $dongusuz, true);
$ornek1->kosul('2', array('' => ''), false);

endif;







		//		ARAMA AÇILIÞI SAYFASI - SONU 	//


		//	ARAMA SONUÇLARI SIRALANIYOR BAÞLANGIÇ	//




if ($satir_sayi > 0): 


// FORUMLARIN BÝLGÝLERÝ ÇEKÝLÝYOR	//

$strSQL = "SELECT id,forum_baslik,okuma_izni FROM $tablo_forumlar ORDER BY dal_no, sira";
$sonuc = mysql_query($strSQL);

while ($forum_satir = mysql_fetch_array($sonuc))
{
    $tumforum_satir[$forum_satir['id']] = $forum_satir['forum_baslik'];
    $tumforum_izin[$forum_satir['id']] = $forum_satir['okuma_izni'];
}


$sayi_arttir = ($_GET['sayfa'] + 1);



// SONUÇLAR SIRALANIYOR

while ($m_arama_satir = mysql_fetch_array($m_arama_sonuc)):


//  BULUNAN CEVAP ÝSE   //

if (is_numeric($m_arama_satir['rakam']) == true)
{
    // cevabýn kaçýncý sýrada olduðu hesaplanýyor
    $result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE silinmis='0' AND hangi_basliktan='$m_arama_satir[hangi_basliktan]' AND id < $m_arama_satir[cevap_id]") or die('<h2>ARAMA SONUÇLANAMADI</h2>');
    $cavabin_sirasi = mysql_num_rows($result);

    $sayfaya_git = ($cavabin_sirasi / $ayarlar['ksyfkota']);
    settype($sayfaya_git,'integer');
    $sayfaya_git = ($sayfaya_git * $ayarlar['ksyfkota']);

    if ($sayfaya_git != 0) $sayfaya_git = '&amp;ks='.$sayfaya_git;
    else $sayfaya_git = '';


    $konu_baslik = $m_arama_satir['mesaj_baslik'].'&nbsp; &raquo; &nbsp;'.$m_arama_satir['cevap_baslik'];

    $sonuc_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $m_arama_satir['cevap_tarih']);

    $yazan = '<a href="profil.php?kim='.$m_arama_satir['cevap_yazan'].'">'.$m_arama_satir['cevap_yazan'].'</a>';

    $mesaj_icerik = SonucDuzenle($m_arama_satir['cevap_icerik']);
}


//  BULUNAN KONU ÝSE    //

else
{
    $konu_baslik = $m_arama_satir['mesaj_baslik'];

    $sonuc_tarih = zonedate($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, $m_arama_satir['tarih']);

    $yazan = '<a href="profil.php?kim='.$m_arama_satir['yazan'].'">'.$m_arama_satir['yazan'].'</a>';

    $mesaj_icerik = SonucDuzenle($m_arama_satir['mesaj_icerik']);
}



//  SONUÇ ÝÇERÝÐÝ VE BAÞLIÐI RENKLENDÝRÝLÝYOR VE KISALTILIYOR  //

if ($tumforum_izin[$m_arama_satir['2']] == 0)
{
    if ((isset($_GET['sozcuk_hepsi'])) AND (strlen($_GET['sozcuk_hepsi']) >=  3))
    {
        if ($ad_boyut == 1)
        {
            $konu_baslik = SonucRenklendir($konu_baslik, $_GET['sozcuk_hepsi']);
            $mesaj_icerik = SonucRenklendir($mesaj_icerik, $_GET['sozcuk_hepsi']);
        }

        elseif ($ad_boyut > 1)
        {
            for ($i=1,$d=0; $d < $ad_boyut; $i++,$d++)
            {
                $konu_baslik = SonucRenklendir($konu_baslik, $arama_dizisi[$d]);
                $mesaj_icerik = SonucRenklendir($mesaj_icerik, $arama_dizisi[$d]);
            }
        }
    }


    if ((isset($_GET['sozcuk_herhangi'])) AND (strlen($_GET['sozcuk_herhangi']) >=  3))
    {
        if ($ad_boyut2 == 1)
        {
            $konu_baslik = SonucRenklendir($konu_baslik, $_GET['sozcuk_herhangi']);
            $mesaj_icerik = SonucRenklendir($mesaj_icerik, $_GET['sozcuk_herhangi']);
        }

        elseif ($ad_boyut2 > 1)
        {
            for ($i=1,$d=0; $d < $ad_boyut2; $i++,$d++)
            {
                $konu_baslik = SonucRenklendir($konu_baslik, $arama_dizisi2[$d]);
                $mesaj_icerik = SonucRenklendir($mesaj_icerik, $arama_dizisi2[$d]);
            }
        }
    }


    if ((isset($_GET['sozcuk_aynen'])) AND (strlen($_GET['sozcuk_aynen']) >=  3))
    {
        $konu_baslik = SonucRenklendir($konu_baslik, $_GET['sozcuk_aynen']);
        $mesaj_icerik = SonucRenklendir($mesaj_icerik, $_GET['sozcuk_aynen']);
    }
}

else $mesaj_icerik = '<u><i>Yetkilendirilmiþ Forum. Bu içeriði okumak için izniniz olmayabilir.</i></u>';


// forum baþlýðý oluþturuluyor

$forum_baslik = '<a href="forum.php?f='.$m_arama_satir['2'].'">'.$tumforum_satir[$m_arama_satir['2']].'</a>';


// konu baþlýðý oluþturuluyor

if (is_numeric($m_arama_satir['rakam']) == true)
    $konu_baslik_bag = '<a href="konu.php?k='.$m_arama_satir['hangi_basliktan'].$sayfaya_git.'#c'.$m_arama_satir['cevap_id'].'">'.$konu_baslik.'</a>';

else $konu_baslik_bag = '<a href="konu.php?k='.$m_arama_satir['id'].'">'.$konu_baslik.'</a>';




//	veriler tema motoruna yollanýyor	//

$tekli1[] = array('{SONUC_SAYISI}' => ($sayi_arttir++),
'{KONU_BASLIK}' => $konu_baslik_bag,
'{FORUM_BASLIK}' => $forum_baslik,
'{YAZAN}' => $yazan,
'{CEVAP_SAYI}' => NumaraBicim($m_arama_satir['cevap_sayi']),
'{GOSTERIM}' => NumaraBicim($m_arama_satir['goruntuleme']),
'{TARIH}' => $sonuc_tarih,
'{MESAJ_ICERIK}' => $mesaj_icerik);

endwhile;




		//	ARAMA SONUÇLARI SIRALANIYOR BÝTÝÞ	//






//		SAYFALAR BAÞLANGIÇ		//

$sayfalama = '';

if ($satir_sayi > $arama_kota):
$sayfalama = '<p>
<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tr>
	<td class="forum_baslik">
Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
	</td>';

if ($_GET['sayfa'] != 0)
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
	$sayfalama .= '&nbsp;<a href="arama.php?b=1&amp;sayfa=0&amp;sozcuk_hepsi='.$_GET['sozcuk_hepsi'].'&amp;yazar_ara='.$_GET['yazar_ara'].'&amp;forum='.$_GET['forum'].'&amp;sozcuk_aynen='.$_GET['sozcuk_aynen'].'&amp;sozcuk_herhangi='.$_GET['sozcuk_herhangi'].'&amp;sozcuk_haric='.$_GET['sozcuk_haric'].'
">&laquo;ilk</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="önceki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="arama.php?b=1&amp;sayfa='.($_GET['sayfa'] - $arama_kota).'&amp;sozcuk_hepsi='.$_GET['sozcuk_hepsi'].'&amp;yazar_ara='.$_GET['yazar_ara'].'&amp;forum='.$_GET['forum'].'&amp;sozcuk_aynen='.$_GET['sozcuk_aynen'].'&amp;sozcuk_herhangi='.$_GET['sozcuk_herhangi'].'&amp;sozcuk_haric='.$_GET['sozcuk_haric'].'
">&lt;</a>&nbsp;</td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['sayfa']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['sayfa'] / $arama_kota) - 3));
	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['sayfa'] + 8)) break;
		if (($sayi == 0) and ($_GET['sayfa'] == 0))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="Þu an bulunduðunuz sayfa">';
			$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
		}

		elseif (($sayi + 1) == (($_GET['sayfa'] / $arama_kota) + 1))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="Þu an bulunduðunuz sayfa">';
			$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}

		else
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaralý sayfaya git">';
			$sayfalama .= '&nbsp;<a href="arama.php?b=1&amp;sayfa='.($sayi * $arama_kota).'&amp;sozcuk_hepsi='.$_GET['sozcuk_hepsi'].'&amp;yazar_ara='.$_GET['yazar_ara'].'&amp;forum='.$_GET['forum'].'&amp;sozcuk_aynen='.$_GET['sozcuk_aynen'].'&amp;sozcuk_herhangi='.$_GET['sozcuk_herhangi'].'&amp;sozcuk_haric='.$_GET['sozcuk_haric'].'
">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}

if ($_GET['sayfa'] < ($satir_sayi - $arama_kota))
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="arama.php?b=1&amp;sayfa='.($_GET['sayfa'] + $arama_kota).'&amp;sozcuk_hepsi='.$_GET['sozcuk_hepsi'].'&amp;yazar_ara='.$_GET['yazar_ara'].'&amp;forum='.$_GET['forum'].'&amp;sozcuk_aynen='.$_GET['sozcuk_aynen'].'&amp;sozcuk_herhangi='.$_GET['sozcuk_herhangi'].'&amp;sozcuk_haric='.$_GET['sozcuk_haric'].'
">&gt;</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	$sayfalama .= '&nbsp;<a href="arama.php?b=1&amp;sayfa='.(($toplam_sayfa - 1) * $arama_kota).'&amp;sozcuk_hepsi='.$_GET['sozcuk_hepsi'].'&amp;yazar_ara='.$_GET['yazar_ara'].'&amp;forum='.$_GET['forum'].'&amp;sozcuk_aynen='.$_GET['sozcuk_aynen'].'&amp;sozcuk_herhangi='.$_GET['sozcuk_herhangi'].'&amp;sozcuk_haric='.$_GET['sozcuk_haric'].'
">son&raquo;</a>&nbsp;</td>';

}

$sayfalama .= '</tr>
</table>';


endif;

//		SAYFALAR BÝTÝÞ		//







//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/arama.html');


$ornek1->kosul('1', array('' => ''), false);
$ornek1->kosul('2', array('' => ''), true);

$ornek1->tekli_dongu('1',$tekli1);

$ornek1->dongusuz(array('{TOPLAM_SONUC}' => $satir_sayi,
						'{SAYFALAMA}' => $sayfalama));


endif;
if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>