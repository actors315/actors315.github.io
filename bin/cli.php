<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/27
 * Time: 10:05
 */
define('ROOT_PATH', dirname(__DIR__));
define('YOUDAO_SECRET', isset($_SERVER['YOUDAO_SECRET']) ? $_SERVER['YOUDAO_SECRET'] : '');

require __DIR__ . "/../vendor/autoload.php";