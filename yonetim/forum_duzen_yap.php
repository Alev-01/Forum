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


if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


//	ZARARLI KODLAR TEM�ZLEN�YOR	//

if (isset($_POST['kip'])) $kip = $_POST['kip'];
elseif (isset($_GET['kip'])) $kip = $_GET['kip'];
else $kip = '';


if (isset($_POST['resim'])) $_POST['resim'] = @zkTemizle($_POST['resim']);
if (isset($_POST['fno'])) $_POST['fno'] = @zkTemizle($_POST['fno']);
if (isset($_POST['dalno'])) $_POST['dalno'] = @zkTemizle($_POST['dalno']);
if (isset($_GET['dalno'])) $_GET['dalno'] = @zkTemizle($_GET['dalno']);
if (isset($_GET['sira'])) $_GET['sira'] = @zkTemizle($_GET['sira']);


// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];



//	DALI D�ZENLEME	//

if ($kip == 'dal_duzenle')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	//	magic_quotes_gpc a��ksa	//
	if (get_magic_quotes_gpc(1))
		$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));

	//	magic_quotes_gpc kapal�ysa	//
	else $_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);


	$strSQL = "UPDATE $tablo_dallar SET ana_forum_baslik='$_POST[forum_baslik]' WHERE id='$_POST[dalno]' LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	header('Location: forumlar.php');
	exit();
}




//	FORUM D�ZENLEME	//

