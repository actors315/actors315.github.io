---  
layout: post  
type: blog  
title: '【PHP 实现数据结构】栈'  
date: 2020-04-06T21:07:19+08:00  
excerpt: '之前介绍过 “队列” 是一种特殊的线性表，这里再介绍另外一种特殊的线性表 “栈”
什么是栈
栈是一种后入先出的数据结构，它只能允许在列表的一端进行操作。允许操作的一端称为栈顶。
栈有两个基本操作，元素'  
key: b970ecacc80a1f4069e6a3bd841218c9  
---  

之前介绍过 “[队列](https://segmentfault.com/a/1190000017772194)” 是一种特殊的线性表，这里再介绍另外一种特殊的线性表 “栈”

**什么是栈**

栈是一种后入先出的数据结构，它只能允许在列表的一端进行操作。允许操作的一端称为栈顶。

栈有两个基本操作，元素压入栈和元素弹出栈，操作示例图。

![image.png](/blog/files/images/20030b452985c6a71e3ec19f69ec1ca9.png "image.png")

**代码实现**

我们来实现上述两个基本操作，和实际应用中常用的其他几个操作。

- push 入栈
- pop 出栈
- peek 栈顶元素预览
- length 栈存储的元素个数
- clear 清空栈

```
<?php

/**
 * Class Stack
 */
class Stack
{
    protected $top = 0;

    protected $dataStore = [];

    /**
     * 入栈
     * @param $data
     */
    public function push($data)
    {
        $this->dataStore[$this->top++] = $data;
    }

    /**
     * 出栈
     * @return mixed
     */
    public function pop()
    {
        return $this->dataStore[--$this->top];
    }

    /**
     * 预览，查看栈顶元素，但是不弹出
     * @return mixed
     */
    public function peek()
    {
        return $this->dataStore[$this->top - 1];
    }

    /**
     * 栈长度
     * @return int
     */
    public function length() {
        return $this->top;
    }

    /**
     * 清空栈元素
     */
    public function clear() {
        $this->top = 0;
        $this->dataStore = [];
    }
}
```

**示例**

```
$stack = new Stack();
$stack->push(1);
$stack->push(2);
$stack->push(3);
echo "stack length:",$stack->length(),PHP_EOL;
$stack->pop();
$stack->pop();
$stack->push(4);
echo "stack top:",$stack->peek(),PHP_EOL;
$stack->clear();
echo "stack length:",$stack->length(),PHP_EOL;
```

![image.png](/blog/files/images/cc4bb682e5221e661bf3265e36debfce.png "image.png")