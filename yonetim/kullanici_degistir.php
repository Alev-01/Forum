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


@ini_set('magic_quotes_runtime', 0);

if (empty($_GET['u']))
{
	header('Location: kullanicilar.php');
	exit();
}

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_GERECLER')) include '../gerecler.php';


// oturum kodu
$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];

$_GET['u'] = zkTemizle($_GET['u']);


//	KULLANICININ B�LG�LER� VER�TABANINDAN �EK�L�YOR	//

$strSQL = "SELECT
id,kullanici_adi,gercek_ad,posta,dogum_tarihi,sehir,web,resim,imza,posta_goster,dogum_tarihi_goster,sehir_goster,yetki,gizli,icq,msn,yahoo,aim,skype,temadizini,temadizinip,ozel_ad,grupid
FROM $tablo_kullanicilar WHERE id='$_GET[u]' LIMIT 1";

$sonuc = mysql_query($strSQL);
$satir = mysql_fetch_array($sonuc);


if ($satir['id'] == 1)
{
	header('Location: ../hata.php?hata=147');
	exit();
}


if (empty($satir['kullanici_adi']))
{
    header('Location: ../hata.php?hata=46');
    exit();
}

$sayfa_adi = 'Y�netim Kullan�c� Profilini De�i�tir - '.$satir['kullanici_adi'];
include 'yonetim_baslik.php';
?>

		<!--	FORMUN DOLDURULDU�UNU DENETLEYEN JAVASCRIPT KODU BA�I		-->

<script type="text/javascript">
<!-- //
function denetle()
{ 
	var dogruMu = true;
	for (var i=0; i<14; i++)
	{
		if ((i != 6) && (i != 7))
		{
			if (document.form1.elements[i].value=="")
			{ 
				dogruMu = false; 
				alert("* ��ARETL� B�L�MLER�N DOLDURULMASI ZORUNLUDUR !");
				break
			}
		}
	}

	if (document.form1.ysifre.value != document.form1.ysifre2.value)
	{
		dogruMu = false; 
		alert("YAZDI�INIZ ��FRELER UYU�MUYOR !");
	}
	return dogruMu;
}
//  -->
</script>


		<!--	FORM VE G�ZL� G�R��LER		-->

<form name="form1" action="kullanici_degistir_yap.php?o=<?php echo $o ?>" method="post" enctype="multipart/form-data" onSubmit="return denetle()">
<input type="hidden" name="profil_degisti_mi" value="form_dolu">
<input type="hidden" name="MAX_FILE_SIZE" value="1022999">
<input type="hidden" name="id" value="<?php echo $satir['id'] ?>">




<table cellspacing="1" cellpadding="0" width="630" border="0" align="center" class="tablo_border">
	<tbody>
	<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" class="tablo_border2">
	<tbody>
	<tr>
	<td align="center" valign="top">

<table cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
	<td align="center" class="liste-veri" height="25">
<a href="kullanicilar.php">Etkin �yeler</a>
&nbsp; | &nbsp;
<a href="kullanicilar.php?kip=etkisiz">Etkin Olmayanlar</a>
&nbsp; | &nbsp;
<a href="kullanicilar.php?kip=engelli">Engellenenler</a>
&nbsp; | &nbsp;
<a href="ozel_izinler.php">�zel �zinliler</a>
&nbsp; | &nbsp;
<a href="kul_izinler.php">�ye �zinleri</a>
&nbsp; | &nbsp;
<a href="gruplar.php">Gruplar</a>
	</td>
	</tr>
</table>

<table cellspacing="1" cellpadding="0" width="96%" border="0" class="tablo_border3">
	<tbody>
	<tr>
	<td align="center" valign="top" class="tablo_ici">

<table cellspacing="10" width="100%" cellpadding="0" border="0" align="center" class="tablo_ici">
	<tbody>
	<tr>
	<td class="baslik" colspan="2">
Kullan�c� Profilini De�i�tir
	</td>
	</tr>

	<tr>
	<td colspan="2" height="10"></td>
	</tr>

	<tr>
	<td colspan="2" class="liste-veri" align="left">
<font size="1">
<i>&nbsp;&nbsp; * i�aretli b�l�mlerin doldurulmas� zorunludur!</i>
</font>
	</td>
	</tr>

	<tr>
	<td colspan="2" height="10">

