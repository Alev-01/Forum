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


@error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
@ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT);
@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';



//  ZARARLI KODLAR TEMÝZLENÝYOR //

if ((isset($_GET['mesaj_no'])) AND (!is_numeric($_GET['mesaj_no']))) $_GET['mesaj_no'] = 0;

if ((isset($_GET['cevapno'])) AND (!is_numeric($_GET['cevapno']))) $_GET['cevapno'] = 0;

if ((isset($_GET['cevap_no'])) AND (!is_numeric($_GET['cevap_no']))) $_GET['cevap_no'] = 0;

if ((isset($_GET['fno'])) AND (!is_numeric($_GET['fno']))) $_GET['fno'] = 0;

if ((isset($_GET['fno1'])) AND (!is_numeric($_GET['fno1']))) $_GET['fno1'] = 0;

if ((isset($_GET['fno2'])) AND (!is_numeric($_GET['fno2']))) $_GET['fno2'] = 0;

if ((isset($_GET['o'])) AND (!preg_match('/^[A-Za-z0-9]+$/', $_GET['o']))) $_GET['o'] = '';



if (isset($_GET['fsayfa']))
{
    if (!is_numeric($_GET['fsayfa'])) $_GET['fsayfa'] = 0;
    if ($_GET['fsayfa'] != 0) $fs = '&amp;fs='.$_GET['fsayfa'];
    else $fs = '';
}

if (isset($_GET['sayfa']))
{
    if (is_numeric($_GET['sayfa']) == false) $_GET['sayfa'] = 0;
    if ($_GET['sayfa'] != 0) $ks = '&amp;ks='.$_GET['sayfa'];
    else $ks = '';
}

if (isset($_GET['git']))
{
    $git = '?git='.@zkTemizle($_GET['git']);
    $git = @zkTemizle4($git);
}
elseif (isset($_SERVER['HTTP_REFERER']))
{
    $git = '?git='.@zkTemizle($_SERVER['HTTP_REFERER']);
    $git = @zkTemizle4($git);
}
else $git = '';





//  BÝLGÝ ÝLETÝLERÝ  - BAÞI //


$bilgi_no[1] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].$ks.'#c'.$_GET['cevapno'].'">
Ýletiniz gönderilmiþtir, okumak için <a href="konu.php?k='.$_GET['mesaj_no'].$ks.'#c'.$_GET['cevapno'].'">týklayýn.</a>
<br>Foruma dönmek için <a href="forum.php?f='.$_GET['fno'].'">týklayýn.</a>';

$bilgi_no[2] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].'">
Ýletiniz gönderilmiþtir, okumak için <a href="konu.php?k='.$_GET['mesaj_no'].'">týklayýn.</a>
<br>Foruma dönmek için <a href="forum.php?f='.$_GET['fno'].'">týklayýn.</a>';

$bilgi_no[3] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].'&amp;f='.$_GET['fno'].$fs.'">
Ýletiniz deðiþtirilmiþtir, okumak için <a href="konu.php?k='.$_GET['mesaj_no'].'&amp;f='.$_GET['fno'].$fs.'">týklayýn.</a>
<br>Foruma dönmek için <a href="forum.php?f='.$_GET['fno'].'">týklayýn.</a>';

$bilgi_no[4] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].$ks.'&amp;f='.$_GET['fno'].$fs.'#c'.$_GET['cevapno'].'">
Ýletiniz deðiþtirilmiþtir, okumak için <a href="konu.php?k='.$_GET['mesaj_no'].$ks.'&amp;f='.$_GET['fno'].$fs.'#c'.$_GET['cevapno'].'">týklayýn.</a>
<br>Foruma dönmek için <a href="forum.php?f='.$_GET['fno'].'">týklayýn.</a>';

$bilgi_no[5] = 'Konuyu ve altýndaki tüm cevaplarý silmek istediðinize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=mesaj&amp;fno='.$_GET['fno'].'&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;o='.$_GET['o'].$fs.'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.'">Hayýr</a>';

$bilgi_no[6] = 'Konu ve tüm cevaplarý silinmiþtir.<br><br>Foruma geri dönmek için <a href="forum.php?f='.$_GET['fno'].$fs.'">týklayýn</a>';

$bilgi_no[7] = 'Cevabý silmek istediðinize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=cevap&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;cevap_no='.$_GET['cevap_no'].'&amp;o='.$_GET['o'].$fs.$ks.'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.$ks.'">Hayýr</a>';

$bilgi_no[8] = 'Cevap silinmiþtir.<br><br>Konuya geri dönmek için <a href="konu.php?k='.$_GET['mesaj_no'].$fs.$ks.'">týklayýn.</a>';

$bilgi_no[9] = 'Seçtiðiniz konu taþýnmýþtýr.<br><br>Geldiðiniz foruma dönmek için <a href="forum.php?f='.$_GET['fno1'].'">týklayýn.</a><br>Konuyu taþýdýðýnýz foruma dönmek için <a href="forum.php?f='.$_GET['fno2'].'">týklayýn.</a>';

$bilgi_no[10] = 'Profiliniz Güncellenmiþtir...<br><br>Profilinizi görmek için <a href="profil.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[11] = 'Özel iletiniz gönderilmiþtir.<br>Gönderilen kutusuna gitmek için <a href="ozel_ileti.php?kip=gonderilen">týklayýn.</a><meta http-equiv="Refresh" content="5;url=ozel_ileti.php?kip=gonderilen">';

$bilgi_no[12] = 'Forum ayarlarýnýz güncellenmiþtir.<br><br>Yönetim ana sayfasýna dönmek için <a href="yonetim/index.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[13] = 'E-POSTANIZ GÖNDERÝLMÝÞTÝR...';

