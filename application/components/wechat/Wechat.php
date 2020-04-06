<?php


namespace app\components\wechat;


use app\base\HttpRequest;

class Wechat extends HttpRequest
{
    const API_URL = 'https://api.weixin.qq.com/cgi-bin/';

    const ACCESS_TOKEN_URI = 'token';

    protected $appId;
    protected $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getAccessToken()
    {
        $url = self::API_URL . self::ACCESS_TOKEN_URI;

        return $this->getJson($url, ['form_params' => [
            'grant_type' => 'client_credential',
            'appid' => $this->appId,
            'secret' => $this->appSecret
        ]]);
    }
}