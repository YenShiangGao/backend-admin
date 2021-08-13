<?php

namespace App\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use App\Http\Api\Requests\Traits\RequestTrait;

/**
 * Class FormRequest
 * @package App\Http\Api\Requests
 */
class FormRequest extends BaseFormRequest
{
    use RequestTrait;

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }

    public function authorize()
    {
        return true;
    }
}
