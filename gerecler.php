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


define('DOSYA_GERECLER',true);


function NumaraBicim($numara)
{
    $donen = @number_format($numara,0,'','.');
    return $donen;
}



function SatirAtla($metin)
{
    $donen = '';
    $bas = 0;

    while ( $secilen = substr($metin, $bas, 90) )
    {
        if ( (!@preg_match('/ /', $secilen)) AND (!@preg_match('/http/i', $secilen)) AND (!@preg_match('(^|[\n])', $secilen)) AND (!@preg_match('/-/', $secilen)) )
            $donen .= $secilen.'<wbr>';

        else $donen .= $secilen;
        $bas += 90;
    }
    return $donen;
}



function zkTemizle($metin)
{
    $donen = @urldecode($metin);
    $donen = @mysql_real_escape_string($donen);

    $bul = array('>', '<');
    $cevir = array('&gt;', '&lt;');
    $donen = @str_replace($bul, $cevir, $donen);

    return $donen;
}



function zkTemizle2($metin)
{
    $donen = @mysql_real_escape_string($metin);

    $bul = array('&', '>', '<', '{', '}');
    $cevir = array('&amp;', '&gt;', '&lt;', '&#123;', '&#125;');
    $donen = @str_replace($bul, $cevir, $donen);

    return $donen;
}


//  �nizleme temizleme i�in //

function zkTemizle3($metin)
{
    $bul = array('&', '>', '<', '{', '}', '\\');
    $cevir = array('&amp;', '&gt;', '&lt;', '&#123;', '&#125;', '&#92;');
    $donen = @str_replace($bul, $cevir, $metin);

    return $donen;
}


//  �ift t�rnak temizleme   //

function zkTemizle4($metin)
{
    $bul = array('"');
    $cevir = array('');
    $donen = @str_replace($bul, $cevir, $metin);

    return $donen;
}


//  t�m iletiler i�in   //

function ileti_yolla($metin, $tip)
{
    if ($tip == 1) $donen = @str_replace('"', '&#34;', @zkTemizle2($metin));
    elseif ($tip == 2) $donen = @zkTemizle2($metin);
    elseif ($tip == 3) $donen = @str_replace('"', '&#34;', @zkTemizle3($metin));
    else $donen = @zkTemizle3($metin);

    return $donen;
}



function zonedate($tarih_bicimi, $saat_dilimi, $sunucu_zamani, $zaman)
{
    if ($sunucu_zamani)
    {
        $yaz_saati = date('I');

        if ($yaz_saati == 1) $bolge = 3600;
        else $bolge = 0;
    }

    else
    {
        $yaz_saati = date('I');

        if ($saat_dilimi >> 0)
        {
            if ($yaz_saati == 1) $bolge = ($saat_dilimi + 1) * 3600;

            else $bolge = $saat_dilimi * 3600;
        }

        else
        {
            if ($yaz_saati == 1) $bolge = 3600;

            else $bolge = 0;
        }
    }

    $ozaman = gmdate('d.m.Y', $zaman + $bolge);
    $simdi = gmdate('d.m.Y', time() + $bolge);
    $dun = gmdate('d.m.Y', (time() + $bolge - 86400));

    if ($ozaman == $simdi)
        $tarih = '<b>Bug�n,</b> '.gmdate('H:i', $zaman + $bolge);

    elseif ($ozaman == $dun)
        $tarih = '<b>D�n,</b> '.gmdate('H:i', $zaman + $bolge);

    else
        $tarih = gmdate($tarih_bicimi, $zaman + $bolge);
    return $tarih;
}



function zonedate2($tarih_bicimi, $saat_dilimi, $sunucu_zamani, $zaman)
{
    if ($sunucu_zamani)
    {
        $yaz_saati = date('I');

        if ($yaz_saati == 1) $bolge = 3600;
        else $bolge = 0;
    }

    else
    {
        $yaz_saati = date('I');

        if ($saat_dilimi >> 0)
        {
            if ($yaz_saati == 1) $bolge = ($saat_dilimi + 1) * 3600;

            else $bolge = $saat_dilimi * 3600;
        }

        else
        {
            if ($yaz_saati == 1) $bolge = 3600;

            else $bolge = 0;
        }
    }

    $tarih = gmdate($tarih_bicimi, $zaman + $bolge);
    return $tarih;
}