$bilgi_no[14] = 'Etkinleþtirme kodu baþvurunuz tamamlanmýþtýr.<br><br>Size gelen E-Postadaki baðlantýyý týklayarak hesabýnýzý etkinleþtirebilirsiniz.<br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$bilgi_no[15] = 'Kayýt iþleminiz baþarýyla tamamlanmýþtýr. <br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$bilgi_no[16] = 'Kayýt iþleminiz baþarýyla tamamlanmýþtýr.<br><br>Hesabýnýzý etkinleþtirmek için yapmanýz gerekenler<br>size gönderilen E-Postada anlatýlmaktadýr.<br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$bilgi_no[17] = 'Kayýt iþleminiz baþarýyla tamamlanmýþtýr.<br><br>Hesabýnýzýn etkinleþtirilmesi için forum yöneticisinin onayýný beklemelisiniz.';

$bilgi_no[18] = 'Hesabýnýz zaten etkinleþtirilmiþ.';

$bilgi_no[19] = 'Hesabýnýz etkinleþtirilmiþtir.<br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$bilgi_no[20] = 'Yeni þifre baþvurunuz tamamlanmýþtýr.<br><br>Þifrenizi sýfýrlamanýz için yapmanýz gerekenler size gönderilen<br>E-Postada anlatýlmaktadýr.<br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$bilgi_no[21] = 'Yeni þifreniz oluþturulmuþtur.<br><br>Yeni þifrenizle giriþ yapmak için <a href="giris.php">týklayýnýz.</a>';

$bilgi_no[22] = 'Yeni Þifre baþvurunuz iptal edilmiþtir. Eski þifreniz hâlâ geçerlidir.';

$bilgi_no[23] = 'Kullanýcý hesabý silinmiþtir.<br><br>Geri dönmek için <a href="yonetim/kullanicilar.php?kip=engelli">týklayýn.</a>';

$bilgi_no[24] = 'Kullanýcýnýn engeli kaldýrýlmýþtýr.<br><br>Geri dönmek için <a href="yonetim/kullanicilar.php?kip=engelli">týklayýn.</a><br><br>Engeli olmayan kullanýcýlarý görmek için <a href="yonetim/kullanicilar.php">týklayýn.</a>';

$bilgi_no[25] = 'Kullanýcý hesabý etkinleþtirilmiþtir.<br>Geri dönmek için <a href="yonetim/kullanicilar.php?kip=etkisiz">týklayýn.</a><br><br>Etkinleþtirilmiþ kullanýcýlarý görmek için <a href="yonetim/kullanicilar.php">týklayýn.</a>';

$bilgi_no[26] = 'Kullanýcý hesabý silinmiþtir.<br><br>Geri dönmek için <a href="yonetim/kullanicilar.php?kip=etkisiz">týklayýn.</a>';

$bilgi_no[27] = 'Forum dalý içinde bulunan; forumlar, alt forumlar, konular ve <br> cevaplarýyla beraber baþarýyla silinmiþtir.<br><br>Forum Yönetimi sayfasýna dönmek için <a href="yonetim/forumlar.php">týklayýn.</a>';

$bilgi_no[28] = 'Tüm forumlar, seçmiþ olduðunuz forum dalýna baþarýyla taþýnmýþtýr.<br><br>Forum Yönetimi sayfasýna dönmek için <a href="yonetim/forumlar.php">týklayýn.</a>';

$bilgi_no[29] = 'Forum, forumun konularý ve konularýn cevaplarý baþarýyla silinmiþtir.<br><br>Forum Yönetimi sayfasýna dönmek için <a href="yonetim/forumlar.php">týklayýn.</a>';

$bilgi_no[30] = 'Forumun konularý ve konularýn cevaplarý baþarýyla taþýnmýþtýr.<br><br>Forum Yönetimi sayfasýna dönmek için <a href="yonetim/forumlar.php">týklayýn.</a>';

$bilgi_no[31] = 'Forum, seçtiðiniz forum dalýna baþarýyla taþýnmýþtýr.<br><br>Forum Yönetimi sayfasýna dönmek için <a href="yonetim/forumlar.php">týklayýn.</a>';

$bilgi_no[32] = 'Kullanýcýnýn Profili Güncellenmiþtir.<br><br>Yönetim ana sayfasýna dönmek için <a href="yonetim/index.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[33] = 'Kullanýcý hesabý etkisizleþtirilmiþtir.<br>Geri dönmek için <a href="yonetim/kullanicilar.php">týklayýn.</a><br><br>Etkinleþtirilmemiþ kullanýcýlarý görmek için <a href="yonetim/kullanicilar.php?kip=etkisiz">týklayýn.</a>';

$bilgi_no[34] = 'Kullanýcý hesabý silinmiþtir.<br><br>Geri dönmek için <a href="yonetim/kullanicilar.php">týklayýn.</a>';

$bilgi_no[35] = 'Kullanýcý engellenmiþtir.<br>Geri dönmek için <a href="yonetim/kullanicilar.php">týklayýn.</a><br><br>Engellenmiþ kullanýcýlarý görmek için <a href="yonetim/kullanicilar.php?kip=engelli">týklayýn.</a>';

$bilgi_no[36] = 'Forumdaki eski mesajlar silinmiþtir.';

$bilgi_no[37] = 'E-POSTALARINIZ YOLLANMIÞTIR...';

$bilgi_no[38] = 'Veritabaný yedeðiniz baþarýyla geri yüklenmiþtir.';

$bilgi_no[39] = 'Yasaklama bilgileri güncellenmiþtir.<br>Geri dönmek için <a href="yonetim/yasaklamalar.php">týklayýn.</a>';

$bilgi_no[40] = 'Güncelleme Baþarýyla Tamamlanmýþtýr.';

$bilgi_no[41] = 'Kullanýcý engellenmiþtir.<br>Geri dönmek için <a href="yonetim/kullanicilar.php?kip=etkisiz">týklayýn.</a><br><br>Engellenmiþ kullanýcýlarý görmek için <a href="yonetim/kullanicilar.php?kip=engelli">týklayýn.</a>';

