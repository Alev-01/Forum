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


//   ÜYE ÝÞLEMLERÝ   //

if ((isset($_GET['kim'])) AND ($_GET['kim'] != ''))
{
	// Üye bilgileri veritabanýndan çekiliyor

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


	// Seçilen üye yönetici ise uyarý ver

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

	$sayfa_adi = 'Yönetim Üye Yetkileri: '.$_GET['kim'];
	$tablo_baslik = '- Forum Seçimi -';
}



//   GRUP ÝÞLEMLERÝ   //

elseif ((isset($_GET['grup'])) AND ($_GET['grup'] != ''))
{
	// Grup bilgileri veritabanýndan çekiliyor

	$_GET['grup'] = zkTemizle(trim($_GET['grup']));

	$strSQL = "SELECT * FROM $tablo_gruplar WHERE id='$_GET[grup]' LIMIT 1";
	$sonuc = mysql_query($strSQL);
	$grup_satir = mysql_fetch_array($sonuc);

	if (empty($grup_satir))
	{
		header('Location: ../hata.php?hata=204');
		exit();
	}


	$sayfa_adi = 'Yönetim Grup Yetkileri: '.$grup_satir['grup_adi'];
	$tablo_baslik = '- Forum Seçimi -';
}



else
{
	$sayfa_adi = 'Yönetim Üye ve Grup Yetkileri';
	$tablo_baslik = '- Üye Seçimi -';
}





// ÜYE ÝZÝN BÝLGÝLERÝ DEÐÝÞTÝRÝLÝYOR //

if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'uye'))
{
	if (isset($_POST['okuma'])) $_POST['okuma'] = zkTemizle($_POST['okuma']);
	if (isset($_POST['yazma'])) $_POST['yazma'] = zkTemizle($_POST['yazma']);
	if (isset($_POST['yonetme'])) $_POST['yonetme'] = zkTemizle($_POST['yonetme']);
	if (isset($_POST['konu_acma'])) $_POST['konu_acma'] = zkTemizle($_POST['konu_acma']);
	if (isset($_POST['fno'])) $_POST['fno'] = zkTemizle($_POST['fno']);


	// ÖZEL ÝZÝN BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]'";
	$VARMI = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	//	DAHA ÖNCEDEN BU FORUM ÝÇÝN YETKÝLENDÝRÝL-MEMÝÞSE INSERT	//

	if (!mysql_num_rows($VARMI))
	{
		// SADECE YETKÝ VERÝLMÝÞSE GÝR	//
		if ( (isset($_POST['okuma'])) AND ($_POST['okuma'] == '1') OR (isset($_POST['yazma'])) AND ($_POST['yazma'] == '1')
		OR (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') OR (isset($_POST['konu_acma'])) AND ($_POST['konu_acma'] == '1') )
		{
			//	SADECE YÖNETÝM VERÝLMÝÞ ÝSE DÝÐERLERÝ DE VERÝLÝYOR	//
			//	KULLANICI YETKÝ DERECESÝ 3 YAPILIYOR	//

			if ($_POST['yonetme'] == '1')
			{
				$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE id='$kullanici_satir[id]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

				$_POST['okuma'] = 1;
				$_POST['yazma'] = 1;
				$_POST['konu_acma'] = 1;
			}

			$strSQL = "INSERT INTO $tablo_ozel_izinler (kulad,kulid,grup,fno,okuma,yazma,yonetme,konu_acma)";
			$strSQL .= "VALUES ('$kullanici_satir[kullanici_adi]','$kullanici_satir[id]','0','$_POST[fno]','$_POST[okuma]','$_POST[yazma]','$_POST[yonetme]','$_POST[konu_acma]')";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}


	//	DAHA ÖNCEDEN BU FORUM ÝÇÝN YETKÝLENDÝRÝLMÝÞSE UPDATE	//

	else
	{
		//	YETKÝSÝ GERÝ ALINIYORSA VERÝTABANINDAN SÝL	//
		if (($_POST['okuma'] == '0') AND ($_POST['yazma'] == '0') AND ($_POST['konu_acma'] == '0') AND ($_POST['yonetme'] == '0'))
		{
			$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]' LIMIT 1";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			// kullanýcýnýn baþka yardýmcýlýðý varmý bakýlýyor
			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='0' AND kulad='$kullanici_satir[kullanici_adi]' AND yonetme='1' LIMIT 1";
			$yardimcilik = mysql_query($strSQL);

			if (!mysql_num_rows($yardimcilik))
			{
				if ($kullanici_satir['grupid'] != '0')
				{
					// kullanýcýnýn üye olduðu grubun yardýmcýlýðý varmý bakýlýyor
					$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$kullanici_satir[grupid]' AND yonetme='1' LIMIT 1";
					$yardimcilik2 = mysql_query($strSQL);
				}

				// hiçbir yardýmcýlýðý yoksa yetkisi normale düþürülüyor
				if (!isset($yardimcilik2['fno']))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$kullanici_satir[id]' LIMIT 1";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
				}
			}
		}

		else
		{
			//	SADECE YÖNETÝM VERÝLMÝÞ ÝSE DÝÐERLERÝ DE VERÝLÝYOR	//
			//	KULLANICI YETKÝ DERECESÝ 3 YAPILIYOR	//

			if ($_POST['yonetme'] == '1')
			{
				$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE id='$kullanici_satir[id]' LIMIT 1";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

				$_POST['okuma'] = 1;
				$_POST['yazma'] = 1;
				$_POST['konu_acma'] = 1;
			}

			$strSQL = "UPDATE $tablo_ozel_izinler SET okuma='$_POST[okuma]', yazma='$_POST[yazma]', yonetme='$_POST[yonetme]', konu_acma='$_POST[konu_acma]'
			WHERE kulad='$kullanici_satir[kullanici_adi]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			//	YÖNETÝM VERÝLMEMÝÞSE, BAÞKA YARDIMCILIÐI DA YOKSA...//
			//	KULLANICI YETKÝSÝ NORMALE DÜÞÜRÜLÜYOR	//

			if ($_POST['yonetme'] == '0')
			{
				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE kulad='$kullanici_satir[kullanici_adi]' AND yonetme='1'";
				$yardimcilik = mysql_query($strSQL);

				if (!mysql_num_rows($yardimcilik))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$kullanici_satir[id]' LIMIT 1";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
				}
			}
		}
	}
}