<table cellspacing="1" width="97%" cellpadding="6" border="0" align="center" bgcolor="#e0e0e0">
	<tr class="tablo_ici">
	<td class="liste-etiket" width="240">
	Kullan�c� Ad�:  
	</td>
	<td class="liste-etiket" align="left">
<b><?php echo $satir['kullanici_adi'] ?></b> &nbsp; 
<font size="-2" style="font-weight: normal">
<i>(De�i�tirilemez)</i>
</font>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Yetkisi: &nbsp;<font size="1">*</font>
	</td>
	<td align="left">
<?php
    echo '<input type="hidden" name="eski_yetki" value="'.$satir['yetki'].'">
    <select class="formlar" name="yetki">
    <option value="0"';

    if ($satir['yetki'] == 0) echo ' selected="selected"';
    echo '>Kay�tl� Kullan�c�</option>';

    if ($satir['yetki'] == 3) echo '<option value="3" selected="selected">B�l�m Yard�mc�s�</option>';

    echo '<option value="2"';
    if ($satir['yetki'] == 2) echo ' selected="selected"';
    echo '>Forum Yard�mc�s�</option>';

    echo '<option value="1"';
    if ($satir['yetki'] == 1) echo ' selected="selected"';
    echo '>Forum Y�neticisi</option></select> &nbsp; ';

?>

<a href="kul_izinler.php?kim=<?php echo $satir['kullanici_adi']?>" class="liste-veri">Di�er Yetkiler</a>
	</td>
	</tr>


	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Birincil Grup: &nbsp;<font size="1">*</font><br>
<font size="1" style="font-weight: normal">
<i>Se�ilen grup yetkilendirilmi�se<br>bu se�im �yenin yetkisini etkiler.</i>
</font>
	</td>
	<td align="left">
<select class="formlar" name="grup">

<?php
	// Gruplar�n bilgileri �ekiliyor
	$strSQL = "SELECT id,grup_adi,uyeler FROM $tablo_gruplar ORDER BY id";
	$sonuc_grup = mysql_query($strSQL);

	$grup_secimi = '';
	$grup_secimi2 = '';

	if (mysql_num_rows($sonuc_grup))
	{
		while ($satir_grup = mysql_fetch_assoc($sonuc_grup))
		{
			if ($satir_grup['id'] == $satir['grupid'])
			{
				$grup_secimi .= '<option value="'.$satir_grup['id'].'" selected="selected">'.$satir_grup['grup_adi'].'</option>';
				$grup_secimi2 .= '<option value="'.$satir_grup['id'].'">'.$satir_grup['grup_adi'].'</option>';
				$grup_uyesi = true;
			}

			elseif (preg_match("/$satir[id],/", $satir_grup['uyeler']))
			{
				$grup_secimi .= '<option value="'.$satir_grup['id'].'">'.$satir_grup['grup_adi'].'</option>';
				$grup_secimi2 .= '<option value="'.$satir_grup['id'].'" selected="selected">'.$satir_grup['grup_adi'].'</option>';
				$grup_uyesi = true;
			}

			else
			{
				$grup_secimi .= '<option value="'.$satir_grup['id'].'">'.$satir_grup['grup_adi'].'</option>';
				$grup_secimi2 .= '<option value="'.$satir_grup['id'].'">'.$satir_grup['grup_adi'].'</option>';
				$grup_uyesi = false;
			}
		}
	}

	else
	{
		$grup_secimi .= '<option value="0">Hen�z grup olu�turulmam�� &nbsp;</option>';
		$grup_secimi2 .= '<option value="0">Hen�z grup olu�turulmam�� &nbsp;</option>';
	}

	if ($grup_uyesi == true) echo '<option value="0">Hi�bir gruba dahil de�il &nbsp;</option>'.$grup_secimi.'</select> &nbsp; ';
	else echo '<option value="0" selected="selected">Hi�bir gruba dahil de�il &nbsp;</option>'.$grup_secimi.'</select> &nbsp; ';
?>

	</td>
	</tr>


	<tr class="tablo_ici">
	<td class="liste-etiket" align="left" valign="top">
Ek Gruplar:<br>
<font size="1" style="font-weight: normal">
<i>Bu se�im �yenin yetkisini <u>etkilemez</u>.<br>
CTRL tu�una bas�l� tutarak �oklu se�im yapabilirsiniz.</i>
</font>
	</td>
	<td align="left">
