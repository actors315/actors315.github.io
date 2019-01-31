<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
define('ROOT_PATH', dirname(__DIR__));


$list = json_decode(file_get_contents(__DIR__ . "/../_posts/files/data.json"), true);

$fileList = [];
$rootPath = __DIR__ . '/../_posts/';
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

    $tempFile = $rootPath . $key . '.md';
    $tempTime = date('Y-m-d H:i:s', $time);
    $content = trim(file_get_contents($tempFile));
    if (strpos($content, 'layout: post') === false) {

        file_put_contents($tempFile, '---  ' . PHP_EOL);
        file_put_contents($tempFile, 'layout: post  ' . PHP_EOL, FILE_APPEND);

        // 标题
        preg_match('/---[\s\S]*title:[\s]*([^\s]+)[\s\S]*---/', $content, $match);
        if(!empty($match[1])) {
            $title = trim($match[1],"'");
        } else {
            $title = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $key);
        }
        file_put_contents($tempFile, "title: '{$title}'  " . PHP_EOL, FILE_APPEND);

        // 作者
        preg_match('/---[\s\S]*author:[\s]*([^\s]+)[\s\S]*---/', $content, $match);
        if(!empty($match[1])) {
            $match[1] = trim($match[1],"'");
            file_put_contents($tempFile, "author: '{$match[1]}'  " . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($tempFile, "date: {$tempTime}  " . PHP_EOL, FILE_APPEND);

        $content = trim(preg_replace('/---[\s\S]+---/','',$content));
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', $content), 0, 100);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, '---  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL, FILE_APPEND);


        file_put_contents($tempFile, $content, FILE_APPEND);
    }
}

file_put_contents(__DIR__ . "/../_posts/files/data.json", json_encode($list));

echo "done", PHP_EOL;

