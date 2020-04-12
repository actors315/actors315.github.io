---  
layout: post  
type: blog  
title: '【PHP 实现数据结构】二叉查找树'  
date: 2020-04-12T14:39:05+08:00  
excerpt: '什么是二叉树
在了解二叉查找树之前，我们行了解一下树的概念。树由节点和层级关系组成，是一种非线性的数据结构。就像现实中树的叶子和枝干一样。树枝把树叶一片片连接起来，树叶就是节点，树枝就是路径。像这样
'  
key: 4f25508f2c0a0ced247bcc3f93c22c0a  
---  

**什么是二叉树**

在了解二叉查找树之前，我们行了解一下树的概念。树由节点和层级关系组成，是一种非线性的数据结构。就像现实中树的叶子和枝干一样。树枝把树叶一片片连接起来，树叶就是节点，树枝就是路径。像这样

![image.png](/blog/files/images/b6711d1959d7ab47af3a8a9c7a5cfc93.png "image.png")

而二叉树是一种特殊的树，它每个节点最多只会有两个节点。像上图，因为节点 D 有三个子节点，它就不能称为二叉树。

**什么是二叉查找树**

二叉查找树又称为二叉排序数，是一种更特殊的树，首先它是二叉树，最多只会有两个节点,分别称为左节点和右节点，其次它的所有节点中，较小的值保存在左节点，较大的值保存在右节点。像这样

![image.png](/blog/files/images/ecb46b90ed361d49f8cc888a9ad371f2.png "image.png")

为了保证值大小的逻辑，在往二叉数里写数据时，就不能像队列或栈一样，直接放在队尾或栈顶了，需要有一定的逻辑处理。

![image.png](/blog/files/images/1f5fcee2416c35cfca6cf8076b88dedc.png "image.png")

**代码实现**

我们先实现数据结构和上述的数据逻辑

***定义节点***

```
/**
 * Class Node
 * @property-read $left
 * @property-read $right
 */
class Node
{
    public $data;
    private $left = null;
    private $right = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param Node $left
     */
    public function setLeft(Node $left)
    {
        $this->left = $left;
    }

    /**
     * @param Node $right
     */
    public function setRight(Node $right)
    {
        $this->right = $right;
    }

    public function __get($name)
    {
        if (in_array($name, ['left', 'right'])) {
            return $this->{$name};
        }
        return null;
    }
}
```

***二叉查找树***

```
class BinarySortTree
{
    /**
     * @var Node
     */
    public $root;

    public function insert($data) {
        $node = new Node($data);
        if(null == $this->root) {
            $this->root = $node;
            return;
        }

        $current = $this->root;
        do {
            $parent = $current;
            if ($node->data < $current->data) {
                $current = $parent->left;
                if(null == $current) {
                    $parent->setLeft($node);
                }
            } else {
                $current = $current->right;
                if(null == $current) {
                    $parent->setRight($node);
                }
            }
        } while ($current);
    }
}
```

**示例**

```
$bst = new BinarySortTree();
$bst->insert(23);
$bst->insert(45);
$bst->insert(16);
$bst->insert(37);
$bst->insert(3);
$bst->insert(99);
$bst->insert(22);
```

这样我们就获得了与上述示例图的一致的一个二叉树实例了，执行代码，数据插入流程是这样的

```
set root 23
current: 23, set right: 45
current: 23, set left: 16
current: 45, set left: 37
current: 16, set left: 3
current: 45, set right: 99
current: 16, set right: 22
```