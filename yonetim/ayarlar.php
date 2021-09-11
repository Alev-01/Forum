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


@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include '../ayar.php';
if (!defined('DOSYA_YONETIM_GUVENLIK')) include 'yonetim_guvenlik.php';
if (!defined('DOSYA_KULLANICI_KIMLIK')) include '../kullanici_kimlik.php';


if ($kullanici_kim['id'] != 1)
{
	header('Location: ../hata.php?hata=151');
	exit();
}


$ayarlar_forum_rengi = $ayarlar['forum_rengi'];
include '../temalar/'.$ayarlar['temadizini'].'/yonetim_tema.php';
$ayarlar_t_tema_adi = $t_tema_adi;
$ayarlar_t_renkler = $t_renkler;


// oturum kodu
$o = $satir['yonetim_kimlik'];
$o = $o[3].$o[6].$o[8].$o[10].$o[13].$o[17].$o[19].$o[25].$o[30].$o[33];


$sayfa_adi = 'Yönetim Genel Ayarlar';
include 'yonetim_baslik.php';


//	tema dosyasý açýlýyor	//
function tema_dosyasi($dosya)
{
	if (!($dosya_ac = fopen($dosya,'r')))
		die ('<p><font color="red"><b>Tema Dosyasý Açýlamýyor '.$dosya.'</b></font></p>');

	$boyut = filesize($dosya);
	$dosya_metni = fread($dosya_ac,$boyut);
	fclose($dosya_ac);

	return $dosya_metni;
}



$javascript_kodu = '<script type="text/javascript">
<!-- //
function denetle()
{
	var dogruMu = true;
	for (var i=0; i<66; i++)
	{
		if (document.form1.elements[i].name=="kul_resim") continue;

		if (document.form1.elements[i].value=="")
		{
			dogruMu = false;
			alert("SMTP SUNUCU AYARLARI VE VARSAYILAN KULLANICI RESMÝ HARÝÇ, \nTÜM ALANLARIN DOLDURULMASI ZORUNLUDUR !");
			break;
		}
	}
	return dogruMu;
}
// -->
</script>';




if ($ayarlar['forum_durumu'] == 1) $forum_durumu_acik = 'checked="checked"';
else $forum_durumu_acik = '';

if ($ayarlar['forum_durumu'] == 0) $forum_durumu_kapali = 'checked="checked"';
else $forum_durumu_kapali = '';



if ($ayarlar['kayit_soru'] == 1) $kayit_soru_acik = 'checked="checked"';
else $kayit_soru_acik = '';

if ($ayarlar['kayit_soru'] == 0) $kayit_soru_kapali = 'checked="checked"';
else $kayit_soru_kapali = '';



if ($ayarlar['onay_kodu'] == 1) $kayit_onay_acik = 'checked="checked"';
else $kayit_onay_acik = '';

if ($ayarlar['onay_kodu'] == 0) $kayit_onay_kapali = 'checked="checked"';
else $kayit_onay_kapali = '';




$saat_dilimi = '<select class="formlar" name="saat_dilimi">
<option value="-12"';

if ($ayarlar['saat_dilimi'] == -12) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 12 Saat</option>
<option value="-11"';

if ($ayarlar['saat_dilimi'] == -11) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 11 Saat</option>
<option value="-10"'; 

if ($ayarlar['saat_dilimi'] == -10) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 10 Saat</option>
<option value="-9"';

if ($ayarlar['saat_dilimi'] == -9) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 9 Saat</option>
<option value="-8"';

if ($ayarlar['saat_dilimi'] == -8) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 8 Saat</option>
<option value="-7"';

if ($ayarlar['saat_dilimi'] == -7) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 7 Saat</option>
<option value="-6"';

if ($ayarlar['saat_dilimi'] == -6) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 6 Saat</option>
<option value="-5"';

