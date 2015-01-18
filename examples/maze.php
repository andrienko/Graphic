<?php
include('../classes/Graphic.php');

function go($x,$y,Graphic $maze,$white,$prev=null){

    $mx=$maze->getWidth();
    $my=$maze->getHeight();

    $maze->setPixel($x,$y,$white);

    $directions=range(0,3);shuffle($directions);
    if(isset($prev) && rand(0,1)==0)array_unshift($directions,$prev);

    foreach($directions as $direction){
        switch($direction){

            case(0):
                if($x+2<$mx && $maze->getPixel($x+2,$y)!=$white){
                    $maze->setPixel($x+1,$y,$white);
                    go($x+2,$y,$maze,$white,0);
                }
                break;
            case(1):
                if($y+2<$my && $maze->getPixel($x,$y+2)!=$white){
                    $maze->setPixel($x,$y+1,$white);
                    go($x,$y+2,$maze,$white,1);
                }
                break;
            case(2):
                if($y-2>0 && $maze->getPixel($x,$y-2)!=$white){
                    $maze->setPixel($x,$y-1,$white);
                    go($x,$y-2,$maze,$white,2);
                }
                break;
            case(3):
                if($x-2>0 && ($maze->getPixel($x-2,$y)!=$white | rand(0,200)==0)){
                    $maze->setPixel($x-1,$y,$white);
                    go($x-2,$y,$maze,$white,3);
                }
                break;
        }
    }
}

$maze=new Graphic(33,33);
$white=$maze->colorRgb(255);
go(3,3,$maze,$white);

$maze->resize(330,330,true);
$maze->outputJpg(null,100);


