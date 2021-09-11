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

//  AYAR DOSYASINI �ND�R TIKLANDI�INDA �ALI�ACAK KISIM  //

if ( (isset($_POST['kurulum_yapildi'])) AND (isset($_POST['ayar_bilgi'])) AND ($_POST['kurulum_yapildi'] == 'tamam') )
{
	header('Content-Type: text/html; charset=UTF-8');
	header('Content-Type: text/x-delimtext; name="ayar.php"');
	header('Content-disposition: attachment; filename=ayar.php');

	//	magic_quotes_gpc a��ksa	//
	if (get_magic_quotes_gpc(1))
	echo stripslashes($_POST['ayar_bilgi']);

	//	magic_quotes_gpc kapal�ysa	//
	else echo $_POST['ayar_bilgi'];
	exit();
}



//  HATA TABLOSU    //

$hata_tablo1 = '<br><br><br><table border="0" cellspacing="1" cellpadding="7" width="530" bgcolor="#999999" align="center">
<tr><td bgcolor="#eeeeee" align="center"><font color="#ff0000"><b>';

$hata_tablo2 = '</b></font></td></tr>
<tr><td bgcolor="#fafafa">
<table border="0" cellspacing="1" cellpadding="7" width="100%" bgcolor="#999999" align="center"><tr><td bgcolor="#eeeeee" align="left"><br>';

$hata_tablo3 = '<br><br></td></tr></table>';



//  FORM B�LG�LER� KONTROL ED�L�YOR - BA�I  //

if ( (empty($_POST['kurulum'])) OR ($_POST['kurulum'] == '') )  exit();

if ( (empty($_POST['kurulum'])) OR ($_POST['kurulum'] != 'form_dolu') OR (empty($_POST['forum_alanadi'])) OR (empty($_POST['forum_dizin'])) OR (empty($_POST['forum_posta'])) OR (empty($_POST['vt_sunucu'])) OR (empty($_POST['vt_adi'])) OR (empty($_POST['tablo_onek'])) OR (empty($_POST['yonetici_adi'])) OR (empty($_POST['gercek_ad'])) OR (empty($_POST['yonetici_sifre1'])) OR (empty($_POST['yonetici_sifre2'])) OR (empty($_POST['eposta'])) )
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Veritaban� kullan�c� ad� ve �ifresi hari� t�m alanlar�n doldurulmas� zorunludur !'.$hata_tablo3;
	exit();
}

if (!isset($_POST['telif_kabul']))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Telif maddelerinin hepsini okuyup kabul etmeden "php Kolay Forum"u kuramaz<br>ve kullanamazs�n�z !'.$hata_tablo3;
	exit();
}

if (!preg_match('/^[a-zA-Z]\w{0,10}+$/', $_POST['tablo_onek']))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Veritaban� tablo �neki sadece harf ile ba�lamal� ve 10 karakterden uzun olmamal�d�r.'.$hata_tablo3;
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_������������.]+$/', $_POST['yonetici_adi']))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Y�netici kullan�c� ad�nda ge�ersiz karakterler var ! <br><br>Latin ve T�rk�e harf, rakam, alt �izgi( _ ), tire ( - ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri ve bo�luk karakterini i�eremez.'.$hata_tablo3;
	exit();
}

if (( strlen($_POST['yonetici_adi']) >  20) or ( strlen($_POST['yonetici_adi']) <  4))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Y�netici kullan�c� ad� en az 4, en �ok 20 karakter olmal�d�r !'.$hata_tablo3;
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_ ������������.]+$/', $_POST['gercek_ad']))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'"Y�netici Ad Soyad - L�kap" alan�nda ge�ersiz karakterler var. <br><br>Latin ve T�rk�e harf, rakam, bo�luk, alt �izgi( _ ), tire ( - ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri i�eremez.'.$hata_tablo3;
	exit();
}

if ( ( strlen($_POST['gercek_ad']) >  30) or ( strlen($_POST['gercek_ad']) <  4) )
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Ad Soyad alan� 4 karakterden k�sa, 30 karakterden uzun olamaz !'.$hata_tablo3;
	exit();
}

if ($_POST['yonetici_sifre1'] != $_POST['yonetici_sifre2'])
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Yazd���n�z y�netici �ifreleri uyu�muyor !'.$hata_tablo3;
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_POST['yonetici_sifre1']))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Y�netici �ifresinde ge�ersiz karakterler var ! <br><br>Latin harf, rakam, alt �izgi( _ ), tire ( - ), and ( & ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri, T�rk�e karakterleri ve bo�luk karakterini i�eremez.'.$hata_tablo3;
	exit();
}

