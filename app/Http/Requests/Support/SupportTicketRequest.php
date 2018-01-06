<?php

namespace App\Http\Requests\Support;

use App\Http\Requests\Request;

class SupportTicketRequest extends Request {

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
            'name' => 'required',
            'summary'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The ticket name field is required',
            'summary.required' => 'The description field is required',
        ];
    }
}
