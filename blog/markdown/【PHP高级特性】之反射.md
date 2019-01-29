PHP5 开始提供了完整的反射API。有反射类（ReflectionClass）和反射函数（ReflectionFunction）等，功能大同小异，这里主要以ReflectionClass为列说明。

**什么是反射**  
他是指PHP在运行状态中，动态的获取类、方法、属性、参数、注释等信息和动态调用对象的方法的功能。

**有什么用**  
可以帮助我们构建复杂的，可扩的运用。比如自动加载插件，自动生成文档等

**代码示例**  
该示例为一个通用API入口

HttpApi.php

```
namespace twinkle\service\http;

class HttpApi
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function parseRequest($method,$params = [])
    {
        $class = new \ReflectionClass($this->class);
        $instance = $class->newInstanceArgs($params);
        $method = $class->getMethod($method);
        $args = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (isset($params[$name])) {
                $args[$name] = $params[$name];
            } else {
                try {
                    $args[$name] = $param->getDefaultValue();
                } catch (\Exception $e) {
                    throw new RequestException(
                        '请求参数不合未能',
                        500
                    );
                }
            }
        }

        return [$instance,$method,$args];
    }
}
```

NotFoundService.php

```
namespace app\services;

use app\base\Service;

class NotFoundService extends Service
{
    public function error()
    {
        return $this->format(['status' => 1, 'msg' => '请求不合法,请确认service和method是否存在']);
    }
}
```

使用范例

```
$params = $_REQUEST;
$serviceName= isset($params['service']) ? $params['service'] : 'NotFound';
$methodName= isset($params['method']) ? $params['method'] : 'error';
$class = '\\app\\services\\' . Str::ucWords($serviceName) . 'Service';
list($instance, $method, $args) = (new HttpApi($class))->parseRequest($methodName, $params);
echo json_encode(($method->invokeArgs($instance, $args)));
```