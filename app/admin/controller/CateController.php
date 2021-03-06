<?php

 
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
 
 
class CateController extends AdminbaseController {

    private $m;
    private $order;
   private $type_info;
    public function _initialize()
    {
        parent::_initialize(); 
        $this->order='type asc,sort asc,id asc';
        $this->m=Db::name('Cate');
        $this->type_info=['goods'=>'产品','news'=>'新闻','service'=>'活动'];
        $this->assign('flag','分类');
        $this->assign('type_info',$this->type_info);
    }
    
    /**
     * 分类列表
     * @adminMenu(
     *     'name'   => '分类管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类管理',
     *     'param'  => ''
     * )
     */
    function index(){
        $m=$this->m;

         $list= $m->order($this->order)->paginate(10);
         // 获取分页显示
         $page = $list->render(); 
          
         $this->assign('page',$page);
         
         $this->assign('list',$list);
         
        return $this->fetch();
    }
    /**
     * 编辑分类
     * @adminMenu(
     *     'name'   => '分类编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类编辑',
     *     'param'  => ''
     * )
     */
    function edit(){
        $m=$this->m;
        $id=$this->request->param('id'); 
        $info=$m->where('id',$id)->find(); 
      
        $this->assign('info',$info);
       
        
        //不同类别到不同的页面
        return $this->fetch();
    }
    /**
     * 分类编辑-执行
     * @adminMenu(
     *     'name'   => '分类编辑-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类编辑-执行',
     *     'param'  => ''
     * )
     */
    function editPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['id'])){
            $this->error('数据错误');
        }
        
        $data= $this->request->param();
        $path='upload/';
        if(!empty($data['pic']) && is_file($path.$data['pic'])){
//             $pic_size=config('cate_pic');
            $pathid='cate/';
            if(strpos($data['pic'], $pathid)!==0){
                //获取后缀名,复制文件
                $ext=substr($data['pic'], strrpos($data['pic'],'.'));
                $new_file=$pathid.($data['id']).'-'.date('Ymd-His').$ext;
                //$data['pic']=zz_set_image($data['pic'], $new_file, $pic_size['width'], $pic_size['height'],2);
                copy($path.$data['pic'], $path.$new_file);
            } 
        }
        $data['time']=time();
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $this->success('修改成功',url('index')); 
        }else{
            $this->error('修改失败');
        }
        
    }
    /**
     * 分类删除
     * @adminMenu(
     *     'name'   => '分类删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类删除',
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
            $this->error('该分类不存在');
        }
   
        $pro=Db::name($info['type']);
        $count=$pro->where('cid',$id)->count();
        if($count>0){
            $this->error('该分类下有信息，不能删除');
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
     * 分类添加
     * @adminMenu(
     *     'name'   => '分类添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类添加',
     *     'param'  => ''
     * )
     */
    public function add(){
        
        return $this->fetch();
    }
    
    /**
     * 分类添加-执行
     * @adminMenu(
     *     'name'   => '分类添加-执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '分类添加-执行',
     *     'param'  => ''
     * )
     */
    public function addPost(){
        
        $m=$this->m; 
        $data= $this->request->param();
        
        $data['time']=time();
        $row=$m->insertGetId($data);
        if($row>=1){
            $this->success('已成功添加',url('index')); 
        }else{
            $this->error('添加失败');
        }
        exit;
    }
}

?>