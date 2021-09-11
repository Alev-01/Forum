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


function HangiSayfada($sayfano, $baslik)
{
	global $forum_index, $portal_index;

	switch ($sayfano)
	{
		case -1;
		$sayfa = 'Kullan�c� ��k�� yapt�';
		break;

		case 0;
		$sayfa = $baslik;
		break;

		case 1;
		$sayfa = '<a href="'.$forum_index.'">Forum Ana Sayfas�</a>';
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
		if (isset($uyeadi[1])) $sayfa = 'Profil G�r�nt�leme: <a href="'.linkver('profil.php?u='.$uyeno[1].'&kim='.$uyeadi[1], $uyeadi[1]).'">'.$uyeadi[1].'</a>';
		else $sayfa = '<a href="profil.php?u='.$uyeno[1].'">Profiline Bak�yor</a>';
		break;

		case 5;
		$sayfa = '<a href="cevrimici.php">�evrimi�i Sayfas�</a>';
		break;

		case 6;
		$sayfa = '<a href="bbcode_yardim.php">Yard�m Sayfas�</a>';
		break;

		case 7;
		$sayfa = '<a href="uyeler.php">�yeler Sayfas�</a>';
		break;

		case 8;
		$sayfa = '<a href="giris.php">Giri� Sayfas�</a>';
		break;

		case 9;
		$sayfa = '<a href="kayit.php">Kay�t Sayfas�</a>';
		break;

		case 10;
		$sayfa = '<a href="arama.php">Arama Sayfas�</a>';
		break;

		case 11;
		$konuno = explode(',', $sayfano);
		$sayfa = 'Ba�l�k Ta��ma: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik).'">'.$baslik.'</a>';
		break;

		case 12;
		$uyeno = explode(': ', $baslik);
		$sayfa = 'E-Posta G�nderme: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 13;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Konu De�i�tirme �nizlemesi: ', $baslik);
		$sayfa = 'Konu De�i�tirme �nizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 14;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap De�i�tirme �nizlemesi: ', $baslik);
		$sayfa = 'Cevap De�i�tirme �nizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'#c'.$konuno[2].'">'.$baslik[1].'</a>';
		break;

		case 15;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Konu De�i�tirme: ', $baslik);
		$sayfa = 'Konu De�i�tirme: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 16;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap De�i�tirme: ', $baslik);
		$sayfa = 'Cevap De�i�tirme: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'#c'.$konuno[2].'">'.$baslik[1].'</a>';
		break;

		case 17;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Yazma �nizlemesi: ', $baslik);
		$sayfa = 'Cevap Yazma �nizlemesi: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 18;
		$forumno = explode(',', $sayfano);
		$baslik = explode('Yeni Konu Olu�turma �nizlemesi: ', $baslik);
		$sayfa = 'Yeni Konu Olu�turma �nizlemesi: <a href="'.linkver('forum.php?f='.$forumno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 19;
		$konuno = explode(',', $sayfano);
		$baslik = explode('Cevap Yazma: ', $baslik);
		$sayfa = 'Cevap Yazma: <a href="'.linkver('konu.php?k='.$konuno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 20;
		$forumno = explode(',', $sayfano);
		$baslik = explode('Yeni Konu Olu�turma: ', $baslik);
		$sayfa = 'Yeni Konu Olu�turma: <a href="'.linkver('forum.php?f='.$forumno[1], $baslik[1]).'">'.$baslik[1].'</a>';
		break;

		case 21;
		$sayfa = '�zel ileti Okuma';
		break;

		case 22;
		$sayfa = '�zel ileti �nizlemesi';
		break;

		case 23;
		$sayfa = '�zel ileti Yazma';
		break;

		case 24;
		$sayfa = '�zel ileti Ayarlar�';
		break;

		case 25;
		$sayfa = '�zel iletiler Ula�an Kutusu';
		break;

		case 26;
		$sayfa = '�zel iletiler G�nderilen Kutusu';
		break;

		case 27;
		$sayfa = '�zel iletiler Kaydedilen Kutusu';
		break;

		case 28;
		$sayfa = '�zel iletiler Gelen Kutusu';
		break;

		case 29;
		$sayfa = 'E-Posta ve �ifre De�i�tirme';
		break;

		case 30;
		$sayfa = 'Profil De�i�tirme';
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
		$sayfa = '<a href="yeni_sifre.php">Yeni �ifre Ba�vurusu</a>';
		break;

		case 34;
		$sayfa = '<a href="ymesaj.php">Okunmam�� iletiler</a>';
		break;

		case 35;
		$sayfa = '<a href="etkinlestir.php">Etkinle�tirme Kodu Ba�vurusu</a>';
		break;

		case 36;
		$sayfa = '<a href="galeri.php">Kullan�c� Resim Galerisi</a>';
		break;

		case 37;
		$uyeno = explode(': ', $baslik);
		$sayfa = '�ye Konu Arama: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 38;
		$uyeno = explode(': ', $baslik);
		$sayfa = '�ye Cevap Arama: <a href="'.linkver('profil.php?kim='.$uyeno[1], $uyeno[1]).'">'.$uyeno[1].'</a>';
		break;

		case 39;
		$sayfa = 'Hata Sayfas�';
		break;

		case 40;
		$sayfa = 'Y�klemeler';
		break;

		case 41;
		$sayfa = '<a href="mobil.php">'.$baslik.'</a>';
		break;

		case 42;
		$sayfa = '<a href="uyeler.php?kip=grup">Yetkililer ve Gruplar Sayfas�</a>';
		break;



		default:
		$sayfa = $baslik;
	}
	return $sayfa;
}

?>