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
$sayfa_adi = 'Y�netim MySQL Bilgileri';
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

//  MYSQL SUNUCUNUN �ALI�MA S�RES� ALINIYOR //

$strSQL = "SHOW STATUS LIKE 'Uptime'";

$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');
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
<b> &nbsp; MySQL sunucu �al��ma s�resi: &nbsp; </b>'.$gun.' g�n, '.$saat.' saat, '.$dakika.' dakika ve '.$saniye.' saniye

<p><b> &nbsp; MySQL sunucu ba�lama zaman�: &nbsp; </b>'.$acilis.'
<br><br><br>
</td>
</tr>
';




//	MySQL B�LG�LER� �EK�L�YOR	//

$strSQL = "SHOW STATUS";

$sonuc = @mysql_query($strSQL) or die ('<h2>Sorgu ba�ar�s�z</h2>');

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