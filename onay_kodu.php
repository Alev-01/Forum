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


session_start();

if((isset($_GET['oturum'])) and (isset($_GET['a'])) and ($_GET['oturum'] == $_COOKIE['PHPSESSID']))
{

	function izgara($im, $w, $h, $s, $renk)
	{
		for($ih=1; $ih<$h/$s; $ih++){imageline($im, 0, $ih*$s, $w, $ih*$s, $renk);}
	}

	function izgara2($im, $w, $h, $s, $renk)
	{
		for($iw=1; $iw<$w/$s; $iw++){imageline($im, $iw*$s, 0, $iw*$s, $w, $renk);}
	}


	header("Content-type: image/png");
	$im = imagecreatetruecolor(115, 20);



	$beyaz = imagecolorallocate($im, 248, 248, 248);
	$gri = imagecolorallocate($im, 220, 220, 220);
	$siyah = imagecolorallocate($im, 50, 50, 50);
	$mavi = imagecolorallocate($im, 0, 140, 255);
	$yesil = imagecolorallocate($im, 0, 128, 0);
	$sari = imagecolorallocate($im, 180, 180, 0);
	$kirmizi = imagecolorallocate($im, 220, 20, 20);
	$bordo = imagecolorallocate($im, 181, 38, 217);
	$turkuaz = imagecolorallocate($im, 55, 192, 200);


	imagefilledrectangle($im, 0, 0, 115, 20, $beyaz);


	$metina = strtoupper(md5(microtime()));
	$metinb = strtoupper(md5(microtime()));
	$artalan = strtoupper(md5(microtime()));
	$artalan2 = strtoupper(md5(microtime()));


	$_SESSION['onay_kodu'] = $metina[15].$metinb[2].$metina[29].$metinb[21].$metina[12].$metinb[8];


	$harf1 = $metina[15];
	$harf2 = $metinb[2];
	$harf3 = $metina[29];
	$harf4 = $metinb[21];
	$harf5 = $metina[12];
	$harf6 = $metinb[8];

	$yer1 = rand(0,14);
	$yer2 = rand(16,25);
	$yer3 = rand(30,44);
	$yer4 = rand(50,65);
	$yer5 = rand(70,90);
	$yer6 = rand(90,105);


	imagestring($im, 4, 0, -5, $artalan, $gri);
	imagestring($im, 5, 0, 6, $artalan2, $gri);


	$izgara = imagecolorallocate($im, 55, 192, 200);
	imagesetstyle($im, array($beyaz, $izgara));
	izgara($im, 115, 20, 14, IMG_COLOR_STYLED);


	$izgara2 = imagecolorallocate($im, 255, 170, 220);
	imagesetstyle($im, array($beyaz, $izgara2));
	izgara2($im, 115, 20, 14, IMG_COLOR_STYLED);


	imagestring($im, 5, $yer1, rand(-2,6), $harf1, $mavi);
	imagestring($im, 5, $yer2, rand(-2,6), $harf2, $bordo);
	imagestring($im, 5, $yer3, rand(-2,6), $harf3, $sari);
	imagestring($im, 5, $yer4, rand(-2,6), $harf4, $kirmizi);
	imagestring($im, 5, $yer5, rand(-2,6), $harf5, $yesil);
	imagestring($im, 5, $yer6, rand(-2,6), $harf6, $turkuaz);


	imagepng($im);
	imagedestroy($im);
}
?>