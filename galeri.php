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


if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GUVENLIK')) include 'guvenlik.php';


$sayfano = 36;
$sayfa_adi = 'Kullanýcý Resim Galerisi';

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
$secili = 'checked="checked"';	// sadece ilkini seçili yap


//	ZARARLI KODLAR TEMÝZLENÝYOR	//
if (isset($_GET['galeri']))
{
	$_GET['galeri'] = str_replace('/','',$_GET['galeri']);
	$_GET['galeri'] = str_replace('.','',$_GET['galeri']);
}




//  DÝÐER GALERÝLER //

$diger_galeriler = 'dosyalar/resimler/galeri/'; // galeri dizini

$dizin = @opendir($diger_galeriler);	// dizini açýyoruz

while ( @gettype($bilgi = @readdir($dizin)) != 'boolean' )
{
	if ( (@is_dir($diger_galeriler.$bilgi)) AND ($bilgi != '.') AND ($bilgi != '..') )
		$dizinler .= '&nbsp; | &nbsp;<a href="galeri.php?galeri='.$bilgi.$kim.'">'.$bilgi.'</a>';
}

@closedir($dizin);	// dizini kapatýyoruz




//	DÝZÝNDEKÝ DOSYALAR DÖNGÜYE SOKULARAK GÖRÜNTÜLENÝYOR	//

if ( (isset($_GET['galeri'])) AND ($_GET['galeri'] != '') )
$dizin_adi = 'dosyalar/resimler/galeri/'.$_GET['galeri'].'/';

else $dizin_adi = 'dosyalar/resimler/galeri/';

$dizin = @opendir($dizin_adi);	// dizini açýyoruz

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

@closedir($dizin);	// dizini kapatýyoruz





//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/galeri.html');


$ornek1->dongusuz(array('{HEDEF}' => $hedef,
						'{GALERI_TABLO}' => $galeri_tablo,
						'{DIGER_DIZINLER}' => $dizinler));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>