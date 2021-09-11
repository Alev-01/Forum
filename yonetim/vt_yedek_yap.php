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


if (!defined('DOSYA_AYAR')) include '../ayar.php';

@ini_set('magic_quotes_runtime', 0);

if ( (isset($_POST['yedekle'])) AND ($_POST['yedekle'] == 'yedek_al') ):

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}



//  tablo adlarýna önek ekleniyor

$tablo_portal_anketsecenek = $tablo_oneki.'portal_anketsecenek';
$tablo_portal_anketsoru = $tablo_oneki.'portal_anketsoru';
$tablo_portal_anketyorum = $tablo_oneki.'portal_anketyorum';
$tablo_portal_ayarlar = $tablo_oneki.'portal_ayarlar';
$tablo_portal_bloklar = $tablo_oneki.'portal_bloklar';
$tablo_portal_galeri = $tablo_oneki.'portal_galeri';
$tablo_portal_galeridal = $tablo_oneki.'portal_galeridal';
$tablo_portal_haberdal = $tablo_oneki.'portal_haberdal';
$tablo_portal_haberler = $tablo_oneki.'portal_haberler';
$tablo_portal_haberyorum = $tablo_oneki.'portal_haberyorum';
$tablo_portal_indir = $tablo_oneki.'portal_indir';
$tablo_portal_indirkategori = $tablo_oneki.'portal_indirkategori';
$tablo_portal_indiryorum = $tablo_oneki.'portal_indiryorum';
$tablo_portal_sayfa = $tablo_oneki.'portal_sayfa';
$tablo_portal_siteekle = $tablo_oneki.'portal_siteekle';
$tablo_portal_siteekledal = $tablo_oneki.'portal_siteekledal';



//  HANGÝ TABLONUN YEDEKLENECEÐÝNE BAKILIYOR VE DOSYA ADI EKLENÝYOR    // 

if ( (isset($_POST['tablo'][0])) AND ($_POST['tablo'][0] == '0')) { $ytablolar[] = $tablo_oneki.'%'; $dosya_ek = ''; }

elseif ( (isset($_POST['tablo'][1])) AND ($_POST['tablo'][1] == '1')) {
    $ytablolar = array($tablo_ayarlar,$tablo_cevaplar,$tablo_dallar,$tablo_duyurular,$tablo_forumlar,$tablo_kullanicilar,$tablo_mesajlar,$tablo_oturumlar,$tablo_ozel_ileti,$tablo_ozel_izinler,$tablo_yasaklar,$tablo_eklentiler,$tablo_gruplar,$tablo_yuklemeler); $dosya_ek = 'forum_'; }

elseif ( (isset($_POST['tablo'][50])) AND ($_POST['tablo'][50] == '50')) {
    $ytablolar[] = $tablo_oneki.'portal_%'; $dosya_ek = 'portal_'; }

