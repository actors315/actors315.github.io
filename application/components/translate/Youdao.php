<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/2/27
 * Time: 3:45
 */

namespace app\components\translate;


use app\base\HttpRequest;

class Youdao extends HttpRequest
{

    const API_URL = 'https://openapi.youdao.com';
    const API_URI = '/api';
    const APP_KEY = '32c97dfa2b5ebc4a';

    /**
     * 中文转英文
     *
     * @param $text
     * @return string | false
     */
    public function zh2en($text)
    {
        $salt = uniqid();
        $nowTime = time();
        $input = $text;
        if (($len = mb_strlen($text)) > 20) {
            $input = mb_substr($text, 0, 10) . $len . mb_substr($text, $len - 10, $len);
        }

        $param = [
            'q' => $text,
            'from' => 'zh-CHS',
            'to' => 'en',
            'appKey' => self::APP_KEY,
            'salt' => $salt,
            'sign' => hash('sha256', self::APP_KEY . $input . $salt . $nowTime . YOUDAO_SECRET), // 签名信息，sha256(appKey+input+salt+curtime+密钥)
            'signType' => 'v3',
            'curtime' => $nowTime,
        ];
        $response = $this->postJson(self::API_URI, ['form_params' => $param], self::API_URL);
        if (0 == $response['errorCode']) {
            return array_shift($response['translation']);
        }

        return false;
    }

}