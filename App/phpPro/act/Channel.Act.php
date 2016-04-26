<?php
//namespace act;
class Channel{
    private $db;
    public function __construct(){
        //$this->db = \lib\Db\Db::Instance();
		$this->db = Db::Instance();
        //header("Access-Control-Allow-Origin:http://192.168.5.201:1001");
    }
    public function find(){
        //$oc = new \mod\opChannel();
		$oc = new opChannel();
		
        $channel = $this->db->table('channel')->select();
        $cha = $oc->setData($channel)->start();
        //$cha = \Common::onmpw_json_encode($cha);
		$cha = Common::onmpw_json_encode($cha);
        echo $cha;
		//echo json_encode(array('res'=>1));
    }
    public function add(){
        $data = array(
            array('id'=>1,'name'=>'迹忆博客','parId'=>0),
            array('id'=>2,'name'=>'学无止境','parId'=>1),
            array('id'=>3,'name'=>'趣味杂谈','parId'=>1),
            array('id'=>4,'name'=>'编程语言','parId'=>2),
            array('id'=>5,'name'=>'网络','parId'=>2),
            array('id'=>6,'name'=>'算法','parId'=>2),
            array('id'=>7,'name'=>'操作系统','parId'=>2),
            array('id'=>8,'name'=>'数据库','parId'=>2),
            array('id'=>9,'name'=>'WEB前端','parId'=>2),
            array('id'=>10,'name'=>'读书','parId'=>3),
            array('id'=>11,'name'=>'观点与感想','parId'=>3),
        );
        $res = $this->db->table('channel')->addMore($data);
        var_dump($res);
    }
    /**
     * 增加子栏目
     */
    public function addChild(){
        $data = array('name'=>addslashes($_POST['name']),'parId'=>intval($_POST['parId']));
        $res = $this->db->table('channel')->add($data);
        if($res){
            echo json_encode(array('res'=>1,'dep'=>intval($_POST['dep']),'id'=>$this->db->lastInsId()));
        }else{
            echo json_encode(array('res'=>0));
        }
    }
    /**
     * 删除栏目
     */
    public function del(){
        $id = $_POST['id'];
        $stack = array($id);
        $ids = array();
        while(count($stack)>0){
            $id = array_pop($stack);
            array_push($ids,$id);
            $res = $this->db->table('channel')->field('id')->where('parId='.$id)->select();
            if(count($res)>0){
                for($i=0;$i<count($res);$i++){
                    array_push($stack,$res[$i]['id']);
                }
            }
        }
        $res = $this->db->table('channel')->where('id in ('.implode(',', $ids).')')->delete();
        if($res){
            echo json_encode($ids);
        }else{
            echo 0;
        }
    }
    /**
     * 更新栏目
     */
    public function update(){
        $id = $_POST['id'];
        $typename = $_POST['typename'];
        $res = $this->db->table('channel')->where('id='.$id)->update(array('name'=>$typename));
        if($res){
            echo json_encode(array('res'=>1));
        }else{
            echo json_encode(array('res'=>0));
        }
        return ;
    }
    /**
     * 移动栏目
     */
    public function move(){
        $parid = $_POST['parId'];
        $id = $_POST['id'];
        //首先查找当前要移动的栏目的父id 是否和要移动到栏目的子栏目的id是否相等
        $ids = $this->db->table('channel')->field('parId')->where('id='.$id)->find();
        if($ids['parId'] == $parid){
            echo json_encode(array('res'=>1));
            return ;
        }
		
        $res = $this->db->table('channel')->where('id='.$id)->update(array('parId'=>$parid));
        if($res){
            echo json_encode(array('res'=>1));
        }else{
            echo json_encode(array('res'=>0));
        }
    }
}