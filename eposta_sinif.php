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


class eposta_yolla
{
	var $normal = false;
	var $smtp = false;

	var $sunucu;
	var $port = 587;  // Türk Telekom 25. portu engellediði için 587 kullanýyoruz.
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



	// gönderilecek adres
	function GonderilenAdres($adres)
	{
		$this->kime = $adres;
	}

	// gönderene bir kopya yolla
	function DigerAdres($adres)
	{
		$this->kopya = $adres;
	}

	// yanýtlama adresi
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




	//  E-POSTA BAÞLIK BÝLGÝLERÝ    //

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




	//  SMTP E-POSTA YÖNTEMÝ    //

	function smtp_mail($kime,$konu,$message,$headers)
	{
		// SMTP sunucuya baðlanýlýyor
		$baglan = @fsockopen($this->sunucu, $this->port, $errno, $errstr, 1);

		if (!$baglan)
		{
			$this->hata_bilgi = "Hatalý Adres - SMTP sunucuya baðlanýlamadý !<br>SMTP sunucu adresini kontrol edin.<p>".$errno.'<p>'.$errstr;
			return false;
		}


		// baðlantý sonucu
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "220")
		{
			$this->hata_bilgi = "SMTP sunucuya baðlanýlamadý !<br>SMTP sunucu adresini kontrol edin.<p>".$satir;
			return false;
		}


		// EHLO (selamlamaya) karþýlýk bekleniyor
		fputs($baglan, "EHLO ".$this->sunucu."\r\n");
		$satir = fgets($baglan,256);

		// EHLO cevap vermezse HELO giriliyor
		if (substr($satir,0,3) != "250")
		{
			// HELO (selamlamaya) karþýlýk bekleniyor
			fputs($baglan, "HELO ".$this->sunucu."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "250")
			{
				$this->hata_bilgi = "SMTP sunucu selamlamaya karþýlýk vermiyor !<p>".$satir;
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



		// kimlik doðrulamasý kullan iþaretli ise
		if ($this->smtp_dogrulama == true)
		{
			// kimlik doðrulamasý
			fputs($baglan, "auth login\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "334")
			{
				$this->hata_bilgi = "SMTP kimlik doðrulamasý baþarýsýz !<p>".$satir;
				return false;
			}


			// base64 kullanýcý adý giriliyor
			fputs($baglan, base64_encode($this->kullanici_adi)."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "334")
			{
				$this->hata_bilgi = "Kimlik doðrulanamadý !<br>SMTP kullanýcý adýnýzý kontrol edin.<p>".$satir;
				return false;
			}


			// base64 þifre giriliyor
			fputs($baglan, base64_encode($this->sifre)."\r\n");
			$satir = fgets($baglan,256);

			if (substr($satir,0,3) != "235")
			{
				$this->hata_bilgi = "Kimlik doðrulanamadý !<br>SMTP kullanýcý adý ve þifresinizi kontrol edin.<p>".$satir;
				return false;
			}
		}



		// E-Posta gönderen
		fputs($baglan, "MAIL FROM: <".$this->gonderen.">\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			if ($this->smtp_dogrulama == true) $this->hata_bilgi = "Gönderen adresi baþarýsýz !<p> SMTP sunucu sadece giriþ yapýlan hesabýn adresi ile E-Posta yollamaya izin veriyor olabilir.<p>".$satir;

			else $this->hata_bilgi = "Gönderen adresi baþarýsýz !<p> SMTP sunucu sadece giriþ yapýlan hesabýn adresi ile E-Posta yollamaya izin veriyor olabilir;<br> Veya SMTP sunucu kimlik doðrulamasý gerektiriyor olabilir !<p>".$satir;

			return false;
		}


		// E-Posta gönderilen
		fputs($baglan, "RCPT TO: <$kime>\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			$this->hata_bilgi = "Gönderilen adresi baþarýsýz !<p>".$satir;
			return false;
		}


		// DATA (veri) onayý al
		fputs($baglan, "DATA\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "354")
		{
			$this->hata_bilgi = "DATA (veri) onayý alýnamadý !<p>".$satir;
			return false;
		}


		// gönderilen, gönderen, e-posta konusu, baþlýk ve e-posta içeriði
		fputs($baglan, "To: $kime\r\nFrom: ".$this->gonderen."\r\nSubject: $konu\r\n$headers\r\n\r\n$message\r\n.\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "250")
		{
			$this->hata_bilgi = "E-Posta içeriði gönderilemedi !<p>".$satir;
			return false;
		}


		// çýkýþ yapýlýyor
		fputs($baglan,"QUIT\r\n");
		$satir = fgets($baglan,256);

		if (substr($satir,0,3) != "221")
		{
			$this->hata_bilgi = "SMTP çýkýþý yapýlamadý !<p>".$satir;
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
				$this->hata_bilgi = 'Örnek mail() fonksiyonu oluþturulamadý !';
				return false;
			}


			// gönderene bir kopya yolla
			if ($this->kopya != '')
			{
				$this->Baslik_Olustur();

				$yollandi = @mail($this->kopya, $this->konu, $this->icerik, $this->baslik);

				if(!$yollandi)
				{
					$this->hata_bilgi = 'Örnek mail() fonksiyonu oluþturulamadý !';
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


			// gönderene bir kopya yolla
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
			$this->hata_bilgi = 'Yanlýþ E-Posta Yöntemi !<p>Yönetim Masasý - Genel Ayarlar sayfasýndaki <br>"E-Posta göndermede kullanýlacak yöntem" alanýndan bir seçim yapýn.';
			return false;
		}
	}
}

?>