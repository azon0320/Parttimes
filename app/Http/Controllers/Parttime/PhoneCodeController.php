<?php


namespace App\Http\Controllers\Parttime;


use App\Http\Controllers\Controller;
use App\Services\JsonProcess;
use App\Services\ParttimeValidatorProcess;
use App\Services\PhoneCodeProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneCodeController extends Controller
{

    use PhoneCodeProcess, JsonProcess;

    public function allocate(Request $request){
        $phone = $request->query('phone');
        if (
            !Validator::make(['phone' => $phone],
                ['phone' => ['required', 'string', 'size:11']]
            )->fails() &&
            is_numeric($phone)
        ){
            $returns = self::allocateVerifyCode($phone);
            return $returns['succ'] ? strval($returns['msg']) : "0";
        }else{
            return "0";
        }
    }
}