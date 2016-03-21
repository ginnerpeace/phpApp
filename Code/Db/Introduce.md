# Db类的介绍
1.采用单例设计模式
该类采用单例设计模式，保证了该类实例对象的唯一性
实现方式

	public static $_instance; //静态属性，存储实例对象

/**
  私有化构造函数 这是构造单例设计模式必须的一步
*/

	private function __construct($config=''){
	     $this->config = $this->parseConfig($config);
	}

/**
  实例化对象 采用静态公共方法
*/

	public static function Instance(){
		if(self::$_instance instanceof self){
		     return self::$_instance;
		}
		self::$_instance = new self;
		return self::$_instance;
	}

2.功能介绍

首先该类支持主从复制数据库的连接，支持一主多从模式的数据库

当线程正在连接的一台从服务器宕机时，程序会自动重新连接其他正常的从服务器

其次当新增数据到数据表中时，该类支持一次添加多条数据，并且这多条数据可以是不同的表

可以通过addMore函数的第二个参数来指明是多表插入，其实现核心代码如下

/**
     * 一次性插入多条数据，支持不同表的插入
     * 当使用多表插入功能时需要在第二个参数中指定 $options['multitable'] = true
     * 并且$data的格式为
     * array(
     *  '表名1'=>array(array(),array()),
     *  '表名2'=>array(array(),array())
     * )
     * @param array $data
     * @param array $options
     * @return boolean
     */
     
    public function addMore($data = array(),$options = array()){
        if(isset($options['table']))
            $this->table($options['table']);
        if(!is_array($data)) return false;
        /*
         * 开启事务处理多条语句
         */
        $this->startTransaction();
        foreach($data as $key=>$val){
            //查看是否是多表插入
            if(isset($options['multitable'])&&$options['multitable']){
                /*
                 * 多表插入，则$key为表名,$val为要插入的数据
                 * 使用递归的方式再次对多条数据进行插入
                 */
                $res = $this->addMore($val,array('table'=>$key));
            }else{
                //单表插入
                $res = $this->add($val);
            }
            if(!$res){  
                //如果有一条数据插入失败，则回滚事务，撤销所有的操作
                $this->rollback();
                return false;
            }
        }
        //如果所有插入操作无误，则提交事务
        $this->commit();
        return true;
    }
    
同时支持事务处理，当多条插入数据之中的一条数据插入失败，可通过事务回滚撤销其它插入的数据
    
3.其它支持正常的增删改查
    
下面介绍使用到的函数的用法
    
1）实例化该对象
    
使用该类需要先实例化该类的对象
    
    $obj = Db::Instance();
    
2) 查找数据
    
查找数据用到的函数有 select()和find()两个函数
    
select()函数查找多条数据
    
使用实例
    
    $res = $obj->field('id,name')->where('id > 10')->select();
    
返回值：
    
查找失败 返回 false    查找成功 返回多条数据
    
    array(
    	array('id'=>11,'name'=>'迹忆博客1'),
    	array('id'=>12,'name'=>'迹忆博客2'),
    )
	
find()是返回一条数据
    
    $res = $obj->field('id,name')->where('id=10')->find()
    
返回值
    
查找失败 返回 false  查找成功 返回一条数据
    
    array('id'=>10,'name'=>'迹忆博客')
    
3）添加数据
    
添加数据有两个函数 add($data,$options) 和addMore($data,$options)
    
    add($data.$options)
    
$data  要添加的数据
    
数据格式
    
    array('id'=>13,'name'=>'onmpw')
    
$options 可选参数
    
可指定表名 格式为 array('table'=>'表名') 此处指定表名的优先级最高
    
返回值
    
插入失败 返回 false  插入成功 返回插入的条数
    
    addMore($data,$options)
    
可以通过$options选项指定是夺标插入还是单表插入
    
