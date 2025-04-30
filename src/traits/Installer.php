<?php  
namespace yangweijie\ThinkElectron\traits;  
  
trait Installer  
{  
    use ExecuteCommand;  
  
    protected function installNPMDependencies(bool $force, ?string $installer = 'npm', bool $withoutInteraction = false): void  
    {  
        // 修改为使用ThinkPHP的API而非Laravel Prompts  
    }  
      
    // 其他方法的移植...  
}