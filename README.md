# gd_diy
定制手机使用gd库生成效果图  

1.imagecopymerge函数对图像损失过大   
推荐使用imagecopyresampled 对图像损失小  

2.建议放大倍数保存，这样画质会高点   


3.无法解决旋转之后斜线锯齿严重问题   

4.对于因图片过大无法处理的问题  
ini_set('memory_limit','256M');    
设置更大的memory_limit来解决问题   


