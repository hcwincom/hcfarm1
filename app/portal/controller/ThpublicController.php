<?php
 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class ThPublicController extends HomeBaseController
{
     public function _initialize()
    {
       
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
       
        //8位18080001
        if(strlen($sn)!=8){
            $this->error('编号错误');
        }
        $where=[ 
            'sn'=>['eq',$sn], 
        ];
        $info=Db::name('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在'.$sn);
        }
        //判断状态
        if($info['status']<3){
            $this->error('卡券尚未开放');
        }
        if($info['psw_num']>10){
            $this->error('密码错误次数过多，请联系客服或明天再试');
        }
        if($info['psw']!=$data['psw']){
            Db::name('voucher')->where($where)->setInc('psw_num');
            $this->error('密码错误');
        }
        session('thj',['sn'=>$info['sn'],'psw'=>$info['psw']]);
        //电脑版手机版
        if(cmf_is_mobile()){
            $this->success('ok',url('portal/thjm/info'));
            exit;
        } 
        $status=config('voucher_status');
        $info['status_name']=$status[$info['status']];
        //提货图片
        $pics1=[];
        if($info['status']>3){
            $where=[
                'vid'=>$info['id'],
                'type'=>1,
            ];
            $pics1=Db::name('voucher_pic')->where($where)->column('id,pic'); 
        }
        
        $this->success('ok','',['info'=>$info,'pics1'=>$pics1]);
        exit;
       
    }
    //提货地址提交
    public function address_do(){
        $data=$this->request->param();
        $thj=session('thj');
        if(empty($thj['psw']) || empty($thj['sn'])){
            session('thj',null);
            $this->error('数据错误，请重新输入编码和密码查询');
        }
        if(empty($data['sn'])  || empty($data['uname']) || empty($data['utel']) || empty($data['take_type'])){
            $this->error('输入错误');
        }
        //提货信息不匹配
        if($thj['sn']!=$data['sn']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        $where=[
            'sn'=>['eq',$data['sn']],
        ];
        $time=time();
        $info=Db::name('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']!=3){
            $this->error('已提货，不能再次提货');
        }
        //判断时间
        if($info['value_time2'] <$time ){
            $this->error('券卡已过期');
        }
        //提货信息不匹配
        if($thj['psw']!=$info['psw']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        //自提还是线上取货
        if($data['take_type']==1){
             //线上取货  
            $update=[
                'uname'=>$data['uname'],
                'utel'=>$data['utel'],
                'city'=>$data['city'],
                'address'=>$data['address'],
                'take_time'=>$time,
                'time'=>$time,
                'take_dsc'=>$data['take_dsc'],
                'status'=>4,
                'take_ip'=>get_client_ip(0),
            ];
            
            $update['city_name']=Db::name('city')
            ->alias('c3')
            ->join('cmf_city c2','c2.id=c3.fid')
            ->join('cmf_city c1','c1.id=c2.fid')
            ->where('c3.id',$data['city'])
            ->value('concat(c1.name,c2.name,c3.name)');
            
            //预订时间为空为立即发货
            if(!empty($data['get0_time'])){
                $data['get0_time']=strtotime($data['get0_time']);
                if($info['value_time1'] > $data['get0_time']){
                    $this->error('预订时间太早，不在发货期内');
                }
                if($info['value_time2'] < $data['get0_time']){
                    $this->error('预订时间太晚，不在发货期内');
                }
                $update['get0_time']=$data['get0_time'];
            }
            $dsc='已提交提货信息，请等待发货，可以在网站查询快递进度';
        }else{
            //有自提点 ,表示已收货
            $update=[
                'uname'=>$data['uname'],
                'utel'=>$data['utel'],
                'network'=>$data['network'], 
                'take_time'=>$time,
                'express_time'=>$time,  
                'get_time'=>$time,  
                'time'=>$time,
                'take_dsc'=>$data['take_dsc'],
                'status'=>6,
                'take_ip'=>get_client_ip(0),
            ];
            $dsc='已提货';
        }
         
        Db::name('voucher')->where($where)->update($update);
        if(cmf_is_mobile()){
            $this->success($dsc,url('portal/thjm/info'));
        }
        $this->success($dsc);
    }
    
    //提货地址提交
    public function back_do(){
        
        $data=$this->request->param();
        $thj=session('thj');
        if(empty($thj['psw']) || empty($thj['sn'])){
            session('thj',null);
            $this->error('数据错误，请重新输入编码和密码查询');
        }
        if(empty($data['dsc'])){
            $this->error('请输入退货描述');
        }
        //提货信息不匹配
        if($thj['sn']!=$data['sn']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        $where=[
            'sn'=>['eq',$thj['sn']],
        ];
        $time=time();
        $m_voucher=Db::name('voucher');
        $info=$m_voucher->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //提货信息不匹配
        if($thj['psw']!=$info['psw']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        //判断状态
        if($info['status']<5 || $info['status']==8){
            $this->error('卡券不能退货');
        }
        //发货7天内退货
        $back_day=config('back_day');
        $time0=$time-$back_day*3600*24;
        if($info['express_time']<$time0){
            $this->error('时间超过退货期，不能退货');
        }
        
        
        //提货券信息
        $update=[
            'time'=>$time, 
        ];
        //退货信息
        $data_back=[
            'pid'=>$info['id'],
            'sn'=>$info['sn'], 
            'dsc'=>$data['dsc'],
            'pic1'=>$data['pic1'],
            'pic2'=>$data['pic2'],
            'pic3'=>$data['pic3'],
            
        ];
        $where_back=['pid'=>$info['id']]; 
        $m_back=Db::name('voucher_back');
        $tmp=$m_back->where($where_back)->find();
        $m_back->startTrans();
        if(empty($tmp)){ 
            $data_back['time']=$time;
            $update['back_time']=$time;
            $update['status']=7;
            $m_back->insert($data_back);
        }else{ 
            $m_back->where($where_back)->update($data_back);
        }
        $m_voucher->where($where)->update($update);
        $m_back->commit();
        if(cmf_is_mobile()){
            $this->success('已提交退货信息',url('portal/thjm/back'));
        }
        $this->success('已提交退货信息',url('portal/thj/back'));
    }
    //确认收货
    public function get_do(){
        
        $data=$this->request->param();
        $thj=session('thj');
        if(empty($thj['psw']) || empty($thj['sn'])){
            session('thj',null);
            $this->error('数据错误，请重新输入编码和密码查询');
        }
        
        //提货信息不匹配
        if($thj['sn']!=$data['sn']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        $where=[
            'sn'=>['eq',$thj['sn']],
        ];
        $time=time();
        $m_voucher=Db::name('voucher');
        $info=$m_voucher->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //提货信息不匹配
        if($thj['psw']!=$info['psw']){
            session('thj',null);
            $this->error('提货信息不匹配，请重新输入编码和密码查询');
        }
        //判断状态
        // 5=>'已发货',
        if($info['status']!=5){
            $this->error('状态错误');
        }
        
        //提货券信息
        $update=[
            'time'=>$time,
            'status'=>6,
            'get_time'=>$time,
        ];
     
        $m_voucher->where($where)->update($update);
        
        $this->success('已确认收货');
    }
}
