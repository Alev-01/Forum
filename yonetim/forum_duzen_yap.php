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


//	ZARARLI KODLAR TEMÝZLENÝYOR	//

if (isset($_POST['kip'])) $kip = $_POST['kip'];
elseif (isset($_GET['kip'])) $kip = $_GET['kip'];
else $kip = '';


if (isset($_POST['resim'])) $_POST['resim'] = @zkTemizle($_POST['resim']);
if (isset($_POST['fno'])) $_POST['fno'] = @zkTemizle($_POST['fno']);
if (isset($_POST['dalno'])) $_POST['dalno'] = @zkTemizle($_POST['dalno']);
if (isset($_GET['dalno'])) $_GET['dalno'] = @zkTemizle($_GET['dalno']);
if (isset($_GET['sira'])) $_GET['sira'] = @zkTemizle($_GET['sira']);


// OTURUM KODU ÝÞLEMLERÝ  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];



//	DALI DÜZENLEME	//

if ($kip == 'dal_duzenle')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
		$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));

	//	magic_quotes_gpc kapalýysa	//
	else $_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);


	$strSQL = "UPDATE $tablo_dallar SET ana_forum_baslik='$_POST[forum_baslik]' WHERE id='$_POST[dalno]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	header('Location: forumlar.php');
	exit();
}




//	FORUM DÜZENLEME	//