<select class="formlar" name="grupc[]"  multiple="multiple" size="5">

<?php
	echo $grup_secimi2.'</select> &nbsp; ';

?>

	</td>
	</tr>





	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
�zel Ad:
<br>
<font size="1" style="font-weight: normal">
<i>�yeye �zel ad verdi�inizde sadece bilgileri alt�nda g�r�necektir, herhangi bir yetki de�i�ikli�i olmayacakt�r.</i>
</font>
	</td>
	<td align="left">
<input class="formlar" type="text" name="ozel_ad" size="35" maxlength="30" value="<?php echo $satir['ozel_ad'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Ad Soyad - L�kap: &nbsp;<font size="1">*</font>
	</td>
	<td align="left">
<input class="formlar" type="text" name="gercek_ad" size="35" maxlength="30" value="<?php echo $satir['gercek_ad'] ?>">
	</td>
	</tr>
	
	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
�ifre: &nbsp;<font size="1">*</font>
<br>
<font size="1" style="font-weight: normal">
<i>De�i�tirmeyecekseniz dokunmay�n.</i>
</font>
	</td>
	<td align="left">
<input class="formlar" type="password" name="ysifre" size="35" maxlength="20" value="sifre_degismedi">
	</td>
	</tr>
	
	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
�ifre Onay: &nbsp;<font size="1">*</font>
	</td>
	<td align="left">
<input class="formlar" type="password" name="ysifre2" size="35" maxlength="20" value="sifre_degismedi">

<script type="text/javascript">
<!-- //
document.form1.ysifre.setAttribute("autocomplete","off"); 
document.form1.ysifre2.setAttribute("autocomplete","off"); 
//  -->
</script>

	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
E-Posta Adresi:  &nbsp;<font size="1">*</font>
	</td>
	<td align="left">
<input class="formlar" type="text" name="posta" size="35" maxlength="70" value="<?php echo $satir['posta'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Do�um Tarihi: &nbsp;<font size="1">*</font>
<font size="1" style="font-weight: normal">
<br><i>�rn.01-01-1981</i>
</font>
	</td>
	<td align="left">
<input class="formlar" type="text" name="dogum_tarihi" size="10" maxlength="10" value="<?php echo $satir['dogum_tarihi'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Bulundu�u �ehir: &nbsp;<font size="1">*</font>
	</td>
	<td align="left">
