<?php

namespace Modules\Xot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

use Illuminate\Database\Eloquent\Relations\Relation; // per dizionario morph 

use Illuminate\Support\Facades\DB;

use Modules\Xot\Providers\XotBaseServiceProvider;


class XotServiceProvider extends XotBaseServiceProvider{
    protected $module_dir= __DIR__;
    protected $module_ns=__NAMESPACE__;
    public $module_name='xot';  

    public function bootCallback(){
    	$this->mergeConfigs();
    	/*
    	$auth=tenantConfig('auth');
    	$db=tenantConfig('database');
    	*/
    	$map=config('xra.model');
    	Relation::morphMap($map);
    	//\DB::reconnect();
    }//end bootCallback

    public function mergeConfigs(){
    	$configs=['database','filesystems','auth','metatag','services','xra'];
    	foreach($configs as $v){
    		$tmp=tenantConfig($v);
    		//ddd($tmp);
    	}
        //DB::purge('mysql');//Call to a member function prepare() on null
        //DB::purge('liveuser_general');
        DB::reconnect();
    }//end mergeConfigs
    
}//end class
