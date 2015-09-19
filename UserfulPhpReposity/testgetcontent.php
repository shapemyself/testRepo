<?php
	$url = 'http://www.360doc.com/content/15/0918/21/12146850_499980507.shtml#';
	$request = new WebRequest($url);
	echo '<pre>';
	$obj = $request->getResponse();
	//var_dump($obj);
//	echo $obj->content;
	
	class WebRequest {
	    var $url        = '';
	    var $host        = '';
	    var $port        = 80;
	    var $path        = '/';
	    var $method        = '';
	    var $postdata    = '';
	    var $cookies    = array(
	    );
	    var $accept                = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
	    var $accept_language    = 'zh-cn';
	    var $accept_encoding    = 'gzip';
//	    var $user_agent            = 'ErikClient/1.0';
	    var $user_agent ='Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0';
	    var $use_gzip            = false;
	
	    var $username;
	    var $password;
	
	    var $timeout = 20;
	
	    /**
	    * 构造函数
	    * @public
	    * @param <string> $url 请求地址
	    * @param <string> $method 请求方式
	    * @param <string> $postData post的数据。$method为非post时该参数无效
	    */
	    function WebRequest ($url, $method = 'get', $postData = '') {
	        $this->url = $url;
	        $this->method = $method;
	        $this->postdata = $postData;
			$this->cookies = array(
	    	//'_ga' => 'GA1.3.1713896726.1377767028',
	    	//'PHPSESSID' => 'uvcgsls9c8o5adoj61k31i6o06',		
	  		//'9af09_c_stamp' => $_COOKIE['9af09_c_stamp'],    	
	  		//'9af09_lastvisit' => $_COOKIE['9af09_lastvisit'],
	    	'9af09_c_stamp' => time(),
			'9af09_lastvisit' => rand(1000, 9999).'%091378703291%09%2Fmode.php%3Fmarea%26qapi%26typedata%26id153',
	    	'9af09_lastpos' => 'bbs'
	    	);
	        $urlPattern = "/^http:\/\/([^\/]+)(\/.*)?/i";
	
	        if (preg_match($urlPattern, $url, $urlArr)) {
	            $hostStr = $urlArr[1];
	            
	            $hosts = preg_split("/:/i", $hostStr);
	            $this->host = $hosts[0];
	            
	            if (count($hosts) > 1) {
	                $this->port = $hosts[1];
	            }
	            if (count($urlArr) > 2) {
	                $this->path = $urlArr[2];
	            }
	        } else {
	        }
	    }
	
	    /**
	    * 获取请求的响应
	    * @public
	    * @return <WebResponse> http请求的响应信息
	    */
	    public function getResponse () {

	        if (!$fp = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout)) {
	            switch($errno) {
	                case -3:
	                    $this->errormsg = 'Socket连接创建失败 (-3)';
	                    break;
	                case -4:
	                    $this->errormsg = 'DNS定位失败 (-4)';
	                    break;
	                case -5:
	                    $this->errormsg = '连接超时或被拒绝 (-5)';
	                    break;
	                default:
	                    $this->errormsg = '连接失败 ('.$errno.')';
	                    break;
	                $this->errormsg .= ' '.$errstr;
	            }
	            return false;
	        }else{

			}
	        socket_set_timeout($fp, $this->timeout);
	
	        $request = $this->buildRequestInfo();
	        fwrite($fp, $request);
	
	        $content = '';
	        $readState = 'start';
	        $response = new WebResponse();
	        while (!feof($fp)) {
	            $line = fgets($fp, 4096);
	            if ($readState == 'start') {
	                $readState = 'header';
	                if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
	                    $this->errormsg = "非法的请求状态: " . htmlentities($line);
	                    return false;
	                }
	                $http_version = $m[1]; //未使用
	                $response->setStatus($m[2]);
	                $status_string = $m[3]; //未使用
	            } else if ($readState == 'header') {
	                if (trim($line) == '') {
	                    $readState = 'content';
	                }
	
	                if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
	                    continue;
	                }
	
	                $key = strtolower(trim($m[1]));
	                $val = trim($m[2]);
	                $response->appendHeader($key, $val);
	            } else {
	                $content .= $line;
	            }
	        }
	        fclose($fp);
	        $response->setContent($content);
	
	        return $response;
	    }
	
	    /**
	    * 构造向socket发送的请求信息
	    * @private
	    * @return <string> request信息
	    */
	    private function buildRequestInfo () {
	        $headers = array();
	        $method = strtoupper($this->method);
	        if ($method != 'POST') $method = 'GET';
	
	        $headers[] = "{$method} {$this->path} HTTP/1.0";
	        $headers[] = "Host: {$this->host}";
	        $headers[] = "User-Agent: {$this->user_agent}";
	        $headers[] = "Accept: {$this->accept}";
	        if ($this->use_gzip) {
	            $headers[] = "Accept-Encoding: {$this->accept_encoding}";
	        }
	        $headers[] = "Accept-Language: {$this->accept_language}";
	
	        // Cookies
	        if ($this->cookies) {
	            $cookie = 'Cookie: ';
	            foreach ($this->cookies as $key => $value) {
	                $cookie .= "$key=$value; ";
	            }
	            $headers[] = $cookie;
	        }
	
	        // authentication
	        if ($this->username && $this->password) {
	            $headers[] = 'Authorization: BASIC ' . base64_encode($this->username.':'.$this->password);
	        }
	
	        // 如果是POST方式, 设置content type和length头
	        if ($method == 'POST') {
	            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
	            $headers[] = 'Content-Length: ' . strlen($this->postdata);
	        }
	
	        $request = implode("\r\n", $headers) . "\r\n\r\n" . $this->postdata;
	        return $request;
	    }
	
	    /**
	    * 设置cookies
	    * @public
	    * @param <array> $array cookies
	    */
	    public function setCookies($array) {
	        $this->cookies = $array;
	    }
	}

/**
* http响应类
*/
class WebResponse {
    var $status;
    var $headers = array();
    var $content;

    function WebResponse () {
    }

    /**
    * 状态码设置
    * @public
    * @param <string> $sta 状态码
    */
    public function setStatus ($sta) {
        $this->status = $sta;
    }

    /**
    * 添加http头信息
    * @public
    * @param <string> $ke http头键
    * @param <string> $value http头值
    */
    public function appendHeader ($ke, $value) {
        if (isset($this->headers[$ke])) {
            if (is_array($this->headers[$ke])) {
                $this->headers[$ke][] = $value;
            } else {
                $this->headers[$ke] = array($this->headers[$ke], $value);
            }
        } else {
            $this->headers[$ke] = $value;
        }
    }

    /**
    * 设置response的内容
    * @public
    * @param <string> $content response的内容
    */
    public function setContent ($content) {
        $this->content = $content;
    }

    /**
    * 获取response的内容
    * @public
    * @return <string> response的内容
    */
    public function getContent () {
        return $this->content;
    }

    /**
    * 获取response headers
    * @public
    * @return <array> response的headers
    */
    public function getHeaders () {
        return $this->headers;
    }
}

	
?>