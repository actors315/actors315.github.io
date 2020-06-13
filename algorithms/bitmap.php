<?php


/**
 * 10G 的大文件，里在是 IPV4 地址, 一行一个，要求用 php 去重，限制使用内存大小不超过1G
 *
 * 考虑到时间复杂度
 */
ini_set("memory_limit", '512M');

echo $start = memory_get_usage(), PHP_EOL;

$phpIntSize = PHP_INT_SIZE * 8;

$handle = new SplFileObject(__DIR__ . '/../tmp/test10G.log', 'r');
$diff = new SplFileObject(__DIR__ . '/../tmp/testdiff.log', 'w+');

$existValue = 0;
while ($line = $handle->current()) {
    echo '-----------------', PHP_EOL;
    echo $begin = memory_get_usage(), PHP_EOL;

    $intValue = ip2long(trim($line));
    $offset = $intValue % $phpIntSize;
    if (($existValue & (1 << $offset)) == 0) {
        $diff->fwrite($line);
    } elseif (($existValue | (1 << $offset)) <> $existValue) {
        $diff->fwrite($line);
    }

    $existValue |= (1 << $offset);

    $handle->next();
    echo $end = memory_get_usage(), PHP_EOL;
    echo $end - $begin, PHP_EOL;
}
echo $over = memory_get_usage(), PHP_EOL;
echo $over - $start, PHP_EOL;
unset($diff, $handle);