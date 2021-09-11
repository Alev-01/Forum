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


//   �YE ��LEMLER�   //

if ((isset($_GET['kim'])) AND ($_GET['kim'] != ''))
{
	// �ye bilgileri veritaban�ndan �ekiliyor

	$_GET['kim'] = zkTemizle(trim($_GET['kim']));

	$strSQL = "SELECT id,kullanici_adi,yetki,grupid FROM $tablo_kullanicilar
			WHERE kullanici_adi='$_GET[kim]' AND engelle='0' AND kul_etkin='1' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$kullanici_satir = mysql_fetch_array($sonuc);

	if (empty($kullanici_satir))
	{
		header('Location: ../hata.php?hata=46');
		exit();
	}


	// Se�ilen �ye y�netici ise uyar� ver

	if ($kullanici_satir['yetki'] == 1)
	{
		header('Location: ../hata.php?uyari=3');
		exit();
	}

	if ($kullanici_satir['yetki'] == 2)
	{
		header('Location: ../hata.php?uyari=4');
		exit();
	}

	$sayfa_adi = 'Y�netim �ye Yetkileri: '.$_GET['kim'];
	$tablo_baslik = '- Forum Se�imi -';
}



//   GRUP ��LEMLER�   //

elseif ((isset($_GET['grup'])) AND ($_GET['grup'] != ''))
{
	// Grup bilgileri veritaban�ndan �ekiliyor

	$_GET['grup'] = zkTemizle(trim($_GET['grup']));

	$strSQL = "SELECT * FROM $tablo_gruplar WHERE id='$_GET[grup]' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$grup_satir = mysql_fetch_array($sonuc);

	if (empty($grup_satir))
	{
		header('Location: ../hata.php?hata=204');
		exit();
	}


	$sayfa_adi = 'Y�netim Grup Yetkileri: '.$grup_satir['grup_adi'];
	$tablo_baslik = '- Forum Se�imi -';
}



else
{
	$sayfa_adi = 'Y�netim �ye ve Grup Yetkileri';
	$tablo_baslik = '- �ye Se�imi -';
}





// �YE �Z�N B�LG�LER� DE���T�R�L�YOR //

