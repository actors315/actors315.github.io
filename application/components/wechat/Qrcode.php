<?php


namespace app\components\wechat;


use Exception;

/**
 * Class Qrcode
 * @package app\components\wechat
 *
 * 先通过接口获取ticket，需要求服务号并已认证
 * 获取二维码ticket后，再用ticket换取二维码图片
 * HTTP GET请求（请使用https协议）https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET 提醒：TICKET记得进行UrlEncode
 */
class Qrcode extends Wechat
{

    const CREATE_QRCODE = 'qrcode/create';
    const ACTION_QR = 'QR_SCENE';
    const ACTION_QR_LIMIT = 'QR_LIMIT_SCENE';
    const ACTION_QR_STR = 'QR_STR_SCENE';
    const ACTION_QR_LIMIT_STR = 'QR_LIMIT_STR_SCENE';

    /**
     * 创建临时二维码
     * @param int | string $sceneId
     * @param int $expire
     * @param $type
     * @return array
     * @throws Exception
     */
    public function create($sceneId, $expire = 86400, $type = self::ACTION_QR)
    {
        $url = self::API_URL . self::CREATE_QRCODE;

        $params = [
            'expire_seconds' => $expire,
            'action_name' => $type
        ];

        if (self::ACTION_QR === $type) {
            $params['action_info']['scene']['scene_id'] = $sceneId;
        } else {
            $params['action_info']['scene']['scene_str'] = $sceneId;
        }

        return $this->postJson($url, ['json' => $params]);
    }

    /**
     * 创建永久二维码
     * @param $sceneId
     * @param string $type
     * @return array
     * @throws Exception
     */
    public function createLimit($sceneId, $type = self::ACTION_QR_LIMIT)
    {
        $url = self::API_URL . self::CREATE_QRCODE;

        $params = [
            'action_name' => $type
        ];

        if (self::ACTION_QR === $type) {
            $params['action_info']['scene']['scene_id'] = $sceneId;
        } else {
            $params['action_info']['scene']['scene_str'] = $sceneId;
        }

        return $this->postJson($url, ['json' => $params]);
    }

}