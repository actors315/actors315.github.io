---  
layout: post  
title: 'PHP实现一个轻量级容器'  
date: 2019-01-27T21:16:38+08:00  
excerpt: '什么是容器
在开发过程中，经常会用到的一个概念就是依赖注入。我们借助依懒注入来解耦代码，选择性的按需加载服务，而这些通常都是借助容器来实现。
容器实现对对象的统一管理，并且确保对象实例的唯一性
容器可'  
---  

**什么是容器**

在开发过程中，经常会用到的一个概念就是依赖注入。我们借助依懒注入来解耦代码，选择性的按需加载服务，而这些通常都是借助容器来实现。

***容器实现对对象的统一管理，并且确保对象实例的唯一性***

容器可以很轻易的找到有很多实现示例，如 [PHP-DI](https://github.com/PHP-DI/PHP-DI) 、 [YII-DI](https://github.com/yiisoft/di) 等各种实现，通常他们要么大而全，要么高度适配特定业务，与实际需要存在冲突。

出于需要，我们自己造一个轻量级的轮子，为了保持规范，我们基于 [PSR-11](https://www.php-fig.org/psr/psr-11/) 来实现。

 **PSR-11**

PSR 是 php-fig 提供的标准化建议，虽然不是官方组织，但是得到广泛认可。PSR-11 提供了容器接口。它包含 ContainerInterface 和 两个异常接口，并提供使用建议。

```
/**
 * Describes the interface of a container that exposes methods to read its entries.
 */
interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id);

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id);
}
```

**实现示例**

我们先来实现接口中要求的两个方法

```
abstract class AbstractContainer implements ContainerInterface
{

    protected $resolvedEntries = [];

    /**
     * @var array
     */
    protected $definitions = [];

    public function __construct($definitions = [])
    {
        foreach ($definitions as $id => $definition) {
            $this->injection($id, $definition);
        }
    }

    public function get($id)
    {

        if (!$this->has($id)) {
            throw new NotFoundException("No entry or class found for {$id}");
        }

        $instance = $this->make($id);

        return $instance;
    }

    public function has($id)
    {
        return isset($this->definitions[$id]);
    }
```

实际我们容器中注入的对象是多种多样的，所以我们单独抽出实例化方法。

```
    protected function make($name)
    {

        if (isset($this->resolvedEntries[$name])) {
            return $this->resolvedEntries[$name];
        }

        $definition = $this->definitions[$name];
        $params = [];
        if (is_array($definition) && isset($definition['class'])) {
            $params = $definition;
            $definition = $definition['class'];
            unset($params['class']);
        }

        $object = $this->reflector($definition, $params);

        return $this->resolvedEntries[$name] = $object;
    }

    public function reflector($concrete, array $params = [])
    {
        if ($concrete instanceof \Closure) {
            return $concrete($params);
        } elseif (is_string($concrete)) {
            $reflection = new \ReflectionClass($concrete);
            $dependencies = $this->getDependencies($reflection);
            foreach ($params as $index => $value) {
                $dependencies[$index] = $value;
            }
            return $reflection->newInstanceArgs($dependencies);
        } elseif (is_object($concrete)) {
            return $concrete;
        }
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    private function getDependencies($reflection)
    {
        $dependencies = [];
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            $dependencies = $this->getParametersByDependencies($parameters);
        }

        return $dependencies;
    }

    /**
     *
     * 获取构造类相关参数的依赖
     * @param array $dependencies
     * @return array $parameters
     * */
    private function getParametersByDependencies(array $dependencies)
    {
        $parameters = [];
        foreach ($dependencies as $param) {
            if ($param->getClass()) {
                $paramName = $param->getClass()->name;
                $paramObject = $this->reflector($paramName);
                $parameters[] = $paramObject;
            } elseif ($param->isArray()) {
                if ($param->isDefaultValueAvailable()) {
                    $parameters[] = $param->getDefaultValue();
                } else {
                    $parameters[] = [];
                }
            } elseif ($param->isCallable()) {
                if ($param->isDefaultValueAvailable()) {
                    $parameters[] = $param->getDefaultValue();
                } else {
                    $parameters[] = function ($arg) {
                    };
                }
            } else {
                if ($param->isDefaultValueAvailable()) {
                    $parameters[] = $param->getDefaultValue();
                } else {
                    if ($param->allowsNull()) {
                        $parameters[] = null;
                    } else {
                        $parameters[] = false;
                    }
                }
            }
        }
        return $parameters;
    }
```

如你所见，到目前为止我们只实现了从容器中取出实例，从哪里去提供实例定义呢，所以我们还需要提供一个方法.

```
    /**
     * @param string $id
     * @param string | array | callable $concrete
     * @throws ContainerException
     */
    public function injection($id, $concrete)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException(sprintf(
                'The id parameter must be of type string, %s given',
                is_object($id) ? get_class($id) : gettype($id)
            ));
        }

        if (is_array($concrete) && !isset($concrete['class'])) {
            throw new ContainerException('数组必须包含类定义');
        }

        $this->definitions[$id] = $concrete;
    }
```

只有这样吗？对的，有了这些操作我们已经有一个完整的容器了，插箱即用。

不过为了使用方便，我们可以再提供一些便捷的方法，比如数组式访问。

```
class Container extends AbstractContainer implements \ArrayAccess
{

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->injection($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->resolvedEntries[$offset]);
        unset($this->definitions[$offset]);
    }
}
```

这样我们就拥有了一个功能丰富，使用方便的轻量级容器了，赶快整合到你的项目中去吧。

点击这里查看[完整代码](https://github.com/actors315/DI)