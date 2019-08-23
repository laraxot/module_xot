<?php
namespace Modules\Xot\Repositories;

//---base
use Modules\Xot\Repositories\XotBaseRepository;

class XotBaseModelRepository extends XotBaseRepository{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = 'Modules\Xot\Models\XotBaseModel';
}