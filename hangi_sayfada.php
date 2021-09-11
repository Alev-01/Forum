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


function HangiSayfada($sayfano, $baslik)
{
	global $forum_index, $portal_index;

	switch ($sayfano)
	{
		case -1;
		$sayfa = 'Kullanýcý çýkýþ yaptý';
		break;

		case 0;
		$sayfa = $baslik;
		break;

		case 1;
		$sayfa = '<a href="'.$forum_index.'">Forum Ana Sayfasý</a>';
		break;

		case 2;
		$konuno = explode(',', $sayfano);
		$konu = explode(' : ', $baslik);
		$sayfa = 'Konu: <a href="'.linkver('konu.php?k='.$konuno[1], $konu[0]).'">'.$baslik.'</a>';
		break;

		case 3;
		$forumno = explode(',', $sayfano);
		$forum = explode(' : ', $baslik);
		$sayfa = 'Forum: <a href="'.linkver('forum.php?f='.$forumno[1], $forum[0]).'">'.$baslik.'</a>';
		break;

		case 4;
		$uyeno = explode(',', $sayfano);
		$uyeadi = explode(': ', $baslik);
		if (isset($uyeadi[1])) $sayfa = 'Profil Görüntüleme: <a href="'.linkver('profil.php?u='.$uyeno[1].'&kim='.$uyeadi[1], $uyeadi[1]).'">'.$uyeadi[1].'</a>';
		else $sayfa = '<a href="profil.php?u='.$uyeno[1].'">Profiline Bakýyor</a>';
		break;

		case 5;
		$sayfa = '<a href="cevrimici.php">Çevrimiçi Sayfasý</a>';
		break;

		case 6;
		$sayfa = '<a href="bbcode_yardim.php">Yardým Sayfasý</a>';
		break;

		case 7;
		$sayfa = '<a href="uyeler.php">Üyeler Sayfasý</a>';
		break;

		case 8;
		$sayfa = '<a href="giris.php">Giriþ Sayfasý</a>';
		break;

		case 9;
		$sayfa = '<a href="kayit.php">Kayýt Sayfasý</a>';
		break;

		case 10;
		$sayfa = '<a href="arama.php">Arama Sayfasý</a>';
		break;

		case 11;
		$konuno = explode(',', $sayfano);
		$sayfa = 'Baþlýk Taþýma: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik).'">'.$baslik.'</a>';
		break;

		case 12;
		$uyeno = explode(': ', $baslik);
		$sayfa = 'E-Posta Gönderme: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 13;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Konu Deðiþtirme Önizlemesi: ', $baslik);
		$sayfa = 'Konu Deðiþtirme Önizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 14;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Deðiþtirme Önizlemesi: ', $baslik);
		$sayfa = 'Cevap Deðiþtirme Önizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'#c'.$konuno[2].'">'.$baslik[1].'</a>';
		break;

		case 15;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Konu Deðiþtirme: ', $baslik);
		$sayfa = 'Konu Deðiþtirme: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 16;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Deðiþtirme: ', $baslik);
		$sayfa = 'Cevap Deðiþtirme: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'#c'.$konuno[2].'">'.$baslik[1].'</a>';
		break;

		case 17;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Yazma Önizlemesi: ', $baslik);
		$sayfa = 'Cevap Yazma Önizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 18;
		$forumno = explode(',', $sayfano);
		$baslik = explode('Yeni Konu Oluþturma Önizlemesi: ', $baslik);
		$sayfa = 'Yeni Konu Oluþturma Önizlemesi: <a href="'.linkver('forum.php?f='.$forumno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 19;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Yazma: ', $baslik);
		$sayfa = 'Cevap Yazma: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 20;
		$forumno = explode(',', $sayfano);
		$baslik = explode('Yeni Konu Oluþturma: ', $baslik);
		$sayfa = 'Yeni Konu Oluþturma: <a href="'.linkver('forum.php?f='.$forumno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 21;
		$sayfa = 'Özel ileti Okuma';
		break;

		case 22;
		$sayfa = 'Özel ileti Önizlemesi';
		break;

		case 23;
		$sayfa = 'Özel ileti Yazma';
		break;

		case 24;
		$sayfa = 'Özel ileti Ayarlarý';
		break;

		case 25;
		$sayfa = 'Özel iletiler Ulaþan Kutusu';
		break;

		case 26;
		$sayfa = 'Özel iletiler Gönderilen Kutusu';
		break;

		case 27;
		$sayfa = 'Özel iletiler Kaydedilen Kutusu';
		break;

		case 28;
		$sayfa = 'Özel iletiler Gelen Kutusu';
		break;

		case 29;
		$sayfa = 'E-Posta ve Þifre Deðiþtirme';
		break;

		case 30;
		$sayfa = 'Profil Deðiþtirme';
		break;

		case 31;
		$sayfa = '<a href="rss.php">RSS Beslemesi</a>';
		break;

		case 32;
		$forumno = explode(',', $sayfano);
		$baslik = explode('RSS Beklemesi: ', $baslik);
		$sayfa = 'RSS Beslemesi: <a href="rss.php?f='.$forumno[1].'">'.$baslik[1].'</a>';
		break;

		case 33;
		$sayfa = '<a href="yeni_sifre.php">Yeni Þifre Baþvurusu</a>';
		break;

		case 34;
		$sayfa = '<a href="ymesaj.php">Okunmamýþ iletiler</a>';
		break;

		case 35;
		$sayfa = '<a href="etkinlestir.php">Etkinleþtirme Kodu Baþvurusu</a>';
		break;

		case 36;
		$sayfa = '<a href="galeri.php">Kullanýcý Resim Galerisi</a>';
		break;

		case 37;
		$uyeno = explode(': ', $baslik);
		$sayfa = 'Üye Konu Arama: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 38;
		$uyeno = explode(': ', $baslik);
		$sayfa = 'Üye Cevap Arama: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 39;
		$sayfa = 'Hata Sayfasý';
		break;

		case 40;
		$sayfa = 'Yüklemeler';
		break;

		case 41;
		$sayfa = '<a href="mobil.php">'.$baslik.'</a>';
		break;

		case 42;
		$sayfa = '<a href="uyeler.php?kip=grup">Yetkililer ve Gruplar Sayfasý</a>';
		break;



		default:
		$sayfa = $baslik;
	}
	return $sayfa;
}

?>