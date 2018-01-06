<?php

namespace App\Http\Requests\Site;

use App\Http\Requests\Request;

class SiteCheckinRequest extends Request {

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
            'question1'  => 'required_with:checkin',
            'question2'  => 'required_with:checkin,checkinStore,checkinTruck',
            'question3'  => 'required_with:checkin,checkinTruck',
            //'question4' => 'required',
            'question5'  => 'required_with:checkin',
            'question6'  => 'required_with:checkin',
            'question7'  => 'required_with:checkin,checkinStore',
            'question8'  => 'required_with:checkin',
            'question9'  => 'required_with:checkinStore',
            'question10' => 'required_with:checkinStore',
            'question11' => 'required_with:checkinStore',
            'question12' => 'required_with:checkinStore',
            'question13' => 'required_with:checkinStore',
            'question14' => 'required_with:checkinTruck',
            'location'   => 'required_without:safe_site',
            'rating'     => 'required_without:safe_site',
            'reason'     => 'required_without:safe_site',
            'action'     => 'required_without:safe_site',
            'media'      => 'mimes:jpg,jpeg,png,gif,bmp,m4v,avi,flv,mp4,mov',
        ];
    }


    public function messages()
    {
        return [
            'question1.required_with'  => 'Please acknowledge you have read the Site Specific Health & Safety Rules.',
            'question2.required_with'  => 'Please acknowledge you are fit for work.',
            'question3.required_with'  => 'Please acknowledge you not affected by any pre-existing medical condition.',
            'question4.required_with'  => 'Please acknowledge you believe it to be safe to work.',
            'question5.required_with'  => 'Please acknowledge you familiar with the site specific Risk Assessment.',
            'question6.required_with'  => 'Please acknowledge you will take action to eliminate or control any hazards.',
            'question7.required_with'  => 'Please acknowledge you will report all incidents, near misses, unsafe work practices and conditions.',
            'question8.required_with'  => 'Please acknowledge you will leave the site secure and safe for others.',
            'question9.required_with'  => 'Please acknowledge you will store all materials safely.',
            'question10.required_with' => 'Please acknowledge you will assess your tasks and implement controls as necessary.',
            'question11.required_with' => 'Please acknowledge you will ensure all safety devices such as handrails are in place.',
            'question12.required_with' => 'Please acknowledge you will practice good housekeeping.',
            'question13.required_with' => 'Please acknowledge you will ensure the site is left secure, is safe for others.',
            'question14.required_with' => 'Please acknowledge you hold a current license.',
            'question15.required_with' => 'Please acknowledge you will abide by the road rules and be a courteous & responsible driver.',
            'question16.required_with' => 'Please acknowledge you report any damage and defects to the vehicle.',
            'question17.required_with' => 'Please acknowledge you any maintenance requirements (including servicing requirements)',
            'location.required_without'  => 'Please provide the location of hazard.',
            'rating.required_without'  => 'Please provide the risk rating of hazard.',
            'reason.required_without'  => 'Please provide the reason for unsafe worksite.',
            'action.required_without'  => 'Please provide the actions to have taken to make the site safe.',
        ];
    }


    /**
     * Overide the default return URL form failed validation request
     * with custom site/{slug}/settings/info
     *
     * @param array $errors
     * @return $this|JsonResponse
     */
    public
    function response(array $errors)
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
                return $this->redirector->to('site/' . $slug . '/settings/info')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            case 'settings:admin':
                return $this->redirector->to('site/' . $slug . '/settings/admin')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            case 'settings:photo':
                return $this->redirector->to('site/' . $slug . '/settings/photo')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            default:
                return $this->redirector->to($this->getRedirectUrl())
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
        }

    }
}
