单例模式是最常用，也是最简单的一种设计模式。

**什么是单例模式**  
他是一个特殊的类，该类在系统运行时只有一个实例。这个类必须提供一个获取对象实例的方法。

**有什么作用**  
1.全局只创建一次实例，提高性能，减少资源损耗  
2.自已统一创建实例，具有可控性  
等

**注意事项**  
1.需要保证一个运行周期只有一个实例存在，所以任何会创新新实例的方法都要禁用（设为私有）  
1\) 禁止外部创建实例  
2\) 禁止实例克隆  
2.不要滥用单例模式

**代码示例**

```
class RedisLogic
{

    private static $_instance = null;

    static $data = [];

    /**
     * SingletonClass constructor.
     * 禁止外部创建实例
     *
     */
    private function __construct()
    {
    }

    /**
     * 禁止克隆
     *
     * @throws \Exception
     */
    private function __clone()
    {
        throw new \Exception('单例模式，不允许克隆');
    }
    
    /**
     * 禁止serialize
     *
     */
    private function __sleep() {
    }
 
    /**
     * 禁止unserialize
     *
     */
    private  function __wakeup() {
    } 
    
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $key
     * @param string $extend
     * @param int $type
     * @param array $options
     * @return mixed
     */
    public function read($key, $extend = '', $type = 1, $options = [])
    {
        global $cur_lang;

        $relKey = $this->getKey($key, $extend, $type);
        if (isset(static::$data[$relKey])) {
            return static::$data[$relKey];
        }
        // ... 省略业务代码

        return static::$data[$relKey] = $value;
    }

    public function write($key, $data, $options = [], $extend = '', $type = 1)
    {
        $relKey = $this->getKey($key, $extend, $type);
        // ... 省略业务代码
    }

    /**
     * @param $key
     * @param string $extend
     * @param int $type 扩展类型,1是扩展在后,2在前
     * @return int
     */
    private function getKey($key, $extend = '', $type = 1)
    {
        if (empty($extend)) {
            return $key;
        }

        return $type == 1 ? $key . $extend : $extend . $key;
    }
}
```

demo 禁止系列化，有的情况可能需要这样。可以参考鸟哥的这遍文章，经测试在php5下是有效的

[Serialize/Unserialize破坏单例](http://www.laruence.com/2011/03/18/1909.html)