# ci
ci是一个基于codeigniter2.2.6开发的快速后台开发框架
backend文件夹为ci扩展项目
项目结构:backend

主要特性包括：
<ul>
  <li>后台管理员登录</li>
  <li>修改管理员密码</li>
  <li>浏览前台(前台需要自己根据实际扩展)</li>
  <li>网站关键字设置，包括网站标题,网站关键字，网站描述</li>
  <li>管理管理员，添加管理员，修改管理员，管理员权限管理</li>
  <li>管理员管理后台菜单栏目</li>
  <li>数据备份，备份到服务器下，或者下载备份文件</li>
  <li>文章管理</li>
  <li>文章分类管理，无限极文章分类添加，删除，修改</li>
  <li>文章列表管理，文章内容添加，删除，修改</li>
  <li>无缝集成图片上传控件</li>
  <li>无缝集成图片预览控件</li>
  <li>无缝集成日期预览控件</li>
  <li>无缝集成文本编辑器控件</li>
  <li>网站单页管理</li>
</ul>

目录结构

<pre>
ci  WEB部署目录
├─application              前台项目
│  ├─cache                 缓存目录
│  ├─config                应用配置目录
│  │  ├─autoload.php       默认访问配置文件
│  │  ├─config.php         核心配置文件
│  │  └─database.php       数据库配置
│  ├─controller            控制器目录
│  ├─errors                错误页面
│  ├─helpers               助手方法文件目录
│  ├─language              语言文件目录
│  ├─libraries             扩展库目录
│  ├─logs                  日志目录
│  ├─models                模型文件目录
│  └─view                  视图文件目录        
├─backend                  ci扩展项目目录
│  ├─application           后台项目
│  │  ├─cache              缓存目录
│  │  ├─config             应用配置目录
│  │  │  ├─autoload.php    默认访问配置文件
│  │  │  ├─config.php      核心配置文件
│  │  │  └─database.php    数据库配置
│  │  ├─controller         控制器目录
│  │  ├─errors             错误页面
│  │  ├─helpers            助手方法文件目录
│  │  ├─language           语言文件目录
│  │  ├─libraries          扩展库目录
│  │  ├─logs               日志目录
│  │  ├─models             模型文件目录
│  │  └─view               视图文件目录
│  ├─backup                数据库备份文件目录
│  ├─css                   样式文件目录
│  ├─fancybox              图片插件目录
│  ├─js                    js文件目录
│  ├─kindeditor            文本编辑器目录
│  ├─upload                上传图片目录
│  └─index.php             后台的入口文件
│
├─system                   codeigniter框架系统目录
│  ├─lang                  语言文件目录
│  ├─library               框架类库目录
│  │
│  ├─core                  codeigniter框架核心目录
│  ├─database              codeigniter框架数据库处理目录
│  ├─fonts                 字体目录
│  ├─helper                codeigniter框架助手方法文件目录
│  ├─language              语言文件目录
│  └─libraries             扩展库目录
│
├─LICENSE.txt              授权说明文件
├─README.md                README 文件
└─index.php                前台的入口文件
</pre>

测试地址 http://local.ci.com/backend/index.php

用户名：administrator

密　码：123456

<h2>作者长期从事PHP开发</h2>
<pre>
<ul>
<li>昵称:datou</li>
<li>qq:2323178881</li>
<li>Tel:18329123270</li>
<li>微信:datou-leo</li>
<li>ci使用开发群:646864389</li>
</ul>
<pre>
