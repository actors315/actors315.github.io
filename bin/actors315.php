<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
define('ROOT_PATH', dirname(__DIR__));

require __DIR__ . "/../vendor/autoload.php";

$list = json_decode(file_get_contents(__DIR__ . "/../blog/files/data.json"), true);

$xml = file_get_contents('https://segmentfault.com/feeds/blog/actors315', false, stream_context_create([
    'ssl' => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ]
]));

$parse = new \app\components\XmlParse();
$tempArr = $parse->xmlToArray($xml);
$entryList = $tempArr['entry'];

$converter = new \League\HTMLToMarkdown\HtmlConverter();
$filter = new \app\components\SFArticle();

foreach ($entryList as $entry) {
    $entry['id'] = str_replace('https://segmentfault.com/a/', '', $entry['id']);
    $entry['updated'] = strtotime($entry['updated']);

    $sign = md5($entry['title'] . $entry['id']);
    $localSign = md5($entry['title'] . 'local');

    if (isset($list[$localSign])) {
        $list[$sign] = [
            'title' => $entry['title'],
            'published' => strtotime($entry['published']),
            'updated' => $list[$localSign]['updated'],
        ];
        unset($list[$localSign]);
    } elseif (empty($list[$sign])) {
        $list[$sign] = [
            'title' => $entry['title'],
            'published' => strtotime($entry['published']),
            'updated' => $entry['updated'],
        ];
        $tempFile = __DIR__ . "/../blog/markdown/{$entry['title']}.md";
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($entry['summary'])), 0, 100);
        file_put_contents($tempFile, '---  '.PHP_EOL);
        file_put_contents($tempFile, 'layout: post  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "title: '{$entry['title']}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "date: {$entry['published']}  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, '---  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL,FILE_APPEND);
        $content = $filter->filterImg(trim($entry['summary']),'blog/');
        file_put_contents($tempFile, $converter->convert($content),FILE_APPEND);
    } elseif ($entry['updated'] > $list[$sign]['updated']) {
        $tempFile = __DIR__ . "/../blog/markdown/{$entry['title']}.md";
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($entry['summary'])), 0, 100);
        file_put_contents($tempFile, '---  '.PHP_EOL);
        file_put_contents($tempFile, 'layout: post  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "title: '{$entry['title']}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "date: {$entry['published']}  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  ".PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, '---  '.PHP_EOL,FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL,FILE_APPEND);

        $list[$sign]['updated'] = $entry['updated'];
        $content = $filter->filterImg(trim($entry['summary']),'blog/');
        file_put_contents($tempFile, $converter->convert($content),FILE_APPEND);
    }
}

$fileList = [];
$rootPath = __DIR__ . '/../blog/markdown/';
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
        'updated' => $time,
    ];
}

$published = array_column($list, 'published');
array_multisort($published, SORT_DESC, $list);

file_put_contents(__DIR__ . "/../blog/files/data.json", json_encode($list));

// 写目录
$file = __DIR__ . "/../blog/README.md";
file_put_contents($file, "# 我的博客  ");
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);
file_put_contents($file, '同步自segmentfault(https://segmentfault.com/blog/actors315)  ', FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

file_put_contents($file, "## 目录  ", FILE_APPEND);
file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);
foreach ($list as $item) {
    file_put_contents($file, "- [{$item['title']}](/markdown/" . $item['title'] . ".md)", FILE_APPEND);
    file_put_contents($file, PHP_EOL, FILE_APPEND);
}

echo "done", PHP_EOL;

