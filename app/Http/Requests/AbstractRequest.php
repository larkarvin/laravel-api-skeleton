<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractRequest extends FormRequest
{
    protected $routeParametersToValidate = [];
    protected $queryParametersToValidate = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    public function all($keys = null)
    {
        $data = parent::all();

        foreach ($this->routeParametersToValidate as $validationDataKey => $routeParameter) {
            if(!empty($this->route($routeParameter))){
                $data[$validationDataKey] = $this->route($routeParameter);
            }
        }

        foreach ($this->queryParametersToValidate as $validationDataKey => $queryParameter) {
            if(!empty($this->query($queryParameter))){
                $data[$validationDataKey] = $this->query($queryParameter);
            }
            
        }
        return $data;
    }
}