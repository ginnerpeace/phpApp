项目由 [迹忆博客](http://www.onmpw.com) 提供
# phpApp
PHP经典小应用——无限级分类

在后台管理项目中，有一个经典的应用模块，那就是栏目的无限级分类。

对无限级分类的几种操作包含以下几种情况

1、添加一个顶级栏目

2、在当前栏目下添加子栏目

3、删除一个栏目，这时应该级联删除其子栏目

4、移动一个栏目

5、展示栏目，这是里面的核心功能——主要是展示各级栏目之间的关系

在我个人的迹忆博客中有对查询的实现原理的简单介绍，网址：http://www.onmpw.com/tm/xwzj/prolan_90.html

全部代码在code文件夹中

当前只有查询功能，接下来会继续补充后续功能

opChannel类的使用说明：

首先由这样几个配置项
```Php
$config = array(
	'requestField'=>array(), //此项表示返回我们需要在返回结果中返回的字段
	'queryType'=>1 , 	//此项表示使用查询的方法，1 表示使用非递归的方式   2 表示使用递归的方式
	'parFieldname'=>''      //表中表示父id的字段名称
	'idFieldname' =>'' 	//表中主键的字段名，在mysql数据库中一般名称为id，但是也有特殊情况，特殊情况下就需要我们指定其名称
	'isReturnDep' =>1	//是否返回深度，1 表示返回     0 表示不返回
)
```
使用举例：
```Php
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
$config = array(
    'requestField'=>array('id','name'),
    'queryType'=>1,   //这里使用非递归的方式
);
$obj = new operaChannel($config);
$obj->setData($channels)->start();
$html = $obj->getResult();
```
