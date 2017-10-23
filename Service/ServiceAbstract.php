<?php
Class ServiceAbstract
{
 public function sendData($url, $params=array())
    {
        $result = array();
        try {
           
 
                $result = $this->_curl_send($url, $params);
 
    
            return $result;
        } catch (Exception $e) {
            $result['resultCode'] ='9999';
            $result['resultMsg'] = $e->getMessage();
            $result['detailMsg'] = 'Exception';
            $result['resultData'] = array();
            return $result;
        }
    }


    public function send($url, $params=array())
    {

        try {
            $try_count = 0;
            do {
                $try_count++;
                $result = $this->_curl_send($url, $params);
                if ($result['curl_errno'] == CURLE_OK || $try_count >=3)
                {
                    if ($result['curl_errno'] == CURLE_OK)
                        return $result['response'];

                    $result['resultCode'] ='9998';
                    $result['resultMsg'] = 'timeout';
                    return $result;

                }
            }while($try_count < 3);
            $result['resultCode'] ='9999';
            $result['resultMsg'] = 'timeout';
            return $result;
        } catch (Exception $e) {
            $result = array();
            $result['resultCode'] ='9999';
            $result['resultMsg'] = $e->getMessage();
            return $result;
        }
    }

    private function _curl_send($url, $params=array())
    {
 
		$this->_api_host = 'https://api.github.com';
        $url = $this->_api_host.$url;
        $url.='&';
        foreach($params as $key=>$param){
            $url.=$key.'='.urlencode($param).'&';
        }
        $url .= (count($params)>0?'&':'');
        $url = rtrim($url, '\&');
        $ch = curl_init();
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
        $myfile = file_put_contents('logs.txt', $url.PHP_EOL , FILE_APPEND | LOCK_EX);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
 
        unset($result);
        $result['response'] = curl_exec($ch);
        $result['curl_errno'] = curl_errno($ch);
        curl_close($ch);
        return $result;
    }
}

