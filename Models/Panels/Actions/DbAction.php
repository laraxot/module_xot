<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\File;
use Modules\Xot\Services\ArrayService;
use Modules\Xot\Services\ModelService;
use Illuminate\Database\QueryException;
use Modules\Theme\Services\ThemeService;

//-------- models -----------

//-------- services --------

//-------- bases -----------

/**
 * Class CloneAction.
 */
class DbAction extends XotBasePanelAction
{
    public bool $onItem = true;

    public string $icon = '<i class="fas fa-database"></i>';

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(){
        //k$database=config('database');
        $data=$this->getAllTablesAndFields();
        $view='xot::admin.home.acts.db';
        $view_params=[
            'view'=>$view,
            'rows'=>$data,
        ];
        return view()->make($view,$view_params);

   }

   public function postHandle(){
        $search=request('search');
        $data=$this->getAllTablesAndFields();
        $data=$data->map(function($item) use($search){
            $item['sql']=$this->makeSql($item,$search);
            return $item;
        });
        $model=$this->getModel();
        $model_service=ModelService::make()->setModel($model);

        foreach($data as $v){
            try{
                $res=$model_service->select($v['sql']);
            }catch(QueryException $e){
                echo '<pre>'.$v['sql'].'</pre>';
                echo '<pre>'.$e->getMessage().'</pre>';
                dddx('a');
            }
            if(count($res)>0){
                echo '<hr>';
                echo '<h3>'.$v['name'].'</h3>';
                echo '<h3>N ['.count($res).'] Results</h3>';
                echo ArrayService::make()->setArray($res)->toHtml();
                //echo '<pre>'.print_r($res,true).'</pre>';
            }
            //if($res!=null){
            //    dddx($res);
            //}
        }
        echo '<h3>+Done Searching ['.$search.']</h3>';
   }

   public function makeSql($item,$search){
        $sql='select * from '.$item['name'].'
        where ';
        $where=[];
        foreach($item['fields'] as $field){
            $name=$field['name'];
            $name='`'.addslashes($name).'`';


            switch($field['type']){
                case 'string':
                    $where[]=$name.' like "%'.$search.'%"';
                    break;
                default:
                $where[]=$name.' = "'.$search.'"';
                break;
            }
        }
       return $sql.implode(chr(13).chr(10).' OR ', $where);
   }

   public function getModel(){
        $module_name=$this->panel->getModuleName();
        $module_path=Module::getModulePath($module_name);
        $module_models_path=$module_path.'/Models';
        $models=File::files($module_models_path);
        $first_model_file=$models[0];
        $first_model_class='Modules\\'.$module_name.'\Models\\'.Str::before($first_model_file->getBasename(),'.php');
        $first_model=app($first_model_class);
        return $first_model;
   }

   public function getAllTablesAndFields():Collection{
        $first_model=$this->getModel();
        $data=ModelService::make()->setModel($first_model)->getAllTablesAndFields();
        return $data;
   }

}