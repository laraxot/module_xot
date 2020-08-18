<?php

namespace Modules\Xot\Jobs\Crud;

/*
 * like a create
 */

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

class AttachJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traits\CommonTrait;

    protected $container;
    protected $item;
    protected $row;
    protected $rows;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($containers, $items, $data = null) {
        if (is_array($containers)) {
            $container = last($containers);
        } else {
            $container = $containers;
            $containers = [$container];
        }
        if (is_array($items)) {
            $item = last($items);
        } else {
            $item = $items;
            $items = [$item];
        }
        $types = Str::camel(Str::plural($container));
        if (is_object($item)) { //l'oggetto figlio potrebbe avere un modello diverso
            $rows = $item->$types();
            $row = $rows->getRelated();
        } else {
            $rows = null;
            $row = xotModel($container);
        }
        //$rows = $rows->paginate(20);
        $panel = Panel::get($row);
        $panel->setRows($rows);
        if (! method_exists($rows, 'getPivotClass')) {
            abort(403, 'not a pivot ');
        }
        $pivot_class = $rows->getPivotClass();
        $pivot = new $pivot_class();
        //$panel = StubService::getByModel($pivot, 'panel', true);
        $panel = Panel::get($pivot);
        $panel->pivot_key_names = [];
        $panel->pivot_key_names[] = $rows->getForeignPivotKeyName();
        if (method_exists($rows, 'getMorphType')) {
            $panel->pivot_key_names[] = $rows->getMorphType();
        }
        $this->panel = $panel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        return $this->panel;
    }
}
