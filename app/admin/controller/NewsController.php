<?php

 
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
 
 
class NewsController extends AdminbaseController {

    private $m;
    private $order;
   private $type_info;
    public function _initialize()
    {
        parent::_initialize(); 
        $this->order='sort asc,id desc';
        $this->m=Db::name('news');
        $cates=Db::name('cate')->order('sort asc,id asc')->where('type','news')->select();
        $tmp=[];
        foreach($cates as $v){
            $tmp[$v['id']]=$v['name'];
        }
        $this->assign('flag','新闻');
        $this->assign('cates',$tmp);
    }
    
    /**
     * 新闻列表
     * @adminMenu(
     *     'name'   => '新闻管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻管理',
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
     * 新闻编辑
     * @adminMenu(
     *     'name'   => '新闻编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻编辑',
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
     * 新闻编辑执行
     * @adminMenu(
     *     'name'   => '新闻编辑执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻编辑执行',
     *     'param'  => ''
     * )
     */
    function editPost(){
        $m=$this->m;
        $data= $this->request->param();
        if(empty($data['id'])){
            $this->error('数据错误');
        }
        if(empty($data['pic'])){
            $this->error('必须要有图片');
        }
        
        $size=config('news_pic');
        $data['pic']=zz_set_image($data['pic'], $data['pic'], $size['width'], $size['height'], 6);
        
        $data['time']=time();
        $data['content']=empty($_POST['content'])?'':$_POST['content'];
        $row=$m->where('id', $data['id'])->update($data);
        if($row===1){
            $this->success('修改成功',url('index')); 
        }else{
            $this->error('修改失败');
        }
        
    }
    /**
     * 新闻删除
     * @adminMenu(
     *     'name'   => '新闻删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻删除',
     *     'param'  => ''
     * )
     */
    function delete(){
        $m=$this->m;
        $id=$this->request->param('id');  
        $row=$m->where('id',$id)->delete();
        if($row===1){ 
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        exit;
    }
   
    /**
     * 新闻添加
     * @adminMenu(
     *     'name'   => '新闻添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻添加',
     *     'param'  => ''
     * )
     */
    public function add(){
        
        return $this->fetch();
    }
    
    /**
     * 新闻添加执行
     * @adminMenu(
     *     'name'   => '新闻添加执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '新闻添加执行',
     *     'param'  => ''
     * )
     */
    public function addPost(){
        
        $m=$this->m; 
        $data= $this->request->param();
        if(empty($data['pic'])){
            $this->error('必须要有图片');
        }
        $size=config('news_pic');
        $data['pic']=zz_set_image($data['pic'], $data['pic'], $size['width'], $size['height'], 6);
        
        $data['time']=time();
        $data['insert_time']=time();
        $data['content']=empty($_POST['content'])?'':$_POST['content'];
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