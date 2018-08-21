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
class NetworkController extends AdminbaseController {

    private $m;
    
    public function _initialize()
    {
        parent::_initialize(); 
       
        $this->m=Db::name('network');
       
        $this->assign('flag','线下网点');
        $this->assign('network_status',config('network_status'));
       
    }
    
    /**
     * 线下网点列表
     * @adminMenu(
     *     'name'   => '线下网点管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点管理',
     *     'param'  => ''
     * )
     */
    function index(){
        $m=$this->m;
        $data=$this->request->param();
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
  
         $list= $m->field('p.*,concat(c1.name,c2.name) as city_name')
         ->alias('p') 
         ->join('cmf_city c2','c2.id=p.city')
         ->join('cmf_city c1','c1.id=c2.fid')
         ->where($where) 
         ->paginate(10);  
         // 获取分页显示
         $page = $list->appends($data)->render(); 
          $citys=$m
          ->alias('p')
          ->distinct('p.city')
          ->join('cmf_city c2','c2.id=p.city')
          ->column('c2.id,c2.name');
         $this->assign('page',$page);
         $this->assign('citys',$citys);
         $this->assign('data',$data);
         $this->assign('list',$list);
         
        return $this->fetch();
    }
    /**
     * 编辑线下网点
     * @adminMenu(
     *     'name'   => '线下网点编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点编辑',
     *     'param'  => ''
     * )
     */
    function edit(){
        $m=$this->m;
        $id=$this->request->param('id'); 
      
        $info= db('network')->field('p.*,concat(c1.name,c2.name) as city_name')
        ->alias('p')
        ->join('cmf_city c2','c2.id=p.city')
        ->join('cmf_city c1','c1.id=c2.fid')
        ->where('p.id',$id)->find();
      
        $this->assign('info',$info);
      
        return $this->fetch();
    }
    /**
     * 线下网点编辑-执行
     * @adminMenu(
     *     'name'   => '线下网点编辑-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点编辑-执行',
     *     'param'  => ''
     * )
     */
    function editPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['id'])){
            $this->error('数据错误');
        }
       
         
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $this->success('修改成功',url('edit',['id'=>$data['id']])); 
        }else{
            $this->error('修改失败');
        }
        
    }
    
    /**
     * 添加线下网点
     * @adminMenu(
     *     'name'   => '线下网点添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点添加',
     *     'param'  => ''
     * )
     */
    function add(){
        
        return $this->fetch();
    }
    /**
     * 线下网点添加-执行
     * @adminMenu(
     *     'name'   => '线下网点添加-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点添加-执行',
     *     'param'  => ''
     * )
     */
    function addPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['name']) || empty($data['city'])){
            $this->error('数据输入错误');
        }
         
         $m->insert($data); 
        
        $this->success('添加成功',url('index'));
       
       
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
      exit('暂无');
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
     * 线下网点删除
     * @adminMenu(
     *     'name'   => '线下网点删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '线下网点删除',
     *     'param'  => ''
     * )
     */
    function delete(){
        
        $m=$this->m;
        $id=$this->request->param('id');  
        
        $info=$m->where('id',$id)->find();
        if(empty($info)){
            $this->error('该线下网点不存在');
        }
    
        $tmp=db('voucher')->where('network',$id)->find();
        if(!(empty($tmp))){
            $this->error('该线下网点下有提货信息，不能删除');
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