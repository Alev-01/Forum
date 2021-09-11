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


class eposta_yolla
{
	var $normal = false;
	var $smtp = false;

	var $sunucu;
	var $port = 587;  // T�rk Telekom 25. portu engelledi�i i�in 587 kullan�yoruz.
	var $kullanici_adi;
	var $sifre;
	var $smtp_dogrulama;

	var $gonderen;
	var $gonderen_adi;
	var $kime;
	var $kopya;
	var $yanitla;
	var $hata_bilgi;

	var $konu;
	var $icerik;
	var $baslik;



	// g�nderilecek adres
	function GonderilenAdres($adres)
	{
		$this->kime = $adres;
	}

	// g�nderene bir kopya yolla
	function DigerAdres($adres)
	{
		$this->kopya = $adres;
	}

	// yan�tlama adresi
	function YanitlamaAdres($adres)
	{
		$this->yanitla = $adres;
	}

	// mail fonksiyonu kullan
	function MailKullan()
	{
		$this->normal = true;
	}

	// smtp kullan
	function SMTPKullan()
	{
		$this->smtp = true;
	}




	//  E-POSTA BA�LIK B�LG�LER�    //

	function Baslik_Olustur()
	{
		$sunucu_ip = $_SERVER['REMOTE_ADDR'];
		if ($this->normal == true) $this->baslik  = 'From: '.$this->gonderen_adi.' <'.$this->gonderen.">\r\n";
		$this->baslik .= 'Reply-To: '.$this->yanitla."\r\n";
		$this->baslik .= 'Return-Path: '.$this->yanitla."\r\n";
		$this->baslik .= 'Message-ID: <'.md5(uniqid(time())).'@'.$_SERVER['SERVER_NAME'].'> '."\r\n";
		$this->baslik .= 'X-Priority: 3'."\r\n";
		$this->baslik .= "X-Originating-IP: {$sunucu_ip}\r\n";
		$this->baslik .= "X-Originating-Email: $_SERVER[SCRIPT_NAME]\r\n";
		$this->baslik .= "X-Sender: $_SERVER[SCRIPT_NAME]\r\n";
		$this->baslik .= 'X-Mailer: PHP_KOLAY_FORUM'."\r\n";
		$this->baslik .= 'MIME-Version: 1.0'."\r\n";
		$this->baslik .= 'Content-Transfer-Encoding: 8bit'."\r\n";
		$this->baslik .= 'Content-Type: text/plain; charset="iso-8859-9"'."\r\n";
		$this->baslik .= 'Importance: Normal'."\r\n";
	}




	//  SMTP E-POSTA Y�NTEM�    //

	function smtp_mail($kime,$konu,$message,$headers)
	{
		// SMTP sunucuya ba�lan�l�yor
		$baglan = @fsockopen($this->sunucu, $this->port, $errno, $errstr, 1);

		if (!$baglan)
		{
			$this->hata_bilgi = "Hatal� Adres - SMTP sunucuya ba�lan�lamad� !<br>SMTP sunucu adresini kontrol edin.<p>".$errno.'<p>'.$errstr;
			return false;
		}


		// ba�lant� sonucu
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "220")
		{
			$this->hata_bilgi = "SMTP sunucuya ba�lan�lamad� !<br>SMTP sunucu adresini kontrol edin.<p>".$satir;
			return false;
		}


		// EHLO (selamlamaya) kar��l�k bekleniyor
		fputs($baglan, "EHLO ".$this->sunucu."\r\n");
		$satir = fgets($baglan,256);

