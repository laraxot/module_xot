<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Services\ModelService;

//----------- Requests ----------
//------------ services ----------

abstract class XotBaseJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    //use Traits\CommonTrait;

    protected $panel;
    protected $data;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct(array $data, PanelContract &$panel) {
        $this->panel = $panel;
        $this->data = $data;
        //$this->data = $this->prepareAndValidate($request->all(), $panel);
    }

    /**
     * Execute the job.
     *
     * @return PanelContract
     */
    public function handle() {
        return $this->panel;
    }

    public function manageRelationships($model, $data, $act) {
        $relationships = ModelService::getRelationshipsAndData($model, $data);
        foreach ($relationships as $k => $v) {
            $func = $act.'Relationships'.$v->relationship_type;
            if (method_exists($this, $func)) {
                self::$func($model, $v->name, $v->data);
            }
        }
        if (isset($data['pivot'])) {
            $func = $act.'Relationships'.'Pivot';
            self::$func($model, 'pivot', $data['pivot']);
        }
    }
}
