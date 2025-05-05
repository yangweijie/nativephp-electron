<?php
namespace native\thinkElectron;
  
use native\thinkElectron\command\BuildCommand;
use native\thinkElectron\command\DevelopCommand;
use native\thinkElectron\command\InstallCommand;
use native\thinkElectron\command\PublishCommand;
use native\thinkElectron\command\VendorPublishCommand;
use native\thinkElectron\updater\UpdaterManager;
use yangweijie\thinkphpPackageTools\concerns\adapter\laravel\LaravelService;
use yangweijie\thinkphpPackageTools\Package;
use yangweijie\thinkphpPackageTools\PackageService;

class Service extends PackageService
{  
    use LaravelService;
    public function registeringPackage(): void
    {  
        // 注册配置  
        $this->app->bind('nativephp.updater', function () {
            return new UpdaterManager($this->app);  
        });  
    }
  
    public function bootingPackage(): void
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
            __DIR__ . '/../config/nativephp.php' => config_path('nativephp.php')
        ]);
    }

    public function configurePackage(Package $package): void
    {
        $package->name('nativephp-electron')
            ->hasConfigFile('nativephp')
            ->hasCommands([
                BuildCommand::class,
                DevelopCommand::class,
                InstallCommand::class,
                PublishCommand::class,
                VendorPublishCommand::class,
            ])
        ;
    }
}