'multitable'=>true   多表插入  如果设定此项则默认是单表插入  多表插入$data的数据格式
    
    $data = array(
    	'tablename1'=>array(
		array('id'=>20,'name'=>'迹忆博客1'),
		array('id'=>21,'name'=>'迹忆博客2'),
		array('id'=>22,'name'=>'迹忆博客3'),
	),
	'tablename2'=>array(
		array('id'=>20,'name'=>'迹忆博客1','url'=>'www.onmpw.com'),
		array('id'=>21,'name'=>'迹忆博客2','url'=>'www.onmpw.com'),
		array('id'=>22,'name'=>'迹忆博客3','url'=>'www.onmpw.com'),
	)
    
    )
   	 
'multitable'=>false / 不设定此项 单表插入 $data的数据格式为
    
    $data = array(
	array('id'=>31,'name'=>'迹忆博客1','url'=>'www.onmpw.com'),
	array('id'=>32,'name'=>'迹忆博客2','url'=>'www.onmpw.com'),
	array('id'=>33,'name'=>'迹忆博客3','url'=>'www.onmpw.com'),
    )
    
'table'=>'表名' 指定插入数据的数据表名，此项在单表插入时有效，并且较之于其他指定表名的方式优先级高
   
返回值
    
插入失败 返回 false   插入成功返回插入的条数
    
4）修改 update($data,$options)
    
修改数据函数
    
$data 要修改的数据，格式为
    
    array(
    	'name'=>'onmpw',
	'url'=>'http://onmpw.com'
    );
    
$options 可以指定表名
    
'table'=>'表名'
    
返回值
    
更新失败 返回 false  更新成功返回更新的条数
    
5）删除 delete($options)
    
$options 可以指定表名
    
'table'=>'表名' 此种指定表名的优先级最高
    
实例
    
    $res = $obj->table('repl')->where('id=10')->delete(); //删除repl表下id=10的记录
    
    $res = $obj->table('repl')->where('id=13')->delete(array('table'=>'test'));  //删除test表下id=13的记录
    
等价于
    
    $res = $obj->where('id=13')->delete(array('table'=>'test'))
    
返回值
    
删除失败 返回false  删除成功返回 删除的记录条数
    
6) table($str) 函数  指定表名
    
    $obj->table('test')  //指定当前操作的表为test表
    
返回值为 当前对象  object
    
7) where($where)  指定where条件
    
$where 可以是字符串也可以是数组
    
字符串
    
    $obj->table('test')->where("name='迹忆博客',url='www.onmpw.com'");
    
数组
    
    $obj->table('test')->where(array('name'=>'迹忆博客','url'=>'www.onmpw.com'))
    
返回值为 当前对象  object
    
8）field($field) 指定查询的字段名称
    
    $obj->table('test')->field('name,url')->select();
    
如果在查询的时候不适用field()函数指定字段，默认会查询该表的所有字段
    
返回值为 当前对象  object
    
9) orderby($str)  指定按照那个字段排序
    
    $obj->table('test')->field('id,name,url')->where("name='迹忆博客'")->orderby('id DESC')->select();
    
按照id 降序排列
    
    $obj->table('test')->field('id,name,url')->where("name='迹忆博客'")->orderby('id')->select();
    
也可以不指定是降序或者升序
    
返回值为 当前对象 object
    
10) limit($limit)
    
$limt 可以为字符串也可以为数组
    
数组
    
    array(page,listrows)
    
page 指定当前的页数   listrows指定每页取出的条数
    
字符串
    
10,12
    
10表示从第十条记录开始取，12表示取出的条数
    
    $res = $obj->table('test')->field('id,name,url')->where("name='迹忆博客'")->orderby('id DESC')->limit('10,12')->select()
    
返回值为 当前对象 object
    
11）sql($sql)  执行指定的sql语句
    
    $sql = "select name,url from test where name='迹忆博客'";
    
    $res = $obj->sql($sql);
    
返回执行的结果

	


