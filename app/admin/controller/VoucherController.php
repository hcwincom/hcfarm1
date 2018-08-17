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
        if(empty($data['name'])){
            $data['name']='';
        }else{
            $where['v.sn']=['like','%'.$data['name'].'%'];
        }
//          $list= $m->field('v.*,p.name as pname')
//          ->alias('v')
//          ->join('cmf_goods p','p.id=v.pid')
//          ->where($where)
//          ->order('v.time desc,v.id desc')
//          ->paginate(10);  
         $list= $m->field('v.*')
         ->alias('v') 
         ->where($where)
         ->order('v.time desc,v.id desc')
         ->paginate(10);  
         // 获取分页显示
         $page = $list->appends($data)->render(); 
          
         $this->assign('page',$page);
         $this->assign('data',$data);
         $this->assign('list',$list);
         
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
        $this->assign('info',$info);
      
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
//         'voucher_status'=>[
//             1=>'系统生成',
//             2=>'已导出',
//             3=>'已发放',
//             4=>'已提货',
//             5=>'已发货',
//             6=>'已收货',
//             7=>'订单完成',
//             8=>'已退货',
//             9=>'售后结束',
//         ],
        if($data['status']!=$info['status']){
            switch ($data['status']){
                case 3:
                    if($info['send_time']==0){
                        $data['send_time']=$data['time'];
                    }
                    break;
                case 4:
                    if($info['take_time']==0){
                        $data['take_time']=$data['time'];
                    }
                    break;
                case 5:
                    if($info['express_time']==0){
                        $data['express_time']=$data['time'];
                    }
                    break;
                case 6:
                    if($info['get_time']==0){
                        $data['get_time']=$data['time'];
                    }
                    break;
                default:
                    break;
            }
        }
        
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $this->success('修改成功',url('edit',['id'=>$data['id']])); 
        }else{
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
            if($v=='--'){
                unset($data[$k]);
            }
        }
        $data['time']=time();
        $where=['sn'=>['between',[$id1,$id2]]];
        $row=$m->where($where)->update($data);
        if($row>=1){
            $this->success('修改成功'.$row.'条数据',url('index'));
        }else{
            $this->error('修改失败');
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
        exit('暂不添加');
       /*  $m=$this->m;
        $cates=Db::name('cate')->where('type','goods')->order('sort asc')->column('id,name');
        $cid=key($cates); 
        $goods=db('goods')->where(['cid'=>$cid])->column('id,name,price');
        
        $this->assign('cates',$cates);
        $this->assign('goods',$goods); */
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
        if(empty($data['pid']) || empty($data['real_money']) || empty($data['show_money']) || empty($data['count'])){
            $this->error('数据错误');
        }
        if($data['count']<=0 || $data['count']>1000 || $data['show_money']<=0){
            $this->error('数据错误,数量请选择1-1000，价格大于0');
        }
       
        $vouchers=[];
        $time=time();
        $date=date('Ym');
        $time0=strtotime($date.'01');
        $tmp=$m->where(['create_time'=>['egt',$time0]])->order('create_time desc,id desc')->find();
        if(empty($tmp)){
            $start=0;
        }else{
            $start=intval(substr($tmp['sn'], 6)); 
        }
        
        if(($start+$data['count'])>999999){
            $this->error('已经超过999999了');
        }
        for($i=0;$i<$data['count'];$i++){
            $start=$start+1;
            
            $vouchers[]=[
                'pid'=>$data['pid'],
                'real_money'=>$data['real_money'],
                'show_money'=>$data['show_money'],
                'sn'=> $date.str_pad($start, 6 , '0',STR_PAD_LEFT),
                'psw'=>rand(100000,999999),
                'dsc'=>$data['dsc'],
                'uid'=>0,
                'create_time'=>$time,
                'time'=>$time,
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
        if(empty($data['name'])){
            $data['name']='';
        }else{
            $where['sn']=['like','%'.$data['name'].'%'];
        }
       
        $list= $m->where($where)
        ->column('id,sn,psw,pid,show_money,real_money,dsc,status,model,spec,num,color');  
        
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
       foreach($list as $k=>$v){ 
           $i++;
           $url = $url0.$v['sn']; 
           $sheet
           ->setCellValue('A'.$i, $i-1) 
           ->setCellValue('B'.$i, $url) 
           ->setCellValue('D'.$i, $v['psw'])
           ->setCellValue('E'.$i, $statuss[$v['status']])
           ->setCellValue('F'.$i, $v['pid'])
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
        if($id==8){
            $this->error('组合套装，不能删除！');
        }
        $info=$m->where('id',$id)->find();
        if(empty($info)){
            $this->error('该提货券不存在');
        }
    
        $count=$m->where('fid',$id)->count();
        if($count>0){
            $this->error('该提货券下有信息，不能删除');
        }
        $row=$m->where('id',$id)->delete();
        if($row===1){ 
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        exit;
    }
   
     
}

?>