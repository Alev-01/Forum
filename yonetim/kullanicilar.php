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

if (empty($_GET['sayfa'])) $_GET['sayfa'] = 0;
else $_GET['sayfa'] = @zkTemizle($_GET['sayfa']);

if (empty($_GET['sirala'])) $_GET['sirala'] = 1;
else $_GET['sirala'] = @zkTemizle($_GET['sirala']);

if (empty($_GET['kullanici'])) $_GET['kullanici'] = 1;
else $_GET['kullanici'] = @zkTemizle($_GET['kullanici']);

if (empty($_GET['kul_id'])) $_GET['kul_id'] = 0;
else $_GET['kul_id'] = @zkTemizle($_GET['kul_id']);

if (empty($_GET['kul_ara'])) $_GET['kul_ara'] = '%';
else
{
	$_GET['kul_ara'] = @zkTemizle(trim($_GET['kul_ara']));
	$_GET['kul_ara'] = @str_replace('*','%',$_GET['kul_ara']);
}



// OTURUM KODU ��LEMLER�  //

if (isset($_GET['o'])) $_GET['o'] = @zkTemizle($_GET['o']);
else $_GET['o'] = '';

$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


//		KULLANICI ETK�S�ZLE�T�RME		 //

if ($_GET['kullanici'] == 'etkisiz')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	if ($_GET['kul_id'] == 1)
	{
		header('Location: ../hata.php?hata=61');
		exit();
	}


	$strSQL = "UPDATE $tablo_kullanicilar SET kul_etkin='0', kullanici_kimlik='', yonetim_kimlik='' WHERE id='$_GET[kul_id]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: ../hata.php?bilgi=33');
	exit();
}



//		KULLANICI ETK�NLE�T�RME		 //

if ($_GET['kullanici'] == 'etkin')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}


	$strSQL = "UPDATE $tablo_kullanicilar SET kul_etkin='1',kul_etkin_kod='0' WHERE id='$_GET[kul_id]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: ../hata.php?bilgi=25');
	exit();
}



//		KULLANICI S�LME		//

if ($_GET['kullanici'] == 'sil')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	if ($_GET['kul_id'] == 1)
	{
		header('Location: ../hata.php?hata=137');
		exit();
	}


	$strSQL = "DELETE FROM $tablo_kullanicilar WHERE id='$_GET[kul_id]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	if ((isset($_GET['kip'])) AND ($_GET['kip'] == 'engelli'))
		header('Location: ../hata.php?bilgi=23');

	elseif ((isset($_GET['kip'])) AND ($_GET['kip'] == 'etkisiz'))
		header('Location: ../hata.php?bilgi=26');

	else header('Location: ../hata.php?bilgi=34');
	exit();
}



//		KULLANICI ENGELLE		//

if ($_GET['kullanici'] == 'engelle')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	if ($_GET['kul_id'] == 1)
	{
		header('Location: ../hata.php?hata=149');
		exit();
	}


	$strSQL = "UPDATE $tablo_kullanicilar SET engelle='1', kullanici_kimlik='', yonetim_kimlik='' WHERE id='$_GET[kul_id]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: ../hata.php?bilgi=35');
	exit();
}



//		KULLANICI ENGEL�N� KALDIRMA		//

if ($_GET['kullanici'] == 'engel_kaldir')
{
	//  OTURUM B�LG�S�NE BAKILIYOR  //
	if ($_GET['o'] != $o)
	{
		header('Location: ../hata.php?hata=45');
		exit();
	}

	$strSQL = "UPDATE $tablo_kullanicilar SET engelle='0' WHERE id='$_GET[kul_id]' LIMIT 1";
	$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');

	header('Location: ../hata.php?bilgi=24');
	exit();
}








//	K�P SE��M�	//

if ((isset($_GET['kip'])) AND ($_GET['kip'] == 'engelli'))
{
	$eksorgu = "engelle='1' AND kul_etkin='1'";
	$sayfaek = 'kip=engelli&amp;';
	$sayfa_adi = 'Y�netim Engellenmi� Kullan�c�lar';
	$syf_baslik = 'Engellenmi� Kullan�c�lar';
	$sonuc_yok = 'Arad���n�z ko�ula uyan engellenmi� kullan�c� yok !';
	$form_bilgisi = '<form action="kullanicilar.php" name="kul_ara" method="get">
<input type="hidden" name="kip" value="engelli">';
	$uye_alan1 = '';
	$uye_alan2 = 'Engel Kald�r';
	$uye_alan3 = '';
}

