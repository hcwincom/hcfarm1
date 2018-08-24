<?php
 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
/* 移动端提货 */
class ThjmController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','thj'); 
    } 
    
    //提货券首页
    public function goods()
    {
        $sn=$this->request->param('sn',''); 
        $this->assign('sn',$sn);
        $this->assign('html_flag','goods');
        $this->assign('html_title','券卡提货');
        
        return $this->fetch();
    }
     
    //券卡信息
    public function info(){
        $sn=session('thj.sn');
        $where=[
            'sn'=>['eq',$sn],
        ];
        $info=Db::name('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<3){
            $this->error('卡券尚未开放');
        }
        $status=config('voucher_status');
        $info['status_name']=$status[$info['status']];
        $this->assign('info',$info);
        
        return $this->fetch();
    }
    //提货信息
    public function address(){
        $sn=session('thj.sn');
        $where=[
            'sn'=>['eq',$sn],
        ];
        $info=Db::name('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<3){
            $this->error('卡券尚未开放');
        }
        $this->assign('info',$info);
        
        return $this->fetch();
    }
     
    //退货申请
    public function back(){
        $sn=session('thj.sn');
        $where=[
            'sn'=>['eq',$sn],
        ];
        $info=Db::name('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<5 || $info['status']==8){
            $this->error('卡券不能退货');
        }
        //发货7天内退货
        $back_day=config('back_day');
        $time=time()-$back_day*3600*24;
        if($info['express_time']<$time){
            $this->error('时间超过退货期，不能退货');
        }
       
        $this->assign('info',$info);
        $back=Db::name('voucher_back')->where('sn',$sn)->find();
        
        $this->assign('back',$back);
        return $this->fetch();
    }
    
    //
    //线下网点
    public function networks()
    {
        //获取查询
        $data=$this->request->param();
        $m_net=Db::name('network');
        //获取所有有线下网点的城市
        $keys=$m_net->distinct('city')->column('city');
        $citys=Db::name('city')->where('id','in',$keys)->column('id,name');
        $where=['p.status'=>1];
        if(empty($data['city'])){
            $data['city']=0;
        }else{
            $where['p.city']=['eq',$data['city']];
        }
        if(empty($data['name'])){
            $data['name']='';
        }else{
            $where['p.name']=['like','%'.$data['name'].'%'];
        }
        
        $list= Db::name('network')->field('p.*,concat(c1.name,c2.name) as city_name')
        ->alias('p')
        ->join('cmf_city c2','c2.id=p.city')
        ->join('cmf_city c1','c1.id=c2.fid')
        ->where($where)
        ->paginate(6);
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('data',$data);
        $this->assign('html_flag','networks');
        $this->assign('html_title','线下网点');
        return $this->fetch();
    }
    //提货券首页
    public function map()
    {
        $id=$this->request->param('id',0,'intval');
        $where=[
            'id'=>['eq',$id],
        ];
        $info=Db::name('network')->where($where)->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $this->assign('data',$info);
        
        return $this->fetch();
    }
}
