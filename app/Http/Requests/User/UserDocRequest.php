<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class UserDocRequest extends Request {

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
            'category_id'  => 'required_with:save',
            'name'         => 'required_with:save',
            'ref_name'     => 'required_if:category_id,6,7,8,20',
            'lic_no'       => 'required_if:category_id,2,3',
            'drivers_type' => 'required_if:category_id,2',
            'cl_type'      => 'required_if:category_id,3',
            'asb_type'     => 'required_if:category_id,9',
            'expiry'       => 'required_if:category_id,2,3',
            'issued'       => 'required_if:category_id,1,5,6,7,9',
            'singlefile'   => 'required_if:filetype,pdf',
            'singleimage'  => 'required_if:filetype,image',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required_with' => 'The category field is required',
            'name.required_with'        => 'The name field is required',
            'ref_name.required_if'      => 'The name field is required',
            'expiry.required_if'        => 'The expiry field is required',
            'lic_no.required_if'        => 'The licence no. field is required',
            'drivers_type.required_if'  => 'The class field is required',
            'cl_type.required_if'       => 'The class field is required',
            'asb_type.required_if'      => 'The class field is required',
            'issued.required_if'        => 'The issued date field is required',
            'singlefile.required_if'    => 'The file field is required',
            'singleimage.required_if'   => 'The file / photo field is required',
        ];
    }
}
