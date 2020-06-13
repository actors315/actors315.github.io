---  
layout: post  
type: blog  
title: '【PHP 实现算法】快速排序的几种实现'  
date: 2020-06-13T22:29:09+08:00  
excerpt: '什么是快速排序
快速排序是运用分治的方法，通过一趟遍历将要排序的数据分割成独立的两部分，其中一部分的所有数据都比另外一部分的所有数据都要小，然后再用同样的方法对这两部分数据分别进行快速排序。 
它的流'  
key: 6217aa577ae917eda22c0731b7402862  
---  

**什么是快速排序**

快速排序是运用分治的方法，通过一趟遍历将要排序的数据分割成独立的两部分，其中一部分的所有数据都比另外一部分的所有数据都要小，然后再用同样的方法对这两部分数据分别进行快速排序。

它的流程是这样

1. 选择一个比较基值，
2. 然后把数组中小于该值的元素放到左边，大于该值的元素放到另右边
3. 分别对左边数组和右边数组重复该步骤，直到不能再分，那数组就是有序的了

**常用实现**

先来看第一种实现方法，也是比较常用的方法，采用临时数组来存储分别存储左边元数和左边元素。我们选第一个元数为基准值。

```
function quickSortByTempArr(&$arr)
{
    $length = count($arr);
    if ($length <= 1) return;

    $left = $right = [];

    for ($i = 1; $i < $length; $i++) {
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
```

该方法符合分治的思想，缺点就是申请了临时数组分别存储左右元素，空间复杂度为O(n)

再来看方法二，采用元素交换的方式，选取中间元素(或选第一个元素)为基准值，每次比较把更小的元素交换到左边，更大的元素交换到右边。

```
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
```

可以看出，该方法并没有申请新的临时数组，空间复杂度为 O(1)，但是该方法效率不如方法一，测试结果执行时间恒定为方法一的接近2倍，显然不够好。仔细查看执行流程，发现有时会把右侧大于基准值的元素也交换到左侧，然后再交换回去，冗余操作。

方法三，还是采用元素交换的方式，但每次交换都只把左则大于基准值的元素与右侧小于基准值的元素进行交换。

```
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
    quickSortOptimize($arr, $start, $smaller - 1);
    quickSortOptimize($arr, $bigger + 1, $end);
}
```

以上三种方法，都是递归的方式实现的，那有没有非递归的方式呢？当然也是有的，我们把每次待排序的数组分段，丢到一个临时数组里面，依次把它们取出来排序就ok了，在方法三的基础上，稍作改动。

```
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
```

借助了一个临时数组，来存储边界值。时间复杂度和方法三完全一致。

接下来我们分别测试一下这几个方法效率，100万个元素为例

```
$arr = range(1, 1000000);
$sortArr = $arr;
shuffle($arr);
$tempArr1 = $tempArr2 = $tempArr3 = $arr;


echo '---------方法一-----------', PHP_EOL;
$startTime = microtime(true);
quickSortByTempArr($arr);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $arr, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;


echo '---------方法二-----------', PHP_EOL;
$startTime = microtime(true);
quickSort($tempArr1, 0, count($tempArr1) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr1, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

echo '---------方法三-----------', PHP_EOL;
$startTime = microtime(true);
quickSortOptimize($tempArr2, 0, count($tempArr2) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr2, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

echo '---------方法四-----------', PHP_EOL;
$startTime = microtime(true);
quickSortStack($tempArr3, 0, count($tempArr3) - 1);
$endTime = microtime(true);
echo 'sort -- ', $sortArr == $tempArr3, PHP_EOL;
echo 'total time', $endTime - $startTime, PHP_EOL;

```

![image.png](/blog/files/images/f33881bede268c0c02f0fab593621e68.png "image.png")