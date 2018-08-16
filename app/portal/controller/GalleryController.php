<?php

namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class GalleryController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','gallery');
         
    } 
    public function index()
    { 
        //菜单
        $list=DB::name('gallery')->order('sort asc,time desc')->paginate(9);
        
        $page = $list->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        
        return $this->fetch();
    }
    
}
