<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'joining_date' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
        ];
    }

    public function messages() {
        return [
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only jpeg,jpg,png file is allowed',
        ];
    }    
}
