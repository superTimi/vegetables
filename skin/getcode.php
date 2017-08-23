<?php
Header("Content-type: image/gif");
//初始化
$border = 0; //是否要边框 1要:0不要
$w = 60; //图片宽度
$h = 22; //图片高度
$fontsize = 5; //字体大小

$im = ImageCreate($w, $h); //创建验证图片

//绘制基本框架
$bgcolor = ImageColorAllocate($im, 255, 255, 255); //设置背景颜色
ImageFill($im, 0, 0, $bgcolor); //填充背景色
if($border){
  $black = ImageColorAllocate($im, 0, 0, 0); //设置边框颜色
  ImageRectangle($im, 0, 0, $w-1, $h-1, $black);//绘制边框
}

//产生随机数字计算结果，并打印出来
$a=rand(10,49); //产生随机数a
$b=rand(10,49); //产生随机数b
$c=$a.'+'.$b; //生成输出字符串
$result=$a+$b; //生成验证码
for($i=0; $i< strlen($c); $i++){
  $j = !$i ? 4 : $j+11; //绘字符位置
  $color3 = ImageColorAllocate($im, mt_rand(0,200), mt_rand(0,200), mt_rand(0,200)); //字符随机颜色
  ImageChar($im, $fontsize, $j, 3, substr($c,$i,1), $color3); //绘字符
}

//添加干扰
for($i=0; $i<3; $i++){ //绘背景干扰线
  $color1 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰线颜色
  ImageArc($im, mt_rand(-5,$w), mt_rand(-5,$h), mt_rand(20,300), mt_rand(20,200), 55, 44, $color1); //干扰线
}   
for($i=0; $i<50; $i++){   //绘背景干扰点
  $color2 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰点颜色 
  ImageSetPixel($im, mt_rand(0,$w), mt_rand(0,$h), $color2); //干扰点
}

//把验证码写入session
session_start();
$_SESSION['getcode'] = $result;

/*绘图结束*/
Imagegif($im);
ImageDestroy($im);
?>