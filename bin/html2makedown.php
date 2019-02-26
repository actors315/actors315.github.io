<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/26
 * Time: 13:56
 */

require __DIR__ . "/../vendor/autoload.php";

$content = file_get_contents('../tmp/travis.html');

$tempFile = '../tmp/travis.md';

$converter = new \League\HTMLToMarkdown\HtmlConverter();

file_put_contents($tempFile, $converter->convert($content), FILE_APPEND);