elseif ($kip == 'forum_duzenle')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// forum seçilmemiþse uyarý ver
	if ( (!isset($_POST['alt_forum'])) OR ($_POST['alt_forum'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['alt_forum'] = @zkTemizle($_POST['alt_forum']);


	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
		$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
    }

	//	magic_quotes_gpc kapalýysa	//
    else
	{
		$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
		$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
    }


	//	düzenlenen forumun üst - alt forum durumuna bakýlýyor //

	$strSQL = "SELECT dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
	$durum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$durum = mysql_fetch_assoc($durum_sonuc);




	//	ÜST FORUM YAPARAK DÜZENLE	//

	if ($_POST['alt_forum'] == 'ust')
	{
		// alt forum ise, üst forum yaparak düzenle
		if ($durum['alt_forum'] != '0')
		{
			// forum dalýnýn en altýndaki üst forumun sýra numarasý alýnýyor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$durum[dal_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
			$enalt['sira']++;

			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', sira='$enalt[sira]', alt_forum='0' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// üst forum yapýlarak düzenlenen forumun altýndaki alt forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL966 = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$durum[alt_forum]' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL966) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL977 = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL977) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}


		// zaten üst forum, sadece düzenle
		else
		{
			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}



	//	ALT FORUM YAPARAK DÜZENLE	//

	else
	{
		// üst forum ise, alt forum yaparak düzenle
		if ($durum['alt_forum'] == '0')
		{
			// seçilen üst forumun dal numarasý alýnýyor

			$strSQL = "SELECT dal_no FROM $tablo_forumlar WHERE id='$_POST[alt_forum]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$dal_no = mysql_fetch_assoc($sonuc);


			// seçilen üst forumun en alt sýradaki alt forumunun sira numarasý alýnýyor

			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
		
			if (!isset($enalt['sira'])) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', dal_no='$dal_no[dal_no]', sira='$enalt[sira]', alt_forum='$_POST[alt_forum]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// alt forum yapýlan forumun geldiði daldaki altýnda kalan üst forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$durum[dal_no]' AND alt_forum='0' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}




		// farklý bir üst forumda alt forum ise, alt forum yaparak düzenle
		elseif ($durum['alt_forum'] != $_POST['alt_forum'])
		{
			// seçilen üst forumun dal numarasý alýnýyor

			$strSQL = "SELECT dal_no FROM $tablo_forumlar WHERE id='$_POST[alt_forum]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$dal_no = mysql_fetch_assoc($sonuc);


			// seçilen üst forumun en alt sýradaki alt forumunun sira numarasý alýnýyor

			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
		
			if (!isset($enalt['sira'])) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', dal_no='$dal_no[dal_no]', sira='$enalt[sira]', alt_forum='$_POST[alt_forum]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			// baþka bir üst forumun altýna alýnan alt forumun,
			// geldiði yerdeki altýnda kalan alt forumlarýn sýra sayýlarý deðiþtiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$durum[alt_forum]' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
			}
		}




		// ayný üst forumun alt forumu ise, sadece düzenle
		else
		{
			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}


	header('Location: forumlar.php');
	exit();
}



//	DAL YUKARI TAÞIMA	//

elseif ($kip == 'dal_yukari')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	//	DAL ZATEN EN BAÞTA ÝSE, YANÝ sira=1 ÝSE BÝR ÞEY YAPMA	//
	if ($_GET['sira'] != '1')
	{
		$asagi_sira = ($_GET['sira'] - 1);

		$strSQL = "UPDATE $tablo_dallar SET sira='0' WHERE sira='$asagi_sira' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$asagi_sira' WHERE sira='$_GET[sira]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$_GET[sira]' WHERE sira='0' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}
	header('Location: forumlar.php');
	exit();
}



//	DAL AÞAÐI TAÞIMA	//

elseif ($kip == 'dal_asagi')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	$strSQL = "SELECT sira FROM $tablo_dallar ORDER BY sira DESC LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	$enalt = mysql_fetch_assoc($sonuc);


	//	DAL ZATEN EN ALTTA ÝSE BÝR ÞEY YAPMA	//

	if ($enalt['sira'] > $_GET['sira'])
	{
		$yukari_sira = ($_GET['sira'] + 1);

		$strSQL = "UPDATE $tablo_dallar SET sira='0' WHERE sira='$yukari_sira' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$yukari_sira' WHERE sira='$_GET[sira]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$_GET[sira]' WHERE sira='0' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}
	header('Location: forumlar.php');
	exit();
}




//	FORUM YUKARI TAÞIMA	//

elseif ($kip == 'forum_yukari')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// ÜST FORUM ÝÞLEMÝ //

	if ( (isset($_GET['ustforum'])) AND ($_GET['ustforum'] == '1') )
	{
		//	FORUM ZATEN EN BAÞTA ÝSE, YANÝ sira=1 ÝSE BÝR ÞEY YAPMA	//

		if ($_GET['sira'] != '1')
		{
			$asagi_sira = ($_GET['sira'] - 1);

			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='$asagi_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz1</h2>'.$strSQL);

			$strSQL = "UPDATE $tablo_forumlar SET sira='$asagi_sira' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz2</h2>'.$strSQL);

			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz3</h2>'.$strSQL);
		}
	}


	// ALT FORUM ÝÞLEMÝ //

	elseif ( (isset($_GET['altforum'])) AND ($_GET['altforum'] != '') )
	{
		//	FORUM ZATEN EN BAÞTA ÝSE, YANÝ sira=1 ÝSE BÝR ÞEY YAPMA	//

		if ($_GET['sira'] != '1')
		{
			$asagi_sira = ($_GET['sira'] - 1);

			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' AND sira='$asagi_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			$strSQL = "UPDATE $tablo_forumlar SET sira='$asagi_sira' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}


	header('Location: forumlar.php');
	exit();
}




//	FORUM AÞAÐI TAÞIMA	//

elseif ($kip == 'forum_asagi')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// ÜST FORUM ÝÞLEMÝ //

	if ( (isset($_GET['ustforum'])) AND ($_GET['ustforum'] == '1') )
	{
		// en alt sýradaki üst forumun sýra numarasý alýnýyor

		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_GET[dalno]' AND alt_forum='0' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
		$enalt = mysql_fetch_array($sonuc);


		//	FORUM ZATEN EN ALTTA ÝSE BÝR ÞEY YAPMA	//

		if ($enalt['sira'] > $_GET['sira'])
		{
			$yukari_sira = ($_GET['sira'] + 1);
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='$yukari_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$yukari_sira' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}


	// ALT FORUM ÝÞLEMÝ //

	elseif ( (isset($_GET['altforum'])) AND ($_GET['altforum'] != '') )
	{
		// en alt sýradaki alt forumun sýra numarasý alýnýyor

		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_GET[dalno]' AND alt_forum='$_GET[altforum]' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
		$enalt = mysql_fetch_array($sonuc);


		//	FORUM ZATEN EN ALTTA ÝSE BÝR ÞEY YAPMA	//

		if ($enalt['sira'] > $_GET['sira'])
		{
			$yukari_sira = ($_GET['sira'] + 1);
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$yukari_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$yukari_sira' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}

	header('Location: forumlar.php');
	exit();
}




//	YENÝ DALI OLUÞTUR	//

elseif ($kip == 'yeni_dal')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	if ($_POST['ana_forum_baslik'] == '')
	{
		header('Location: ../hata.php?hata=140');
		exit();
	}

	$strSQL = "SELECT sira FROM $tablo_dallar ORDER BY sira DESC LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	$sira = mysql_fetch_assoc($sonuc);
	if (isset($sira['sira'])) $sira['sira']++;
	else  $sira['sira'] = 1;

	//	magic_quotes_gpc açýksa	//
	if (get_magic_quotes_gpc(1))
		$_POST['ana_forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['ana_forum_baslik']));

	//	magic_quotes_gpc kapalýysa	//
	else $_POST['ana_forum_baslik'] = @mysql_real_escape_string($_POST['ana_forum_baslik']);


	$strSQL = "INSERT INTO $tablo_dallar (ana_forum_baslik,sira)
				VALUES ('$_POST[ana_forum_baslik]', '$sira[sira]')";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

	header('Location: forumlar.php');
	exit();
}




//	YENÝ ÜST VEYA ALT FORUM OLUÞTUR	//

elseif ($kip == 'yeni_forum')
{
	//  OTURUM BÝLGÝSÝNE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	// forum seçilmemiþse uyarý ver
	if ( (!isset($_POST['alt_forum'])) OR ($_POST['alt_forum'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['alt_forum'] = @zkTemizle($_POST['alt_forum']);


	// baþlýk girilmemiþse uyarý ver
	if ($_POST['forum_baslik'] == '')
	{
		header('Location: ../hata.php?hata=141');
		exit();
	}


	//	YENÝ ÜST FORUM OLUÞTUR	//

	if ($_POST['alt_forum'] == 'ust')
	{
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalno]' ORDER BY sira DESC LIMIT 1";
	
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
		$sira = mysql_fetch_assoc($sonuc);
		if (isset($sira['sira'])) $sira['sira']++;
		else  $sira['sira'] = 1;

		//	magic_quotes_gpc açýksa	//
		if (get_magic_quotes_gpc(1))
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
			$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
		}

		//	magic_quotes_gpc kapalýysa	//
		else
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
			$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
		}
    

		$strSQL = "INSERT INTO $tablo_forumlar (dal_no, forum_baslik, forum_bilgi, sira)
		VALUES ('$_POST[dalno]','$_POST[forum_baslik]','$_POST[forum_bilgi]', '$sira[sira]')";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}


	//	YENÝ ALT FORUM OLUÞTUR	//

	else
	{
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
	
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	
		$sira = mysql_fetch_assoc($sonuc);
		if (isset($sira['sira'])) $sira['sira']++;
		else  $sira['sira'] = 1;

		//	magic_quotes_gpc açýksa	//
		if (get_magic_quotes_gpc(1))
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
			$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
		}

		//	magic_quotes_gpc kapalýysa	//
		else
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
			$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
		}
    

		$strSQL = "INSERT INTO $tablo_forumlar (dal_no, forum_baslik, forum_bilgi, sira, alt_forum)
		VALUES ('$_POST[dalno]','$_POST[forum_baslik]','$_POST[forum_bilgi]', '$sira[sira]', '$_POST[alt_forum]')";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}



	header('Location: forumlar.php');
	exit();
}
?>