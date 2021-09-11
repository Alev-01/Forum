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


//  FORM DOLU ÝSE  //

if ((isset($_POST['kayit_yapildi_mi'])) AND ($_POST['kayit_yapildi_mi'] == 'form_dolu')):


// tüm alanlara bakýlýyor

if ( (!$_POST['kullanici_adi']) OR (!$_POST['gercek_ad']) OR (!$_POST['posta']) OR (!$_POST['sifre']) OR (!$_POST['sifre2']) OR (!$_POST['dogum_gun']) or (!$_POST['dogum_ay']) or (!$_POST['dogum_yil']) OR (!$_POST['sehir']) )
{
	header('Location: ../hata.php?hata=26');
	exit();
}


if (!preg_match('/^[A-Za-z0-9-_ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['kullanici_adi']))
{
	header('Location: ../hata.php?hata=27');
	exit();
}

if (( strlen($_POST['kullanici_adi']) > 20) OR ( strlen($_POST['kullanici_adi']) < 4))
{
	header('Location: ../hata.php?hata=28');
	exit();
}


if (!preg_match('/^[A-Za-z0-9-_ ðÐüÜÞþÝýÖöÇç.]+$/', $_POST['gercek_ad']))
{
	header('Location: ../Hata.php?hata=31');
    exit();
}

if ( ( strlen($_POST['gercek_ad']) > 30) OR ( strlen($_POST['gercek_ad']) < 4) )
{
	header('Location: ../hata.php?hata=32');
	exit();
}


if ($_POST['sifre'] != $_POST['sifre2'])
{
	header('Location: ../hata.php?hata=33');
	exit();
}

if (!preg_match('/^[A-Za-z0-9-_.&]+$/', $_POST['sifre']))
{
	header('Location: ../hata.php?hata=34');
	exit();
}

if (( strlen($_POST['sifre']) > 20) OR ( strlen($_POST['sifre']) < 5))
{
	header('Location: ../hata.php?hata=35');
	exit();
}


if ((!preg_match('/^[A-Za-zðÐüÜÞþÝýÖöÇç]+$/', $_POST['sehir'])) OR ( strlen($_POST['sehir']) >  15))
{
	header('Location: ../hata.php?hata=36');
	exit();
}


if ((!preg_match('/^[0-9]+$/', $_POST['dogum_gun'])) OR ( strlen($_POST['dogum_gun']) != 2))
{
	header('Location: ../hata.php?hata=37');
	exit();
}

if ((!preg_match('/^[0-9]+$/', $_POST['dogum_ay'])) OR ( strlen($_POST['dogum_ay']) != 2))
{
	header('Location: ../hata.php?hata=37');
	exit();
}

if ((!preg_match('/^[0-9]+$/', $_POST['dogum_yil'])) OR ( strlen($_POST['dogum_yil']) != 4))
{
	header('Location: ../hata.php?hata=38');
	exit();
}


if ( strlen($_POST['posta']) > 70)
{
	header('Location: ../hata.php?hata=40');
	exit();
}

if (!preg_match('/^([~&+.0-9a-z_-]+)@(([~&+0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $_POST['posta']))
{
	header('Location: ../hata.php?hata=10');
	exit();
}

if (($_POST['yetki'] != '0') AND ($_POST['yetki'] != '1') AND ($_POST['yetki'] != '2'))
{
	header('Location: ../hata.php?hata=148');
	exit();
}



// e-posta gizleme

if (isset($_POST['eposta_gizle'])) $posta_goster = 0;
else $posta_goster = 1;

$_POST['posta'] = mysql_real_escape_string($_POST['posta']);

$dogum_tarihi = $_POST['dogum_gun'].'-'.$_POST['dogum_ay'].'-'.$_POST['dogum_yil'];

$tarih = time();


// anahtar deðeri þifreyle karýþtýrýlarak sha1 ile kodlanýyor
$karma = sha1(($anahtar.$_POST['sifre']));


// kullanýcý adýnýn daha önce alýnýp alýnmadýðýna bakýlýyor

$strSQL = "SELECT kullanici_adi FROM $tablo_kullanicilar WHERE kullanici_adi='$_POST[kullanici_adi]'";
$sonuc = mysql_query($strSQL);


// e-posta adresi ile daha önce kayýt yapýlýp yapýlmadýðýna bakýlýyor

$strSQL = "SELECT posta FROM $tablo_kullanicilar WHERE posta='$_POST[posta]'";
$sonuc2 = mysql_query($strSQL);

if (mysql_num_rows($sonuc))
{
	header('Location: ../hata.php?hata=42');
	exit();
}

elseif (mysql_num_rows($sonuc2))
{
	header('Location: ../hata.php?hata=43');
	exit();
}




//  ÜYE KAYDI YAPILIYOR  //

$strSQL = "INSERT INTO $tablo_kullanicilar (kullanici_adi, sifre, posta, posta_goster, gercek_ad, dogum_tarihi, katilim_tarihi, sehir, kul_etkin, son_giris, son_hareket, kul_ip, yetki, hangi_sayfada,sayfano)";
$strSQL .= "VALUES ('$_POST[kullanici_adi]','$karma','$_POST[posta]','$posta_goster','$_POST[gercek_ad]','$dogum_tarihi','$tarih','$_POST[sehir]','1','$tarih','$tarih','$_SERVER[REMOTE_ADDR]', '$_POST[yetki]', 'Kullanýcý çýkýþ yaptý', '-1')";
$sonuc3 = mysql_query($strSQL) or die ('<h2>sorgu baþarýsýz</h2>');

$kulid = mysql_insert_id();


header('Location: ../hata.php?bilgi=48&fno='.$kulid);
exit();


endif;



$sayfa_adi = 'Yönetim Yeni Üye Oluþturma';
include 'yonetim_baslik.php';

?>


<script type="text/javascript"><!-- //
//  php Kolay Forum (phpKF)
//  =======================
//  Telif - Copyright (c) 2007 - 2012 Adem YILMAZ
//  http://www.phpkf.com   -   phpkf @ phpkf.com
//  Tüm haklarý saklýdýr - All Rights Reserved

function denetle(){
var dogruMu = true;
for (var i=0; i<13; i++){
	if (document.form1.elements[i].value == ''){
		dogruMu = false;
		alert('TÜM ALANLARIN DOLDURULMASI ZORUNLUDUR !');
		break;}}
if (document.form1.sifre.value != document.form1.sifre2.value){
	dogruMu = false;
	alert('YAZDIÐINIZ ÞÝFRELER UYUÞMUYOR !');}
return dogruMu;}
function Temizle(){
document.getElementById('kullanici_adi_alan2').innerHTML = '<a href="javascript:void(0);" onclick="KAdi()">Kontrol Et</a>';}
function GonderAl(adres,katman){
var katman1 = document.getElementById(katman);
var veri_yolla = "name=value";
if (document.all) var istek = new ActiveXObject("Microsoft.XMLHTTP");
else var istek = new XMLHttpRequest();
istek.open("GET", adres, true);
istek.onreadystatechange = function(){
if (istek.readyState == 4){
	if (istek.status == 200) katman1.innerHTML = istek.responseText;
	else katman1.innerHTML = "<b>Baðlantý Kurulamadý !</b>";}};
istek.send(veri_yolla);}
function KAdi(){
var veri = document.form1.kullanici_adi.value;
if(veri != ''){
var adres = "../kayit.php?kosul=kadi&kadi="+veri;
var katman = "kullanici_adi_alan2";
var katman1 = document.getElementById(katman);
katman1.innerHTML = '<img src="../dosyalar/yukleniyor.gif" width="18" height="18" alt="Yü." title="Yükleniyor...">';
setTimeout("GonderAl('"+adres+"','"+katman+"')",1000);}}
// -->
</script>


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
- Yeni Üye Oluþturma -

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


<form action="yeni_uye.php" method="post" onsubmit="return denetle()" name="form1">
<input type="hidden" name="kayit_yapildi_mi" value="form_dolu">


<table cellspacing="1" width="77%" cellpadding="5" border="0" align="right" class="tablo_border4">
	<tbody>
	<tr>
	<td class="forum_baslik" bgcolor="#0099ff" colspan="2" align="center" valign="middle" height="23">
Üye Bilgileri
	</td>
	</tr>


	<tr class="tablo_ici">
	<td colspan="2" class="liste-veri" align="left" valign="bottom" height="45">
<font size="1">
<i>Tüm alanlarýn doldurulmasý zorunludur!</i>
</font>
	</td>
	</tr>

	<tr class="liste-etiket">
	<td align="left" width="40%" height="40" class="tablo_ici">
Kullanýcý Adý:
	</td>

	<td align="left" width="60%" height="40" class="tablo_ici">
<div style="float: left; position: relative;">
<input type="text" class="formlar" name="kullanici_adi" size="35" style="width: 220px" maxlength="20" value="" onkeyup="javascript:Temizle()" onblur="KAdi()"> &nbsp; </div>

<div style="float: left; width: 20px; height: 20px; position: relative;" id="kullanici_adi_alan"></div><br>
<div style="float: left; width: 250px; height: 18px; position: relative; top: 5px; font-size:10px; color: #ff0000" id="kullanici_adi_alan2">
<a href="javascript:void(0);" onclick="KAdi()">Kontrol Et</a>
</div>
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Ad Soyad - Lâkap:
	</td>

	<td align="left" height="40" class="tablo_ici">
<input type="text" class="formlar" name="gercek_ad" size="35" style="width: 220px" maxlength="30" value="">
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
E-Posta Adresi:
	</td>

	<td align="left" height="40" class="tablo_ici">
<input type="text" class="formlar" name="posta" size="35" style="width: 220px" maxlength="70" value=""><br><label style="cursor: pointer;"><input type="checkbox" name="eposta_gizle">
<font style="font-size: 11px; font-weight: normal; font-style: italic;">E-Posta adresini gizle</font></label>
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Þifre: 
	</td>

	<td align="left" height="40" class="tablo_ici">
<input type="password" class="formlar" name="sifre" size="35" style="width: 220px" maxlength="20" value="">
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Þifre Onay: 
	</td>

	<td align="left" height="40" class="tablo_ici">
<input type="password" class="formlar" name="sifre2" size="35" style="width: 220px" maxlength="20" value="">
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Doðum Tarihi:
<br><font style="font-weight: normal;" size="1">
<i>örn.01 ocak 1981</i>
</font>
	</td>

	<td align="left" height="40" class="tablo_ici">
<select class="formlar" name="dogum_gun">
<option value="" selected="selected">-Gün-</option>
	<option value="01">1</option>
	<option value="02">2</option>
	<option value="03">3</option>
	<option value="04">4</option>
	<option value="05">5</option>
	<option value="06">6</option>
	<option value="07">7</option>
	<option value="08">8</option>
	<option value="09">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
</select>&nbsp;


<select class="formlar" name="dogum_ay">
	<option value="" selected="selected"> &nbsp; - Ay -</option>
	<option value="01">Ocak</option>
	<option value="02" >&#350;ubat</option>
	<option value="03">Mart</option>
	<option value="04">Nisan</option>
	<option value="05">May&#305;s</option>
	<option value="06">Haziran</option>
	<option value="07">Temmuz</option>
	<option value="08">A&#287;ustos</option>
	<option value="09">Eylül</option>
	<option value="10">Ekim</option>
	<option value="11">Kasým</option>
	<option value="12">Aral&#305;k</option>
</select>&nbsp;

<input type="text" class="formlar" name="dogum_yil" size="10" style="width: 55px" maxlength="4" value="">

	</td>
	</tr>



	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Bulunduðu Þehir:
	</td>

	<td align="left" height="40" class="tablo_ici">
<select class="formlar" name="sehir">
	<option value="YurtDýþý" selected="selected">Yurt Dýþý</option>
	<option value="Adana">Adana</option>
	<option value="Adýyaman">Adýyaman</option>
	<option value="Afyon">Afyon</option>
	<option value="Aðrý">Aðrý</option>
	<option value="Aksaray">Aksaray</option>
	<option value="Amasya">Amasya</option>
	<option value="Ankara">Ankara</option>
	<option value="Antalya">Antalya</option>
	<option value="Ardahan">Ardahan</option>
	<option value="Artvin">Artvin</option>
	<option value="Aydýn">Aydýn</option>
	<option value="Balýkesir">Balýkesir</option>
	<option value="Batman">Batman</option>
	<option value="Bartýn">Bartýn</option>
	<option value="Bayburt">Bayburt</option>
	<option value="Bilecik">Bilecik</option>
	<option value="Bingöl">Bing&#246;l</option>
	<option value="Bitlis">Bitlis</option>
	<option value="Bolu">Bolu</option>
	<option value="Burdur">Burdur</option>
	<option value="Bursa">Bursa</option>
	<option value="Çanakkale">&#199;anakkale</option>
	<option value="Çankýrý">&#199;ankýrý</option>
	<option value="Çorum">&#199;orum</option>
	<option value="Denizli">Denizli</option>
	<option value="Diyarbakýr">Diyarbakýr</option>
	<option value="Düzce">D&#252;zce</option>
	<option value="Edirne">Edirne</option>
	<option value="Elazýð">Elazýð</option>
	<option value="Erzincan">Erzincan</option>
	<option value="Erzurum">Erzurum</option>
	<option value="Eskiþehir">Eskiþehir</option>
	<option value="Gaziantep">Gaziantep</option>
	<option value="Giresun">Giresun</option>
	<option value="Gümüþhane">G&#252;m&#252;þhane</option>
	<option value="Hakkari">Hakkari</option>
	<option value="Hatay">Hatay</option>
	<option value="Iðdýr">Iðdýr</option>
	<option value="Isparta">Isparta</option>
	<option value="Ýçel">Ý&#231;el</option>
	<option value="Ýstanbul">Ýstanbul</option>
	<option value="Ýzmir">Ýzmir</option>
	<option value="Kars">Kars</option>
	<option value="Kastamonu">Kastamonu</option>
	<option value="Kayseri">Kayseri</option>
	<option value="Karaman">Karaman</option>
	<option value="Karabük">Karab&#252;k</option>
	<option value="Kýrklareli">Kýrklareli</option>
	<option value="Kýrþehir">Kýrþehir</option>
	<option value="Kýrýkkale">Kýrýkkale</option>
	<option value="Kilis">Kilis</option>
	<option value="Kocaeli">Kocaeli</option>
	<option value="Konya">Konya</option>
	<option value="Kütahya">K&#252;tahya</option>
	<option value="Malatya">Malatya</option>
	<option value="Manisa">Manisa</option>
	<option value="KahramanMaraþ">K.Maraþ</option>
	<option value="Mardin">Mardin</option>
	<option value="Muðla">Muðla</option>
	<option value="Muþ">Muþ</option>
	<option value="Nevþehir">Nevþehir</option>
	<option value="Niðde">Niðde</option>
	<option value="Ordu">Ordu</option>
	<option value="Osmaniye">Osmaniye</option>
	<option value="Rize">Rize</option>
	<option value="Sakarya">Sakarya</option>
	<option value="Samsun">Samsun</option>
	<option value="Siirt">Siirt</option>
	<option value="Sinop">Sinop</option>
	<option value="Sivas">Sivas</option>
	<option value="Þýrnak">Þýrnak</option>
	<option value="Tekirdað">Tekirdað</option>
	<option value="Tokat">Tokat</option>
	<option value="Trabzon">Trabzon</option>
	<option value="Tunceli">Tunceli</option>
	<option value="Þanlýurfa">Þanlýurfa</option>
	<option value="Uþak">Uþak</option>
	<option value="Van">Van</option>
	<option value="Yalova">Yalova</option>
	<option value="Yozgat">Yozgat</option>
	<option value="Zonguldak">Zonguldak</option>
</select>

	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="left" height="40" class="tablo_ici">
Yetkisi:
	</td>

	<td align="left" height="40" class="tablo_ici">
<select class="formlar" name="yetki">
<option value="0" selected="selected">Kayýtlý Kullanýcý</option>
<option value="2">Forum Yardýmcýsý</option>
<option value="1">Forum Yöneticisi</option>
</select>
	</td>
	</tr>


	<tr class="liste-etiket">
	<td align="center" valign="middle" height="50" colspan="2" class="tablo_ici">
<input class="dugme" type="submit" value="Kaydol">
 &nbsp; &nbsp; 
<input class="dugme" type="reset" value="Temizle">
	</td>
	</tr>

</table>
</form>

</td></tr></table>
</td></tr></table>
</td></tr></table>
<tr>
<td align="center" height="15"></td>
</tr>
</table>
</td></tr></table>

<script type="text/javascript">
<!-- //
document.form1.sifre.setAttribute("autocomplete","off");
document.form1.sifre2.setAttribute("autocomplete","off");
//  -->

</script>

<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>