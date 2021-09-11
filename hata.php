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


@error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
@ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT);
@ini_set('magic_quotes_runtime', 0);

if (!defined('DOSYA_AYAR')) include 'ayar.php';
if (!defined('DOSYA_GERECLER')) include 'gerecler.php';



//  ZARARLI KODLAR TEM�ZLEN�YOR //

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





//  B�LG� �LET�LER�  - BA�I //


$bilgi_no[1] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].$ks.'#c'.$_GET['cevapno'].'">
�letiniz g�nderilmi�tir, okumak i�in <a href="konu.php?k='.$_GET['mesaj_no'].$ks.'#c'.$_GET['cevapno'].'">t�klay�n.</a>
<br>Foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno'].'">t�klay�n.</a>';

$bilgi_no[2] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].'">
�letiniz g�nderilmi�tir, okumak i�in <a href="konu.php?k='.$_GET['mesaj_no'].'">t�klay�n.</a>
<br>Foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno'].'">t�klay�n.</a>';

$bilgi_no[3] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].'&amp;f='.$_GET['fno'].$fs.'">
�letiniz de�i�tirilmi�tir, okumak i�in <a href="konu.php?k='.$_GET['mesaj_no'].'&amp;f='.$_GET['fno'].$fs.'">t�klay�n.</a>
<br>Foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno'].'">t�klay�n.</a>';

$bilgi_no[4] = '<meta http-equiv="Refresh" content="5;url=konu.php?k='.$_GET['mesaj_no'].$ks.'&amp;f='.$_GET['fno'].$fs.'#c'.$_GET['cevapno'].'">
�letiniz de�i�tirilmi�tir, okumak i�in <a href="konu.php?k='.$_GET['mesaj_no'].$ks.'&amp;f='.$_GET['fno'].$fs.'#c'.$_GET['cevapno'].'">t�klay�n.</a>
<br>Foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno'].'">t�klay�n.</a>';

$bilgi_no[5] = 'Konuyu ve alt�ndaki t�m cevaplar� silmek istedi�inize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=mesaj&amp;fno='.$_GET['fno'].'&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;o='.$_GET['o'].$fs.'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.'">Hay�r</a>';

$bilgi_no[6] = 'Konu ve t�m cevaplar� silinmi�tir.<br><br>Foruma geri d�nmek i�in <a href="forum.php?f='.$_GET['fno'].$fs.'">t�klay�n</a>';

$bilgi_no[7] = 'Cevab� silmek istedi�inize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=cevap&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;cevap_no='.$_GET['cevap_no'].'&amp;o='.$_GET['o'].$fs.$ks.'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.$ks.'">Hay�r</a>';

$bilgi_no[8] = 'Cevap silinmi�tir.<br><br>Konuya geri d�nmek i�in <a href="konu.php?k='.$_GET['mesaj_no'].$fs.$ks.'">t�klay�n.</a>';

$bilgi_no[9] = 'Se�ti�iniz konu ta��nm��t�r.<br><br>Geldi�iniz foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno1'].'">t�klay�n.</a><br>Konuyu ta��d���n�z foruma d�nmek i�in <a href="forum.php?f='.$_GET['fno2'].'">t�klay�n.</a>';

$bilgi_no[10] = 'Profiliniz G�ncellenmi�tir...<br><br>Profilinizi g�rmek i�in <a href="profil.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[11] = '�zel iletiniz g�nderilmi�tir.<br>G�nderilen kutusuna gitmek i�in <a href="ozel_ileti.php?kip=gonderilen">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=ozel_ileti.php?kip=gonderilen">';

$bilgi_no[12] = 'Forum ayarlar�n�z g�ncellenmi�tir.<br><br>Y�netim ana sayfas�na d�nmek i�in <a href="yonetim/index.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[13] = 'E-POSTANIZ G�NDER�LM��T�R...';

$bilgi_no[14] = 'Etkinle�tirme kodu ba�vurunuz tamamlanm��t�r.<br><br>Size gelen E-Postadaki ba�lant�y� t�klayarak hesab�n�z� etkinle�tirebilirsiniz.<br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$bilgi_no[15] = 'Kay�t i�leminiz ba�ar�yla tamamlanm��t�r. <br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$bilgi_no[16] = 'Kay�t i�leminiz ba�ar�yla tamamlanm��t�r.<br><br>Hesab�n�z� etkinle�tirmek i�in yapman�z gerekenler<br>size g�nderilen E-Postada anlat�lmaktad�r.<br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$bilgi_no[17] = 'Kay�t i�leminiz ba�ar�yla tamamlanm��t�r.<br><br>Hesab�n�z�n etkinle�tirilmesi i�in forum y�neticisinin onay�n� beklemelisiniz.';

$bilgi_no[18] = 'Hesab�n�z zaten etkinle�tirilmi�.';

$bilgi_no[19] = 'Hesab�n�z etkinle�tirilmi�tir.<br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$bilgi_no[20] = 'Yeni �ifre ba�vurunuz tamamlanm��t�r.<br><br>�ifrenizi s�f�rlaman�z i�in yapman�z gerekenler size g�nderilen<br>E-Postada anlat�lmaktad�r.<br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$bilgi_no[21] = 'Yeni �ifreniz olu�turulmu�tur.<br><br>Yeni �ifrenizle giri� yapmak i�in <a href="giris.php">t�klay�n�z.</a>';

$bilgi_no[22] = 'Yeni �ifre ba�vurunuz iptal edilmi�tir. Eski �ifreniz h�l� ge�erlidir.';

$bilgi_no[23] = 'Kullan�c� hesab� silinmi�tir.<br><br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php?kip=engelli">t�klay�n.</a>';

