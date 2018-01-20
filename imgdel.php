<?php
//填充颜色
$json1 ='{
  "parts_img": "upload/2018/01/12/iphone.png",
  "phone_length": "480",
  "phone_width": "250",
  "parts_left": "-10",
  "parts_top": "-3",    
  "comera_left": "-10",
  "comera_top": "-3",
  "comera_img":"upload/2018/01/12/iphone_comera.png",
  "radius":"20"
}';

$json2 ='[{
    "type": "image",
    "z-index": 0,
    "value": "upload/image/logo.png",
    "left": "0",
    "top": -1,
    "zoom": 0.1,
    "angle":0
  },

  {
    "type": "image",
    "z-index": 6,
    "value": "upload/image/img1.png",
    "left": 0,
    "top": 0,
    "zoom": 0.1,
    "angle":0
  },
    {
    "type": "image",
    "z-index": 5,
    "value": "upload/image/img111.jpeg",
    "left": -100,
    "top": 0,
    "zoom": 0.4,
    "angle":0
  },

  {
    "type": "text",
    "z-index": 10,
    "value": "我是谁",
    "font":"upload/font/msyhbd.ttf",
    "font-size": 15,
    "font-color": "#a52a2a",
    "left": "0",
    "top": 0,
    "angle":0
  },

  {
    "type": "text",
    "z-index": 4,
    "value": "我是谁",
    "font":"upload/font/msyhbd.ttf",
    "font-size": 15,
    "font-color": "#a52a2a",
    "left": 0,
    "top": 0,
    "angle":0
  }
]';
//文字不用zoom

$data1 = json_decode($json1,true);//获取底图长宽等属性

$data2 = json_decode($json2,true);//获取需要渲染的数据

//对data2的数据按图层进行排序
//array_column获取二元数组中某个元素变成一维数组
//array_multisort 排序
array_multisort(array_column($data2,'z-index'),SORT_ASC,$data2);


//生成底图
$im = imagecreatetruecolor($data1['phone_width'],$data1['phone_length']);
$color = imagecolorallocate($im,255,255,255);

//用来变透明的颜色
$other_color = imagecolorallocate($im,255,250,240);
imagefill($im,0,0,$color);//填充颜色

//增加图片
foreach($data2 as $d){

    if($d['type']=='image'){        
        //先旋转获取宽高
        
        $img = createimgbytype($d['value']);
        $img = imagerotate($img,$d['angle'],$other_color);       

        list($dep_width,$dep_height)=getimagesize($d['value']);//原来的长宽
        $width=imagesx($img);
        $height=imagesy($img);

        $new_width = $width*$d['zoom'];//新宽
        $new_height = $height*$d['zoom'];//新高
        $new_img = imagecreatetruecolor($new_width,$new_height);
        imagecopyresized($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //设置背景色为透明色
        imagecolortransparent($new_img,$other_color);

        //处理图片(有1像素的差别所以需要减一)        
        imagecopymerge($im,$new_img, $d['left']-(($new_width-$dep_width*$d['zoom'])/2), $d['top']-(($new_height-$dep_height*$d['zoom'])/2),0, 0, $new_width, $new_height, 100);//最后一个参数是模糊度0-100
        
        
    }else{
        //处理文字
        $rgb_color = changetorgb($d['font-color']);
        $font_color = imagecolorallocate($im,$rgb_color[0],$rgb_color[1],$rgb_color[2]);
        imagettftext($im,$d['font-size'],$d['angle'],$d['left'],$d['font-size']+$d['top'],$font_color,$d['font'],$d['value']);


    }


}



    //增加圆角
    $radius  = $data1['radius'];  
    // lt(左上角)  
    $lt_corner  = get_lt_rounder_corner($radius);  
    imagecopymerge($im, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);  
    // lb(左下角)  
    $lb_corner  = imagerotate($lt_corner, 90, 0);  
    imagecopymerge($im, $lb_corner, 0, $data1['phone_length'] - $radius, 0, 0, $radius, $radius, 100);  
    // rb(右上角)  
    $rb_corner  = imagerotate($lt_corner, 180, 0);  
    imagecopymerge($im, $rb_corner, $data1['phone_width'] - $radius, $data1['phone_length'] - $radius, 0, 0, $radius, $radius, 100);  
    // rt(右下角)  
    $rt_corner  = imagerotate($lt_corner, 270, 0);  
    imagecopymerge($im, $rt_corner, $data1['phone_width'] - $radius, 0, 0, 0, $radius, $radius, 100);  

    header("Content-type:image/png");
    imagepng($im);
    imagedestroy($im);



//根据类型生成图片
function createimgbytype($img){
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



//把16进制的颜色转换成rgb数组
function changetorgb($hex)
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



function get_lt_rounder_corner($radius) {  
        $img     = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像  
        $bgcolor    = imagecolorallocate($img, 255, 255, 255);   // 圆角色
        $fgcolor    = imagecolorallocate($img, 0, 0, 0);  //透明色
        imagefill($img, 0, 0, $bgcolor);  
        
        imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);  
        // 将弧角图片的颜色设置为透明  
        imagecolortransparent($img, $fgcolor);                  

        return $img;  
    }  