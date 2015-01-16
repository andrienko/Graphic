<?php
include('../classes/Graphic.php');

$image = new Graphic(512,256);
$image->fill(0,0,$image->colorRgb(64,0,32));

$points = array(
    array(0,0),
    array(50,200),
    array(500,100),
    array(50,70),
    array(512,256)
);

foreach(range(1,10) as $i)
    $image->curve($points,$image->colorRgb(255),false,$i / 2);



$image->outputJpg();

/*
echo('<pre>');
var_dump(Graphic::caculate_cardinal_points(array(
    array(1,1),
    array(50,50),
    array(120,100)
)));*/