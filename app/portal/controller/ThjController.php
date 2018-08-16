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
    public function th()
    {
        $sn=$this->request->param('sn','');
        $sn='201808003001';
        $len=strlen($sn);
        //12位201808000001
        //8位18080001
        if($len==12){
            $sn=(substr($sn,2,4)).(substr($sn,8));
        }
        $this->assign('sn',$sn);
        return $this->fetch();
    }
    
}
