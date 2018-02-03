<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;

class CompanyRequest extends Request {

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
        $tabs = $this->get('tabs');

        switch ($tabs) {
            case 'settings:logo': {
                return [
                    'logo' => 'required|mimes:jpg,jpeg,png,gif,bmp'
                ];
            }
            default: { // Create + setting:info rules
                return [
                    'name'            => 'required',
                    'address'         => 'required',
                    'suburb'          => 'required',
                    'state'           => 'required',
                    'postcode'        => 'required',
                    'phone'           => 'required',
                    'primary_user'    => 'required',
                    'abn'             => 'required',
                    'business_entity' => 'required',
                    'gst'             => 'required',
                    'email'           => 'required|email|max:255',
                    //'supervisors'     => 'required_with:transient',
                ];
            }
        }

        return [];
    }

    public function messages()
    {
        return [
            'name.required'            => 'The company name field is required.',
            'person_name.required'     => 'The persons name field is required.',
            'business_entity.required' => 'The business entity field is required.',
            'logo.required'            => 'No image was selected.'
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

   $slug = $this->get('slug');
   $tabs = $this->get('tabs');

   switch ($tabs) {
       case 'settings:info':
           return $this->redirector->to('company/' . $slug . '/settings/info')
               ->withInput($this->except($this->dontFlash))
               ->withErrors($errors, $this->errorBag);
       case 'settings:logo':
           return $this->redirector->to('company/' . $slug . '/settings/logo')
               ->withInput($this->except($this->dontFlash))
               ->withErrors($errors, $this->errorBag);
       default:
           return $this->redirector->to($this->getRedirectUrl())
               ->withInput($this->except($this->dontFlash))
               ->withErrors($errors, $this->errorBag);
   }

}*/

}