$bilgi_no[24] = 'Kullan�c�n�n engeli kald�r�lm��t�r.<br><br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php?kip=engelli">t�klay�n.</a><br><br>Engeli olmayan kullan�c�lar� g�rmek i�in <a href="yonetim/kullanicilar.php">t�klay�n.</a>';

$bilgi_no[25] = 'Kullan�c� hesab� etkinle�tirilmi�tir.<br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php?kip=etkisiz">t�klay�n.</a><br><br>Etkinle�tirilmi� kullan�c�lar� g�rmek i�in <a href="yonetim/kullanicilar.php">t�klay�n.</a>';

$bilgi_no[26] = 'Kullan�c� hesab� silinmi�tir.<br><br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php?kip=etkisiz">t�klay�n.</a>';

$bilgi_no[27] = 'Forum dal� i�inde bulunan; forumlar, alt forumlar, konular ve <br> cevaplar�yla beraber ba�ar�yla silinmi�tir.<br><br>Forum Y�netimi sayfas�na d�nmek i�in <a href="yonetim/forumlar.php">t�klay�n.</a>';

$bilgi_no[28] = 'T�m forumlar, se�mi� oldu�unuz forum dal�na ba�ar�yla ta��nm��t�r.<br><br>Forum Y�netimi sayfas�na d�nmek i�in <a href="yonetim/forumlar.php">t�klay�n.</a>';

$bilgi_no[29] = 'Forum, forumun konular� ve konular�n cevaplar� ba�ar�yla silinmi�tir.<br><br>Forum Y�netimi sayfas�na d�nmek i�in <a href="yonetim/forumlar.php">t�klay�n.</a>';

$bilgi_no[30] = 'Forumun konular� ve konular�n cevaplar� ba�ar�yla ta��nm��t�r.<br><br>Forum Y�netimi sayfas�na d�nmek i�in <a href="yonetim/forumlar.php">t�klay�n.</a>';

$bilgi_no[31] = 'Forum, se�ti�iniz forum dal�na ba�ar�yla ta��nm��t�r.<br><br>Forum Y�netimi sayfas�na d�nmek i�in <a href="yonetim/forumlar.php">t�klay�n.</a>';

$bilgi_no[32] = 'Kullan�c�n�n Profili G�ncellenmi�tir.<br><br>Y�netim ana sayfas�na d�nmek i�in <a href="yonetim/index.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[33] = 'Kullan�c� hesab� etkisizle�tirilmi�tir.<br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php">t�klay�n.</a><br><br>Etkinle�tirilmemi� kullan�c�lar� g�rmek i�in <a href="yonetim/kullanicilar.php?kip=etkisiz">t�klay�n.</a>';

$bilgi_no[34] = 'Kullan�c� hesab� silinmi�tir.<br><br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php">t�klay�n.</a>';

$bilgi_no[35] = 'Kullan�c� engellenmi�tir.<br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php">t�klay�n.</a><br><br>Engellenmi� kullan�c�lar� g�rmek i�in <a href="yonetim/kullanicilar.php?kip=engelli">t�klay�n.</a>';

$bilgi_no[36] = 'Forumdaki eski mesajlar silinmi�tir.';

$bilgi_no[37] = 'E-POSTALARINIZ YOLLANMI�TIR...';

$bilgi_no[38] = 'Veritaban� yede�iniz ba�ar�yla geri y�klenmi�tir.';

$bilgi_no[39] = 'Yasaklama bilgileri g�ncellenmi�tir.<br>Geri d�nmek i�in <a href="yonetim/yasaklamalar.php">t�klay�n.</a>';

$bilgi_no[40] = 'G�ncelleme Ba�ar�yla Tamamlanm��t�r.';

$bilgi_no[41] = 'Kullan�c� engellenmi�tir.<br>Geri d�nmek i�in <a href="yonetim/kullanicilar.php?kip=etkisiz">t�klay�n.</a><br><br>Engellenmi� kullan�c�lar� g�rmek i�in <a href="yonetim/kullanicilar.php?kip=engelli">t�klay�n.</a>';

$bilgi_no[42] = 'E-Posta adresiniz kaydedilmi�tir. <br><br>Adres de�i�ikli�in tamamlanmas� i�in yapman�z gerekenler <br>size g�nderilen E-Postada anlat�lmaktad�r.<br><br>Profilinizi g�rmek i�in <a href="profil.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[43] = '�ifreniz de�i�tirilmi�tir...<br><br>Profilinizi g�rmek i�in <a href="profil.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[44] = '�ifreniz ve E-Posta adresiniz kaydedilmi�tir. <br><br>Adres de�i�ikli�in tamamlanmas� i�in yapman�z gerekenler <br>size g�nderilen E-Postada anlat�lmaktad�r.<br><br>Profilinizi g�rmek i�in <a href="profil.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[45] = 'Yeni E-Posta adresiniz onaylanm�� ve de�i�tirilmi�tir.<br><br>Profilinizi g�rmek i�in <a href="profil.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil.php">';

$bilgi_no[46] = '�zel ileti ayarlar�n�z g�ncellenmi�tir.<br><br>Geri d�nmek i�in <a href="ozel_ileti.php?kip=ayarlar">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=ozel_ileti.php?kip=ayarlar">';

$bilgi_no[47] = 'Forumdaki eski �zel iletiler silinmi�tir.<br><br>Y�netim ana sayfas�na d�nmek i�in <a href="yonetim/index.php">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=yonetim/index.php">';

$bilgi_no[48] = '�ye ba�ar�yla olu�turulmu�tur, geri d�nmek i�in <a href="yonetim/yeni_uye.php">t�klay�n.</a><br><br>�yenin profilini g�rmek i�in <a href="profil.php?u='.$_GET['fno'].'">t�klay�n.</a><br>�yenin profilini de�i�tirmek i�in <a href="yonetim/kullanici_degistir.php?u='.$_GET['fno'].'">t�klay�n.</a>';