$bilgi_no[42] = 'E-Posta adresiniz kaydedilmiþtir. <br><br>Adres deðiþikliðin tamamlanmasý için yapmanýz gerekenler <br>size gönderilen E-Postada anlatýlmaktadýr.<br><br>Profilinizi görmek için <a href="profil.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[43] = 'Þifreniz deðiþtirilmiþtir...<br><br>Profilinizi görmek için <a href="profil.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[44] = 'Þifreniz ve E-Posta adresiniz kaydedilmiþtir. <br><br>Adres deðiþikliðin tamamlanmasý için yapmanýz gerekenler <br>size gönderilen E-Postada anlatýlmaktadýr.<br><br>Profilinizi görmek için <a href="profil.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[45] = 'Yeni E-Posta adresiniz onaylanmýþ ve deðiþtirilmiþtir.<br><br>Profilinizi görmek için <a href="profil.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[46] = 'Özel ileti ayarlarýnýz güncellenmiþtir.<br><br>Geri dönmek için <a href="ozel_ileti.php?kip=ayarlar">týklayýn.</a><meta http-equiv="Refresh" content="5;url=ozel_ileti.php?kip=ayarlar">';

$bilgi_no[47] = 'Forumdaki eski özel iletiler silinmiþtir.<br><br>Yönetim ana sayfasýna dönmek için <a href="yonetim/index.php">týklayýn.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[48] = 'Üye baþarýyla oluþturulmuþtur, geri dönmek için <a href="yonetim/yeni_uye.php">týklayýn.</a><br><br>Üyenin profilini görmek için <a href="profil.php?u='.$_GET['fno'].'">týklayýn.</a><br>Üyenin profilini deðiþtirmek için <a href="yonetim/kullanici_degistir.php?u='.$_GET['fno'].'">týklayýn.</a>';

$bilgi_no[49] = 'Dosya silinmiþtir.<br><br>Geri dönmek için <a href="yonetim/yuklemeler.php">týklayýn.</a>';

$bilgi_no[50] = 'Dosya silinmiþtir.<br><br>Geri dönmek için <a href="profil_degistir.php?kosul=yuklemeler">týklayýn.</a>';


//  BÝLGÝ ÝLETÝLERÝ  - SONU	//







//  HATA ÝLETÝLERÝ  - BAÞI  //


$hata_no[1] = 'Son aramanýzýn üzerinden belli bir süre geçmeden yeni arama yapamazsýnýz !';

$hata_no[2] = 'Tüm alanlar boþ býrakýlamaz !<br>Aradýðýnýz sözcük 3 harfden uzun olmalýdýr !<br><br>Lütfen <a href="arama.php">geri</a> dönüp aramak istediðiniz sözcüðü ilgili bölüme giriniz.';

$hata_no[3] = 'Bu konuyu taþýmaya yetkiniz yok !';

$hata_no[4] = 'Gönderilen kýsmý boþ býrakýlamaz !';

$hata_no[5] = 'E-posta baþlýðý en az 3, en fazla 60 karakterden oluþmalýdýr.<br><br>E-posta içeriði en az 3 karakterden oluþmalýdýr.';

$hata_no[6] = 'Yolladýðýnýz son iletinin üzerinden<br>'.$ayarlar['ileti_sure'].' saniye geçmeden baþka bir ileti gönderemezsiniz.';

$hata_no[7] = 'Hatalý kullanýcý adý !<br><br>Göndermek istediðiniz kiþiyi kontrol edip tekrar deneyiniz.';

$hata_no[8] = 'Lütfen E-Posta adresinizi yazýnýz !';

$hata_no[9] = 'E-Posta adresiniz 70 karakterden uzun olamaz !';

$hata_no[10] = 'E-Posta adresiniz hatalý !';

$hata_no[11] = '<font color="#007900">Kayýt iþleminiz baþarýyla tamamlanmýþtýr.</font> <br><br>Fakat sunucudaki bir hatadan dolayý E-postanýz gönderilememiþtir !<br><br>Ýstediðiniz zaman <a href="etkinlestir.php">buradan</a> etkinleþtirme kodu baþvurusunda bulunabilirsiniz.';

$hata_no[12] = 'Bu E-Posta adresine baðlý hesabýnýz zaten etkinleþtirilmiþ !';

$hata_no[13] = 'Yazdýðýnýz E-Posta adresi veritabanýnda bulunmamaktadýr !';

$hata_no[14] = 'Seçtiðiniz forum veritabanýnda bulunmamaktadýr !';

$hata_no[15] = 'Bu foruma sadece yöneticiler girebilir !';

$hata_no[16] = 'Bu foruma sadece yöneticiler ve yardýmcýlar girebilir !';

$hata_no[17] = 'Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler girebilir !';

$hata_no[18] = 'Lütfen kullanýcý adý ve þifrenizi giriniz !';

$hata_no[19] = 'Kullanýcý adý en az 4, en fazla 20 karakter olmalýdýr !';

$hata_no[20] = 'Þifreniz en az 5, en fazla 20 karakter olmalýdýr !';

$hata_no[21] = 'Beþ baþarýsýz giriþ denemesi yaptýnýz.<br>'.($ayarlar['kilit_sure'] / 60).' dakika boyunca hesabýnýz kilitlenmiþtir.';

$hata_no[22] = 'Hatalý kallanýcý adý veya parola.<br>Caps Lock açýk kalmýþ olabilir, þifrelerde büyük/küçük harf ayrýmý vardýr.<br><br>Lütfen geri dönüp <a href="giris.php">tekrar</a> deneyin.';

$hata_no[23] = 'Hesabýnýz henüz etkinleþtirilmemiþ !<br><br>Hesabýnýzý etkinleþtirmek için yapmanýz gerekenler <br>size gönderilen E-Postada anlatýlmaktadýr.<br><br><a href="etkinlestir.php">Etkinleþtirme kodunu tekrar yolla</a>';

$hata_no[24] = 'Hesabýnýz engellenmiþtir !';

$hata_no[25] = 'Çok fazla kayýt giriþiminde bulundunuz. Daha sonra tekrar deneyin !';

