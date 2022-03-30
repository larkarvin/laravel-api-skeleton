<?php

namespace App\Http\Requests;

class TaskCreateRequest extends AbstractRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'description'  => 'required',
            'due_date' => 'date_format:Y-m-d|after:today|nullable',
            'priority' => 'in:Urgent,High,Normal,Low|nullable',
            'archived_at' => 'date_format:Y-m-d|nullable',
            'completed_at' => 'date_format:Y-m-d|nullable',
            'media.*' => 'mimes:jpg,bmp,png,mp4,svg,jpeg,csv,txt,doc,docx|nullable'
        ];
    }

    public function messages()
    {
        return [
            'priority.in' => 'Priority can only be Urgent, High, Normal, or Low.'
        ];
    }

}