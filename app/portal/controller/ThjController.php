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
         
        //12位201808000001
        //8位18080001
        if(strlen($sn)==12){
            $sn=(substr($sn,2,4)).(substr($sn,8));
        }
        if(cmf_is_mobile()){
            $this->redirect('portal/thjm/goods',['sn'=>$sn]);
        }else{
            $this->redirect('portal/thj/goods',['sn'=>$sn]);
        }
       
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
        $sn=$data['sn'];
       
        //8位18080001
        if(strlen($sn)!=8){
            $this->error('编号错误');
        }
        $where=[ 
            'sn'=>['eq',$sn], 
        ];
        $info=db('voucher')->where($where)->find();
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
            db('voucher')->where($where)->setInc('psw_num');
            $this->error('密码错误');
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
            $pics1=db('voucher_pic')->where($where)->column('id,pic');
        }
        session('thj',['sn'=>$info['sn'],'psw'=>$info['psw']]);
        $this->success('ok','',['info'=>$info,'pics1'=>$pics1]);
    }
    //提货地址提交
    public function address_do(){
        $data=$this->request->param();
        if(empty($data['sn']) || empty($data['psw']) || empty($data['uname']) || empty($data['utel']) || empty($data['take_type'])){
            $this->error('输入错误');
        }
        if(strlen($data['psw'])!=6){
            $this->error('密码错误');
        }
        $where=[
            'sn'=>['eq',$data['sn']],
        ];
        $time=time();
        $info=db('voucher')->where($where)->find();
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
            
            $update['city_name']=db('city')
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
         
        db('voucher')->where($where)->update($update);
        $this->success($dsc);
    }
    //
    //退货申请
    public function back(){
        $sn=session('thj.sn');
        $where=[
            'sn'=>['eq',$sn],
        ];
        $info=db('voucher')->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<5 || $info['status']==8){
            $this->error('卡券不能退货');
        }
        //发货7天内退货
        $time=time()-7*3600*24;
        if($info['express_time']<$time){
            $this->error('时间超过退货期，不能退货');
        }
        
        $this->assign('info',$info);
        $back=db('voucher_back')->where('sn',$sn)->find();
        $this->assign('back',$back);
        return $this->fetch();
    }
    //提货地址提交
    public function back_do(){
        
        $data=$this->request->param();
        $thj=session('thj');
        if(empty($thj['psw'])){
            $this->error('数据错误');
        }
        if(empty($data['dsc'])){
            $this->error('请输入退货描述');
        }
        $where=[
            'sn'=>['eq',$thj['sn']],
        ];
        $time=time();
        $m_voucher=db('voucher');
        $info=$m_voucher->where($where)->find();
        if(empty($info)){
            $this->error('该编号不存在');
        }
        //判断状态
        if($info['status']<5 || $info['status']==8){
            $this->error('卡券不能退货');
        }
        //发货7天内退货
        $time0=$time-7*3600*24;
        if($info['express_time']<$time0){
            $this->error('时间超过退货期，不能退货');
        }
        
        
        //提货券信息
        $update=[
            'time'=>$time,
            'status'=>7,
        ];
        $m_voucher->startTrans();
        $m_voucher->where($where)->update($update);
        $data_back=[
            'pid'=>$info['id'],
            'sn'=>$info['sn'],
            'time'=>$time,
            'dsc'=>$data['dsc'],
            'pic1'=>$data['pic1'],
            'pic2'=>$data['pic2'],
            'pic3'=>$data['pic3'],
            
        ];
        if(empty($data['id'])){
            db('voucher_back')->insert($data_back);
        }else{
            db('voucher_back')->where('id',$data['id'])->update($data_back);
        }
        $this->success('已提交退货信息');
    }
    //线下网点
    public function networks()
    {
        //获取查询
        $data=$this->request->param();
        $m_net=db('network');
        //获取所有有线下网点的城市
        $keys=$m_net->distinct('city')->column('city');
        $citys=db('city')->where('id','in',$keys)->column('id,name');
      
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
        $info=db('network')->where($where)->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $this->assign('data',$info);
        
        return $this->fetch();
    }
}
