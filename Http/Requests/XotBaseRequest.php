<?php
namespace Modules\Xot\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//use Modules\Food\Models\Profile;
//use Modules\Xot\Traits\FormRequestTrait;
//--- Rules ---
use Modules\Food\Rules\Slugify as SlugifyRule;

abstract class XotBaseRequest extends FormRequest{
    //use FormRequestTrait;

    //public function __construct(){
    //$this->setContainer(factory(Profile::class));
    //$this->setContainer(app());
    //}

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        

        //return $rules;
    }

    /*
     * Get the validated data from the request.
     *
     * @return array
     */
    /*
    public function validated()
    {
        $rules = $this->container->call([$this, 'rules']);
        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return explode('.', $rule)[0];
        })->unique()->toArray());
    }
    */
}
