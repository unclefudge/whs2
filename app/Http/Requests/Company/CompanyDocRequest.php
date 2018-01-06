<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;

class CompanyDocRequest extends Request {

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
            'category_id' => 'required_with:save',
            'name'        => 'required_with:save',
            'expiry'      => 'required_if:category_id,1,2',
            'ref_no'      => 'required_if:category_id,1,2',
            'ref_name'    => 'required_if:category_id,1,2',
            'ref_type'    => 'required_if:category_id,2,3,8',
            'singlefile'  => 'required_with_all:save,create',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required_with'    => 'The category field is required',
            'name.required_with'           => 'The name field is required',
            'expiry.required_if'           => 'The policy no. field is required',
            'ref_no.required_if'           => 'The policy no. field is required',
            'ref_name.required_if'         => 'The insurer field is required',
            'ref_type.required_if'         => 'The category field is required',
            'singlefile.required_with_all' => 'The file field is required',
        ];
    }
}