<select class="formlar" name="sehir">
<option value="<?php echo $satir['sehir'] ?>"><?php echo $satir['sehir'] ?></option>
			<option value="YurtD���">Yurt D���</option>
			<option value="Adana">Adana</option>
			<option value="Ad�yaman">Ad�yaman</option>
			<option value="Afyon">Afyon</option>
			<option value="A�r�">A�r�</option>
			<option value="Aksaray">Aksaray</option>
			<option value="Amasya">Amasya</option>
			<option value="Ankara">Ankara</option>
			<option value="Antalya">Antalya</option>
			<option value="Ardahan">Ardahan</option>
			<option value="Artvin">Artvin</option>
			<option value="Ayd�n">Ayd�n</option>
			<option value="Bal�kesir">Bal�kesir</option>
			<option value="Batman">Batman</option>
			<option value="Bart�n">Bart�n</option>
			<option value="Bayburt">Bayburt</option>
			<option value="Bilecik">Bilecik</option>
			<option value="Bing�l">Bing&#246;l</option>
			<option value="Bitlis">Bitlis</option>
			<option value="Bolu">Bolu</option>
			<option value="Burdur">Burdur</option>
			<option value="Bursa">Bursa</option>
			<option value="�anakkale">&#199;anakkale</option>
			<option value="�ank�r�">&#199;ank�r�</option>
			<option value="�orum">&#199;orum</option>
			<option value="Denizli">Denizli</option>
			<option value="Diyarbak�r">Diyarbak�r</option>
			<option value="D�zce">D&#252;zce</option>
			<option value="Edirne">Edirne</option>
			<option value="Elaz��">Elaz��</option>
			<option value="Erzincan">Erzincan</option>
			<option value="Erzurum">Erzurum</option>
			<option value="Eski�ehir">Eski�ehir</option>
			<option value="Gaziantep">Gaziantep</option>
			<option value="Giresun">Giresun</option>
			<option value="G�m��hane">G&#252;m&#252;�hane</option>
			<option value="Hakkari">Hakkari</option>
			<option value="Hatay">Hatay</option>
			<option value="I�d�r">I�d�r</option>
			<option value="Isparta">Isparta</option>
			<option value="��el">�&#231;el</option>
			<option value="�stanbul">�stanbul</option>
			<option value="�zmir">�zmir</option>
			<option value="Kars">Kars</option>
			<option value="Kastamonu">Kastamonu</option>
			<option value="Kayseri">Kayseri</option>
			<option value="Karaman">Karaman</option>
			<option value="Karab�k">Karab&#252;k</option>
			<option value="K�rklareli">K�rklareli</option>
			<option value="K�r�ehir">K�r�ehir</option>
			<option value="K�r�kkale">K�r�kkale</option>
			<option value="Kilis">Kilis</option>
			<option value="Kocaeli">Kocaeli</option>
			<option value="Konya">Konya</option>
			<option value="K�tahya">K&#252;tahya</option>
			<option value="Malatya">Malatya</option>
			<option value="Manisa">Manisa</option>
			<option value="KahramanMara�">K.Mara�</option>
			<option value="Mardin">Mardin</option>
			<option value="Mu�la">Mu�la</option>
			<option value="Mu�">Mu�</option>
			<option value="Nev�ehir">Nev�ehir</option>
			<option value="Ni�de">Ni�de</option>
			<option value="Ordu">Ordu</option>
			<option value="Osmaniye">Osmaniye</option>
			<option value="Rize">Rize</option>
			<option value="Sakarya">Sakarya</option>
			<option value="Samsun">Samsun</option>
			<option value="Siirt">Siirt</option>
			<option value="Sinop">Sinop</option>
			<option value="Sivas">Sivas</option>
			<option value="��rnak">��rnak</option>
			<option value="Tekirda�">Tekirda�</option>
			<option value="Tokat">Tokat</option>
			<option value="Trabzon">Trabzon</option>
			<option value="Tunceli">Tunceli</option>
			<option value="�anl�urfa">�anl�urfa</option>
			<option value="U�ak">U�ak</option>
			<option value="Van">Van</option>
			<option value="Yalova">Yalova</option>
			<option value="Yozgat">Yozgat</option>
			<option value="Zonguldak">Zonguldak</option>
</select>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Web Adresi:
	</td>
	<td align="left">
<input class="formlar" type="text" name="web" size="35" maxlength="70" value="<?php echo $satir['web'] ?>">
	</td>
	</tr>


<?php




echo'
	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Se�ilebilir Forum Temalar�: 
	</td>
	<td align="left">';


$temalar = explode(',',$ayarlar['tema_secenek']);

$adet = count($temalar);

$uye_tema = '<select class="formlar" name="tema_secim">';


for ($i=0; $adet-1 > $i; $i++)
{
	if ($satir['temadizini'] != $temalar[$i])
		$uye_tema .= '<option value="'.$temalar[$i].'">'.$temalar[$i].'</option>';
	
	else $uye_tema .= '<option value="'.$temalar[$i].'" selected="selected">'.$temalar[$i].'</option>';
}

$uye_tema .= '</select>';

echo $uye_tema.'


	</td>
	</tr>';


// portal tema se�imi alan�

if ($portal_kullan == '1')
{
	$tablo_portal_ayarlar = $tablo_oneki.'portal_ayarlar';

	$strSQL = "SELECT * FROM $tablo_portal_ayarlar where isim='tema_secenek' LIMIT 1";
	$pt_sonuc = @mysql_query($strSQL);
	$portal_temalari = mysql_fetch_assoc($pt_sonuc);


    $ptemalar = explode(',',$portal_temalari['sayi']);
	$adet = count($ptemalar);

	$uye_portal_tema = '
	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
	Se�ilebilir Portal Temalar�: 
	</td>
	<td align="left">
	<select class="formlar" name="tema_secimp">';

	for ($i=0; $adet-1 > $i; $i++)
	{
		if ($satir['temadizinip'] != $ptemalar[$i])
			$uye_portal_tema .= '<option value="'.$ptemalar[$i].'">'.$ptemalar[$i].'</option>';
	
		else $uye_portal_tema .= '<option value="'.$ptemalar[$i].'" selected="selected">'.$ptemalar[$i].'</option>';
	}

	$uye_portal_tema .= '
	</select>
	</td>
	</tr>';

	echo $uye_portal_tema;
}

