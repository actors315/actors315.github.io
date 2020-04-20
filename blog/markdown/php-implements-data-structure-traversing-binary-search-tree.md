---  
layout: post  
type: blog  
title: '【PHP 实现数据结构】遍历二叉查找树'  
date: 2020-04-19T23:00:12+08:00  
excerpt: '上一篇 我们简单介绍了什么是二叉查找树，并实现了该结构。
这一篇我们来看如何遍历二叉树。常用的三种遍历方式有“先序” “中序” “后序”。对于二次查找树来说，中序遍历刚好可以得到一个有序的结果（即排序'  
key: 765ae81d177306dcc7c7e3448896a70f  
---  

[上一篇](https://segmentfault.com/a/1190000022346129) 我们简单介绍了什么是二叉查找树，并实现了该结构。

这一篇我们来看如何遍历二叉树。常用的三种遍历方式有“先序” “中序” “后序”。对于二次查找树来说，中序遍历刚好可以得到一个有序的结果（即排序）。三种遍历方式的定义如下

从根结点出发，逆时针（访问左子树）沿着二叉树外缘移动，对每个结点均途径三次，最后回到根结点。

1. 先序：访问结点均是第一次经过结点时进行的（根节点 -&gt; 左节点 -&gt; 右节点）。
2. 中序：访问结点均是第二次经过结点时进行的（左节点 -&gt; 根节点 -&gt; 右节点）。
3. 后序：访问结点均是第三次经过结点时进行的（左节点 -&gt; 右节点 -&gt; 根节点）。

是不是看了云里雾里，没关系，我们来画个图，注意箭头方向  
![遍历二叉树](/blog/files/images/c1270fe298881ea3cd3c48fd77983174.jpg "遍历二叉树")

有两个子节点的好理解一点，像节点 D 只有一个子节点是I，那这种到底是先D 还是先I，还是搞不明白。那我们再画一个图，注意数字。  
![遍历二叉树2](/blog/files/images/e4ee862245f69efbe592a57fb85cd672.jpg "遍历二叉树2")

上一个图的基础上，结合[上一遍](https://segmentfault.com/a/1190000022346129) 的节点的实现,所有节点都有两个子节点，只不过有的子节点的是null。我们为所有没有两个叶子节点的节点以空节点来补齐两个。

这样子是不是很清晰了。

接下来我们把这个思路转化为代码实现。

```
public function preOrder()
    {
        $stack = [];

        // 先访问根结点
        $current = $this->root;
        $pre = [];

        while (!empty($stack) || $current != null) {

            // 访问左子树
            while ($current != null) {
                $pre[] = $current->data;
                $stack[] = $current;
                $current = $current->left;
            }
            $current = array_pop($stack);

            // 访问右子树
            $current = $current->right;
        }

        return $pre;
    }

    public function inOrder()
    {
        $stack = [];

        // 先访问根结点
        $current = $this->root;
        $sort = [];
        while (!empty($stack) || $current != null) {

            // 访问左子树
            while ($current != null) {
                $stack[] = $current;
                $current = $current->left;
            }
            $current = array_pop($stack);
            $sort[] = $current->data;

            // 访问右子树
            $current = $current->right;
        }

        return $sort;
    }

    public function postOrder()
    {
        $stack = [];
        $visitStack = [];
        $current = $this->root;
        while ($current != null) {
            $visitStack[] = $current;
            if ($current->left != null) {
                $stack[] = $current->left;
            }
            if ($current->right != null) {
                $stack[] = $current->right;
            }

            $current = array_pop($stack);
        }

        $next = [];
        while ($current = array_pop($visitStack)) {
            $next[] = $current->data;
        }

        return $next;
    }
```

示例  
还是上一篇的二叉树实例，验证一下

```
$arr = $bst->preOrder();
echo "pre order: ",implode(' ',$arr),PHP_EOL;

$arr = $bst->inOrder();
echo "in order: ",implode(' ',$arr),PHP_EOL;

$arr = $bst->postOrder();
echo "post order: ",implode(' ',$arr),PHP_EOL;
```

![image.png](/blog/files/images/7a55726c4d4cb7b42e9cde8663c82cfd.png "image.png")