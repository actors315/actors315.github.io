<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2019/1/10
 * Time: 0:32
 */
define('ROOT_PATH', dirname(__DIR__));

define('YOUDAO_SECRET', isset($argv[2]) ? $argv[2] : '');

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
            'key' => $sign,
        ];
        unset($list[$localSign]);
    } elseif (empty($list[$sign])) {
        $list[$sign] = [
            'title' => $entry['title'],
            'published' => strtotime($entry['published']),
            'updated' => $entry['updated'],
            'key' => $sign,
        ];
        $tempFile = __DIR__ . "/../blog/markdown/{$entry['title']}.md";
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($entry['summary'])), 0, 100);
        file_put_contents($tempFile, '---  ' . PHP_EOL);
        file_put_contents($tempFile, 'layout: post  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, 'type: blog  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "title: '{$entry['title']}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "date: {$entry['published']}  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, '---  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL, FILE_APPEND);
        $content = $filter->filterImg(trim($entry['summary']), 'blog/');
        file_put_contents($tempFile, $converter->convert($content), FILE_APPEND);
    } elseif ($entry['updated'] > $list[$sign]['updated']) {
        $tempFile = __DIR__ . "/../blog/markdown/{$entry['title']}.md";
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($entry['summary'])), 0, 100);
        file_put_contents($tempFile, '---  ' . PHP_EOL);
        file_put_contents($tempFile, 'layout: post  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, 'type: blog  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "title: '{$entry['title']}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "date: {$entry['published']}  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, '---  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL, FILE_APPEND);

        $list[$sign]['updated'] = $entry['updated'];
        $content = $filter->filterImg(trim($entry['summary']), 'blog/');
        file_put_contents($tempFile, $converter->convert($content), FILE_APPEND);
    } elseif (empty($list[$sign]['key'])) {
        $list[$sign]['key'] = $sign;
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
        'key' => $sign
    ];

    $tempFile = $rootPath . $key . '.md';
    $tempTime = date('Y-m-d H:i:s', $time);
    $content = file_get_contents($tempFile);
    if (strpos($content, 'layout: post') === false) {
        $tempDesc = mb_substr(preg_replace("/<[^>]+>/", '', trim($content)), 0, 100);
        file_put_contents($tempFile, '---  ' . PHP_EOL);
        file_put_contents($tempFile, 'layout: post  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, 'type: blog  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "title: '{$key}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "date: {$tempTime}  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "excerpt: '{$tempDesc}'  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, "key: $sign  " . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, '---  ' . PHP_EOL, FILE_APPEND);
        file_put_contents($tempFile, PHP_EOL, FILE_APPEND);

        file_put_contents($tempFile, $content, FILE_APPEND);
    }
}

$totalCount = count($list);
$totalPage = ceil($totalCount / 20);

for ($i = 1; $i <= $totalPage; $i++) {
    if ($i == 1) {
        $tempFile = __DIR__ . "/../blog/index.html";
    } else {
        $tempFile = __DIR__ . "/../blog/page{$i}/index.html";
    }

    $newPage = false;
    if (file_exists($tempFile)) {
        $tempContent = file_get_contents($tempFile);
    } else {
        $tempContent = file_get_contents(__DIR__ . "/../blog/page.html");
        $newPage = true;
        if (!is_dir($dir = dirname($tempFile))) {
            mkdir($dir, 0777, true);
        }
    }
    $tempContent = preg_replace('/page:[\s]*\d+[^\d]/', "page: {$i}" . PHP_EOL, $tempContent);
    $tempContent = preg_replace('/total_pages:[\s]*\d+[^\d]/', "total_pages: {$totalPage}" . PHP_EOL, $tempContent);
    if ($i == 2) {
        $tempContent = preg_replace('/prev_page_path:[\s]*[^\s]+[\s]*?/', "prev_page_path: /blog/", $tempContent);
    } elseif ($i > 2) {
        $prev = $i - 1;
        $tempContent = preg_replace('/prev_page_path:[\s]*[^\s]+[\s]*?/', "prev_page_path: /blog/page{$prev}/", $tempContent);
    }

    if ($i < $totalPage) {
        $next = $i + 1;
        $tempContent = preg_replace('/next_page_path:[\s]*[^\s]+[\s]*?/', "next_page_path: /blog/page{$next}/", $tempContent);
    } elseif ($i == $totalPage) {
        $tempContent = preg_replace('/next_page_path:[\s]*[^\s]+[\s]*?/', "next_page_path: none", $tempContent);
    }

    if ($newPage) {
        $tempContent = str_replace('#offset#', ($i - 1) * 20, $tempContent);
    }

    file_put_contents($tempFile, $tempContent);
}

file_put_contents(__DIR__ . "/../blog/files/data.json", json_encode($list));

echo "done", PHP_EOL;

