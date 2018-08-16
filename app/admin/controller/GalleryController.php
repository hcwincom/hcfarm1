<?php


namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

 
class GalleryController extends AdminbaseController {
    
    private $m;
    private $order;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->order='sort asc,time desc';
        $this->m=Db::name('Gallery');
        $this->assign('flag','展示信息');
    }
    
    /**
     * 展示信息列表
     * @adminMenu(
     *     'name'   => '展示信息管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息管理',
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
     * 展示信息编辑
     * @adminMenu(
     *     'name'   => '展示信息编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息编辑',
     *     'param'  => ''
     * )
     */
    function edit(){
        $m=$this->m;
        $id=$this->request->param('id');
        $info=$m->where('id',$id)->find();
        $this->assign('info',$info); 
        //不同类别到不同的页面
        return $this->fetch('edit');
    }
    /**
     * 展示信息编辑1
     * @adminMenu(
     *     'name'   => '展示信息编辑_执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息编辑_执行',
     *     'param'  => ''
     * )
     */
    function editPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['id'])){
            $this->error('数据错误');
        }
        if(empty($data['pic0'])){
            $this->error('图片上传错误');
        }

        $data['pic']= $data['pic0'].'.jpg';
        $size=config('gallery_pic');
        zz_set_image($data['pic0'], $data['pic'], $size['width'], $size['height'], 6);
      
        $data['time']=time();
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $this->success('修改成功',url('index')); 
        }else{
            $this->error('修改失败');
        }
        
    }
    /**
     * 展示信息删除
     * @adminMenu(
     *     'name'   => '展示信息删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息删除',
     *     'param'  => ''
     * )
     */
    function delete(){
        $m=$this->m;
        $id=$this->request->param('id');
        $row=$m->where('id='.$id)->delete();
        if($row===1){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        exit;
    }
    
    /**
     * 展示信息添加
     * @adminMenu(
     *     'name'   => '展示信息添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息添加',
     *     'param'  => ''
     * )
     */
    public function add(){
        
        return $this->fetch();
    }
    
    /**
     * 展示信息添加1
     * @adminMenu(
     *     'name'   => '展示信息添加_执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '展示信息添加_执行',
     *     'param'  => ''
     * )
     */
    public function addPost(){
        
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['pic0'])){
            $this->error('图片上传错误');
        }
        $data['pic']= $data['pic0'].'.jpg';
        $size=config('gallery_pic');
        zz_set_image($data['pic0'], $data['pic'], $size['width'], $size['height'], 6);
         
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