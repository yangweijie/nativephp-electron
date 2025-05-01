<?php

namespace yangweijie\thinkElectron\command;

use think\console\Command;
use yangweijie\thinkElectron\traits\LaravelCommand;

class ExampleCommand extends Command
{
    use LaravelCommand;
    
    /**
     * 命令签名
     *
     * @var string
     */
    protected $signature = 'electron:example {name : 示例参数} {--force : 是否强制执行}'; 
    
    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '这是一个使用LaravelCommand trait的示例命令';
    
    /**
     * 执行命令
     *
     * @return int
     */
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