// GRUP ÝZÝN BÝLGÝLERÝ DEÐÝÞTÝRÝLÝYOR //

if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'grup'))
{
	if (isset($_POST['okuma'])) $_POST['okuma'] = zkTemizle($_POST['okuma']);
	if (isset($_POST['yazma'])) $_POST['yazma'] = zkTemizle($_POST['yazma']);
	if (isset($_POST['yonetme'])) $_POST['yonetme'] = zkTemizle($_POST['yonetme']);
	if (isset($_POST['konu_acma'])) $_POST['konu_acma'] = zkTemizle($_POST['konu_acma']);
	if (isset($_POST['fno'])) $_POST['fno'] = zkTemizle($_POST['fno']);


	//	SADECE YÖNETÝM VERÝLMÝÞ ÝSE DÝÐERLERÝ DE VERÝLÝYOR	//
	//	KULLANICI YETKÝ DERECESÝ 3 YAPILIYOR	//

	if ( (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') )
	{
		$_POST['okuma'] = 1;
		$_POST['yazma'] = 1;
		$_POST['konu_acma'] = 1;

		$strSQL = "UPDATE $tablo_kullanicilar SET yetki='3' WHERE grupid='$grup_satir[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

		$strSQL = "UPDATE $tablo_gruplar SET yetki='3' WHERE id='$grup_satir[id]'";
		$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	}


	// ÖZEL ÝZÝN BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
	$VARMI = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


	//	DAHA ÖNCEDEN BU FORUM ÝÇÝN YETKÝLENDÝRÝL-MEMÝÞSE INSERT	//

	if (!mysql_num_rows($VARMI))
	{
		// SADECE YETKÝ VERÝLMÝÞSE GÝR	//
		if ( (isset($_POST['okuma'])) AND ($_POST['okuma'] == '1') OR (isset($_POST['yazma'])) AND ($_POST['yazma'] == '1')
		OR (isset($_POST['yonetme'])) AND ($_POST['yonetme'] == '1') OR (isset($_POST['konu_acma'])) AND ($_POST['konu_acma'] == '1') )
		{
			$strSQL = "INSERT INTO $tablo_ozel_izinler (kulad,kulid,grup,fno,okuma,yazma,yonetme,konu_acma)";
			$strSQL .= "VALUES ('$grup_satir[grup_adi]', '0', '$grup_satir[id]', '$_POST[fno]','$_POST[okuma]','$_POST[yazma]','$_POST[yonetme]','$_POST[konu_acma]')";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		}
	}


	//	DAHA ÖNCEDEN BU FORUM ÝÇÝN YETKÝLENDÝRÝLMÝÞSE UPDATE	//

	else
	{
		//	YETKÝSÝ GERÝ ALINIYORSA VERÝTABANINDAN SÝL	//
		if (($_POST['okuma'] == '0') AND ($_POST['yazma'] == '0') AND ($_POST['konu_acma'] == '0') AND ($_POST['yonetme'] == '0'))
		{
			$strSQL = "DELETE FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			// GRUBUN BAÞKA YÖNETÝCÝLÝÐÝ YOKSA, KULLANICI YETKÝSÝ NORMALE DÜÞÜRÜLÜYOR	//
			$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND yonetme='1'";
			$yardimcilik = mysql_query($strSQL);

			if (!mysql_num_rows($yardimcilik))
			{
				$strSQL = "UPDATE $tablo_gruplar SET yetki='-1' WHERE id='$grup_satir[id]'";
				$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


				// ayrýca grup üyelerinin baþka yardýmcýlýðý olup olmadýðýna bakýlýyor
				$strSQL = "SELECT id FROM $tablo_kullanicilar WHERE grupid='$grup_satir[id]'";
				$sonucguye = mysql_query($strSQL);

				while ($guye = mysql_fetch_assoc($sonucguye))
				{
					$strSQL = "SELECT kulid FROM $tablo_ozel_izinler WHERE kulid='$guye[id]' AND yonetme='1'";
					$yardimcilik = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

					// baþka yardýmcýlýðý yoksa yetkisi normale düþürülüyor
					if (!mysql_num_rows($yardimcilik))
					{
						$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE id='$guye[id]' LIMIT 1";
						$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
					}
				}
			}
		}

		else
		{
			$strSQL = "UPDATE $tablo_ozel_izinler SET okuma='$_POST[okuma]', yazma='$_POST[yazma]', yonetme='$_POST[yonetme]', konu_acma='$_POST[konu_acma]'
			WHERE grup='$grup_satir[id]' AND fno='$_POST[fno]'";
			$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

			//	YÖNETÝM VERÝLMEMÝÞSE, BAÞKA YARDIMCILIÐI DA YOKSA...//
			//	KULLANICI YETKÝSÝ NORMALE DÜÞÜRÜLÜYOR	//

			if ($_POST['yonetme'] == '0')
			{
				$strSQL = "SELECT fno FROM $tablo_ozel_izinler WHERE grup='$grup_satir[id]' AND yonetme='1'";
				$yardimcilik = mysql_query($strSQL);

				if (!mysql_num_rows($yardimcilik))
				{
					$strSQL = "UPDATE $tablo_kullanicilar SET yetki='0' WHERE grupid='$grup_satir[id]'";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

					$strSQL = "UPDATE $tablo_gruplar SET yetki='-1' WHERE id='$grup_satir[id]'";
					$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
				}
			}
		}
	}
}








//	ÝZÝNLERÝ GÖSTER TIKLANIÞSA	//

if ((isset($_POST['izingoster'])) AND ($_POST['izingoster'] != ''))
{
	if ((!isset($_POST['forum_sec'])) OR ($_POST['forum_sec'] == ''))
	{
		header('Location: ../hata.php?hata=152');
		exit();
	}

	else $_POST['forum_sec'] = zkTemizle($_POST['forum_sec']);


	// FORUM ÝZÝN BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT id,forum_baslik,okuma_izni,yazma_izni,konu_acma_izni FROM $tablo_forumlar WHERE id='$_POST[forum_sec]' LIMIT 1";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$forum_izin = mysql_fetch_array($sonuc);


	//	SEÇÝLEN FORUMUN ÝZÝNLERÝ ÖZEL AYARINDA DEÐÝLSE UYARILIYOR	//

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


	// üye - grup seçimi
	if ($_POST['izingoster'] == 'uye') $ek_sorgu = "kulad='$kullanici_satir[kullanici_adi]'";
	else $ek_sorgu = "grup='$grup_satir[id]'";


	// ÝZÝN BÝLGÝLERÝ ÇEKÝLÝYOR //

	$strSQL = "SELECT * FROM $tablo_ozel_izinler WHERE $ek_sorgu AND fno='$forum_izin[id]'";
	$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
	$izinler_satir = mysql_fetch_array($sonuc);

	//	DAHA ÖNCEDEN BU FORUM ÝÇÝN YETKÝLENDÝRÝLMEMÝÞSE	//
	//	DEÐÝÞKEN DEÐERÝ 0 (YETKÝSÝZ) YAPILIYOR	//

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
- Üye ve Grup Yetkileri -

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




		<!--	FORUM ÝZÝNLERÝ TABLOSU BAÞI		-->


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
<b>Üye Adý: &nbsp; </b><input type="text" name="kim" value="" class="formlar">
&nbsp;<input type="submit" value="Yetki" class="dugme">
&nbsp;&nbsp; <a style="font-weight: normal; text-decoration: underline" href="javascript:uye_ara();">Üye Ara</a>
</p>
</form>

<br>
<br>

&nbsp; &nbsp; Üyelere özel yetkiler vermek için yukarýdaki alana üyenin kullanýcý adýný tam olarak yazýn.
<br>
Ya da <a href="kullanicilar.php">bu sayfadan</a> istediðiniz üyenin yanýndaki <b>Ö. Yetki</b> baðlantýsýný týklayýn.
<br>
<br>
Gruplar için; <a href="gruplar.php">bu sayfadan</a> istediðiniz üye grubunun yanýndaki <b>Özel yetki ver</b> baðlantýsýný týklayýn.
<br>
<br>
<br>Üye veya grup seçimini yaptýktan sonra açýlan sayfadan özel yetki vermek istediðiniz forumu seçerek istediðiniz özel yetkiyi verebilirsiniz. 

<br><br><br>
	</td>
	</tr>
';




elseif ((isset($kullanici_satir['id'])) OR (isset($_GET['grup']))):


if (isset($kullanici_satir['id']))
{
	echo '<form name="kul_izinleri" action="kul_izinler.php?kim='.$kullanici_satir['kullanici_adi'].'#izinver" method="post">
	<input type="hidden" name="izingoster" value="uye">

	<br> &nbsp; &nbsp; &nbsp; <b>'.$kullanici_satir['kullanici_adi'].'</b> isimli üyeye, bölüm yardýmcýlýðý veya özel izin vermek istediðiniz forumu aþaðýdan seçip, "Forumu Seç" düðmesine týklayýn. Daha sonra altta çýkan alandan okuma, konu açma, cevap yazma veya yönetme yetkisi verebilirsiniz.';
}

else
{
	echo '<form name="grup_izinleri" action="kul_izinler.php?grup='.$grup_satir['id'].'#izinver" method="post">
	<input type="hidden" name="izingoster" value="grup">

	<br> &nbsp; &nbsp; &nbsp; <b>'.$grup_satir['grup_adi'].'</b> isimli gruba, bölüm yardýmcýlýðý veya özel izin vermek istediðiniz forumu aþaðýdan seçip, "Forumu Seç" düðmesine týklayýn. Daha sonra altta çýkan alandan okuma, konu açma, cevap yazma veya yönetme yetkisi verebilirsiniz.';
}



echo '<br><br><br>


<center>
<b>Forum Seç:</b>
<br><br>


';


$forum_secenek = '<select name="forum_sec" class="formlar" size="15">';


// forum dalý adlarý çekiliyor

$strSQL = "SELECT id,ana_forum_baslik FROM $tablo_dallar ORDER BY sira";
$dallar_sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');


while ($dallar_satir = mysql_fetch_array($dallar_sonuc))
{
	$forum_secenek .= '<option value="">[ '.$dallar_satir['ana_forum_baslik'].' ]';


	// forum adlarý çekiliyor

	$strSQL = "SELECT id,forum_baslik,alt_forum FROM $tablo_forumlar WHERE alt_forum='0' AND dal_no='$dallar_satir[id]' ORDER BY sira";
	$sonuc = mysql_query($strSQL);


	while ($forum_satir = mysql_fetch_array($sonuc))
	{
		// alt forumuna bakýlýyor
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
<p align="center"><input type="submit" value="Forum Seç" class="dugme"></p>
<br>';


if ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'uye'))
echo '<p align="center"><font color="green"><b>'.$kullanici_satir['kullanici_adi'].' isimli üyenin, " '.$_POST['fad']. ' "<br>forumundaki izinleri deðiþtirilmiþtir.</b></font></p><br>';

elseif ((isset($_POST['izindegistir'])) AND ($_POST['izindegistir'] == 'grup'))
echo '<p align="center"><font color="green"><b>'.$grup_satir['grup_adi'].' isimli grubun, " '.$_POST['fad']. ' "<br>forumundaki izinleri deðiþtirilmiþtir.</b></font></p><br>';


echo '
</form>
	</td>
	</tr>';



//	FORUM SEÇ TIKLANMIÞSA	//

if (isset($forum_izin)):

?>

	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
<a name="izinver"></a>
- Kullanýcýya Bu Forumda Ýzin Ver -
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

//  OKUMA YETKÝLERÝ

if ((empty($forum_izin['okuma_izni'])) OR ($forum_izin['okuma_izni'] == 0))
{
    echo 'Herkesin okuma yetkisi var.';
    echo '<input type="hidden" name="okuma" value="0">';
}

elseif ((isset($forum_izin['okuma_izni'])) AND ($forum_izin['okuma_izni'] == 4))
{
    echo 'Tüm üyelerin okuma yetkisi var.';
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
	<td class="liste-etiket" align="left" valign="top">Konu Açma:</td>
	<td class="liste-veri" align="left" valign="top">
<?php

//  KONU YETKÝLERÝ

if ((empty($forum_izin['konu_acma_izni'])) OR ($forum_izin['konu_acma_izni'] == 0))
{
    echo 'Zaten tüm üyelerin konu açma yetkisi var.';
    echo '<input type="hidden" name="konu_acma" value="0">';
}

elseif ((isset($forum_izin['konu_acma_izni'])) AND ($forum_izin['konu_acma_izni'] == 2))
{
    echo 'Sadece bölüm yardýmcýlýðý yetkisi verdiðiniz üyeler konu açabilir.';
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

//  CEVAP YETKÝLERÝ

if ((empty($forum_izin['yazma_izni'])) OR ($forum_izin['yazma_izni'] == 0))
{
    echo 'Zaten tüm üyelerin cevap yazma yetkisi var.';
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
	<td class="liste-etiket" align="left" valign="top">Yönetme:</td>
	<td class="liste-veri" align="left" valign="top">
<?php

//  YÖNETME YETKÝLERÝ

if (($forum_izin['konu_acma_izni'] == 1) OR ($forum_izin['yazma_izni'] == 1))
echo 'Bu forumu sadece yöneticiler yönetebilir.';

elseif ((empty($izinler_satir['yonetme'])) OR ($izinler_satir['yonetme'] == 0))
echo '<select name="yonetme" class="formlar">
<option value="0" selected="selected">Yetkisi Yok
<option value="1">Yetki Ver</select> &nbsp; Bu bölümün yardýmcýsý yap.';


elseif ((isset($izinler_satir['yonetme'])) AND ($izinler_satir['yonetme'] == 1))
echo '<select name="yonetme" class="formlar">
<option value="0">Yetkiyi Al
<option value="1" selected="selected">Yetkisi Var</select> &nbsp; Bu bölümün yardýmcýsý yap.';

?>
<br><br>
	</td>
	</tr>

	<tr>
	<td class="liste-veri" align="left" valign="top" colspan="2">
<i>Yönetme yetkisi verdiðinizde, üye o bölümün yardýmcýsý olur. Ayrýca okuma, konu açma veya cevap yazma yetkisi vermenize gerek kalmaz.</i>
	</td>
	</tr>
</table>


<br>
<input type="submit" value="Ýzinleri Deðiþtir" class="dugme">

<br><br>
</form>
<?php

	//	FORM ÝZÝNLERÝ GÖRÜNTÜLENÝYOR - BÝTÝÞ	//

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