$hata_no[26] = 'Tüm bölümlerin doldurulmasý zorunludur !';

$hata_no[27] = 'Kullanýcý adýnda geçersiz karakterler var ! <br><br>Latin ve Türkçe harf, rakam, alt çizgi( _ ), tire ( - ), nokta ( . ) kullanýlabilir. <br>Bunlarýn dýþýndaki özel karakterleri ve boþluk karakterini içeremez.';

$hata_no[28] = 'Kullanýcý adý en az 4, en fazla 20 karakter olmalýdýr !';

$hata_no[29] = 'Bu kullanýcý adý yasaklanmýþtýr, lütfen baþka bir kullanýcý adý deneyin !';

$hata_no[30] = 'Bu E-Posta adresi yasaklanmýþtýr !';

$hata_no[31] = '"Ad Soyad - Lâkap" alanýnda geçersiz karakterler var !<br><br>Latin ve Türkçe harf, rakam, boþluk, alt çizgi( _ ), tire ( - ), nokta ( . ) kullanýlabilir. <br>Bunlarýn dýþýndaki özel karakterleri içeremez.';

$hata_no[32] = '"Ad Soyad - Lâkap" en az 4, en fazla 30 karakter olmalýdýr !';

$hata_no[33] = 'Yazdýðýnýz þifreler uyuþmuyor !';

$hata_no[34] = 'Þifrenizde geçersiz karakterler var ! <br><br>Latin harf, rakam, alt çizgi( _ ), tire ( - ), and ( & ), nokta ( . ) kullanýlabilir. <br>Bunlarýn dýþýndaki özel karakterleri, Türkçe karakterleri ve boþluk karakterini içeremez.';

$hata_no[35] = 'Þifreniz en az 5, en fazla 20 karakter olmalýdýr !';

$hata_no[36] = 'Þehir geçersiz !';

$hata_no[37] = 'Doðum tarihi geçersiz !';

$hata_no[38] = 'Doðum tarihinin yýl kýsmý geçersiz !<br>Lütfen 1981 þeklinde 4 rakam ile yazýnýz.';

$hata_no[39] = 'Silmeye çalýþtýðýnýz forumun alt forumlarý var. Önce alt forumlarýný  silin !';

$hata_no[40] = 'E-Posta adresiniz 70 karakterden uzun olamaz !';

$hata_no[41] = 'Kayýt güvenlik sorusunun cevabý hatalý !<br><br>Genelde buraya sadece robot programlarýn giremeyeceði çok kolay sorular yazýlýr.<br><br>Cevabý tahmin edemiyorsanýz forum yöneticisiyle iletiþime geçin.';

$hata_no[42] = 'Bu kullanýcý adý kullanýlmaktadýr, lütfen baþka bir isim deneyin !';

$hata_no[43] = 'Bu E-posta adresiyle daha önce kayýt yapýlmýþtýr !';

$hata_no[44] = 'Onay kodunu yanlýþ girdiniz. Lütfen geri dönüp tekrar deneyiniz.';

$hata_no[45] = 'Hatalý Adres !<br>Lütfen kontrol edip tekrar deneyin.';

$hata_no[46] = 'Forumda bu isimde bir üye bulunmamaktadýr !';

$hata_no[47] = 'Seçtiðiniz konu veritabanýnda bulunmamaktadýr !';

$hata_no[48] = 'Etkinleþtirme kodunuzda eksik var, ya da adresi eksik kopyaladýnýz.<br><br>Lütfen kontrol edip tekrar deneyiniz.<br>Yine ayný sorunla karþýlaþýrsanýz forum yöneticisine baþvurun.';

$hata_no[49] = 'Etkinleþtirme kodunuz hatalý, ya da adresi eksik kopyaladýnýz.<br><br>Lütfen kontrol edip tekrar deneyiniz.<br>Yine ayný sorunla karþýlaþýrsanýz forum yöneticisine baþvurun.';

$hata_no[50] = 'Kilitli konularý deðiþtiremezsiniz !';

$hata_no[51] = 'Kilitli konularýn cevaplarýný deðiþtiremezsiniz !';

$hata_no[52] = 'Bu iletiyi deðiþtirmeye yetkiniz yok !';

$hata_no[53] = 'Ýleti baþlýðý en az 3, en fazla 53 karakterden oluþmalýdýr.<br><br>Ýleti içeriði en az 3 karakterden oluþmalýdýr.';

$hata_no[54] = 'Bu konuyu kilitlemeye veya açmaya yetkiniz yok !';

$hata_no[55] = 'Seçtiðiniz cevap veritabanýnda bulunmamaktadýr !';

$hata_no[56] = 'Bu iletiyi silmeye yetkiniz yok !';

$hata_no[57] = 'Kilitli konulara cevap yazamazsýnýz !';

$hata_no[58] = 'Bu foruma sadece yöneticiler cevap yazabilir !';

$hata_no[59] = 'Bu foruma sadece yöneticiler ve yardýmcýlar cevap yazabilir !';

$hata_no[60] = 'Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler cevap yazabilir !';

$hata_no[61] = 'Site kurucusunu etkisizleþtiremezsiniz !';

$hata_no[62] = 'Aradýðýnýz özel ileti bulunamýyor.<br>Silinmiþ ya da okuma yetkiniz olmayabilir.';

$hata_no[63] = 'Gönderilen kýsmý boþ býrakýlamaz !';

$hata_no[64] = 'Özel ileti baþlýðý en az 3, en fazla 60 karakterden oluþmalýdýr.<br><br>Özel Ýleti içeriði en az 3 karakterden oluþmalýdýr.';

$hata_no[65] = 'Yolladýðýnýz son iletinin üzerinden '.$ayarlar['ileti_sure'].' saniye geçmeden baþka bir ileti gönderemezsiniz.';

$hata_no[66] = 'Forumda bu isimde bir üye bulunmamaktadýr.<br>Lütfen geri dönüp tekrar deneyin.';

