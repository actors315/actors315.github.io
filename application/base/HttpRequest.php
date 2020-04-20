<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/27
 * Time: 9:48
 */

namespace app\base;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
    protected $retryWait = 0.5;

    /**
     * @param $uri
     * @param array $params
     * @param array $options 例 ['base_uri' => 'https://graph.facebook.com/']
     * @return mixed
     */
    public function getJson($uri, $params = [], $options = [])
    {
        $content = $this->get($uri, $params, $options);
        return \GuzzleHttp\json_decode($content, true);
    }

    public function get($uri, $params = [], $options = [])
    {
        return $this->send($uri, $params, 'GET', $options);
    }

    public function postJson($uri, $params = [], $options = [])
    {
        $content = $this->post($uri, $params, $options);
        return \GuzzleHttp\json_decode($content, true);
    }

    public function post($uri, $params = [], $options = [])
    {
        return $this->send($uri, $params, 'POST', $options);
    }

    /**
     * @param $uri
     * @param array $params
     * @param string $type
     * @param array $options
     * @return string
     * @throws Exception
     * @throws GuzzleException
     */
    protected function send($uri, $params = [], $type = 'POST', $options = [])
    {
        $defaultOptions = [
            'connect_timeout' => 1,
            'timeout' => 3,
        ];
        $options = array_merge($defaultOptions, $options);

        $defaultParams = ['verify' => false];
        $params = array_merge($defaultParams,$params);
        $client = new Client($options);

        $retries = 0;
        CONNECTION_RETRY: {
            try {
                $request = $client->request($type, $uri, $params);
                $response = $request->getBody();
                return $response->getContents();
            } catch (Exception $e) {
                if ($retries == $this->retryLimit) {
                    throw new Exception($e);
                }
                usleep($this->retryWait * 1000000);
                ++$retries;
                goto CONNECTION_RETRY;
            }
        }
    }
}