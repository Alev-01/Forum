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

if ( (isset($_POST['guncelleme_yapildi'])) AND (isset($_POST['ayar_bilgi'])) AND
	($_POST['guncelleme_yapildi'] == 'tamam') )
{
	header('Content-Type: text/html; charset=UTF-8');
	header('Content-Type: text/x-delimtext; name="ayar.php"');
	header('Content-disposition: attachment; filename=ayar.php');

	//  magic_quotes_gpc a��ksa //
	if (get_magic_quotes_gpc(1))
	echo stripslashes($_POST['ayar_bilgi']);

	//  magic_quotes_gpc kapal�ysa  //
	else echo $_POST['ayar_bilgi'];
	exit();
}



// ayar.php yok, kurulum yap�lmam��.

if (!@is_file('../ayar.php'))
{
	echo '<br><center><font color="red" face="arial" size="4">ayar.php dosyas� yok !</font>
	<br><br>Kurulum yapmad�ysan�z �nce <a href="index.php">kurulum sayfas�ndan</a> kurulum yap�n.</center>';

	exit();
}

else include '../ayar.php';

if (!isset($portal_kullan)) $portal_kullan = 0;



//  SADECE Y�NET�C�LER G�NCELLEME YAPAB�L�R  //

$giris_formu = '<center><font color="#ff0000"><b>Sadece y�neticiler g�ncelleme yapabilir !
<br><br>Giri� sayfas�ndan veya alttaki formdan y�netici olarak giri� yap�p tekrar deneyin.</b>
</font><br><br><br></center>
<form name="giris" action="../giris.php" method="post">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">
<input type="hidden" name="git" value="kurulum/index.php">
<table cellspacing="1" width="300" cellpadding="7" border="0" align="center" bgcolor="#d0d0d0">
<tbody><tr bgcolor="#ffffff"><td width="100" align="left"><b>Kullan�c� Ad�:</b></td><td align="left">
<input class="formlar" type="text" name="kullanici_adi" size="20" maxlength="20"></td>
</tr><tr bgcolor="#ffffff"><td align="left"><b>�ifre:</b></td><td align="left">
<input type="password" name="sifre" size="20" maxlength="20"></td>
</tr><tr bgcolor="#ffffff"><td height="30" align="left"><input type="checkbox" name="hatirla">Beni hat�rla</td>
<td align="center" valign="top"><input type="submit" value="Giri� Yap"></td>
</tr></tbody></table></form>';


if ( (isset($_COOKIE['kullanici_kimlik'])) AND ($_COOKIE['kullanici_kimlik'] != '') )
{
	$strSQL = "SELECT id,yetki FROM $tablo_kullanicilar WHERE kullanici_kimlik='$_COOKIE[kullanici_kimlik]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$kullanici_kim = mysql_fetch_assoc($sonuc);

	if ( (!isset($kullanici_kim['yetki'])) OR ($kullanici_kim['yetki'] != '1') )
	{
		echo $giris_formu;
		exit();
	}
}

else
{
	echo $giris_formu;
	exit();
}





// G�NCELLEME YAPILMI�SA //

if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.90'))
{
	header('Location: ../hata.php?uyari=1');
	exit();
}


// Eklenti Kurulum
function xml_oku($dosya){
$ebilgi = new XMLReader();
$ebilgi->open($dosya, 'iso-8859-9');
while ($ebilgi->read()){
if ($ebilgi->nodeType == XMLReader::ELEMENT) $etiket = $ebilgi->name;
elseif (($ebilgi->nodeType == XMLReader::TEXT) OR ($ebilgi->nodeType == XMLReader::CDATA)){
if ($etiket == 'eklenecek_dosya') $dizi[$etiket][0] = $ebilgi->value;
elseif ($etiket == 'dizin_olustur') $dizi[$etiket][0] = $ebilgi->value;
elseif ($etiket == 'kur_veritabani') $dizi[$etiket][0] = $ebilgi->value;
else $dizi[$etiket] = $ebilgi->value;}}
$ebilgi->close();
return($dizi);
}



//   S�R�M 1.20`DEN ESK� �SE   //
//   S�R�M 1.20`DEN ESK� �SE   //


