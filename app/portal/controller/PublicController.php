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
}
