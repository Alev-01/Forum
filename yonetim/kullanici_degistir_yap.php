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
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// oturum bilgisine bakýlýyor
if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

if ($_GET['o'] != $o)
{
	header('Location: ../hata.php?hata=45');
	exit();
}



$_POST['id'] = @zkTemizle($_POST['id']);

$strSQL = "SELECT id,kullanici_adi,sifre,posta,resim,grupid FROM $tablo_kullanicilar WHERE id='$_POST[id]' LIMIT 1";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
$satir = mysql_fetch_array($sonuc);


if ($satir['id'] == 1)
{
	header('Location: ../hata.php?hata=147');
	exit();
}




// FORM DOLUMU BAKILIYOR    //


if ($_POST['profil_degisti_mi'] == 'form_dolu'):


if ( (!$_POST['ysifre']) OR (!$_POST['ysifre2']) OR (!$_POST['posta']) OR (!$_POST['gercek_ad']) OR (!$_POST['dogum_tarihi']) OR (!$_POST['sehir']) )
{
	header('Location: ../hata.php?hata=73');
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_ ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['gercek_ad']))
{
	header('Location: ../hata.php?hata=31');
	exit();
}

if ( strlen($_POST['gercek_ad']) > 30)
{
	header('Location: ../hata.php?hata=32');
	exit();
}

if ($_POST['ysifre'] != $_POST['ysifre2'])
{
	header('Location: ../hata.php?hata=33');
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_POST['ysifre']))
{
	header('Location: ../hata.php?hata=34');
	exit();
}

if (( strlen($_POST['ysifre']) > 20) or ( strlen($_POST['ysifre']) < 5))
{
	header('Location: ../hata.php?hata=35');
	exit();
}

if ((!preg_match('/^[A-Za-zðÐüÜÞþÝýÖöÇç]+$/', $_POST['sehir'])) or ( strlen($_POST['sehir']) > 15))
{
	header('Location: ../hata.php?hata=36');
	exit();
}

if ((!preg_match('/^[0-9-]+$/', $_POST['dogum_tarihi'])) or ( strlen($_POST['dogum_tarihi']) != 10))
{
	header('Location: ../hata.php?hata=74');
	exit();
}

if ( ($_POST['yetki'] != '0') AND ($_POST['yetki'] != '1') AND ($_POST['yetki'] != '2') AND ($_POST['yetki'] != '3') )
{
	header('Location: ../hata.php?hata=148');
	exit();
}

if ( ($_POST['eski_yetki'] != '0') AND ($_POST['eski_yetki'] != '1') AND ($_POST['eski_yetki'] != '2') AND ($_POST['eski_yetki'] != '3') )
{
	header('Location: ../hata.php?hata=148');
	exit();
}

if ((!isset($_POST['grup'])) OR (!preg_match('/^[0-9]+$/', $_POST['grup'])))
{
	header('Location: ../hata.php?hata=204');
	exit();
}