$bilgi_no[49] = 'Dosya silinmi�tir.<br><br>Geri d�nmek i�in <a href="yonetim/yuklemeler.php">t�klay�n.</a>';

$bilgi_no[50] = 'Dosya silinmi�tir.<br><br>Geri d�nmek i�in <a href="profil_degistir.php?kosul=yuklemeler">t�klay�n.</a>';


//  B�LG� �LET�LER�  - SONU	//







//  HATA �LET�LER�  - BA�I  //


$hata_no[1] = 'Son araman�z�n �zerinden belli bir s�re ge�meden yeni arama yapamazs�n�z !';

$hata_no[2] = 'T�m alanlar bo� b�rak�lamaz !<br>Arad���n�z s�zc�k 3 harfden uzun olmal�d�r !<br><br>L�tfen <a href="arama.php">geri</a> d�n�p aramak istedi�iniz s�zc��� ilgili b�l�me giriniz.';

$hata_no[3] = 'Bu konuyu ta��maya yetkiniz yok !';

$hata_no[4] = 'G�nderilen k�sm� bo� b�rak�lamaz !';

$hata_no[5] = 'E-posta ba�l��� en az 3, en fazla 60 karakterden olu�mal�d�r.<br><br>E-posta i�eri�i en az 3 karakterden olu�mal�d�r.';

$hata_no[6] = 'Yollad���n�z son iletinin �zerinden<br>'.$ayarlar['ileti_sure'].' saniye ge�meden ba�ka bir ileti g�nderemezsiniz.';

$hata_no[7] = 'Hatal� kullan�c� ad� !<br><br>G�ndermek istedi�iniz ki�iyi kontrol edip tekrar deneyiniz.';

$hata_no[8] = 'L�tfen E-Posta adresinizi yaz�n�z !';

$hata_no[9] = 'E-Posta adresiniz 70 karakterden uzun olamaz !';

$hata_no[10] = 'E-Posta adresiniz hatal� !';

$hata_no[11] = '<font color="#007900">Kay�t i�leminiz ba�ar�yla tamamlanm��t�r.</font> <br><br>Fakat sunucudaki bir hatadan dolay� E-postan�z g�nderilememi�tir !<br><br>�stedi�iniz zaman <a href="etkinlestir.php">buradan</a> etkinle�tirme kodu ba�vurusunda bulunabilirsiniz.';

$hata_no[12] = 'Bu E-Posta adresine ba�l� hesab�n�z zaten etkinle�tirilmi� !';

$hata_no[13] = 'Yazd���n�z E-Posta adresi veritaban�nda bulunmamaktad�r !';

$hata_no[14] = 'Se�ti�iniz forum veritaban�nda bulunmamaktad�r !';

$hata_no[15] = 'Bu foruma sadece y�neticiler girebilir !';

$hata_no[16] = 'Bu foruma sadece y�neticiler ve yard�mc�lar girebilir !';

$hata_no[17] = 'Bu foruma sadece, y�neticinin verdi�i �zel yetkilere sahip �yeler girebilir !';

$hata_no[18] = 'L�tfen kullan�c� ad� ve �ifrenizi giriniz !';

$hata_no[19] = 'Kullan�c� ad� en az 4, en fazla 20 karakter olmal�d�r !';

$hata_no[20] = '�ifreniz en az 5, en fazla 20 karakter olmal�d�r !';

$hata_no[21] = 'Be� ba�ar�s�z giri� denemesi yapt�n�z.<br>'.($ayarlar['kilit_sure'] / 60).' dakika boyunca hesab�n�z kilitlenmi�tir.';

$hata_no[22] = 'Hatal� kallan�c� ad� veya parola.<br>Caps Lock a��k kalm�� olabilir, �ifrelerde b�y�k/k���k harf ayr�m� vard�r.<br><br>L�tfen geri d�n�p <a href="giris.php">tekrar</a> deneyin.';

$hata_no[23] = 'Hesab�n�z hen�z etkinle�tirilmemi� !<br><br>Hesab�n�z� etkinle�tirmek i�in yapman�z gerekenler <br>size g�nderilen E-Postada anlat�lmaktad�r.<br><br><a href="etkinlestir.php">Etkinle�tirme kodunu tekrar yolla</a>';

$hata_no[24] = 'Hesab�n�z engellenmi�tir !';

$hata_no[25] = '�ok fazla kay�t giri�iminde bulundunuz. Daha sonra tekrar deneyin !';

$hata_no[26] = 'T�m b�l�mlerin doldurulmas� zorunludur !';

$hata_no[27] = 'Kullan�c� ad�nda ge�ersiz karakterler var ! <br><br>Latin ve T�rk�e harf, rakam, alt �izgi( _ ), tire ( - ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri ve bo�luk karakterini i�eremez.';

$hata_no[28] = 'Kullan�c� ad� en az 4, en fazla 20 karakter olmal�d�r !';

$hata_no[29] = 'Bu kullan�c� ad� yasaklanm��t�r, l�tfen ba�ka bir kullan�c� ad� deneyin !';

$hata_no[30] = 'Bu E-Posta adresi yasaklanm��t�r !';

$hata_no[31] = '"Ad Soyad - L�kap" alan�nda ge�ersiz karakterler var !<br><br>Latin ve T�rk�e harf, rakam, bo�luk, alt �izgi( _ ), tire ( - ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri i�eremez.';

$hata_no[32] = '"Ad Soyad - L�kap" en az 4, en fazla 30 karakter olmal�d�r !';

