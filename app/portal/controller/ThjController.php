<?php
 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class ThjController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','thj'); 
    } 
    //提货券首页
    public function th()
    {
        //sn号码统计改变
        
        $sn=$this->request->param('sn','');
        $sn='201808003001';
       
        //12位201808000001
        //8位18080001
        if(strlen($sn)==12){
            $sn=(substr($sn,2,4)).(substr($sn,8));
        }
        $this->redirect('goods',['sn'=>$sn]);
    }
    //提货券首页
    public function goods()
    {
        //sn号码统计改变
        
        $sn=$this->request->param('sn','');
        
        $this->assign('sn',$sn);
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
        $sn=$data['sn'];
        $len=strlen($sn);
        //12位201808000001
        //8位18080001
        if($len==12){
            $sn=(substr($sn,2,4)).(substr($sn,8));
        }elseif($len!=8){
            $this->error('该编号不存在');
        }
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
        if($info['psw']!=$data['psw']){
            $this->error('密码错误');
        }
         
        $this->success('ok','',['info'=>$info]);
    }
    
}
