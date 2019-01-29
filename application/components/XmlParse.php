<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:22
 */

namespace app\components;


class XmlParse
{

    public function loadXml($xml)
    {
        return simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    public function xmlToArray($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($data), true);
    }

}