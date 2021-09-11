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


//  VERÝTABANI EK YÜK GÝDERME ÝÞLEMÝ   //

if ( (isset($_GET['vt'])) AND ($_GET['vt'] == 'ekyuk') )
{
    $strSQL = "SHOW TABLE STATUS LIKE '%'";
    $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

    $ekyuk_sonucu = '';

    while ($tablo_bilgileri = mysql_fetch_array($sonuc))
    {
		$strSQL = "OPTIMIZE TABLE $tablo_bilgileri[Name]";
		$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
		$ekyuk_bilgisi = mysql_fetch_assoc($sonuc2);
		$ekyuk_sonucu .= $ekyuk_bilgisi['Table'].' &nbsp;-&nbsp; '.$ekyuk_bilgisi['Op'].
		' &nbsp;-&nbsp; '.$ekyuk_bilgisi['Msg_type'].' &nbsp;-&nbsp; '.$ekyuk_bilgisi['Msg_text'].'<br>';
    }
}



//  VERÝTABANI ONARMA ÝÞLEMÝ    //

if ( (isset($_GET['vt'])) AND ($_GET['vt'] == 'onar') )
{
    $strSQL = "SHOW TABLE STATUS LIKE '%'";
    $sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

    $onarma_sonucu = '';

    while ($tablo_bilgileri = mysql_fetch_array($sonuc))
    {
        $strSQL = "REPAIR TABLE $tablo_bilgileri[Name]";
        $sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');
        $onarma_bilgisi = mysql_fetch_assoc($sonuc2);
        $onarma_sonucu .= $onarma_bilgisi['Table'].' &nbsp;-&nbsp; '.$onarma_bilgisi['Op'].
        ' &nbsp;-&nbsp; '.$onarma_bilgisi['Msg_type'].' &nbsp;-&nbsp; '.$onarma_bilgisi['Msg_text'].'<br>';
    }
}

$sayfa_adi = 'Yönetim - Veritabaný Yönetimi';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';

?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
Veritabaný Yönetimi

	</td>
	</tr>
	
	<tr>
	<td height="20"></td>
	</tr>
	
	<tr>
	<td align="center">

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tr>
	<td>



<?php include './../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html' ?>





<table cellspacing="1" width="77%" cellpadding="4" border="0" align="right" class="tablo_border4">
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" align="center" colspan="5">
Veritabaný Yönetimi
	</td>
	</tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" colspan="5">
<br>

<?php
//  VERÝTABANINDAKÝ EK YÜKÜ GÝDER TILANMIÞSA //

if ( (isset($ekyuk_sonucu)) AND ($ekyuk_sonucu != '') )
{
    echo '<center><b>Veritabanýndaki Ek Yük Giderilmiþtir !</b></center><p>
    &nbsp;&nbsp; Veritabaný ek yük giderme ayrýntýlarý:</p>'.
    $ekyuk_sonucu.'
    <br><br><center>***************************************</center>';
}


//  VERÝTABANINI ONAR TILANMIÞSA //

if ( (isset($onarma_sonucu)) AND ($onarma_sonucu != '') )
{
    echo '<center><b>Veritabaný Onarýlmýþtýr !</b></center><p>
    &nbsp;&nbsp; Veritabaný onarým ayrýntýlarý:</p>'.
    $onarma_sonucu.'
    <br><br><center>***************************************</center>';
}
?>

<br>
 &nbsp;&nbsp; Buradan, veritabanýnýz boyutu ve satýr sayýsý hakkýndaki bilgileri tablo tablo görebilir; Gerek duyarsanýz onarma ve ek yük giderme iþlemlerini gerçekleþtirebilirsiniz.
<br><br>
 &nbsp;&nbsp; Çizelgedeki; ek yük boyutu verileri bayt cinsindendir.
<br>Toplam, veri boyutu ve index boyutu verileri ise kilobayt cinsindendir.
<br><br>
    </td>
    </tr>

	<tr class="tablo_ici">
    <td align="center" class="liste-etiket" width="130">tablo adý</td>
    <td align="center" class="liste-etiket">girdi</td>
    <td align="center" class="liste-etiket">veri boyutu</td>
    <td align="center" class="liste-etiket" width="100">index boyutu</td>
    <td align="center" class="liste-etiket">ek yük</td>
    </tr>

<?php


//	VERÝTABANI BOYUTU HESAPLANIYOR - BAÞI	//

$strSQL = "SHOW TABLE STATUS LIKE '%'";
$sonuc = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

$toplam_boyut = 0;
$toplam_satir = 0;
$toplam_ekyuk = 0;

while ($tablo_bilgileri = mysql_fetch_array($sonuc))
{
	echo '<tr class="liste-veri" bgcolor="'.$yazi_tabani1.'" onMouseOver="this.bgColor= \''.$yazi_tabani2.'\'" onMouseOut="this.bgColor= \''.$yazi_tabani1.'\'">
	<td align="left" class="liste-veri">'.$tablo_bilgileri['Name'].
	'</td><td align="left" class="liste-veri">'.$tablo_bilgileri['Rows'].
	'</td><td align="left" class="liste-veri">';

	printf("%.1f" , ($tablo_bilgileri['Data_length'] / 1024));
	echo ' <b>kb.</b></td><td align="left" class="liste-veri">';

	printf("%.1f" , ($tablo_bilgileri['Index_length'] / 1024));
	echo ' <b>kb.</b></td><td align="left" class="liste-veri">'.$tablo_bilgileri['Data_free'].
	'</td></tr>';

	$toplam_boyut += ($tablo_bilgileri['Data_length'] + $tablo_bilgileri['Index_length']);
	$toplam_satir += $tablo_bilgileri['Rows'];
	$toplam_ekyuk += $tablo_bilgileri['Data_free'];
}

echo '<tr class="tablo_ici">
    <td align="left" class="liste-veri" colspan="5">&nbsp;</td>
    </tr>
    <tr class="tablo_ici">
    <td align="left" class="liste-etiket">Toplam</td>
    <td align="left" class="liste-etiket">'.$toplam_satir.
    '<td align="left" class="liste-etiket" colspan="2">';
printf("%.2f" , ($toplam_boyut / 1024));
echo ' kb.</td>
    <td align="left" class="liste-etiket">'.$toplam_ekyuk;


//	VERÝTABANI BOYUTU HESAPLANIYOR - SONU	//

?>

    </td>
    </tr>

	<tr class="tablo_ici">
	<td align="left" class="liste-veri" colspan="5">
<br><br>
 &nbsp;&nbsp; <a href="vt_yonetim.php?vt=ekyuk">Tüm tablolardaki ek yükü gider.</a>
<p>
 &nbsp;&nbsp; <a href="vt_yonetim.php?vt=onar">Tüm tablolarý onar.</a>


<br><br>
    </td>
    </tr>

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