# LaravelCommand Trait 使用说明

## 简介

`LaravelCommand` trait 是一个用于将 Laravel 风格的命令适配到 ThinkPHP 命令系统的工具。使用此 trait，您可以轻松地将原有的 Laravel 命令类迁移到 ThinkPHP 框架中，而无需重写大量代码。

## 功能特点

- 自动将 Laravel 的 `signature` 格式转换为 ThinkPHP 的命令配置
- 支持参数和选项的自动映射
- 保留 Laravel 命令的熟悉接口（如 `argument()`、`option()`、`info()` 等方法）
- 自动处理依赖注入

## 使用方法

### 步骤 1: 修改命令类继承

将原有 Laravel 命令类的继承从 `Illuminate\Console\Command` 改为 `think\console\Command`：

```php
// 修改前
class YourCommand extends \Illuminate\Console\Command

// 修改后
class YourCommand extends \think\console\Command
```

### 步骤 2: 使用 LaravelCommand trait

在命令类中使用 `LaravelCommand` trait：

```php
use yangweijie\thinkElectron\traits\LaravelCommand;

class YourCommand extends \think\console\Command
{
    use LaravelCommand;
    
    // 保留原有的 signature 和 description
    protected $signature = 'your:command {argument} {--option}';
    protected $description = '您的命令描述';
    
    // 保留原有的 handle 方法
    public function handle()
    {
        // 您的命令逻辑
    }
}
```

### 步骤 3: 注册命令

在 ThinkPHP 的服务提供者中注册您的命令：

```php
// 在 Service.php 中
public function boot()
{
    if ($this->app->runningInConsole()) {
        $this->commands([
            YourCommand::class,
        ]);
    }
}
```

## 示例

查看 `src/command/ExampleCommand.php` 文件，了解完整的使用示例：

```php
<?php

namespace yangweijie\thinkElectron\command;

use think\console\Command;
use yangweijie\thinkElectron\traits\LaravelCommand;

class ExampleCommand extends Command
{
    use LaravelCommand;
    
    protected $signature = 'electron:example {name : 示例参数} {--force : 是否强制执行}'; 
    protected $description = '这是一个使用LaravelCommand trait的示例命令';
    
    public function handle()
    {
        $name = $this->argument('name');
        $force = $this->option('force');
        
        $this->info("执行示例命令: {$name}");
        
        if ($force) {
            $this->warn('使用了强制模式');
        }
        
        $this->success('命令执行成功！');
        
        return 0;
    }
}
```

## 支持的方法

`LaravelCommand` trait 提供了以下 Laravel 命令常用方法：

- `argument($key = null)`: 获取参数值
- `option($key = null)`: 获取选项值
- `line($string, $style = null)`: 输出一行信息
- `info($string)`: 输出信息（蓝色）
- `success($string)`: 输出成功信息（绿色）
- `error($string)`: 输出错误信息（红色）
- `warn($string)`: 输出警告信息（黄色）
- `confirm($question, $default = false)`: 询问确认
- `choice($question, array $choices, $default = null)`: 询问选择
- `ask($question, $default = null)`: 询问输入

## 注意事项

- 确保您的命令类中定义了 `$signature` 和 `$description` 属性
- 如果您的命令依赖其他服务，可以在 `handle` 方法中使用类型提示，trait 会自动尝试解析依赖
- 此 trait 主要适用于简单的命令迁移，对于复杂的命令可能需要额外的调整