if (!isset($ayarlar['surum']))
{
	$ayarlar['surum'] = '1.20';
	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('surum', '1.20')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 0</h2>'.mysql_error());

	$tablo_duyurular = $tablo_oneki.'duyurular';


	$strSQL = "SHOW FIELDS FROM $tablo_kullanicilar";
	$ksonuc = mysql_query($strSQL);
	$ksatir_sayi = mysql_num_rows($ksonuc);


	// phpKF s�r�m 1.15 ise //

	if (isset($ayarlar['yonetici']))
	{
		$tablo_duyurular = $tablo_oneki.'duyurular';

		$strSQL = "CREATE TABLE `$tablo_duyurular` (
		`id` smallint(5) unsigned NOT NULL auto_increment,
		`fno` varchar(5) default NULL,
		`duyuru_baslik` varchar(110) default NULL,
		`duyuru_icerik` text,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM CHARSET=utf8";

		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 1</h2>'.mysql_error());
	}


	// phpKF s�r�m 1.12 ise //

	elseif (!isset($ayarlar['yonetici']))
	{
		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kurucu', 'Site Kurucusu')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 2</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('yonetici', 'Forum Y�neticisi')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 3</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('yardimci', 'Forum Yard�mc�s�')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 4</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kullanici', 'Kay�tl� Kullan�c�')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 5</h2>'.mysql_error());


		$strSQL = "CREATE TABLE `$tablo_duyurular` (
		`id` smallint(5) unsigned NOT NULL auto_increment,
		`fno` varchar(5) default NULL,
		`duyuru_baslik` varchar(110) default NULL,
		`duyuru_icerik` text,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM CHARSET=utf8";

		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 6</h2>'.mysql_error());
	}


	// phpKF s�r�m 1.10 dan eski ise //

	elseif ($ksatir_sayi < '36')
	{
		$strSQL = "ALTER TABLE $tablo_kullanicilar ADD icq varchar(30) default NULL";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 7</h2>'.mysql_error());

		$strSQL = "ALTER TABLE $tablo_kullanicilar ADD msn varchar(100) default NULL";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 8</h2>'.mysql_error());

		$strSQL = "ALTER TABLE $tablo_kullanicilar ADD yahoo varchar(100) default NULL";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 9</h2>'.mysql_error());

		$strSQL = "ALTER TABLE $tablo_kullanicilar ADD aim varchar(100) default NULL";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 10</h2>'.mysql_error());

		$strSQL = "ALTER TABLE $tablo_kullanicilar ADD skype varchar(100) default NULL";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 11</h2>'.mysql_error());


		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kurucu', 'Site Kurucusu')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 2</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('yonetici', 'Forum Y�neticisi')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 3</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('yardimci', 'Forum Yard�mc�s�')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 4</h2>'.mysql_error());

		$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kullanici', 'Kay�tl� Kullan�c�')";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 5</h2>'.mysql_error());


		$strSQL = "CREATE TABLE `$tablo_duyurular` (
		`id` smallint(5) unsigned NOT NULL auto_increment,
		`fno` varchar(5) default NULL,
		`duyuru_baslik` varchar(110) default NULL,
		`duyuru_icerik` text,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM CHARSET=utf8";

		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 12</h2>'.mysql_error());
	}


	if (!isset($ayarlar['seo']))
	{
		$strSQL = "INSERT INTO `$tablo_ayarlar` VALUES ('seo', '1');";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 13</h2>'.mysql_error());
	}
}





//   S�R�M 1.20 �SE   //
//   S�R�M 1.20 �SE   //


if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.20'))
{
	$ayarlar['surum'] = '1.40';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.40' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 14</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('sonkonular', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 15</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kacsonkonu', '10')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 16</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('temadizini', '5renkli')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 17</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('tema_secenek', '5renkli,')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 18</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD temadizini varchar(25) DEFAULT NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 19</h2>'.mysql_error());
}











//   S�R�M 1.40 �SE   //
//   S�R�M 1.40 �SE   //


