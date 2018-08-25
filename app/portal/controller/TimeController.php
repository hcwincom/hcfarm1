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
        $log='task.txt';
        zz_log('定时任务开始',$log);
        $time_day=trim(config('task_date'));
        $time=time();
        $date=trim(date('Y-m-d'));
        //判断重复任务
        if($time_day===$date){
            exit();
        }
        $rows=Db::name('voucher')->where('psw_num','gt',0)->update(['psw_num'=>0]);
        zz_log('密码错误清零'.$rows.'条',$log);
        cmf_set_dynamic_config(['task_date'=>$date]);
        exit();
    }
     
}
