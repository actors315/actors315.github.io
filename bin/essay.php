<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
require 'cli.php';

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
    if (!empty($item['filename'])) {
        $filename = substr($item['filename'], 0, -3);
        if (!file_exists(ROOT_PATH . '/_posts/' . $item['filename'])) {
            unset($list[$key]);
        }
    } else {
        $filename = $item['title'];
        if (!isset($fileList[$filename])) {
            unset($list[$key]);
        }
    }
    unset($fileList[$filename]);
}

$filenameTransfer = new \app\components\Filename();

foreach ($fileList as $key => $time) {
    $sign = md5($key . 'local');
    preg_match('/^\d{4}-\d{2}-\d{2}/', $key, $match);
    $list[$sign] = [
        'title' => $key,
        'published' => $time,
        'key' => $sign
    ];

    $tempFile = $rootPath . $key . '.md';
    $tempTime = date('Y-m-d H:i:s', $time);
    $content = trim(file_get_contents($tempFile));

    $transName = false;
    if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $key)) {
        if ($filename = $filenameTransfer->generateUrl($key)) {
            unlink($tempFile);
            $tempFile = $rootPath . $filename . '.md';
            $transName = true;
        }
    }
    $list[$sign]['filename'] = str_replace($rootPath, '', $tempFile);

    if (strpos($content, 'layout: post') === false) {
        file_put_contents($tempFile, '---  ' . PHP_EOL);
        file_put_contents($tempFile, 'layout: post  ' . PHP_EOL, FILE_APPEND);

        // 标题
        preg_match('/---[\s\S]*title:[\s]*([^\s]+)[\s\S]*---/', $content, $match);
        if (!empty($match[1])) {
            $title = trim($match[1], "'");
        } else {
            $title = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $key);
        }
        file_put_contents($tempFile, "title: '{$title}'  " . PHP_EOL, FILE_APPEND);

        // 作者
        preg_match('/---[\s\S]*author:[\s]*([^\s]+)[\s\S]*---/', $content, $match);
        if (!empty($match[1])) {
            $match[1] = trim($match[1], "'");
            file_put_contents($tempFile, "author: '{$match[1]}'  " . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($tempFile, "date: {$tempTime}  " . PHP_EOL, FILE_APPEND);

        $content = trim(preg_replace('/---[\s\S]+---/', '', $content));
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', $content), 0, 100);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, '---  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL, FILE_APPEND);

        file_put_contents($tempFile, $content, FILE_APPEND);
    } elseif ($transName) {
        file_put_contents($tempFile, $content);
    }
}

foreach ($list as $key => $item) {
    $tempFile = $rootPath . $item['title'] . '.md';
    if (empty($item['filename']) || preg_match("/[\x{4e00}-\x{9fa5}]+/u", $item['filename'])) {
        if ($filename = $filenameTransfer->generateUrl($item['title'])) {
            $content = trim(file_get_contents($tempFile));
            unlink($tempFile);
            $tempFile = $rootPath . $filename . '.md';
            file_put_contents($tempFile, $content, FILE_APPEND);
            $list[$key]['filename'] = str_replace($rootPath, '', $tempFile);
        }
    }
}

file_put_contents(__DIR__ . "/../_posts/files/data.json", json_encode($list));

echo "done", PHP_EOL;

