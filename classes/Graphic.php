<?php

define('EFFECT_GAUSSIAN_BLUR',2);
define('EFFECT_EMBOSS',4);
define('EFFECT_NEGATIVE',8);
define('EFFECT_GRAY',16);
define('EFFECT_SMOOTH',32);
define('EFFECT_EDGES',64);
define('EFFECT_SCANLINE',128);
define('EFFECT_RIPPLE',256);

/**
 * Lame graphic class
 *  A part of gearbox framework.
 *  used mainly for captcha generation and thumbnail creation
 */

class Graphic{

    public $image;

    private $width;
    private $height;

    private $color;
    private $text_y=1;
    //private $text_x=0;//TODO:Make symbol-based ttfs

    // -- Object manipulation and data
    function __construct($width=100,$height=100,$file=null){
        if(!extension_loaded('gd'))throw new \Exception('Gd is not available');
        if(isset($file)){
            $this->image=$this->imageFromFile($file);
            $this->updateSize();

            if(isset($width) | isset($height))$this->resize($width,$height);

        }
        else{
            $this->image=imagecreatetruecolor($width,$height);
            $this->width=$width;
            $this->height=$height;
        }

        $this->color=$this->colorRgb(0);
    }

    function __destruct(){
        imagedestroy($this->image);
    }

    function getHeight(){
        return $this->height;
    }

    function getWidth(){
        return $this->width;
    }

    private function updateSize(){
        $this->width=imagesx($this->image);
        $this->height=imagesy($this->image);
    }

    // -- Service (private) functions
    private function imageFromFile($filename){
        if(file_exists($filename)){
            $info=getimagesize($filename);
            switch ($info[2]){
                case IMAGETYPE_GIF:return imagecreatefromgif($filename);
                case IMAGETYPE_JPEG:return imagecreatefromjpeg($filename);
                case IMAGETYPE_PNG:return imagecreatefrompng($filename);
                case IMAGETYPE_BMP:return imagecreatefrombmp($filename);
                default:throw new \Exception("File $filename is not a supported image file");
            }
        }
        else throw new \Exception("File $filename is not accessible or does not exist");
    }

    private function resample($image,$width,$height){
        $temp=imagecreatetruecolor($width,$height);
        imagecopyresampled($temp,$image,0,0,0,0,$width,$height,imagesx($image),imagesy($image));
        return $temp;
    }

    private function cut($image,$x1,$y1,$x2,$y2){
        if($x1>$x2)list($x1,$x2)=array($x2,$x1);
        if($y1>$y2)list($y1,$y2)=array($y2,$y1);
        $temp=imagecreatetruecolor($x2-$x1,$y2-$y1);
        imagecopy($temp,$image,0,0,$x1,$y1,$x2-$x1,$y2-$y1);
        return $temp;
    }

    private function rotate($image,$angle){
        return imagerotate($image,$angle,$this->colorRgb(0,0,0,127),0);
    }

    private function repeat($image,$x,$y){
        $sw=imagesx($image);$sh=imagesy($image);
        $temp=imagecreatetruecolor($x*$sw,$y*$sh);
        imagesettile($temp,$image);
        imagefill($temp,0,0,IMG_COLOR_TILED);
        return $temp;
    }

    private function shift($image,$x,$y){

        $sw=imagesx($image);$sh=imagesy($image);
        $temp=imagecreatetruecolor($sw,$sh);

        // TODO: Make this less lame
        imagecopy($temp,$image,$x,$y,0,0,$sw,$sh);
        imagecopy($temp,$image,$x-$sw,$y,0,0,$sw,$sh);
        imagecopy($temp,$image,$x,$y-$sh,0,0,$sw,$sh);
        imagecopy($temp,$image,$x-$sw,$y-$sh,0,0,$sw,$sh);

        return $temp;

    }

