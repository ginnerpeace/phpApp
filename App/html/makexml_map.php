<?php
/**
 * 生成百度网站地图
 *
 * @version        $Id: makehtml_map.php 1 11:17 2010年7月19日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/dedetag.class.php");
global $dsql;
ob_start();
$everynum = 5000;
$count = 0;
$everypage = 10;
$sitemap_dir = $_SERVER['DOCUMENT_ROOT']."/data/sitemapdir/";
$sitemap_url = "http://".$_SERVER['HTTP_HOST']."/data/sitemapdir/";
$urls = array();
$filearr = array();
if(!is_dir($sitemap_dir)){
    mkdir($sitemap_dir,0777);
}
/*
 * 生成栏目列表页的url
 */
$query = "SELECT id,typedir,namerule,namerule2 FROM `#@__arctype` WHERE id NOT IN(13) and topid!=4";
$dsql->SetQuery($query);
$dsql->Execute();
while($row = $dsql->GetArray()){
    $sql = "SELECT count(*) from `#@__archives` where typeid={$row['id']} or typeid in (select id from `#@__arctype` where topid={$row['id']})";
    $res = $dsql->GetOne($sql);
    if ($res['count(*)'] > 0) {
        if ($res['count(*)'] <= $everypage) {
            $typeurl = str_replace('{cmspath}', "http://" . $_SERVER['HTTP_HOST'], $row['typedir']) . "/index.html";
            $urls[]['url'] = $typeurl;
        } else {
            $pagenum = ceil($res['count(*)'] / $everypage);
            for ($i = 1; $i <= $pagenum; $i ++) {
                $typeurl = str_replace('{cmspath}', "http://" . $_SERVER['HTTP_HOST'], $row['typedir']);
                $typeurl = str_replace('{typedir}', $typeurl, str_replace('{tid}_{page}', $row['id'] . "_" . $i, $row['namerule2']));
                $urls[]['url'] = $typeurl;
            }
        }
    }
}
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<urlset>\n";
for($i=0;$i<count($urls);$i++){
    $xml .= "<url>\n";
    $xml .= "<loc>{$urls[$i]['url']}</loc>\n";
    $xml .= "</url>\n";
}
$xml .= "</urlset>";
$filename = "channel_sitemap.xml";
$f = fopen($sitemap_dir.$filename, "w");
fwrite($f, $xml);
fclose($f);
$filearr[] = $filename;
echo "channel_sitemap.xml successfully!<br />";
echoto();
ob_flush();
flush();
/*
 * 生成内容页的url
 */
$query = "SELECT count(*) FROM `#@__archives` a";
$query .= " LEFT JOIN `#@__arctype` t ON a.typeid=t.id WHERE t.id NOT IN(13) and topid!=4";
$total = $dsql->GetOne($query);
$total = $total['count(*)'];
if($total<=$everynum){
    $query = "SELECT a.id aid,a.pubdate,t.id tid,typedir,namerule,namerule2 FROM `#@__archives` a";
    $query .= " LEFT JOIN `#@__arctype` t ON a.typeid=t.id WHERE t.id NOT IN(13) and topid!=4 and arcrank=0";
    $dsql->SetQuery($query);
    $dsql->Execute();
    $urls = array();
    while($res = $dsql->GetArray()){
        $typeurl = str_replace('{cmspath}', "http://" . $_SERVER['HTTP_HOST'], $res['typedir']);
        $typeurl = str_replace('{typedir}', $typeurl, $res['namerule']);
        $typeurl = str_replace('{aid}', $res['aid'], $typeurl);
        $urls[]['url'] = $typeurl;
    }
    $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $xml .= "<urlset>\n";
    for($i=0;$i<count($urls);$i++){
        $xml .= "<url>\n";
        $xml .= "<loc>{$urls[$i]['url']}</loc>\n";
        $xml .= "</url>\n";
    }
	/*
     * 小实例的xml路径
     */
    $iterator = new DirectoryIterator($_SERVER['DOCUMENT_ROOT']."/lt/html/");
    foreach ($iterator as $info) {
        if ($info->isFile()) {
            $filename = $info->getFileName();
            $fileinfo = explode('.', $filename);
            if ($fileinfo[1] == 'html') {
                $xml .= "<url>\n";
                $xml .= "<loc>http://" . $_SERVER['HTTP_HOST'] . "/lt/html/" . $filename . "</loc>\n";
                $xml .= "</url>\n";
            }
        }
    }
    $xml .= "</urlset>";
    $filename = "art_sitemap.xml";
    $f = fopen($sitemap_dir.$filename, "w");
    fwrite($f, $xml);
    fclose($f);
    $filearr[] = $filename;
    echo "art_sitemap.xml successfully!<br />";
    echoto();
    ob_flush();
    flush();
}else{
    $num = ceil($total/$everynum);
    for($i=1;$i<=$num;$i++){
        $urls = array();
        $query = "SELECT a.id aid,a.pubdate,t.id tid,typedir,namerule,namerule2 FROM `#@__archives` a";
        $query .= " LEFT JOIN `#@__arctype` t ON a.typeid=t.id WHERE t.id NOT IN(13) and topid!=4 and arcrank=0 LIMIT ".($i-1)*$everynum.",{$everynum}";
        $dsql->SetQuery($query);
        $dsql->Execute();
        while($res = $dsql->GetArray()){
            $typeurl = str_replace('{cmspath}', "http://" . $_SERVER['HTTP_HOST'], $res['typedir']);
            $typeurl = str_replace('{typedir}', $typeurl, $res['namerule']);
            $typeurl = str_replace('{aid}', $res['aid'], $typeurl);
            $urls[]['url'] = $typeurl;
        }
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<urlset>\n";
        for($i=0;$i<count($urls);$i++){
            $xml .= "<url>\n";
            $xml .= "<loc>{$urls[$i]['url']}</loc>\n";
            $xml .= "</url>\n";
        }
		/*
         * 小实例的xml路径
         */
        $iterator = new DirectoryIterator($_SERVER['DOCUMENT_ROOT']."/lt/html/");
        foreach ($iterator as $info) {
            if ($info->isFile()) {
                $filename = $info->getFileName();
                $fileinfo = explode('.', $filename);
                if ($fileinfo[1] == 'html') {
                    $xml .= "<url>\n";
                    $xml .= "<loc>http://" . $_SERVER['HTTP_HOST'] . "/lt/html/" . $filename . "</loc>\n";
                    $xml .= "</url>\n";
                }
            }
        }
        $xml .= "</urlset>";
        $filename = "art_sitemap_{$i}.xml";
        $f = fopen($sitemap_dir.$filename, "w");
        fwrite($f, $xml);
        fclose($f);
        $filearr[] = $filename;
        echo "art_sitemap_{$i}.xml successfully!<br />";
        echoto();
        ob_flush();
        flush();
    }
}
/*
 * 生成存sitemap文件的xml文件
 */
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<sitemapindex>\n";
for($i=0;$i<count($filearr);$i++){
    $xml .= "<sitemap>\n";
    $xml .= "<loc>{$sitemap_url}{$filearr[$i]}</loc>\n";
    $xml .= "</sitemap>\n";
}
$xml .= "</sitemapindex>\n";
$f = fopen($_SERVER['DOCUMENT_ROOT']."/data/sitemap_index.xml", "w");
fwrite($f, $xml);
fclose($f);
echo "sitemap_index.xml successfully!<br />";
echoto();
ob_flush();
function echoto(){
    for($j=0;$j<1024;$j++){
        echo " ";
    }
}