elseif ((isset($_GET['kip'])) AND ($_GET['kip'] == 'etkisiz'))
{
	$eksorgu = "kul_etkin='0'";
	$sayfaek = 'kip=etkisiz&amp;';
	$sayfa_adi = 'Y�netim Etkin Olmayan Kullan�c�lar';
	$syf_baslik = 'Etkin Olmayan Kullan�c�lar';
	$sonuc_yok = 'Arad���n�z ko�ula uyan hesab� etkinle�tirilmemi� kullan�c� yok !';
	$form_bilgisi = '<form action="kullanicilar.php" name="kul_ara" method="get">
<input type="hidden" name="kip" value="etkisiz">';
	$uye_alan1 = 'Engelle';
	$uye_alan2 = 'Etkin yap';
	$uye_alan3 = '';
}

else
{
	$eksorgu = "engelle='0' AND kul_etkin='1'";
	$sayfaek = '';
	$sayfa_adi = 'Y�netim Etkin Kullan�c�lar';
	$syf_baslik = 'Etkin Kullan�c�lar';
	$sonuc_yok = 'Arad���n�z ko�ula uyan kullan�c� yok !';
	$form_bilgisi = '<form action="kullanicilar.php" name="kul_ara" method="get">';
	$uye_alan1 = 'Engelle';
	$uye_alan2 = 'Etkisiz yap';
	$uye_alan3 = 'Yetki';
}







//	SORGU SONUCUNDAK� TOPLAM SONU� SAYISI ALINIYOR	//

$result = mysql_query("SELECT id FROM $tablo_kullanicilar WHERE $eksorgu AND kullanici_adi LIKE '$_GET[kul_ara]%'");
$satir_sayi = mysql_num_rows($result);

$uyeler_kota = 30;

$toplam_sayfa = ($satir_sayi / $uyeler_kota);
settype($toplam_sayfa,'integer');

if (($satir_sayi % $uyeler_kota) != 0) $toplam_sayfa++;


//	KULLANICILARIN B�LG�LER� �EK�L�YOR	//

$strSQL = "SELECT id,kullanici_adi,mesaj_sayisi,katilim_tarihi,kul_ip FROM $tablo_kullanicilar WHERE $eksorgu AND kullanici_adi LIKE '$_GET[kul_ara]%' ORDER BY ";

if ($_GET['sirala'] == 'mesaj_0dan9a') $strSQL .= "mesaj_sayisi LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'mesaj_9dan0a') $strSQL .= "mesaj_sayisi DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'katilim_9dan0a') $strSQL .= "id DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'ad_AdanZye') $strSQL .= "kullanici_adi LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'ad_ZdenAya') $strSQL .= "kullanici_adi DESC LIMIT $_GET[sayfa],$uyeler_kota";
elseif ($_GET['sirala'] == 'yetki') $strSQL .= "yetki=0, yetki=3, yetki=2, yetki=1, id LIMIT $_GET[sayfa],$uyeler_kota";
else $strSQL .= "id LIMIT $_GET[sayfa],$uyeler_kota";

$sonuc2 = mysql_query($strSQL) or die ('<h2>sorgu ba�ar�s�z</h2>');




//  SAYFA BA�LIK    //

include 'yonetim_baslik.php';




$siralama_secenek = '<option value="1">Kat�l�m tarihine g�re
<option value="katilim_9dan0a" ';

