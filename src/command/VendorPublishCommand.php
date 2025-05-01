<?php

namespace yangweijie\thinkElectron\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use yangweijie\thinkElectron\traits\LaravelCommand;
use yangweijie\thinkElectron\traits\LaravelService;

class VendorPublishCommand extends Command
{
    use LaravelCommand;

    /**
     * 命令签名
     *
     * @var string
     */
    protected $signature = 'vendor:publish 
                            {--force : 强制覆盖已存在的文件}
                            {--provider= : 指定服务提供者}
                            {--tag= : 指定标签}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '发布服务提供者的资源文件';

    /**
     * 执行命令
     *
     * @return void
     */
    public function handle()
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