<?php

 
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Border;
 
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Alignment;
class VoucherController extends AdminbaseController {

    private $m;
   
    private $voucher_status;
    public function _initialize()
    {
        parent::_initialize(); 
       
        $this->m=Db::name('voucher');
        $this->voucher_status=config('voucher_status');
        $this->assign('voucher_status', $this->voucher_status);
        $this->assign('flag','提货券');
       
    }
    
    /**
     * 提货券列表
     * @adminMenu(
     *     'name'   => '提货券管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券管理',
     *     'param'  => ''
     * )
     */
    function index(){
        $m=$this->m;
        $data=$this->request->param();
        $where=[];
        if(empty($data['status'])){
            $data['status']=0; 
        }else{
            $where['v.status']=['eq',$data['status']];
        }
        //按类型搜索
        $types=[
            'no'=>'未选择',
            'sn'=>'编码',
            'model'=>'型号',
            'spec'=>'规格',
            'num'=>'只数',
            'color'=>'颜色',
            'uname'=>'提货人',
            'utel'=>'提货人联系方式',
            
        ];
        if(empty($data['type1']) || $data['type1']=='no'){
            $data['type1']='no';
            $data['type1_name']='';
        }else{
            if(empty($data['type1_name'])){
                $data['type1_name']='';
            }else{
                $where['v.'.$data['type1']]=['eq',$data['type1_name']];
            } 
        }
        if(empty($data['type2']) || $data['type2']=='no'){
            $data['type2']='no';
            $data['type2_name']='';
        }else{
            if(empty($data['type2_name'])){
                $data['type2_name']='';
            }else{
                $where['v.'.$data['type2']]=['like','%'.$data['type2_name'].'%'];
            }
        }
        //时间
        $times=[
            'no'=>'选择时间',
            'create_time'=>'创建时间',
            'send_time'=>'发卡时间',
            'take_time'=>'提货时间',
            'express_time'=>'发货时间',
            'get0_time'=>'预定收货时间',
            'get_time'=>'实际收货时间',
            'back_time'=>'退货时间',
            'end_time'=>'售后完成时间',
            'time'=>'最后更新时间',
            
        ];
        if(empty($data['time']) || $data['time']=='no'){
            $data['time']='no';
            $data['time1']='';
            $data['time2']='';
        }else{
            if(empty($data['time1'])){
                $data['time1']='';
                if(empty($data['time2'])){
                    $data['time2']=''; 
                }else{
                    $time2=strtotime($data['time2']);
                    $where['v.'.$data['time']]=['elt',$time2];
                }
            }else{
                $time1=strtotime($data['time1']);
                if(empty($data['time2'])){
                    $data['time2']='';
                    $where['v.'.$data['time']]=['egt',$time1];
                }else{
                    $time2=strtotime($data['time2']);
                    if($time1>=$time2){
                        $this->error('时间选择错误');
                    }
                    $where['v.'.$data['time']]=['between',[$time1,$time2]];
                }
            }
        }
        //排序方式
        if(empty($data['order'])){
            $data['order']='time';
        }
        if(empty($data['sort'])){
            $data['sort']='desc';
        }
        
         $list= $m->field('v.*')
         ->alias('v') 
         ->where($where)
         ->order('v.'.$data['order'].' '. $data['sort'])
         ->paginate(20);  
         // 获取分页显示
         $page = $list->appends($data)->render(); 
          
         $this->assign('page',$page);
         $this->assign('data',$data);
         $this->assign('list',$list);
         $this->assign('types',$types);
         $this->assign('times',$times);
         $orders=[
             'time'=>'更新时间',
             'sn'=>'编号',
             'send_time'=>'发卡时间',
             'take_time'=>'提货时间',
             'get0_time'=>'预订时间',
             'express_time'=>'发货时间',
             'get_time'=>'实际收货时间',
             'back_time'=>'退货时间',
             'end_time'=>'售后完成时间', 
         ];
         $this->assign('orders',$orders);
        return $this->fetch();
    }
    /**
     * 退货列表
     * @adminMenu(
     *     'name'   => '退货列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '退货列表',
     *     'param'  => ''
     * )
     */
    function back(){
        $m=$this->m;
        $data=$this->request->param();
        $where=[];
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['v.status']=['eq',$data['status']];
        }
        //按类型搜索
        $types=[
            'no'=>'未选择',
            'sn'=>'编码',
            'model'=>'型号',
            'spec'=>'规格',
            'num'=>'只数',
            'color'=>'颜色',
            'uname'=>'提货人',
            'utel'=>'提货人联系方式',
            
        ];
        if(empty($data['type1']) || $data['type1']=='no'){
            $data['type1']='no';
            $data['type1_name']='';
        }else{
            if(empty($data['type1_name'])){
                $data['type1_name']='';
            }else{
                $where['v.'.$data['type1']]=['eq',$data['type1_name']];
            }
        }
        if(empty($data['type2']) || $data['type2']=='no'){
            $data['type2']='no';
            $data['type2_name']='';
        }else{
            if(empty($data['type2_name'])){
                $data['type2_name']='';
            }else{
                $where['v.'.$data['type2']]=['like','%'.$data['type2_name'].'%'];
            }
        }
        //时间
        $times=[
            'no'=>'选择时间',
            'create_time'=>'创建时间',
            'send_time'=>'发卡时间',
            'take_time'=>'提货时间',
            'express_time'=>'发货时间',
            'get0_time'=>'预定收货时间',
            'get_time'=>'实际收货时间',
            'back_time'=>'退货时间',
            'end_time'=>'售后完成时间',
            'time'=>'最后更新时间',
            
        ];
        if(empty($data['time']) || $data['time']=='no'){
            $data['time']='no';
            $data['time1']='';
            $data['time2']='';
        }else{
            if(empty($data['time1'])){
                $data['time1']='';
                if(empty($data['time2'])){
                    $data['time2']='';
                }else{
                    $time2=strtotime($data['time2']);
                    $where['v.'.$data['time']]=['elt',$time2];
                }
            }else{
                $time1=strtotime($data['time1']);
                if(empty($data['time2'])){
                    $data['time2']='';
                    $where['v.'.$data['time']]=['egt',$time1];
                }else{
                    $time2=strtotime($data['time2']);
                    if($time1>=$time2){
                        $this->error('时间选择错误');
                    }
                    $where['v.'.$data['time']]=['between',[$time1,$time2]];
                }
            }
        }
        
        
        $list= Db::name('voucher_back')
        ->field('vk.sn,vk.dsc,v.sn,v.id,v.name,v.model,v.spec,v.show_money,v.back_time,v.time,v.status,v.uname,v.utel')
        ->alias('vk')
        ->join('cmf_voucher v','v.id=vk.pid')
        ->where($where)
        ->order('vk.id desc')
        ->paginate(20);
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('list',$list);
        $this->assign('types',$types);
        $this->assign('times',$times);
        
