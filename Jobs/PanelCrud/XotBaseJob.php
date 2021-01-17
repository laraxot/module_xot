<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Services\ModelService;

//----------- Requests ----------
//------------ services ----------

/**
 * Class XotBaseJob.
 */
abstract class XotBaseJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    //use Traits\CommonTrait;

    protected PanelContract $panel;

    protected array $data;

    /**
     * __construct.
     */
    public function __construct(array $data, PanelContract &$panel) {
        $this->panel = $panel;
        $this->data = $data;
        //$this->data = $this->prepareAndValidate($request->all(), $panel);
    }

    /**
     * Execute the job.
     */
    public function handle(): PanelContract {
        return $this->panel;
    }

    /**
     * @param ModelContract|Model $model
     */
    public function manageRelationships($model, array $data, string $act): void {
        $relationships = ModelService::getRelationshipsAndData($model, $data);
        foreach ($relationships as $k => $v) {
            $func = $act.'Relationships'.$v->relationship_type;
            if (method_exists($this, $func)) {
                static::$func($model, $v->name, $v->data);
            }
        }
        if (isset($data['pivot'])) {
            $func = $act.'Relationships'.'Pivot';
            self::$func($model, 'pivot', $data['pivot']);
        }
    }
}
