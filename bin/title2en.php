<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/27
 * Time: 9:58
 */
require 'cli.php';

$list = json_decode(file_get_contents(__DIR__ . "/../_posts/files/data.json"), true);

$translator = new \app\components\TranslateByYoudao();
foreach ($list as $key => $value) {
    preg_match('/^\d{4}-\d{2}-\d{2}-/', $value['title'], $match);
    $title = str_replace($match[0], '', $value['title']);
    if ($titleEn = $translator->zh2en($title)) {
        $titleEn = strtolower(trim($titleEn));
        $titleEn = str_replace('&', ' ', $titleEn);
        $titleEn = str_replace("'", ' ', $titleEn);
        $titleEn = preg_replace('/\s+/', ' ', $titleEn);
        $titleEn = str_replace(' ', '-', $titleEn);
        $list[$key]['filename'] = $match[0] . $titleEn;
    }
}

print_r($list);