    // -- Colors section
    function colorRgb($r,$g=null,$b=null,$alpha=null){
        if(!isset($g) | !isset($b)){
            $g=$r;
            $b=$r;
        }
        if(isset($alpha))$this->color=imagecolorallocatealpha($this->image,$r,$g,$b,$alpha);
        else $this->color=imagecolorallocate($this->image,$r,$g,$b);
        return $this->color;
    }

    function colorHsv($h,$s=255,$v=255,$alpha=null){
        list($r,$g,$b)=graphic::hsv2rgb($h,$s,$v);
        return $this->colorRgb($r,$g,$b,$alpha);
    }

    function colorHex($hex,$alpha=null){
        $hex=preg_replace("/[^0-9a-fA-F]/", '', $hex);
        if(strlen($hex)>=6)list($r,$g,$b)=array(hexdec(substr($hex,0,2)),hexdec(substr($hex,2,2)),hexdec(substr($hex,4,2)));
        else if(strlen($hex)==3)list($r,$g,$b)=array(hexdec(substr($hex,0,1)),hexdec(substr($hex,1,1)),hexdec(substr($hex,2,1)));
        else list($r,$g,$b)=array(0,0,0);
        return $this->colorRgb($r,$g,$b,$alpha);

    }

    function colorPattern($filename){//TODO:Fix this crap

        imagesettile($this->image,$this->imageFromFile($filename));
        $this->color=IMG_COLOR_TILED;
        return IMG_COLOR_TILED;
    }

    // -- Calculations section
    static function pointRotate($axis_x,$axis_y,$radius,$angle){
        return array($axis_x+$radius*cos(deg2rad($angle)),$axis_y+$radius*sin(deg2rad($angle)));
    }

    static function hsv2rgb($h, $s=255, $v=255){
        while($h<0)$h+=360;while($h>=360)$h-=360;if($v>255)$v=255;if($v<0)$v=0;
        $s /= 255.0;
        if($s>1)$s=1;
        if ($s == 0.0) return array($v,$v,$v);
        $h /= (360.0 / 6.0);
        $i = floor($h);
        $f = $h - $i;
        $p = (integer)($v * (1.0 - $s));
        $q = (integer)($v * (1.0 - $s * $f));
        $t = (integer)($v * (1.0 - $s * (1.0 - $f)));
        switch($i) {
            case 0: return array($v,$t,$p);
            case 1: return array($q,$v,$p);
            case 2: return array($p,$v,$t);
            case 3: return array($p,$q,$v);
            case 4: return array($t,$p,$v);
            default: return array($v,$p,$q);
        }
    }

    // -- Function wrappers section

    function ellipse($x,$y,$radius,$radius2=null,$color=null,$filled=false){
        if($filled===true)imagefilledellipse($this->image,$x,$y,$radius*2,isset($radius2)?$radius2*2:$radius*2,isset($color)?$color:$this->color);
        else imageellipse($this->image,$x,$y,$radius*2,isset($radius2)?$radius2*2:$radius*2,isset($color)?$color:$this->color);

    }

    function fill($x,$y,$color=null){
        imagefill($this->image,$x,$y,isset($color)?$color:$this->color);
    }

    function line($x1,$y1=null,$x2=null,$y2=null,$color=null){
        if(is_array($x1)){
            $points=floor(count($x1)/2.0);
            if($points<1)return;
            else if($points==1)$this->setPixel($x1[0],$x1[1],isset($color)?$color:$this->color);
            else if($points==2)$this->line($x1[0],$x1[1],$x1[2],$x1[3],isset($color)?$color:$this->color);
            else{
                for($i=0;$i<$points-1;$i++){
                    $this->line($x1[$i*2],$x1[$i*2+1],$x1[($i+1)*2],$x1[($i+1)*2+1],isset($color)?$color:$this->color);
                }
            }
        }
        else{
            if(isset($y1) & isset($x2) & isset($y2))imageline($this->image,$x1,$y1,$x2,$y2,isset($color)?$color:$this->color);
            else return;
        }

    }

    function getPixelColor($x,$y,$index=null){
        $val=imagecolorsforindex($this->image,imagecolorat($this->image,$x,$y));
        if(isset($index))return $val[$index];
        else return $val;
    }

