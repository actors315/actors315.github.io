<?php
ini_set('memory_limit', '512M');
/**
 * 递归的方式,空间最优，时间复杂度 O(n * log(n)),空间复杂度 O(1)
 * @param $arr
 * @param $start
 * @param $end
 */
function quickSort(&$arr, $start, $end)
{
    if ($start >= $end) return;

    $smaller = $start;
    $bigger = $end;
    $middle = $start + ($end - $start) / 2;

    while ($smaller <= $bigger) {

        // 如果更小，就呆在左边
        if ($arr[$smaller] <= $arr[$middle]) {
            if ($smaller > $middle) {
                list($arr[$smaller], $arr[$middle]) = [$arr[$middle], $arr[$smaller]];
                $middle = $smaller;
            }
            $smaller++;
            continue;
        }

        // 更大，则跟最右边的未分类数据交换

        // 右边全是排好序时, 直接跟middle 交换
        if ($bigger <= $middle) {
            list($arr[$smaller], $arr[$middle]) = [$arr[$middle], $arr[$smaller]];
            $middle = $smaller;
        } else {
            list($arr[$smaller], $arr[$bigger]) = [$arr[$bigger], $arr[$smaller]];
        }

        $bigger--;
    }

    quickSort($arr, $start, $smaller - 1);
    quickSort($arr, $bigger + 1, $end);
}

function quickSortOptimize(&$arr, $start, $end)
{
    if ($start >= $end) return;

    $smaller = $start;
    $bigger = $end;

    $temp = $arr[$smaller];

    while ($smaller < $bigger) {

        // 从右则找一个小于基准值的元素，赋给smaller，然后把该值空出来
        while ($smaller < $bigger && $arr[$bigger] >= $temp) $bigger--;
        $arr[$smaller] = $arr[$bigger];
        // 然后从左则找一个大于基准值的元素，赋给空出来的 $bigger
        while ($smaller < $bigger && $arr[$smaller] < $temp) $smaller++;
        $arr[$bigger] = $arr[$smaller];

    }
    $arr[$smaller] = $temp;
    if ($start < $smaller - 1) quickSortOptimize($arr, $start, $smaller - 1);
    if ($end > $bigger - 1) quickSortOptimize($arr, $bigger + 1, $end);
}

/**
 * 递归方式，空间复杂度 o(n)
 * @param $arr
 */
function quickSortByTempArr(&$arr)
{
    $length = count($arr);
    if ($length <= 1) return;

    $left = $right = [];

    for ($i = 1; $i < $length; $i++) {
        //判断当前元素的大小
        if ($arr[$i] < $arr[0]) {
            $left[] = $arr[$i];
        } else {
            $right[] = $arr[$i];
        }
    }

    quickSortByTempArr($left);
    quickSortByTempArr($right);

    $arr = array_merge($left, [$arr[0]], $right);
}

/**
 * 非递归方式
 * @param $arr
 */
function quickSortStack(&$arr, $start, $end)
{
    $stack[] = [$start, $end];
    while (!empty($stack)) {

        list($start, $end) = array_pop($stack);

        $smaller = $start;
        $bigger = $end;

        if ($start >= $end) continue;

        $temp = $arr[$smaller];

        while ($smaller < $bigger) {

            // 从右则找一个小于基准值的元素，赋给smaller，然后把该值空出来
            while ($smaller < $bigger && $arr[$bigger] >= $temp) $bigger--;
            $arr[$smaller] = $arr[$bigger];
            // 然后从左则找一个大于基准值的元素，赋给空出来的 $bigger
            while ($smaller < $bigger && $arr[$smaller] < $temp) $smaller++;
            $arr[$bigger] = $arr[$smaller];

        }
        $arr[$smaller] = $temp;
        if ($start < $smaller - 1) array_push($stack, [$start, $smaller - 1]);
        if ($end > $bigger + 1) array_push($stack, [$bigger + 1, $end]);
    }
}

$arr = range(1, 1000000);
$sortArr = $arr;
shuffle($arr);
$tempArr1 = $tempArr2 = $tempArr3 = $arr;


echo '---------quickSortByTempArr-----------', PHP_EOL;
$startTime = microtime(true);
quickSortByTempArr($arr);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $arr, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;


echo '---------quickSort-----------', PHP_EOL;
$startTime = microtime(true);
quickSort($tempArr1, 0, count($tempArr1) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr1, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

echo '---------quickSortOptimize-----------', PHP_EOL;
$startTime = microtime(true);
quickSortOptimize($tempArr2, 0, count($tempArr2) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr2, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

echo '---------quickSortStack-----------', PHP_EOL;
$startTime = microtime(true);
quickSortStack($tempArr3, 0, count($tempArr3) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr3, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

