---  
layout: post  
type: blog  
title: '【PHP 实现数据结构】单向链表'  
date: 2019-01-04T19:55:52+08:00  
excerpt: '什么是单向链表
链表是以链式存储数据的结构，其不需要连续的存储空间，链表中的数据以节点来表示，每个节点由元素(存储数据)和指针(指向后继节点)组成。
单向链表（也叫单链表）是链表中最简单的一种形式，每'  
key: 8a5f55ef18fe2c051ed201065eef02cf  
---  

**什么是单向链表**

链表是以链式存储数据的结构，其不需要连续的存储空间，链表中的数据以节点来表示，每个节点由元素(存储数据)和指针(指向后继节点)组成。

单向链表（也叫单链表）是链表中最简单的一种形式，每个节点只包含一个元素和一个指针。  
它有一个表头，并且除了最后一个节点外，所有节点都有其后继节点。  
它的存储结构如下图所示  
![图片描述](/blog/files/images/c864917e17e5a14ef39f32be150617df.png "图片描述")

**代码实现**

***定义节点***

```
class Node
{
    public $data;

    /**
     * @var null | Node
     */
    public $next;

    public function __construct($data)
    {
        $this->data = $data;
        $this->next = null;
    }

}
```

***单链表实现***

```
/**
 * Class SingleLinkList
 * 单链接的实现示例，实现简单的填加，插入，删除, 查询，长度，遍历这几个简单操作
 */
class SingleLinkList
{
    /**
     * 链表头结点，头节点必须存在，
     * @var Node
     */
    public $header;

    private $size = 0;

    /**
     * 构造函数，默认填加一个哨兵节点，该节点元素为空
     * SingleLinkList constructor.
     */
    public function __construct()
    {
        $this->header = new Node(null);
    }

    /**
     * 在链表末尾添加节点
     * @param Node $node
     * @return int
     */
    public function addNode(Node $node)
    {
        $current = $this->header;
        while ($current->next != null) {
            $current = $current->next;
        }
        $current->next = $node;

        return ++$this->size;
    }

    /**
     * 在指定位置插入节点
     * @param int $index 节点位置，从1开始计数
     * @param Node $node
     * @return int
     * @throws Exception
     */
    public function insertNodeByIndex($index, Node $node)
    {
        if ($index < 1 || $index > ($this->size + 1)) {
            throw new Exception(sprintf('你要插入的位置，超过了链表的长度 %d', $this->size));
        }

        $current = $this->header;
        $tempIndex = 1;
        do {
            if ($index == $tempIndex++) {
                $node->next = $current->next;
                $current->next = $node;
                break;
            }
        } while ($current->next != null && ($current = $current->next));

        return ++$this->size;
    }

    /**
     * 删除节点
     * @param int $index 节点位置，从1开始计数
     * @return int
     * @throws Exception
     */
    public function deleteNodeByIndex($index)
    {
        if ($index < 1 || $index > ($this->size + 1)) {
            throw new Exception('你删除的节点不存在');
        }

        $current = $this->header;
        $tempIndex = 1;
        do {
            if ($index == $tempIndex++) {
                $current->next = $current->next->next;
                break;
            }
        } while ($current->next != null && ($current = $current->next));

        return --$this->size;
    }

    /**
     * 查询节点
     * @param int $index 节点位置，从1开始计数
     * @return Node|null
     * @throws Exception
     */
    public function searchNodeByIndex($index) {
        if ($index < 1 || $index > ($this->size + 1)) {
            throw new Exception('你查询的节点不存在');
        }

        $current = $this->header;
        $tempIndex = 1;
        do {
            if ($index == $tempIndex++) {
                return $current->next;
            }
        } while ($current->next != null && ($current = $current->next));
    }

    /**
     * 获取节点长度
     * @return int
     */
    public function getLength()
    {
        return $this->size;
    }

    /**
     * 遍历列表
     */
    public function showNode()
    {
        $current = $this->header;
        $index = 1;
        while ($current->next != null) {
            $current = $current->next;
            echo 'index --- ' . $index++ . ' --- ';
            echo var_export($current->data);
            echo PHP_EOL;
        }
    }
}
```

**示例**

```
$link = new SingleLinkList();
$link->addNode(new Node(1));
$link->addNode(new Node(2));
$link->insertNodeByIndex(3, new Node(3));
$link->addNode(new Node(4));
$link->addNode(new Node(5));
echo $link->getLength(), PHP_EOL;
$link->showNode();
echo '-----------', PHP_EOL;
var_dump($link->searchNodeByIndex(3));
echo '-----------', PHP_EOL;
$link->deleteNodeByIndex(3);
$link->showNode();
```