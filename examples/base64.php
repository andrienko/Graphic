<?php

include('../classes/Graphic.php');

$graphic=new Graphic(36,36);

for($s=0;$s<$graphic->getWidth();$s++)
    $graphic->line($s,0,$s,$graphic->getHeight(),$graphic->colorHsv($s*10,256,256));

$base64 = $graphic->getBase64Png();

echo('<img src="'.$base64.'"/>');