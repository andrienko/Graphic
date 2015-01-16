<?php
include('../classes/Graphic.php');

$text=new Graphic(256,128);
$text->fill(0,0,$text->colorRgb(128,128,255));

$text->colorRgb(255);

$text->string("This is the first line");
$text->string("When the coodinates are not specified - ");
$text->string("the next line goes right after previous in");
$text->string("some stupid manner.");

$font="../assets/ubuntumonor.ttf";

$text->string("This is the text with ubuntu mono font",10,50,9,null,$font);
$text->string("А это другой текст, с Unicode",10,60,9,null,$font);
$text->string("Sample\nof alpha\nstuff",20,100,16,$text->colorRgb(255,255,255,100),$font,90);


$text->outputPng();


?>