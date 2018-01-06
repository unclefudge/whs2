<?php

namespace App\Http\Requests\Comms;

use App\Http\Requests\Request;

class TodoRequest extends Request {

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
            'name'         => 'required',
            'type'         => 'required',
            'info'         => 'required',
            'assign_to'    => 'required',
            'user_list'    => 'required_if:assign_to, "user"',
            'company_list' => 'required_if:assign_to, "company"',
            'group_list'   => 'required_if:assign_to, "group"',
        ];
    }

    public function messages()
    {
        return [
            'user_list.required_if' => 'Select at least one user',
            'company_list.required_if' => 'Select at least one company',
            'group_list.required_if' => 'Select at least one group',
        ];
    }

}
