<?php

namespace App\Http\Requests\Site;

use App\Http\Requests\Request;

class SiteQaRequest extends Request {

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
            'name' => 'required',
            'category_id' => 'required',
            'task1' => 'required_with:item1',
            'task2' => 'required_with:item2',
            'task3' => 'required_with:item3',
            'task4' => 'required_with:item4',
            'task5' => 'required_with:item5',
            'task6' => 'required_with:item6',
            'task7' => 'required_with:item7',
            'task8' => 'required_with:item8',
            'task9' => 'required_with:item9',
            'task10' => 'required_with:item10',
            'task11' => 'required_with:item11',
            'task12' => 'required_with:item12',
            'task13' => 'required_with:item13',
            'task14' => 'required_with:item14',
            'task15' => 'required_with:item15',
            'task16' => 'required_with:item16',
            'task17' => 'required_with:item17',
            'task18' => 'required_with:item18',
            'task19' => 'required_with:item19',
            'task20' => 'required_with:item20',
            'task21' => 'required_with:item21',
            'task22' => 'required_with:item22',
            'task23' => 'required_with:item23',
            'task24' => 'required_with:item24',
            'task25' => 'required_with:item25',
        ];

    }

    public function messages()
    {
        return [
            'category_id.required'        => 'The category field is required',
        ];
    }

}