$hata_no[67] = 'Gönderdiðiniz kiþinin Gelen Kutusu dolu olduðundan ileti gönderilemedi.';

$hata_no[68] = 'Seçim yapmadýnýz !';

$hata_no[69] = 'Bu özel iletiyi silmeye yetkiniz yok!';

$hata_no[70] = 'Kaydedilen kutunuz dolu.<br>Boþaltmadan baþka ileti kaydedemezsiniz.';

$hata_no[71] = 'Bu iletiyi kaydetmeye yetkiniz yok!';

$hata_no[72] = 'Kullanýcý adý 20 karakterden uzun olamaz !';

$hata_no[73] = '* iþaretli bölümlerin doldurulmasý zorunludur !';

$hata_no[74] = 'Doðum tarihi geçersiz !<br>Lütfen tire(-)lerde dahil olmaz üzere 31-12-1985 þeklinde yazýnýz.';

$hata_no[75] = 'Web Adresiniz 70 karakterden uzun olamaz !';

$hata_no[76] = 'Tema dizini adý, alt çizgi( _ ) ve tire ( - ) dýþýndaki özel karakterleri ve Türkçe karakterleri içeremez !';

$hata_no[77] = 'Tema klasörünün adý 20 karakterden uzun olamaz !';

$hata_no[78] = 'Ýmzanýz '.$ayarlar['imza_uzunluk'].' karakterden uzun olamaz !';

$hata_no[79] = 'ICQ Numaranýz 30 karakterden uzun olamaz !';

$hata_no[80] = 'AIM Adýnýz 70 karakterden uzun olamaz !';

$hata_no[81] = 'MSN Messenger Adýnýz 70 karakterden uzun olamaz !';

$hata_no[82] = 'Yahoo! Messenger Adýnýz 70 karakterden uzun olamaz !';

$hata_no[83] = 'Skype Adýnýz 70 karakterden uzun olamaz !';

$hata_no[84] = 'Yüklemeye çalýþtýðýnýz resim bozuk !';

$hata_no[85] = 'Sadece jpeg, gif veya png resimleri yüklenebilir ! <br>Eðer dosyanýz doðru tipte ise bozuk olabilir.';

$hata_no[86] = 'Yüklemeye çalýþtýðýnýz resim '.($ayarlar['resim_boyut']/1024).' kilobayt`dan büyük !';

$hata_no[87] = 'Yüklemeye çalýþtýðýnýz resmin boyutlarý '.$ayarlar['resim_genislik'].'x'.$ayarlar['resim_yukseklik'].'`den büyük !';

$hata_no[88] = 'Dosya yüklenemedi !<br><br>Yöneticiyseniz FTP programýnýzdan dosyalar/resimler/yuklenen/<br>dizinine yazma hakký vermeyi (chmod 777) deneyin.';

$hata_no[89] = 'Uzak resim, kontrol edilirken bir sorunla karþýlaþýldý.<br>Sunucunun uzak dosya eriþimi kapatýlmýþ olabilir ya da <br>adreste veya resim dosyasýnda bir sorun olabilir.';

$hata_no[90] = 'Eklemeye çalýþtýðýnýz resim '.($ayarlar['resim_boyut']/1024).' kilobayt`dan büyük !';

$hata_no[91] = 'Eklemeye çalýþtýðýnýz resmin boyutlarý '.$ayarlar['resim_genislik'].'x'.$ayarlar['resim_yukseklik'].'`den büyük !';

$hata_no[92] = 'E-Posta Adresini Göster, Doðum Tarihini Göster, Þehir Göster ve <br>Çevrimiçi Durumunu Göster ayarlarý sadece açýk-kapalý deðeri alabilir !';

$hata_no[93] = 'Bu E-posta adresi baþka bir kullanýcýya aittir !';

$hata_no[94] = 'YETKÝNÝZ YOK !!!';

$hata_no[95] = 'Buradaki yazýlarý ancak forum üzerinden okuyabilirsiniz.';

$hata_no[96] = 'Yeni Þifre kodunuz hatalý, ya da adresi eksik kopyaladýnýz.<br>Lütfen kontrol edip tekrar deneyiniz.<br>Yine ayný sorunla karþýlaþýrsanýz forum yöneticisine baþvurun.';

$hata_no[97] = 'Çok fazla giriþiminde bulundunuz. Daha sonra tekrar deneyin !';

$hata_no[98] = 'Tüm alanlarý doldurmalýsýnýz! <i>(SMTP sunucusu ayarlarý hariç)</i>';

$hata_no[99] = 'Sayfa baþlýðý 100 karakterden uzun olamaz !';

$hata_no[100] = 'Alan adý 100 karakterden uzun olamaz !';

$hata_no[101] = 'Dizin adý 100 karakterden uzun olamaz !';

$hata_no[102] = 'Konu ve cevap sayýsý alanlarýna en fazla 99 deðerini girebilirsiniz !';

$hata_no[103] = 'Konu ve cevap sayýsý alanlarý sadece rakamdan oluþabilir !';

$hata_no[104] = 'Çerez geçerlilik süresi sadece rakamdan oluþabilir !';

$hata_no[105] = 'Çerez geçerlilik süreleri en fazla 5 rakamdan oluþabilir !<br><br>Yani en fazla 99`999 dakika deðerini alabilir ki bu da 69 gün eder.';

$hata_no[106] = 'Ýki ileti arasý bekleme süresi sadece rakamdan oluþabilir !';

$hata_no[107] = 'Ýki ileti arasý bekleme süresi en fazla 86`400 saniye alabilir ki bu da 24 saat eder.';

$hata_no[108] = 'Hesap kilit süresi sadece rakamdan oluþabilir !';

$hata_no[109] = 'Beþ baþarýsýz giriþten sonra hesabýn kilitli kalacaðý süre<br>en fazla 1440 dakika olabilir ki bu da 24 saat eder.';

