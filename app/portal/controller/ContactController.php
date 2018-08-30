<?php

namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class ContactController extends HomeBaseController
{
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','contact');
        
       
    } 
    public function index()
    {  
       
        if(cmf_is_mobile()){
            return $this->fetch('mobile_index');
        }
        return $this->fetch();
    }
    
}
