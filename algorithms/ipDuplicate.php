<?php

/**
 * 10G 的大文件，里在是 IPV4 地址, 一行一个，要求用 php 去重，限制使用内存大小不超过1G
 *
 * 不考虑时是复杂度的情况下，可以用极少的内存
 */
ini_set("memory_limit", '1M');

/**
 * $handle = new SplFileObject(__DIR__ . '/../tmp/10G.log', 'w');
 * $handle->fwrite(long2ip(190) . PHP_EOL);
 * $handle->fwrite(long2ip(191) . PHP_EOL);
 * for ($i = 1; $i < 1000000000; $i++) {
 * $handle->fwrite(long2ip($i) . PHP_EOL);
 * }
 * unset($handle);
 */

echo $start = memory_get_usage(), PHP_EOL;
$handle = new SplFileObject(__DIR__ . '/../tmp/10G.log', 'r');
$diff = new SplFileObject(__DIR__ . '/../tmp/diff.log', 'w+');

while ($line = $handle->current()) {
    echo '-----------------', PHP_EOL;
    echo $begin = memory_get_usage(), PHP_EOL;
    $diff->rewind();
    $first = false;
    while (!$diff->eof()) {
        $checkLine = $diff->current();
        if (trim($checkLine) == trim($line)) {
            $first = true;
            break;
        }
        $diff->next();
    }
    if (!$first) {
        $diff->fseek(0, SEEK_END);
        $diff->fwrite($line);
    }
    $handle->next();
    echo $end = memory_get_usage(), PHP_EOL;
    echo $end - $begin, PHP_EOL;
}
echo $over = memory_get_usage(), PHP_EOL;
echo $over - $start, PHP_EOL;
unset($diff,$handle);