        return $this->fetch();
    }
    
    /**
     * 编辑提货券
     * @adminMenu(
     *     'name'   => '提货券编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券编辑',
     *     'param'  => ''
     * )
     */
    function edit(){
        $m=$this->m;
        $id=$this->request->param('id'); 
        $info= $m->where('id',$id)->find();
       /*  $info= $m->field('v.*,p.name as pname,p.price as pprice')
        ->alias('v')
        ->join('cmf_goods p','p.id=v.pid')
        ->find(); */
        if($info['network']!=0){
            $info['network_name']=Db::name('network') 
            ->alias('net')
            ->join('cmf_city city','city.id=net.city')
            ->where('net.id',$info['network'])
            ->value('concat(net.name,city.name)');
            
        }
        $where=[
            'vid'=>$info['id'],
            'type'=>1,
        ];
        $m_pic=Db::name('voucher_pic');
        $pics1=$m_pic->where($where)->column('id,pic');
        
        $back=[];
        if($info['status']>6){
            $back=Db::name('voucher_back')->where('pid',$info['id'])->find();
        }
        
        $this->assign('info',$info);
        $this->assign('express_pics',$pics1);
        $this->assign('back',$back);
        return $this->fetch();
    }
    /**
     * 提货券编辑-执行
     * @adminMenu(
     *     'name'   => '提货券编辑-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券编辑-执行',
     *     'param'  => ''
     * )
     */
    function editPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['id'])){
            $this->error('数据错误');
        }
        $info=$m->where('id', $data['id'])->find();
        
        $data['time']=time();
        
 
        if(!empty($data['vs'])){
            $data['status']=$data['vs'];
        }
        
        unset($data['vs']);
        if($data['status']!=$info['status']){
            switch ($data['status']){
                case 3:
                   
                        $data['send_time']=$data['time'];
                   
                    break;
                case 4:
                    
                        $data['take_time']=$data['time'];
                    
                    break;
                case 5: 
                        $data['express_time']=$data['time'];
                 
                    break;
                case 6: 
                        $data['get_time']=$data['time']; 
                    break;
               
                default:
                    break;
            }
        }
        //处理时间
        $data['value_time1']=strtotime($data['value_time1']);
        $data['value_time2']=strtotime($data['value_time2']);
        if($data['value_time1']>=$data['value_time2']){
           $this->error('请填写有效提货时间');
       }
        //处理图片
        $pics=[];
        if(isset($data['urls'])){
            $path='upload/';
            $pathid='voucher/'.$info['sn'].'/';
            $pics=$data['urls'];
            $pic_size=config('voucher_pic');
            if(!is_dir($path.$pathid)){
                mkdir($path.$pathid);
            }
            foreach($pics as $k=>$v){
                if (!is_file($path.$v))
                {
                    $this->error('有图片损坏，请注意');
                }
                //先比较是否需要额外保存,admin打头的要重新保存
                if(strpos($v, $pathid)!==0){
                    //获取后缀名,复制文件
                    $ext=substr($v, strrpos($v,'.'));
                    $new_file=$pathid.'express-'.$k.'-'.date('Ymd-His').$ext;
                    zz_set_image($v, $new_file, $pic_size['width'], $pic_size['height']);
                    unlink($path.$v);
                    $pics[$k]=$new_file;
                }
            }
            unset($data['urls']);
        }
        
        $where=[
            'vid'=>$info['id'],
            'type'=>1,
        ];
        $m_pic=Db::name('voucher_pic');
        $pics0=$m_pic->where($where)->column('id,pic');
        //比较变化
        $m_pic->startTrans();
        if(!empty(array_diff($pics0,$pics)) ||  !empty(array_diff($pics,$pics0))){
            $data_pic=[];
            foreach($pics as $k=>$v){
                $data_pic[]=[
                    'vid'=>$info['id'],
                    'type'=>1,
                    'pic'=>$v,
                ];
            }
            //删除旧数据
            if(!empty($pics0)){
                $m_pic->where($where)->delete();
            }
            //添加新数据
            if(!empty($pics)){
                $m_pic->insertAll($data_pic);
            }
        }
        if(isset($data['back_express'])){
            $where_back=['pid'=>$data['id']];
            $data_back=[
                'express'=>$data['back_express'],
                'res'=>$data['back_res'],
            ];
            $m_back=Db::name('voucher_back');
            $tmp=$m_back->where($where_back)->find();
            if(empty($tmp)){
                $data_back['pid']=$data['id'];
                $data_back['sn']=$info['sn'];
                $data_back['time']=$data['time'];
                $data['back_time']=$data['time'];
                $m_back->insert($data_back);
            }else{
                $m_back->where($where_back)->update($data_back);
            }
            
            unset($data['back_express']);
            unset($data['back_res']);
        }
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $m_pic->commit();
            $this->success('修改成功',url('edit',['id'=>$data['id']])); 
        }else{
            $m_pic->rollback();
            $this->error('修改失败');
        }
        
    }
    /**
     * 批量编辑提货券
     * @adminMenu(
     *     'name'   => '提货券批量编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券批量编辑',
     *     'param'  => ''
     * )
     */
    function edits(){
        
        
        return $this->fetch();
    }
    /**
     * 提货券批量编辑-执行
     * @adminMenu(
     *     'name'   => '提货券批量编辑-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券批量编辑-执行',
     *     'param'  => ''
     * )
     */
    function editsPost(){
        $m=$this->m;
        $data= $this->request->param();
        $id1=intval($data['id1']);
        $id2=intval($data['id2']);
        if($id1<=0 || $id1>=$id2 ){
            $this->error('编号输入错误');
        }
        unset($data['id1']);
        unset($data['id2']);
        //过滤不更改的
        foreach ($data as $k=>$v){
            if($v==='--' || $v===''){
                unset($data[$k]);
            }
        }
        //处理时间
        if(isset($data['value_time1'])){
            $data['value_time1']=strtotime($data['value_time1']);
        }
        if(isset($data['value_time2'])){
            $data['value_time2']=strtotime($data['value_time2']);
        }
         if(($id2-$id1)>300){
             $this->error('批量处理一次不能超过300');
         }
        $data['time']=time();
        $where=['sn'=>['between',[$id1,$id2]]];
        //原状态
        if(!empty($data['status0'])){
            $where['status']=['eq',$data['status0']];
            unset($data['status0']);
        }
        if(!empty($data['status'])){
            switch ($data['status']){
                case 3: 
                    $data['send_time']=$data['time']; 
                    break;
                
                case 6: 
                    $data['get_time']=$data['time']; 
                    break;
                
                case 8:
                    $data['end_time']=$data['time'];
                    break;
                default:
                    break;
            }
        }
        $row=$m->where($where)->update($data);
        
        if($row>0){
            $this->success('修改成功'.$row.'条数据',url('index'));
        }else{
            $this->error('未修改数据');
        }
        
    }
    /**
     * 添加提货券
     * @adminMenu(
     *     'name'   => '提货券添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券添加',
     *     'param'  => ''
     * )
     */
    function add(){
       /*  exit('暂不添加');
         $m=$this->m;
        $cates=Db::name('cate')->where('type','goods')->order('sort asc')->column('id,name');
        $cid=key($cates); 
        $goods=Db::name('goods')->where(['cid'=>$cid])->column('id,name,price');
        
        $this->assign('cates',$cates);
        $this->assign('goods',$goods);  */
        return $this->fetch();
    }
    /**
     * 提货券添加-执行
     * @adminMenu(
     *     'name'   => '提货券添加-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券添加-执行',
     *     'param'  => ''
     * )
     */
    function addPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['name']) || empty($data['real_money']) || empty($data['show_money']) || empty($data['count'])){
            $this->error('数据错误');
        }
        if($data['count']<=0 || $data['count']>1000 || $data['show_money']<=0){
            $this->error('数据错误,数量请选择1-1000，价格大于0');
        }
       
        $vouchers=[];
        $time=time();
       
        $tmp=$m->order('sn desc')->value('sn');
        
        if(empty($tmp)){
            $start=18080000;
        }else{
            $start=$tmp; 
        }
       
        if(($start+$data['count'])>99999999){
            $this->error('已经超过99999999了');
        }
        for($i=0;$i<$data['count'];$i++){
            $start=$start+1;
            
            $vouchers[]=[
                
                'real_money'=>$data['real_money'],
                'show_money'=>$data['show_money'],
                'name'=>$data['name'],
                'model'=>$data['model'],
                'spec'=>$data['spec'],
                'num'=>$data['num'],
                'color'=>$data['color'],
                'pid'=>0,
                'sn'=> $start,
                'psw'=>rand(100000,999999),
                'dsc'=>$data['dsc'],
                'uid'=>0,
                'create_time'=>$time,
                'time'=>$time,
                'value_time1'=>strtotime($data['value_time1']),
                'value_time2'=>strtotime($data['value_time2']),
            ];
        } 
        $counts=$m->insertAll($vouchers); 
        
        $this->success('已经生成'.$counts.'条记录了');
       
       
    }
    /**
     * 导出excel
     * @adminMenu(
     *     'name'   => '导出excel',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '导出excel',
     *     'param'  => ''
     * )
     */
    function excel(){
      
        $m=$this->m;
        $statuss=$this->voucher_status;
        $data=$this->request->param();
        $where=[];
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        if(empty($data['type1']) || $data['type1']=='no'){
            $data['type1']='no';
            $data['type1_name']='';
        }else{
            if(empty($data['type1_name'])){
                $data['type1_name']='';
            }else{
                $where[$data['type1']]=['eq',$data['type1_name']];
            }
        }
        if(empty($data['type2']) || $data['type2']=='no'){
            $data['type2']='no';
            $data['type2_name']='';
        }else{
            if(empty($data['type2_name'])){
                $data['type2_name']='';
            }else{
                $where[$data['type2']]=['like','%'.$data['type2_name'].'%'];
            }
        }
       
        $list= $m->where($where)
        ->column('id,sn,psw,name,show_money,real_money,dsc,status,model,spec,num,color');  
        
        if(empty($list)){
            $this->error('数据不存在');
        }
 
        ini_set('max_execution_time', '0');
        
        $filename='提货卡'.date('Y-m-d-H-i-s').'.xls';
        $phpexcel = new PHPExcel();
         
        //设置第一个sheet
        $phpexcel->setActiveSheetIndex(0);
        $sheet= $phpexcel->getActiveSheet();
       
        //设置sheet表名
        $sheet->setTitle($filename);
       
        // 所有单元格默认高度
        $sheet->getDefaultRowDimension()->setRowHeight(60);
        $sheet->getDefaultColumnDimension()->setWidth(10);
       
        //单个宽度设置
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
         
        //设置水平居中
        $sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        //垂直居中
        $sheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //长度不够显示的时候 是否自动换行
        $sheet->getDefaultStyle()->getAlignment()->setWrapText(true); 
        //设置文本格式
        $str=PHPExcel_Cell_DataType::TYPE_STRING;
       
        //设置第一行
        $i=1;
       
        $sheet
        ->setCellValue('A'.$i, '序号')
        ->setCellValue('B'.$i, '提货网址')
        ->setCellValue('C'.$i, '提货编号') 
        ->setCellValue('D'.$i, '提货密码')
        ->setCellValue('E'.$i, '状态')
        ->setCellValue('F'.$i, '产品名称')
        ->setCellValue('G'.$i, '展示价格')
        ->setCellValue('H'.$i, '实际价格')
        ->setCellValue('I'.$i, '型号')
        ->setCellValue('J'.$i, '规格')
        ->setCellValue('K'.$i, '数量')
        ->setCellValue('L'.$i, '颜色')
        ->setCellValue('M'.$i, '备注');
        //设置第一行
//         import('phpqrcode',EXTEND_PATH);
//         $dir='upload/qrcode/';
         
        $url0 = 'http://hcfarm.wincomtech.cn'.url('portal/thj/th','',false,false).'/sn/'; 
        $aid=session('ADMIN_ID');
       foreach($list as $k=>$v){ 
           $i++;
           $url = $url0.$v['sn']; 
           $sheet
           ->setCellValue('A'.$i, $i-1) 
           ->setCellValue('B'.$i, $url) 
           ->setCellValue('D'.$i, ($aid==1)?$v['psw']:'******')
           ->setCellValue('E'.$i, $statuss[$v['status']])
           ->setCellValue('F'.$i, $v['name'])
           ->setCellValue('G'.$i, $v['show_money'])
           ->setCellValue('H'.$i, $v['real_money'])
           ->setCellValue('I'.$i, $v['model'])
           ->setCellValue('J'.$i, $v['spec'])
           ->setCellValue('K'.$i, $v['num'])
           ->setCellValue('L'.$i, $v['color'])
           ->setCellValue('M'.$i, $v['dsc']);
           
           $sheet->setCellValueExplicit('C'.$i, $v['sn'],$str);
            
//             //二维码图片
// //            $url = url('portal/thj/th',['sn'=>$v['sn']],true,true); 
//            $tmp_pic=$dir.$v['sn'].'.png';
//            \QRcode::png($url.'/sn/'.$v['sn'], $tmp_pic, QR_ECLEVEL_L,2, 2); 
//            /*设置图片路径 切记：只能是本地图片*/
//            $objDrawing = new PHPExcel_Worksheet_Drawing();
//            $objDrawing->setPath($tmp_pic);
//            /*设置图片高度*/
//            //默认原图大小，不设置
//           /*  $objDrawing->setHeight(60);//照片高度
//            $objDrawing->setWidth(60); //照片宽度 */
           
//            /*设置图片要插入的单元格*/
//            $objDrawing->setCoordinates('B'.$i);
//            /*设置图片所在单元格的格式*/
//            $objDrawing->setOffsetX(5);
//            $objDrawing->setOffsetY(5);
//            $objDrawing->setRotation(0);
//            $objDrawing->setWorksheet($sheet);  
            
       }
       unset($list);
        //***********************画出单元格边框*****************************
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        
        $sheet->getStyle('A1:D'.$i)->applyFromArray($styleArray);
       
        //在浏览器输出
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
        $objwriter->save('php://output');
        unset($sheet);
        unset($phpexcel);
        unset($objwriter);
        $where['status']=['eq',1];
        $rows= $m->where($where)->update(['status'=>2]);
       
        exit;
    }
     
    
    /**
     * 提货券删除
     * @adminMenu(
     *     'name'   => '提货券删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券删除',
     *     'param'  => ''
     * )
     */
    function delete(){
        $m=$this->m;
        $id=$this->request->param('id');  
        
        $info=$m->where('id',$id)->find();
        if($info['status']!=1){
            $this->error('该提货券已导出，不能删除');
        }
        $row=$m->where('id',$id)->delete();
        if($row===1){ 
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        exit;
    }
    /**
     * 提货券批量删除
     * @adminMenu(
     *     'name'   => '提货券批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '提货券批量删除',
     *     'param'  => ''
     * )
     */
    function deletes(){
        $m=$this->m;
        
        if(empty($_POST['ids'])){
            $this->error('未选中');
        }
        $ids=$_POST['ids'];
        
        $where=[
            'id'=>['in',$ids],
            'status'=>['eq',1],
        ];
        $row=$m->where($where)->delete();
        if($row>0){
            $this->success('删除成功'.$row.'条数据');
        }else{
            $this->error('没有删除数据，只能删除未导出的数据');
        }
        exit;
    }
    
     
}

?>