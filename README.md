# Laravel PHP Framework

http://laravelacademy.org/laravel-docs-5_3

Laravel规定了项目的目录结构，如下：
```
 app
 |---Commands 自定义命令
 |---Console Artisan命令
 |---Events 放置事件类
 |---Exceptions 异常处理器
 |---Listeners 事件监听器
 |---Http
     |---Controllers 控制器
     |---Middleware 中间件
     |---Service 自定义，数据封装等
     |---Model
 |---Services 自定义驱动
 |---Policies 授权策略类
 |---User.php 模型文件
 bootstrap 目录包含了少许文件用于框架的启动和自动载入配置，还有一个cache文件夹用于包含框架生成的启动文件以提高性能；
 config 配置
 database 数据库相关，包括迁移脚本和数据导入脚本
 public 静态资源目录
 resources
 |---lang
 |---views 视图
     |---welcome.blade.php
     |---home.blade.php
 storage 临时文件、日志文件等
 |---logs
 routes 路由
    |---api.php
    |---web.php
 tests
 vendor 第三方依赖库
 .env
 artisan 脚手架！重点工具
 composer.json
 server.php
 index.php
 
```


## Before
+ [VirtualBox](https://www.virtualbox.org) installed
+ [Vagrant](http://www.vagrantup.com) installed
+ [Composer](https://getcomposer.org) installed

## Quickstart

```
$ svn checkout  
$ cd crawler-job
```

check out to your branch

```
$ composer install || composer update
$ vagrant up
```


命名空间

+ php artisan app:name App

配置nginx

```
 server {
        listen 80;
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6
        root /Users/ahan/project/crawler-job/7.6;
        index index.html index.htm index.php;
        server_name cm.dev.crawler-job.com
        location / {
                autoindex on;
                if (!-e $request_filename){
                        rewrite . /index.php last;
                }
        }
        location /crawler-job/ {
            index  index.html index.htm index.php;
            if (!-e $request_filename){
                rewrite  ^/crawler-job/(.*)$  /crawler-job/index.php?s=$1  last;
            }
        }
        location ~ \.php$ {
                fastcgi_pass 127.0.0.1:9000;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
}
```

设置文件权限及代码同步限制：
bootstrap/cache, storage/, public/index.php 和.env不做同步
/bootstrap/cache 和 /storage/ 增加写权限

访问：
open http://m.dev.crawler-job.com/crawler-job/

或着

```
cd ./crawler-job
php artisan serve

```
open http://localhost:8000/


> Enjoy your work!

生产部署配置
----
```
 composer install --no-dev or
 composer update --no-dev
 生成key：     php artisan key:generate
 生成常用类缓存：php artisan optimize     解除：php artisan clear-compiled
 生成配置缓存：  php artisan config:cache 解除：php artisan config:clear
 生成路由缓存：  php artisan route:cache  解除：php artisan route:clear
 生成视图缓存：  自动生成                  解除：php artisan view:clear
```

配置文件都放在app/config目录，
可以通过`Config::get('filename.keyname.subkeyname')`读取。


Composer新增驱动
----
* composer require jenssegers/mongodb
* composer require predis/predis

* composer require barryvdh/laravel-debugbar --dev
* composer require barryvdh/laravel-ide-helper --dev
* composer require mpociot/laravel-test-factory-helper --dev

# 清除依赖
* composer remove barryvdh/laravel-debugbar --dev


# 新加入驱动需要在/Config/App.php中增加
* Jenssegers\Mongodb\MongodbServiceProvider::class,
* Barryvdh\Debugbar\ServiceProvider::Class,
* Mpociot\LaravelTestFactoryHelper\TestFactoryHelperServiceProvider::Class,
* Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::Class,


执行：
* php artisan ide-helper:generate
* php artisan ide-helper:meta


路由
----

所有的路由配置都放在`routes/api.php`（访问路由前请加api/）和`routes/web.php`文件中。所有的配置均通过Facde类Route实现。
简单控制器可以使用匿名函数实现，复杂的可以使用普通类。

### 简单示例

简单GET请求：

```
// 匿名函数
Route::get('hello', function() { //获取
    return 'Hello';
});
// 控制器类
Route::post('hello', 'HelloController@store');//添加
Route::put('book/1', 'BookController@update');//修改
Route::delete('book/1', 'BookController@delete');//删除
```

注意，控制器约定为Controller结尾，但不做强制要求，你可以随意命名。@后面是方法名。

### 路径参数

Laravel提供了简单明了的方法来获取路径参数。

简单示例：

```
Route::delete('hello/{name}', function($name) {
    if (!$name) $name = 'world';

    return 'Hello, ' . $name;
});
```

我们还可以对路径参数进行检查：

```
Route::get('books/{id}', function($id) {
    // ...
})->where('id', '[0-9]+');
```

多参数检查：

```
Route::get('users/{user_id}/books/{book_id}', function($user_id, $book_id) {
    // ...
})->where([
    'user_id' => '[0-9]+',
    'book_id' => '[0-9]+',
]);
```

在其他地方（非路由器）获取路由参数：

```
$id = Route::input('id');
```

### 路由过滤器（拦截器）

我们可以使用`Route::filter`定义拦截器：

```
Route::filter('check.signature', function() {
    // ...
});
```

使用拦截器：

```
Route::get('books/{id}', ['before' => 'check.signature', function() {
    // ...
});
```

注意：任何拦截器返回字符串或者Response对象，则整个请求过程结束，返回的内容会发给客户端。

### 路由组

可以对一组路由规则进行分组，正对组设置过滤器：

```
Route::group(['before' => 'auth'], function() {
    Route::get('/', function() {
        // ...
    });
    Route::get('user', function() {
        // ...
    });
});
```

上述路由组内所有请求都需要进行auth过滤器/拦截器过滤。

### REST资源路由

Laravel为REST风格API提供个更为方便的路由方式：

```
Route::resource('photo', 'PhotoController');
```

映射结果可以通过Laravel提供artisan工具查看：

```
sh
➜  php artisan route:list

+--------+---------------------------+--------------+------------------------+
| Domain | URI                       | Name         | Action                 |
+--------+---------------------------+--------------+------------------------+
|        | GET|HEAD book             | book.index   | BookController@index   |
|        | GET|HEAD book/create      | book.create  | BookController@create  |
|        | POST book                 | book.store   | BookController@store   |
|        | GET|HEAD book/{book}      | book.show    | BookController@show    |
|        | GET|HEAD book/{book}/edit | book.edit    | BookController@edit    |
|        | PUT book/{book}           | book.update  | BookController@update  |
|        | PATCH book/{book}         |              | BookController@update  |
|        | DELETE book/{book}        | book.destroy | BookController@destroy |
+--------+---------------------------+--------------+------------------------+
```

请注意默认的映射规则。你还可以传入一个数组作为第三个参数，使用only、except来限制映射方法。



### 控制器

Laravel对控制器没有任何限制，任何类都可以作为控制器，可以说是相当的灵活。

唯一需要说明的是，我们可以使用Laravel的脚手架artisan来快速生成控制器：

```sh
php artisan controller:make BookController
```

这样，Laravel会帮我们在`app/controllers`目录生成BookController.php文件。

更多信息请移步[官方文档](http://laravel.com/docs/4.2/routing)。

请求信息
--------

### 输入参数

获取请求参数使用Input。简单用法如下：

```
$name = Input::get('name');
$name = Input::get('name', 'default');
```

Input可以读取GET、POST和JSON格式请求参数。

检查参数是否存在：

```
if (Input::has('name')) {
    // ...
}
```

获取所有参数：

```
$input = Input::all();
```

获取数组参数：

```
$input = Input::get('books.1.name');
```

### Cookie

获取Cookie：

```
$value = Cookie::get('name');
```

发送Cookie：

```
$response = Response::make('hehe');
$response->withCookie(Cooke::make('name', 'value', $minutes));
```

### Session

session的永久保存（在不过期范围内,memcached 默认最大30天）：

```
Session::put('key', 'value');
```
 
//等同于PHP的原生session

```
$_SESSION['key'] = 'value';
```
 
//get操作

```
$value = Session::get('key', 'default');
```
 
//去除操作并删除，类似pop概念

```
$value = Session::pull('key', 'default');
```
 
//检测是否存在key

```
Session::has('users');
```
 
//删除key

```
Session::forget('key');
```

这个对应只要session不过期，基本上是永久保存，下次http请求也是存在的。不同于下面的flash概念。
laravel的session中flash概念

### 其他信息

```
$uri = Request::path();
$method = Response::method();
$url = Request::url();
$header = Request::header('name');
$server_info = Request::server('PATH_INFO');
$is_ajax = Request::ajax();
```

更多信息请移步[官方文档](http://laravel.com/docs/4.2/requests)。

响应对象
--------

初始化：

```
$response = Response::make($contents, $statusCode);
```
设置头信息：

```
$response->header('Content-Type', $value);
```
重定向：

```
return Redirect::to('user/login');
```
输出JSON：

```
$response = Response::json($contents, $statusCode);
```

定时任务-JOB
----

### 生成job文件
* php artisan make:command SendEmails --command=command:send
### 运行job文件：
* php artisan command:send



模板
----
所有模板均以`.blade.php`结尾，约定放在`app/views`目录。

### 骨架模板示例
```
<!-- 存储在app/views/layouts/master.blade.php -->

<html>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
```

### 扩展骨架示例
```
@extends('layouts.master')

@section('sidebar')
    <p>This is appended to the master sidebar.</p>
@stop

@section('content')
    <p>This is my body content.</p>
@stop
```

在控制器结束的时候使用如下方法编译模板：
```
return View:make('hello', []);
```
make的第二个参数是要模板中使用的变量。

### 输出语法
```
Hello, {{{ $name }}}.
Hello, {{ $name }}.
```
三个大括号会转义。

### 判断语法
```
@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif

@unless (Auth::check())
    You are not signed in.
@endunless
```

### 循环语法
```
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor

@foreach ($users as $user)
    <p>This is user {{ $user->id }}</p>
@endforeach

@forelse($users as $user)
      <li>{{ $user->name }}</li>
@empty
      <p>No users</p>
@endforelse

@while (true)
    <p>I'm looping forever.</p>
@endwhile
```


更多信息请移步[官方文档](http://laravelacademy.org/laravel-docs-5_3)。


为了防止svn提交错误大家执行下：
* svn propset svn:ignore 'vendor'
本地运行时执行
* composer update