$hata_no[110] = 'Kayýt sorusu açýk kapalý ayarlarý sadece açýk-kapalý deðeri alabilir !';

$hata_no[111] = 'Kayýt sorusu ve cevabý 100 karakterden uzun olamaz !';

$hata_no[112] = 'Ýmza uzunluðu 1 ila 500 arasý olabilir !';

$hata_no[113] = 'Tarih biçimi en fazla 20 karakter olabilir !';

$hata_no[114] = 'Zaman dilimi 1 ila  4 karakter arasý olabilir !';

$hata_no[115] = 'Hatalý forum rengi !';

$hata_no[116] = 'Hesap Etkinleþtirme ayarý sadece kapalý, kullanýcý ve yönetici deðerlerini alabilir !';

$hata_no[117] = 'BBCode, Özel ileti ve Güncel Konular ayarlarý sadece açýk-kapalý deðeri alabilir !';

$hata_no[118] = 'Gösterilecek güncel konu sayýsý ayarý sadece rakamdan oluþabilir !';

$hata_no[119] = 'Gösterilecek güncel konu sayýsý ayarý 50`den fazla olamaz !';

$hata_no[120] = 'Site kurucusu adý 100 karakterden uzun olamaz !';

$hata_no[121] = 'Forum yöneticisi adý 100 karakterden uzun olamaz !';

$hata_no[122] = 'Forum yardýmcýsý adý 100 karakterden uzun olamaz !';

$hata_no[123] = 'Kayýtlý kullanýcý adý 100 karakterden uzun olamaz !';

$hata_no[124] = 'Gelen, ulaþan ve kaydedilen kutusu kota deðerleri en fazla 3 rakamdan oluþabilir !<br><br>Yani en fazla 999 deðerini alabilir.';

$hata_no[125] = 'Gelen, ulaþan ve kaydedilen kutusu kota deðerleri sadece rakamdan oluþabilir !';

$hata_no[126] = 'Resim yükleme özelliði sadece açýk-kapalý deðeri alabilir !';

$hata_no[127] = 'Uzak resim özelliði sadece açýk-kapalý deðeri alabilir !';

$hata_no[128] = 'Resim galerisi özelliði sadece açýk-kapalý deðeri alabilir !';

$hata_no[129] = 'Resim dosyasýnýn büyüklüðü 1 ila 999 kb. arasý olabilir !';

$hata_no[130] = 'Resim boyutu en büyük 999 x 999 arasý olabilir !';

$hata_no[131] = 'Yönetici E-Posta adresi 100 karakterden uzun olamaz !';

$hata_no[132] = 'E-Posta yöntemi sadece mail, sendmail ve smtp deðerlerini alabilir !';

$hata_no[133] = 'SMTP kimlik doðrulamasý alaný sadece true ve false deðerlerini alabilir !';

$hata_no[134] = 'SMTP sunucu adresi 100 karakterden uzun olamaz !';

$hata_no[135] = 'SMTP kullanýcý adý 100 karakterden uzun olamaz !';

$hata_no[136] = 'SMTP þifresi 100 karakterden uzun olamaz !';

$hata_no[137] = 'Site kurucusunu silemezsiniz !';

$hata_no[138] = 'Bir hata oluþtu ya da sayfaya doðrudan eriþmeye çalýþýyorsunuz. <br>Yapmak istediðiniz iþlemi <a href="yonetim/forumlar.php">Forum Yönetimi</a> sayfasýndan seçiniz.';

$hata_no[139] = 'Seçtiðiniz forum dalý veritabanýnda bulunmamaktadýr !';

$hata_no[140] = 'Forum dalý baþlýðýný girmeyi unuttunuz !';

$hata_no[141] = 'Forum baþlýðýný girmeyi unuttunuz !';

$hata_no[142] = 'Taþýmak istediðniz forum dalýný seçmeyi unuttunuz. <br><br>Lütfen geri dönüp tekrar deneyin.';

$hata_no[143] = 'Taþýmak istediðniz forumu seçmeyi unuttunuz. <br><br>Lütfen geri dönüp tekrar deneyin.';

$hata_no[144] = 'Yönetim Yetkiniz Yok !';

$hata_no[145] = '<a href="yonetim/kullanicilar.php">Bu sayfadan</a> istediðiniz üyenin kullanýcý adýný týklayýn.<br><br>Açýlan "Kullanýcý Profilini Deðiþtir" sayfasýndaki, Diðer Yetkiler baðlantýsýný týklayýn.<br><br>Açýlan sayfadan özel yetki vermek istediðiniz forumu seçerek kullanýcýya istediðiniz özel yetkiyi verebilirsiniz. ';

$hata_no[146] = 'Seçtiðiniz forumun yetkisi sadece yöneticilere verilmiþ.<br>Özel bir üyeye izin veremezsiniz !';

$hata_no[147] = 'Site kurucusunun bilgilerini buradan deðiþteremezsiniz !';

$hata_no[148] = 'Yetki alaný verisi geçersiz !';

$hata_no[149] = 'Site kurucusunu engelleyemezsiniz !';

$hata_no[150] = 'Varsayýlan 5 Renkli temasý seçeneklerden kaldýrýlamaz !';

$hata_no[151] = 'Bu sayfaya sadece site kurucusu girebilir.';

$hata_no[152] = 'Forum seçmeyi unuttunuz !';

$hata_no[153] = 'Gün alanýna 1 ila 999 arasýnda bir sayý girmelisiniz.';

$hata_no[154] = 'Seçmiþ olduðunuz grupta hiçbir üye bulunmamaktadýr !';

$hata_no[155] = 'Sunucunuz sýkýþtýrýlmýþ dosya oluþturulmasýný desteklemiyor !';

$hata_no[156] = 'Dosya Yüklenemedi, Dosya adý alýnamadý !<br><br>Bunun nedeni dosyanýn 2mb.`dan büyük olmasý ya da<br>dosya adýnýn kabul edilemeyen karakterler içermesi olabilir. <br><br>Yedeði tablo tablo ayrý dosyalara bölmeyi deneyin veya dosya adýný deðiþtirmeyi deneyin.';

