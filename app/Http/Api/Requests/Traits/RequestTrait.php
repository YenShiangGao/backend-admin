<?php

namespace App\Http\Api\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;
use App\Http\Api\Resources\ErrorResource;

/**
 * Trait RequestTrait
 */
trait RequestTrait
{
    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();

        (new ErrorResource($error))->response()->throwResponse();
    }
}
