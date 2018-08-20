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
    //提货验证
    public function verify(){
        $data=$this->request->param();
        if(empty($data['sn']) || empty($data['psw']) || empty($data['verify']) ){
            $this->error('输入错误');
        }
        if (!cmf_captcha_check($data['verify'])) {
            $this->error('验证码错误');
        }
        if(strlen($data['psw'])!=6){
            $this->error('密码错误');
        }
        if(strlen($data['sn'])!=8){
            $this->error('编号错误');
        }
        
        $where=[ 
            'sn'=>['eq',$data['sn']], 
        ];
        $info=db('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在'.$data['sn']);
        }
        //判断状态
        if($info['status']<3){
            $this->error('卡券尚未开放');
        }
        if($info['psw_num']>20){
            $this->error('密码错误次数过多，请联系客服后明天再试');
        }
        if($info['psw']!=$data['psw']){
            db('voucher')->where($where)->setInc('psw_num');
            $this->error('密码错误');
        }
        session('thj',['sn'=>$data['sn'],'psw'=>$data['psw']]);
        $this->success('ok',url('info',['sn'=>$data['sn']]));
    }
    //券卡信息
    public function info(){
        $sn=$this->request->param('sn',''); 
        $where=[
            'sn'=>['eq',$sn],
        ];
        $info=db('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<3){
            $this->error('卡券尚未开放');
        }
        $this->assign('info',$info);
        dump($info);
        exit('no');
        return $this->fetch();
    }
    //提货地址提交
    public function address_do(){
        $data=$this->request->param();
        if(empty($data['sn']) || empty($data['psw']) || empty($data['uname']) || empty($data['utel']) || empty($data['city'])){
            $this->error('输入错误');
        }
        $time=time();
        $update=[
            'uname'=>$data['uname'],
            'utel'=>$data['utel'],
            'city'=>$data['city'],
            'address'=>$data['address'],
            'take_time'=>$time,
            'time'=>$time,
            'take_dsc'=>$data['take_dsc'], 
        ];
        if(strlen($data['psw'])!=6){
            $this->error('密码错误');
        }
        $where=[
            'sn'=>['eq',$data['sn']],
        ];
        $info=db('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']!=3){
            $this->error('状态异常，不能提货');
        }
        //判断时间 
        if($info['value_time2'] >$time ){
            $this->error('券卡已过期');
        }
        //预订时间为空为立即发货 
        if(!empty($data['get0_time'])){
            if($info['value_time1'] > $data['get0_time']){
                $this->error('预订时间太早，不在发货期内');
           }
           if($info['value_time2'] < $data['get0_time']){
               $this->error('预订时间太晚，不在发货期内');
           }
           $update['get0_time']=$data['get0_time'];
        }
        
        db('voucher')->where($where)->update($update);
        $this->success('已提交提货信息，请等待发货，可以在网站查询快递进度');
    }
    //
    //线下网点
    public function networks()
    {
        //获取查询
        $data=$this->request->param();
        $m_net=db('network');
        
        $where=[];
        if(empty($data['city'])){
             $data['city']=0;
        }else{
             $where['city']=['eq',$data['city']];
        }
        if(empty($data['name'])){
             $data['name']='';
        }else{
             $where['name']=['like','%'.$data['name'].'%'];
        }
        
        $list= db('network')->field('p.*,concat(c1.name,c2.name) as city_name')
        ->alias('p')
        ->join('cmf_city c2','c2.id=p.city')
        ->join('cmf_city c1','c1.id=c2.fid')
        ->where($where)
        ->paginate(6);
        // 获取分页显示
        $page = $list->appends($data)->render(); 
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('html_flag','networks');
        $this->assign('html_title','线下网点');
        return $this->fetch();
    }
}
