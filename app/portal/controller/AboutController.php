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
class AboutController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','about');
        
       
    } 
    public function index()
    { 
         
        //首页关于我们
        $about=DB::name('about')->order('id asc')->limit('2')->select();
       //关于我们下的主厨
        $m_worker=DB::name('worker');
        $workers=$m_worker->where('is_other',0)->order('sort asc,time desc')->select();
         
        
        $this->assign('about',$about);
        $this->assign('workers',$workers);
       
        return $this->fetch();
    }
    
}
