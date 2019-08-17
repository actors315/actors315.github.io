---  
layout: post  
type: blog  
title: '【Postman】API 接口测试'  
date: 2019-08-17T23:23:55+08:00  
excerpt: 'Script
Postman 提供了一个强大的基于 Nodejs 的运行环境，允许开发人员在请求或集合中填加动态行为，如编写测试用例，动态参数，请求之前传递数据等。Postman 提供了两个事件来填加'  
key: 10bbf2ec46162d90771764bd16a9422c  
---  

**Script**

Postman 提供了一个强大的基于 Nodejs 的运行环境，允许开发人员在请求或集合中填加动态行为，如编写测试用例，动态参数，请求之前传递数据等。Postman 提供了两个事件来填加 Javascript 代码来实现特定行为。分别为  
1.pre-request script 在请求发送之前  
2.test script 在响应完成之后

![clipboard.png](/blog/files/images/d1b3412092849cea55f542dff7d4c0e8.png "clipboard.png")

**简单的测试用例**

响应参数

```
{"statusCode":200,"result":["swimwear","bikinis","two-piece outfits","tops","dresses","tees"],"msg":""}
```

测试脚本

```
// example using pm.response.to.have
pm.test("response is ok", function () {
    pm.response.to.have.status(200); // 响应状态码必须为200
});

pm.test("response must be valid and have a body", function () {
     // assert that the status code is 200
    pm.response.to.be.ok; // 响应体是否OK
    pm.response.to.be.json; // 响应体是否为 json 格式，它也会较验响应体是否存在，如果写了这个，上面这个断言就没有必要了
});

pm.test("response is correct", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.statusCode).to.equal(200); // 响应体 statusCode 字段值等于200，区分数据类型，如响应的字段串200，那这里就较验通不过
    pm.expect(jsonData.result).to.be.an('array') // 响应体 result 字段必须是个数组
});

// json schema 较验
var schema = {
    "items": {
        "type":"string"
    }
}
pm.test("响应参数格式不合法", function () {
    var jsonData = pm.response.json();
    pm.expect(tv4.validate(jsonData.result, schema)).to.be.true;  // scheme 较验是通过 tv4 这个类库来实现的，Postman 内置了它，并不需要额外填加
})
```

pm.response 表示获取到响应体  
pm.response.to.be 声名预定义的规则，如 pm.response.to.be.json 表示响应体为 json 格式。

**变量**

变量有5种类型，从外到里优先级从低到高

![clipboard.png](/blog/files/images/73fe7076a78629a59e955eb8864da278.png "clipboard.png")

变量读取通过双大括号包围 {{variable}}

***变量传递可以通过设置设量来实现***

```
pm.globals.set() // 全局变量

pm.environment.set() // 环境变量

pm.variables.set(); // 本地变量

```

同时还提供了三个动态变量

{% raw %}
```
{{$guid}} //添加v4风格的guid
{{$timestamp}} //添加当前时间戳
{{$randomInt}} //添加0到1000之间的随机整数
```
{% endraw %}

**循环和分支**

在实现真实逻辑时，往往需要根据不同的情况做不同的操作，执行不同的接口。可以通过

```
postman.setNextRequest("request_name")
```

***request\_name 为自已时表示循环，切注循环一定要有结束条件***

```
postman.setNextRequest(null) //这个表示结束执行流程
```

**参考**

断言脚本语法：<https://www.chaijs.com/api/bdd/>  
JSON Scheme 自动生成：<https://www.jsonschema.net/>