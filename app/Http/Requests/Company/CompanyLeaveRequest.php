<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;

class CompanyLeaveRequest extends Request {

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
            'company_id' => 'required',
            'from' => 'required',
        ];

        return [];
    }

    public function messages()
    {
        return [
            'company_id.required' => 'The company field is required.',
            'from.required' => 'Please specify a date range',
        ];
    }


    /**
     * Overide the default return URL form failed validation request
     * with custom company/{slug}/settings/info
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
