<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 22:14
 */

namespace app\components;


use GuzzleHttp\Client;

class SFArticle
{

    const BASE_URL = "https://segmentfault.com";

    public function filterImg($content,$dir = '')
    {
        $preg = '/<img.+src=[\'"](.+?)[\'"]/';
        preg_match_all($preg, $content, $matches);

        if (!empty($matches[1])) {
            $replace = [];
            foreach ($matches[1] as $url) {
                if ($filename = $this->download($url,$dir)) {
                    $replace[$url] = str_replace(ROOT_PATH, '', $filename);
                }
            }
            if (!empty($replace)) {
                $content = str_replace(array_keys($replace), $replace, $content);
            }
        }

        return $content;
    }

    protected function download($url, $dir = '')
    {
        try {
            $url = self::BASE_URL . $url;

            $filename = md5(pathinfo($url, PATHINFO_FILENAME));

            $client = new Client();
            $res = $client->request('GET', $url);
            $type = $res->getHeader('Content-Type');
            $ext = $this->getImgType($type);
            $filename = ROOT_PATH . "/{$dir}files/images/" . $filename . $ext;
            $fp = fopen($filename, 'a');
            fwrite($fp, $res->getBody());
            fclose($fp);
            return $filename;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getImgType($type)
    {
        $ext = '';
        if (isset($type[0])) {
            switch ($type[0]) {
                case 'image/png':
                    $ext = '.png';
                    break;
                case 'image/jpeg':
                    $ext = '.jpg';
                    break;
            }
        }

        return $ext;
    }

}