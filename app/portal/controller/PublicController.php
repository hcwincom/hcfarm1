<?php
 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class PublicController extends HomeBaseController
{
     public function _initialize()
    {
        
    } 
    //获取城市
    public function city(){
        $city=$this->request->param('city',0,'intval');
        $citys=db('city')->where('fid',$city)->column('id,name');
        $this->success('ok','',['citys'=>$citys]);
    }
    
    //线下网点城市
    public function citys_network(){
       
        //获取所有有线下网点的城市
        $citys=db('network')
        ->alias('net')
        ->join('cmf_city city','city.id=net.city')
        ->distinct('net.city')
        ->where('net.status',1)
        ->column('city.id,city.name');
        $this->success('ok','',['citys'=>$citys]);
       
    }
    //线下网点 
    public function city_networks(){
        
        //获取所有有线下网点的城市
        $city=$this->request->param('city',0,'intval');
        $where=['status'=>1];
        if($city>0){
           $where['city']=$city;
        }
        $lists=db('network')->where($where)->column('id,name');
        $this->success('ok','',['lists'=>$lists]);
        
    }
    
    
    /* 文件上传 */
    public function upload(){
        set_time_limit(300);
        $dir=$this->request->param('dir','');
        if(empty($dir)){
           $dir='tmp/';
        }else{
            $dir=$dir.'/';
        }
        
        $path='upload/';
        if(!is_dir($path.$dir)){
            mkdir($path.$dir);
        }
       
        if(empty($_FILES['file'])){
            $this->error('请选择图片');
        }
        $file=$_FILES['file'];
         
        if($file['error']==0){
            if($file['size']>4024000){
                $this->error('文件超出大小限制');
            }
            $info=pathinfo($file['tmp_name']);
            $ext=$info['extension'];
            $filename=$dir.date('Ymd-His').$file['name'];
           
            $destination=$path.$filename;
            if(move_uploaded_file($file['tmp_name'], $destination)){
                 
                if(is_file($path.$filename)){
                    
                    $this->success('文件上传成功','',['path'=>$filename]);
                }else{
                    $this->error('文件上传失败');
                }
            }else{
                $this->error('文件上传失败');
            }
        }else{
            $this->error('文件传输失败');
        }
    }
}
