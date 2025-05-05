# NativePHP Electron 集成

这是一个用于NativePHP的Electron集成库，提供了PHP应用程序与Electron的无缝集成功能。

## 目录结构

```
├── src/
│   ├── command/           # 命令类
│   ├── facade/            # 门面类
│   ├── traits/            # 特性类
│   └── Service.php        # 服务类
├── config/                # 配置文件
├── composer.json
└── README.md
```

## 安装

你可以通过Composer安装这个包：

```bash
composer require yangweijie/thinkphp-electron
```

## 特性

### LaravelCommand Trait

本扩展包提供了一个 `LaravelCommand` trait，用于将 Laravel 风格的命令适配到 ThinkPHP 命令系统。使用此 trait，您可以轻松地将原有的 Laravel 命令类迁移到 ThinkPHP 框架中。

#### 使用方法

1. 将原有 Laravel 命令类的继承改为 `think\console\Command`
2. 使用 `LaravelCommand` trait
3. 保留原有的 `$signature` 和 `$description` 属性

```php
use think\console\Command;
use yangweijie\thinkElectron\traits\LaravelCommand;

class YourCommand extends Command
{
    use LaravelCommand;
    
    protected $signature = 'your:command {argument} {--option}';
    protected $description = '您的命令描述';
    
    public function handle()
    {
        // 您的命令逻辑
    }
}
```

更多详细信息，请查看 [LaravelCommand 使用说明](../thinkphp-package-tools/src/adapter/laravel/README.md)。

## 使用

更多使用说明和文档正在编写中...

## 许可证

本项目采用MIT许可证。详情请查看[LICENSE](LICENSE)文件。