<?php
/**
 * 个人博客：迹忆博客
 * 博客地址：www.onmpw.com
 * 递归实现无限极分类
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
$html = array();

/**
 * 递归查找父id为$parid的结点
 * @param array $html   按照父-》子的结构存放查找出来的结点
 * @param int $parid    指定的父id
 * @param array $channels   数据数组
 * @param int $dep   遍历的深度，初始化为1
 */
function getChild(&$html,$parid,$channels,$dep){
    /*
     * 遍历数据，查找parId为参数$parid指定的id
     */
    for($i = 0;$i<count($channels);$i++){
        if($channels[$i]['parId'] == $parid){
            $html[] = array('id'=>$channels[$i]['id'],'name'=>$channels[$i]['name'],'dep'=>$dep);
            getChild($html,$channels[$i]['id'],$channels,$dep+1);
        }
    }
}
getChild($html,0,$channels,1);
?>
