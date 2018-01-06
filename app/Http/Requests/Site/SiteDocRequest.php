<?php

namespace App\Http\Requests\Site;

use App\Http\Requests\Request;

class SiteDocRequest extends Request {

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
            'site_id'    => 'required_with:save',
            'type'       => 'required_with:save',
            'name'       => 'required_with:save',
            'singlefile' => 'required_with_all:save,create',
        ];
    }

    public function messages()
    {
        return [
            'site_id.required_with'        => 'The site field is required',
            'type.required_with'           => 'The type field is required',
            'name.required_with'           => 'The name field is required',
            'singlefile.required_with_all' => 'The file field is required',
        ];
    }


    /**
     * Overide the default return URL form failed validation request
     * with custom site/{slug}/settings/info
     *
     * @param array $errors
     * @return $this|JsonResponse
     */
		 /*
    public function response(array $errors)
    {
        // Optionally, send a custom response on authorize failure
        // (default is to just redirect to initial page with errors)
        //
        // Can return a response, a view, a redirect, or whatever else

        // Optionally, send a custom response on authorize failure
        // (default is to just redirect to initial page with errors)
        //
        // Can return a response, a view, a redirect, or whatever else

        if ($this->ajax() || $this->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);

    }*/
}
