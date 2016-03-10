<?php
/**
*¿¿¿¿¿¿¿¿¿¿¿¿¿¿¿¿¿
*/
$channels = array(
    array('id'=>1,'name'=>"ÒÂ·ş",'parId'=>0),
    array('id'=>2,'name'=>"Êé¼®",'parId'=>0),
    array('id'=>3,'name'=>"TĞô",'parId'=>1),
    array('id'=>4,'name'=>"¿ã×Ó",'parId'=>1),
    array('id'=>5,'name'=>"Ğ¬×Ó",'parId'=>1),
    array('id'=>6,'name'=>"Æ¤Ğ¬",'parId'=>5),
    array('id'=>7,'name'=>"ÔË¶¯Ğ¬",'parId'=>5),
    array('id'=>8,'name'=>"ÄÍ¿Ë",'parId'=>7),
    array('id'=>9,'name'=>"ÄÍ¿Ë",'parId'=>3),
    array('id'=>10,'name'=>"ºèĞÇ¶û¿Ë",'parId'=>7),
    array('id'=>11,'name'=>"Ğ¡Ëµ",'parId'=>2),
    array('id'=>12,'name'=>"¿Æ»ÃĞ¡Ëµ",'parId'=>11),
    array('id'=>13,'name'=>"¹ÅµäÃûÖø",'parId'=>11),
    array('id'=>14,'name'=>"ÎÄÑ§",'parId'=>2),
    array('id'=>15,'name'=>"ËÄÊéÎå¾­",'parId'=>14)
);
$stack = array();  //¶¨ÒåÒ»¸ö¿ÕÕ»
$html = array();   //ÓÃÀ´±£´æ¸÷¸öÀ¸Ä¿Ö®¼äµÄ¹ØÏµÒÔ¼°¸ÃÀ¸Ä¿µÄÉî¶È
/*
 * ×Ô¶¨ÒåÈëÕ»º¯Êı
 */
function pushStack(&$stack,$channel,$dep){
    array_push($stack, array('channel'=>$channel,'dep'=>$dep));
}
/*
 * ×Ô¶¨Òå³öÕ»º¯Êı
 */
function popStack(&$stack){
    return array_pop($stack);
}
/*
 * Ê×ÏÈ½«¶¥¼¶À¸Ä¿Ñ¹ÈëÕ»ÖĞ
 */
foreach($channels as $key=>$val){
    if($val['parId'] == 0)
        pushStack($stack,$val,0);
}
/*
 * ½«Õ»ÖĞµÄÔªËØ³öÕ»£¬²éÕÒÆä×ÓÀ¸Ä¿
 */
do{
    $par = popStack($stack); //½«Õ»¶¥ÔªËØ³öÕ»
    /*
     * ²éÕÒÒÔ´ËÀ¸Ä¿Îª¸¸¼¶À¸Ä¿µÄid£¬½«ÕâĞ©À¸Ä¿ÈëÕ»
     */
    for($i=0;$i<count($channels);$i++){
        if($channels[$i]['parId'] == $par['channel']['id']){
            pushStack($stack,$channels[$i],$par['dep']+1);
        }
    }
    /*
     * ½«³öÕ»µÄÀ¸Ä¿ÒÔ¼°¸ÃÀ¸Ä¿µÄÉî¶È±£´æµ½Êı×éÖĞ
     */
    $html[] = array('id'=>$par['channel']['id'],'name'=>$par['channel']['name'],'dep'=>$par['dep']);
}while(count($stack)>0);