if (( strlen($_POST['yonetici_sifre1']) >  20) or ( strlen($_POST['yonetici_sifre1']) <  5))
{
	echo $hata_tablo1.'Hatal� Bilgi'.$hata_tablo2.'Y�netici �ifresi en az 5, en �ok 20 karakter olmal�d�r !'.$hata_tablo3;
	exit();
}


//  FORM B�LG�LER� KONTROL ED�L�YOR - SONU  //




$tarih = time();
@ini_set('magic_quotes_runtime', 0);

//	tablo adlar�na �nek ekleniyor
$tablo_ayarlar = $_POST['tablo_onek'].'ayarlar';
$tablo_cevaplar = $_POST['tablo_onek'].'cevaplar';
$tablo_dallar = $_POST['tablo_onek'].'dallar';
$tablo_forumlar = $_POST['tablo_onek'].'forumlar';
$tablo_eklentiler = $_POST['tablo_onek'].'eklentiler';
$tablo_gruplar = $_POST['tablo_onek'].'gruplar';
$tablo_yuklemeler = $_POST['tablo_onek'].'yuklemeler';
$tablo_kullanicilar = $_POST['tablo_onek'].'kullanicilar';
$tablo_mesajlar = $_POST['tablo_onek'].'mesajlar';
$tablo_oturumlar = $_POST['tablo_onek'].'oturumlar';
$tablo_ozel_ileti = $_POST['tablo_onek'].'ozel_ileti';
$tablo_ozel_izinler = $_POST['tablo_onek'].'ozel_izinler';
$tablo_yasaklar = $_POST['tablo_onek'].'yasaklar';
$tablo_duyurular = $_POST['tablo_onek'].'duyurular';

//	�ifrelerinin kar��t�r�laca�� anahtar �retiliyor
$anahtar = md5(microtime());

//  �ifre anahtar ile kar��t�r�larak sha1 ile �ifreleniyor
$sifre =  sha1($anahtar.$_POST['yonetici_sifre1']);




            //      VER�TABANI B�LG�LER�        //



$vtkaydi = "



--		`ayarlar` TABLOSU VERiLERi

