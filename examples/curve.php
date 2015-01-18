<?php
include('../classes/Graphic.php');

$image = new Graphic(512,256);
$image->fill(0,0,$image->colorRgb(64,0,32));

$points = array(
    array(0,0),
    array(64,256),
    array(128,0),
    array(192,256),
    array(256,0),
    array(320,256),
    array(385,0)
);
$points = array_map(function($a){return array($a[0]+48,$a[1]-16);},$points);


foreach(range(1,10) as $i){
    $image->curve($points,$image->colorRgb(255,255,255,127-$i*12),false,$i / 2);
    $image->effect(EFFECT_GAUSSIAN_BLUR);
}

//foreach($points as $point)$image->ellipse($point[0],$point[1],3,null,$image->colorRgb(255,0,0),true);




$image->outputPng();

/*
echo('<pre>');
var_dump(Graphic::caculate_cardinal_points(array(
    array(1,1),
    array(50,50),
    array(120,100)
)));*/