<?php
include('../classes/Graphic.php');


function shadedText(Graphic $image,$text,$x,$y){

    $font="../assets/sharetech.ttf";

    $image->string($text,$x,$y + 5,24,$image->colorRgb(100),$font);
    $image->effect(EFFECT_GAUSSIAN_BLUR,10);
    $image->string($text,$x,$y + 1,24,$image->colorRgb(230),$font);
    $image->string($text,$x,$y,24,$image->colorRgb(150),$font);
}


$image = new Graphic(512,256);
$image->fill(0,0,$image->colorRgb(240));

shadedText($image,'Kind of hello!',130,135);

$image->outputJpg();