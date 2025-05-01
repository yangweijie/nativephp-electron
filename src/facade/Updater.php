<?php  
namespace yangweijie\thinkElectron\facade;
  
use think\Facade;

/**
 * @method static array builderOptions()
 * @method static array environmentVariables()
 */
class Updater extends Facade  
{  
    protected static function getFacadeClass(): string
    {  
        return 'electron.updater';  
    }  
}