$hata_no[33] = 'Yazd���n�z �ifreler uyu�muyor !';

$hata_no[34] = '�ifrenizde ge�ersiz karakterler var ! <br><br>Latin harf, rakam, alt �izgi( _ ), tire ( - ), and ( & ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri, T�rk�e karakterleri ve bo�luk karakterini i�eremez.';

$hata_no[35] = '�ifreniz en az 5, en fazla 20 karakter olmal�d�r !';

$hata_no[36] = '�ehir ge�ersiz !';

$hata_no[37] = 'Do�um tarihi ge�ersiz !';

$hata_no[38] = 'Do�um tarihinin y�l k�sm� ge�ersiz !<br>L�tfen 1981 �eklinde 4 rakam ile yaz�n�z.';

$hata_no[39] = 'Silmeye �al��t���n�z forumun alt forumlar� var. �nce alt forumlar�n�  silin !';

$hata_no[40] = 'E-Posta adresiniz 70 karakterden uzun olamaz !';

$hata_no[41] = 'Kay�t g�venlik sorusunun cevab� hatal� !<br><br>Genelde buraya sadece robot programlar�n giremeyece�i �ok kolay sorular yaz�l�r.<br><br>Cevab� tahmin edemiyorsan�z forum y�neticisiyle ileti�ime ge�in.';

$hata_no[42] = 'Bu kullan�c� ad� kullan�lmaktad�r, l�tfen ba�ka bir isim deneyin !';

$hata_no[43] = 'Bu E-posta adresiyle daha �nce kay�t yap�lm��t�r !';

$hata_no[44] = 'Onay kodunu yanl�� girdiniz. L�tfen geri d�n�p tekrar deneyiniz.';

$hata_no[45] = 'Hatal� Adres !<br>L�tfen kontrol edip tekrar deneyin.';

$hata_no[46] = 'Forumda bu isimde bir �ye bulunmamaktad�r !';

$hata_no[47] = 'Se�ti�iniz konu veritaban�nda bulunmamaktad�r !';

$hata_no[48] = 'Etkinle�tirme kodunuzda eksik var, ya da adresi eksik kopyalad�n�z.<br><br>L�tfen kontrol edip tekrar deneyiniz.<br>Yine ayn� sorunla kar��la��rsan�z forum y�neticisine ba�vurun.';

$hata_no[49] = 'Etkinle�tirme kodunuz hatal�, ya da adresi eksik kopyalad�n�z.<br><br>L�tfen kontrol edip tekrar deneyiniz.<br>Yine ayn� sorunla kar��la��rsan�z forum y�neticisine ba�vurun.';

$hata_no[50] = 'Kilitli konular� de�i�tiremezsiniz !';

$hata_no[51] = 'Kilitli konular�n cevaplar�n� de�i�tiremezsiniz !';

$hata_no[52] = 'Bu iletiyi de�i�tirmeye yetkiniz yok !';

$hata_no[53] = '�leti ba�l��� en az 3, en fazla 53 karakterden olu�mal�d�r.<br><br>�leti i�eri�i en az 3 karakterden olu�mal�d�r.';

$hata_no[54] = 'Bu konuyu kilitlemeye veya a�maya yetkiniz yok !';

$hata_no[55] = 'Se�ti�iniz cevap veritaban�nda bulunmamaktad�r !';

$hata_no[56] = 'Bu iletiyi silmeye yetkiniz yok !';

$hata_no[57] = 'Kilitli konulara cevap yazamazs�n�z !';

$hata_no[58] = 'Bu foruma sadece y�neticiler cevap yazabilir !';

$hata_no[59] = 'Bu foruma sadece y�neticiler ve yard�mc�lar cevap yazabilir !';

$hata_no[60] = 'Bu foruma sadece, y�neticinin verdi�i �zel yetkilere sahip �yeler cevap yazabilir !';

$hata_no[61] = 'Site kurucusunu etkisizle�tiremezsiniz !';

$hata_no[62] = 'Arad���n�z �zel ileti bulunam�yor.<br>Silinmi� ya da okuma yetkiniz olmayabilir.';

$hata_no[63] = 'G�nderilen k�sm� bo� b�rak�lamaz !';

$hata_no[64] = '�zel ileti ba�l��� en az 3, en fazla 60 karakterden olu�mal�d�r.<br><br>�zel �leti i�eri�i en az 3 karakterden olu�mal�d�r.';

$hata_no[65] = 'Yollad���n�z son iletinin �zerinden '.$ayarlar['ileti_sure'].' saniye ge�meden ba�ka bir ileti g�nderemezsiniz.';

$hata_no[66] = 'Forumda bu isimde bir �ye bulunmamaktad�r.<br>L�tfen geri d�n�p tekrar deneyin.';

$hata_no[67] = 'G�nderdi�iniz ki�inin Gelen Kutusu dolu oldu�undan ileti g�nderilemedi.';

$hata_no[68] = 'Se�im yapmad�n�z !';

$hata_no[69] = 'Bu �zel iletiyi silmeye yetkiniz yok!';

$hata_no[70] = 'Kaydedilen kutunuz dolu.<br>Bo�altmadan ba�ka ileti kaydedemezsiniz.';

$hata_no[71] = 'Bu iletiyi kaydetmeye yetkiniz yok!';

$hata_no[72] = 'Kullan�c� ad� 20 karakterden uzun olamaz !';

$hata_no[73] = '* i�aretli b�l�mlerin doldurulmas� zorunludur !';

$hata_no[74] = 'Do�um tarihi ge�ersiz !<br>L�tfen tire(-)lerde dahil olmaz �zere 31-12-1985 �eklinde yaz�n�z.';