		// EHLO cevap vermezse HELO giriliyor
		if (substr($satir,0,3) != "250")
		{
			// HELO (selamlamaya) kar��l�k bekleniyor
			fputs($baglan, "HELO ".$this->sunucu."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "250")
			{
				$this->hata_bilgi = "SMTP sunucu selamlamaya kar��l�k vermiyor !<p>".$satir;
				return false;
			}
		}

		else
		{
			while ($satir = fgets($baglan,256))
			{
				if (substr($satir,3,1) != '-') break;
			}
		}



		// kimlik do�rulamas� kullan i�aretli ise
		if ($this->smtp_dogrulama == true)
		{
			// kimlik do�rulamas�
			fputs($baglan, "auth login\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "334")
			{
				$this->hata_bilgi = "SMTP kimlik do�rulamas� ba�ar�s�z !<p>".$satir;
				return false;
			}


			// base64 kullan�c� ad� giriliyor
			fputs($baglan, base64_encode($this->kullanici_adi)."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "334")
			{
				$this->hata_bilgi = "Kimlik do�rulanamad� !<br>SMTP kullan�c� ad�n�z� kontrol edin.<p>".$satir;
				return false;
			}


			// base64 �ifre giriliyor
			fputs($baglan, base64_encode($this->sifre)."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "235")
			{
				$this->hata_bilgi = "Kimlik do�rulanamad� !<br>SMTP kullan�c� ad� ve �ifresinizi kontrol edin.<p>".$satir;
				return false;
			}
		}



		// E-Posta g�nderen
		fputs($baglan, "MAIL FROM: <".$this->gonderen.">\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			if ($this->smtp_dogrulama == true) $this->hata_bilgi = "G�nderen adresi ba�ar�s�z !<p> SMTP sunucu sadece giri� yap�lan hesab�n adresi ile E-Posta yollamaya izin veriyor olabilir.<p>".$satir;

			else $this->hata_bilgi = "G�nderen adresi ba�ar�s�z !<p> SMTP sunucu sadece giri� yap�lan hesab�n adresi ile E-Posta yollamaya izin veriyor olabilir;<br> Veya SMTP sunucu kimlik do�rulamas� gerektiriyor olabilir !<p>".$satir;

			return false;
		}


		// E-Posta g�nderilen
		fputs($baglan, "RCPT TO: <$kime>\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			$this->hata_bilgi = "G�nderilen adresi ba�ar�s�z !<p>".$satir;
			return false;
		}


		// DATA (veri) onay� al
		fputs($baglan, "DATA\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "354")
		{
			$this->hata_bilgi = "DATA (veri) onay� al�namad� !<p>".$satir;
			return false;
		}


		// g�nderilen, g�nderen, e-posta konusu, ba�l�k ve e-posta i�eri�i
		fputs($baglan, "To: $kime\r\nFrom: ".$this->gonderen."\r\nSubject: $konu\r\n$headers\r\n\r\n$message\r\n.\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			$this->hata_bilgi = "E-Posta i�eri�i g�nderilemedi !<p>".$satir;
			return false;
		}


		// ��k�� yap�l�yor
		fputs($baglan,"QUIT\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "221")
		{
			$this->hata_bilgi = "SMTP ��k��� yap�lamad� !<p>".$satir;
			return false;
		}

		return true;
	}




	//  E-POSTA YOLANIYOR   //

	function Yolla()
	{
		if ($this->normal == true)
		{
			$this->Baslik_Olustur();

			$yollandi = @mail($this->kime, $this->konu, $this->icerik, $this->baslik);

			if(!$yollandi)
			{
				$this->hata_bilgi = '�rnek mail() fonksiyonu olu�turulamad� !';
				return false;
			}


			// g�nderene bir kopya yolla
			if ($this->kopya != '')
			{
				$this->Baslik_Olustur();

				$yollandi = @mail($this->kopya, $this->konu, $this->icerik, $this->baslik);

				if(!$yollandi)
				{
					$this->hata_bilgi = '�rnek mail() fonksiyonu olu�turulamad� !';
					return false;
				}
			}
			return true;
		}


		elseif ($this->smtp == true)
		{
			$this->Baslik_Olustur();

			$yollandi = $this->smtp_mail($this->kime, $this->konu, $this->icerik, $this->baslik);

			if(!$yollandi) return false;


			// g�nderene bir kopya yolla
			if ($this->kopya != '')
			{
				$this->Baslik_Olustur();

				$yollandi = $this->smtp_mail($this->kopya, $this->konu, $this->icerik, $this->baslik);

				if(!$yollandi) return false;
			}
			return true;
		}


		else
		{
			$this->hata_bilgi = 'Yanl�� E-Posta Y�ntemi !<p>Y�netim Masas� - Genel Ayarlar sayfas�ndaki <br>"E-Posta g�ndermede kullan�lacak y�ntem" alan�ndan bir se�im yap�n.';
			return false;
		}
	}
}

?>