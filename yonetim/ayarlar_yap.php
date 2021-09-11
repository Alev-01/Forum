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
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}


//	FORM DOLU MU ?	//

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


// OTURUM KODU ÝÞLEMLERÝ  //
if (isset($_POST['o'])) $_POST['o'] = @zkTemizle($_POST['o']);
else $_POST['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
if ($_POST['o'] != $o)
{
	header('Location: ../hata.php?hata=45');
	exit();
}


//	ALANLAR DOLU MU ?	//

if ( (!$_POST['title']) OR (!$_POST['anasyfbaslik']) OR (!$_POST['syfbaslik']) OR (!$_POST['alanadi']) OR (!$_POST['f_dizin']) OR (!$_POST['fsyfkota']) OR (!$_POST['ksyfkota']) OR (!$_POST['k_cerez_zaman']) OR (!$_POST['ileti_sure']) OR (!$_POST['kilit_sure']) OR (!$_POST['tarih_bicimi']) OR (!$_POST['y_posta']) OR (!$_POST['eposta_yontem']) OR (!$_POST['gelen_kutu_kota']) OR (!$_POST['ulasan_kutu_kota']) OR (!$_POST['kaydedilen_kutu_kota']) OR (!$_POST['imza_uzunluk']) OR (!$_POST['resim_boyut']) OR (!$_POST['resim_yukseklik']) OR (!$_POST['resim_genislik']) OR (!$_POST['kurucu']) OR (!$_POST['yonetici']) OR (!$_POST['yardimci']) OR (!$_POST['blm_yrd']) OR (!$_POST['kullanici']) OR (!$_POST['cevrimici']))
{
	header('Location: ../hata.php?hata=98');
	exit();
}

elseif (( strlen($_POST['title']) > 100) OR ( strlen($_POST['anasyfbaslik']) > 100) OR ( strlen($_POST['syfbaslik']) > 100))
{
	header('Location: ../hata.php?hata=99');
	exit();
}

elseif (( strlen($_POST['alanadi']) > 100))
{
	header('Location: ../hata.php?hata=100');
	exit();
}

elseif (( strlen($_POST['f_dizin']) > 100))
{
	header('Location: ../hata.php?hata=101');
	exit();
}

elseif (( strlen($_POST['fsyfkota']) > 2) OR ( strlen($_POST['ksyfkota']) > 2))
{
	header('Location: ../hata.php?hata=102');
	exit();
}

elseif ((!preg_match('/^[0-9]+$/', $_POST['fsyfkota'])) OR (!preg_match('/^[0-9]+$/', $_POST['ksyfkota'])))
{
	header('Location: ../hata.php?hata=103');
	exit();
}

elseif (!preg_match('/^[0-9]+$/', $_POST['cevrimici']))
{
	header('Location: ../hata.php?hata=162');
	exit();
}

elseif (strlen($_POST['cevrimici']) > 2)
{
	header('Location: ../hata.php?hata=163');
	exit();
}

elseif (!preg_match('/^[0-9]+$/', $_POST['k_cerez_zaman']))
{
	header('Location: ../hata.php?hata=104');
	exit();
}

elseif (strlen($_POST['k_cerez_zaman']) > 5)
{
	header('Location: ../hata.php?hata=105');
	exit();
}

elseif ((!preg_match('/^[0-9]+$/', $_POST['ileti_sure'])))
{
	header('Location: ../hata.php?hata=106');
	exit();
}

elseif ($_POST['ileti_sure'] > 86400)
{
	header('Location: ../hata.php?hata=107');
	exit();
}

elseif ((!preg_match('/^[0-9]+$/', $_POST['kilit_sure'])))
{
	header('Location: ../hata.php?hata=108');
	exit();
}

elseif ($_POST['kilit_sure'] > 1440)
{
	header('Location: ../hata.php?hata=109');
	exit();
}

elseif (!preg_match('/^[01]+$/', $_POST['kayit_soru']))
{
	header('Location: ../hata.php?hata=110');
	exit();
}

elseif ( ( strlen($_POST['kayit_sorusu']) > 100) OR ( strlen($_POST['kayit_cevabi']) > 100))
{
	header('Location: ../hata.php?hata=111');
	exit();
}

elseif ( (!preg_match('/^[0-9]+$/', $_POST['imza_uzunluk'])) OR
		($_POST['imza_uzunluk'] > 500) )
{
	header('Location: ../hata.php?hata=112');
	exit();
}

elseif (( strlen($_POST['tarih_bicimi']) > 20))
{
	header('Location: ../hata.php?hata=113');
	exit();
}

elseif (( strlen($_POST['saat_dilimi']) > 4) OR ( strlen($_POST['saat_dilimi']) < 1))
{
	header('Location: ../hata.php?hata=114');
	exit();
}

elseif (( strlen($_POST['forum_rengi']) > 10) OR ( strlen($_POST['forum_rengi']) < 1))
{
	header('Location: ../hata.php?hata=115');
	exit();
}

elseif (!preg_match('/^[012]+$/', $_POST['hesap_etkin']))
{
	header('Location: ../hata.php?hata=116');
	exit();
}

elseif ( (!preg_match('/^[01]+$/', $_POST['bbcode'])) OR (!preg_match('/^[01]+$/', $_POST['o_ileti']))
		OR (!preg_match('/^[01]+$/', $_POST['seo'])) OR (!preg_match('/^[01]+$/', $_POST['sonkonular']))
		OR (!preg_match('/^[01]+$/', $_POST['forum_durumu'])) OR (!preg_match('/^[01]+$/', $_POST['portal']))
		OR (!preg_match('/^[01]+$/', $_POST['boyutlandirma'])) OR (!preg_match('/^[01]+$/', $_POST['kayit_onay']))
		OR (!preg_match('/^[01]+$/', $_POST['bolum_kisi'])) OR (!preg_match('/^[01]+$/', $_POST['konu_kisi']))
		OR (!preg_match('/^[01]+$/', $_POST['uye_kayit'])) OR (!preg_match('/^[01]+$/', $_POST['oi_uyari'])) )
{
	header('Location: ../hata.php?hata=160');
	exit();
}

elseif (!preg_match('/^[0-9]+$/', $_POST['kacsonkonu']))
{
	header('Location: ../hata.php?hata=118');
	exit();
}

elseif ($_POST['kacsonkonu'] > 50)
{
	header('Location: ../hata.php?hata=119');
	exit();
}




			//	YETKÝLÝ ÝSÝMLERÝ AYARLARI	//


elseif (( strlen($_POST['kurucu']) > 100))
{
	header('Location: ../hata.php?hata=120');
	exit();
}

elseif (( strlen($_POST['yonetici']) > 100))
{
	header('Location: ../hata.php?hata=121');
	exit();
}

elseif (( strlen($_POST['yardimci']) > 100))
{
	header('Location: ../hata.php?hata=122');
	exit();
}

elseif (( strlen($_POST['blm_yrd']) > 100))
{
	header('Location: ../hata.php?hata=191');
	exit();
}

elseif (( strlen($_POST['kullanici']) > 100))
{
	header('Location: ../hata.php?hata=123');
	exit();
}


			//	ÖZEL ÝLETÝ AYARLARI	//


elseif (( strlen($_POST['gelen_kutu_kota']) > 3) OR ( strlen($_POST['ulasan_kutu_kota']) > 3) OR ( strlen($_POST['kaydedilen_kutu_kota']) > 3))
{
	header('Location: ../hata.php?hata=124');
	exit();
}

elseif ( (!preg_match('/^[0-9]+$/', $_POST['gelen_kutu_kota'])) OR
		(!preg_match('/^[0-9]+$/', $_POST['ulasan_kutu_kota'])) OR
		(!preg_match('/^[0-9]+$/', $_POST['kaydedilen_kutu_kota'])) )
{
	header('Location: ../hata.php?hata=125');
	exit();
}


			//	KULLANICI RESÝMÝ AYARLARI	//


elseif ((!preg_match('/^[01]+$/', $_POST['resim_yukle'])))
{
	header('Location: ../hata.php?hata=126');
	exit();
}

elseif ((!preg_match('/^[01]+$/', $_POST['uzak_resim'])))
{
	header('Location: ../hata.php?hata=127');
	exit();
}

elseif ((!preg_match('/^[01]+$/', $_POST['resim_galerisi'])))
{
	header('Location: ../hata.php?hata=128');
	exit();
}

elseif ((!preg_match('/^[0-9]+$/', $_POST['resim_boyut'])) OR ($_POST['resim_boyut'] > 999))
{
	header('Location: ../hata.php?hata=129');
	exit();
}

elseif ( (!preg_match('/^[0-9]+$/', $_POST['resim_yukseklik'])) OR
		(!preg_match('/^[0-9]+$/', $_POST['resim_genislik'])) OR
		($_POST['resim_yukseklik'] > 999) OR ($_POST['resim_genislik'] > 999))
{
	header('Location: ../hata.php?hata=130');
	exit();
}


			//	E-POSTA AYARLARI	//


elseif (( strlen($_POST['y_posta']) > 100))
{
	header('Location: ../hata.php?hata=131');
	exit();
}

elseif (($_POST['eposta_yontem'] != 'mail') AND ($_POST['eposta_yontem'] != 'sendmail') AND ($_POST['eposta_yontem']!= 'smtp'))
{
	header('Location: ../hata.php?hata=132');
	exit();
}

elseif (($_POST['smtp_kd'] != 'true') AND ($_POST['smtp_kd'] != 'false'))
{
	header('Location: ../hata.php?hata=133');
	exit();
}

elseif (( strlen($_POST['smtp_sunucu']) > 100))
{
	header('Location: ../hata.php?hata=134');
	exit();
}

elseif (( strlen($_POST['smtp_kullanici']) > 100))
{
	header('Location: ../hata.php?hata=135');
	exit();
}

elseif (( strlen($_POST['smtp_sifre']) > 100))
{
	header('Location: ../hata.php?hata=136');
	exit();
}

else
{
	if (!defined('DOSYA_GERECLER')) include '../gerecler.php';

	if (get_magic_quotes_gpc(1))
	{
		$_POST['title'] = stripslashes($_POST['title']);
		$_POST['anasyfbaslik'] = stripslashes($_POST['anasyfbaslik']);
		$_POST['syfbaslik'] = stripslashes($_POST['syfbaslik']);
	}

	$_POST['title'] = zkTemizle($_POST['title']);
	$_POST['anasyfbaslik'] = zkTemizle($_POST['anasyfbaslik']);
	$_POST['syfbaslik'] = zkTemizle($_POST['syfbaslik']);
	$_POST['alanadi'] = zkTemizle($_POST['alanadi']);
	$_POST['f_dizin'] = zkTemizle($_POST['f_dizin']);
	$_POST['tarih_bicimi'] = zkTemizle($_POST['tarih_bicimi']);
	$_POST['kayit_sorusu'] = zkTemizle($_POST['kayit_sorusu']);
	$_POST['kayit_cevabi'] = zkTemizle($_POST['kayit_cevabi']);
	$_POST['forum_rengi'] = zkTemizle($_POST['forum_rengi']);
	$_POST['kul_resim'] = zkTemizle($_POST['kul_resim']);
	$_POST['kurucu'] = zkTemizle($_POST['kurucu']);
	$_POST['yonetici'] = zkTemizle($_POST['yonetici']);
	$_POST['yardimci'] = zkTemizle($_POST['yardimci']);
	$_POST['blm_yrd'] = zkTemizle($_POST['blm_yrd']);
	$_POST['kullanici'] = zkTemizle($_POST['kullanici']);



	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[title]' where etiket='title' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[anasyfbaslik]' where etiket='anasyfbaslik' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[syfbaslik]' where etiket='syfbaslik' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[alanadi]' where etiket='alanadi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[f_dizin]' where etiket='f_dizin' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[forum_durumu]' where etiket='forum_durumu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[fsyfkota]' where etiket='fsyfkota' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[ksyfkota]' where etiket='ksyfkota' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


	$_POST['k_cerez_zaman'] = ($_POST['k_cerez_zaman'] * 60);

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[k_cerez_zaman]' where etiket='k_cerez_zaman' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[ileti_sure]' where etiket='ileti_sure' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


	$_POST['kilit_sure'] = ($_POST['kilit_sure'] * 60);

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kilit_sure]' where etiket='kilit_sure' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[uye_kayit]' where etiket='uye_kayit' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kayit_soru]' where etiket='kayit_soru' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kayit_sorusu]' where etiket='kayit_sorusu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kayit_cevabi]' where etiket='kayit_cevabi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[imza_uzunluk]' where etiket='imza_uzunluk' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[tarih_bicimi]' where etiket='tarih_bicimi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[hesap_etkin]' where etiket='hesap_etkin' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[saat_dilimi]' where etiket='saat_dilimi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[forum_rengi]' where etiket='forum_rengi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[bbcode]' where etiket='bbcode' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[seo]' where etiket='seo' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[boyutlandirma]' where etiket='boyutlandirma' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[bolum_kisi]' where etiket='bolum_kisi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[konu_kisi]' where etiket='konu_kisi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[portal]' where etiket='portal_kullan' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kayit_onay]' where etiket='onay_kodu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[sonkonular]' where etiket='sonkonular' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kacsonkonu]' where etiket='kacsonkonu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kul_resim]' where etiket='kul_resim' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kurucu]' where etiket='kurucu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[yonetici]' where etiket='yonetici' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[yardimci]' where etiket='yardimci' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[blm_yrd]' where etiket='blm_yrd' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kullanici]' where etiket='kullanici' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[o_ileti]' where etiket='o_ileti' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[oi_uyari]' where etiket='oi_uyari' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[gelen_kutu_kota]' where etiket='gelen_kutu_kota' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[ulasan_kutu_kota]' where etiket='ulasan_kutu_kota' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[kaydedilen_kutu_kota]' where etiket='kaydedilen_kutu_kota' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[resim_yukle]' where etiket='resim_yukle' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[uzak_resim]' where etiket='uzak_resim' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[resim_galerisi]' where etiket='resim_galerisi' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


	$_POST['resim_boyut'] = ($_POST['resim_boyut'] * 1024);

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[resim_boyut]' where etiket='resim_boyut' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[resim_yukseklik]' where etiket='resim_yukseklik' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[resim_genislik]' where etiket='resim_genislik' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[y_posta]' where etiket='y_posta' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[eposta_yontem]' where etiket='eposta_yontem' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[smtp_kd]' where etiket='smtp_kd' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[smtp_sunucu]' where etiket='smtp_sunucu' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[smtp_kullanici]' where etiket='smtp_kullanici' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');


	$_POST['cevrimici'] = ($_POST['cevrimici'] * 60);

	$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[cevrimici]' where etiket='cevrimici' LIMIT 1";
	$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');



	if ($_POST['smtp_sifre'] != 'sifre_degismedi')
	{
		$strSQL = "UPDATE $tablo_ayarlar SET deger='$_POST[smtp_sifre]' where etiket='smtp_sifre' LIMIT 1";
		$sonuc = mysql_query($strSQL) OR die ('<h2>sorgu baþarýsýz</h2>');
	}



	header('Location: ../hata.php?bilgi=12');
	exit();
}
endif;
?>