$hata_no[75] = 'Web Adresiniz 70 karakterden uzun olamaz !';

$hata_no[76] = 'Tema dizini ad�, alt �izgi( _ ) ve tire ( - ) d���ndaki �zel karakterleri ve T�rk�e karakterleri i�eremez !';

$hata_no[77] = 'Tema klas�r�n�n ad� 20 karakterden uzun olamaz !';

$hata_no[78] = '�mzan�z '.$ayarlar['imza_uzunluk'].' karakterden uzun olamaz !';

$hata_no[79] = 'ICQ Numaran�z 30 karakterden uzun olamaz !';

$hata_no[80] = 'AIM Ad�n�z 70 karakterden uzun olamaz !';

$hata_no[81] = 'MSN Messenger Ad�n�z 70 karakterden uzun olamaz !';

$hata_no[82] = 'Yahoo! Messenger Ad�n�z 70 karakterden uzun olamaz !';

$hata_no[83] = 'Skype Ad�n�z 70 karakterden uzun olamaz !';

$hata_no[84] = 'Y�klemeye �al��t���n�z resim bozuk !';

$hata_no[85] = 'Sadece jpeg, gif veya png resimleri y�klenebilir ! <br>E�er dosyan�z do�ru tipte ise bozuk olabilir.';

$hata_no[86] = 'Y�klemeye �al��t���n�z resim '.($ayarlar['resim_boyut']/1024).' kilobayt`dan b�y�k !';

$hata_no[87] = 'Y�klemeye �al��t���n�z resmin boyutlar� '.$ayarlar['resim_genislik'].'x'.$ayarlar['resim_yukseklik'].'`den b�y�k !';

$hata_no[88] = 'Dosya y�klenemedi !<br><br>Y�neticiyseniz FTP program�n�zdan dosyalar/resimler/yuklenen/<br>dizinine yazma hakk� vermeyi (chmod 777) deneyin.';

$hata_no[89] = 'Uzak resim, kontrol edilirken bir sorunla kar��la��ld�.<br>Sunucunun uzak dosya eri�imi kapat�lm�� olabilir ya da <br>adreste veya resim dosyas�nda bir sorun olabilir.';

$hata_no[90] = 'Eklemeye �al��t���n�z resim '.($ayarlar['resim_boyut']/1024).' kilobayt`dan b�y�k !';

$hata_no[91] = 'Eklemeye �al��t���n�z resmin boyutlar� '.$ayarlar['resim_genislik'].'x'.$ayarlar['resim_yukseklik'].'`den b�y�k !';

$hata_no[92] = 'E-Posta Adresini G�ster, Do�um Tarihini G�ster, �ehir G�ster ve <br>�evrimi�i Durumunu G�ster ayarlar� sadece a��k-kapal� de�eri alabilir !';

$hata_no[93] = 'Bu E-posta adresi ba�ka bir kullan�c�ya aittir !';

$hata_no[94] = 'YETK�N�Z YOK !!!';

$hata_no[95] = 'Buradaki yaz�lar� ancak forum �zerinden okuyabilirsiniz.';

$hata_no[96] = 'Yeni �ifre kodunuz hatal�, ya da adresi eksik kopyalad�n�z.<br>L�tfen kontrol edip tekrar deneyiniz.<br>Yine ayn� sorunla kar��la��rsan�z forum y�neticisine ba�vurun.';

$hata_no[97] = '�ok fazla giri�iminde bulundunuz. Daha sonra tekrar deneyin !';

$hata_no[98] = 'T�m alanlar� doldurmal�s�n�z! <i>(SMTP sunucusu ayarlar� hari�)</i>';

$hata_no[99] = 'Sayfa ba�l��� 100 karakterden uzun olamaz !';

$hata_no[100] = 'Alan ad� 100 karakterden uzun olamaz !';

$hata_no[101] = 'Dizin ad� 100 karakterden uzun olamaz !';

$hata_no[102] = 'Konu ve cevap say�s� alanlar�na en fazla 99 de�erini girebilirsiniz !';

$hata_no[103] = 'Konu ve cevap say�s� alanlar� sadece rakamdan olu�abilir !';

$hata_no[104] = '�erez ge�erlilik s�resi sadece rakamdan olu�abilir !';

$hata_no[105] = '�erez ge�erlilik s�releri en fazla 5 rakamdan olu�abilir !<br><br>Yani en fazla 99`999 dakika de�erini alabilir ki bu da 69 g�n eder.';

$hata_no[106] = '�ki ileti aras� bekleme s�resi sadece rakamdan olu�abilir !';

$hata_no[107] = '�ki ileti aras� bekleme s�resi en fazla 86`400 saniye alabilir ki bu da 24 saat eder.';

$hata_no[108] = 'Hesap kilit s�resi sadece rakamdan olu�abilir !';

$hata_no[109] = 'Be� ba�ar�s�z giri�ten sonra hesab�n kilitli kalaca�� s�re<br>en fazla 1440 dakika olabilir ki bu da 24 saat eder.';

$hata_no[110] = 'Kay�t sorusu a��k kapal� ayarlar� sadece a��k-kapal� de�eri alabilir !';

$hata_no[111] = 'Kay�t sorusu ve cevab� 100 karakterden uzun olamaz !';

$hata_no[112] = '�mza uzunlu�u 1 ila 500 aras� olabilir !';

$hata_no[113] = 'Tarih bi�imi en fazla 20 karakter olabilir !';

$hata_no[114] = 'Zaman dilimi 1 ila  4 karakter aras� olabilir !';

$hata_no[115] = 'Hatal� forum rengi !';

$hata_no[116] = 'Hesap Etkinle�tirme ayar� sadece kapal�, kullan�c� ve y�netici de�erlerini alabilir !';

