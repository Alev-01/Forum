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


if (!defined('PHPKF_ICINDEN')) exit();
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// TEL�F SATIRINI S�LMEY�N, DE���T�RMEY�N, K���LTMEY�N, OKUNAMAZ HALE GET�RMEY�N, �ST�N� KAPATMAYIN //
// TEL�F SATIRINI S�LMEY�N, DE���T�RMEY�N, K���LTMEY�N, OKUNAMAZ HALE GET�RMEY�N, �ST�N� KAPATMAYIN //
// TEL�F SATIRINI S�LMEY�N, DE���T�RMEY�N, K���LTMEY�N, OKUNAMAZ HALE GET�RMEY�N, �ST�N� KAPATMAYIN //
// ------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------ //
//         YUKARIDAK� TEL�F MADDELER�N� D�KKATL�CE OKUYUN,
//         KABUL ETMED���N�Z HERHANG� B�R MADDE VARSA PHPKF`Y� KULLANMAYIN.
//         TEL�F MADDELER�NE UYMADI�I TESP�T ED�LENLER HAKKINDA YASAL ��LEM BA�LATILACAKTIR.








$dosya = './../temalar/'.$ayarlar['temadizini'].'/tema_bilgi.txt';
$yonetim = '';

if ( (isset($kullanici_kim['yetki'])) AND ($kullanici_kim['yetki'] == 1) ){
if ($ayarlar['portal_kullan'] == 1) $yonetim .= '<a href="index.php">- <u>Forum Y�netim</u> -</a> | <a href="../portal/index.php">- <u>Portal Y�netim</u> -</a><br><br>';
else $yonetim .= '<a href="index.php">- <u>Y�netim Masas�</u> -</a><br><br>';}

if (!($dosya_ac = fopen($dosya,'r'))) echo '<p><font color="#ff0000"><b>'.$dosya.' tema bilgi dosyas� bulunam�yor!</b></font></p>';

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

if (isset($tema_duzenleme[1][0])) $tema_bilgisi = '<font style="font-family: Tahoma, helvetica; font-size: 10px" color="#000000"><b>Tema:</b> &nbsp; '.$tema_adi.' &nbsp; | &nbsp; <a target="_blank" href="http://'.$tema_baglanti.'">'.$tema_yapimci.'</a> &nbsp; &nbsp; - &nbsp; &nbsp; <b>D�zenleme:</b> &nbsp; '.@zkTemizle($tema_duzenleme[1][0]).'<br>';

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