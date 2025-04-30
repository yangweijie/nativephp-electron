<?php  
namespace yangweijie\thinkElectron\command;
  
use think\console\Command;  
use think\console\Input;  
use think\console\Output;  
use yangweijie\thinkElectron\traits\Installer;
  
class InstallCommand extends Command
{  
    use Installer;  
  
    protected function configure()  
    {  
        $this->setName('electron:install')  
            ->setDescription('Install all of the Electron resources')  
            ->addOption('force', null, null, 'Overwrite existing files')  
            ->addOption('installer', null, null, 'The package installer to use: npm, yarn or pnpm');  
    }  
  
    protected function execute(Input $input, Output $output)  
    {  
        // 参考原InstallCommand实现安装逻辑  
        // 但使用ThinkPHP的API  
    }
    
    // 在InstallCommand中  
    private function installComposerScript()  
    {  
        // 读取和修改composer.json来添加命令  
        $composerJson = json_decode(file_get_contents(root_path('composer.json')), true);  
        
        // 添加命令  
        $composerJson['scripts']['electron:dev'] = [  
            'Composer\\Config::disableProcessTimeout',   
            'npx concurrently -k "php think electron:serve" "npm run dev"'  
        ];  
        
        // 保存修改  
        file_put_contents(  
            root_path('composer.json'),  
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)  
        );  
    }
}