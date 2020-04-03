---  
layout: post  
type: blog  
title: '【Postman】pm.response.headers 对象探究'  
date: 2020-04-03T16:34:09+08:00  
excerpt: '是什么
顾名思义，这个对象包含响应头信息。
是数组吗
网上很多介绍都说他是“以数组的形式返回当前请求成功后的response的headers”，用console.log() 输出，发现它也像是数组。
'  
key: e133edca251a04314750f6cc6872c53a  
---  

**是什么**

顾名思义，这个对象包含响应头信息。

**是数组吗**

网上很多介绍都说他是“以数组的形式返回当前请求成功后的response的headers”，用console.log() 输出，发现它也像是数组。  
![image.png](/blog/files/images/5e751ba6fd92c5457bbaea0828ceeae8.png "image.png")

可如果你真把它当成数组去操作的时候，你可能会怀疑人生了，undefined 是个什么鬼。

**究竟是什么**

```
console.log(typeof(headers))
```

哦，原来是 object

```
console.log(pm.response.headers instanceof Object)

// true
```

那为什么 console.log() 直接输出数组了呢？  
我们一步一步来看

**【pm.response.headers】实现**

来看 postman 官方文档中 [response 的实现](https://www.postmanlabs.com/postman-collection/Response.html)

![image.png](/blog/files/images/f4cac0f4d08e83781b8c127725f262c3.png "image.png")

他是一个自定义类型 HeaderList,找到 toString 方法

![image.png](/blog/files/images/7973cf1d5ee0aa06cfc882eea485fb9e.png "image.png")

最终走到这里

![image.png](/blog/files/images/b00e0715e5b1b88f723ca7a206f2cc95.png "image.png")

所以它实际上 console.log() 输出的就是 pm.response.headers 对象中的 members 实性。我们来对比一下  
![image.png](/blog/files/images/b9fe0ca1259fbc61bd5236ba28ce191f.png "image.png")

是的，他们就是同一个数组