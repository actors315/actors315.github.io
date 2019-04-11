<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/27
 * Time: 9:48
 */

namespace app\base;


use GuzzleHttp\Client;

class HttpRequest
{
    /**
     * 重试次数
     * @var int
     */
    protected $retryLimit = 3;

    /**
     * 重试等待秒数
     * @var int
     */
    protected $retryWait = 1;

    public function postJson($uri, $params = [], $baseUri = '')
    {
        $content = $this->post($uri, $params, $baseUri);
        return \GuzzleHttp\json_decode($content,true);
    }

    public function post($uri, $params = [], $baseUri = '')
    {
        if (!empty($baseUri)) $params['options']['base_uri'] = $baseUri;

        return $this->send($uri, $params, 'POST');
    }

    protected function send($uri, $param = [], $type = 'POST')
    {
        $options = [
            'connect_timeout' => 1,
            'timeout' => 3,
        ];
        if (!empty($params['options'])) {
            $options = array_merge($options, $params['options']);
        }

        $retries = 0;
        CONNECTION_RETRY: {
            try {
                $client = new Client($options);
                $param['verify'] = false;
                $request = $client->request($type, $uri, $param);
                return $response = $request->getBody()->getContents();
            } catch (\Exception $e) {
                if ($retries == $this->retryLimit) {
                    return false;
                }
                usleep($this->retryWait * 1000);
                ++$retries;
                goto CONNECTION_RETRY;
            }
        }
    }
}