$hata_no[117] = 'BBCode, �zel ileti ve G�ncel Konular ayarlar� sadece a��k-kapal� de�eri alabilir !';

$hata_no[118] = 'G�sterilecek g�ncel konu say�s� ayar� sadece rakamdan olu�abilir !';

$hata_no[119] = 'G�sterilecek g�ncel konu say�s� ayar� 50`den fazla olamaz !';

$hata_no[120] = 'Site kurucusu ad� 100 karakterden uzun olamaz !';

$hata_no[121] = 'Forum y�neticisi ad� 100 karakterden uzun olamaz !';

$hata_no[122] = 'Forum yard�mc�s� ad� 100 karakterden uzun olamaz !';

$hata_no[123] = 'Kay�tl� kullan�c� ad� 100 karakterden uzun olamaz !';

$hata_no[124] = 'Gelen, ula�an ve kaydedilen kutusu kota de�erleri en fazla 3 rakamdan olu�abilir !<br><br>Yani en fazla 999 de�erini alabilir.';

$hata_no[125] = 'Gelen, ula�an ve kaydedilen kutusu kota de�erleri sadece rakamdan olu�abilir !';

$hata_no[126] = 'Resim y�kleme �zelli�i sadece a��k-kapal� de�eri alabilir !';

$hata_no[127] = 'Uzak resim �zelli�i sadece a��k-kapal� de�eri alabilir !';

$hata_no[128] = 'Resim galerisi �zelli�i sadece a��k-kapal� de�eri alabilir !';

$hata_no[129] = 'Resim dosyas�n�n b�y�kl��� 1 ila 999 kb. aras� olabilir !';

$hata_no[130] = 'Resim boyutu en b�y�k 999 x 999 aras� olabilir !';

$hata_no[131] = 'Y�netici E-Posta adresi 100 karakterden uzun olamaz !';

$hata_no[132] = 'E-Posta y�ntemi sadece mail, sendmail ve smtp de�erlerini alabilir !';

$hata_no[133] = 'SMTP kimlik do�rulamas� alan� sadece true ve false de�erlerini alabilir !';

$hata_no[134] = 'SMTP sunucu adresi 100 karakterden uzun olamaz !';

$hata_no[135] = 'SMTP kullan�c� ad� 100 karakterden uzun olamaz !';

$hata_no[136] = 'SMTP �ifresi 100 karakterden uzun olamaz !';

$hata_no[137] = 'Site kurucusunu silemezsiniz !';

$hata_no[138] = 'Bir hata olu�tu ya da sayfaya do�rudan eri�meye �al���yorsunuz. <br>Yapmak istedi�iniz i�lemi <a href="yonetim/forumlar.php">Forum Y�netimi</a> sayfas�ndan se�iniz.';

$hata_no[139] = 'Se�ti�iniz forum dal� veritaban�nda bulunmamaktad�r !';

$hata_no[140] = 'Forum dal� ba�l���n� girmeyi unuttunuz !';

$hata_no[141] = 'Forum ba�l���n� girmeyi unuttunuz !';

$hata_no[142] = 'Ta��mak istedi�niz forum dal�n� se�meyi unuttunuz. <br><br>L�tfen geri d�n�p tekrar deneyin.';

$hata_no[143] = 'Ta��mak istedi�niz forumu se�meyi unuttunuz. <br><br>L�tfen geri d�n�p tekrar deneyin.';

$hata_no[144] = 'Y�netim Yetkiniz Yok !';

$hata_no[145] = '<a href="yonetim/kullanicilar.php">Bu sayfadan</a> istedi�iniz �yenin kullan�c� ad�n� t�klay�n.<br><br>A��lan "Kullan�c� Profilini De�i�tir" sayfas�ndaki, Di�er Yetkiler ba�lant�s�n� t�klay�n.<br><br>A��lan sayfadan �zel yetki vermek istedi�iniz forumu se�erek kullan�c�ya istedi�iniz �zel yetkiyi verebilirsiniz. ';

$hata_no[146] = 'Se�ti�iniz forumun yetkisi sadece y�neticilere verilmi�.<br>�zel bir �yeye izin veremezsiniz !';

$hata_no[147] = 'Site kurucusunun bilgilerini buradan de�i�teremezsiniz !';

$hata_no[148] = 'Yetki alan� verisi ge�ersiz !';

$hata_no[149] = 'Site kurucusunu engelleyemezsiniz !';

$hata_no[150] = 'Varsay�lan 5 Renkli temas� se�eneklerden kald�r�lamaz !';

$hata_no[151] = 'Bu sayfaya sadece site kurucusu girebilir.';

$hata_no[152] = 'Forum se�meyi unuttunuz !';

$hata_no[153] = 'G�n alan�na 1 ila 999 aras�nda bir say� girmelisiniz.';

$hata_no[154] = 'Se�mi� oldu�unuz grupta hi�bir �ye bulunmamaktad�r !';

$hata_no[155] = 'Sunucunuz s�k��t�r�lm�� dosya olu�turulmas�n� desteklemiyor !';

$hata_no[156] = 'Dosya Y�klenemedi, Dosya ad� al�namad� !<br><br>Bunun nedeni dosyan�n 2mb.`dan b�y�k olmas� ya da<br>dosya ad�n�n kabul edilemeyen karakterler i�ermesi olabilir. <br><br>Yede�i tablo tablo ayr� dosyalara b�lmeyi deneyin veya dosya ad�n� de�i�tirmeyi deneyin.';

$hata_no[157] = '2mb.`dan b�y�k yedek y�kleyemezsiniz. <br>Yede�i tablo tablo ayr� dosyalara b�lmeyi deneyin.';

