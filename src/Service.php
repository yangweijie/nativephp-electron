<?php
namespace native\thinkElectron;
  
use think\Service as BaseService;
use native\thinkElectron\command\BuildCommand;
use native\thinkElectron\command\DevelopCommand;
use native\thinkElectron\command\InstallCommand;
use native\thinkElectron\command\PublishCommand;
use native\thinkElectron\command\VendorPublishCommand;
use native\thinkElectron\traits\LaravelService;
use native\thinkElectron\updater\UpdaterManager;

class Service extends BaseService  
{  
    use LaravelService;
    public function register(): void
    {  
        // 注册配置  
        $this->app->bind('nativephp.updater', function () {
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
            __DIR__ . '/../config/config.php' => config_path('nativephp.php')
        ]);
    }
}