elseif ( (isset($_POST['tablo'])) AND (is_array($_POST['tablo'])) )
{
    foreach ($_POST['tablo'] as $anahtar => $deger)
    {
        switch($deger)
        {
            case '2';   $ytablolar[] = $tablo_ayarlar; $dosya_ek = 'ayarlar_'; break;
            case '3';   $ytablolar[] = $tablo_cevaplar; $dosya_ek = 'cevaplar_'; break;
            case '4';   $ytablolar[] = $tablo_dallar; $dosya_ek = 'dallar_';break;
            case '5';   $ytablolar[] = $tablo_duyurular; $dosya_ek = 'duyurular_'; break;
            case '6';   $ytablolar[] = $tablo_forumlar; $dosya_ek = 'forumlar_'; break;
            case '7';   $ytablolar[] = $tablo_kullanicilar; $dosya_ek = 'kullanicilar_'; break;
            case '8';   $ytablolar[] = $tablo_mesajlar; $dosya_ek = 'mesajlar_'; break;
            case '9';   $ytablolar[] = $tablo_oturumlar; $dosya_ek = 'oturumlar_'; break;
            case '10';  $ytablolar[] = $tablo_ozel_ileti; $dosya_ek = 'ozel_ileti_'; break;
            case '11';  $ytablolar[] = $tablo_ozel_izinler; $dosya_ek = 'ozel_izinler_'; break;
            case '12';  $ytablolar[] = $tablo_yasaklar; $dosya_ek = 'yasaklar_'; break;
            case '13';  $ytablolar[] = $tablo_eklentiler; $dosya_ek = 'eklentiler_'; break;
            case '14';  $ytablolar[] = $tablo_gruplar; $dosya_ek = 'gruplar_'; break;
            case '15';  $ytablolar[] = $tablo_yuklemeler; $dosya_ek = 'yuklemeler_'; break;

            case '51';   $ytablolar[] = $tablo_portal_anketsecenek; $dosya_ek = 'anketsecenek_'; break;
            case '52';   $ytablolar[] = $tablo_portal_anketsoru; $dosya_ek = 'anketsoru_'; break;
            case '53';   $ytablolar[] = $tablo_portal_anketyorum; $dosya_ek = 'anketyorum_'; break;
            case '54';   $ytablolar[] = $tablo_portal_ayarlar; $dosya_ek = 'portal_ayarlar_'; break;
            case '55';   $ytablolar[] = $tablo_portal_blok; $dosya_ek = 'blok_'; break;
            case '56';   $ytablolar[] = $tablo_portal_galeri; $dosya_ek = 'galeri_'; break;
            case '57';   $ytablolar[] = $tablo_portal_galeridal; $dosya_ek = 'galeridal_'; break;
            case '59';   $ytablolar[] = $tablo_portal_haberler; $dosya_ek = 'haberler_'; break;
            case '60';   $ytablolar[] = $tablo_portal_haberyorum; $dosya_ek = 'haberyorum_'; break;
            case '61';   $ytablolar[] = $tablo_portal_indir; $dosya_ek = 'indir_'; break;
            case '62';   $ytablolar[] = $tablo_portal_indirkategori; $dosya_ek = 'indirkategori_'; break;
            case '63';   $ytablolar[] = $tablo_portal_indiryorum; $dosya_ek = 'indiryorum_'; break;
            case '64';   $ytablolar[] = $tablo_portal_sayfa; $dosya_ek = 'sayfa_'; break;
            case '65';   $ytablolar[] = $tablo_portal_siteekle; $dosya_ek = 'siteekle_'; break;
            case '66';   $ytablolar[] = $tablo_portal_siteekledal; $dosya_ek = 'siteekledal_'; break;

            default: $dosya_ek = '';
        }
    }
}

else { $ytablolar[] = $tablo_oneki.'%'; $dosya_ek = ''; }



// geliþmiþ yedekleme adýmlarý  //

$insert_sayisi = 1;

if (!isset($_POST['adim'])) $insert_adim = 0;
else $insert_adim = $_POST['adim'];

if (!isset($_POST['devam'])) $insert_devam = 0;
else $insert_devam = $_POST['devam'];


if ( ($insert_adim == 0) AND ($insert_devam == 0) )
$sorgu_limit = '';

else $sorgu_limit = "LIMIT $insert_devam,$insert_adim";



//	çift týklanma olasýlýðýna karþý 1 saniye bekleniyor
sleep(1);


$sira = 0;

$genel_cikti = "\n";
$genel_cikti .= '--		'.$ayarlar['anasyfbaslik'].' FORUMLARI VERiTABANI YEDEÐi';
$genel_cikti .= "\n";
$genel_cikti .= '--		TARiH: '.zonedate2($ayarlar['tarih_bicimi'], $ayarlar['saat_dilimi'], false, time());
$genel_cikti .= "\n";
$genel_cikti .= '--		SUNUCU ADRESi: http://'.$ayarlar['alanadi'].$ayarlar['f_dizin'];
$genel_cikti .= "\n\n";



//  SEÇÝLÝ TABLOLARIN VERÝLERÝ TEKER TEKER ALINIYOR    //