$hata_no[157] = '2mb.`dan büyük yedek yükleyemezsiniz. <br>Yedeði tablo tablo ayrý dosyalara bölmeyi deneyin.';

$hata_no[158] = 'Sunucunuz sýkýþtýrýlmýþ dosya yüklemesini desteklemiyor !';

$hata_no[159] = 'Sadece .sql ve .gz uzantýlý dosyalar yüklenebilir !';

$hata_no[160] = 'BBCode, Özel ileti, Forum durumu, Portal kullanýmý, Kayýt Onay Kodu, SEO,<br> Üye Alýmý, Boyutlandýrma, Güncel Konular, Bölüm ve Konu görüntüleyenler<br> ayarlarý sadece açýk-kapalý deðeri alabilir !';

$hata_no[161] = 'Seçtiðiniz forum kapatýlmýþ.<br>Özel bir üyeye izin veremezsiniz !';

$hata_no[162] = 'Çevrimiçi süresi sadece rakamdan oluþabilir !';

$hata_no[163] = 'Çevrimiçi süresi için en fazla 99 dakika deðerini girebilirsiniz !';

$hata_no[164] = 'Bu forum kapatýlmýþtýr !';

$hata_no[165] = 'Bu foruma sadece yöneticiler konu açabilir !';

$hata_no[166] = 'Bu foruma sadece yöneticiler ve yardýmcýlar konu açabilir !';

$hata_no[167] = 'Bu foruma sadece, yöneticinin verdiði özel yetkilere sahip üyeler konu açabilir !';

$hata_no[168] = 'Bu konu daha önceden geri yüklenmiþ veya silinmemiþ !';

$hata_no[169] = 'Bu cevap daha önceden geri yüklenmiþ veya silinmemiþ !';

$hata_no[170] = 'Bu konuyu üst veya alt konu yapmaya yetkiniz yok !';

$hata_no[171] = 'Kurmaya çalýþtýðýnýz eklentinin adýnda kabul edilmeyen karakterler var !';

$hata_no[172] = '/eklentiler dizinine yazýlamýyor ! <br><br>Eklenti kurulumu için bu dizine yazma hakký (chmod 777) vermelisiniz.';

$hata_no[173] = 'Belirtilen eklenti dosyasý bulunamýyor ! <br><br>Týkladýðýnýz adresi kontrol edip tekrar deneyin.';

$hata_no[174] = 'Bu eklenti zaten kurulu !';

$hata_no[175] = 'Sunucudaki bir hatadan dolayý onay E-Postasý gönderilememiþtir !<br> <br>Lütfen daha sonra tekrar deneyin ve durumu yöneticiye bildirin.';

$hata_no[176] = 'Bu üye kimseden özel ileti kabul etmiyor !';

$hata_no[177] = 'Bu üye sizden özel ileti kabul etmiyor !';

$hata_no[178] = 'Bu üye forumdan uzaklaþtýrýlmýþ !';

$hata_no[179] = 'Bu üyenin hesabý henüz etkinleþtirilmemiþ !';

$hata_no[180] = 'Tarayýcýnýz çerez kabul etmiyor !<br>Tarayýcýnýzýn çerez özelliði kapalý veya desteklemiyor olabilir.<br><br>Giriþ yapabilmeniz için çerez özelliði gereklidir.<br>Çerezlere izin verin veya baþka bir tarayýcýda tekrar deneyin.';

$hata_no[181] = 'Sadece yöneticiler güncelleme yapabilir !<br><br>Yönetici olarak giriþ yapýp tekrar deneyin.';

$hata_no[182] = 'Bu eklenti kurulu deðil !';

$hata_no[183] = 'Bu eklenti zaten etkin !';

$hata_no[184] = 'Bu eklenti zaten etkisiz !';

$hata_no[185] = 'Bu eklenti kullandýðýnýz sürüm ile uyumsuz görünüyor !';

$hata_no[186] = '"Ad Soyad - Lâkap" alanýna girdiðiniz isim yasaklanmýþtýr !';

$hata_no[187] = 'Kurulu eklentileri silemezsiniz !<br>Önce eklentiyi kaldýrýp sonra silmeyi deneyin.';

$hata_no[188] = 'Þifreniz yanlýþ !<br><br>Lütfen <a href="profil_degistir.php?kosul=sifre">geri dönüp</a> tekrar deneyiniz.';

$hata_no[189] = 'Kurmaya çalýþtýðýnýz eklenti portal için ama siz portal kullanmýyor görünüyorsunuz !';

$hata_no[190] = 'Hatalý ip adresi !';

$hata_no[191] = 'Bölüm yardýmcýsý adý 100 karakterden uzun olamaz !';

$hata_no[192] = 'Bu forum konu açmaya kapatýlmýþtýr !';

$hata_no[193] = 'Bu forum cevap yazmaya kapatýlmýþtýr !';

$hata_no[194] = 'Seçtiðiniz forumun yetkisi sadece yönetici ve yardýmcýlara verilmiþ.<br>Özel bir üyeye izin veremezsiniz !';

$hata_no[195] = 'Konuyu taþýdýðýnýz forumda yetkiniz yok !';

$hata_no[196] = 'Bu tema kullandýðýnýz sürüm ile uyumsuz görünüyor !';

$hata_no[197] = 'Seçeneklerde olmayan bir tema varsayýlan olarak ayarlanamaz !<br>Temayý önce seçenekler arasýna ekleyin.';

$hata_no[198] = '<font color="#007900">Kayýt iþleminiz baþarýyla tamamlanmýþtýr.</font> <br><br>Fakat sunucudaki bir hatadan dolayý E-postanýz gönderilememiþtir !<br><br>Giriþ yapmak için <a href="giris.php">týklayýn.</a>';

