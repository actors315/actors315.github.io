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