elseif ($kip == 'forum_duzenle')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// forum se�ilmemi�se uyar� ver
	if ( (!isset($_POST['alt_forum'])) OR ($_POST['alt_forum'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['alt_forum'] = @zkTemizle($_POST['alt_forum']);


	//	magic_quotes_gpc a��ksa	//
	if (get_magic_quotes_gpc(1))
	{
		$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
		$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
    }

	//	magic_quotes_gpc kapal�ysa	//
    else
	{
		$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
		$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
    }


	//	d�zenlenen forumun �st - alt forum durumuna bak�l�yor //

	$strSQL = "SELECT dal_no,sira,alt_forum FROM $tablo_forumlar WHERE id='$_POST[fno]' LIMIT 1";
	$durum_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$durum = mysql_fetch_assoc($durum_sonuc);




	//	�ST FORUM YAPARAK D�ZENLE	//

	if ($_POST['alt_forum'] == 'ust')
	{
		// alt forum ise, �st forum yaparak d�zenle
		if ($durum['alt_forum'] != '0')
		{
			// forum dal�n�n en alt�ndaki �st forumun s�ra numaras� al�n�yor
			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$durum[dal_no]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
			$enalt['sira']++;

			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', sira='$enalt[sira]', alt_forum='0' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// �st forum yap�larak d�zenlenen forumun alt�ndaki alt forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL966 = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$durum[alt_forum]' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL966) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL977 = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL977) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}


		// zaten �st forum, sadece d�zenle
		else
		{
			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}



	//	ALT FORUM YAPARAK D�ZENLE	//

	else
	{
		// �st forum ise, alt forum yaparak d�zenle
		if ($durum['alt_forum'] == '0')
		{
			// se�ilen �st forumun dal numaras� al�n�yor

			$strSQL = "SELECT dal_no FROM $tablo_forumlar WHERE id='$_POST[alt_forum]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$dal_no = mysql_fetch_assoc($sonuc);


			// se�ilen �st forumun en alt s�radaki alt forumunun sira numaras� al�n�yor

			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
		
			if (!isset($enalt['sira'])) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', dal_no='$dal_no[dal_no]', sira='$enalt[sira]', alt_forum='$_POST[alt_forum]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// alt forum yap�lan forumun geldi�i daldaki alt�nda kalan �st forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE dal_no='$durum[dal_no]' AND alt_forum='0' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}




		// farkl� bir �st forumda alt forum ise, alt forum yaparak d�zenle
		elseif ($durum['alt_forum'] != $_POST['alt_forum'])
		{
			// se�ilen �st forumun dal numaras� al�n�yor

			$strSQL = "SELECT dal_no FROM $tablo_forumlar WHERE id='$_POST[alt_forum]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$dal_no = mysql_fetch_assoc($sonuc);


			// se�ilen �st forumun en alt s�radaki alt forumunun sira numaras� al�n�yor

			$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			$enalt = mysql_fetch_assoc($sonuc);
		
			if (!isset($enalt['sira'])) $enalt['sira'] = 1;
			else $enalt['sira']++;


			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]', dal_no='$dal_no[dal_no]', sira='$enalt[sira]', alt_forum='$_POST[alt_forum]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			// ba�ka bir �st forumun alt�na al�nan alt forumun,
			// geldi�i yerdeki alt�nda kalan alt forumlar�n s�ra say�lar� de�i�tiriliyor
			$strSQL = "SELECT id FROM $tablo_forumlar
					WHERE alt_forum='$durum[alt_forum]' AND sira > '$durum[sira]'";
			$sonuc_sira = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


			while ($forum_sira = mysql_fetch_assoc($sonuc_sira))
			{
				$strSQL = "UPDATE $tablo_forumlar SET sira=sira - 1 WHERE id='$forum_sira[id]' LIMIT 1";
				$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
			}
		}




		// ayn� �st forumun alt forumu ise, sadece d�zenle
		else
		{
			$strSQL = "UPDATE $tablo_forumlar SET forum_baslik='$_POST[forum_baslik]', forum_bilgi='$_POST[forum_bilgi]', resim='$_POST[resim]' WHERE id='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}


	header('Location: forumlar.php');
	exit();
}



//	DAL YUKARI TA�IMA	//

elseif ($kip == 'dal_yukari')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	//	DAL ZATEN EN BA�TA �SE, YAN� sira=1 �SE B�R �EY YAPMA	//
	if ($_GET['sira'] != '1')
	{
		$asagi_sira = ($_GET['sira'] - 1);

		$strSQL = "UPDATE $tablo_dallar SET sira='0' WHERE sira='$asagi_sira' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$asagi_sira' WHERE sira='$_GET[sira]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$_GET[sira]' WHERE sira='0' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}
	header('Location: forumlar.php');
	exit();
}



//	DAL A�A�I TA�IMA	//

elseif ($kip == 'dal_asagi')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	$strSQL = "SELECT sira FROM $tablo_dallar ORDER BY sira DESC LIMIT 1";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	$enalt = mysql_fetch_assoc($sonuc);


	//	DAL ZATEN EN ALTTA �SE B�R �EY YAPMA	//

	if ($enalt['sira'] > $_GET['sira'])
	{
		$yukari_sira = ($_GET['sira'] + 1);

		$strSQL = "UPDATE $tablo_dallar SET sira='0' WHERE sira='$yukari_sira' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$yukari_sira' WHERE sira='$_GET[sira]' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_dallar SET sira='$_GET[sira]' WHERE sira='0' LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}
	header('Location: forumlar.php');
	exit();
}




//	FORUM YUKARI TA�IMA	//

elseif ($kip == 'forum_yukari')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// �ST FORUM ��LEM� //

	if ( (isset($_GET['ustforum'])) AND ($_GET['ustforum'] == '1') )
	{
		//	FORUM ZATEN EN BA�TA �SE, YAN� sira=1 �SE B�R �EY YAPMA	//

		if ($_GET['sira'] != '1')
		{
			$asagi_sira = ($_GET['sira'] - 1);

			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='$asagi_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z1</h2>'.$strSQL);

			$strSQL = "UPDATE $tablo_forumlar SET sira='$asagi_sira' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z2</h2>'.$strSQL);

			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' AND sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z3</h2>'.$strSQL);
		}
	}


	// ALT FORUM ��LEM� //

	elseif ( (isset($_GET['altforum'])) AND ($_GET['altforum'] != '') )
	{
		//	FORUM ZATEN EN BA�TA �SE, YAN� sira=1 �SE B�R �EY YAPMA	//

		if ($_GET['sira'] != '1')
		{
			$asagi_sira = ($_GET['sira'] - 1);

			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' AND sira='$asagi_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			$strSQL = "UPDATE $tablo_forumlar SET sira='$asagi_sira' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}


	header('Location: forumlar.php');
	exit();
}




//	FORUM A�A�I TA�IMA	//

elseif ($kip == 'forum_asagi')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	// �ST FORUM ��LEM� //

	if ( (isset($_GET['ustforum'])) AND ($_GET['ustforum'] == '1') )
	{
		// en alt s�radaki �st forumun s�ra numaras� al�n�yor

		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_GET[dalno]' AND alt_forum='0' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
		$enalt = mysql_fetch_array($sonuc);


		//	FORUM ZATEN EN ALTTA �SE B�R �EY YAPMA	//

		if ($enalt['sira'] > $_GET['sira'])
		{
			$yukari_sira = ($_GET['sira'] + 1);
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='$yukari_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$yukari_sira' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='0' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}


	// ALT FORUM ��LEM� //

	elseif ( (isset($_GET['altforum'])) AND ($_GET['altforum'] != '') )
	{
		// en alt s�radaki alt forumun s�ra numaras� al�n�yor

		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_GET[dalno]' AND alt_forum='$_GET[altforum]' ORDER BY sira DESC LIMIT 1";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
		$enalt = mysql_fetch_array($sonuc);


		//	FORUM ZATEN EN ALTTA �SE B�R �EY YAPMA	//

		if ($enalt['sira'] > $_GET['sira'])
		{
			$yukari_sira = ($_GET['sira'] + 1);
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='0' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$yukari_sira' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$yukari_sira' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='$_GET[sira]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
			$strSQL = "UPDATE $tablo_forumlar SET sira='$_GET[sira]' WHERE alt_forum='$_GET[altforum]' AND dal_no='$_GET[dalno]' and sira='0' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}

	header('Location: forumlar.php');
	exit();
}




//	YEN� DALI OLU�TUR	//

elseif ($kip == 'yeni_dal')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
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

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	$sira = mysql_fetch_assoc($sonuc);
	if (isset($sira['sira'])) $sira['sira']++;
	else  $sira['sira'] = 1;

	//	magic_quotes_gpc a��ksa	//
	if (get_magic_quotes_gpc(1))
		$_POST['ana_forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['ana_forum_baslik']));

	//	magic_quotes_gpc kapal�ysa	//
	else $_POST['ana_forum_baslik'] = @mysql_real_escape_string($_POST['ana_forum_baslik']);


	$strSQL = "INSERT INTO $tablo_dallar (ana_forum_baslik,sira)
				VALUES ('$_POST[ana_forum_baslik]', '$sira[sira]')";

	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: forumlar.php');
	exit();
}




//	YEN� �ST VEYA ALT FORUM OLU�TUR	//

elseif ($kip == 'yeni_forum')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	// forum se�ilmemi�se uyar� ver
	if ( (!isset($_POST['alt_forum'])) OR ($_POST['alt_forum'] == '') )
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['alt_forum'] = @zkTemizle($_POST['alt_forum']);


	// ba�l�k girilmemi�se uyar� ver
	if ($_POST['forum_baslik'] == '')
	{
		header('Location: ../hata.php?hata=141');
		exit();
	}


	//	YEN� �ST FORUM OLU�TUR	//

	if ($_POST['alt_forum'] == 'ust')
	{
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE dal_no='$_POST[dalno]' ORDER BY sira DESC LIMIT 1";
	
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
		$sira = mysql_fetch_assoc($sonuc);
		if (isset($sira['sira'])) $sira['sira']++;
		else  $sira['sira'] = 1;

		//	magic_quotes_gpc a��ksa	//
		if (get_magic_quotes_gpc(1))
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
			$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
		}

		//	magic_quotes_gpc kapal�ysa	//
		else
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
			$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
		}
    

		$strSQL = "INSERT INTO $tablo_forumlar (dal_no, forum_baslik, forum_bilgi, sira)
		VALUES ('$_POST[dalno]','$_POST[forum_baslik]','$_POST[forum_bilgi]', '$sira[sira]')";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}


	//	YEN� ALT FORUM OLU�TUR	//

	else
	{
		$strSQL = "SELECT sira FROM $tablo_forumlar WHERE alt_forum='$_POST[alt_forum]' ORDER BY sira DESC LIMIT 1";
	
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	
		$sira = mysql_fetch_assoc($sonuc);
		if (isset($sira['sira'])) $sira['sira']++;
		else  $sira['sira'] = 1;

		//	magic_quotes_gpc a��ksa	//
		if (get_magic_quotes_gpc(1))
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string(stripslashes($_POST['forum_baslik']));
			$_POST['forum_bilgi'] = @mysql_real_escape_string(stripslashes($_POST['forum_bilgi']));
		}

		//	magic_quotes_gpc kapal�ysa	//
		else
		{
			$_POST['forum_baslik'] = @mysql_real_escape_string($_POST['forum_baslik']);
			$_POST['forum_bilgi'] = @mysql_real_escape_string($_POST['forum_bilgi']);
		}
    

		$strSQL = "INSERT INTO $tablo_forumlar (dal_no, forum_baslik, forum_bilgi, sira, alt_forum)
		VALUES ('$_POST[dalno]','$_POST[forum_baslik]','$_POST[forum_bilgi]', '$sira[sira]', '$_POST[alt_forum]')";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}



	header('Location: forumlar.php');
	exit();
}
?>