if (!preg_match('/^([~&+.0-9a-z_-]+)@(([~&+0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $_POST['posta']))
{
	header('Location: ../hata.php?hata=10');
	exit();
}

if ( strlen($_POST['posta']) > 70)
{
	header('Location: ../hata.php?hata=40');
	exit();
}

if ( strlen($_POST['web']) > 70)
{
	header('Location: ../hata.php?hata=75');
	exit();
}

if ( strlen($_POST['web']) != 0)
{
	if ((!preg_match('/^http:\/\//i', $_POST['web'])))
	{
		$_POST['web'] = 'http://'.$_POST['web'];
	}
}


if (!preg_match('/^[A-Za-z0-9-_]+$/', $_POST['tema_secim']))
{
	header('Location: ../hata.php?hata=76');
	exit();
}

if ( strlen($_POST['tema_secim']) > 20)
{
	header('Location: ../hata.php?hata=77');
	exit();
}


if ( (isset($_POST['tema_secimp'])) AND (!preg_match('/^[A-Za-z0-9-_]+$/', $_POST['tema_secimp'])) )
{
	header('Location: ../hata.php?hata=76');
	exit();
}

if  ( (isset($_POST['tema_secimp'])) AND ( strlen($_POST['tema_secimp']) > 20) )
{
	header('Location: ../hata.php?hata=77');
	exit();
}


if( strlen($_POST['imza']) > $ayarlar['imza_uzunluk'] )
{
	header('Location: ../hata.php?hata=78');
	exit();
}


//  ANINDA MESAJLAÞMA ADRESLERÝ //

if ( strlen($_POST['icq']) > 30)
{
	header('Location: ../hata.php?hata=79');
	exit();
}

if ( strlen($_POST['aim']) > 70)
{
	header('Location: ../hata.php?hata=80');
	exit();
}

if ( strlen($_POST['msn']) > 70)
{
	header('Location: ../hata.php?hata=81');
	exit();
}

if ( strlen($_POST['yahoo']) > 70)
{
	header('Location: ../hata.php?hata=82');
	exit();
}

if ( strlen($_POST['skype']) > 70)
{
	header('Location: ../hata.php?hata=83');
	exit();
}


//	ÝMZADAN ZARARLI KODLAR TEMÝZLENÝYOR	//

if ( isset($_POST['imza']) AND ($_POST['imza'] != '') )
{
	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['imza'] = zkTemizle(stripslashes($_POST['imza']));
	}

	//	magic_quotes_gpc kapalýysa	//
	else
	{
		$_POST['imza'] = zkTemizle($_POST['imza']);
	}
}






		//	KULLANICI RESÝMÝNÝN TÝP VE BÜYÜKLÜÐÜNE BAKILIYOR - BAÞI 	//



//	RESÝM SÝL SEÇÝLÝYSE $kul_resim DEÐÝÞKENÝ BOÞALTILIYOR	//
if ( isset($_POST['resim_sil']) ) $kul_resim = '';


//	RESÝM YÜKLEME KISMI - BAÞI	//

elseif ( ($ayarlar['resim_yukle'] == 1) AND (isset($_FILES['resim_yukle']['tmp_name']))
	AND ($_FILES['resim_yukle']['tmp_name'] != '') )
{
	list($genislik, $yukseklik, $tip) = @getimagesize($_FILES['resim_yukle']['tmp_name']);

	if ( (isset($tip)) AND ($tip == 2) )
	{
		$uzanti = '.jpg';

		if ( !@imagecreatefromjpeg($_FILES['resim_yukle']['tmp_name']) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	elseif ( (isset($tip)) AND ($tip == 1) )
	{
		$uzanti = '.gif';

		if ( !@imagecreatefromgif($_FILES['resim_yukle']['tmp_name']) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	elseif ( (isset($tip)) AND ($tip == 3) )
	{
		$uzanti = '.png';

		if ( !@imagecreatefrompng($_FILES['resim_yukle']['tmp_name']) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	else
	{
		header('Location: ../hata.php?hata=85');
		exit();
	}



	if ($_FILES['resim_yukle']['size'] > $ayarlar['resim_boyut'])
	{
		header('Location: ../hata.php?hata=86');
		exit();
	}

	if ( ($genislik > $ayarlar['resim_genislik']) OR ($yukseklik > $ayarlar['resim_yukseklik']) )
	{
		header('Location: ../hata.php?hata=87');
		exit();
	}

	$dosya_yolu = '../dosyalar/resimler/yuklenen/'.rand(1111111111, 9999999999).$uzanti;

	if ( !@move_uploaded_file($_FILES["resim_yukle"]["tmp_name"],$dosya_yolu) )
	{
		header('Location: ../hata.php?hata=88');
		exit();
	}

	//	DOSYA SORUNSUZSA ../ temizlenip ADRES $kul_resim DEÐÝÞKENÝNE AKTARILIYOR	//
	$kul_resim = str_replace('../','',$dosya_yolu);
}


//	RESÝM YÜKLEME KISMI - SONU	//




//	UZAK RESÝM YÜKLEME KISMI - BAÞI	//


elseif ( ($ayarlar['uzak_resim'] == 1) AND (isset($_POST['uzak_resim'])) AND ($_POST['uzak_resim'] != '') OR 
	($ayarlar['resim_galerisi'] == 1) AND (isset($_POST['uzak_resim2'])) AND ($_POST['uzak_resim2'] != '') )
{
	if ( (isset($_POST['uzak_resim'])) AND ($_POST['uzak_resim'] != '') )
	$resim_adres = $_POST['uzak_resim'];

	elseif ( (isset($_POST['uzak_resim2'])) AND ($_POST['uzak_resim2'] != '') )
	$resim_adres = '../'.$_POST['uzak_resim2'];

	if ( (!@getimagesize($resim_adres)) AND (preg_match('/^http:\/\//i', $resim_adres))
	OR (!@getimagesize($resim_adres)) AND (preg_match('/^ftp:\/\//i', $resim_adres)) )
	{
		header('Location: ../hata.php?hata=89');
		exit();
	}

	list($genislik, $yukseklik, $tip) = @getimagesize($resim_adres);

	if ( (isset($tip)) AND ($tip == 2) )
	{
		if ( !@imagecreatefromjpeg($resim_adres) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	elseif ( (isset($tip)) AND ($tip == 1) )
	{
		if ( !@imagecreatefromgif($resim_adres) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	elseif ( (isset($tip)) AND ($tip == 3) )
	{
		if ( !@imagecreatefrompng($resim_adres) )
		{
			header('Location: ../hata.php?hata=84');
			exit();
		}
	}

	else
	{
		header('Location: ../hata.php?hata=85');
		exit();
	}



	$resim_dosya = @file_get_contents($resim_adres);
	$resim_boyut = @round((strlen($resim_dosya)),2);

	if ($resim_boyut > $ayarlar['resim_boyut'])
	{
		header('Location: ../hata.php?hata=86');
		exit();
	}

	if ( ($genislik > $ayarlar['resim_genislik']) OR ($yukseklik > $ayarlar['resim_yukseklik']) )
	{
		header('Location: ../hata.php?hata=87');
		exit();
	}

	//	DOSYA SORUNSUZSA ../ temizlenip ADRES $kul_resim DEÐÝÞKENÝNE AKTARILIYOR	//
	$kul_resim = str_replace('../','',$resim_adres);
}

//	UZAK RESÝM YÜKLEME KISMI - SONU	//


//	HÝÇBÝR ÞEY YÜKLENMÝYORSA ESKÝ RESÝM ADRESÝ $kul_resim DEÐÝÞKENÝNE AKTARILIYOR	//

else $kul_resim = $satir['resim'];



		//	KULLANICI RESÝMÝNÝN TÝP VE BÜYÜKLÜÐÜNE BAKILIYOR - SONU 	//









if ( (!preg_match('/^[01]+$/', $_POST['posta_goster'])) OR
		(!preg_match('/^[01]+$/', $_POST['dogum_tarihi_goster'])) OR
		(!preg_match('/^[01]+$/', $_POST['sehir_goster'])) OR
		(!preg_match('/^[01]+$/', $_POST['gizli'])) )
{
	header('Location: ../hata.php?hata=92');
	exit();
}



// 	E-POSTA ADRESÝ DEÐÝÞTÝYSE YENÝ ADRESLE DAHA ÖNCE KAYIT YAPILIP YAPILMADIÐI DENETLENÝYOR 	//

$_POST['posta'] = zkTemizle($_POST['posta']);

if ($satir['posta'] != $_POST['posta'])
{
	$strSQL = "SELECT posta FROM $tablo_kullanicilar WHERE posta='$_POST[posta]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL);

	if (mysql_num_rows($sonuc2))
	{
		header('Location: ../hata.php?hata=93');
		exit();
	}
}

if (($_POST['ysifre'] == 'sifre_degismedi') or ($_POST['ysifre2'] == 'sifre_degismedi'))
{
	$karma = $satir['sifre'];
}

//	ÞÝFRE DEÐÝÞTÝRÝLDÝYSE ANAHTAR ÝLE BÝRLEÞTÝRÝLÝP SHA1 ÝLE ÞÝFRELENÝYOR	//

if (($_POST['ysifre'] != 'sifre_degismedi') or ($_POST['ysifre2'] != 'sifre_degismedi'))
{
	$karma=sha1(($anahtar.$_POST['ysifre']));
}


$_POST['web'] = zkTemizle($_POST['web']);
$_POST['icq'] = zkTemizle($_POST['icq']);
$_POST['aim'] = zkTemizle($_POST['aim']);
$_POST['msn'] = zkTemizle($_POST['msn']);
$_POST['yahoo'] = zkTemizle($_POST['yahoo']);
$_POST['skype'] = zkTemizle($_POST['skype']);
$_POST['ozel_ad'] = zkTemizle($_POST['ozel_ad']);


// portal tema dizini
if (isset($_POST['tema_secimp'])) $temadizinip_sorgu = ",temadizinip='$_POST[tema_secimp]'";
else $temadizinip_sorgu = '';


$strSQL = "UPDATE $tablo_kullanicilar SET sifre='$karma',posta='$_POST[posta]',web='$_POST[web]',dogum_tarihi='$_POST[dogum_tarihi]',sehir='$_POST[sehir]',gercek_ad='$_POST[gercek_ad]',resim='$kul_resim',imza='$_POST[imza]',posta_goster='$_POST[posta_goster]',dogum_tarihi_goster='$_POST[dogum_tarihi_goster]',sehir_goster='$_POST[sehir_goster]',yetki='$_POST[yetki]',gizli='$_POST[gizli]',icq='$_POST[icq]',aim='$_POST[aim]',msn='$_POST[msn]',yahoo='$_POST[yahoo]',skype='$_POST[skype]',temadizini='$_POST[tema_secim]',ozel_ad='$_POST[ozel_ad]',grupid='$_POST[grup]' $temadizinip_sorgu WHERE id='$satir[id]' LIMIT 1";

$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');



// eski ve yeni yetkisi bölüm yardýmcýsý ise bir þey yapma
if (($_POST['eski_yetki'] == '3') AND ($_POST['yetki'] == '3'));


else
{
	// yeni yetkisi kayýtlý kullanýcý ise sadece yönetme yetkisi olan özel izinlerini sil
	if ($_POST['yetki'] == '0')
	{
		$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE kulad='$satir[kullanici_adi]' AND yonetme='1'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}


	// yeni yetkisi yönetici veya forum yardýmcýsý ise tüm özel izinlerini sil
	elseif (($_POST['yetki'] == '1') OR ($_POST['yetki'] == '2'))
	{
		$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE kulad='$satir[kullanici_adi]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}
}





//   GRUP AYARLARI - BAÞI   //

if ($satir['grupid'] != $_POST['grup'])
{
	// seçilen grubun bilgileri çekiliyor
	$strSQL = "SELECT id,yetki,uyeler,ozel_ad FROM $tablo_gruplar WHERE id='$_POST[grup]' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$grupsatir = mysql_fetch_assoc($sonuc);


	// grup seçimi yapýlmýþsa
	if ($_POST['grup'] != '0')
	{
		// seçilen grubun yetkisi varsa üyeye uygulanýyor
		if ($grupsatir['yetki'] != '-1')
		{
			$strSQL = "UPDATE $tablo_kullanicilar SET yetki='$grupsatir[yetki]',ozel_ad='$grupsatir[ozel_ad]' WHERE id='$satir[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}

		// yoksa sadece özel ad deðiþtiriliyor
		else
		{
			$strSQL = "UPDATE $tablo_kullanicilar SET ozel_ad='$grupsatir[ozel_ad]' WHERE id='$satir[id]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}

		// daha önceden ek gruba eklenmemiþse
		if (!preg_match("/$satir[id],/", $grupsatir['uyeler']))
		{
			// üye gruba ekleniyor
			$strSQL = "UPDATE $tablo_gruplar SET uyeler=CONCAT(uyeler,'$satir[id],') WHERE id='$_POST[grup]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}
}

//   GRUP AYARLARI - SONU   //




//   EK GRUP SEÇÝMÝ - BAÞI   //

if ((isset($_POST['grup'])) AND ($_POST['grup'] != '0')) $sorguek = " AND id!='$_POST[grup]'";
else $sorguek = '';


// ek grup seçimi yapýlmýþsa
if ( (isset($_POST['grupc'])) AND (is_array($_POST['grupc'])) )
{
	// grup seçimleri sýralanýyor
	foreach ($_POST['grupc'] as $grupc)
	{
		// boþ ve geçersiz olanlar geçiliyor
		if (($grupc == '') OR (!preg_match('/^[0-9]+$/', $grupc))) continue;

		// birincil grup geçiliyor
		if ((isset($_POST['grup'])) AND ($_POST['grup'] != '0') AND ($_POST['grup'] == $grupc)) continue;

		// seçilen grubun bilgileri çekiliyor
		$strSQL = "SELECT id,uyeler FROM $tablo_gruplar WHERE id='$grupc' LIMIT 1";
		$sonuc = mysql_query($strSQL);
		$gruplar = mysql_fetch_assoc($sonuc);


		// seçilen gruba dahil deðilse ekleniyor
		if (!preg_match("/$satir[id],/", $gruplar['uyeler']))
		{
			$strSQL = "UPDATE $tablo_gruplar SET uyeler=CONCAT(uyeler,'$satir[id],') WHERE id='$grupc' LIMIT 1";
			$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}

		$sorguek .= " AND id!='$grupc'";
	}
}


// seçili olmayan gruplardaki üyeliklerine bakýlýyor
$strSQL2 = "SELECT id,grup_adi,uyeler FROM $tablo_gruplar WHERE uyeler LIKE '%$satir[id],%' $sorguek ORDER BY id";
$sonucg = mysql_query($strSQL2) or die ('<h2>sorgu baþarýsýz</h2>');

// gruplar sýralanýyor
while ($gruplar = mysql_fetch_assoc($sonucg))
{
	$cikart = str_replace("$satir[id],", '', $gruplar['uyeler']);

	// seçili olmayan gruplardaki üyelikleri siliniyor
	$strSQL = "UPDATE $tablo_gruplar SET uyeler='$cikart' WHERE id='$gruplar[id]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
}

	//   EK GRUP SEÇÝMÝ - SONU   //



	header('Location: ../hata.php?bilgi=32');
	exit();

endif;
?>