    function getPixel($x,$y){
        $this->color=imagecolorat($this->image,$x,$y);
        return $this->color;
    }

    function setPixel($x,$y,$color=null){
        imagesetpixel($this->image,$x,$y,isset($color)?$color:$this->color);
    }

    function polygon($points,$color=null,$pointlimit=null,$filled=true){
        if($filled===true)imagefilledpolygon($this->image,$points,isset($pointlimit)?$pointlimit:count($points)/2,isset($color)?$color:$this->color);
        else imagepolygon($this->image,$points,isset($pointlimit)?$pointlimit:count($points)/2,isset($color)?$color:$this->color);

    }

    function rectangle($x1,$y1,$x2,$y2=null,$color=null,$filled=true){
        if($filled===true)imagefilledrectangle($this->image,$x1,$y1,$x2,isset($y2)?$y2:$y1+$x2-$x1,isset($color)?$color:$this->color);
        else imagerectangle($this->image,$x1,$y1,$x2,isset($y2)?$y2:$y1+$x2-$x1,isset($color)?$color:$this->color);
    }

    function arc($x,$y,$radius,$radius2,$start,$end,$color=null,$filled=true){
        if($filled===false)imagearc($this->image,$x,$y,$radius*2,isset($radius2)?$radius2*2:$radius*2,$start,$end,isset($color)?$color:$this->color);
        else imagefilledarc($this->image,$x,$y,$radius*2,isset($radius2)?$radius2*2:$radius*2,$start,$end,isset($color)?$color:$this->color,$filled===true?IMG_ARC_PIE:$filled);
    }

    function polygonRegular($x,$y,$radius=10,$sides=5,$color=null,$angle=0,$filled=true){
        $points = array();
        for($a = 0;$a <= 360; $a += 360/$sides){
            $points=array_merge($points,graphic::pointRotate($x,$y,$radius,$a+$angle));
        }
        $this->polygon($points,isset($color)?$color:$this->color,null,$filled);
    }

    function string($text,$x=1,$y=null,$fontsize=1,$color=null,$fontfile=null,$angle=0){//TODO: Add unicode
        if(!isset($y)){
            $y=$this->text_y;
            $this->text_y+=8;
        }
        if(isset($fontfile) && file_exists($fontfile)){
            imagettftext($this->image,$fontsize,$angle,$x,$y,isset($color)?$color:$this->color,$fontfile,$text);
        }
        else imagestring($this->image,$fontsize,$x,$y,$text,isset($color)?$color:$this->color);
    }

    function filter($filter,$arg1=null,$arg2=null,$arg3=null,$arg4=null){
        imagefilter($this->image,$filter,$arg1,$arg2,$arg3,$arg4);//TODO: Fix errors on excess parameters
    }

    function copy(graphic $source,$dst_x,$dst_y,$src_x1,$src_y1,$src_x2,$src_y2){
        imagecopy($this->image,$source->image,$dst_x,$dst_y,$src_x1,$src_y1,$src_x2-$src_x1,$src_y2-$src_y1);
    }

    // -- Transformations section

