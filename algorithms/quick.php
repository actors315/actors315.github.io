<?php

function quickSort($arr)
{
    $borderStack[] = [0, count($arr) - 1]; //数组边界
    while (!empty($borderStack))
    {
        $border = array_pop($borderStack);
        $left = $border[0];
        $right = $border[1];
        $pivot = $arr[$left]; // 分界值
        while ($left < $right)
        {
            while ($left<$right && $arr[$right] >= $pivot) $right--;
            $arr[$left] = $arr[$right];
            while ($left<$right && $arr[$left] < $pivot) $left++;
            $arr[$right] = $arr[$left];
        }
        //$left 等于 $right :
        $arr[$left] = $pivot;
        if ($border[0] < $left - 1) $borderStack[] = [$border[0],$left-1];
        if ($border[1] > $left + 1) $borderStack[] = [$left+1, $border[1]];
    }
    return $arr;
}