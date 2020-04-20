<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/4/11
 * Time: 14:17
 */

namespace app\components\seo\baidu;


use app\base\HttpRequest;

class Link extends HttpRequest
{

    const SUBMIT_URL = 'http://data.zz.baidu.com/urls?site=blog.xiehuanjin.cn&token=';

    /**
     * 链接提交，主动推送
     *
     * @param $urlList
     * @return string
     */
    public function submit($urlList)
    {
        $url = self::SUBMIT_URL . BAIDU_TOKEN;

        return $this->postJson($url, ['body' => implode("\n", $urlList)],[
            'connect_timeout' => 5,
            'timeout' => 5,
        ]);
    }

}