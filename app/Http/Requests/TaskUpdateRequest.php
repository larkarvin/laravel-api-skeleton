<?php

namespace App\Http\Requests;

class TaskUpdateRequest extends AbstractRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|nullable|filled',
            'description'  => 'string|nullable|filled',
            'due_date' => 'date_format:Y-m-d|after:today|nullable|filled',
            'priority' => 'in:Urgent,High,Normal,Low|nullable|filled',
            'archived_at' => 'date_format:Y-m-d|nullable|filled',
            'completed_at' => 'date_format:Y-m-d|nullable|filled',
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