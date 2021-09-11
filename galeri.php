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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';


$sayfano = 36;
$sayfa_adi = 'Kullan�c� Resim Galerisi';

include 'baslik.php';


if ( (isset($_GET['kim'])) AND ($_GET['kim'] != '') )
{
	$kim = '&amp;kim='.$_GET['kim'];
	$hedef = 'yonetim/kullanici_degistir.php?u='.$_GET['kim'];
}

else
{
	$hedef = 'profil_degistir.php';
	$kim = '';
}


$galeri_tablo = '';
$dizinler = '<a href="galeri.php?galeri='.$kim.'">Ana Galeri</a>';
$secili = 'checked="checked"';	// sadece ilkini se�ili yap


//	ZARARLI KODLAR TEM�ZLEN�YOR	//
if (isset($_GET['galeri']))
{
	$_GET['galeri'] = str_replace('/','',$_GET['galeri']);
	$_GET['galeri'] = str_replace('.','',$_GET['galeri']);
}




//  D��ER GALER�LER //

$diger_galeriler = 'dosyalar/resimler/galeri/'; // galeri dizini

$dizin = @opendir($diger_galeriler);	// dizini a��yoruz

while ( @gettype($bilgi = @readdir($dizin)) != 'boolean' )
{
	if ( (@is_dir($diger_galeriler.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
		$dizinler .= '&nbsp; | &nbsp;<a href="galeri.php?galeri='.$bilgi.$kim.'">'.$bilgi.'</a>';
}

@closedir($dizin);	// dizini kapat�yoruz




//	D�Z�NDEK� DOSYALAR D�NG�YE SOKULARAK G�R�NT�LEN�YOR	//

if ( (isset($_GET['galeri'])) AND ($_GET['galeri'] != '') )
$dizin_adi = 'dosyalar/resimler/galeri/'.$_GET['galeri'].'/';

else $dizin_adi = 'dosyalar/resimler/galeri/';

$dizin = @opendir($dizin_adi);	// dizini a��yoruz

while ( @gettype($bilgi = @readdir($dizin)) != 'boolean' )
{
	if ( (!@is_dir($dizin_adi.$bilgi)) AND (preg_match('/.jpg$/i', $bilgi)) OR
		(!@is_dir($dizin_adi.$bilgi)) AND (preg_match('/.jpeg$/i', $bilgi)) OR
		(!@is_dir($dizin_adi.$bilgi)) AND (preg_match('/.gif$/i', $bilgi)) OR
		(!@is_dir($dizin_adi.$bilgi)) AND (preg_match('/.png$/i', $bilgi)) )
	{

		$galeri_tablo .= '
<table cellspacing="1" cellpadding="0" border="0" align="left" class="tablo_border4" style="margin: 6px;float:left">
    <tr>
    <td height="135" width="135" align="center" valign="middle" class="tablo_ici">
<label style="cursor: pointer;"><img src="'.$dizin_adi.$bilgi.'" alt="'.$bilgi.'"><br>
<input type="radio" name="galeri_resimi" size="20" value="'.$dizin_adi.$bilgi.'" '.$secili.'></label>
	</td>
    </tr>
</table>

';
		$secili = '';
	}
}

@closedir($dizin);	// dizini kapat�yoruz





//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/galeri.html');


$ornek1->dongusuz(array('{HEDEF}' => $hedef,
						'{GALERI_TABLO}' => $galeri_tablo,
						'{DIGER_DIZINLER}' => $dizinler));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>