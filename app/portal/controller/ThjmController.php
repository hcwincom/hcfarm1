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
        $sn=session('thj.sn');
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
        
        return $this->fetch();
    }
    //提货信息
    public function address(){
        $sn=session('thj.sn');
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
        
        return $this->fetch();
    }
    //提货地址提交
    public function address_do(){
        $data=$this->request->param();
        $thj=session('thj');
        if(empty($thj['psw'])){
            $this->error('数据错误');
        }
        if(empty($data['uname']) || empty($data['utel']) ){
            $this->error('输入错误');
        } 
        $where=[
            'sn'=>['eq',$thj['sn']],
        ];
        $time=time();
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
            ];
            
            $update['city_name']=db('city')
            ->alias('c3')
            ->join('cmf_city c2','c2.id=c3.fid')
            ->join('cmf_city c1','c1.id=c2.fid')
            ->where('c3.id',$data['city'])
            ->value('concat(c1.name,c2.name,c3.name)');
            
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
                'status'=>7,
            ];
            $dsc='已提货';
        }
        
        db('voucher')->where($where)->update($update);
        $this->success($dsc,url('info'));
    }
    //
    //线下网点
    public function networks()
    {
        //获取查询
        $data=$this->request->param();
        $m_net=db('network');
        //获取所有有线下网点的城市
        $keys=$m_net->distinct('city')->column('city');
        $citys=db('city')->where('id','in',$keys)->column('id,name');
        $where=[];
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
        ->paginate(2);
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