function duzenli_ifadeler($metin)
{
    $donen = str_replace('  ',' &nbsp; ',$metin);

    $donen = preg_replace('#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '\\1<a href="\\2" target="_blank">\\2</a>', $donen);

    $donen = preg_replace('#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '\\1<a href="http://\\2" target="_blank">\\2</a>', $donen);

    $donen = preg_replace('#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i', '\\1<a href="mailto:\\2@\\3">\\2@\\3</a>', $donen);

    return $donen;
}



function imza_denetim($metin)
{
    return $metin;
}



function bbcode_kapali($metin)
{
    $donen = str_replace("\n", '<br>', duzenli_ifadeler(SatirAtla($metin)));
    return $donen;
}



function bbcode_acik($metin, $kodno)
{
    global $ayarlar;
    $donen = preg_replace('|\[list=([a-z0-9]*?)\](.*?)\[/list\]|si','<ul type="\\1">\\2</ul>',$metin);

    $bul = array('[list]', '[*]', '[/list]', '[LIST]', '[/LIST]',
                '[b]', '[/b]', '[B]', '[/B]',
                '[u]', '[/u]', '[U]', '[/U]',
                '[i]', '[/i]', '[I]', '[/I]',
                '[s]', '[/s]', '[S]', '[/S]',
                '[center]', '[/center]', '[CENTER]', '[/CENTER]',
                '[left]', '[/left]', '[LEFT]', '[/LEFT]',
                '[right]', '[/right]', '[RIGHT]', '[/RIGHT]');

    $cevir = array('<ul>', '<li>', '</ul>', '<ul>', '</ul>',
                '<b>', '</b>', '<b>', '</b>',
                '<u>', '</u>', '<u>', '</u>',
                '<i>', '</i>', '<i>', '</i>',
                '<s>', '</s>', '<s>', '</s>',
                '<div align="center">', '</div>', '<div align="center">', '</div>',
                '<div align="left">', '</div>', '<div align="left">', '</div>',
                '<div align="right">', '</div>', '<div align="right">', '</div>');

    $donen = str_replace($bul, $cevir, $donen);


    if ($ayarlar['boyutlandirma'] == '1') $donen = preg_replace('|\[img\]([a-z0-9?&\\/\-_+.:,=#@;]+?)\[/img\]|si','<img src="\\1" alt="Resim Ekleme" onload="ResimBoyutlandir(this)" onclick="javascript:ResimBuyut(this)">',$donen);
    else $donen = preg_replace('|\[img\]([a-z0-9?&\\/\-_+.:,=#@;]+?)\[/img\]|si','<img src="\\1" alt="Resim Ekleme">',$donen);

    $donen = preg_replace('|\[color=([a-z0-9#]*?)\](.*?)\[/color\]|si','<font color="\\1">\\2</font>',$donen);
    $donen = preg_replace('|\[size=([0-9]*?)\](.*?)\[/size\]|si','<font size="\\1">\\2</font>',$donen);
    $donen = preg_replace('|\[font=([a-z0-9-_ ]*?)\](.*?)\[/font\]|si','<font face="\\1">\\2</font>',$donen);
    $donen = preg_replace('|\[url=([a-z0-9?&\\/\-_+.:,=#@;]+?)\](.*?)\[/url\]|si','<a href="\\1" target="_blank">\\2</a>',$donen);
    $donen = preg_replace('|\[mail=([a-z0-9?&\\/\-_+.:,=#@;]+?)\](.*?)\[/mail\]|si','<a href="mailto:\\1">\\2</a>',$donen);
    $donen = preg_replace('|\[youtube\]http://www.youtube.com/watch\?v=([a-z0-9?&\\/\-_+.:,=#@;]+?)\[/youtube\]|si','<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/\\1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/\\1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>',$donen);


    $kod_bul = preg_match_all('|\[code=(.*?)\](.*?)\[/code\]|si', $donen, $uyusanlar, PREG_SET_ORDER);

    if (isset($kod_bul))
    {
        $parcala = preg_split('|\[code=(.*?)\](.*?)\[/code\]|si', $donen, -1, PREG_SPLIT_OFFSET_CAPTURE);

        $dongu = 0;
        $donen2 = '';

        foreach ($parcala as $yazi)
        {
            $depo = str_replace("\n", '<br>', SatirAtla(duzenli_ifadeler($yazi[0])));

            $bul = array('[quote="', '[/quote]', '[QUOTE="', '"]', '[/QUOTE]');

            $cevir = array('<div class="alinti_baslik"><b style="position:relative; top:5px">&nbsp;Al�nt� �izelgesi:</b>&nbsp;<u style="position:relative; top:5px">', '</div><br>', '<div class="alinti_baslik"><b style="position:relative; top:5px">&nbsp;Al�nt� �izelgesi:</b>&nbsp;<u style="position:relative; top:5px">', ' yazm��</u></div><div class="alinti_icerik">', '</div><br>');

            $donen2 .= str_replace($bul, $cevir, $depo);



            if (isset($uyusanlar[$dongu][1]))
            {
                $bul2 = array('&amp;', '&gt;', '&lt;', '&#123;', '&#125;', '&#92;');
                $cevir2 = array('&', '>', '<', '{', '}', '\\');
                $renklendi = @str_replace($bul2, $cevir2, $uyusanlar[$dongu][2]);

                $renklendi = highlight_string(('mwdvstqkhsnl_<?php '.$renklendi),true);
                $renklendi = str_replace('mwdvstqkhsnl_<span style="color: #0000BB">&lt;?php&nbsp;','<span style="color: #0000BB">',$renklendi);
                $renklendi = str_replace('mwdvstqkhsnl_<font color="#0000BB">&lt;?php&nbsp;','<span style="color: #0000BB">',$renklendi);

                $donen2 .= '<br><div class="kod_baslik">
                <span style="position:relative; top: 5px; width: 300px; float: left"><b>&nbsp;Kod �izelgesi</b> &nbsp; &nbsp; <u>Kod Dili:</u> '.$uyusanlar[$dongu][1].'</span><span style="position:relative; top: 5px; width: 73px; float: right"><a href="javascript:void(0);" onclick="javascript:hepsiniSec(\'kod_sec_'.$kodno.$dongu.'\');return false;">Hepsini Se�</a></span></div><div class="kod_icerik" id="kod_sec_'.$kodno.$dongu.'">'.$renklendi.'</div><br>';
            }
            $dongu++;
        }
    }
    return $donen2;
}




//  �FADELER TANIMLANIYOR - BA�I   //
//  Farkl� ifadeler eklemek i�in �u konuya bak�n:
//  http://www.phpkf.com/k1877-ifade-ekleme-ve-siralanisini-degistirme.html

if ($ayarlar['f_dizin'] == '/') $ifadeler_dizin = '/dosyalar/ifadeler/';
else $ifadeler_dizin = $ayarlar['f_dizin'].'/dosyalar/ifadeler/';


$ifaadeler_dizi1 = array(
':)', ':d', ';)', ':(', ':g', ':p', '(h)', ':-a', ':i', ':-s', ':|', '*-)', '|-)', '-o)', ':-b', ':-o', ':s'
);

$ifaadeler_dizi2 = array(
'happy.gif',
'bigsmile.gif',
'wink.gif',
'sad.gif',
'grin48.jpg',
'tongue.gif',
'buff.gif',
'msnmix.gif',
'naughty.gif',
'bar.gif',
'msnmix2.gif',
'unimpressed1.gif',
'sleepy.gif',
'uga.gif',
'shy01.gif',
'confused.gif',
'cnf.gif'
);

//  �FADELER TANIMLANIYOR - SONU   //




function ifadeler($metin)
{
    global $ayarlar;
    global $ifadeler_dizin;
    global $ifaadeler_dizi1;
    global $ifaadeler_dizi2;
    $dongu = 0;

    foreach ($ifaadeler_dizi2 as $tek)
    {
        $cevir[] = '<img src="'.$ifadeler_dizin.$tek.'" title="'.$ifaadeler_dizi1[$dongu].'" alt="'.$ifaadeler_dizi1[$dongu].'">';
        $dongu++;
    }

    $donen = str_replace($ifaadeler_dizi1, $cevir, $metin);
    return $donen;
}



function ifade_olustur($adet)
{
    global $ayarlar;
    global $ifadeler_dizin;
    global $ifaadeler_dizi1;
    global $ifaadeler_dizi2;

    $dongu = 0;
    $olustur = '';

    foreach ($ifaadeler_dizi2 as $tek)
    {
        $olustur .= '<img src="'.$ifadeler_dizin.$tek.'" title="'.$ifaadeler_dizi1[$dongu].'" alt="'.$ifaadeler_dizi1[$dongu].'" id="ifade'.($dongu+1).'" border="0" class="ifade" onmouseover="olay_fare_ustune2(this)" onmouseout="olay_fare_cekme2(this)"  onclick="islem_ifade(\''.$ifadeler_dizin.$tek.'\', \''.$ifaadeler_dizi1[$dongu].'\'), olay_fare_tikb(this), setTimeout(\'olay_fare_tikb2(\\\'ifade'.($dongu+1).'\\\')\', 100)">&nbsp;'."\r\n";

        $dongu++;

        if ( ($dongu % $adet) == 0 ) $olustur .= '<br>';
    }

    return $olustur;
}

?>