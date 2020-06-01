<?php

namespace Modules\Xot\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

//use Modules\Food\Models\Profile;
//use Modules\Xot\Traits\FormRequestTrait;
//--- Rules ---

abstract class XotBaseRequest extends FormRequest {
    //use FormRequestTrait;

    //public function __construct(){
    //$this->setContainer(factory(Profile::class));
    //$this->setContainer(app());
    //}
    public $panel;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        //return $rules;
    }

    public function setPanel($panel) {
        $this->panel = $panel;
    }

    public function validatePanel($panel, $act = '') {
        $this->setPanel($panel);
        $this->prepareForValidation();
        $rules = $panel->rules(['act' => $act]);
        $this->validate($rules, $panel->rulesMessages());
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
    /**
     * https://stackoverflow.com/questions/28854585/laravel-5-form-request-data-pre-manipulation?rq=1.
     *
     **/

    /**
     * Cerco di rilevare quando viene chiamato.
     *
     **/
    public function modifyInput(array $data) {
        ddd($data);
    }

    public function prepareForValidation() {
        $data = $this->request->all();
        $date_fields = collect($this->panel->fields())->filter(function ($item) use ($data) {
            return Str::startsWith($item->type, 'Date') && isset($data[$item->name]);
        })->all();
        foreach ($date_fields as $field) {
            $value = $data[$field->name]; // metterlo nel filtro sopra ?
            /*
            *  Se e' un oggetto e' già convertito
            **/
            if (! is_object($value)) {
                $func = 'Conv'.$field->type;
                $value_new = $this->$func($field, $value);
                $this->request->add([$field->name => $value_new]);
            }
        }
    }

    /**
     * Cerco di rilevare quando viene chiamato.
     *
     **/
    public function validationData() {
        ddd('aaa');
    }

    public function ConvDate($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y', $value);

        return $value_new;
    }

    public function ConvDateTime($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y H:i', $value);

        return $value_new;
    }

    public function ConvDateTime2Fields($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y H:i', $value);

        return $value_new;
    }
}
