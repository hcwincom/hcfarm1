<?php
 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
/* 定时操作 */
class TimeController extends HomeBaseController
{
     public function _initialize()
    {
        
    } 
    //每日3点密码错误清零
    public function task(){
       
        $time_day=trim(config('task_date'));
        $time=time();
        $date=trim(date('Y-m-d'));
        //判断重复任务
        if($time_day===$date){
            exit();
        }
        db('user')->where('psw_num','gt',0)->update(['psw_num'=>0]);
        
         
        cmf_set_dynamic_config(['task_date'=>$date]);
        
    }
     
}
