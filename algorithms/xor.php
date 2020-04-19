<?php

/**
 * 找出数组中唯一的重复元素
 * 问题描述：数组a[N], 1---N-1 这N-1个数存放在a[N]中，其中某个数重复了1次。写一个函数，找出被重复的数字
 * 原理：异或操作 如果a、b两个值不相同，则异或结果为1。如果a、b两个值相同，异或结果为0。
 * 从这个描述我们可以知道，任何值与 0 做异或操作，结果都是这个数
 * 实现逻辑：假设重复数为A，
 * 1 至 N -1 的异或结果是 B (不包含重复 A)。
 * a[N] 所有元数的异或结果是 A ^ B (包含重复的A)
 * 现把 a[N] 的异或结果与 (至 N -1 的异或结果) 再做一次异或操作，A ^ B ^ B = A ^ 0
 * @param array $array
 * @return int|mixed
 */
function getDuplicateByXor($array)
{
    $duplicate = $array[0];
    for ($i = 1; $i < count($array); $i++) {
        $duplicate ^= $array[$i] ^ $i;
    }

    return $duplicate;
}

$a = range(1, 100);
$a[] = 11;
shuffle($a);
echo "重复元数为：", getDuplicateByXor($a);