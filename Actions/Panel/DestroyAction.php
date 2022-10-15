<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Panel;

use Modules\Xot\Contracts\PanelContract;
use Spatie\QueueableAction\QueueableAction;

class DestroyAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(PanelContract $panel, array $data): PanelContract
    {
       
        $row = $panel->getRow();
        //viene chiamata quest'azione con i dati del pannello da cancellare e richiesta di distruzione
        //la chiamata all'Azione proviene da:
        //XotPanelController.php metodo magico __call con argomenti destroy e xotRequest
        //ContainersController.php sempre con stessi argomenti di XotPanelController.php
        //ContainersController.php funzione __callRouteAct con argomenti:
        /* 0 => "destroy"
        1 => array:8 [
          0 => "intellinet"
          1 => "it"
          2 => "profiles"
          3 => "1"
          4 => "reports"
          5 => "3"
          6 => "keywords"
          7 => "8" */
        //quindi la route da distruggere
        //PanelMiddleware.php di Cms

        //a rules passi il parametro act=>edit
        //rules prende i campi del pannello del modello da cancellare
        //Esempio (nel caso di belongsToMany su Profile->Reports->Keywords):
        /*0 => {#2245
            +"type": "Text"
            +"name": "id"
            +"comment": "not in Doctrine"
          }
          1 => {#2200
            +"type": "Text"
            +"name": "keyword"
            +"comment": "not in Doctrine"
          }*/
        //Poi $act viene impostato a null
        //essendo null diverso da stringa
        //entra nella funzione che restituire SOLO i campi non except
        //in caso di campi ['Cell', 'CellLabel'] guarda se ci sono sotto-campi
        //poi inizia a restituire le regole
        //se non esistono regole nel campo la mette vuota
        //se le regole sono pivot_rules ti restituisce le regole per disassociare dalla tabella pivot CREDO
        //ma comunque non c'entra con il caso della belongsToMany
        
        //alla fine restituisce il panel a XotPanelController.php che ri-sputa fuori il panel
        //dopo aver richiamato \Modules\Xot\Actions\!!!MODEL!!!\DestroyAction
        //passando modello da cancellare ($row), richiesta dalla route($data) e nomi dei campi da cancellare ($rules)



        $rules = $panel->rules(['act' => 'edit']);
        $act = str_replace('\Panel\\', '\Model\\', __CLASS__);
        app('\\' . $act)->execute($row, $data, $rules);
        dddx(['\\' . $act,$row, $data, $rules]);

        if (method_exists($panel, 'destroyCallback')) {
            $panel->destroyCallback(['row' => $row]);
        }
        return $panel;
    }
}
