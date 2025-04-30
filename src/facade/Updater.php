<?php  
namespace yangweijie\ThinkElectron\facade;  
  
use think\Facade;  
  
class Updater extends Facade  
{  
    protected static function getFacadeClass()  
    {  
        return 'electron.updater';  
    }  
}