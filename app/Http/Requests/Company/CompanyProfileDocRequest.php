<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;

class CompanyProfileDocRequest extends Request {

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
            'category_id'     => 'required',
            'expiry'          => 'required',
            'ref_no'          => 'required_if:category_id,1,2,3',
            'ref_name'        => 'required_if:category_id,1,2,3',
            'ref_type'        => 'required_if:category_id,2,3',
            'lic_no'          => 'required_if:category_id,7',
            'lic_type'        => 'required_if:category_id,7',
            'extra_lic_type'  => 'required_if:category_id,8,9,89',
            'extra_lic_class' => 'required_if:extra_lic_type,8',
            'extra_lic_name'  => 'required_if:extra_lic_type,9',
            'singlefile'      => 'required_if:action,add',
        ];
    }

    public function messages()
    {
        return [
            'ref_no.required_if'          => 'The policy no. field is required',
            'ref_name.required_if'        => 'The insurer field is required',
            'ref_type.required_if'        => 'The category field is required',
            'lic_no.required_if'        => 'The licence no. field is required',
            'lic_type.required_if'        => 'The class field is required',
            'extra_lic_type.required_if'  => 'The type field is required',
            'extra_lic_class.required_if' => 'The class field is required',
            'extra_lic_name.required_if'  => 'The licence name field is required',
            'singlefile.required_if'      => 'The file field is required',
        ];
    }
}