$hata_no[158] = 'Sunucunuz s�k��t�r�lm�� dosya y�klemesini desteklemiyor !';

$hata_no[159] = 'Sadece .sql ve .gz uzant�l� dosyalar y�klenebilir !';

$hata_no[160] = 'BBCode, �zel ileti, Forum durumu, Portal kullan�m�, Kay�t Onay Kodu, SEO,<br> �ye Al�m�, Boyutland�rma, G�ncel Konular, B�l�m ve Konu g�r�nt�leyenler<br> ayarlar� sadece a��k-kapal� de�eri alabilir !';

$hata_no[161] = 'Se�ti�iniz forum kapat�lm��.<br>�zel bir �yeye izin veremezsiniz !';

$hata_no[162] = '�evrimi�i s�resi sadece rakamdan olu�abilir !';

$hata_no[163] = '�evrimi�i s�resi i�in en fazla 99 dakika de�erini girebilirsiniz !';

$hata_no[164] = 'Bu forum kapat�lm��t�r !';

$hata_no[165] = 'Bu foruma sadece y�neticiler konu a�abilir !';

$hata_no[166] = 'Bu foruma sadece y�neticiler ve yard�mc�lar konu a�abilir !';

$hata_no[167] = 'Bu foruma sadece, y�neticinin verdi�i �zel yetkilere sahip �yeler konu a�abilir !';

$hata_no[168] = 'Bu konu daha �nceden geri y�klenmi� veya silinmemi� !';

$hata_no[169] = 'Bu cevap daha �nceden geri y�klenmi� veya silinmemi� !';

$hata_no[170] = 'Bu konuyu �st veya alt konu yapmaya yetkiniz yok !';

$hata_no[171] = 'Kurmaya �al��t���n�z eklentinin ad�nda kabul edilmeyen karakterler var !';

$hata_no[172] = '/eklentiler dizinine yaz�lam�yor ! <br><br>Eklenti kurulumu i�in bu dizine yazma hakk� (chmod 777) vermelisiniz.';

$hata_no[173] = 'Belirtilen eklenti dosyas� bulunam�yor ! <br><br>T�klad���n�z adresi kontrol edip tekrar deneyin.';

$hata_no[174] = 'Bu eklenti zaten kurulu !';

$hata_no[175] = 'Sunucudaki bir hatadan dolay� onay E-Postas� g�nderilememi�tir !<br> <br>L�tfen daha sonra tekrar deneyin ve durumu y�neticiye bildirin.';

$hata_no[176] = 'Bu �ye kimseden �zel ileti kabul etmiyor !';

$hata_no[177] = 'Bu �ye sizden �zel ileti kabul etmiyor !';

$hata_no[178] = 'Bu �ye forumdan uzakla�t�r�lm�� !';

$hata_no[179] = 'Bu �yenin hesab� hen�z etkinle�tirilmemi� !';

$hata_no[180] = 'Taray�c�n�z �erez kabul etmiyor !<br>Taray�c�n�z�n �erez �zelli�i kapal� veya desteklemiyor olabilir.<br><br>Giri� yapabilmeniz i�in �erez �zelli�i gereklidir.<br>�erezlere izin verin veya ba�ka bir taray�c�da tekrar deneyin.';

$hata_no[181] = 'Sadece y�neticiler g�ncelleme yapabilir !<br><br>Y�netici olarak giri� yap�p tekrar deneyin.';

$hata_no[182] = 'Bu eklenti kurulu de�il !';

$hata_no[183] = 'Bu eklenti zaten etkin !';

$hata_no[184] = 'Bu eklenti zaten etkisiz !';

$hata_no[185] = 'Bu eklenti kulland���n�z s�r�m ile uyumsuz g�r�n�yor !';

$hata_no[186] = '"Ad Soyad - L�kap" alan�na girdi�iniz isim yasaklanm��t�r !';

$hata_no[187] = 'Kurulu eklentileri silemezsiniz !<br>�nce eklentiyi kald�r�p sonra silmeyi deneyin.';

$hata_no[188] = '�ifreniz yanl�� !<br><br>L�tfen <a href="profil_degistir.php?kosul=sifre">geri d�n�p</a> tekrar deneyiniz.';

$hata_no[189] = 'Kurmaya �al��t���n�z eklenti portal i�in ama siz portal kullanm�yor g�r�n�yorsunuz !';

$hata_no[190] = 'Hatal� ip adresi !';

$hata_no[191] = 'B�l�m yard�mc�s� ad� 100 karakterden uzun olamaz !';

$hata_no[192] = 'Bu forum konu a�maya kapat�lm��t�r !';

$hata_no[193] = 'Bu forum cevap yazmaya kapat�lm��t�r !';

$hata_no[194] = 'Se�ti�iniz forumun yetkisi sadece y�netici ve yard�mc�lara verilmi�.<br>�zel bir �yeye izin veremezsiniz !';

$hata_no[195] = 'Konuyu ta��d���n�z forumda yetkiniz yok !';

$hata_no[196] = 'Bu tema kulland���n�z s�r�m ile uyumsuz g�r�n�yor !';

$hata_no[197] = 'Se�eneklerde olmayan bir tema varsay�lan olarak ayarlanamaz !<br>Temay� �nce se�enekler aras�na ekleyin.';

$hata_no[198] = '<font color="#007900">Kay�t i�leminiz ba�ar�yla tamamlanm��t�r.</font> <br><br>Fakat sunucudaki bir hatadan dolay� E-postan�z g�nderilememi�tir !<br><br>Giri� yapmak i�in <a href="giris.php">t�klay�n.</a>';

