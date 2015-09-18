<?php
/**
 * Created by PhpStorm.
 * User: liuhengsheng
 * Date: 15/9/18
 * Time: 下午3:47
 */
class MyTestClass{
    public function main(){
        echo "just test for github";
    }

    public function fun1(){
        $var = 'hello ,man';
        curl_multi_setopt($var,$var,$var);
    }
}

$objTestClasss = new MyTestClass();
$objTestClasss->fun1();