if ($ayarlar['saat_dilimi'] == -5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 5 Saat</option>
<option value="-4"';

if ($ayarlar['saat_dilimi'] == -4) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 4 Saat</option>
<option value="-3.5"';

if ($ayarlar['saat_dilimi'] == -3.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 3.5 Saat</option>
<option value="-3"';

if ($ayarlar['saat_dilimi'] == -3) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 3 Saat</option>
<option value="-2"';

if ($ayarlar['saat_dilimi'] == -2) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 2 Saat</option>
<option value="-1"';

if ($ayarlar['saat_dilimi'] == -1) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT - 1 Saat</option>
<option value="0"';

if ($ayarlar['saat_dilimi'] == 0) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT</option>
<option value="1"';

if ($ayarlar['saat_dilimi'] == 1) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 1 Saat</option>
<option value="2"';

if ($ayarlar['saat_dilimi'] == 2) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 2 Saat</option>
<option value="3"';

if ($ayarlar['saat_dilimi'] == 3) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 3 Saat</option>
<option value="3.5"';

if ($ayarlar['saat_dilimi'] == 3.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 3.5 Saat</option>
<option value="4"';

if ($ayarlar['saat_dilimi'] == 4) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 4 Saat</option>
<option value="4.5"';

if ($ayarlar['saat_dilimi'] == 4.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 4.5 Saat</option>
<option value="5"';

if ($ayarlar['saat_dilimi'] == 5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 5 Saat</option>
<option value="5.5"';

if ($ayarlar['saat_dilimi'] == 5.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 5.5 Saat</option>
<option value="6"';

if ($ayarlar['saat_dilimi'] == 6) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 6 Saat</option>
<option value="6.5"';

if ($ayarlar['saat_dilimi'] == 6.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 6.5 Saat</option>
<option value="7"';

if ($ayarlar['saat_dilimi'] == 7) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 7 Saat</option>
<option value="8"';

if ($ayarlar['saat_dilimi'] == 8) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 8 Saat</option>
<option value="9"';

if ($ayarlar['saat_dilimi'] == 9) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 9 Saat</option>
<option value="9.5"';

if ($ayarlar['saat_dilimi'] == 9.5) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 9.5 Saat</option>
<option value="10"';

if ($ayarlar['saat_dilimi'] == 10) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 10 Saat</option>
<option value="11"';

if ($ayarlar['saat_dilimi'] == 11) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 11 Saat</option>
<option value="12"';

if ($ayarlar['saat_dilimi'] == 12) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 12 Saat</option>
<option value="13"';

if ($ayarlar['saat_dilimi'] == 13) $saat_dilimi .= ' selected="selected"';
$saat_dilimi .= '>GMT + 13 Saat</option>
</select>';



if (is_array($ayarlar_t_renkler))
{
	$forum_rengi = '<select class="formlar" name="forum_rengi">';

	foreach ($ayarlar_t_renkler as $renkler1=>$renkler2)
	{
		if ($ayarlar_forum_rengi == $renkler2)
			$forum_rengi .= "\r\n".'<option value="'.$renkler2.'" selected="selected">'.$renkler1;
		else $forum_rengi .= "\r\n".'<option value="'.$renkler2.'">'.$renkler1;
	}

	$forum_rengi .= '</select>';
}

else $forum_rengi = '<input type="hidden" name="forum_rengi" value="siyah">'.$ayarlar_t_tema_adi.' için renk seçimi yok.';



if ($ayarlar['hesap_etkin'] == 0) $hesap_etkin_kapali = ' checked="checked"';
else $hesap_etkin_kapali = '';

if ($ayarlar['hesap_etkin'] == 1) $hesap_etkin_kullanici = ' checked="checked"';
else $hesap_etkin_kullanici = '';

if ($ayarlar['hesap_etkin'] == 2) $hesap_etkin_yonetici = ' checked="checked"';
else $hesap_etkin_yonetici = '';



if ($ayarlar['uye_kayit'] == 1) $uye_kayit_acik = 'checked="checked"';
else $uye_kayit_acik = '';

if ($ayarlar['uye_kayit'] == 0) $uye_kayit_kapali = 'checked="checked"';
else $uye_kayit_kapali = '';



if ($ayarlar['bbcode'] == 1) $bbcode_acik = 'checked="checked"';
else $bbcode_acik = '';

if ($ayarlar['bbcode'] == 0) $bbcode_kapali = 'checked="checked"';
else $bbcode_kapali = '';



if ($ayarlar['seo'] == 1) $seo_acik = 'checked="checked"';
else $seo_acik = '';

if ($ayarlar['seo'] == 0) $seo_kapali = 'checked="checked"';
else $seo_kapali = '';



if ($ayarlar['boyutlandirma'] == 1) $boyutlandirma_acik = 'checked="checked"';
else $boyutlandirma_acik = '';

if ($ayarlar['boyutlandirma'] == 0) $boyutlandirma_kapali = 'checked="checked"';
else $boyutlandirma_kapali = '';



if ($ayarlar['bolum_kisi'] == 1) $bolumkisi_acik = 'checked="checked"';
else $bolumkisi_acik = '';

if ($ayarlar['bolum_kisi'] == 0) $bolumkisi_kapali = 'checked="checked"';
else $bolumkisi_kapali = '';



if ($ayarlar['konu_kisi'] == 1) $konukisi_acik = 'checked="checked"';
else $konukisi_acik = '';

if ($ayarlar['konu_kisi'] == 0) $konukisi_kapali = 'checked="checked"';
else $konukisi_kapali = '';



if ($ayarlar['portal_kullan'] == 1) $portal_acik = 'checked="checked"';
else $portal_acik = '';

if ($ayarlar['portal_kullan'] == 0) $portal_kapali = 'checked="checked"';
else $portal_kapali = '';



if ($ayarlar['sonkonular'] == 1) $sonkonular_acik = 'checked="checked"';
else $sonkonular_acik = '';

if ($ayarlar['sonkonular'] == 0) $sonkonular_kapali = 'checked="checked"';
else $sonkonular_kapali = '';



if ($ayarlar['o_ileti'] == 1) $o_ileti_acik = 'checked="checked"';
else $o_ileti_acik = '';

if ($ayarlar['o_ileti'] == 0) $o_ileti_kapali = 'checked="checked"';
else $o_ileti_kapali = '';



if ($ayarlar['oi_uyari'] == 1) $oi_uyari_acik = 'checked="checked"';
else $oi_uyari_acik = '';

if ($ayarlar['oi_uyari'] == 0) $oi_uyari_kapali = 'checked="checked"';
else $oi_uyari_kapali = '';



if ($ayarlar['resim_yukle'] == 1) $resim_yukle_acik = 'checked="checked"';
else $resim_yukle_acik = '';

if ($ayarlar['resim_yukle'] == 0) $resim_yukle_kapali = 'checked="checked"';
else $resim_yukle_kapali = '';



if ($ayarlar['uzak_resim'] == 1) $uzak_resim_acik = 'checked="checked"';
else $uzak_resim_acik = '';

if ($ayarlar['uzak_resim'] == 0) $uzak_resim_kapali = 'checked="checked"';
else $uzak_resim_kapali = '';



if ($ayarlar['resim_galerisi'] == 1) $resim_galerisi_acik = 'checked="checked"';
else $resim_galerisi_acik = '';

if ($ayarlar['resim_galerisi'] == 0) $resim_galerisi_kapali = 'checked="checked"';
else $resim_galerisi_kapali = '';



if ($ayarlar['eposta_yontem'] == 'mail') $eposta_mail = 'checked="checked"';
else $eposta_mail = '';

if ($ayarlar['eposta_yontem'] == 'smtp') $eposta_smtp = 'checked="checked"';
else $eposta_smtp = '';



if ($ayarlar['smtp_kd'] == 'true') $smtp_kd_acik = 'checked="checked"';
else $smtp_kd_acik = '';

if ($ayarlar['smtp_kd'] == 'false') $smtp_kd_kapali = 'checked="checked"';
else $smtp_kd_kapali = '';




//	TEMA UYGULANIYOR	//

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/ayarlar.html');

$yonetim_sol_menu = '<input type="hidden" name="o" value="'.$o.'">';
$yonetim_sol_menu .= tema_dosyasi('./../temalar/'.$ayarlar['temadizini'].'/yonetim/sol_menu.html');


//	veriler tema motoruna yollanýyor	//

$dongusuz = array('{YONETIM_SOL_MENU}' => $yonetim_sol_menu,
'{JAVASCRIPT_KODU}' => $javascript_kodu,
'{TITLE}' => $ayarlar['title'],
'{ANASYF_BASLIK}' => $ayarlar['anasyfbaslik'],
'{SYF_BASLIK}' => $ayarlar['syfbaslik'],
'{ALANADI}' => $ayarlar['alanadi'],
'{F_DIZIN}' => $ayarlar['f_dizin'],
'{FORUM_DURUMU_ACIK}' => $forum_durumu_acik,
'{FORUM_DURUMU_KAPALI}' => $forum_durumu_kapali,
'{FSYFKOTA}' => $ayarlar['fsyfkota'],
'{KSYFKOTA}' => $ayarlar['ksyfkota'],
'{CEVRIMICI}' => ($ayarlar['cevrimici'] / 60),
'{K_CEREZ_ZAMAN}' => ($ayarlar['k_cerez_zaman'] / 60),
'{ILETI_SURE}' => $ayarlar['ileti_sure'],
'{KILIT_SURE}' => ($ayarlar['kilit_sure'] / 60),
'{KAYIT_SORU_ACIK}' => $kayit_soru_acik,
'{KAYIT_SORU_KAPALI}' => $kayit_soru_kapali,
'{KAYIT_SORUSU}' => $ayarlar['kayit_sorusu'],
'{KAYIT_CEVABI}' => $ayarlar['kayit_cevabi'],
'{KAYIT_ONAY_ACIK}' => $kayit_onay_acik,
'{KAYIT_ONAY_KAPALI}' => $kayit_onay_kapali,
'{IMZA_UZUNLUK}' => $ayarlar['imza_uzunluk'],
'{KUL_RESIM}' => $ayarlar['kul_resim'],
'{TARIH_BICIMI}' => $ayarlar['tarih_bicimi'],
'{SAAT_DILIMI}' => $saat_dilimi,
'{FORUM_RENGI}' => $forum_rengi,
'{UYE_KAYIT_ACIK}' => $uye_kayit_acik,
'{UYE_KAYIT_KAPALI}' => $uye_kayit_kapali,
'{HESAP_ETKIN_KAPALI}' => $hesap_etkin_kapali,
'{HESAP_ETKIN_KULLANICI}' => $hesap_etkin_kullanici,
'{HESAP_ETKIN_YONETICI}' => $hesap_etkin_yonetici,
'{BBCODE_ACIK}' => $bbcode_acik,
'{BBCODE_KAPALI}' => $bbcode_kapali,
'{SEO_ACIK}' => $seo_acik,
'{SEO_KAPALI}' => $seo_kapali,
'{BOYUTLANDIRMA_ACIK}' => $boyutlandirma_acik,
'{BOYUTLANDIRMA_KAPALI}' => $boyutlandirma_kapali,
'{BOLUMKISI_ACIK}' => $bolumkisi_acik,
'{BOLUMKISI_KAPALI}' => $bolumkisi_kapali,
'{KONUKISI_ACIK}' => $konukisi_acik,
'{KONUKISI_KAPALI}' => $konukisi_kapali,
'{PORTAL_ACIK}' => $portal_acik,
'{PORTAL_KAPALI}' => $portal_kapali,
'{SONKONULAR_ACIK}' => $sonkonular_acik,
'{SONKONULAR_KAPALI}' => $sonkonular_kapali,
'{KACSONKONU}' => $ayarlar['kacsonkonu'],
'{KURUCU}' => $ayarlar['kurucu'],
'{YONETICI}' => $ayarlar['yonetici'],
'{YARDIMCI}' => $ayarlar['yardimci'],
'{BLM_YRD}' => $ayarlar['blm_yrd'],
'{KULLANICI}' => $ayarlar['kullanici'],
'{O_ILETI_ACIK}' => $o_ileti_acik,
'{O_ILETI_KAPALI}' => $o_ileti_kapali,
'{OI_UYARI_ACIK}' => $oi_uyari_acik,
'{OI_UYARI_KAPALI}' => $oi_uyari_kapali,
'{GELEN_KUTU_KOTA}' => $ayarlar['gelen_kutu_kota'],
'{ULASAN_KUTU_KOTA}' => $ayarlar['ulasan_kutu_kota'],
'{KAYDEDILEN_KUTU_KOTA}' => $ayarlar['kaydedilen_kutu_kota'],
'{RESIM_YUKLE_ACIK}' => $resim_yukle_acik,
'{RESIM_YUKLE_KAPALI}' => $resim_yukle_kapali,
'{UZAK_RESIM_ACIK}' => $uzak_resim_acik,
'{UZAK_RESIM_KAPALI}' => $uzak_resim_kapali,
'{RESIM_GALERISI_ACIK}' => $resim_galerisi_acik,
'{RESIM_GALERISI_KAPALI}' => $resim_galerisi_kapali,
'{RESIM_BOYUT}' => ($ayarlar['resim_boyut']/1024),
'{RESIM_YUKSEKLIK}' => $ayarlar['resim_yukseklik'],
'{RESIM_GENISLIK}' => $ayarlar['resim_genislik'],
'{Y_POSTA}' => $ayarlar['y_posta'],
'{EPOSTA_MAIL}' => $eposta_mail,
'{EPOSTA_SENDMAIL}' => '',
'{EPOSTA_SMTP}' => $eposta_smtp,
'{SMTP_KD_ACIK}' => $smtp_kd_acik,
'{SMTP_KD_KAPALI}' => $smtp_kd_kapali,
'{SMTP_SUNUCU}' => $ayarlar['smtp_sunucu'],
'{SMTP_KULLANICI}' => $ayarlar['smtp_kullanici']);



$ornek1->dongusuz($dongusuz);

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>