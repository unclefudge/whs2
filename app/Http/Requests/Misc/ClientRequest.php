<?php

namespace App\Http\Requests\Misc;

use App\Http\Requests\Request;

class ClientRequest extends Request {

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
            'address' => 'required',
            'suburb' => 'required',
            'state' => 'required',
            'postcode' => 'required',
            'phone' => 'required',
            'company_id' => 'required',
            'email' => 'email|max:255'
        ];
    }
}
