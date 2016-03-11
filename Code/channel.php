<?php
/**
 * 个人博客：迹忆博客
 * 博客地址：www.onmpw.com
 * 使用非递归，即使用栈的方式实现栏目的无限级分类查询
*/
$channels = array(
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
$stack = array();  //定义一个空栈
$html = array();   //用来保存各个栏目之间的关系以及该栏目的深度
/*
 * 自定义入栈函数
 */
function pushStack(&$stack,$channel,$dep){
    array_push($stack, array('channel'=>$channel,'dep'=>$dep));
}
/*
 * 自定义出栈函数
 */
function popStack(&$stack){
    return array_pop($stack);
}
/*
 * 首先将顶级栏目压入栈中
 */
foreach($channels as $key=>$val){
    if($val['parId'] == 0)
        pushStack($stack,$val,0);
}
/*
 * 将栈中的元素出栈，查找其子栏目
 */
do{
    $par = popStack($stack); //将栈顶元素出栈
    /*
     * 查找以此栏目为父级栏目的id，将这些栏目入栈
     */
    for($i=0;$i<count($channels);$i++){
        if($channels[$i]['parId'] == $par['channel']['id']){
            pushStack($stack,$channels[$i],$par['dep']+1);
        }
    }
    /*
     * 将出栈的栏目以及该栏目的深度保存到数组中
     */
    $html[] = array('id'=>$par['channel']['id'],'name'=>$par['channel']['name'],'dep'=>$par['dep']);
}while(count($stack)>0);
