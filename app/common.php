<?php
// | Copyright (c) 2018-2019 http://zz.zheng11223.top All rights reserved.
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// | Author: infinitezheng<infinitezheng@qq.com>
use think\Config;
use think\Db;
use think\Url;
 
/**
 * 文件日志
 * @param $content 要写入的内容
 * @param string $file 日志文件,在web 入口目录
 */
function zz_log($content, $file = "log.txt")
{
    file_put_contents('log/'.$file, date('Y-m-d H:i:s').' '.$content."\r\n",FILE_APPEND);
}
/* 过滤HTML得到纯文本 */
function zz_get_content($list,$len=100){
    //过滤富文本
    $tmp=[];
    foreach ($list as $k=>$v){
        
        $content_01 = $v["content"];//从数据库获取富文本content
        $content_02 = htmlspecialchars_decode($content_01); //把一些预定义的 HTML 实体转换为字符
        $content_03 = str_replace("&nbsp;","",$content_02);//将空格替换成空
        $contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        $con = mb_substr($contents, 0, $len,"utf-8");//返回字符串中的前100字符串长度的字符
        $v['content']=$con.'...';
        $tmp[]=$v;
    }
    return $tmp;
}

/* 浏览量计算 */
function zz_browse($name,$id){
    
    $session=session('goods.'.$name);
    if(empty($session) || !in_array($id, $session)){
        $session[]=$id;
        //使用sql函数
        //Db::name($name)->where('id',$id)->update(['browse'=>['exp','browse+1']]);
        //字段自增,默认为1
        Db::name($name)->where('id',$id)->setInc('browse');
        session('goods.'.$name,$session);
    }
}

/*制作缩略图
 * zz_set_image(原图名,新图名,新宽度,新高度,缩放类型)
 *  */
function zz_set_image($pic,$pic_new,$width,$height,$thump=6){
    /* 缩略图相关常量定义 */
    //     const THUMB_SCALING   = 1; //常量，标识缩略图等比例缩放类型
    //     const THUMB_FILLED    = 2; //常量，标识缩略图缩放后填充类型
    //     const THUMB_CENTER    = 3; //常量，标识缩略图居中裁剪类型
    //     const THUMB_NORTHWEST = 4; //常量，标识缩略图左上角裁剪类型
    //     const THUMB_SOUTHEAST = 5; //常量，标识缩略图右下角裁剪类型
    //     const THUMB_FIXED     = 6; //常量，标识缩略图固定尺寸缩放类型
    //         $water=INDEXIMG.'water.png';//水印图片
    //         $image->thumb(800, 800,1)->water($water,1,50)->save($imgSrc);//生成缩略图、删除原图以及添加水印
    // 1; //常量，标识缩略图等比例缩放类型
    //         6; //常量，标识缩略图固定尺寸缩放类型
    $path=getcwd().'/upload/';
    $imgSrc=$path.$pic;
    $imgSrc1=$path.$pic_new;
    if(is_file($imgSrc)){
        $image = \think\Image::open($imgSrc);
        $size=$image->size();
        if($size!=[$width,$height] || !is_file($imgSrc1)){
            $image->thumb($width, $height,$thump)->save($imgSrc1);
        }
    }
    return $pic_new;
}

/* 为网址补加http:// */
function zz_link($link){
    $link=trim($link);
    if($link==''){
        return '';
    }
    //处理网址，补加http://
    $exp='/^(http|ftp|https):\/\/([\w.]+\/?)\S*/';
    if(preg_match($exp, $link)==0){
        $link='http://'.$link;
    }
    return $link;
}