if ($_GET['sirala'] == 'katilim_9dan0a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kat�l�m tarihine g�re tersten

<option value="ad_AdanZye" ';
if ($_GET['sirala'] == 'ad_AdanZye') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kullan�c� ad�na g�re A\'dan Z\'ye

<option value="ad_ZdenAya" ';
if ($_GET['sirala'] == 'ad_ZdenAya') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Kullan�c� ad�na g�re Z\'den A\'ya

<option value="mesaj_9dan0a" ';
if ($_GET['sirala'] == 'mesaj_9dan0a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>�leti say�s�na g�re

<option value="mesaj_0dan9a" ';
if ($_GET['sirala'] == 'mesaj_0dan9a') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>�leti say�s�na g�re tersten

<option value="yetki" ';
if ($_GET['sirala'] == 'yetki') $siralama_secenek .= 'selected="selected"';
$siralama_secenek .= '>Yetkisine g�re(Y�neticiler �nde)';




//  �YELER�N B�LG�LER� SIRALANIYOR  //

while ($uyeler_satir = mysql_fetch_array($sonuc2)):


$uye_ileti = '<a href="../oi_yaz.php?ozel_kime='.$uyeler_satir['kullanici_adi'].'">ileti</a>';

$uye_adi = '&nbsp;<a href="kullanici_degistir.php?u='.$uyeler_satir['id'].'">'.$uyeler_satir['kullanici_adi'].'</a>';

$uye_katilim = zonedate('d-m-Y', $ayarlar['saat_dilimi'], false, $uyeler_satir['katilim_tarihi']);

$uye_ip = '<a href="ip_yonetimi.php?kip=1&amp;ip='.$uyeler_satir['kul_ip'].'">'.$uyeler_satir['kul_ip'].'</a>';



if ((isset($_GET['kip'])) AND ($_GET['kip'] == 'engelli'))
{
	$uye_etkin = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=engel_kaldir" onclick="return window.confirm(\'Kullan�c� engelini kald�rmak istedi�inize emin misiniz?\')">Engel Kald�r</a>';

	$uye_sil = '<a href="kullanicilar.php?kip='.$_GET['kip'].'&amp;kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=sil" onclick="return window.confirm(\'Kullan�c�y� silmek istedi�inize emin misiniz ?\')">Sil</a>';

	$uye_engel = '&nbsp; &nbsp; &nbsp; ';
	$uye_yetki = '&nbsp; &nbsp; &nbsp; ';
}

elseif ((isset($_GET['kip'])) AND ($_GET['kip'] == 'etkisiz'))
{
	$uye_engel = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=engelle" onclick="return window.confirm(\'Kullan�c�y� engellemek istedi�inize emin misiniz ?\')">Engelle</a>';

	$uye_etkin = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=etkin" onclick="return window.confirm(\'Kullan�c�y� etkinle�tirmek istedi�inize emin misiniz ?\')">Etkin yap</a>';

	$uye_sil = '<a href="kullanicilar.php?kip='.$_GET['kip'].'&amp;kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=sil" onclick="return window.confirm(\'Kullan�c�y� silmek istedi�inize emin misiniz ?\')">Sil</a>';
	$uye_yetki = '&nbsp; &nbsp; &nbsp; ';
}

else
{
	$uye_engel = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=engelle" onclick="return window.confirm(\'Kullan�c�y� engellemek istedi�inize emin misiniz ?\')">Engelle</a>';

	$uye_etkin = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=etkisiz" onclick="return window.confirm(\'Kullan�c�y� etkisizle�tirmek istedi�inize emin misiniz ?\')">Etkisiz yap</a>';

	$uye_sil = '<a href="kullanicilar.php?kul_id='.$uyeler_satir['id'].'&amp;o='.$o.'&amp;kullanici=sil" onclick="return window.confirm(\'Kullan�c�y� silmek istedi�inize emin misiniz ?\')">Sil</a>';
	$uye_yetki = '<a href="kul_izinler.php?kim='.$uyeler_satir['kullanici_adi'].'">�. Yetki</a>';
}


//	veriler tema motoruna yollan�yor	//

$tekli1[] = array('{UYE_ILETI}' => $uye_ileti,
'{UYE_ADI}' => $uye_adi,
'{UYE_MESAJ}' => NumaraBicim($uyeler_satir['mesaj_sayisi']),
'{UYE_KATILIM}' => $uye_katilim,
'{UYE_YETKI}' => $uye_yetki,
'{UYE_IP_ADRESI}' => $uye_ip,
'{UYE_ENGEL}' => $uye_engel,
'{UYE_ETKIN}' => $uye_etkin,
'{UYE_SIL}' => $uye_sil);


endwhile;



//  SAYFALAMA   //

$sayfalama = '';

if ($satir_sayi > $uyeler_kota):

$sayfalama .= '<p>
<table cellspacing="1" cellpadding="2" border="0" align="right" class="tablo_border">
	<tr>
	<td class="forum_baslik">
Toplam '.$toplam_sayfa.' Sayfa:&nbsp;
	</td>
';


if ($_GET['sayfa'] != 0)
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="ilk sayfaya git">';
	$sayfalama .= '&nbsp;<a href="kullanicilar.php?'.$sayfaek.'sayfa=0&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&laquo;ilk</a>&nbsp;</td>';
	
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�nceki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="kullanicilar.php?'.$sayfaek.'sayfa='.($_GET['sayfa'] - $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&lt;</a>&nbsp;</td>';
}

for ($sayi=0,$sayfa_sinir=$_GET['sayfa']; $sayi < $toplam_sayfa; $sayi++)
{
	if ($sayi < (($_GET['sayfa'] / $uyeler_kota) - 3));

	else
	{
		$sayfa_sinir++;
		if ($sayfa_sinir >= ($_GET['sayfa'] + 8)) {break;}
		if (($sayi == 0) and ($_GET['sayfa'] == 0))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>[1]</b>&nbsp;</td>';
		}
	
		elseif (($sayi + 1) == (($_GET['sayfa'] / $uyeler_kota) + 1))
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="�u an bulundu�unuz sayfa">';
			$sayfalama .= '&nbsp;<b>['.($sayi + 1).']</b>&nbsp;</td>';
		}
	
		else
		{
			$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="'.($sayi + 1).' numaral� sayfaya git">';

			$sayfalama .= '&nbsp;<a href="kullanicilar.php?'.$sayfaek.'sayfa='.($sayi * $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">'.($sayi + 1).'</a>&nbsp;</td>';
		}
	}
}
if ($_GET['sayfa'] < ($satir_sayi - $uyeler_kota))
{
	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="sonraki sayfaya git">';
	$sayfalama .= '&nbsp;<a href="kullanicilar.php?'.$sayfaek.'sayfa='.($_GET['sayfa'] + $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">&gt;</a>&nbsp;</td>';

	$sayfalama .= '<td bgcolor="#ffffff" class="liste-veri" title="son sayfaya git">';
	$sayfalama .= '&nbsp;<a href="kullanicilar.php?'.$sayfaek.'sayfa='.(($toplam_sayfa - 1) * $uyeler_kota).'&amp;kul_ara='.$_GET['kul_ara'].'&amp;sirala='.$_GET['sirala'].'">son&raquo;</a>&nbsp;</td>';
}

$sayfalama .= '</tr>
</table>';

endif;




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/kullanicilar.html');


if (isset($tekli1))
{
	$ornek1->kosul('2', array(''=>''), false);
	$ornek1->kosul('1', array(''=>''), true);

	$ornek1->tekli_dongu('1',$tekli1);
}

else
{
	$tekli1[] = array('{UYE_ILETI}' => '',
	'{UYE_ADI}' => '',
	'{UYE_MESAJ}' => '',
	'{UYE_KATILIM}' => '',
	'{UYE_YETKI}' => '',
	'{UYE_ENGEL}' => '',
	'{UYE_ETKIN}' => '',
	'{UYE_SIL}' => '');

	$ornek1->tekli_dongu('1',$tekli1);

	$ornek1->kosul('2', array('{SONUC_YOK}' => $sonuc_yok), true);
	$ornek1->kosul('1', array(''=>''), false);
}


$dongusuz = array('{FORM_BILGISI}' => $form_bilgisi,
'{SAYFA_BASLIK}' => $syf_baslik,
'{KULLANICI_ARA}' => @str_replace('%','*',$_GET['kul_ara']),
'{SIRALAMA_SECENEK}' => $siralama_secenek,
'{SAYFALAMA}' => $sayfalama,
'{UYE_ALAN1}' => $uye_alan1,
'{UYE_ALAN2}' => $uye_alan2,
'{UYE_ALAN3}' => $uye_alan3,
'{ARAMA_SONUC_YAZISI1}' => 'Arad���n�z ko�ula uyan �ye say�s�:',
'{ARAMA_SONUC_YAZISI2}' => '(Engellenmi� ve etkinle�tirilmemi� olanlar hari�)',
'{UYE_SAYISI}' => NumaraBicim($satir_sayi));

$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>