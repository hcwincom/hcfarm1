<?php

namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
 
class OurmenuController extends HomeBaseController
{
    private $m;
    private $cates;
     public function _initialize()
    {
        
        parent::_initialize();
        $this->assign('html_flag','ourmenu');
        $this->m=DB::name('goods');
    
        $cates=session('cates');
        $this->cates=$cates['goods'];
    } 
    public function index()
    {
        $m=$this->m;
         
        $where=[];
        $data=$this->request->param();
        if(!empty($data['cid'])){
           $where['cid']=['eq',$data['cid']];
        }else{
            $data['cid']=0;
        }
        $list=$m->where($where)->order('sort asc,browse desc,id desc')->paginate(9);
        // 获取分页显示
        $page = $list->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('cid',$data['cid']);
        return $this->fetch();
    }
    
    public function detail()
    {
        $id=$this->request->param('id',0,'intval'); 
        if($id==100){
            $type=$this->request->param('type',0,'intval'); 
            $this->redirect('xie',['type'=>$type]);
        }
        $m=$this->m; 
        $info=$m->where('id',$id)->find();
        if(empty($info)){
            $this->redirect(url('portal/index/index'));
        }
        if($info['cid']==config('pros')){
            $this->redirect(url('more_detail',['id'=>$id]));
        }
        foreach(($this->cates) as $k=>$v){
            if($v['id']==$info['cid']){
                $info['cname']=$v['name'];
                break;
            }
        }
        if(empty($info['cname'])){
            $info['cname']='分类不存在';
         }
          zz_browse('goods',$id);
        $this->assign('info',$info); 
        return $this->fetch();
    }
     public function xie(){
         $type=$this->request->param('type',0,'intval'); 
         $this->assign('type',$type); 
         return $this->fetch();
     }
    
    public function more_detail()
    {
        $id=$this->request->param('id',0,'intval');
        $m=$this->m; 
        $info=$m->where('id',$id)->find();
        if(empty($info) || $info['cid']!=config('pros')){
            $this->redirect(url('portal/index/index'));
        }
        
        foreach(($this->cates) as $k=>$v){
            if($v['id']==$info['cid']){
                $info['cname']=$v['name'];
                break;
            }
        }
        if(empty($info['cname'])){
            $info['cname']='分类不存在';
        }
        $list=Db::query('select gp.*,g.name,g.dsc,g.unit,g.pack,g.much from cmf_goods_pros as gp left join cmf_goods as g on g.id=gp.gid where gp.pid=?',[$id]);
        zz_browse('goods',$id);
        $count=count($list);
        $i=ceil($count/2);
        foreach ($list as $k=>$v){
            if($k<$i){
                $list1[]=$v;
            }else{
                $list2[]=$v;
            }
        }
        $this->assign('info',$info);
        $this->assign('list2',$list2);
        $this->assign('list1',$list1);
        return $this->fetch();
    }
    
}
