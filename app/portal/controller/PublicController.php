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
}
