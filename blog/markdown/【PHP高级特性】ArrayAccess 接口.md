---  
layout: post  
title: '【PHP高级特性】ArrayAccess 接口'  
date: 2018-11-22T18:04:03+08:00  
excerpt: 'php提供了6个常用的预定义接口，实现某些特定的能力。其中最最常用的就是 ArrayAccess 了，像 Laravel 这种流行的框架都用到了它。
ArrayAccess 是啥
如官方文档所述，它“'  
---  

php提供了6个常用的预定义接口，实现某些特定的能力。其中最最常用的就是 ArrayAccess 了，像 Laravel 这种流行的框架都用到了它。

**ArrayAccess 是啥**

如官方文档所述，它“提供像访问数组一样访问对象的能力的接口”。

它提供了4个接口

```
/**
 * Interface to provide accessing objects as arrays.
 * @link http://php.net/manual/en/class.arrayaccess.php
 */
interface ArrayAccess {

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset);

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset);

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value);

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset);
}
```

我们实现这4个接口，依次对应数组的isset,读取，设置，unset操作。

**有什么用**

定义说的很明白啦，提供像访问数组一样访问对象的能力。用上了它，可以让一个类即可以支持对象引用，也支持数组引用。

**代码实现示例**

```
class Container implements ArrayAccess
{

    /**
     * @var array 单例对象索引
     */
    private $instances = [];

    /**
     * @var array 可实例化对象定义索引
     */
    private $definitions = [];

    public function offsetExists($offset)
    {
        return isset($this->definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->instances[$offset])) {
            return $this->instances[$offset];
        } elseif (isset($this->definitions[$offset])) {
            return $this->make($offset);
        }

        throw new \Exception('未提供对象定义');
    }

    public function offsetSet($offset, $value)
    {
        // ... 省略一些较验判断
        $this->definitions[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->definitions[$offset]);
        unset($this->instances[$offset]);
    }

    private function make($offset)
    {
        $definition = $this->definitions[$offset];

        if ($definition instanceof \Closure) {
            return $this->instances[$offset] = $definition();
        }

        if (is_object($definition)) {
            return $this->instances[$offset] = $definition;
        }

        if (is_array($definition)) {
            $class = $definition['class'];
            $reflection = new \ReflectionClass($class);

            $dependencies = [];
            // ... 省略反射的实现代码
            $object = $reflection->newInstanceArgs($dependencies);
            return $this->instances[$offset] = $object;
        }

        throw new \Exception('对象定义不合法');
    }
}
```

**使用示例**

```
$container = new Container();

$container['test'] = function () {
  return 'this is a test';
};

var_dump(isset($container['test']));

echo $container['test'];

unset($container['test']);
```

**参考**

[预定义接口](http://php.net/manual/zh/reserved.interfaces.php)