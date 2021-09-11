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
$sayfa_adi = 'Yönetim MySQL Bilgileri';
include 'yonetim_baslik.php';
include './../temalar/'.$ayarlar['temadizini'].'/yonetim/govde.php';

?>
	<tr>
	<td class="baslik" align="center" valign="bottom">
<br>
MySQL Bilgileri

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
	<td class="forum_baslik" bgcolor="#0099ff" align="center" colspan="2">
MySQL Bilgileri
	</td>
	</tr>


<?php

//  MYSQL SUNUCUNUN ÇALIÞMA SÜRESÝ ALINIYOR //

$strSQL = "SHOW STATUS LIKE 'Uptime'";

$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');
$mysql_calisma = mysql_fetch_assoc($sonuc);


$acilis = zonedate('d.m.Y - H:i:s', $ayarlar['saat_dilimi'], false, (time()-$mysql_calisma['Value']));

$gun = ($mysql_calisma['Value'] / 60 / 60 / 24);
settype($gun,'integer');
$saat = (($mysql_calisma['Value'] / 60 / 60 ) % 24);
$dakika = (($mysql_calisma['Value'] / 60 ) % 60);
$saniye = ($mysql_calisma['Value'] % 60);


echo '
<tr class="tablo_ici">
<td align="left" class="liste-veri" colspan="2">
<br><br>
<b> &nbsp; MySQL sunucu çalýþma süresi: &nbsp; </b>'.$gun.' gün, '.$saat.' saat, '.$dakika.' dakika ve '.$saniye.' saniye

<p><b> &nbsp; MySQL sunucu baþlama zamaný: &nbsp; </b>'.$acilis.'
<br><br><br>
</td>
</tr>
';




//	MySQL BÝLGÝLERÝ ÇEKÝLÝYOR	//

$strSQL = "SHOW STATUS";

$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu baþarýsýz</h2>');

while ($show_status = mysql_fetch_array($sonuc))
{
    echo '
    <tr class="liste-veri" bgcolor="'.$yazi_tabani1.'" onMouseOver="this.bgColor= \''.$yazi_tabani2.'\'" onMouseOut="this.bgColor= \''.$yazi_tabani1.'\'">
    <td align="left" class="liste-etiket">'.$show_status[0].'</td>
    <td align="left" class="liste-veri" width="40%">'.$show_status[1].'</td>
    </tr>';  
}
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