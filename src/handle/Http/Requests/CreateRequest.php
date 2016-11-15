<?php
namespace Ohio\Content\Handle\Http\Requests;

use Ohio\Core\Base\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'url' => 'required|unique:handles|unique_route',
            'handleable_id' => 'required',
            'handleable_type' => 'required',
        ];
    }

}