if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.40'))
{
	$ayarlar['surum'] = '1.50';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.50' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 20</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('cevrimici', '600')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 21</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('forum_durumu', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 22</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_ozel_izinler ADD konu_acma TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 23</h2>'.mysql_error());


	//  FORUMLAR TABLOSUNA YEN� ALANLAR EKLEN�YOR   //

	$strSQL = "ALTER TABLE $tablo_forumlar ADD konu_acma_izni TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 24</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_forumlar ADD konu_sayisi MEDIUMINT(8) UNSIGNED NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 25</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_forumlar ADD cevap_sayisi INT(10) UNSIGNED NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 26</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_forumlar ADD alt_forum SMALLINT(5) UNSIGNED NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 27</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_forumlar ADD INDEX (alt_forum)";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 28</h2>'.mysql_error());


	//  KULLANICILAR TABLOSUNA YEN� ALANLAR EKLEN�YOR   //

	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD temadizinip varchar(25) DEFAULT NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 29</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD ozel_ad VARCHAR(30) DEFAULT NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 30</h2>'.mysql_error());


	// FORUM B�LG�LER� �EK�L�YOR	//

	$strSQL = "SELECT id FROM $tablo_forumlar";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_assoc($sonuc))
	{
		//	FORUMDAK� BA�LIKLARIN SAYISI ALINIYOR	//

		$result = mysql_query("SELECT id FROM $tablo_mesajlar WHERE hangi_forumdan='$forum_satir[id]'");
		$konu_sayi = mysql_num_rows($result);


		//	FORUMDAK� T�M MESAJLARIN SAYISI ALINIYOR	//

		$result = mysql_query("SELECT id FROM $tablo_cevaplar WHERE hangi_forumdan='$forum_satir[id]'");
		$cevap_sayi = mysql_num_rows($result);


		//  KONU VE CEVAP SAYISI YEN� ALANLARA G�R�L�YOR    //

		$strSQL = "UPDATE $tablo_forumlar SET konu_sayisi='$konu_sayi', cevap_sayisi='$cevap_sayi'
			WHERE id='$forum_satir[id]' LIMIT 1";
		$sonuc2 = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 31</h2>'.mysql_error());
	}
}





//   S�R�M 1.50 �SE   //
//   S�R�M 1.50 �SE   //


if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.50'))
{
	$ayarlar['surum'] = '1.60';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.60' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 32</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_mesajlar ADD silinmis TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 33</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_cevaplar ADD silinmis TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 34</h2>'.mysql_error());
}





//   S�R�M 1.60 �SE   //
//   S�R�M 1.60 �SE   //


if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.60'))
{
	$ayarlar['surum'] = '1.70';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.70' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 35</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('portal_kullan', '$portal_kullan')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 36</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('onay_kodu', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 37</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD posta2 VARCHAR(100) DEFAULT NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 38</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_yasaklar ADD tip TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 39</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_yasaklar VALUES ('adsoyad', '', '0')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 40</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_mesajlar ADD ifade TINYINT(1) NOT NULL DEFAULT '1'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 41</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_cevaplar ADD ifade TINYINT(1) NOT NULL DEFAULT '1'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 42</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_ozel_ileti ADD ifade TINYINT(1) NOT NULL DEFAULT '1'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 43</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_mesajlar ADD son_cevap int(10) unsigned NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 44</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_mesajlar ADD son_cevap_yazan varchar(20) NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 45</h2>'.mysql_error());


	// konular al�n�yor
	$strSQL = "SELECT id FROM $tablo_mesajlar WHERE cevap_sayi!=0 ORDER BY id";
	$sonuc = mysql_query($strSQL);

	while ($konular = mysql_fetch_assoc($sonuc))
	{
		// son cevap al�n�yor
		$strSQL = "SELECT id,tarih,cevap_yazan FROM $tablo_cevaplar WHERE hangi_basliktan='$konular[id]' AND silinmis=0 ORDER BY tarih DESC LIMIT 1";
		$soncevap_sonuc = mysql_query($strSQL);
		$soncevap = mysql_fetch_assoc($soncevap_sonuc);

		// son cevap bilgileri giriliyor
		$strSQL = "UPDATE $tablo_mesajlar SET son_cevap='$soncevap[id]',son_mesaj_tarihi='$soncevap[tarih]',son_cevap_yazan='$soncevap[cevap_yazan]' where id='$konular[id]' LIMIT 1";
		$soncevap_sonuc = mysql_query($strSQL);
	}
}





//   S�R�M 1.70 �SE   //
//   S�R�M 1.70 �SE   //