CREATE TABLE `$tablo_ayarlar` (
  `etiket` varchar(30) NOT NULL,
  `deger` varchar(100) default NULL,
  PRIMARY KEY  (`etiket`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_ayarlar` VALUES ('title', 'PHP KOLAY FORUM');

INSERT INTO `$tablo_ayarlar` VALUES ('anasyfbaslik', 'PHP KOLAY FORUM (phpKF)');

INSERT INTO `$tablo_ayarlar` VALUES ('syfbaslik', 'php Kolay Forum');

INSERT INTO `$tablo_ayarlar` VALUES ('fsyfkota', '20');

INSERT INTO `$tablo_ayarlar` VALUES ('ksyfkota', '8');

INSERT INTO `$tablo_ayarlar` VALUES ('k_cerez_zaman', '604800');

INSERT INTO `$tablo_ayarlar` VALUES ('ileti_sure', '20');

INSERT INTO `$tablo_ayarlar` VALUES ('gelen_kutu_kota', '20');

INSERT INTO `$tablo_ayarlar` VALUES ('ulasan_kutu_kota', '20');

INSERT INTO `$tablo_ayarlar` VALUES ('kaydedilen_kutu_kota', '20');

INSERT INTO `$tablo_ayarlar` VALUES ('bbcode', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('o_ileti', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('tarih_bicimi', 'd.m.Y- H:i');

INSERT INTO `$tablo_ayarlar` VALUES ('y_posta', '$_POST[forum_posta]');

INSERT INTO `$tablo_ayarlar` VALUES ('alanadi', '$_POST[forum_alanadi]');

INSERT INTO `$tablo_ayarlar` VALUES ('f_dizin', '$_POST[forum_dizin]');

INSERT INTO `$tablo_ayarlar` VALUES ('eposta_yontem', 'mail');

INSERT INTO `$tablo_ayarlar` VALUES ('smtp_kd', 'true');

INSERT INTO `$tablo_ayarlar` VALUES ('smtp_sunucu', '');

INSERT INTO `$tablo_ayarlar` VALUES ('smtp_kullanici', '');

INSERT INTO `$tablo_ayarlar` VALUES ('smtp_sifre', '');

INSERT INTO `$tablo_ayarlar` VALUES ('saat_dilimi', '2');

INSERT INTO `$tablo_ayarlar` VALUES ('kilit_sure', '1800');

INSERT INTO `$tablo_ayarlar` VALUES ('imza_uzunluk', '255');

INSERT INTO `$tablo_ayarlar` VALUES ('uzak_resim', '0');

INSERT INTO `$tablo_ayarlar` VALUES ('resim_yukle', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('resim_boyut', '35840');

INSERT INTO `$tablo_ayarlar` VALUES ('resim_genislik', '130');

INSERT INTO `$tablo_ayarlar` VALUES ('resim_yukseklik', '130');

INSERT INTO `$tablo_ayarlar` VALUES ('resim_galerisi', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('kayit_cevabi', 'Ankara');

INSERT INTO `$tablo_ayarlar` VALUES ('kayit_soru', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('kayit_sorusu', 'T�rkiye`nin ba�kenti neresidir?');

INSERT INTO `$tablo_ayarlar` VALUES ('hesap_etkin', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('forum_rengi', 'siyah');

INSERT INTO `$tablo_ayarlar` VALUES ('seo', '0');

INSERT INTO `$tablo_ayarlar` VALUES ('kurucu', 'Site Kurucusu');

INSERT INTO `$tablo_ayarlar` VALUES ('yonetici', 'Forum Y�neticisi');

INSERT INTO `$tablo_ayarlar` VALUES ('yardimci', 'Forum Yard�mc�s�');

INSERT INTO `$tablo_ayarlar` VALUES ('kullanici', 'Kay�tl� Kullan�c�');

INSERT INTO `$tablo_ayarlar` VALUES ('surum', '1.90');

INSERT INTO `$tablo_ayarlar` VALUES ('sonkonular','1');

INSERT INTO `$tablo_ayarlar` VALUES ('kacsonkonu','10');

INSERT INTO `$tablo_ayarlar` VALUES ('temadizini','5renkli');

INSERT INTO `$tablo_ayarlar` VALUES ('tema_secenek','5renkli,');

INSERT INTO `$tablo_ayarlar` VALUES ('cevrimici', '600');

INSERT INTO `$tablo_ayarlar` VALUES ('forum_durumu', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('portal_kullan', '0');

INSERT INTO `$tablo_ayarlar` VALUES ('onay_kodu', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('blm_yrd', 'B�l�m Yard�mc�s�');

INSERT INTO `$tablo_ayarlar` VALUES ('kul_resim', 'dosyalar/resimler/galeri/resim_yok.png');

INSERT INTO `$tablo_ayarlar` VALUES ('boyutlandirma', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('duyuru_tarihi', '0');

INSERT INTO `$tablo_ayarlar` VALUES ('bolum_kisi', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('konu_kisi', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('uye_kayit', '1');

INSERT INTO `$tablo_ayarlar` VALUES ('oi_uyari', '1');




--		`cevaplar` TABLOSU VERiLERi

CREATE TABLE `$tablo_cevaplar` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tarih` int(11) unsigned NOT NULL,
  `cevap_baslik` varchar(60) NOT NULL,
  `cevap_icerik` text NOT NULL,
  `cevap_yazan` varchar(20) NOT NULL,
  `hangi_basliktan` mediumint(8) unsigned NOT NULL,
  `degistirme_tarihi` int(11) unsigned default NULL,
  `degistirme_sayisi` smallint(5) unsigned NOT NULL default '0',
  `degistiren` varchar(20) default NULL,
  `hangi_forumdan` smallint(5) unsigned NOT NULL,
  `yazan_ip` varchar(15) default NULL,
  `degistiren_ip` varchar(15) default NULL,
  `bbcode_kullan` tinyint(1) NOT NULL default '0',
  `silinmis` tinyint(1) NOT NULL default '0',
  `ifade` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `cevap_yazan` (`cevap_yazan`),
  KEY `hangi_basliktan` (`hangi_basliktan`),
  KEY `hangi_forumdan` (`hangi_forumdan`),
  KEY `tarih` (`tarih`)
) ENGINE=MyISAM CHARSET=utf8;




--		`dallar` TABLOSU VERiLERi

CREATE TABLE `$tablo_dallar` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `ana_forum_baslik` text NOT NULL,
  `sira` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `sira` (`sira`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_dallar` VALUES (1, 'Deneme Forum Dal� 1', 1);




--		`forumlar` TABLOSU VERiLERi

CREATE TABLE `$tablo_forumlar` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `dal_no` smallint(5) unsigned NOT NULL,
  `forum_baslik` text NOT NULL,
  `forum_bilgi` text NOT NULL,
  `sira` tinyint(3) unsigned NOT NULL default '1',
  `okuma_izni` tinyint(1) NOT NULL default '0',
  `yazma_izni` tinyint(1) NOT NULL default '0',
  `resim` varchar(100) default NULL,
  `konu_acma_izni` tinyint(1) NOT NULL default '0',
  `konu_sayisi` mediumint(8) unsigned default '0',
  `cevap_sayisi` int(10) unsigned default '0',
  `alt_forum` smallint(5) unsigned default '0',
  `gizle` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `dal_no` (`dal_no`),
  KEY `sira` (`sira`),
  KEY `alt_forum` (`alt_forum`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_forumlar` VALUES (1, 1, 'Deneme Forumu 1', 'Bu forum deneme ama�l� a��lm��t�r.', 1, 0, 0, '', 0, 1, 0, 0, 0);

INSERT INTO `$tablo_forumlar` VALUES (2, 1, 'Deneme Alt Forumu', 'Bu alt forum deneme ama�l� a��lm��t�r.', 1, 0, 0, '', 0, 0, 0, 1, 0);




--		`eklentiler` TABLOSU VERiLERi

CREATE TABLE `$tablo_eklentiler` (
  `ad` varchar(40) NOT NULL,
  `kur` tinyint(1) unsigned NOT NULL,
  `etkin` tinyint(1) unsigned NOT NULL,
  `vt` tinyint(1) unsigned NOT NULL,
  `dosya` tinyint(1) unsigned NOT NULL,
  `dizin` tinyint(1) unsigned NOT NULL,
  `sistem` tinyint(1) unsigned NOT NULL,
  `usurum` varchar(5) NOT NULL,
  `esurum` varchar(5) NOT NULL,
  PRIMARY KEY (`ad`)
) ENGINE=MyISAM CHARSET=utf8;




--		`gruplar` TABLOSU VERiLERi

CREATE TABLE `$tablo_gruplar` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `grup_adi` varchar(30) NOT NULL,
  `sira` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `gizle` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `yetki` tinyint(1) NOT NULL DEFAULT '-1',
  `ozel_ad` varchar(30) DEFAULT NULL,
  `uyeler` text,
  `grup_bilgi` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grup_adi` (`grup_adi`)
) ENGINE=MyISAM CHARSET=utf8;




--		`yuklemeler` TABLOSU VERiLERi

CREATE TABLE `$tablo_yuklemeler` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`tarih` int(11) NOT NULL DEFAULT '0',
`boyut` int(7) unsigned DEFAULT '0',
`ip` varchar(15) DEFAULT NULL,
`uye_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
`uye_adi` varchar(20) NOT NULL DEFAULT '',
`dosya` varchar(30) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8;




--		`kullanicilar` TABLOSU VERiLERi

CREATE TABLE `$tablo_kullanicilar` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `kullanici_kimlik` varchar(40) default NULL,
  `kullanici_adi` varchar(20) NOT NULL,
  `sifre` varchar(40) NOT NULL,
  `posta` varchar(100) NOT NULL,
  `web` varchar(100) default NULL,
  `gercek_ad` varchar(100) NOT NULL,
  `dogum_tarihi` varchar(10) NOT NULL,
  `katilim_tarihi` int(11) unsigned NOT NULL,
  `sehir` varchar(30) NOT NULL,
  `mesaj_sayisi` mediumint(8) unsigned NOT NULL default '0',
  `yonetim_kimlik` varchar(40) default NULL,
  `resim` varchar(100) default NULL,
  `imza` text,
  `posta_goster` tinyint(1) NOT NULL default '1',
  `dogum_tarihi_goster` tinyint(1) NOT NULL default '1',
  `sehir_goster` tinyint(1) NOT NULL default '1',
  `okunmamis_oi` smallint(3) unsigned NOT NULL default '0',
  `son_ileti` int(11) unsigned NOT NULL default '0',
  `kul_etkin` tinyint(1) NOT NULL default '0',
  `kul_etkin_kod` varchar(10) NOT NULL default '0',
  `engelle` tinyint(1) NOT NULL default '0',
  `yeni_sifre` mediumint(7) unsigned NOT NULL default '0',
  `yetki` tinyint(1) NOT NULL default '0',
  `kilit_tarihi` int(11) unsigned NOT NULL default '0',
  `giris_denemesi` tinyint(1) unsigned NOT NULL default '0',
  `son_giris` int(11) unsigned NOT NULL default '0',
  `son_hareket` int(11) unsigned NOT NULL default '0',
  `hangi_sayfada` varchar(120) default NULL,
  `kul_ip` varchar(15) default NULL,
  `gizli` tinyint(1) NOT NULL default '0',
  `icq` varchar(30) default NULL,
  `msn` varchar(100) default NULL,
  `yahoo` varchar(100) default NULL,
  `aim` varchar(100) default NULL,
  `skype` varchar(100) default NULL,
  `temadizini` varchar(25) default NULL,
  `temadizinip` varchar(25) default NULL,
  `ozel_ad` varchar(30) default NULL,
  `posta2` varchar(100) default NULL,
  `sayfano` varchar(25) DEFAULT '0',
  `grupid` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `kullanici_adi` (`kullanici_adi`),
  KEY `posta` (`posta`),
  KEY `katilim_tarihi` (`katilim_tarihi`),
  KEY `kul_etkin` (`kul_etkin`),
  KEY `engelle` (`engelle`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_kullanicilar` VALUES (1, NULL, '$_POST[yonetici_adi]', '$sifre', '$_POST[eposta]', '', '$_POST[gercek_ad]', '01-01-1981', $tarih, 'Ankara', 1, NULL, 'dosyalar/resimler/galeri/phpkf_k.png', '', 1, 1, 1, 0, 0, 1, '0', 0, 0, 1, 0, 0, $tarih-1, $tarih-1, 'Kullan�c� ��k�� yapt�', '', 0, '', '', '', '', '','', '', '', '','','');




--		`mesajlar` TABLOSU VERiLERi

CREATE TABLE `$tablo_mesajlar` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `tarih` int(11) unsigned NOT NULL,
  `mesaj_baslik` varchar(60) NOT NULL,
  `mesaj_icerik` text NOT NULL,
  `yazan` varchar(20) NOT NULL,
  `degistirme_tarihi` int(11) unsigned default NULL,
  `hangi_forumdan` smallint(5) unsigned NOT NULL,
  `goruntuleme` mediumint(8) unsigned NOT NULL default '0',
  `cevap_sayi` smallint(5) unsigned NOT NULL default '0',
  `son_mesaj_tarihi` int(11) unsigned default NULL,
  `degistirme_sayisi` smallint(5) unsigned NOT NULL default '0',
  `degistiren` varchar(20) default NULL,
  `yazan_ip` varchar(15) default NULL,
  `degistiren_ip` varchar(15) default NULL,
  `bbcode_kullan` tinyint(1) NOT NULL default '0',
  `ust_konu` tinyint(1) NOT NULL default '0',
  `kilitli` tinyint(1) NOT NULL default '0',
  `silinmis` tinyint(1) NOT NULL default '0',
  `ifade` tinyint(1) NOT NULL default '1',
  `son_cevap` int(10) unsigned NOT NULL DEFAULT '0',
  `son_cevap_yazan` varchar(20) NULL,
  PRIMARY KEY  (`id`),
  KEY `tarih` (`tarih`),
  KEY `yazan` (`yazan`),
  KEY `hangi_forumdan` (`hangi_forumdan`),
  KEY `cevap_sayi` (`cevap_sayi`),
  KEY `son_mesaj_tarihi` (`son_mesaj_tarihi`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_mesajlar` VALUES (1, $tarih, 'php Kolay Forum`a ho�geldiniz ! ', '[quote=\"phpKF\"][b]\r\n      php Kolay Forum kurulumunuz ba�ar�yla tamamlanm��t�r...\r\n\r\n[/b][/quote]\r\n\r\nY�netici olarak giri� yapt���n�zda alt tarafta g�r�nen  [url=yonetim/index.php]- Y�netim Masas� -[/url]   ba�lant�s�n� t�klayarak, y�netimle ilgili her �eye ula�abilirsiniz.', '$_POST[yonetici_adi]', NULL, 1, 0, 0, $tarih, 0, NULL, NULL, '', 1, 0, 0, 0, 0, 0, NULL);




--		`oturumlar` TABLOSU VERiLERi

CREATE TABLE `$tablo_oturumlar` (
  `sid` varchar(32) NOT NULL,
  `giris` int(11) unsigned NOT NULL,
  `son_hareket` int(11) unsigned NOT NULL,
  `hangi_sayfada` varchar(120) NOT NULL,
  `kul_ip` varchar(15) NOT NULL,
  `sayfano` varchar(25) DEFAULT '0',
  KEY `sid` (`sid`)
) ENGINE=MyISAM CHARSET=utf8;




--		`ozel_ileti` TABLOSU VERiLERi

CREATE TABLE `$tablo_ozel_ileti` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `kimden` varchar(20) NOT NULL,
  `kime` varchar(20) NOT NULL,
  `ozel_baslik` varchar(60) NOT NULL,
  `ozel_icerik` text NOT NULL,
  `gonderme_tarihi` int(11) unsigned NOT NULL,
  `okunma_tarihi` int(11) unsigned default NULL,
  `gonderen_kutu` tinyint(1) NOT NULL default '0',
  `alan_kutu` tinyint(1) NOT NULL default '0',
  `bbcode_kullan` tinyint(1) NOT NULL default '0',
  `ifade` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `kimden` (`kimden`),
  KEY `kime` (`kime`),
  KEY `gonderme_tarihi` (`gonderme_tarihi`)
) ENGINE=MyISAM CHARSET=utf8;




--		`ozel_izinler` TABLOSU VERiLERi

CREATE TABLE `$tablo_ozel_izinler` (
  `kulad` varchar(30) NOT NULL,
  `kulid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `grup` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fno` smallint(5) unsigned NOT NULL,
  `okuma` tinyint(1) NOT NULL default '0',
  `yazma` tinyint(1) NOT NULL default '0',
  `yonetme` tinyint(1) NOT NULL default '0',
  `konu_acma` tinyint(1) NOT NULL default '0',
  KEY `kulid` (`kulid`),
  KEY `kulad` (`kulad`),
  KEY `fno` (`fno`),
  KEY `grup` (`grup`)
) ENGINE=MyISAM CHARSET=utf8;




--		`yasaklar` TABLOSU VERiLERi

CREATE TABLE `$tablo_yasaklar` (
  `etiket` varchar(30) NOT NULL,
  `deger` text,
  `tip` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`etiket`)
) ENGINE=MyISAM CHARSET=utf8;

INSERT INTO `$tablo_yasaklar` VALUES ('kulad', '', '0');

INSERT INTO `$tablo_yasaklar` VALUES ('adsoyad', '', '0');

INSERT INTO `$tablo_yasaklar` VALUES ('posta', '', '0');

INSERT INTO `$tablo_yasaklar` VALUES ('sozcukler', '', '0');

INSERT INTO `$tablo_yasaklar` VALUES ('cumle', '', '0');

INSERT INTO `$tablo_yasaklar` VALUES ('yasak_ip', '', '0');




--		`duyurular` TABLOSU VERiLERi

CREATE TABLE `$tablo_duyurular` (
`id` smallint(5) unsigned NOT NULL auto_increment,
`fno` varchar(5) default NULL,
`duyuru_baslik` varchar(110) default NULL,
`duyuru_icerik` text,
PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8

INSERT INTO `$tablo_duyurular` VALUES (1, 'tum', 'phpKF Forumunuz Hay�rl� olsun', '<center><b>Kurulumunuz ba�ar�yla tamamlanm��t�r, hay�rl� olsun.</b>\r\n<p>Forumu kendinize g�re ayarlamak, forumlar ve duyurular eklemek i�in <a href=\"yonetim/index.php\">Y�netim Masas�</a> sayfalar�n� ziyaret edin.</center>\r\n<br>');
";




		//	VER�TABANI Y�KLEME KISMI - BA�I 	//


//  veritaban�na girilen veriler temizleniyor

function zkTemizle($metin)
{
	$donen = urldecode($metin);
	$donen = str_replace('>','',$donen);
	$donen = str_replace('<','',$donen);
	$donen = str_replace("'",'',$donen);
	$donen = str_replace('\\','',$donen);
	$donen = str_replace('/','',$donen);
	$donen = str_replace('"','',$donen);
	return $donen;
}

$_POST['vt_sunucu'] = zkTemizle($_POST['vt_sunucu']);
$_POST['vt_kullanici'] = zkTemizle($_POST['vt_kullanici']);
$_POST['vt_sifre'] = zkTemizle($_POST['vt_sifre']);
$_POST['vt_adi'] = zkTemizle($_POST['vt_adi']);



//	VER�TABANI BA�LANTISI KURULUYOR	//

$link = @mysql_connect($_POST['vt_sunucu'],$_POST['vt_kullanici'],$_POST['vt_sifre']);

$veri_tabani = @mysql_select_db($_POST['vt_adi'],$link);

if ( (!$link) OR (!$veri_tabani) )
{
	$hata = mysql_error();

	if ( (preg_match("|Can\'t connect to MySQL server|si", $hata)) OR
			(preg_match("|Unknown MySQL server|si", $hata)) )
		echo $hata_tablo1.'Veritaban� sunucusu ile ba�lant� kurulam�yor !'.$hata_tablo2.'Girdi�iniz veritaban� adresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Access denied for user|si", $hata))
		echo $hata_tablo1.'Veritaban� sunucusu ile ba�lant� kurulam�yor !'.$hata_tablo2.'Girdi�iniz veritaban� kullan�c� ad� ve �ifresini kontrol edip tekrar deneyin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	elseif (preg_match("|Unknown database|si", $hata))
		echo $hata_tablo1.'Veritaban� a��lam�yor !'.$hata_tablo2.'Veritaban� ad�n� do�ru yazd���n�zdan emin olun.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	else echo $hata_tablo1.'Veritaban� ile ba�lant� kurulam�yor !'.$hata_tablo2.'Veritaban� sunucu adresi, kullan�c� ad� ve �ifre bilgilerinizi tekrar girin.<br><br>
<b>Hata ayr�nt�s�: </b>'.$hata.$hata_tablo3;

	die();
}



//  SUNUCUDAK� MYSQL S�R�M�NE BAKILIYOR   //

if (@mysql_get_server_info())
{
	$mysql_surum = mysql_get_server_info();

	if($mysql_surum < '4.0')
	{
		echo $hata_tablo1.'MySQL S�r�m�'.$hata_tablo2.'phpKF\'nin �al��mas� i�in, sunucunuzda MySQL 4.0 ve �zeri y�kl� olmas� gerekmektedir !'.$hata_tablo3;
		exit();
	}
}

else
{
	echo $hata_tablo1.'MySQL S�r�m�'.$hata_tablo2.'phpKF\'nin �al��mas� i�in, sunucunuzda MySQL 4.0 ve �zeri y�kl� olmas� gerekmektedir !'.$hata_tablo3;
	exit();
}




// dosyadaki veriler sat�r sat�r dizi de�i�kene aktar�l�yor //
$toplam = explode(";\n\n", $vtkaydi);

// sat�r say�s� al�n�yor //
$toplam_sayi = count($toplam);

// dizideki sat�rlar d�ng�ye sokuluyor //
for ($satir=0;$satir<$toplam_sayi;$satir++)
{
	// 9 karakterden k�sa dizi elemanlar� diziden at�l�yor	//
	if (strlen($toplam[$satir]) > 9)
	{
		// yorumlar diziden at�l�yor //
		if (preg_match("/\n\n--/", $toplam[$satir]))
		{
			$yorum = explode("\n\n", $toplam[$satir]);
			$yorum_sayi = count($yorum);

			for ($satir2=0;$satir2<$yorum_sayi;$satir2++)
			{
				if ( (strlen($yorum[$satir2]) > 9) AND (!preg_match("/--/", $yorum[$satir2])) )
				// sorgu veritaban�na giriliyor //
				$strSQL = mysql_query($yorum[$satir2]) or die ($hata_tablo1.'Sorgu Ba�ar�s�z'.$hata_tablo2.mysql_error().$hata_tablo3);
			}
		}

		else // sorgu veritaban�na giriliyor //
		$strSQL = mysql_query($toplam[$satir]) or die ($hata_tablo1.'Sorgu Ba�ar�s�z'.$hata_tablo2.mysql_error().$hata_tablo3);
	}
}



		//	VER�TABANI Y�KLEME KISMI - SONU 	//



		//	AYAR.PHP DOSYA ��ER��� - BA�I 	//



$ayar_cikti = '&lt;?php
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


if (!defined(\'PHPKF_ICINDEN\')) define(\'PHPKF_ICINDEN\', true);
define(\'DOSYA_AYAR\',true);

@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
@ini_set(\'error_reporting\', E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);


//  veritaban� sunucu adresi
$cfgdbhost = \''.$_POST['vt_sunucu'].'\';

//  veritaban� ismi
$cfgdbisim = \''.$_POST['vt_adi'].'\';

//  veritaban� kullan�c� ad�
$cfgdbkul = \''.$_POST['vt_kullanici'].'\';

//  veritaban� �ifresi
$cfgdbsifre = \''.$_POST['vt_sifre'].'\';

//  tablo �neki
$tablo_oneki = \''.$_POST['tablo_onek'].'\';

//  tablo adlar�na �nek ekleniyor
$tablo_ayarlar = $tablo_oneki.\'ayarlar\';
$tablo_cevaplar = $tablo_oneki.\'cevaplar\';
$tablo_dallar = $tablo_oneki.\'dallar\';
$tablo_forumlar = $tablo_oneki.\'forumlar\';
$tablo_eklentiler = $tablo_oneki.\'eklentiler\';
$tablo_gruplar = $tablo_oneki.\'gruplar\';
$tablo_yuklemeler = $tablo_oneki.\'yuklemeler\';
$tablo_kullanicilar = $tablo_oneki.\'kullanicilar\';
$tablo_mesajlar = $tablo_oneki.\'mesajlar\';
$tablo_oturumlar = $tablo_oneki.\'oturumlar\';
$tablo_ozel_ileti = $tablo_oneki.\'ozel_ileti\';
$tablo_ozel_izinler = $tablo_oneki.\'ozel_izinler\';
$tablo_yasaklar = $tablo_oneki.\'yasaklar\';
$tablo_duyurular = $tablo_oneki.\'duyurular\';

//  forumdaki kullan�c� �ifrelerinin kar��t�r�laca�� anahtar s�zc�k
//  sadece forumda hen�z kullan�c� yoksa de�i�tirin, yoksa kimse giri� yapamaz.
$anahtar = \''.$anahtar.'\';

//  forum a��l�� tarihi
$forum_acilis = '.$tarih.';

//  forum anasayfas�n�n dosya adi (varsay�lan index.php)
$forum_index = \'index.php\';

//  portal anasayfas�n�n dosya adi (varsay�lan portal_index.php)
$portal_index = \'portal_index.php\';


//  FORUM AYARLARI VER�TABANINDAN �EK�L�YOR //

$link = @mysql_connect($cfgdbhost,$cfgdbkul,$cfgdbsifre) or die (\'&lt;h2&gt;Veritaban� ile ba�lant� kurulam�yor!&lt;/h2&gt;\'.mysql_error());

$veri_tabani = @mysql_select_db($cfgdbisim,$link) or die (\'&lt;h2&gt;veritaban� a��lam�yor!&lt;br&gt;&lt;/h2&gt;\'.mysql_error());

$sonuc = @mysql_query(&quot;SELECT * FROM $tablo_ayarlar&quot;) or die (\'&lt;h2&gt;Sorgu ba�ar�s�z&lt;/h2&gt;\'.mysql_error());

while ($ayar = @mysql_fetch_array($sonuc))
{
	$etiket = $ayar[\'0\'];
	$ayarlar[$etiket] = $ayar[\'1\'];
}

//  phpKF-portal kullan�m�
$portal_kullan = $ayarlar[\'portal_kullan\'];

?&gt;';



		//	AYAR.PHP DOSYA ��ER��� - SONU 	//





//	KURULUM TAMAMLANDI �LET�S� - 1	//

$kurulum_tamam1 = '<br><br><br><table border="0" cellspacing="1" cellpadding="10" width="580" bgcolor="#999999" align="center">
<tr><td bgcolor="#eeeeee" align="center"><font color="#333333" size="5"><b>
Kurulum Ba�ar�yla Tamamlanm��t�r
</b></font></td></tr>
<tr><td bgcolor="#fafafa">
<form action="kurulum.php" method="post" name="kurulum_formu2">
<input type="hidden" name="kurulum_yapildi" value="tamam">
<input type="hidden" name="ayar_bilgi" value="'.$ayar_cikti.'">
<font face="arial" size="3">
Kurulum tamamlanm��t�r, phpKF`yi tercih etti�iniz i�in te�ekk�r ederiz.

<p><font color="ff0000">Sunucunuzda yazma hakk� bulunmad��� i�in <b>ayar.php</b> dosyas�n�<br /> sizin y�klemeniz gerekiyor.</font>

<br><br>Forumun �al���r hale gelmesi i�in, FTP program�n�z� kullanarak yapman�z gereken iki k���k �ey kald�.

<p><li>Alttaki "Ayar Dosyas�n� indir" d��mesini t�klad�ktan sonra gelen "ayar.php" dosyas�n�, forumun bulundu�u klas�re at�n.

<p><li>"kurulum" klas�r�n�, i�indeki t�m dosyalarla beraber silin.
<br><br><br>

<center><input class="dugme" type="submit" value="Ayar Dosyas�n� indir">
<br><br><a href="../index.php">Forum Ana Sayfas�na Gitmek i�in T�klay�n</a>
</center>
</form>

<br /></td></tr></table>';



//	KURULUM TAMAMLANDI �LET�S� - 2	//

$kurulum_tamam2 = '<br><br><br><table border="0" cellspacing="1" cellpadding="10" width="580" bgcolor="#999999" align="center">
<tr><td bgcolor="#eeeeee" align="center"><font color="#333333" size="5"><b>
Kurulum Ba�ar�yla Tamamlanm��t�r
</b></font></td></tr>
<tr><td bgcolor="#fafafa">

<font face="arial" size="3">
<br />Kurulum tamamlanm��t�r, phpKF`yi tercih etti�iniz i�in te�ekk�r ederiz.

<p><b>ayar.php dosyas� otomatik olarak olu�turulmu�tur.</b>

<br><br>Forumun �al���r hale gelmesi i�in, FTP program�n�z� kullanarak yapman�z gereken tek bir �ey kald�.

<p><li>"kurulum" klas�r�n�, i�indeki t�m dosyalarla beraber silin.
<br><br><br>

<center><br><a href="../index.php">Forum Ana Sayfas�na Gitmek i�in T�klay�n</a></center>

<br /></td></tr></table>';




$adi_ayarphp = '../ayar.php';

if (@touch($adi_ayarphp))
{
	if (@is_writable($adi_ayarphp))
	{
		$dosya_ayarphp = @fopen($adi_ayarphp, 'w');

		@flock($dosya_ayarphp, 2);


		$bul = array('&gt;', '&lt;', '&quot;');
		$cevir = array('>', '<', '"');
		$ayar_cikti = @str_replace($bul, $cevir, $ayar_cikti);


		@fwrite($dosya_ayarphp, $ayar_cikti);

		@flock($dosya_ayarphp, 3);
		@fclose($dosya_ayarphp);

		echo $kurulum_tamam2;
	}

	else echo $kurulum_tamam1;
}

else echo $kurulum_tamam1;

?>