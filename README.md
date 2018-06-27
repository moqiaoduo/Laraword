# Laraword

这是一个由Laravel框架为基础编写的PHP博客程序

因防止任何个人或团体用于商业用途，因此采用GPL协议

如果各位大神愿意提供改进建议并认真阅读源码后pull request，那么您们不需要关闭这个页面

所有的教程全部在wiki中，请自行查看

声明：本程序不适合小白使用，主要是自用，有兴趣的话可以自己做插件功能；另外并没有权限等级这种<del>骚</del>东西

建议：项目拉下来之前不要先配置Web服务器，先把程序安装好

<hr>

基本环境要求：PHP >= 7.1.3 MySQL >=5.7

解释一下为什么需要如此高版本：

1. 本程序使用的Laravel版本为5.6，官方的要求就是PHP >= 7.1.3

2. 因需要MySQL5.7的JSON特性，故使用MySQL >=5.7（不过好像我更新源码以后没有用json字段了...）