if ( (isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.70') )
{
	$ayarlar['surum'] = '1.80';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.80' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 46</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('blm_yrd', 'B�l�m Yard�mc�s�')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 47</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('kul_resim', 'dosyalar/resimler/galeri/resim_yok.png')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 48</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('boyutlandirma', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 49</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('duyuru_tarihi', '0')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 50</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_forumlar ADD gizle TINYINT(1) NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 51</h2>'.mysql_error());

	$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE yetki='2'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 52</h2>'.mysql_error());


	// �zel ileti - ba��
	$strSQL = "SELECT id,gonderen_kopya,alan_kutu FROM $tablo_ozel_ileti WHERE gonderen_kopya!='' ORDER BY id";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 53</h2>');

	while ($oileti = mysql_fetch_array($sonuc))
	{
		$strSQL = "UPDATE $tablo_ozel_ileti SET alan_kutu='$oileti[alan_kutu]' WHERE id='$oileti[gonderen_kopya]' LIMIT 1";
		$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 54</h2>');
	}

	$strSQL = "DELETE FROM $tablo_ozel_ileti WHERE gonderen_kopya!=''";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 55</h2>');

	$strSQL = "ALTER TABLE $tablo_ozel_ileti DROP gonderen_kopya";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 56</h2>');
	// �zel ileti - sonu
}





//   S�R�M 1.80 �SE   //
//   S�R�M 1.80 �SE   //


if ((isset($ayarlar['surum'])) AND ($ayarlar['surum'] == '1.80'))
{
	$ayarlar['surum'] = '1.90';
	$strSQL = "UPDATE $tablo_ayarlar SET deger='1.90' WHERE etiket='surum' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 57</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('bolum_kisi', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 58</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('konu_kisi', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 59</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('uye_kayit', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 60</h2>'.mysql_error());

	$strSQL = "INSERT INTO $tablo_ayarlar VALUES ('oi_uyari', '1')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 61</h2>'.mysql_error());


	$strSQL = "INSERT INTO $tablo_yasaklar VALUES ('yasak_ip','','0')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 62</h2>'.mysql_error());


	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD `grupid` smallint(5) unsigned DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 63</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_kullanicilar ADD `sayfano` VARCHAR(25) NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 64</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_oturumlar ADD `sayfano` VARCHAR(25) NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 65</h2>'.mysql_error());


	$strSQL = "UPDATE $tablo_kullanicilar SET sayfano='-1' where hangi_sayfada='Kullan�c� ��k�� yapt�'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 66</h2>'.mysql_error());



	$strSQL = "ALTER TABLE $tablo_ozel_izinler CHANGE `kulad` `kulad` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 67</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_ozel_izinler ADD `kulid` mediumint(8) unsigned NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 68</h2>'.mysql_error());

	$strSQL = "ALTER TABLE $tablo_ozel_izinler ADD `grup` smallint(5) unsigned NOT NULL DEFAULT '0'";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 69</h2>'.mysql_error());

	// �zel izinler id alma - ba��
	$strSQL = "SELECT kulad FROM $tablo_ozel_izinler ORDER BY kulad";
	$sonucoi = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 70</h2>');

	while ($oizin = mysql_fetch_array($sonucoi))
	{
		$strSQL = "SELECT id FROM $tablo_kullanicilar WHERE kullanici_adi='$oizin[kulad]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 71</h2>');
		$kulid = mysql_fetch_array($sonuc);


		$strSQL = "UPDATE $tablo_ozel_izinler SET kulid='$kulid[id]' WHERE kulad='$oizin[kulad]'";
		$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z 72</h2>');
	}
	// �zel izinler id alma - sonu



	$tablo_eklentiler = $tablo_oneki.'eklentiler';
	$tablo_gruplar = $tablo_oneki.'gruplar';
	$tablo_yuklemeler = $tablo_oneki.'yuklemeler';


	$strSQL = "CREATE TABLE `$tablo_eklentiler` (
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
) ENGINE=MyISAM CHARSET=utf8;";

	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 73</h2>'.mysql_error());


	$strSQL = "CREATE TABLE `$tablo_gruplar` (
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
) ENGINE=MyISAM CHARSET=utf8;";

	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 74</h2>'.mysql_error());


	$strSQL = "CREATE TABLE `$tablo_yuklemeler` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`tarih` int(11) NOT NULL DEFAULT '0',
