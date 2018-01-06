<?php

namespace App\Http\Requests\Site\Planner;

use App\Http\Requests\Request;

class SitePlannerExportRequest extends Request {

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
            'date'       => 'required',
            'weeks'       => 'required',
        ];
    }

    public function messages()
    {
        return [
            //'site_id.required' => 'The site field is required',
            //'action.required'  => 'The preventative action field is required',
            //'notes.required_without' => 'Please provide notes before you close the accident report',
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

        if ($this->ajax() || $this->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);

    }*/
}
