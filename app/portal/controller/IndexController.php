<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class IndexController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','index'); 
    } 
    public function index()
    {
        
        //banner
        $banners=DB::name('banner')->order('sort asc,id asc')->select();
        //首页关于我们
        $about=DB::name('about')->where('id',3)->find();
        
        //新闻
        $news=DB::name('news')->order('sort asc,browse desc,id desc')->limit(6)->select();
       //褒奖改为活动
        //$people=DB::name('service')->order('sort asc,time desc')->select();
        //获取菜单信息
        $m_goods=DB::name('goods');
        //排名菜单
        $goods_sort=$m_goods->order('sort asc,browse desc,id desc')->limit('6')->select();
        //最新菜单
        $goods_new=$m_goods->order('id desc')->limit('3')->select();
      
        //获取首页新闻
        $service=DB::name('service')->order('sort asc,browse desc,id desc')->find();
       
        $this->assign('banners',$banners);
        $this->assign('about',$about);
       
        $this->assign('news',$news);
        $this->assign('goods_sort',$goods_sort);
        $this->assign('goods_new',$goods_new);
       
        $this->assign('service',$service);
        $cates=session('cates');
       
        $this->assign('cate_goods',$cates['goods']);
        if(cmf_is_mobile()){
            return $this->fetch('mobile_index');
        }
        return $this->fetch();
    }
    
    public function send(){
       
        $data=$this->request->param();
      
        $data['time']=time();
        $row=DB::name('contact')->insertGetId($data);
        if($row>=1){
            $this->success('感谢你的留言，公司会及时回复');
        }else{
            $this->error('留言失败，请刷新重试');
        }
         
    }
}
