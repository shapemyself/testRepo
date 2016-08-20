<?php
/**
 * Created by PhpStorm.
 * User: liuhengsheng
 * Date: 15/9/19
 * Time: 下午11:23
 */

class TestMain{

    const TYPE_GET = 1;
    const TYPE_POST = 2;
    const BASE_DIR = '/Users/baidu/Downloads/tmp.dir';

    /**
     * @param string $url
     * @param int $requestType :请求的类型:1->get请求,2->post请求
     * @param array $postArray
     */
    public function requestUrl($url='',$requestType=0, $postArray=array()){
        $useAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36';
        $cookie = '360docArtPageBackGroundColor=mainbj6; bdshare_firstime=1442312902569; doctaobaocookie=1; Hm_lvt_d86954201130d615136257dde062a503=1442312903,1442678902; Hm_lpvt_d86954201130d615136257dde062a503=1442681513';
        $referInfo = 'http://www.360doc.com/content/15';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //不输出返回的结果
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ( !empty($requestType) && $requestType == self::TYPE_POST ){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postArray);
        }
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $useAgent);
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_REFERER, $referInfo);
        curl_exec($curl);
        if ( curl_errno($curl) ){
            echo 'url请求出现错误,错误信息:'.curl_errno($curl);
        }
        curl_close($curl);
    }

}

$objTestMain = new TestMain();
//$objTestMain->requestUrl('http://www.360doc.com/content/15/0918/21/12146850_499980507.shtml',TestMain::TYPE_GET);
for($i=0; $i<100; $i++){
    $objTestMain->requestUrl('http://www.360doc.com/content/15/0918/21/12146850_499980507.shtml',TestMain::TYPE_GET);
}


