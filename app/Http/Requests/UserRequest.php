<?php

namespace App\Http\Requests;

use App\User;
use App\Http\Requests\Request;


class UserRequest extends Request {

    //protected $redirect = 'user/account/info';

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
        $rules = [
            'username'           => 'required|min:3|max:50|unique:users',
            'password'           => 'required|min:3',
            'firstname'          => 'required',
            'lastname'           => 'required',
            'company_id'         => 'required',
            'email'              => 'required_if:status,1|email|max:255|unique:users,email,NULL',
            //'email' => 'email|max:255|unique:users,email,NULL,id,email,NOT_EMPTY'
            'subcontractor_type' => 'required_if:employment_type,2',
            'roles'              => 'required',
        ];

        $tabs = $this->get('tabs');
        if (!$tabs)
            return $rules;

        switch ($tabs) {
            case 'settings:info': {
                return [
                    'firstname'          => 'required',
                    'lastname'           => 'required',
                    'email'              => 'required_if:status,1|email|max:255|unique:users,email,' . $this->get('id') . ',id',
                    'subcontractor_type' => 'required_if:employment_type,2',
                ];
            }
            case 'settings:photo': {
                return [
                    'photo' => 'required|mimes:jpg,jpeg,png,gif,bmp'
                ];
            }
            case 'settings:password': {
                return [
                    'username' => 'required|min:3|max:50|unique:users,username,' . $this->get('id'),
                    'password' => 'required|confirmed|min:3'
                ];
            }
        }
    }

    /**
     * Custom error messages for Form Request
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_id.required'            => 'The company field is required.',
            'photo'                          => 'No image was selected.',
            'email.required_if'              => 'The email field is required if user active ie. Login Enabled.',
            'roles'                          => 'You must select at least one role',
            'subcontractor_type.required_if' => 'The subcontractor entity is required',
        ];
    }

    /**
     * Overide the default return URL form failed validation request
     * with custom user/{username}/settings/info
     *
     * @param array $errors
     * @return $this|JsonResponse
     */
    public function response(array $errors)
    {
        // Optionally, send a custom response on authorize failure
        // (default is to just redirect to initial page with errors)
        //
        // Can return a response, a view, a redirect, or whatever else

        if ($this->ajax() || $this->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        $user = User::find($this->get('id'));
        if ($user)
            $username = $user->username;

        $tabs = $this->get('tabs');

        switch ($tabs) {
            case 'settings:info':
                return $this->redirector->to('user/' . $username . '/settings/info')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            case 'settings:photo':
                return $this->redirector->to('user/' . $username . '/settings/photo')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            case 'settings:password':
                return $this->redirector->to('user/' . $username . '/settings/password')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            default:
                return $this->redirector->to($this->getRedirectUrl())
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
        }
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    /*
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        $user = $this->route()->parameter('user');

        var_dump($user);
        var_dump($this->route());
        //var_dump($url);

        return $url->route('user.fudge2.account');
    }*/

}
