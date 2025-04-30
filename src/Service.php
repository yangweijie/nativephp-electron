<?php
namespace YourNamespace\ThinkElectron;  
  
use think\Service as BaseService;  
  
class Service extends BaseService  
{  
    public function register()  
    {  
        // 注册配置  
        $this->app->bind('electron.updater', function () {  
            return new UpdaterManager($this->app);  
        });  
          
        // 合并配置  
        $this->mergeConfigFrom(__DIR__ . '/../config/electron.php', 'electron');  
    }  
  
    public function boot()  
    {  
        // 注册命令  
        if ($this->app->runningInConsole()) {  
            $this->commands([  
                // 移植后的命令类  
            ]);  
        }  
          
        // 发布配置  
        $this->publishes([  
            __DIR__ . '/../config/electron.php' => config_path('electron.php')  
        ], 'electron-config');  
    }  
}