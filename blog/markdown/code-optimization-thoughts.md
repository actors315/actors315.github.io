---  
layout: post  
type: blog  
title: '代码优化随想'  
date: 2019-10-11T16:18:35+08:00  
excerpt: '
这不是一篇技术文章，只是最近做代码重构的一些心得和想法。
要有坚定的目标
从某种程度上来说，重构是件费力不讨好的事，尤其是在业务驱动的团队，毕竟重构并不能带来直接、可量化的收益。
业务只看到功能能不'  
key: a009d3ead44eacfc71cd25363bc368af  
---  

![微信图片_20191011160954.jpg](/blog/files/images/34fe82f0370cd2fdc45155c968accf1e.jpg "微信图片_20191011160954.jpg")

这不是一篇技术文章，只是最近做代码重构的一些心得和想法。

**要有坚定的目标**

从某种程度上来说，重构是件费力不讨好的事，尤其是在业务驱动的团队，毕竟重构并不能带来直接、可量化的收益。

业务只看到功能能不能跑，不会管用什么姿势跑。很多时候也会发现，你的同事简简单单堆砌代码被表扬高效做的多，获得好的回报，而你只能默默前行，如果没有明确和坚定的目标，很可能就半途而废了，而半途而废甚至不如不做，至少不做你还知道这是对的。

重构的目标一定要符合行业主流规范，如MVC，组件化，服务化，可重用。代码一定要高效、简单易懂，这点很重要，技术人总有一种炫技的冲动，写出只有自己能看懂的代码并不能收获同行者。

**清晰的计划**

用嘴写代码总是不可行的，要想达到目标，少不了一个清晰的落地计划。就像做任何事情一样，总会遇到阻碍，有很多的需求需要处理，会被打扰，在没有可见的成果之前，也不会得到支持，毕竟在线业务，稳定大于一切。

从见效最快的地方做起，如果优化一个缓存结构，可以提升60%的性能，那应该果断把它做了。

从局部做起，拆一栋房子不敢，拆一个窗户还能楼塌了？

如果迫不得已需要试错，从边缘业务开始尝试，并要考虑充分回滚方案，可在线调整配置的方式是风险相对较少的。

持续的做，慢慢的就会有成果，大胆的分享这些成果和经验，这时再去做更大的尝试，就会有一些支持了。

**重构总是有风险的，要接受这个现实**

毋庸质疑，重构一定会有风险，不然也轮不到你做。我们可以，也一定会犯错误，需要接受这个现实，只有这样，才能理解如何降低这些错误带来的影响。

要坦诚，出错了不要掩饰，一次掩饰过去并不能消除别人对你的怀疑，但一定会给人不可信任的印象，下次就会失去试错的资格。

不要盲目自信，尤其是汲外部调用很多的逻辑，在确保完全搞清楚逻辑之前，不要乱动，调研清楚。

要借助工具，代码总是比人可靠。完善的单元测试，自动化测试，总能覆盖你想不到的点，获得意想不到的收获。

幸运女神不会一再眷顾，你必须自己寻求突破。

![微信图片_20191011161020.jpg](/blog/files/images/110c00ace636ee1e6236dd46ee1feb88.jpg "微信图片_20191011161020.jpg")

原文摘自我的公众号，有增删。