?>


	<tr class="tablo_ici">
	<td class="liste-etiket" valign="top" align="left">
Kullan�c� �mzas�:
<br><font size="1" style="font-weight: normal">
�mza en fazla <?php echo $ayarlar['imza_uzunluk'] ?> karakter olabilir.<br>
<i>BBCode kullanabilirsiniz.</i>
</font>
<br><br><br><br><br><br>
<div id="imza_uzunluk" style="font-weight: normal">Eklenebilir Karakter: </div>
	</td>
	<td class="liste-etiket" align="left">
<textarea class="formlar" cols="39" rows="9" name="imza" onkeyup="imzaUzunluk()">
<?php

echo $satir['imza'];
echo '</textarea><br>

<script type="text/javascript">
<!-- //
function imzaUzunluk()
{
	var div_katman = document.getElementById(\'imza_uzunluk\');
	div_katman.innerHTML = \'Eklenebilir Karakter: \' + ('.$ayarlar['imza_uzunluk'].'-document.form1.imza.value.length);

	if (document.form1.imza.value.length > '.$ayarlar['imza_uzunluk'].')
	{
        alert(\'En fazla '.$ayarlar['imza_uzunluk'].' karakter girebilirsiniz.\');
        document.form1.imza.value = document.form1.imza.value.substr(0,'.$ayarlar['imza_uzunluk'].');
        div_katman.innerHTML = \'Eklenebilir Karakter: 0\';
	}
	return true;
}
imzaUzunluk();
//  -->
</script>';

?>
	</td>
	</tr>






	<tr>
	<td height="20" colspan="2" class="forum_baslik" align="center">
ANINDA MESAJLA�MA ADRESLER�
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
ICQ Numaran�z:
	</td>
	<td align="left">
<input class="formlar" type="text" name="icq" size="35" maxlength="30" value="<?php echo $satir['icq'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
AIM Ad�n�z:
	</td>
	<td align="left">
<input class="formlar" type="text" name="aim" size="35" maxlength="70" value="<?php echo $satir['aim'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
MSN Ad�n�z:
	</td>
	<td align="left">
<input class="formlar" type="text" name="msn" size="35" maxlength="70" value="<?php echo $satir['msn'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Yahoo! Messenger Ad�n�z:
	</td>
	<td align="left">
<input class="formlar" type="text" name="yahoo" size="35" maxlength="70" value="<?php echo $satir['yahoo'] ?>">
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Skype Ad�n�z:
	</td>
	<td align="left">
<input class="formlar" type="text" name="skype" size="35" maxlength="70" value="<?php echo $satir['skype'] ?>">
	</td>
	</tr>





<?php
if ( ($ayarlar['uzak_resim'] == 1) OR ($ayarlar['resim_yukle'] == 1) OR
	($ayarlar['resim_galerisi'] == 1) ):
?>


	<tr>
	<td height="20" colspan="2" class="forum_baslik" align="center">
KULLANICI RESM� AYARLARI
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-veri" colspan="2" align="left">
Resim sadece jpeg, gif veya png tipinde olabilir.<br>
<?php echo 'Dosya <b>boyutu '.($ayarlar['resim_boyut']/1024).'</b> kilobayt, <b>y�ksekli�i '.$ayarlar['resim_yukseklik'].'</b> ve <b>geni�li�i '.$ayarlar['resim_genislik'].'</b> noktadan b�y�k olamaz.'; ?>
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" valign="top" align="left">Ge�erli Resim:</td>
	<td class="liste-veri" align="left">
<?php
if ( (isset($_POST['secim_yap'])) AND
	(isset($_POST['galeri_resimi'])) AND
	($_POST['galeri_resimi'] != '') )
echo '<img src="../'.$_POST['galeri_resimi'].'" alt="Kullan�c� Resmi">&nbsp;
<label style="cursor: pointer;">
<input type="checkbox" name="resim_sil">Ge�erli Resimi Sil</label>';

elseif ($satir['resim'])
{
	if ( (preg_match('/^http:\/\//i', $satir['resim'])) OR
	(preg_match('/^ftp:\/\//i', $satir['resim'])) )
	
		echo '<img src="'.$satir['resim'].'" alt="Kullan�c� Resmi">&nbsp;
		<label style="cursor: pointer;">
		<input type="checkbox" name="resim_sil">Ge�erli Resimi Sil</label>';


	else

		echo '<img src="../'.$satir['resim'].'" alt="Kullan�c� Resmi">&nbsp;
		<label style="cursor: pointer;">
		<input type="checkbox" name="resim_sil">Ge�erli Resimi Sil</label>';
}

else echo 'YOK';
?>
	</td>
	</tr>

<?php if ($ayarlar['resim_yukle'] == 1): ?>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Resim Y�kle:
<br><font size="1" style="font-weight: normal">
<i>Bilgisayar�n�zdan resim y�kleyin.</i>
</font>
	</td>
	<td align="left">
<input class="formlar" name="resim_yukle" type="file" size="30">
	</td>
	</tr>


<?php endif; if ($ayarlar['uzak_resim'] == 1): ?>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Uzak Resim Ekle:
<br><font size="1" style="font-weight: normal">
<i>Ba�ka sitede bulunan resimin adresini girin.</i>
</font>
	</td>
	<td align="left">
<input class="formlar" type="text" name="uzak_resim" size="40" maxlength="150" value="">
	</td>
	</tr>


<?php endif; if ($ayarlar['resim_galerisi'] == 1): ?>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Galeriden Resim Se�:
	</td>
	<td class="liste-veri" align="left">
<a href="../galeri.php?kim=<?php echo $satir['id'] ?>"><u>Galeriyi G�ster</u></a>
<input class="formlar" type="hidden" name="uzak_resim2" size="40" maxlength="150" value="<?php

if ( (isset($_POST['secim_yap'])) AND
	(isset($_POST['galeri_resimi'])) AND
	($_POST['galeri_resimi'] != '') )

echo $_POST['galeri_resimi'];

?>">
	</td>
	</tr>

<?php endif; endif; ?>







	<tr>
	<td height="20" colspan="2" class="forum_baslik" align="center">
SE�ENEKLER
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
E-Posta Adresini G�ster: 
	</td>
	<td class="liste-veri" align="left">
<input type=radio name="posta_goster" value="1" <?php if ($satir['posta_goster'] == 1) echo 'checked=checked' ?>>
Evet&nbsp;&nbsp;
<input type="radio" name="posta_goster" value="0" <?php if ($satir['posta_goster'] == 0) echo 'checked=checked' ?>>
Hay�r
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
Do�um Tarihini G�ster: 
	</td>
	<td class="liste-veri" align="left">
<input type="radio" name="dogum_tarihi_goster" value="1" <?php if ($satir['dogum_tarihi_goster'] == 1) echo 'checked=checked' ?>>
Evet&nbsp;&nbsp;
<input type="radio" name="dogum_tarihi_goster" value="0" <?php if ($satir['dogum_tarihi_goster'] == 0) echo 'checked=checked' ?>>
Hay�r
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
�ehir G�ster: 
	</td>
	<td class="liste-veri" align="left">
<input type="radio" name="sehir_goster" value="1" <?php if ($satir['sehir_goster'] == 1) echo 'checked=checked' ?>>
Evet&nbsp;&nbsp;
<input type="radio" name="sehir_goster" value="0" <?php if ($satir['sehir_goster'] == 0) echo 'checked=checked' ?>>
Hay�r
	</td>
	</tr>

	<tr class="tablo_ici">
	<td class="liste-etiket" align="left">
�evrimi�i Durumunu G�ster: 
	</td>
	<td class="liste-veri" align="left">
<input type="radio" name="gizli" value="0" <?php if($satir['gizli'] == 0) echo 'checked=checked' ?>>
Evet&nbsp;&nbsp;
<input type="radio" name="gizli" value="1" <?php if($satir['gizli'] == 1) echo 'checked=checked' ?>>
Hay�r
	</td>
	</tr>

</table>

	<tr class="tablo_ici">
	<td height="10"></td>
	</tr>

	<tr>
	<td class="tablo_ici" align="center">

<input class="dugme" type="submit" value="De�i�tir"> &nbsp; &nbsp; 
<input class="dugme" type="reset">

<br>
</td></tr></table>
</td></tr></table>
<br>
</td></tr></table>
</td></tr></table>
</form>
<?php
$ornek1 = new phpkf_tema();
include 'son.php';
?>