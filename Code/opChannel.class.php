<?php
/**
 * ���˲��ͣ����䲩��
 * ���͵�ַ��www.onmpw.com
 * ��װ���޼�����
 */
class operaChannel{
    /**
     * ������
     * @var array
     */
    private $config = array(
        'requestField'=>array('id'),
        'queryType'=>1,  //��ѯ��ʽ    1-��ʾʹ�÷ǵݹ鷽ʽ    2-��ʾʹ�õݹ鷽ʽ
        'parFieldname'=>'parId',
        'idFieldname' => 'id',
        'isReturnDep' => 1 //�Ƿ񷵻���ȣ�1��ʾ�������        0 ��ʾ������
    );
    /**
     * Ҫ��ѯ������
     * @var array
     */
    private $channels;  //Ҫ��ѯ������
    /**
     * ���������ɵ�����
     * @var array
     */
    private $html = array();
    /**
     * �������õ���ջ
     * @var array
     */
    private $stack = array();
    /* private $channels = array(
        array('id'=>1,'name'=>"�·�",'parId'=>0),
        array('id'=>2,'name'=>"�鼮",'parId'=>0),
        array('id'=>3,'name'=>"T��",'parId'=>1),
        array('id'=>4,'name'=>"����",'parId'=>1),
        array('id'=>5,'name'=>"Ь��",'parId'=>1),
        array('id'=>6,'name'=>"ƤЬ",'parId'=>5),
        array('id'=>7,'name'=>"�˶�Ь",'parId'=>5),
        array('id'=>8,'name'=>"�Ϳ�",'parId'=>7),
        array('id'=>9,'name'=>"�Ϳ�",'parId'=>3),
        array('id'=>10,'name'=>"���Ƕ���",'parId'=>7),
        array('id'=>11,'name'=>"С˵",'parId'=>2),
        array('id'=>12,'name'=>"�ƻ�С˵",'parId'=>11),
        array('id'=>13,'name'=>"�ŵ�����",'parId'=>11),
        array('id'=>14,'name'=>"��ѧ",'parId'=>2),
        array('id'=>15,'name'=>"�����徭",'parId'=>14)
    ); */
    /**
     * ���캯������Ҫ��ʵ������һЩ��ʼ������
     * @param array $config
     */
    public function __construct($config=array()){
        if(count($config)>0){
            $this->config = array_merge($this->config,$config);
        }
        
    }
    /**
     * ����Ҫ��ѯ������
     * @param array $channels
     * @return operaChannel  ���ص�ǰ����
     */
    public function setData($channels=array()){
        $this->channels = $channels;
        return $this;
    }
    /**
     * ��ʼ����
     */
    public function start(){
        /*
         * �жϲ�ѯ��ʽ
         */
        if($this->config['queryType'] == 1){
            $this->query();
        }else{
            $this->recurQuery(0,1);
        }
    }
    /**
     * �ǵݹ��ѯ����
     */
    private function query(){
        /*
         * ���Ƚ�������Ŀ��ջ
         */
        foreach($this->channels as $key=>$val){
            if($val[$this->config['parFieldname']] == 0){
                $this->push($val,1);
            }
        }
        do{
            $par = $this->pop();
            for($i=0;$i<count($this->channels);$i++){
                if($this->channels[$i][$this->config['parFieldname']] == $par['channel'][$this->config['idFieldname']]){
                    $this->push($this->channels[$i],$par['dep']+1);
                }
            }
            if($this->config['isReturnDep'] == 1){
                $arr = array('dep'=>$par['dep']);
            }elseif($this->config['isReturnDep'] == 0){
                $arr = array();
            }
            foreach($this->config['requestField'] as $v){
                $arr[$v] = $par['channel'][$v];
            }
            $this->html[] = $arr;
        }while(count($this->stack)>0);
    }
    
    private function recurQuery($parid,$dep){
        /*
         * �������ݣ�����parIdΪ����$paridָ����id
         */
        for($i = 0;$i<count($this->channels);$i++){
            if($this->channels[$i]['parId'] == $parid){
                if($this->config['isReturnDep'] == 1){
                    $arr = array('dep'=>$dep);
                }elseif($this->config['isReturnDep'] == 0){
                    $arr = array();
                }
                foreach($this->config['requestField'] as $v){
                    $arr[$v] = $this->channels[$i][$v];
                }
                $this->html[] = $arr;
                self::recurQuery($this->channels[$i]['id'],$dep+1);
            }
        }
    }
    
    /**
     * ��ջ����
     * @param array $channel  Ҫ��ջ������
     * @param int $dep   ���
     */
    private function push($channel,$dep){
        array_push($this->stack,array('channel'=>$channel,'dep'=>$dep));
    }
    /**
     * ��ջ����
     * @return mixed
     */
    private function pop(){
        return array_pop($this->stack);
    }
    /**
     * ���������
     */
    public function getResult(){
        return $this->html;
    }
    
}
?>
