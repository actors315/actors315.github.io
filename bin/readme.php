<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
define('ROOT_PATH', dirname(__DIR__));

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
file_put_contents($fileData,'');

foreach ($list as $key => $item) {
    file_put_contents($file, "- [{$item['title']}](/blog/markdown/" . str_replace(' ', '%20', $item['title']) . ".md)", FILE_APPEND);
    file_put_contents($file, PHP_EOL, FILE_APPEND);

    file_put_contents($fileData, " - key: {$item['key']}" . PHP_EOL, FILE_APPEND);
    file_put_contents($fileData, "   title: {$item['title']}" . PHP_EOL, FILE_APPEND);
    file_put_contents($fileData, "   path: ./markdown/" . PHP_EOL, FILE_APPEND);
    if (!empty($list[$key-1])) {
        file_put_contents($fileData, "   prev: {$list[$key-1]['title']}" . PHP_EOL, FILE_APPEND);
    }
    if (!empty($list[$key+1])) {
        file_put_contents($fileData, "   next: {$list[$key+1]['title']}" . PHP_EOL, FILE_APPEND);
    }
}

file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 心情随笔  ", FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 目录  ", FILE_APPEND);
file_put_contents($file, PHP_EOL, FILE_APPEND);

$list = json_decode(file_get_contents(__DIR__ . "/../essay/files/data.json"), true);
$list = array_values($list);
$published = array_column($list, 'published');
array_multisort($published, SORT_DESC, $list);


foreach ($list as $item) {
    file_put_contents($file, "- [{$item['title']}](/essay/markdown/" . str_replace(' ','%20',$item['title']) . ".md)", FILE_APPEND);
    file_put_contents($file, PHP_EOL, FILE_APPEND);
}


echo "done", PHP_EOL;

