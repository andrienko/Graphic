<?php
include('../classes/Graphic.php');

class gui extends Graphic{

    function rectangleOutlined($x1,$y1,$x2,$y2,$color,$outlineColor){

        $this->rectangle($x1,$y1,$x2,$y2,$color);
        $this->rectangle($x1-1,$y1-1,$x2+1,$y2+1,$outlineColor,false);

    }

    function window($x1,$y1,$x2,$y2,$caption){

        if($y2-$y1<12)$y2=$y1+12;
        if($x2-$x1<strlen($caption)*5+15)$x2=$x1+strlen($caption)*5+15;
        $x1++;$x2--;$y1++;$y2--;

        $this->rectangle($x1+2,$y1+2,$x2+4,$y2+4,$this->colorRgb(0,0,0,64));
        $this->rectangleOutlined($x1,$y1,$x2,$y2,$this->colorRgb(200),$this->colorRgb(0));
        $this->rectangle($x1,$y1,$x2,$y1+10,$this->colorRgb(0,0,128));
        $this->string($caption,$x1+4,$y1+1,1,$this->colorRgb(255));
        $this->button($x2-7,$y1,"X");


    }

    function button($x,$y,$caption){
        $this->rectangleOutlined($x,$y,$x+strlen($caption)*5+2,$y+9,$this->colorRgb(200),$this->colorRgb(0));
        $this->line($x+strlen($caption)*5+2,$y,$x+strlen($caption)*5+2,$y+9,$this->colorRgb(100));
        $this->line($x,$y+9,$x+strlen($caption)*5+2,$y+9);
        $this->line($x,$y,$x+strlen($caption)*5+2,$y,$this->colorRgb(220));
        $this->line($x,$y,$x,$y+9);
        $this->string($caption,$x+2,$y+1,1,$this->colorRgb(0));
    }

    function alert($x,$y,$caption,$text,$buttontext){
        $buttontext='  '.$buttontext.'  ';
        $wc=strlen($caption)*5;
        $wt=strlen($text)*5;
        $wb=strlen($buttontext)*5;
        $ww=$wc+15;
        if($ww<$wt+15)$ww=$wt+15;
        if($ww<$wb+15)$ww=$wb+15;
        $hw=50;
        $this->window($x,$y,$x+$ww,$y+$hw,$caption);
        $this->string($text,$x+($ww-$wt)/2,$y+$hw/3,1,$this->colorRgb(0));
        $this->button($x+($ww-$wb+2)/2,$y+$hw*(2/3),$buttontext);
    }

}

$my=new gui(150,150);
$my->fill(0,0,$my->colorRgb(128,128,255));

$my->alert(1,1,"Fatal error","Stack-heap collision","OK");

$my->alert(7,44,"Critical exception","I/O error occured","Abort");
$my->alert(90,25,"null","---","OK");
$my->alert(10,85,":(","Error","Retry");
$my->alert(43,72,"Error","File not found","Ignore");


//$my->resize($my->getWidth()*4);

$my->outputJpg(null,100);