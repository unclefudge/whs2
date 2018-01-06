<?php

namespace App\Http\Requests\Safety;

use App\Http\Requests\Request;

class WmsRequest extends Request {

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
            'swms_type'      => 'required',
            'name'           => 'required',
            'for_company_id' => 'required',
            'principle'      => 'required_without:principle_switch',
            'replace_id'     => 'required_with:replace_switch',
            'master_id'      => 'required_if:swms_type,library',
            'attachment'     => 'required_if:swms_type,upload',
        ];

    }

    public function messages()
    {
        return [
            'swms_type.required'         => 'Select option to create SWMS is required.',
            'for_company_id.required'    => 'The company field is required.',
            'principle.required_without' => 'The principle contractor field is required.',
            'replace_id.required_with'   => 'The SWMS to replace field is required.',
            'master_id.required_if'      => 'The template field is required.',
            'attachment.required_if'     => 'A file is required.',
        ];
    }

}
