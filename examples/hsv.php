<?php

include('../classes/Graphic.php');

$graphic=new Graphic(512,512);
$graphic->fill(0,0,$graphic->colorRgb(128));

for($s=256;$s>=0;$s-=16)
    for($a=0;$a<360;$a+=5)
        $graphic->arc(256,256,$s,null,$a,$a+5,$graphic->colorHsv($a,$s,256),true);

$graphic->outputPng();