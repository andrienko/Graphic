<?php
include('../classes/Graphic.php');

$pattern=new graphic(4,4);

$pattern->line(1,0,1,1,$pattern->colorRgb(255));
$pattern->line(0,2,1,2);
$pattern->line(2,1,3,1);
$pattern->line(2,2,2,3);

$pattern->offset(2);
$pattern->tile(10);
$pattern->resize($pattern->getWidth()*10);

$pattern->outputPng();