    function effect($effect,$arg1=null,$arg2=null){
        if($effect&EFFECT_EMBOSS)
            imageconvolution($this->image, array(array(2, 0, 0), array(0, -1, 0), array(0, 0, -1)), 1, 127);
        if($effect&EFFECT_GAUSSIAN_BLUR){
            if(isset($arg1))for($i=0;$i<=$arg1;$i++)imagefilter($this->image,IMG_FILTER_GAUSSIAN_BLUR);
            else imagefilter($this->image,IMG_FILTER_GAUSSIAN_BLUR);
        }

        if($effect&EFFECT_NEGATIVE)
            imagefilter($this->image,IMG_FILTER_NEGATE);
        if($effect&EFFECT_GRAY)
            imagefilter($this->image,IMG_FILTER_GRAYSCALE);
        if($effect&EFFECT_SMOOTH)
            imagefilter($this->image,IMG_FILTER_SMOOTH,$arg1);
        if($effect&EFFECT_EDGES){
            imagefilter($this->image,IMG_FILTER_CONTRAST,255);
            imagefilter($this->image,IMG_FILTER_EDGEDETECT);
        }
        if($effect&EFFECT_SCANLINE){

            if(isset($arg1))$c=$arg1;else $c=2;
            if(isset($arg2))$this->color=$arg2;else $this->colorRgb(0);
            for($y=0;$y<=$this->height;$y+=$c){
                $this->line(0,$y,$this->width,$y);
            }
        }
        if($effect&EFFECT_RIPPLE){
            $temp=imagecreatetruecolor($this->width*2,$this->height*2);
            imagecopyresampled($temp,$this->image,0,0,0,0,$this->width*2,$this->height*2,$this->width,$this->height);
            $amplitude=isset($arg1)?$arg1:10;
            $period=isset($arg2)?$arg2:10;
            for($i = 0; $i < $this->width*2; $i += 2)imagecopy($temp, $temp, $i - 2, sin($i / $period) * $amplitude, $i, 0, 2, $this->height*2);
            imagecopyresampled($this->image,$temp,0,0,0,0,$this->width, $this->height, $this->width*2, $this->height*2);
            imagedestroy($temp);
        }
    }


    function resize($width=null,$height=null,$allow_rescale=true){
        if(!isset($width) && !isset($height))throw new \Exception("You must specify width and/or height for resize.");
        if(!isset($height))$height=($width/$this->width)*$this->height;
        else if(!isset($width))$width=($height/$this->height)*$this->width;

        //TODO:Make percentage

        if($allow_rescale===true){
            $this->image=$this->resample($this->image,$width,$height);
            $this->updateSize();
        }
        else{
            $temp=imagecreatetruecolor($width,$height);//TODO:make this more elegant and/or replace with crop
            imagefill($temp,0,0,$this->color);
            imagecopy($temp,$this->image,0,0,0,0,$this->width,$this->height);
            $this->image=$temp;
            $this->updateSize();
        }


    }

    function tile($x,$y=null){
        if(!isset($y))$y=$x;
        $this->image=$this->repeat($this->image,$x,$y);
        $this->updateSize();
    }

    function crop($x1,$y1,$x2,$y2){
        $this->image=$this->cut($this->image,$x1,$y1,$x2,$y2);
        $this->updateSize();
    }

    function offset($x,$y=null){
        if(!isset($y))$y=$x;
        while($x>=$this->width)$x-=$this->width;
        while($x<0)$x+=$this->width;
        while($y>=$this->height)$y-=$this->height;
        while($y<0)$y+=$this->height;

        $this->image=$this->shift($this->image,$x,$y);
    }


    function addImage($filename,$x=0,$y=0,$width=null,$height=null,$angle=null){
        $temp=$this->imageFromFile($filename);
        if(isset($width) && isset($height))$temp=$this->resample($temp,$width,$height);
        if(isset($angle))$temp=$this->rotate($temp,$angle);//TODO:fix rotation
        list($w,$h)=array(imagesx($temp),imagesy($temp));
        imagecopy($this->image,$temp,$x,$y,0,0,$w,$h);
    }

    // -- Output section

    function outputJpg($filename=null,$quality=100){
        if(!isset($filename) | $filename==""){
            header('Content-Type: image/jpeg');
            imagejpeg($this->image,null,$quality);
        }
        else imagejpeg($this->image,$filename,$quality);

    }

    function outputPng($filename=null,$quality=null,$filters=null){
        if(!isset($filename) | $filename==""){
            header ('Content-Type: image/png');
            imagepng($this->image,null,$quality,$filters);
        }
        imagepng($this->image,$filename,$quality,$filters);
    }

    function outputGif($filename=null){
        if(!isset($filename) | $filename==""){
            header('Content-Type: image/gif');
            imagegif($this->image);
        }
        else imagegif($this->image,$filename);

    }

    //TODO: base64 output

}