$hata_no[199] = '<font color="#007900">Kay�t i�leminiz ba�ar�yla tamamlanm��t�r.</font> <br><br>Fakat sunucudaki bir hatadan dolay� E-postan�z g�nderilememi�tir !<br><br>Hesab�n�z�n etkinle�tirilmesi i�in forum y�neticisinin onay�n� beklemelisiniz.';

$hata_no[200] = 'Bu eklenti etkisizle�tirmeyi desteklemiyor !';

$hata_no[201] = 'Grup ad�nda karakterler var !<br><br>Latin ve T�rk�e harf, rakam, bo�luk, alt �izgi( _ ), tire ( - ), nokta ( . ) kullan�labilir. <br>Bunlar�n d���ndaki �zel karakterleri i�eremez.';

$hata_no[202] = 'Grup ad� en az 4, en fazla 30 karakter olmal�d�r !';

$hata_no[203] = 'Bu grup ad� kullan�lmaktad�r, ba�ka bir ad deneyin !';

$hata_no[204] = 'Forumda b�yle bir grup bulunmamaktad�r !';

$hata_no[205] = 'Grubun b�l�m yard�mc�l��� yetkisini de�i�tirmek i�in �nce<br><a href="yonetim/ozel_izinler.php">�zel izinler</a> sayfas�nda g�r�nen b�l�m y�netme izinlerini al�n !';

$hata_no[206] = 'Arad���n�z dosya bulunam�yor.<br>Dosya daha �nceden silinmi� olabilir. L�tfen kontrol edip tekrar deneyin.';


//  HATA �LET�LER�  - SONU	//










//  UYARI �LET�LER�  - BA�I //


$uyari_no[1] = '<font color="orange">phpKF s�r�m 1.90 g�ncellemesi zaten yap�lm�� !</font>';

$uyari_no[2] = '<font color="orange">�zel �leti hizmeti kapat�lm��t�r !</font>';

$uyari_no[3] = '<font color="orange">Se�ti�iniz kullan�c� bir y�netici !<br> Y�neticilerin yetkileri s�n�rs�zd�r.</font>';

$uyari_no[4] = '<font color="orange">Se�ti�iniz kullan�c� forum yard�mc�s� !<br> Forum yard�mc�lar� t�m forum b�l�mleri �zerinde yetki sahibidir.</font>';

$uyari_no[5] = 'Konuyu ve alt�ndaki t�m cevaplar� silmek istedi�inize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=mesaj&amp;fno='.$_GET['fno'].'&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;o='.$_GET['o'].'&amp;fsayfa='.$_GET['fsayfa'].'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$fs.'">Hay�r</a>';

$uyari_no[6] = '<font color="orange">Bu sayfaya sadece �yeler girebilir !</font><br><br>Giri� yapmak i�in <a href="giris.php'.$git.'">t�klay�n.</a> <br><br> �ye olmak i�in <a href="kayit.php">t�klay�n.</a>';

$uyari_no[7] = 'Cevab� silmek istedi�inize emin misiniz ?<br><br><a href="mesaj_sil.php?onay=kabul&amp;kip=cevap&amp;mesaj_no='.$_GET['mesaj_no'].'&amp;cevap_no='.$_GET['cevap_no'].'&amp;o='.$_GET['o'].'&amp;fsayfa='.$_GET['fsayfa'].'&amp;sayfa='.$_GET['sayfa'].'">Evet</a> &nbsp; - &nbsp; <a href="konu.php?k='.$_GET['mesaj_no'].$ks.$fs.'">Hay�r</a>';

$uyari_no[8] = 'Herhangi bir de�i�iklik yapmad�n�z.<br><br>Geri d�nmek i�in <a href="profil_degistir.php?kosul=sifre">t�klay�n.</a><meta http-equiv="Refresh" content="5;url=profil_degistir.php?kosul=sifre">';

$uyari_no[9] = '<font color="orange">�ye al�m� ge�ici bir s�re i�in durdurulmu�tur !</font>';


//  UYARI �LET�LER�  - SONU //









// GELEN VER�YE G�RE SAYFA HAZIRLANIYOR - BA�I  //

if ( isset($_GET['bilgi']) )
{
		if ( (empty($bilgi_no[$_GET['bilgi']])) OR (is_numeric($_GET['bilgi']) == false) )
		{
			$sayfa_adi = 'Hatal� Adres !';
			$hata_baslik = 'Hatal� Adres !';
			$hata_icerik = 'Hatal� Adres !';
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
			$sayfa_adi = 'Hatal� Adres !';
			$hata_baslik = 'Hatal� Adres !';
			$hata_icerik = 'Hatal� Adres !';
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
			$sayfa_adi = 'Hatal� Adres !';
			$hata_baslik = 'Hatal� Adres !';
			$hata_icerik = 'Hatal� Adres !';
		}

		else
		{
			$sayfa_adi = 'Uyar� iletisi ';
			$hata_baslik = 'Uyar� iletisi :';
			$hata_icerik = $uyari_no[$_GET['uyari']];
		}
}



else
{
	$sayfa_adi = 'Hatal� Adres !';
	$hata_baslik = 'Hatal� Adres !';
	$hata_icerik = 'Hatal� Adres !';
}

// GELEN VER�YE G�RE SAYFA HAZIRLANIYOR - SONU  //




//  TEMA UYGULANIYOR    //

include 'baslik.php';

$ornek1 = new phpkf_tema();
$ornek1->tema_dosyasi('./temalar/'.$ayarlar['temadizini'].'/hata.html');


$ornek1->dongusuz(array('{HATA_BASLIK}' => $hata_baslik,
						'{HATA_ICERIK}' => $hata_icerik));

if((strlen(SATIR1)==828)AND(strlen(SATIR2)==704)AND(strlen($avh)==251)AND(strlen($enst)==355))eval($avh);

?>