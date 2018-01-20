<?php

class mobilediy_facade
{

	//把16进制的颜色转换成rgb数组
	public static function changetorgb($hex)
	{
		 $hex = str_replace("#","",$hex);

		  if(strlen($hex)==3){
		      $r = hexdec(substr($hex,0,1).substr($hex,0,1));//十六进制转换成十进制
		      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		  }else{
		      $r = hexdec(substr($hex,0,2));
		      $g = hexdec(substr($hex,2,2));
		      $b = hexdec(substr($hex,4,2));
		  }

		  return array($r,$g,$b);
	}

	//获取一张圆角图(左上)
	public static function get_lt_rounder_corner($radius) {  
        $img     = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像  
        $bgcolor    = imagecolorallocate($img, 255, 255, 255);   // 圆角色
        $fgcolor    = imagecolorallocate($img, 0, 0, 0);  //透明色
        imagefill($img, 0, 0, $bgcolor);  
        
        imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);  
        // 将弧角图片的颜色设置为透明  
        imagecolortransparent($img, $fgcolor);                  

        return $img;  
    }  


    //根据类型生成图片
	public static function createimgbytype($img){
	  //获取后缀名
	  $ext = pathinfo($img,PATHINFO_EXTENSION);
	  //暂时支持png，jpg
	  switch($ext){
	    case "jpeg":
	    case "jpg":$im = imagecreatefromjpeg($img);break;
	    case "png":$im = imagecreatefrompng($img);break;
	    case "gif":$im = imagecreatefromgif($img);break;
	    default:break;
	  }
	  return $im;
	  
	}


}
