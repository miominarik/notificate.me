<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditSettingsRequest extends FormRequest
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
            'enable_email_notif' => 'required|boolean',
            'notification_time' => 'required|numeric|min:0|max:24',
            'mobile_number' => 'nullable|string|max:20',
            'color_palette' => 'numeric|min:1|max:7|required',
        ];
    }
}
