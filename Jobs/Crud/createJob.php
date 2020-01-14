<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

class createJob implements ShouldQueue {
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
    public function __construct($container, $item, $data = null) {
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

        $this->row = $row;
        $this->rows = $rows;
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
