<?php

namespace App\Http\Requests\Comms;

use App\Http\Requests\Request;

class NotifyRequest extends Request {

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
            'from'         => 'required',
            'info'         => 'required',
            'assign_to'    => 'required',
            'user_list'    => 'required_if:assign_to, "user"',
            'company_list' => 'required_if:assign_to, "company"',
            'group_list'   => 'required_if:assign_to, "group"',
            'role_list'    => 'required_if:assign_to, "role"',
            'site_list'    => 'required_if:assign_to, "site"',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'The title field is required',
            'info.required'            => 'The Message field is required',
            'from.required'            => 'Please specify a date range',
            'assign_to.required'       => 'Please select who you want to send the alert to',
            'user_list.required_if'    => 'Select at least one user',
            'company_list.required_if' => 'Select at least one company',
            'group_list.required_if'   => 'Select at least one group',
            'role_list.required_if'    => 'Select at least one role',
        ];
    }

}
