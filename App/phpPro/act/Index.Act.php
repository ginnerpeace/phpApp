<?php
class Index{
    public function index(){
        
    }
    public function add(){
        $data = array(
            array('id'=>1,'name'=>"衣服",'parId'=>0),
            array('id'=>2,'name'=>"书籍",'parId'=>0),
            array('id'=>3,'name'=>"T恤",'parId'=>1),
            array('id'=>4,'name'=>"裤子",'parId'=>1),
            array('id'=>5,'name'=>"鞋子",'parId'=>1),
            array('id'=>6,'name'=>"皮鞋",'parId'=>5),
            array('id'=>7,'name'=>"运动鞋",'parId'=>5),
            array('id'=>8,'name'=>"耐克",'parId'=>7),
            array('id'=>9,'name'=>"耐克",'parId'=>3),
            array('id'=>10,'name'=>"鸿星尔克",'parId'=>7),
            array('id'=>11,'name'=>"小说",'parId'=>2),
            array('id'=>12,'name'=>"科幻小说",'parId'=>11),
            array('id'=>13,'name'=>"古典名著",'parId'=>11),
            array('id'=>14,'name'=>"文学",'parId'=>2),
            array('id'=>15,'name'=>"四书五经",'parId'=>14)
        );
    }
}