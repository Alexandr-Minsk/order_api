<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class OrderValidator
{
    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function getOrderValidator($data) {
        return Validator::make($data, self::rules());
    }

    public static function rules()
    {
        return [
            'status' => 'min:3|max:11',
            'product_ids' => 'array',
            'product_ids.*' => 'integer'
        ];
    }
}