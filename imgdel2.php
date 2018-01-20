<?php 
    // $text = '呵呵呵呵';     $text = iconv("gbk","utf-8",$text);//转码，避免乱码     
// $block = imagecreatetruecolor(170,170);//建立一个画板    
//  $bg = imagecolorallocatealpha($block , 0 , 0 , 0 , 127);//拾取一个完全透明的颜色，不要用imagecolorallocate拾色     
//  $color = imagecolorallocate($block,255,0,0); //字体拾色     
//  imagealphablending($block , false);//关闭混合模式，以便透明颜色能覆盖原画板    
//   imagefill($block , 0 , 0 , $bg);//填充    
//    imagefttext($block,12,0,10,20,$color,'upload/font/msyhbd.ttf',$text);     
//    imagesavealpha($block , true);//设置保存PNG时保留透明通道信息     
     



$im = imagecreatetruecolor(250,500);
$color = imagecolorallocate($im,255,100,155);
imagefill($im,0,0,$color);//填充颜色


$srcWidth=80;
$srcHeight=80;
$srcImg  = imagecreatefrompng("upload/image/logo.png");
$acolor = imagecolorallocatealpha($im,255,100,255,127);
$srcImg = imagerotate($srcImg,30,$acolor);  

 // imagecolortransparent($im,$acolor);


imagecopyresampled($im,$srcImg,10,10,0,0,$srcWidth,$srcHeight,$srcWidth,$srcHeight);

      header("content-type:image/png"); 
     imagepng($im);//生成图片     

