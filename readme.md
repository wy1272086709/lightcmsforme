# LightCMS
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eddy8/lightCMS/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eddy8/lightCMS/?branch=master)    [![StyleCI](https://github.styleci.io/repos/175428969/shield?branch=master)](https://github.styleci.io/repos/175428969)    [![Build Status](https://www.travis-ci.org/eddy8/lightCMS.svg?branch=master)](https://www.travis-ci.org/eddy8/lightCMS)    [![PHP Version](https://img.shields.io/badge/php-%3E%3D7.0-8892BF.svg)](http://www.php.net/)

## 项目简介
`lightCMS`是一个轻量级的`CMS`系统，也可以作为一个通用的后台管理框架使用。`lightCMS`集成了用户管理、权限管理、日志管理、菜单管理等后台管理框架的通用功能，同时也提供模型管理、分类管理等`CMS`系统中常用的功能。`lightCMS`的**代码一键生成**功能可以快速对特定模型生成增删改查代码，极大提高开发效率。

`lightCMS`基于`Laravel 5.5`开发，前端框架基于`layui`。

`LightCMS&Laravel`学习交流QQ群：**972796921**

**注意：如果你只需要一个通用的后台管理框架，使用`admin`分支即可。**

## 功能点一览
后台：
* 基于`RBAC`的权限管理
* 管理员、日志、菜单管理
* 分类管理
* 配置管理
* 模型、模型字段、模型内容管理（后台可自定义业务模型，方便垂直行业快速开发）
* 会员管理
* 评论管理
* 基于Tire算法的敏感词过滤系统
* 普通模型增删改查代码一键生成

前台：
* 用户注册登录（包括微信、QQ、微博三方登录）
* 模型内容详情页、列表页
* 评论相关

更多功能待你发现~

## 后台预览
![首页](https://user-images.githubusercontent.com/2555476/54804611-16fa4900-4caf-11e9-885e-7f5c0dac7ce4.png)

![系统管理](https://user-images.githubusercontent.com/2555476/54804599-0ea20e00-4caf-11e9-8d10-526aca358916.png)

## 系统环境
`linux/windows & nginx/apache/iis & mysql 5.5+ & php 7.0+`

## 系统部署

### 获取代码并安装依赖
首先请确保系统已安装好[composer](https://getcomposer.org/)。
```bash
cd /data/www
git clone git_repository_url
cd lightCMS
composer install
```
### 系统配置并初始化
新建一份环境配置，并配置好数据库等相关配置:
```
copy .env.example .env.pro
```
初始化系统：
```
php artisan migrate --seed
```

### 配置Web服务器（此处以`Nginx`为例）
```
server {
    listen 80;
    server_name light.com;
    root /data/www/lightCMS/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param   APP_ENV pro; #不同配置对应不同的环境配置文件。比如此处应用会加载.env.pro文件。
        include fastcgi_params;
    }
}
```

### 后台登陆
后台访问地址：`/admin/login`

默认用户（此用户为超级用户，不受权限管理限制）：admin/admin

## 权限管理
基于角色的权限管理。只需新建好角色，给对应的角色分配好相应的权限，最后给用户指定角色即可。`lightCMS`中权限的概念其实就是菜单，一条菜单对应一个`laravel`的路由，也就是一个具体的操作。

### 菜单自动获取
只需要按约定方式写好指定路由的控制器注释，则可在[菜单管理](/admin/menus)页面自动添加/更新对应的菜单信息。例如：
```php
/**
 * 角色管理-角色列表
 *
 * 取方法的第一行注释作为菜单的名称、分组名。注释格式：分组名称-菜单名称。
 * 未写分组名称，则直接作为菜单名称，分组名为空。
 * 未写注释则选用uri作为菜单名，分组名为空。
 */
public function index()
{
    $this->breadcrumb[] = ['title' => '角色列表', 'url' => ''];
    return view('admin.role.index', ['breadcrumb' => $this->breadcrumb]);
}
```

需要注意的是，程序可以自动获取菜单，但是菜单的层级关系还是需要在后台手动配置的。

## 配置管理
首先需要将`config/light.php`配置文件中的`light_config`设置为`true`：

然后只需在[配置管理](/admin/configs)页面新增配置项或编辑已存在配置项，则在应用中可以直接使用`laravel`的配置获取函数`config`获取指定配置，例如：
```php
// 获取 key 为 SITE_NAME 的配置项值
$siteName = config('light_config.SITE_NAME');
```

## 模型管理
`lightCMS`支持在后台直接创建模型，并可对模型的表字段进行自定义设置。设置完模型字段后，就不需要做其它工作了，模型的增删改查功能系统已经内置。

这里说明下模型的表单验证及后端的保存和更新处理。如果有自定义表单验证需求，只需在`app/Http/Request/Admin/Entity`目录下创建模型的表单请求验证类即可。类名的命名规则：**模型名+Request**。例如`User`模型对应的表单请求验证类为`UserRequest`。

同理，如果想自定义模型的保存和更新处理逻辑，只需在`app/Http/Controllers/Admin/Entity`目录下创建模型的控制器类即可，`save`和`update`方法实现可参考`app/Http/Controllers/Admin/ContentController`。类名的命名规则：**模型名+Controller**。例如`User`模型对应的控制器类为`UserController`。

## 系统日志
`lightCMS`集成了一套简单的日志系统，默认情况下记录后台的所有操作相关信息，具体实现可以参考`Log`中间件。

## 代码一键生成
对于一个普通的模型，管理后台通常有增删改查相关的业务需求。`lightCMS`拥有一键生成相关代码的能力，在建好模型的数据库表结构后，可以使用如下`artisan`命令生成相关代码：
```bash
# config 为模型名称 配置 为模型中文名称
php artisan light:basic config 配置
```
成功执行完成后，会创建如下文件（注意：相关目录需要有写入权限）：

* routes/auto/config.php
路由：包含模型增删改查相关路由，应用会自动加载`routes/auto/`目录下的路由。
* app/Model/Admin/Config.php
模型：`$searchField`属性用来配置搜索字段，`$listField`用来配置列表视图中需要展示哪些字段数据。
* app/Repository/Admin/ConfigRepository.php
模型服务层：默认有一个`list`方法，该方法用来返回列表数据。需要注意的是如果列表中的数据不能和数据库字段数据直接对应，则可对数据库字段数据做相应转换，可参考`list`方法中的`transform`部分。
* app/Http/Controllers/Admin/ConfigController.php
控制器：默认有一个`$formNames`属性，用来配置新增/编辑表单请求字段的白名单。此属性必需配置，否则获取不到表单数据。参考 [request 对象的 only 方法](https://laravel.com/docs/5.5/requests#retrieving-input)
* app/Http/Requests/Admin/ConfigRequest.php
表单请求类：可在此类中编写表单验证规则，参考 [Form Request Validation](https://laravel.com/docs/5.5/validation#form-request-validation)
* resources/views/admin/config/index.blade.php
列表视图：列表数据、搜索表单。
* resources/views/admin/config/index.blade.php
新增/编辑视图：只列出了基本架构，需要自定义相关字段的表单展示。参考 [layui form](https://www.layui.com/doc/element/form.html)

最后，如果想让生成的路由展示在菜单中，只需在[菜单管理](/admin/menus)页面点击**自动更新菜单**即可。

## 敏感词检测
如果需要对发表的内容（文章、评论等）进行内容审查，则可直接调用`LightCMS`提供的`checkSensitiveWords`函数即可。示例如下：
```php
$result = checkSensitiveWords('出售正品枪支');
print_r($result);
/*
[
    "售 出售 枪",
    "正品枪支"
]
*/
```

## 前台相关
### 用户注册登录
`LightCMS`集成了一套简单的用户注册登录系统，支持微信、QQ、微博三方登录。三方登录相关配置请参考`config/light.php`。

## TODO
* 模版管理+模版标签

## 完善中。。。

## 说明
有问题可以提 issue ，为项目贡献代码可以提 pull request

如果该项目对于您有所帮助，欢迎打赏支持项目开发 ^_^

<img src="https://user-images.githubusercontent.com/2555476/54803956-a7835a00-4cac-11e9-9a10-ede3cfa6cef0.JPG" width="160" height="200" />