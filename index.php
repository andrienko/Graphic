<?
namespace gearbox;
include('../core/gears.php');
$folder=new directory(".");
foreach($folder->get_files("*_*.php") as $file){
    echo("<a href=\"$file\">$file</a><br/>");
}


?>