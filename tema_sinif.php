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


define('DOSYA_TEMA_SINIF',true);

class phpkf_tema
{
	var $tema_ham;
	var $tema_cikis;



	//	tema dosyasý açýlýyor	//
	function tema_dosyasi($dosya)
	{
		if (!($dosya_ac = fopen($dosya,'r')))
			die ('<br><p align="center"><font style="background-color: #ffffff; color: #FF0000;"><b>Tema Dosyasý Açýlamýyor '.$dosya.'</b></font></p><br>');

		$boyut = filesize($dosya);
		$dosya_metni = fread($dosya_ac,$boyut);
		fclose($dosya_ac);
		$this->tema_ham = $dosya_metni;
	}



	// içiçe döngü varsa	//
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
				//	dýþ döngü //
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


					//	iç döngü	//
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



	//	tek kademeli döngü varsa	//
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



	//	hiçbir döngü yoksa	//
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



	//	koþul varsa	//
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



	// tema uygulanýyor	//
	function tema_uygula()
	{
		$this->tema_ham = preg_replace('|<!--__KOSUL_([a-z]*?)-([a-z0-9]*?)__-->|si','', $this->tema_ham);
		echo $this->tema_ham;
	}
}
?>