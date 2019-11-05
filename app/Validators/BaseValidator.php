<?php


namespace App\Validators;


use Illuminate\Support\Facades\Validator;

abstract class BaseValidator
{
    protected $rules = [];
    protected $messages = [];

    public function __construct($rules = [], $messages = [])
    {
        $this->rules = $rules;
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function validator(array $inputs){
        return Validator::make($inputs, $this->rules, $this->messages);
    }
}