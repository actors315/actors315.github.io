<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/2/27
 * Time: 3:45
 */

namespace app\components;


class TranslateByYoudao
{

    const API_URL = 'https://openapi.youdao.com/api';
    const APP_KEY = '32c97dfa2b5ebc4a';

    /**
     * 中文转英文
     *
     * @param $text
     */
    public function zh2en($text)
    {
        $salt = uniqid();
        $nowTime = time();
        $input = $text;
        if (($len = strlen($text)) > 20) {
            $input = substr($text, 0, 10) . $len . substr($text, -10);
        }

        $data = [
            'q' => $text,
            'from' => 'zh-CHS',
            'to' => 'en',
            'appKey' => self::APP_KEY,
            'salt' => $salt,
            'sign' => hash('sha256', self::APP_KEY . $input . $salt . $nowTime . YOUDAO_SECRET), // 签名信息，sha256(appKey+input+salt+curtime+密钥)
            'signType' => 'v3',
            'curtime' => $nowTime,
        ];
        
    }

}