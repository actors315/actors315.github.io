<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/26
 * Time: 13:56
 */

require 'cli.php';

print_r((new \app\components\seo\baidu\Link())->submit(['https://blog.xiehuanjin.cn']));

/**

$content = file_get_contents('../tmp/travis.html');

$tempFile = '../tmp/travis.md';

$converter = new \League\HTMLToMarkdown\HtmlConverter();

file_put_contents($tempFile, $converter->convert($content), FILE_APPEND);

 */