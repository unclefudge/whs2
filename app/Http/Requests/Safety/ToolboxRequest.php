<?php

namespace App\Http\Requests\Safety;

use App\Http\Requests\Request;

class ToolboxRequest extends Request {

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
            'toolbox_type'   => 'required',
            'name'           => 'required',
            'master_id'      => 'required_if:toolbox_type,library',
            'previous_id'    => 'required_if:toolbox_type,previous',
        ];

    }

    public function messages()
    {
        return [
            'toolbox_type.required'   => 'Select option to create Toolbox talk is required.',
            'master_id.required_if'   => 'The template field is required.',
            'previous_id.required_if' => 'The previous talk field is required.',
            'for_company_id.required' => 'The company field is required.',
        ];
    }
}
