<?php

namespace native\thinkElectron\command;

use think\console\Command;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelService;

class VendorPublishCommand extends Command
{
    use LaravelCommand;

    public function __construct(){
        $this->signature = 'vendor:publish 
                            {--force : 强制覆盖已存在的文件}
                            {--provider= : 指定服务提供者}
                            {--tag= : 指定标签}';
        parent::__construct();
    }

    /**
     * 命令描述
     *
     * @var string
     */
    protected string $description = '发布服务提供者的资源文件';

    /**
     * 执行命令
     *
     * @return void
     */
    public function handle(): void
    {
        $provider = $this->option('provider');
        $tag = $this->option('tag');
        $force = $this->option('force');

        if (empty($provider) && empty($tag)) {
            $this->info('发布所有资源...');
        } elseif (!empty($provider)) {
            $this->info("发布 {$provider} 的资源...");
        } elseif (!empty($tag)) {
            $this->info("发布标签 {$tag} 的资源...");
        }

        // 调用 LaravelService 的静态方法发布资源
        call_user_func([LaravelService::class, 'publishResources'], $provider, $tag, $force);

        $this->info('资源发布完成！');
    }
}