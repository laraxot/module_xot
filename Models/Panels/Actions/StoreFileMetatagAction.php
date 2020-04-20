<?php
namespace Modules\Xot\Models\Panels\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StoreFileMetatagAction extends XotBasePanelAction {
     public $onContainer = false; //per tutte le righe, esempio xls
     public $onItem = true; //per riga selezionata
     public $icon = '<i class="far fa-save"></i>';

     public function handle(){
        $data=$this->row->toArray();
        $content=var_export($data, true);
        $filename=base_path('config/'.$this->row->tennant_name.'/metatag.php');
        $filename=str_replace(['\\','/'],[DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR],$filename);
        //return $filename;
        $content='<'.'?php'.chr(13).'return '.$content.';';
        File::put($filename, $content);

     }
}