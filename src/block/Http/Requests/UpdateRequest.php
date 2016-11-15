<?php
namespace Ohio\Content\Block\Http\Requests;

use Ohio\Core\Base\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'name' => 'sometimes|required',
            'body' => 'sometimes|required',
        ];
    }

}