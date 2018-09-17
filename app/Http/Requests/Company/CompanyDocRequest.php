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
            'ref_no'      => 'required_if:category_id,1,2,3',
            'ref_name'    => 'required_if:category_id,1,2,3',
            'ref_type'    => 'required_if:category_id,2,3',
            'lic_no'      => 'required_if:category_id,7',
            'lic_type'    => 'required_if:category_id,7',
            'asb_type'    => 'required_if:category_id,8',
            'expiry'      => 'required_if:category_id,1,2,3,4,5,7,8,9,10,11',
            'tag_date'    => 'required_if:category_id,6',
            //'expiry'      => 'required',
            'singlefile'  => 'required_if:filetype,pdf',
            'singleimage' => 'required_if:filetype,image',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required_with' => 'The category field is required',
            'name.required_with'        => 'The name field is required',
            'expiry.required_if'        => 'The expiry field is required',
            'expiry.required'           => 'The expiry field is required',
            'ref_no.required_if'        => 'The policy no. field is required',
            'ref_name.required_if'      => 'The insurer field is required',
            'ref_type.required_if'      => 'The category field is required',
            'lic_no.required_if'        => 'The licence no. field is required',
            'lic_type.required_if'      => 'The class field is required',
            'asb_type.required_if'      => 'The class field is required',
            'tag_date.required_if'      => 'The test date field is required',
            'singlefile.required_if'    => 'The file field is required',
            'singleimage.required_if'   => 'The file / photo field is required',
        ];
    }
}
