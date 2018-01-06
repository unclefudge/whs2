<?php

namespace App\Http\Requests\Site;

use App\Http\Requests\Request;

class SiteHazardRequest extends Request {

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
            'site_id' => 'required',
            'location'  => 'required',
            'reason'  => 'required',
            'rating'  => 'required',
            'action'  => 'required',
            'media'   => 'mimes:jpg,jpeg,png,gif,bmp,m4v,avi,flv,mp4,mov',
        ];
    }

    public function messages()
    {
        return [
            'site_id.required' => 'The site field is required',
        ];
    }
}
