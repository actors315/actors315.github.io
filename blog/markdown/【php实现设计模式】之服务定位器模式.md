---  
layout: post  
type: blog  
title: '【php实现设计模式】之服务定位器模式'  
date: 2017-06-24T11:04:55+08:00  
excerpt: '什么是服务定位器
服务定位器（service locator）他知道如何定位（创建或者获取）一个应用所需要的服务，服务使用者在实际使用中无需关心服务的实际实现。
有什么作用
实现服务使用者和服务的解耦'  
key: 'b40acbafec88bf4dfda8325ba0e04175'  
---  

**什么是服务定位器**

服务定位器（service locator）他知道如何定位（创建或者获取）一个应用所需要的服务，服务使用者在实际使用中无需关心服务的实际实现。

**有什么作用**

实现服务使用者和服务的解耦，无需改变代码而只是通过简单配置更服服务实现。

**UML图示**  
![uml24.png](/blog/files/images/0b4b064a9f639925786f616f5096279d.png "uml24.png")

**代码示例**

```
class ServiceLocator {

    /**
     * 服务实例索引
     */
    privite $_services = [];

    /**
     * 服务定义索引
     */
    private $_definitions = [];
    
    /**
     * 是否全局服务共享（单例模式）
     */
    private $_shared = [];
    
    public function has($id){
        return isset($this->_services[$id]) || isset($this->_definitions[$id]);
    }
    
    public function __get($id){
        if($this->has($this->id)){
            $this->get($id);
        }
        
        // another implement
    }
    
    public function get($id){
        if(isset($this->_services[$id]) && $this->_shared[$id]){
            return $this->_services[$id];
        }
        
        if (isset($this->_definitions[$id])) {
            // 实例化
            $definition = $this->_definitions[$id];
            $object = Creator::createObject($definition);//省略服务实例化实现
            if($this->_shared[$id]){
                $this->_services[$id] = $object
            }
            
            return $object;
        }
        
        throw new Exception("无法定位服务{$id}")
    }
        
    public function set($id,$definition,$share = false){
        if ($definition === null) {
            unset($this->_services[$id], $this->_definitions[$id]);
            return;
        }
        
        unset($this->_services[$id]);
        $this->_shared[$id] = $share;
        if (is_string($definition)) {
            return $this->_definitions[$id] = $definition;
        }
        if (is_object($definition) || is_callable($definition, true)) {
            return $this->_definitions[$id] = $definition;
        }
        
        if (is_array($definition)) {
            if (isset($definition['class'])) {
                return $this->_definitions[$id] = $definition;
            }
        }
        
        throw new Exception("服务添加失败");
    }
}
```

**感谢**

文中图片来源来源网络 [http://designpatternsphp.read...](http://designpatternsphp.readthedocs.io/zh_CN/latest/More/ServiceLocator/README.html)