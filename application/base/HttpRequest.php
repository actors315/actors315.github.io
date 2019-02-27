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

    protected function send($uri, $param = [], $baseUri = '')
    {
        if (strpos($baseUri, 'http') !== 0) {
            $baseUri = 'http://' . $baseUri;
        }

        $retries = 0;
        CONNECTION_RETRY: {
            try {
                $client = new Client([
                    'base_uri' => $baseUri,
                    'connect_timeout' => 5,
                    'timeout' => 15,
                ]);
                $param['verify'] = false;
                $request = $client->request('POST', $uri, $param);
                $response = $request->getBody();
                return \GuzzleHttp\json_decode($response, true);
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