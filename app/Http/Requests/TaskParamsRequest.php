<?php

namespace App\Http\Requests;

class TaskParamsRequest extends AbstractRequest
{

    /** 
     * Pass Query Parameter to Request->all() to allow
     * For Request to validate
     */
    protected $queryParametersToValidate = [
        'due_date' => 'due_date',
        'completed_date_from' => 'completed_date_from',
        'completed_date_to' => 'completed_date_to',
        'created_date_from' => 'created_date_from',
        'created_date_to' => 'created_date_to',
        'archived_date_from' => 'archived_date_from',
        'archived_date_to`' => 'archived_date_to`',
        'sort_by' => 'sort_by',
        'order_by' => 'order_by',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'due_date_from' => 'nullable|date_format:Y-m-d|required_with:due_date_to|before:due_date_to',
            'due_date_to' => 'nullable|date_format:Y-m-d|required_with:due_date_from',
            'completed_date_from' => 'nullable|required_with:completed_date_to|date_format:Y-m-d|before:completed_date_to',
            'completed_date_to' => 'nullable|required_with:completed_date_from|date_format:Y-m-d',
            'created_date_from' => 'nullable|date_format:Y-m-d',
            'created_date_to' => 'nullable|date_format:Y-m-d',
            'archived_date_from' => 'nullable|date_format:Y-m-d|required_with:archived_date_to|before:archived_date_to',
            'archived_date_to' => 'nullable|date_format:Y-m-d|required_with:archived_date_from',
            'sort_by' => 'nullable|in:title,description,due_date,created_at,completed_date,priority',
            'order_by' => 'nullable|in:desc,asc,DESC,ASC',
            'priority' => 'nullable|in:Urgent,High,Normal,Low'
        ];
    }

    public function messages()
    {
        return [
            'sort_by.in' => 'The ort_by param can only be title, description, due_date, created_at, completed_date, priority',
            'order_by.in' => 'The order_by param can only be desc, or asc',
            'priority.in' => 'The priority param can only be Urgent, High, Normal, or Low',
            'completed_date_from.required_with' => 'The completed_date_to param is required to work with completed_date_from',
            'completed_date_to.required_with' => 'The completed_date_from param is required to work with completed_date_to',
        ];
    }

}