<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_name' => 'required|max:50|string',
            'task_note' => 'nullable|max:50|string',
            'task_repeat_value' => 'required|integer',
            'task_repeat_type' => 'required|integer',
            'task_notification_value' => 'required|integer',
            'task_notification_type' => 'required|integer',
        ];
    }
}
