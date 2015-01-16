<?
namespace gearbox;
include('../core/gears.php');
$cs=32;
$matrix=new graphic($cs,$cs);

//$matrix->rectangle(0,0,$cs,$cs,$matrix->colorRgb(255),false);

$letters="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 _.,?!()-:/<>=+*$&%;\"''";
$lx=0;
$ly=0;
$c=32;
$cy=256;
$matrix->tile($c,$cy);

$matrix->colorRgb(255);

function getNext(){
    global $letters;
    global $lx;
    global $ly;
    global $c;
    global $cy;
    if($ly*strlen($letters)+$ly>strlen($letters)*strlen($letters))$ret="  ";
    else $ret=$letters[$ly].$letters[$lx];

    $lx+=1;
    if($lx>=strlen($letters)){
        $ly+=1;
        $lx=0;
    }
    return $ret;
}

$l=0;
$t=27;
$fs=22;
$f='fonts/sharetech.ttf';
for($y=0;$y<$cy;$y++){
    for($x=0;$x<$c;$x++){
        $matrix->string(getNext(),$cs*$x+$l,$y*$cs+$t,$fs,null,$f);
    }
}



$matrix->outputPng();