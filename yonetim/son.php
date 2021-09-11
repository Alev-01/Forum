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


if (!defined('PHPKF_ICINDEN')) exit();
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// TELÝF SATIRINI SÝLMEYÝN, DEÐÝÞTÝRMEYÝN, KÜÇÜLTMEYÝN, OKUNAMAZ HALE GETÝRMEYÝN, ÜSTÜNÜ KAPATMAYIN //
// TELÝF SATIRINI SÝLMEYÝN, DEÐÝÞTÝRMEYÝN, KÜÇÜLTMEYÝN, OKUNAMAZ HALE GETÝRMEYÝN, ÜSTÜNÜ KAPATMAYIN //
// TELÝF SATIRINI SÝLMEYÝN, DEÐÝÞTÝRMEYÝN, KÜÇÜLTMEYÝN, OKUNAMAZ HALE GETÝRMEYÝN, ÜSTÜNÜ KAPATMAYIN //
// ------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------ //
//         YUKARIDAKÝ TELÝF MADDELERÝNÝ DÝKKATLÝCE OKUYUN,
//         KABUL ETMEDÝÐÝNÝZ HERHANGÝ BÝR MADDE VARSA PHPKF`YÝ KULLANMAYIN.
//         TELÝF MADDELERÝNE UYMADIÐI TESPÝT EDÝLENLER HAKKINDA YASAL ÝÞLEM BAÞLATILACAKTIR.








$dosya = './../temalar/'.$ayarlar['temadizini'].'/tema_bilgi.txt';
$yonetim = '';

if ( (isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1) ){
if ($ayarlar['portal_kullan'] == 1) $yonetim .= '<a href="index.php">- <u>Forum Yönetim</u> -</a> | <a href="../portal/index.php">- <u>Portal Yönetim</u> -</a><br><br>';
else $yonetim .= '<a href="index.php">- <u>Yönetim Masasý</u> -</a><br><br>';}

if (!($dosya_ac = fopen($dosya,'r'))) echo '<p><font color="#ff0000"><b>'.$dosya.' tema bilgi dosyasý bulunamýyor!</b></font></p>';

$boyut = filesize($dosya);
$dosya_metni = fread($dosya_ac,$boyut);
fclose($dosya_ac);

preg_match('|<TEMA_ADI>(.*?)</TEMA_ADI>|si', $dosya_metni, $tema_adi, PREG_OFFSET_CAPTURE);
preg_match('|<YAPIMCI>(.*?)</YAPIMCI>|si', $dosya_metni, $tema_yapimci, PREG_OFFSET_CAPTURE);
preg_match('|<BAGLANTI>(.*?)</BAGLANTI>|si', $dosya_metni, $tema_baglanti, PREG_OFFSET_CAPTURE);
preg_match('|<DUZENLEME>(.*?)</DUZENLEME>|si', $dosya_metni, $tema_duzenleme, PREG_OFFSET_CAPTURE);

$tema_adi = zkTemizle($tema_adi[1][0]);
$tema_yapimci = zkTemizle($tema_yapimci[1][0]);
$tema_baglanti = zkTemizle($tema_baglanti[1][0]);

if (isset($tema_duzenleme[1][0])) $tema_bilgisi = '<font style="font-family: Tahoma, helvetica; font-size: 10px" color="#000000"><b>Tema:</b> &nbsp; '.$tema_adi.' &nbsp; | &nbsp; <a target="_blank" href="http://'.$tema_baglanti.'">'.$tema_yapimci.'</a> &nbsp; &nbsp; - &nbsp; &nbsp; <b>Düzenleme:</b> &nbsp; '.@zkTemizle($tema_duzenleme[1][0]).'<br>';

else{
if (($ayarlar['temadizini']=='5renkli')OR($ayarlar['temadizini']=='kar_cicegi')OR($ayarlar['temadizini']=='kara_elmas')OR($ayarlar['temadizini']=='kfp-tema')OR($ayarlar['temadizini']=='tekrenkli')OR($ayarlar['temadizini']=='v_tema'))
$tema_bilgisi = '';
else $tema_bilgisi = '<font style="font-family: Tahoma, helvetica; font-size: 10px" color="#000000"><b>Tema:</b> &nbsp; '.$tema_adi.' &nbsp; | &nbsp; <a target="_blank" href="http://'.$tema_baglanti.'" style="text-decoration:none; color:#000000">'.$tema_yapimci.'</a><br></font>';}

$yonetim_masasi = $tema_bilgisi.'<font style="font-family: Tahoma, helvetica; font-size: 11px" color="#000000"><br>'.$ayarlar['anasyfbaslik'].'</font><br>';

$ornek2 = new phpkf_tema();
$sayfason = '../temalar/'.$ayarlar['temadizini'].'/yonetim/son.html';
$dongusuz = array('{TELIF_BILGI2}' => $telif_bilgi);
eval($enst);
?>