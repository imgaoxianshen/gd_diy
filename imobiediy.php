<?php
/**
 * 手机定制相关
 *
 * @author Administrator
 */
class imobilediy extends iapibase
{
	 private $valid= array(
	 	"get_mobile_img"=>array(
	 		array("field"=>"json1","required"=>true),
	 		array("field"=>"json2","required"=>true),
	 	),
	 );


	//获取手机定制展示图(和制作图)
	public function get_mobile_img()
	{
		ini_set('memory_limit','256M');
		$client=array();
                    
        $arrInput=array();
        $params=(self::ExtractCommonParams());
        extract($params); 

           
        self::chkMemberValid($token,$client);

        $msg="";
        if(!$this->Validate($this->valid['get_mobile_img'],$params,$msg)){
            self::XpiResp(-1,$msg);
        }


        $data1 = json_decode($json1,true);//获取底图长宽等属性

		$data2 = json_decode($json2,true);//获取需要渲染的数据

		$rate =6;//倍率


		//对data2的数据按图层进行排序
		//array_column获取二元数组中某个元素变成一维数组
		//array_multisort 排序
		array_multisort(array_column($data2,'z-index'),SORT_ASC,$data2);

		
		//生成底图
		$im = imagecreatetruecolor($data1['phone_width']*$rate,$data1['phone_length']*$rate);
		$color = imagecolorallocate($im,255,255,255);
		imagefill($im,0,0,$color);//填充颜色
		//用来变透明的颜色
		$other_color = imagecolorallocatealpha($im,255,250,240,127);
		

		foreach($data2 as $d){

		    if($d['type']=='image'){        
		        //先旋转获取宽高
		        
		        $img = mobilediy_facade::createimgbytype($d['value']);
		        $img = imagerotate($img,$d['angle'],$other_color);       


		        list($dep_width,$dep_height)=getimagesize($d['value']);//原来的长宽
		        //旋转之后图片的长宽
		        $width=imagesx($img);
		        $height=imagesy($img);

		        $new_width = $width*$d['zoom']*$rate;//新宽
		        $new_height = $height*$d['zoom']*$rate;//新高
		        $new_img = imagecreatetruecolor($new_width,$new_height);
		        //设置透明色（放大之后的图像）
		        imagefill($new_img,0,0,$other_color);

		        imagecopyresized($new_img, $img, 0, 0, 0, 0, $new_width, $new_height , $width , $height);
		        

		        //处理图片
		        imagecopyresampled($im,$new_img, $d['left']*$rate-(($new_width-$dep_width*$d['zoom']*$rate)/2) , $d['top']*$rate-(($new_height-$dep_height*$d['zoom']*$rate)/2),0, 0, $new_width, $new_height, $new_width,$new_height);//最后一个参数是模糊度0-100
		        // echo $d['left']."<br/>";
		        // echo (($new_width-$dep_width*$d['zoom']*$rate)/2);
		        // exit;
		        
		    }else{
		        //处理文字
		        $rgb_color = mobilediy_facade::changetorgb($d['font-color']);
		        $font_color = imagecolorallocate($im,$rgb_color[0],$rgb_color[1],$rgb_color[2]);
		        imagettftext($im,$d['font-size']*$rate,$d['angle'],$d['left']*$rate,$d['font-size']*$rate+$d['top']*$rate,$font_color,$d['font'],$d['value']);
		    }
		}



	    $filename2 = PhotoUpload::hashDir(true)."/".date("YmdHis").mt_rand(100, 999).".png";

	    header("Content-type:image/png");

	    imagepng($im,$filename2);


		//增加圆角
	    $radius  = $data1['radius']*$rate;  
	    // lt(左上角)  
	    $lt_corner  = mobilediy_facade::get_lt_rounder_corner($radius);  
	    imagecopymerge($im, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);  
	    // lb(左下角)  
	    $lb_corner  = imagerotate($lt_corner, 90, 0);  
	    imagecopymerge($im, $lb_corner, 0, $data1['phone_length']*$rate - $radius, 0, 0, $radius, $radius, 100);  
	    // rb(右上角)  
	    $rb_corner  = imagerotate($lt_corner, 180, 0);  
	    imagecopymerge($im, $rb_corner, $data1['phone_width']*$rate  - $radius, $data1['phone_length']*$rate  - $radius, 0, 0, $radius, $radius, 100);  
	    // rt(右下角)  
	    $rt_corner  = imagerotate($lt_corner, 270, 0);  
	    imagecopymerge($im, $rt_corner, $data1['phone_width']*$rate  - $radius, 0, 0, 0, $radius, $radius, 100);  


		
	    //再增加摄像头等东西
	    $camera_img = mobilediy_facade::createimgbytype($data1['comera_img']);
	    $srcWidth = imagesx($camera_img);
	    $srcHeight = imagesy($camera_img);
	    $alpha = imagecolorallocatealpha($camera_img, 0, 0, 0, 127);//设置透明色
		

	    imagecolortransparent($camera_img,$alpha);
	    
	    // imagecopymerge($im,$camera_img,$data1['comera_left'],$data1['comera_top'],0,0,$srcWidth,$srcHeight,100);

		imagecopyresampled($im,$camera_img,$data1['comera_left']*$rate,$data1['comera_top']*$rate,0,0,$srcWidth*$rate,$srcHeight*$rate,$srcWidth,$srcHeight);


	    $filename = PhotoUpload::hashDir(true)."/".date("YmdHis").mt_rand(100, 999).".png";

	    // header("Content-type:image/png");

	    imagepng($im,$filename);
	    imagedestroy($im);
	    $this->XpiRespSuccess($_SERVER['SERVER_NAME']."/".$filename);
	}
}
