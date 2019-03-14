<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
require 'cli.php';

// 写目录
$file = __DIR__ . "/../README.md";
file_put_contents($file, "# 我的博客  ");
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, '[![Build Status](https://travis-ci.org/actors315/actors315.github.io.svg?branch=master)](https://travis-ci.org/actors315/actors315.github.io)  ', FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 呜啦啦的碎碎念  ", FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, '同步自segmentfault(https://segmentfault.com/blog/actors315)  ', FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 目录  ", FILE_APPEND);
file_put_contents($file, PHP_EOL, FILE_APPEND);

$list = json_decode(file_get_contents(__DIR__ . "/../blog/files/data.json"), true);
$list = array_values($list);
$published = array_column($list, 'published');
array_multisort($published, SORT_DESC, $list);

$fileData = __DIR__ . "/../_data/blogList.yml";
file_put_contents($fileData, '');

$tempCount = 0;
foreach ($list as $key => $item) {
    file_put_contents($file, "- [{$item['title']}](/blog/markdown/{$item['filename']})", FILE_APPEND);
    file_put_contents($file, PHP_EOL, FILE_APPEND);

    file_put_contents($fileData, " - key: {$item['key']}" . PHP_EOL, FILE_APPEND);
    file_put_contents($fileData, "   title: {$item['title']}" . PHP_EOL, FILE_APPEND);
    file_put_contents($fileData, "   filename: " . substr($item['filename'], 0, -3) . PHP_EOL, FILE_APPEND);
    if (!empty($list[$key - 1])) {
        file_put_contents($fileData, "   prev: " . PHP_EOL, FILE_APPEND);
        file_put_contents($fileData, "      title: {$list[$key-1]['title']}" . PHP_EOL, FILE_APPEND);
        file_put_contents($fileData, "      filename: " . substr($list[$key - 1]['filename'], 0, -3) . PHP_EOL, FILE_APPEND);
    }
    if (!empty($list[$key + 1])) {
        file_put_contents($fileData, "   next: " . PHP_EOL, FILE_APPEND);
        file_put_contents($fileData, "      title: {$list[$key+1]['title']}" . PHP_EOL, FILE_APPEND);
        file_put_contents($fileData, "      filename: " . substr($list[$key + 1]['filename'], 0, -3) . PHP_EOL, FILE_APPEND);
    }
    if(++$tempCount >=20) {
        break;
    }
}

file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 心情随笔  ", FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 目录  ", FILE_APPEND);
file_put_contents($file, PHP_EOL, FILE_APPEND);

$list = json_decode(file_get_contents(__DIR__ . "/../_posts/files/data.json"), true);
$list = array_values($list);
$published = array_column($list, 'published');
array_multisort($published, SORT_DESC, $list);

$tempCount = 0;
foreach ($list as $key => $item) {
    $title = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $item['title']);
    file_put_contents($file, "- [{$title}](/_posts/{$item['filename']})" . PHP_EOL, FILE_APPEND);
    if(++$tempCount >=20) {
        break;
    }
}


echo "done", PHP_EOL;

