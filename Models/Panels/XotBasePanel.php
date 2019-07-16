<?php
namespace Modules\Xot\Models\Panels;

//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;
use Illuminate\Support\Str;

//---- Traits ----
//use Modules\Extend\Traits\Updater;

abstract class XotBasePanel  
{

	public function rules(){
		$fields=$this->fields();
		$rules=collect($fields)->map(function ($item){
			return [$item->name=>isset($item->rules)?$item->rules:''];
		})->collapse()->all();
		return $rules;
	}

	public function rulesMessages(){
		//ddd(self::$model);//Access to undeclared static property: 
		//ddd(static::$model);//Modules\Food\Models\RestaurantOwner
		//ddd($this::$model);//Modules\Food\Models\RestaurantOwner
		//ddd(collect(config('xra.model'))->search(static::$model));
		
		$lang=\App::getLocale();
		$rules_msg_fields=collect($this->fields())->filter(function ($value, $key) use ($lang){
			return (isset($value->rules_messages) && isset($value->rules_messages[$lang]));
		})
		->map(function ($item) use($lang) {
			$tmp=[];
			foreach($item->rules_messages[$lang] as $k=>$v){
				$tmp[$item->name.'.'.$k]=$v;
			}
			return $tmp;
		})
		->collapse()
		->all();
		$mod=Str::before(Str::after(static::$model,'Modules\\'),'\\');
		$mod=strtolower($mod);
		$name=Str::snake(class_basename(static::$model));
		$trans_ns=$mod.'::'.$name.'__rules_messages';
		//ddd($trans_ns);//food::restaurant_owner__rules_messages
		$rules_msg= trans($trans_ns);
		if (!\is_array($rules_msg)) $rules_msg=[];
		$rules_msg_generic = trans('extend::generic');
		if (!\is_array($rules_msg_generic)) $rules_msg_generic=[];
		$msg=[];
		//$msg = \array_merge($msg,$rules_msg_generic); 
		//$msg = \array_merge($msg, $rules_msg);
		$msg=\array_merge($msg, $rules_msg_fields);
		return $msg;
	}

	public function getXotModelName(){
		return collect(config('xra.model'))->search(static::$model);
	}



}
