<?php
/**
 * Created by PhpStorm.
 * User: liuhengsheng
 * Date: 15/9/18
 * Time: 下午5:11
 */

class HttpUtil{

    /**
     * bref:使用php实现一个post请求
     * @param $paramentArray post请求中参数数组
     * @param $uri:post请求的参数之前的部分url
     * @return 返回请求的响应结果
     */
    public function postReuest($paramentArray, $uri){
        if ( !is_array($paramentArray) ){
            throw new Exception('参数类型有误',-1);
        }
        foreach($paramentArray as $key => $value ){
            $data[$key] = $value;
        }
        $data = http_build_query($data);
        $opts = array (
            'http' => array (
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencodedrn" .
                    "Content-Length: " . strlen($data) . "rn",
                'content' => $data
            )
        );
        $context = stream_context_create($opts);
        $html = file_get_contents($uri, false, $context);
        return $html;
    }

    /**
     * @param $uri
     * @param $postArray
     * @bref:使用curl_setopt()函数进行模仿post请求
     */
//    public function urlRequestByCurl($uri, $postArray) {
//        $ch =  curl_init();
//        curl_setopt($ch, CURLOPT_URL, $uri);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
//        $result = curl_exec($ch);
//    }

    /**
     * @bref:模拟登陆请求
     * @param string $url
     * @param array $postArray
     * @param string $cookie
     * @param string $cookieJar
     * @param string $refer
     * @return mixed
     */
     public function simulateLogin($url='',
                                   $postArray=array(),
                                   $cookie='',
                                   $cookieJar='',
                                   $refer=''){
         $postArrayToStr = '';
         if (is_array($postArray) && count($postArray)>0 ){
             foreach($postArray as $key => $value){
                 $postArrayToStr .= urlencode($key). ' = '.$value.'&';
             }
             $postArrayToStr = substr($postArrayToStr, 0, strlen($postArrayToStr)-1);
         }
         $cookiePath = getcwd().'/'.$cookieJar;    //getcwd()取得当前的工作目录
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
         if ( !empty($refer) ){
             curl_setopt($curl, CURLOPT_REFERER, $refer);
         }else{
             curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
         }
         if ( is_array($postArray) && count($postArray)>0 ){
             curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_POSTFIELDS, $postArrayToStr);
         }
         if ( !empty($cookie) ){
             curl_setopt($curl, CURLOPT_COOKIE, $cookie);
         }
         if ( !empty($cookieJar) ){
             curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieJar);
             curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiePath);
         }
         curl_setopt($curl, CURLOPT_TIMEOUT, 100);
         curl_setopt($curl, CURLOPT_HEADER,0);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         $resultInfo = curl_exec($curl);
         if ( curl_errno($curl) ){
             echo 'url请求出现错误,错误信息:'.curl_errno($curl);
         }
         curl_close($curl);
         return $resultInfo;
     }

    /**
     * @bref: 获取url中的主机域名
     * @param string $httpUrl
     * @return string
     * @throws Exception
     */
     public function getHostFromHttpUrl($httpUrl=''){
         $regex = "/^(http:\/\/)(.*?)\//i";
         $result = preg_match($regex, $httpUrl, $matchArray);
         if ( $result === 0 ){
             throw new Exception('正则匹配失败,传入的url非法', -1);
         }
         $tmpHostInfo = $matchArray[2];
         $tmpArray = explode(':', $tmpHostInfo);  //去掉可能存在的端口
         if ( strpos($tmpArray[0],'www') !== false ){
             $hostArray = explode('.', $tmpArray[0]);
             unset($hostArray[0]);//去掉www
             $host = implode('.',$hostArray);
         }else{
             $host = $tmpArray[0];
         }
         return $host;
     }




}
