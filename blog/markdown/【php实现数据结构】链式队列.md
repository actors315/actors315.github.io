---  
layout: post  
type: blog  
title: '【php实现数据结构】链式队列'  
date: 2019-01-05T12:01:14+08:00  
excerpt: '什么是链式队列
队列是一种“先进先出”的存储结构,是一种特殊的线性表，于它只允许在表的前端（front）进行删除操作，而在表的后端（rear）进行插入操作。通常队列可以分为顺序队列和链式队列两种实现，'  
key: '5eb178cd5e9a7a20cb97f00991d06262'  
---  

**什么是链式队列**

队列是一种“先进先出”的存储结构,是一种特殊的线性表，于它只允许在表的前端（front）进行删除操作，而在表的后端（rear）进行插入操作。  
通常队列可以分为顺序队列和链式队列两种实现，  
顺序队列顾名思义就是采用顺序存储，如以数组方式来实现，  
链式队列采用链式存储，如以上篇说到的单向链表来实现，

***链式队列是以链式数据结构实现的队列***

队列有两个基本的操作，入队列和出队列

**代码实现**

链式队列实现方式多种多样，可以以单链表，双向链表，循环链表等各种方式来实现，这里以上篇提到的[单链表](https://segmentfault.com/a/1190000017764793)的方式来实现。

```
<?php

require 'SingleLinkList.php';

/**
 * Class Queue
 * 队列是一种“先进先出”的存储结构,它只允许在队头进行删除操作，而在队尾进行插入操作
 * 通常队列可以分为顺序队列和链式队列两种实现
 * 顺序队列顾名思义就是采用顺序存储，如以数组方式来实现
 * 链式队列采用链式存储，如以上篇说到的单向链表来实现
 *
 * 队列有两个基本的操作，入队列和出队列
 */
class QueueImplementedBySingleLinkList extends SingleLinkList
{

    /**
     * Queue constructor.
     * 构造函数，初始化队列
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 入队
     * @param $data
     */
    public function enQueue($data)
    {
        $node = new Node($data);
        parent::addNode($node);
    }

    /**
     * 出队
     * @return mixed
     * @throws Exception
     */
    public function deQueue()
    {
        if ($this->isEmpty()) {
            throw new Exception('队列为空');
        }

        $node = parent::searchNodeByIndex(1);
        parent::deleteNodeByIndex(1);
        return $node->data;
    }

    /**
     * 队列是否为空
     * @return bool
     */
    public function isEmpty()
    {
        return $this->header->next == null;
    }
}
```

**示例**

```
$queue = new QueueImplementedBySingleLinkList();
$queue->enQueue('1');
$queue->enQueue('2');
$queue->enQueue('3');
$queue->enQueue('4');
var_dump($queue);
echo '-----------', PHP_EOL;
$queue->deQueue();
$queue->deQueue();
var_dump($queue);
```