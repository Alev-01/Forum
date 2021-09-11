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


define('DOSYA_TEMA_SINIF',true);

class phpkf_tema
{
	var $tema_ham;
	var $tema_cikis;



	//	tema dosyas� a��l�yor	//
	function tema_dosyasi($dosya)
	{
		if (!($dosya_ac = fopen($dosya,'r')))
			die ('<br><p align="center"><font style="background-color: #ffffff; color: #FF0000;"><b>Tema Dosyas� A��lam�yor '.$dosya.'</b></font></p><br>');

		$boyut = filesize($dosya);
		$dosya_metni = fread($dosya_ac,$boyut);
		fclose($dosya_ac);
		$this->tema_ham = $dosya_metni;
	}



	// i�i�e d�ng� varsa	//
	function icice_dongu($adet, $dizi, $dizi2)
	{
		$isaret1 = '|<!--__DIS_BASLAT-'.$adet.'__-->(.*?)<!--__DIS_BITIR-'.$adet.'__-->|si';
		$isaret2 = '|<!--__IC_BASLAT-'.$adet.'__-->(.*?)<!--__IC_BITIR-'.$adet.'__-->|si';

		$this->tema_cikis = '';

		if (preg_match_all($isaret1, $this->tema_ham, $uyusanlar, PREG_SET_ORDER))
		{
			$parcala = preg_split($isaret1, $this->tema_ham, -1, PREG_SPLIT_OFFSET_CAPTURE);

			if (isset($uyusanlar[0][1]))
			{
				//	d�� d�ng� //
				preg_match_all($isaret2, $uyusanlar[0][1], $uyusanlar2, PREG_SET_ORDER);

				$parcala2 = preg_split($isaret2, $uyusanlar[0][1], -1, PREG_SPLIT_OFFSET_CAPTURE);
					
				$this->tema_cikis .= $parcala[0][0];
				$dongu = 0;

				foreach ($dizi as $deger)
				{
					foreach ($deger as $anahtar => $deger2)
					{
						$ara[] = $anahtar;
						$degis[] = $deger2;
					}

					$depo1 = $parcala2[0][0];

					$depo1 = str_replace($ara,$degis,$depo1);
					unset($ara);
					unset($degis);
					$this->tema_cikis .= $depo1;


					//	i� d�ng�	//
					foreach ($dizi2[$dongu] as $deger3)
					{
						foreach ($deger3 as $anahtar2 => $deger4)
						{
							$ara[] = $anahtar2;
							$degis[] = $deger4;
						}

						$depo2 = $uyusanlar2[0][1];

						$depo2 = str_replace($ara,$degis,$depo2);
						unset($ara);
						unset($degis);

						$this->tema_cikis .= $depo2;
					}
					$dongu++;
					$this->tema_cikis .= $parcala2[1][0];
				}
			}
			$this->tema_cikis .= $parcala[1][0];
			$this->tema_ham = $this->tema_cikis;
		}
	}



	//	tek kademeli d�ng� varsa	//
	function tekli_dongu($adet, $dizi)
	{
		$this->tema_cikis = '';
		$isaret = '|<!--__TEKLI_BASLAT-'.$adet.'__-->(.*?)<!--__TEKLI_BITIR-'.$adet.'__-->|si';

		if (preg_match_all($isaret, $this->tema_ham, $uyusanlar, PREG_SET_ORDER))
		{
			$parcala = preg_split($isaret, $this->tema_ham, -1, PREG_SPLIT_OFFSET_CAPTURE);

			if (isset($uyusanlar[0][1]))
			{
				$this->tema_cikis .= $parcala[0][0];

				foreach ($dizi as $deger)
				{
					$depo = $uyusanlar[0][1];

					foreach ($deger as $anahtar => $deger2)
					{
						$ara[] = $anahtar;
						$degis[] = $deger2;
					}

					$depo = str_replace($ara,$degis,$depo);
					unset($ara);
					unset($degis);
					$this->tema_cikis .= $depo;
				}
			}
			$this->tema_cikis .= $parcala[1][0];
			$this->tema_ham = $this->tema_cikis;
		}
	}



	//	hi�bir d�ng� yoksa	//
	function dongusuz($dizi)
	{
		$depo = $this->tema_ham;

		foreach ($dizi as $anahtar => $deger)
		{
			$ara[] = $anahtar;
			$degis[] = $deger;
		}

		$depo = str_replace($ara,$degis,$depo);
		unset($ara);
		unset($degis);
		$this->tema_ham = $depo;
	}



	//	ko�ul varsa	//
	function kosul($adet, $dizi, $varmi)
	{
		$this->tema_cikis = '';
		$isaret = '|<!--__KOSUL_BASLAT-'.$adet.'__-->(.*?)<!--__KOSUL_BITIR-'.$adet.'__-->|si';

		if ($varmi == true)
		{
			if (preg_match_all($isaret, $this->tema_ham, $uyusanlar, PREG_SET_ORDER))
			{
				$parcala = preg_split($isaret, $this->tema_ham, -1, PREG_SPLIT_OFFSET_CAPTURE);

				if (isset($uyusanlar[0][1]))
				{
					$this->tema_cikis .= $parcala[0][0];

					$depo = $uyusanlar[0][1];

					foreach ($dizi as $anahtar => $deger)
					{
						$ara[] = $anahtar;
						$degis[] = $deger;
					}

					$depo = str_replace($ara,$degis,$depo);
					unset($ara);
					unset($degis);
					$this->tema_cikis .= $depo;
				}
				$this->tema_cikis .= $parcala[1][0];
				$this->tema_ham = $this->tema_cikis;
			}
		}

		else
		{
			if (preg_match_all($isaret, $this->tema_ham, $uyusanlar, PREG_SET_ORDER))
			{
				$parcala = preg_split($isaret, $this->tema_ham, -1, PREG_SPLIT_OFFSET_CAPTURE);
				$this->tema_cikis .= $parcala[0][0];
				$this->tema_cikis .= $parcala[1][0];
				$this->tema_ham = $this->tema_cikis;
			}
		}
	}



	// tema uygulan�yor	//
	function tema_uygula()
	{
		$this->tema_ham = preg_replace('|<!--__KOSUL_([a-z]*?)-([a-z0-9]*?)__-->|si','', $this->tema_ham);
		echo $this->tema_ham;
	}
}
?>