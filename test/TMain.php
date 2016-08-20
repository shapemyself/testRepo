<?php
/**
 * Created by PhpStorm.
 * User: liuhengsheng
 * Date: 15/9/21
 * Time: 下午7:20
 */
require('../UserfulPhpReposity/HttpUtil.php');
function fun1(){
    $objHttpUtil = new HttpUtil();
    $result = $objHttpUtil->getHostFromHttpUrl('http://127.0.0.1/index.php');
    echo $result;
}
function fun2(){
    $regex = "/a([b]+).d/";
    $str = 'abcdxxabbbbddxxxabbbbrd';
    preg_match_all($regex, $str, $match,PREG_PATTERN_ORDER);
    print_r($match);
}

function fun3(){
    $str = '&ldsdjskds&';
    $str = substr($str,1,strlen($str)-1);
    echo $str;

}


fun3();