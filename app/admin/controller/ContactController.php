<?php


namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Validate;


class ContactController extends AdminbaseController {
    
    private $m;
    private $order;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->order='id asc';
        $this->m=Db::name('contact');
        $this->assign('flag','用户留言信息');
    }
    
    /**
     * 用户留言列表
     * @adminMenu(
     *     'name'   => '用户留言管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言管理',
     *     'param'  => ''
     * )
     */
    function index(){
        $m=$this->m;
        $params=$this->request->param();
        //条件筛选
        $where=[];
        if(empty($params['status0'])){
            $status0=-1;
        }else{
            $status0=$params['status0']-1;
            $where['status']=['eq',$status0];
        }
        if(empty($params['start_time'] )){
            $start_time='';
        }else{
            $start_time=$params['start_time'];
            $start_time0=strtotime($start_time);
        }
        if(empty($params['end_time'] )){
            $end_time='';
        }else{
            $end_time=$params['end_time'];
            $end_time0=strtotime($end_time);
        }
        if(isset($start_time0)){
            if(isset($end_time0)){
                if($start_time0>=$end_time0){
                    $this->error('起始时间不能大于等于结束时间',url('index'));
                }else{
                    $where['time']=['between',[$start_time0,$end_time0]];
                }
            }else{
                $where['time']=['egt',$start_time0];
            }
        }elseif(isset($end_time0)){
            $where['time']=['elt',$end_time0];
        }
        if(empty($params['name'] )){
            $name='';
        }else{
            $name=$params['name'];
            $where['name']=['like','%'.$name.'%'];
        }
        $list= $m->where($where)->order($this->order)->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $status=[0=>'未处理',1=>'已处理',2=>'无法联系'];
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('status',$status);
        $this->assign('name',$name);
        $this->assign('end_time',$end_time);
        $this->assign('start_time',$start_time);
        $this->assign('status0',$status0);
        
        
        return $this->fetch();
    }
    /**
     * 用户留言回复处理
     * @adminMenu(
     *     'name'   => '用户留言回复处理',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言回复处理',
     *     'param'  => ''
     * )
     */
    function edit(){
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
        $status=$this->request->param('status');
        $info=$m->where('id',$id)->find();
        if(empty($info) || $info['status']!=0){
           $this->error('预定状态改变，请刷新');
        }
        $row=$m->where('id',$id)->update(['status'=>$status]);
        if($row===1){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请刷新');
        }
      
    }
    
    /**
     * 用户留言删除
     * @adminMenu(
     *     'name'   => '用户留言删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言删除',
     *     'param'  => ''
     * )
     */
    function delete(){
        $m=$this->m;
        $id=$this->request->param('id');
       
        $info=$m->where('id',$id)->find();
        if(empty($info) ){
            $this->error('该预定已不存在，请刷新');
        }
        $row=$m->where('id',$id)->delete();
        if($row===1){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        
    }
    /**
     * 用户留言批量删除
     * @adminMenu(
     *     'name'   => '用户留言批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言批量删除',
     *     'param'  => ''
     * )
     */
    function delete_all(){
        $m=$this->m;
       
        $ids     = $this->request->param('ids/a');
        $row = $m->where(['id' => ['in', $ids]])->delete();
         
        if($row>=1){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        
    }
    /**
     * 用户留言邮件回复
     * @adminMenu(
     *     'name'   => '用户留言邮件回复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言邮件回复',
     *     'param'  => ''
     * )
     */
    function reply(){
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
       
        $info=$m->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    /**
     * 用户留言邮件回复_执行
     * @adminMenu(
     *     'name'   => '用户留言邮件回复_执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户留言邮件回复_执行',
     *     'param'  => ''
     * )
     */
    function reply_post(){
        
        $id=$this->request->param('id',0,'intval');
        $validate = new Validate([
            'to'      => 'require|email',
            'subject' => 'require',
            'content' => 'require',
        ]);
        $validate->message([
            'to.require'      => '收件箱不能为空！',
            'to.email'        => '收件箱格式不正确！',
            'subject.require' => '标题不能为空！',
            'content.require' => '内容不能为空！',
        ]);
        
        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        $data['content']=$_POST['content'];
        $result = cmf_send_email($data['to'], $data['subject'], $data['content']);
        if ($result && empty($result['error'])) {
            $this->success('发送成功！',url('index',['id'=>$id]));
        } else {
            $this->error('发送失败：' . $result['message']);
        }
    }
    
}

?>