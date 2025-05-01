<?php
namespace yangweijie\thinkElectron;
  
use think\Service as BaseService;
use yangweijie\thinkElectron\command\BuildCommand;
use yangweijie\thinkElectron\command\DevelopCommand;
use yangweijie\thinkElectron\command\InstallCommand;
use yangweijie\thinkElectron\command\PublishCommand;
use yangweijie\thinkElectron\command\VendorPublishCommand;
use yangweijie\thinkElectron\traits\LaravelService;
use yangweijie\thinkElectron\updater\UpdaterManager;

class Service extends BaseService  
{  
    use LaravelService;
    public function register(): void
    {  
        // 注册配置  
        $this->app->bind('electron.updater', function () {  
            return new UpdaterManager($this->app);  
        });  
    }
  
    public function boot(): void
    {  
        // 注册命令  
        if ($this->app->runningInConsole()) {  
            $this->commands([  
                // 移植后的命令类  
                BuildCommand::class,
                DevelopCommand::class,
                InstallCommand::class,
                PublishCommand::class,
                VendorPublishCommand::class,
            ]);  
        }  

        // 发布配置
        $this->publishes([
            __DIR__ . '/../config/electron.php' => config_path('electron.php')
        ], 'electron-config');
    }
}