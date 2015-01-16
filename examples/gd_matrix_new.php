<?
namespace gearbox;
include('../core/gears.php');

error_reporting(0);

$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 _.,?!():<>=+-*/${}&%;"[]\'~^';
//$alphabet="ABCDEFGH";
//$alphabet=' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRTSUVWXYZ[\\]^_`{|}~';
//$alphabet='АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
//$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ346789';
//$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZБГДЖИЙЛПУФЦЧШЩЪЫЬЭЮЯ0123456789., _:!?*';

$cell_num=64;
$cell_size=16;
$font='fonts/ubuntumonor.ttf';

$total=pow(u::strlen($alphabet),2);
$total_alphabet=u::strlen($alphabet);
$total_square_pages=ceil($total/($cell_num*$cell_num));

$matrix=new graphic($cell_size,$cell_size);
//$matrix->rectangle(0,0,$cell_size,$cell_size,$matrix->colorRgb(0,0,90),false);
$matrix->tile($cell_num,$cell_num * $total_square_pages);

$matrix->colorRgb(255);

$fs=$cell_size*0.75;
$fs=10;

$ox=1;
$oy=-$cell_size/6;


for($n=0;$n<$total;$n++){
    $xn=$n%$cell_num;
    $yn=floor($n/$cell_num);

    $x=$xn * $cell_size + $ox;
    $y=$yn * $cell_size + $oy + $cell_size;



    $xa=$n%$total_alphabet;
    $ya=floor($n/$total_alphabet);
    //echo($n.':'.$xn.'x'.$yn.':'.$xa.'x'.$ya.'; ');

    $matrix->string(u::substr($alphabet,$ya,1).u::substr($alphabet,$xa,1),$x,$y,$fs,null,$font);
}
//echo(u::substr($alphabet,0,1));
//$matrix->string($total_alphabet.' - '.$total,0,$matrix->getHeight(),23,$matrix->colorRgb(255,255,255,50),$font);

$matrix->outputPng();