if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'uye'))
{
	if (isset($_POST['okuma'])) $_POST['okuma'] = zkTemizle($_POST['okuma']);
	if (isset($_POST['yazma'])) $_POST['yazma'] = zkTemizle($_POST['yazma']);
	if (isset($_POST['yonetme'])) $_POST['yonetme'] = zkTemizle($_POST['yonetme']);
	if (isset($_POST['konu_acma'])) $_POST['konu_acma'] = zkTemizle($_POST['konu_acma']);
	if (isset($_POST['fno'])) $_POST['fno'] = zkTemizle($_POST['fno']);


	// �ZEL �Z�N B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]'";
	$VARMI = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	//	DAHA �NCEDEN BU FORUM ���N YETK�LEND�R�L-MEM��SE INSERT	//

	if (!mysql_num_rows($VARMI))
	{
		// SADECE YETK� VER�LM��SE G�R	//
		if ( (isset($_POST['okuma'])) AND ($_POST['okuma'] == '1') OR (isset($_POST['yazma'])) AND ($_POST['yazma'] == '1')
		OR (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') OR (isset($_POST['konu_acma'])) AND ($_POST['konu_acma'] == '1') )
		{
			//	SADECE Y�NET�M VER�LM�� �SE D��ERLER� DE VER�L�YOR	//
			//	KULLANICI YETK� DERECES� 3 YAPILIYOR	//

			if ($_POST['yonetme'] == '1')
			{
				$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE id='$kullanici_satir[id]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

				$_POST['okuma'] = 1;
				$_POST['yazma'] = 1;
				$_POST['konu_acma'] = 1;
			}

			$strSQL = "INSERT INTO $tablo_ozel_izinler (kulad,kulid,grup,fno,okuma,yazma,yonetme,konu_acma)";
			$strSQL .= "VALUES ('$kullanici_satir[kullanici_adi]','$kullanici_satir[id]','0','$_POST[fno]','$_POST[okuma]','$_POST[yazma]','$_POST[yonetme]','$_POST[konu_acma]')";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}


	//	DAHA �NCEDEN BU FORUM ���N YETK�LEND�R�LM��SE UPDATE	//

	else
	{
		//	YETK�S� GER� ALINIYORSA VER�TABANINDAN S�L	//
		if (($_POST['okuma'] == '0') AND ($_POST['yazma'] == '0') AND ($_POST['konu_acma'] == '0') AND ($_POST['yonetme'] == '0'))
		{
			$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			// kullan�c�n�n ba�ka yard�mc�l��� varm� bak�l�yor
			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND yonetme='1' LIMIT 1";
			$yardimcilik = mysql_query($strSQL);

			if (!mysql_num_rows($yardimcilik))
			{
				if ($kullanici_satir['grupid'] != '0')
				{
					// kullan�c�n�n �ye oldu�u grubun yard�mc�l��� varm� bak�l�yor
					$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$kullanici_satir[grupid]' AND yonetme='1' LIMIT 1";
					$yardimcilik2 = mysql_query($strSQL);
				}

				// hi�bir yard�mc�l��� yoksa yetkisi normale d���r�l�yor
				if (!isset($yardimcilik2['fno']))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$kullanici_satir[id]' LIMIT 1";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
				}
			}
		}

		else
		{
			//	SADECE Y�NET�M VER�LM�� �SE D��ERLER� DE VER�L�YOR	//
			//	KULLANICI YETK� DERECES� 3 YAPILIYOR	//

			if ($_POST['yonetme'] == '1')
			{
				$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE id='$kullanici_satir[id]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

				$_POST['okuma'] = 1;
				$_POST['yazma'] = 1;
				$_POST['konu_acma'] = 1;
			}

			$strSQL = "UPDATE $tablo_ozel_izinler SET okuma='$_POST[okuma]', yazma='$_POST[yazma]', yonetme='$_POST[yonetme]', konu_acma='$_POST[konu_acma]'
			WHERE kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			//	Y�NET�M VER�LMEM��SE, BA�KA YARDIMCILI�I DA YOKSA...//
			//	KULLANICI YETK�S� NORMALE D���R�L�YOR	//

			if ($_POST['yonetme'] == '0')
			{
				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE kulad='$kullanici_satir[kullanici_adi]' AND yonetme='1'";
				$yardimcilik = mysql_query($strSQL);

				if (!mysql_num_rows($yardimcilik))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$kullanici_satir[id]' LIMIT 1";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
				}
			}
		}
	}
}






// GRUP �Z�N B�LG�LER� DE���T�R�L�YOR //

if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'grup'))
{
	if (isset($_POST['okuma'])) $_POST['okuma'] = zkTemizle($_POST['okuma']);
	if (isset($_POST['yazma'])) $_POST['yazma'] = zkTemizle($_POST['yazma']);
	if (isset($_POST['yonetme'])) $_POST['yonetme'] = zkTemizle($_POST['yonetme']);
	if (isset($_POST['konu_acma'])) $_POST['konu_acma'] = zkTemizle($_POST['konu_acma']);
	if (isset($_POST['fno'])) $_POST['fno'] = zkTemizle($_POST['fno']);


	//	SADECE Y�NET�M VER�LM�� �SE D��ERLER� DE VER�L�YOR	//
	//	KULLANICI YETK� DERECES� 3 YAPILIYOR	//

	if ( (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') )
	{
		$_POST['okuma'] = 1;
		$_POST['yazma'] = 1;
		$_POST['konu_acma'] = 1;

		$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE grupid='$grup_satir[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

		$strSQL = "UPDATE $tablo_gruplar SET yetki='3' WHERE id='$grup_satir[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	}


	// �ZEL �Z�N B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
	$VARMI = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


	//	DAHA �NCEDEN BU FORUM ���N YETK�LEND�R�L-MEM��SE INSERT	//

	if (!mysql_num_rows($VARMI))
	{
		// SADECE YETK� VER�LM��SE G�R	//
		if ( (isset($_POST['okuma'])) AND ($_POST['okuma'] == '1') OR (isset($_POST['yazma'])) AND ($_POST['yazma'] == '1')
		OR (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') OR (isset($_POST['konu_acma'])) AND ($_POST['konu_acma'] == '1') )
		{
			$strSQL = "INSERT INTO $tablo_ozel_izinler (kulad,kulid,grup,fno,okuma,yazma,yonetme,konu_acma)";
			$strSQL .= "VALUES ('$grup_satir[grup_adi]', '0', '$grup_satir[id]', '$_POST[fno]','$_POST[okuma]','$_POST[yazma]','$_POST[yonetme]','$_POST[konu_acma]')";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
		}
	}


	//	DAHA �NCEDEN BU FORUM ���N YETK�LEND�R�LM��SE UPDATE	//

	else
	{
		//	YETK�S� GER� ALINIYORSA VER�TABANINDAN S�L	//
		if (($_POST['okuma'] == '0') AND ($_POST['yazma'] == '0') AND ($_POST['konu_acma'] == '0') AND ($_POST['yonetme'] == '0'))
		{
			$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			// GRUBUN BA�KA Y�NET�C�L��� YOKSA, KULLANICI YETK�S� NORMALE D���R�L�YOR	//
			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND yonetme='1'";
			$yardimcilik = mysql_query($strSQL);

			if (!mysql_num_rows($yardimcilik))
			{
				$strSQL = "UPDATE $tablo_gruplar SET yetki='-1' WHERE id='$grup_satir[id]'";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


				// ayr�ca grup �yelerinin ba�ka yard�mc�l��� olup olmad���na bak�l�yor
				$strSQL = "SELECT id FROM $tablo_kullanicilar WHERE grupid='$grup_satir[id]'";
				$sonucguye = mysql_query($strSQL);

				while ($guye = mysql_fetch_assoc($sonucguye))
				{
					$strSQL = "SELECT kulid FROM $tablo_ozel_izinler WHERE kulid='$guye[id]' AND yonetme='1'";
					$yardimcilik = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

					// ba�ka yard�mc�l��� yoksa yetkisi normale d���r�l�yor
					if (!mysql_num_rows($yardimcilik))
					{
						$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$guye[id]' LIMIT 1";
						$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
					}
				}
			}
		}

		else
		{
			$strSQL = "UPDATE $tablo_ozel_izinler SET okuma='$_POST[okuma]', yazma='$_POST[yazma]', yonetme='$_POST[yonetme]', konu_acma='$_POST[konu_acma]'
			WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

			//	Y�NET�M VER�LMEM��SE, BA�KA YARDIMCILI�I DA YOKSA...//
			//	KULLANICI YETK�S� NORMALE D���R�L�YOR	//

			if ($_POST['yonetme'] == '0')
			{
				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND yonetme='1'";
				$yardimcilik = mysql_query($strSQL);

				if (!mysql_num_rows($yardimcilik))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE grupid='$grup_satir[id]'";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

					$strSQL = "UPDATE $tablo_gruplar SET yetki='-1' WHERE id='$grup_satir[id]'";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
				}
			}
		}
	}
}








//	�Z�NLER� G�STER TIKLANI�SA	//

if ((isset($_POST['izingoster'])) AND ($_POST['izingoster'] != ''))
{
	if ((!isset($_POST['forum_sec'])) OR ($_POST['forum_sec'] == ''))
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['forum_sec'] = zkTemizle($_POST['forum_sec']);


	// FORUM �Z�N B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT id,forum_baslik,okuma_izni,yazma_izni,konu_acma_izni FROM $tablo_forumlar WHERE id='$_POST[forum_sec]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$forum_izin = mysql_fetch_array($sonuc);


	//	SE��LEN FORUMUN �Z�NLER� �ZEL AYARINDA DE��LSE UYARILIYOR	//

	if (($forum_izin['okuma_izni'] == 5) OR ($forum_izin['konu_acma_izni'] == 5) OR ($forum_izin['yazma_izni'] == 5))
	{
		header('Location: ../hata.php?hata=161');
		exit();
	}

	if (($forum_izin['okuma_izni'] == 1) OR ($forum_izin['konu_acma_izni'] == 1) OR ($forum_izin['yazma_izni'] == 1))
	{
		header('Location: ../hata.php?hata=146');
		exit();
	}

	if (($forum_izin['okuma_izni'] == 2) OR ($forum_izin['konu_acma_izni'] == 2) OR ($forum_izin['yazma_izni'] == 2))
	{
		header('Location: ../hata.php?hata=194');
		exit();
	}


	// �ye - grup se�imi
	if ($_POST['izingoster'] == 'uye') $ek_sorgu = "kulad='$kullanici_satir[kullanici_adi]'";
	else $ek_sorgu = "grup='$grup_satir[id]'";


	// �Z�N B�LG�LER� �EK�L�YOR //

	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE $ek_sorgu AND fno='$forum_izin[id]'";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');
	$izinler_satir = mysql_fetch_array($sonuc);

	//	DAHA �NCEDEN BU FORUM ���N YETK�LEND�R�LMEM��SE	//
	//	DE���KEN DE�ER� 0 (YETK�S�Z) YAPILIYOR	//

	if (empty($izinler_satir))
	{
		$izinler_satir['okuma_izni'] = 0;
		$izinler_satir['yazma_izni'] = 0;
		$izinler_satir['konu_acma_izni'] = 0;
		$izinler_satir['yonetme_izni'] = 0;
	}
}











include 'yonetim_baslik.php';
?>

<table cellspacing="1" cellpadding="0" width="760" border="0" align="center" class="tablo_border">
	<tbody>
	<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tbody>
	<tr>
	<td height="17"></td>
	</tr>

	<tr>
	<td align="center" valign="top">

<table cellspacing="1" cellpadding="0" width="96%" border="0" class="tablo_border3">
	<tbody>
	<tr>
	<td align="center" valign="top" class="tablo_ici">

<table cellspacing="0" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tbody>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
- �ye ve Grup Yetkileri -

	</td>
	</tr>

	<tr>
	<td height="20"></td>
	</tr>

	<tr>
	<td align=center>
<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td>



<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html' ?>




		<!--	FORUM �Z�NLER� TABLOSU BA�I		-->


<table cellspacing="1" width="77%" cellpadding="5" border="0" align="right" class="tablo_border4">

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
<?php echo $tablo_baslik ?>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri">

<?php

if ((!isset($kullanici_satir['id'])) AND (!isset($grup_satir['id']))):

echo '<script type="text/javascript">
<!-- //
function uye_ara(){
var uye = document.kul_izinleri.kim.value;
window.open("../oi_yaz.php?kip=2&uye_ara="+uye, "_uyeara", "resizable=yes,width=390,height=350,scrollbars=yes");}
//  -->
</script>


<br>
<form name="kul_izinleri" action="kul_izinler.php" method="get">
<p align="center">
<b>�ye Ad�: &nbsp; </b><input type="text" name="kim" value="" class="formlar">
&nbsp;<input type="submit" value="Yetki" class="dugme">
&nbsp;&nbsp; <a style="font-weight: normal; text-decoration: underline" href="javascript:uye_ara();">�ye Ara</a>
</p>
</form>

<br>
<br>

&nbsp; &nbsp; �yelere �zel yetkiler vermek i�in yukar�daki alana �yenin kullan�c� ad�n� tam olarak yaz�n.
<br>
Ya da <a href="kullanicilar.php">bu sayfadan</a> istedi�iniz �yenin yan�ndaki <b>�. Yetki</b> ba�lant�s�n� t�klay�n.
<br>
<br>
Gruplar i�in; <a href="gruplar.php">bu sayfadan</a> istedi�iniz �ye grubunun yan�ndaki <b>�zel yetki ver</b> ba�lant�s�n� t�klay�n.
<br>
<br>
<br>�ye veya grup se�imini yapt�ktan sonra a��lan sayfadan �zel yetki vermek istedi�iniz forumu se�erek istedi�iniz �zel yetkiyi verebilirsiniz. 

<br><br><br>
	</td>
	</tr>
';




elseif ((isset($kullanici_satir['id'])) OR (isset($_GET['grup']))):


if (isset($kullanici_satir['id']))
{
	echo '<form name="kul_izinleri" action="kul_izinler.php?kim='.$kullanici_satir['kullanici_adi'].'#izinver" method="post">
	<input type="hidden" name="izingoster" value="uye">

	<br> &nbsp; &nbsp; &nbsp; <b>'.$kullanici_satir['kullanici_adi'].'</b> isimli �yeye, b�l�m yard�mc�l��� veya �zel izin vermek istedi�iniz forumu a�a��dan se�ip, "Forumu Se�" d��mesine t�klay�n. Daha sonra altta ��kan alandan okuma, konu a�ma, cevap yazma veya y�netme yetkisi verebilirsiniz.';
}

else
{
	echo '<form name="grup_izinleri" action="kul_izinler.php?grup='.$grup_satir['id'].'#izinver" method="post">
	<input type="hidden" name="izingoster" value="grup">

	<br> &nbsp; &nbsp; &nbsp; <b>'.$grup_satir['grup_adi'].'</b> isimli gruba, b�l�m yard�mc�l��� veya �zel izin vermek istedi�iniz forumu a�a��dan se�ip, "Forumu Se�" d��mesine t�klay�n. Daha sonra altta ��kan alandan okuma, konu a�ma, cevap yazma veya y�netme yetkisi verebilirsiniz.';
}



echo '<br><br><br>


<center>
<b>Forum Se�:</b>
<br><br>


';


$forum_secenek = '<select name="forum_sec" class="formlar" size="15">';


// forum dal� adlar� �ekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlar� �ekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bak�l�yor
		$strSQL = "SELECT id,forum_baslik FROM $tablo_forumlar WHERE alt_forum='$forum_satir[id]' ORDER BY sira";
		$sonuca = mysql_query($strSQL);


		if (!mysql_num_rows($sonuca))
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"';

			if ( ( isset($_POST['forum_sec']) ) AND ($_POST['forum_sec'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			else $forum_secenek .= '>';

			$forum_secenek .= ' &nbsp; - '.$forum_satir['forum_baslik'];
		}


		else
		{
			$forum_secenek .= '
			<option value="'.$forum_satir['id'].'"';

			if ( ( isset($_POST['forum_sec']) ) AND ($_POST['forum_sec'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
			else $forum_secenek .= '>';

			$forum_secenek .= ' &nbsp; - '.$forum_satir['forum_baslik'];


			while ($alt_forum_satir = mysql_fetch_array($sonuca))
			{
				$forum_secenek .= '
				<option value="'.$alt_forum_satir['id'].'"';

				if ( ( isset($_POST['forum_sec']) ) AND ($_POST['forum_sec'] == $alt_forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
				elseif ( ( isset($_POST['fno']) ) AND ($_POST['fno'] == $alt_forum_satir['id']) ) $forum_secenek .= ' selected="selected">';
				else $forum_secenek .= '>';

				$forum_secenek .= ' &nbsp; &nbsp; &nbsp; -- '.$alt_forum_satir['forum_baslik'];
			}
		}
	}
}


echo $forum_secenek.'
</select>
</center>
<br>
<p align="center"><input type="submit" value="Forum Se�" class="dugme"></p>
<br>';


if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'uye'))
echo '<p align="center"><font color="green"><b>'.$kullanici_satir['kullanici_adi'].' isimli �yenin, " '.$_POST['fad']. ' "<br>forumundaki izinleri de�i�tirilmi�tir.</b></font></p><br>';

elseif ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'grup'))
echo '<p align="center"><font color="green"><b>'.$grup_satir['grup_adi'].' isimli grubun, " '.$_POST['fad']. ' "<br>forumundaki izinleri de�i�tirilmi�tir.</b></font></p><br>';


echo '
</form>
	</td>
	</tr>';



//	FORUM SE� TIKLANMI�SA	//

if (isset($forum_izin)):

?>

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
<a name="izinver"></a>
- Kullan�c�ya Bu Forumda �zin Ver -
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="center">
<br>

<?php

echo '<font class="liste-etiket">..:::&nbsp; '.$forum_izin['forum_baslik'].' &nbsp;:::..</font>
<br><br>';


if (isset($kullanici_satir['id']))
{
	echo '<form name="kul_izinleri" action="kul_izinler.php?kim='.$kullanici_satir['kullanici_adi'].'" method="post">
	<input type="hidden" name="izindegistir" value="uye">
	<input type="hidden" name="fno" value="'.$forum_izin['id'].'">
	<input type="hidden" name="fad" value="'.$forum_izin['forum_baslik'].'">';
}

else
{
	echo '<form name="grup_izinleri" action="kul_izinler.php?grup='.$grup_satir['id'].'" method="post">
	<input type="hidden" name="izindegistir" value="grup">
	<input type="hidden" name="fno" value="'.$forum_izin['id'].'">
	<input type="hidden" name="fad" value="'.$forum_izin['forum_baslik'].'">';
}

?>


<table cellspacing="1" cellpadding="2" width="95%" border="0" align="center">
	<tr>
	<td class="liste-etiket" align="left" width="110" valign="top">Okuma:</td>

	<td class="liste-veri" align="left" valign="top">
<?php

//  OKUMA YETK�LER�

if ((empty($forum_izin['okuma_izni'])) OR ($forum_izin['okuma_izni'] == 0))
{
    echo 'Herkesin okuma yetkisi var.';
    echo '<input type="hidden" name="okuma" value="0">';
}

elseif ((isset($forum_izin['okuma_izni'])) AND ($forum_izin['okuma_izni'] == 4))
{
    echo 'T�m �yelerin okuma yetkisi var.';
    echo '<input type="hidden" name="okuma" value="0">';
}

else
{
    echo '<select name="okuma" class="formlar">';

    if ((empty($izinler_satir['okuma'])) OR ($izinler_satir['okuma'] == 0))
    echo '<option value="0" selected="selected">Yetkisi Yok
    <option value="1">Yetki Ver';

    elseif ((isset($izinler_satir['okuma'])) AND ($izinler_satir['okuma'] == 1))
    echo '<option value="0">Yetkiyi Al
    <option value="1" selected="selected">Yetkisi Var';

    echo '</select>';
}

?>
<br><br>
	</td>
	</tr>





	<tr>
	<td class="liste-etiket" align="left" valign="top">Konu A�ma:</td>
	<td class="liste-veri" align="left" valign="top">
<?php

//  KONU YETK�LER�

if ((empty($forum_izin['konu_acma_izni'])) OR ($forum_izin['konu_acma_izni'] == 0))
{
    echo 'Zaten t�m �yelerin konu a�ma yetkisi var.';
    echo '<input type="hidden" name="konu_acma" value="0">';
}

elseif ((isset($forum_izin['konu_acma_izni'])) AND ($forum_izin['konu_acma_izni'] == 2))
{
    echo 'Sadece b�l�m yard�mc�l��� yetkisi verdi�iniz �yeler konu a�abilir.';
    echo '<input type="hidden" name="konu_acma" value="0">';
}

else
{
    echo '<select name="konu_acma" class="formlar">';

    if ((empty($izinler_satir['konu_acma'])) OR ($izinler_satir['konu_acma'] == 0))
    echo '<option value="0" selected="selected">Yetkisi Yok
    <option value="1">Yetki Ver';

    elseif ((isset($izinler_satir['konu_acma'])) AND ($izinler_satir['konu_acma'] == 1))
    echo '<option value="0">Yetkiyi Al
    <option value="1" selected="selected">Yetkisi Var';

    echo '</select>';
}

?>
<br><br>
	</td>
	</tr>




	<tr>
	<td class="liste-etiket" align="left" valign="top">Cevap Yazma:</td>
	<td class="liste-veri" align="left" valign="top">
<?php

//  CEVAP YETK�LER�

if ((empty($forum_izin['yazma_izni'])) OR ($forum_izin['yazma_izni'] == 0))
{
    echo 'Zaten t�m �yelerin cevap yazma yetkisi var.';
    echo '<input type="hidden" name="yazma" value="0">';
}

else
{
    echo '<select name="yazma" class="formlar">';

    if ((empty($izinler_satir['yazma'])) OR ($izinler_satir['yazma'] == 0))
    echo '<option value="0" selected="selected">Yetkisi Yok
    <option value="1">Yetki Ver';

    elseif ((isset($izinler_satir['yazma'])) AND ($izinler_satir['yazma'] == 1))
    echo '<option value="0">Yetkiyi Al
    <option value="1" selected="selected">Yetkisi Var';

    echo '</select>';
}

?>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-etiket" align="left" valign="top">Y�netme:</td>
	<td class="liste-veri" align="left" valign="top">
<?php

//  Y�NETME YETK�LER�

if (($forum_izin['konu_acma_izni'] == 1) OR ($forum_izin['yazma_izni'] == 1))
echo 'Bu forumu sadece y�neticiler y�netebilir.';

elseif ((empty($izinler_satir['yonetme'])) OR ($izinler_satir['yonetme'] == 0))
echo '<select name="yonetme" class="formlar">
<option value="0" selected="selected">Yetkisi Yok
<option value="1">Yetki Ver</select> &nbsp; Bu b�l�m�n yard�mc�s� yap.';


elseif ((isset($izinler_satir['yonetme'])) AND ($izinler_satir['yonetme'] == 1))
echo '<select name="yonetme" class="formlar">
<option value="0">Yetkiyi Al
<option value="1" selected="selected">Yetkisi Var</select> &nbsp; Bu b�l�m�n yard�mc�s� yap.';

?>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top" colspan="2">
<i>Y�netme yetkisi verdi�inizde, �ye o b�l�m�n yard�mc�s� olur. Ayr�ca okuma, konu a�ma veya cevap yazma yetkisi vermenize gerek kalmaz.</i>
	</td>
	</tr>
</table>


<br>
<input type="submit" value="�zinleri De�i�tir" class="dugme">

<br><br>
</form>
<?php

	//	FORM �Z�NLER� G�R�NT�LEN�YOR - B�T��	//

endif;
endif;

?>

</table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
<tr>
<td align="center" height="15"></td>
</tr>
</table>
</td></tr></table>
<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>