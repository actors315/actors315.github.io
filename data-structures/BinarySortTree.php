<?php

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
        echo "current: ", $this->data, ", set left: ", $left->data, PHP_EOL;
        $this->left = $left;
    }

    /**
     * @param Node $right
     */
    public function setRight(Node $right)
    {
        echo "current: ", $this->data, ", set right: ", $right->data, PHP_EOL;
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

$bst = new BinarySortTree();
$bst->insert(23);
$bst->insert(45);
$bst->insert(16);
$bst->insert(37);
$bst->insert(3);
$bst->insert(99);
$bst->insert(22);