foreach ($ytablolar as $anahtar => $yedeklenecek_tablo)
{
    //	TABLOLARIN BÝLGÝLERÝ ÇEKÝLÝYOR (sadece önadý uyuþanlar)	//

    $sorgu1 = mysql_query("SHOW TABLE STATUS LIKE '$yedeklenecek_tablo'")
            or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());


    //	HER TABLO SIRAYLA DÖNGÜYE SOKULUYOR	//

    while ($tablolar = mysql_fetch_assoc($sorgu1))
    {
        if ($insert_devam == 0)
        {
            $sorgu2 = mysql_query("SHOW CREATE TABLE `$tablolar[Name]`")
                or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());
            $tablo_olustur = mysql_fetch_row($sorgu2);

            $genel_cikti .= "\n\n\n--		`$tablolar[Name]` TABLOSU VERiLERi\n\n";
            $genel_cikti .= "DROP TABLE IF EXISTS `$tablolar[Name]`;\n\n";
            $genel_cikti .= $tablo_olustur[1];
            $genel_cikti .= ";\n\n";
        }



        //	TABLONUN VERÝLERÝ ÇEKÝLÝYOR	//
        
        $sorgu3 = mysql_query("SELECT * FROM `$tablolar[Name]` $sorgu_limit")
            or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());
        $alan_sayisi = mysql_num_fields($sorgu3);


        //	TABLODAKÝ INT (SAYI) ALANLARI ALINIYOR	//

        $sorgu4 = mysql_query("SHOW FIELDS FROM `$tablolar[Name]`")
        or die ('<h2>Baþarýsýz Sorgu<br></h2>'.mysql_error());

        $i=0;

        while ($alan_tipi = mysql_fetch_assoc($sorgu4))
        {
            $tip_dizi[$i] = $alan_tipi['Type'];
            $i++;
        }


        //	tablo satýr satýr taranýyor	//
        while ($tablo_verileri = mysql_fetch_row($sorgu3))
        {
            $genel_cikti .= "INSERT INTO `$tablolar[Name]` VALUES (";

            //	alanlardaki veriler VALUES içine yazdýrýlýyor
            for ($i=0; $i < $alan_sayisi; $i++)
            {
                //	alan int (sayý) alaný ise týrnak iþareti arasýna koyma
                if (!preg_match('/int/i', $tip_dizi[$i]))
                $veri = '\''.addslashes($tablo_verileri[$i]).'\'';

                else
                {
                    //	int verisi NULL ise NULL yaz
                    if (is_null($tablo_verileri[$i])) $veri = 'NULL';
                    else $veri = addslashes($tablo_verileri[$i]);
                }

                //	satýr atlama kodunu \r\n yap
                $genel_cikti .= str_replace("\r\n",'\r\n',$veri);
                
                //	her alan arasýna virgül koy
                if ($i < ($alan_sayisi-1)) $genel_cikti .= ', ';
            }
            //	VALUES parantezini kapat
            $genel_cikti .= ");\n\n";
        }
    }
}
mysql_close($link);



//*****************		GZÝP BAÞI	********************//

if ( (isset($_POST['gzip'])) AND ($_POST['gzip'] == 1) )
{
	if(!@extension_loaded('zlib'))
	{
		header('Location: ../hata.php?hata=155');
		exit();
	}

	//	dönemsel kalan kontrolü fonsiyonu	//

	function gzip_PrintFourChars($Val)
	{
		$return = '';
		for ($i = 0; $i < 4; $i ++)
		{
			$return .= chr($Val % 256);
			$Val = floor($Val / 256);
		}
		return $return;
	} 

	//	$genel_cikti deðiþkeni $contents deðiþkenine aktarýlýyor	//
	$contents = $genel_cikti;

	ob_start();
	ob_implicit_flush(0);

	//	çýktý yazdýrýlýyor	//
	echo $genel_cikti;

	//	boyut bilgisi	//
	$Size = strlen($contents);

	//	dönemsel kalan kontrolü bilgisi	//
	$Crc = crc32($contents);
	$contents = ob_get_contents();
	$contents = gzcompress($contents, 9);

	//	ekran temizleniyor	//
	ob_end_clean();

	//	DOSYANIN ÝSMÝ BELÝRLENÝYOR	//
	header('Content-Type: application/x-gzip; name="phpkf_vt_yedek_'.$dosya_ek.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, time()).'.sql.gz"');
	header('Content-disposition: attachment; filename=phpkf_vt_yedek_'.$dosya_ek.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, time()).'.sql.gz');

	//	gzip baþlýk çýktýsý	//
	echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";

	$contents = substr($contents, 0, strlen($contents) - 4);

	//	yazdýrýlýyor //
	echo $contents;

	//	crc ve boyut bilgileri	//
	echo gzip_PrintFourChars($Crc);
	echo gzip_PrintFourChars($Size);
}


//***************	NORMAL DOSYA ***************//

else
{
	//	DOSYANIN ÝSMÝ BELÝRLENÝYOR	//
	header('Content-Type:text/html; charset=UTF-8');
	header('Content-Type: text/x-delimtext; name="phpkf_vt_yedek_'.$dosya_ek.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, time()).'.sql"');
	header('Content-disposition: attachment; filename=phpkf_vt_yedek_'.$dosya_ek.zonedate2('d-m-Y', $ayarlar['saat_dilimi'], false, time()).'.sql');

	//	çýktý yazdýrýlýyor	//
	echo $genel_cikti;
}
endif;

?>