`boyut` int(7) unsigned DEFAULT '0',
`ip` varchar(15) DEFAULT NULL,
`uye_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
`uye_adi` varchar(20) NOT NULL DEFAULT '',
`dosya` varchar(30) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8;";

	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 75</h2>'.mysql_error());
}


// T�m kurulu eklentiler taran�yor

$yedizin_adi = '../eklentiler/';
$yedizin = @opendir($yedizin_adi);
while (@gettype($bilgi = @readdir($yedizin)) != 'boolean')
{
	$vt_islem = 0;
	$dosya_islem = 0;
	$dizin_islem = 0;

	if ((@is_dir($yedizin_adi.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..'))
	{
		if (@is_file($yedizin_adi.$bilgi.'/eklenti_bilgi.xml')) $edbilgi = @xml_oku($yedizin_adi.$bilgi.'/eklenti_bilgi.xml');
		else continue;

		if ($edbilgi['eklenti_kurulu']!='1') continue;

		if (isset($edbilgi['eklenti_etkin'])) $eetkin = 1;
		else $eetkin = 2;

		if (isset($edbilgi['kur_veritabani'])) $vt_islem = 1;
		if (isset($edbilgi['eklenecek_dosya'])) $dosya_islem = 1;
		if (isset($edbilgi['dizin_olustur'])) $dizin_islem = 1;
	}
	else continue;

	$strSQL = "INSERT INTO $tablo_eklentiler VALUES ('$bilgi', '1', '$eetkin', '$vt_islem', '$dosya_islem', '$dizin_islem', '$edbilgi[sistem]', '$edbilgi[uyumlu_surum]', '$edbilgi[eklenti_surumu]')";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu ba�ar�s�z 76</h2>'.mysql_error());

	unset($edbilgi);
}
@closedir($yedizin);



		//	AYAR.PHP DOSYA ��ER��� - BA�I 	//


if (!isset($forum_index)) $forum_index = 'index.php';
if (!isset($portal_index)) $portal_index = 'portal_index.php';


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
$cfgdbhost = \''.$cfgdbhost.'\';

//  veritaban� ismi
$cfgdbisim = \''.$cfgdbisim.'\';

//  veritaban� kullan�c� ad�
$cfgdbkul = \''.$cfgdbkul.'\';

//  veritaban� �ifresi
$cfgdbsifre = \''.$cfgdbsifre.'\';

//  tablo �neki
$tablo_oneki = \''.$tablo_oneki.'\';

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
$forum_acilis = '.$forum_acilis.';

//  forum anasayfas�n�n dosya adi (varsay�lan index.php)
$forum_index = \''.$forum_index.'\';

//  portal anasayfas�n�n dosya adi (varsay�lan portal_index.php)
$portal_index = \''.$portal_index.'\';



//  FORUM AYARLARI VER�TABANINDAN �EK�L�YOR  //

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






//	G�NCELLEME TAMAMLANDI �LET�S� - 1	//

$guncelleme_tamam1 = '<center><br><h1>G�ncelleme Ba�ar�yla Tamamlanm��t�r.</h1><br>

<form action="guncelle.php" method="post" name="guncelleme_formu2">
<input type="hidden" name="guncelleme_yapildi" value="tamam">
<input type="hidden" name="ayar_bilgi" value="'.$ayar_cikti.'">
<font face="arial" size="3">

<font color="red">Sunucunuzda yazma hakk� bulunmad��� i�in <b>ayar.php</b> dosyas�n� sizin y�klemeniz gerekiyor.</font></center>

<br><br><li>Son olarak alttaki "Ayar Dosyas�n� indir" d��mesini t�klad�ktan sonra gelen "ayar.php" dosyas�n�, eski ayar.php dosyas�n�n �zerine yaz�n.

<p><li>"kurulum" klas�r�n�, i�indeki t�m dosyalarla beraber silin.
<br><br><br>

<center><input class="dugme" type="submit" value="Ayar Dosyas�n� indir">
<br><br><a href="../index.php">Forum Ana Sayfas�na Gitmek i�in T�klay�n</a>
</font>
</center>
</form>';



//	G�NCELLEME TAMAMLANDI �LET�S� - 2	//

$guncelleme_tamam2 = '<center><br><h1>G�ncelleme Ba�ar�yla Tamamlanm��t�r.</h1><br>

<font face="arial" size="3">

<b>ayar.php dosyas� otomatik olarak olu�turulmu�tur.</b>

<br><br><li>Son olarak "kurulum" klas�r�n�, i�indeki t�m dosyalarla beraber silin.

<br><br><br><a href="../index.php">Forum Ana Sayfas�na Gitmek i�in T�klay�n</a>
</font>
</center>';




$adi_ayarphp = '../ayar.php';

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

	echo $guncelleme_tamam2;
}

else echo $guncelleme_tamam1;

?>