$hata_no[199] = '<font color="#007900">Kayýt iþleminiz baþarýyla tamamlanmýþtýr.</font> <br><br>Fakat sunucudaki bir hatadan dolayý E-postanýz gönderilememiþtir !<br><br>Hesabýnýzýn etkinleþtirilmesi için forum yöneticisinin onayýný beklemelisiniz.';

$hata_no[200] = 'Bu eklenti etkisizleþtirmeyi desteklemiyor !';

$hata_no[201] = 'Grup adýnda karakterler var !<br><br>Latin ve Türkçe harf, rakam, boþluk, alt çizgi( _ ), tire ( - ), nokta ( . ) kullanýlabilir. <br>Bunlarýn dýþýndaki özel karakterleri içeremez.';

$hata_no[202] = 'Grup adý en az 4, en fazla 30 karakter olmalýdýr !';

$hata_no[203] = 'Bu grup adý kullanýlmaktadýr, baþka bir ad deneyin !';

$hata_no[204] = 'Forumda böyle bir grup bulunmamaktadýr !';

$hata_no[205] = 'Grubun bölüm yardýmcýlýðý yetkisini deðiþtirmek için önce<br><a href="yonetim/ozel_izinler.php">özel izinler</a> sayfasýnda görünen bölüm yönetme izinlerini alýn !';

$hata_no[206] = 'Aradýðýnýz dosya bulunamýyor.<br>Dosya daha önceden silinmiþ olabilir. Lütfen kontrol edip tekrar deneyin.';


//  HATA ÝLETÝLERÝ  - SONU	//










//  UYARI ÝLETÝLERÝ  - BAÞI //


$uyari_no[1] = '<font color="orange">phpKF sürüm 1.90 güncellemesi zaten yapýlmýþ !</font>';

$uyari_no[2] = '<font color="orange">Özel Ýleti hizmeti kapatýlmýþtýr !</font>';

$uyari_no[3] = '<font color="orange">Seçtiðiniz kullanýcý bir yönetici !<br> Yöneticilerin yetkileri sýnýrsýzdýr.</font>';

$uyari_no[4] = '<font color="orange">Seçtiðiniz kullanýcý forum yardýmcýsý !<br> Forum yardýmcýlarý tüm forum bölümleri üzerinde yetki sahibidir.</font>';

$uyari_no[5] = 'Konuyu ve altýndaki tüm cevaplarý silmek istediðinize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=mesaj&amp;fno='.$_GET['fno'].'&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;o='.$_GET['o'].'&amp;fsayfa='.$_GET['fsayfa'].'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.'">Hayýr</a>';

$uyari_no[6] = '<font color="orange">Bu sayfaya sadece üyeler girebilir !</font><br><br>Giriþ yapmak için <a href="giris.php'.$git.'">týklayýn.</a> <br><br> Üye olmak için <a href="kayit.php">týklayýn.</a>';

$uyari_no[7] = 'Cevabý silmek istediðinize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=cevap&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;cevap_no='.$_GET['cevap_no'].'&amp;o='.$_GET['o'].'&amp;fsayfa='.$_GET['fsayfa'].'&amp;sayfa='.$_GET['sayfa'].'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$ks.$fs.'">Hayýr</a>';

$uyari_no[8] = 'Herhangi bir deðiþiklik yapmadýnýz.<br><br>Geri dönmek için <a href="profil_degistir.php?kosul=sifre">týklayýn.</a><meta http-equiv="Refresh" content="5;url=profil_degistir.php?kosul=sifre">';

$uyari_no[9] = '<font color="orange">Üye alýmý geçici bir süre için durdurulmuþtur !</font>';


//  UYARI ÝLETÝLERÝ  - SONU //









// GELEN VERÝYE GÖRE SAYFA HAZIRLANIYOR - BAÞI  //

if ( isset($_GET['bilgi']) )
{
		if ( (empty($bilgi_no[$_GET['bilgi']])) OR (is_numeric($_GET['bilgi']) == false) )
		{
			$sayfa_adi = 'Hatalý Adres !';
			$hata_baslik = 'Hatalý Adres !';
			$hata_icerik = 'Hatalý Adres !';
		}

		else
		{
			$sayfa_adi = 'Bilgi iletisi ';
			$hata_baslik = 'Bilgi iletisi :';
			$hata_icerik = $bilgi_no[$_GET['bilgi']];
		}
}



elseif ( isset($_GET['hata']) )
{
		if ( (empty($hata_no[$_GET['hata']])) OR (is_numeric($_GET['hata']) == false) )
		{
			$sayfa_adi = 'Hatalý Adres !';
			$hata_baslik = 'Hatalý Adres !';
			$hata_icerik = 'Hatalý Adres !';
		}

		else
		{
			$sayfa_adi = 'Hata iletisi ';
			$hata_baslik = 'Hata iletisi :';
			$hata_icerik = '<font color="red">'.$hata_no[$_GET['hata']].'</font>';
		}
}



elseif ( isset($_GET['uyari']) )
{
		if ( (empty($uyari_no[$_GET['uyari']])) OR (is_numeric($_GET['uyari']) == false) )
		{
			$sayfa_adi = 'Hatalý Adres !';
			$hata_baslik = 'Hatalý Adres !';
			$hata_icerik = 'Hatalý Adres !';
		}

		else
		{
			$sayfa_adi = 'Uyarý iletisi ';
			$hata_baslik = 'Uyarý iletisi :';
			$hata_icerik = $uyari_no[$_GET['uyari']];
		}
}



else
{
	$sayfa_adi = 'Hatalý Adres !';
	$hata_baslik = 'Hatalý Adres !';
	$hata_icerik = 'Hatalý Adres !';
}

// GELEN VERÝYE GÖRE SAYFA HAZIRLANIYOR - SONU  //




//  TEMA UYGULANIYOR    //

include 'baslik.php';

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/hata.html');


$ornek1->dongusuz(array('{HATA_BASLIK}' => $hata_baslik,
						'{HATA_ICERIK}' => $hata_icerik));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>