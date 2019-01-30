<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
define('ROOT_PATH', dirname(__DIR__));


$list = json_decode(file_get_contents(__DIR__ . "/../blog/files/data.json"), true);

$fileList = [];
$rootPath = __DIR__ . '/../_essay/markdown/';
$handler = opendir($rootPath);
if (!empty($handler)) {
    while (($filename = readdir($handler)) !== false) {
        if ($filename != '.' && $filename != '..' && is_file($rootPath . $filename)) {
            if (substr($filename, -2) == 'md') {
                $fileList[substr($filename, 0, -3)] = filemtime($rootPath . $filename);
            }
        }
    }
}

foreach ($list as $key => $item) {
    // 清理掉已经不存在的文件
    if (!isset($fileList[$item['title']])) {
        unset($list[$key]);
    }

    unset($fileList[$item['title']]);
}

foreach ($fileList as $key => $time) {
    $sign = md5($key . 'local');
    $list[$sign] = [
        'title' => $key,
        'published' => $time,
        'key' => $sign
    ];

    $tempFile = $rootPath.$key.'.md';
    $tempTime = date('Y-m-d H:i:s',$time);
    $content = file_get_contents($tempFile);
    if (strpos($content, 'layout: post') === false) {
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($content)), 0, 100);
        file_put_contents($tempFile, '---  '.PHP_EOL);
        file_put_contents($tempFile, 'layout: post  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "title: '{$key}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "date: {$tempTime}  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, '---  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL,FILE_APPEND);

        file_put_contents($tempFile, $content, FILE_APPEND);
    }
}

file_put_contents(__DIR__ . "/../_essay/files/data.json", json_encode($list));

echo "done", PHP_EOL;

