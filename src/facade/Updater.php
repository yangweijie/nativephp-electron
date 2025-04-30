<?php  
namespace yangweijie\thinkElectron\facade;
  
use think\Facade;  
  
class Updater extends Facade  
{  
    protected static function getFacadeClass()  
    {  
        return 'electron.updater';  
    }  
}